<?php

namespace App\Services\Payment;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class StripeGateway extends AbstractPaymentGateway
{
    protected $stripe;

    protected $gateway = 'stripe';

    public function __construct()
    {
        // Try to get secret key from settings first, then fall back to config
        $secretKey = settings('stripe.api_key') ?? config('services.stripe.key');

        // Only initialize if credentials are properly configured
        if ($secretKey &&
            ! str_starts_with($secretKey, 'sk_test_your_key') &&
            ! str_starts_with($secretKey, 'sk_live_your_key') &&
            ! str_starts_with($secretKey, 'your_stripe_')) {

            $this->stripe = new StripeClient($secretKey);
        } else {
            // Stripe credentials not configured - will return errors for Stripe operations
            $this->stripe = null;
        }
    }

    public function createCheckoutSession(array $data)
    {
        if (! $this->stripe) {
            return [
                'success' => false,
                'message' => 'Stripe credentials not configured',
            ];
        }

        try {
            $lineItems = [];
            foreach ($data['items'] as $item) {
                $lineItems[] = [
                    'price' => $this->getOrCreateStripePrice($item['price']),
                    'quantity' => $item['quantity'] ?? 1,
                ];
            }

            // Get or create customer for the user
            $customerId = $this->getOrCreateStripeCustomer($data['user']);

            // Determine if this is a subscription based on the first item's billing period
            $isSubscription = false;
            if (! empty($data['items'])) {
                $firstItem = $data['items'][0];
                $isSubscription = ($firstItem['price']->billing_period !== 'once');
            }

            $sessionData = [
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => $isSubscription ? 'subscription' : 'payment',
                'success_url' => $data['success_url'],
                'cancel_url' => $data['cancel_url'],
                'customer' => $customerId,
                'metadata' => [
                    'user_id' => $data['user']->id,
                    'order_id' => $data['order']->id,
                ],
            ];

            if ($isSubscription) {
                $sessionData['allow_promotion_codes'] = true;
                $sessionData['subscription_data'] = [
                    'metadata' => [
                        'user_id' => $data['user']->id,
                        'order_id' => $data['order']->id,
                    ],
                ];

                // Pass the trial to Stripe so the customer is not charged during it
                $trialDays = (int) ($firstItem['price']->trial_days ?? 0);
                if ($trialDays > 0) {
                    $sessionData['subscription_data']['trial_period_days'] = $trialDays;
                }
            } else {
                // Tag the payment intent so webhooks (payment_intent.succeeded/failed)
                // can be mapped back to our order
                $sessionData['payment_intent_data'] = [
                    'metadata' => [
                        'user_id' => $data['user']->id,
                        'order_id' => $data['order']->id,
                    ],
                ];
            }

            // Tax is delegated to Stripe Tax when enabled in settings
            if (settings('stripe.automatic_tax')) {
                $sessionData['automatic_tax'] = ['enabled' => true];
                $sessionData['customer_update'] = ['address' => 'auto'];
            }

            $session = $this->stripe->checkout->sessions->create($sessionData);

            return [
                'success' => true,
                'id' => $session->id,
                'url' => $session->url,
            ];
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error('Stripe API error: '.$e->getMessage());

            // Check for specific error types and provide user-friendly messages
            if (str_contains($e->getMessage(), 'No such price')) {
                return [
                    'success' => false,
                    'message' => 'The selected product pricing is not properly configured. Please try a different payment method or contact support.',
                ];
            } elseif (str_contains($e->getMessage(), 'Invalid email')) {
                return [
                    'success' => false,
                    'message' => 'A valid email address is required to process your payment.',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Payment processing error: '.$e->getMessage(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Stripe checkout session error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'An unexpected error occurred while setting up your payment. Please try again.',
            ];
        }
    }

    public function handleWebhook(array $event): array
    {
        $eventType = $event['type'] ?? null;
        $eventData = $event['data']['object'] ?? [];

        Log::info('Processing Stripe webhook event', [
            'event_type' => $eventType,
            'event_id' => $event['id'] ?? 'unknown',
        ]);

        return match ($eventType) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($eventData),
            'payment_intent.succeeded' => $this->handlePaymentSuccess($eventData),
            'payment_intent.payment_failed' => $this->handlePaymentFailure($eventData),
            'customer.subscription.created' => $this->handleSubscriptionCreated($eventData),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($eventData),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($eventData),
            'invoice.payment_succeeded' => $this->handleInvoicePaymentSucceeded($eventData),
            'invoice.payment_failed' => $this->handleInvoicePaymentFailed($eventData),
            default => ['success' => true, 'action' => 'noop', 'message' => 'Unhandled event type'],
        };
    }

    public function handlePaymentSuccess(array $paymentIntent): array
    {
        $paymentIntentId = $paymentIntent['id'] ?? '';

        return [
            'success' => true,
            'action' => 'mark_order_paid_by_payment_intent',
            'payment_intent_id' => $paymentIntentId,
            'order_id' => $paymentIntent['metadata']['order_id'] ?? null,
            'gateway_data' => [
                'stripe_payment_intent' => $paymentIntentId,
            ],
        ];
    }

    public function handlePaymentFailure(array $paymentIntent): array
    {
        $paymentIntentId = $paymentIntent['id'] ?? '';

        return [
            'success' => true,
            'action' => 'mark_order_failed_by_payment_intent',
            'payment_intent_id' => $paymentIntentId,
            'order_id' => $paymentIntent['metadata']['order_id'] ?? null,
            'failure_reason' => $paymentIntent['last_payment_error']['message'] ?? 'Payment failed',
            'gateway_data' => [
                'stripe_payment_intent' => $paymentIntentId,
            ],
        ];
    }

    protected function handleCheckoutCompleted(array $session): array
    {
        $sessionId = $session['id'] ?? '';
        $mode = $session['mode'] ?? '';

        if (! $sessionId) {
            return ['success' => false, 'error' => 'Missing session ID'];
        }

        $result = [
            'success' => true,
            'action' => 'mark_order_paid',
            'gateway_order_id' => $sessionId,
            'gateway_data' => [
                'stripe_payment_intent' => $session['payment_intent'] ?? null,
                'stripe_subscription' => $session['subscription'] ?? null,
                'stripe_session_id' => $sessionId,
            ],
        ];

        if ($mode === 'subscription' && isset($session['subscription'])) {
            // Subscription checkout: the initial payment transaction is recorded by
            // the invoice.payment_succeeded webhook, so no transaction here
            $result['subscription_action'] = 'activate';
            $result['subscription_data'] = [
                'gateway_subscription_id' => $session['subscription'],
                'gateway_status' => 'active',
                'stripe_checkout_session' => $sessionId,
            ];
        } else {
            $result['transaction_data'] = [
                'gateway_transaction_id' => $session['payment_intent'] ?? $sessionId,
                'gateway_data' => [
                    'checkout_session_id' => $sessionId,
                    'payment_intent' => $session['payment_intent'] ?? null,
                ],
            ];
        }

        return $result;
    }

    protected function handleSubscriptionCreated(array $subscriptionData): array
    {
        $subscriptionId = $subscriptionData['id'] ?? '';

        return [
            'success' => true,
            'action' => 'update_subscription_status',
            'subscription_id' => $subscriptionId,
            'status' => $this->mapSubscriptionStatus($subscriptionData['status'] ?? 'active'),
            'gateway_data' => [
                'gateway_status' => $subscriptionData['status'] ?? 'active',
                'current_period_end' => isset($subscriptionData['current_period_end'])
                    ? \Carbon\Carbon::createFromTimestamp($subscriptionData['current_period_end'])
                    : null,
                'stripe_subscription_data' => $subscriptionData,
            ],
        ];
    }

    protected function handleSubscriptionUpdated(array $subscriptionData): array
    {
        $subscriptionId = $subscriptionData['id'] ?? '';

        return [
            'success' => true,
            'action' => 'update_subscription_status',
            'subscription_id' => $subscriptionId,
            'status' => $this->mapSubscriptionStatus($subscriptionData['status'] ?? 'unknown'),
            'gateway_data' => [
                'gateway_status' => $subscriptionData['status'] ?? 'unknown',
                'current_period_end' => isset($subscriptionData['current_period_end'])
                    ? \Carbon\Carbon::createFromTimestamp($subscriptionData['current_period_end'])
                    : null,
                'stripe_subscription_data' => $subscriptionData,
            ],
        ];
    }

    protected function handleSubscriptionDeleted(array $subscriptionData): array
    {
        $subscriptionId = $subscriptionData['id'] ?? '';

        return [
            'success' => true,
            'action' => 'cancel_subscription',
            'subscription_id' => $subscriptionId,
            'gateway_data' => [
                'gateway_status' => 'canceled',
                'current_period_end' => isset($subscriptionData['current_period_end'])
                    ? \Carbon\Carbon::createFromTimestamp($subscriptionData['current_period_end'])
                    : now(),
            ],
        ];
    }

    protected function handleInvoicePaymentSucceeded(array $invoice): array
    {
        $subscriptionId = $invoice['subscription'] ?? '';

        return [
            'success' => true,
            'action' => 'create_subscription_transaction',
            'subscription_id' => $subscriptionId,
            'transaction_data' => [
                'type' => 'subscription_payment',
                'status' => 'completed',
                'amount' => ($invoice['amount_paid'] ?? 0) / 100,
                'currency' => $invoice['currency'] ?? 'usd',
                'gateway_transaction_id' => $invoice['id'] ?? '',
                'gateway_data' => $invoice,
            ],
        ];
    }

    protected function handleInvoicePaymentFailed(array $invoice): array
    {
        $subscriptionId = $invoice['subscription'] ?? '';

        return [
            'success' => true,
            'action' => 'handle_invoice_payment_failed',
            'subscription_id' => $subscriptionId,
            'transaction_data' => [
                'type' => 'subscription_payment',
                'status' => 'failed',
                'amount' => ($invoice['amount_due'] ?? 0) / 100,
                'currency' => $invoice['currency'] ?? 'usd',
                'gateway_transaction_id' => $invoice['id'] ?? '',
                'gateway_data' => $invoice,
            ],
        ];
    }

    protected function mapSubscriptionStatus(string $stripeStatus): string
    {
        return match ($stripeStatus) {
            'active' => 'active',
            'past_due' => 'past_due',
            'canceled' => 'canceled',
            'unpaid' => 'past_due',
            'incomplete' => 'incomplete',
            'incomplete_expired' => 'expired',
            'trialing' => 'trialing',
            default => 'unknown',
        };
    }

    public function getPaymentDetails(string $paymentId)
    {
        if (! $this->stripe) {
            return [
                'success' => false,
                'error' => 'Stripe credentials not configured',
            ];
        }

        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentId);

            return [
                'success' => true,
                'data' => $paymentIntent,
            ];
        } catch (\Exception $e) {
            Log::error('Stripe get payment details error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function refundPayment(string $paymentId, ?float $amount = null)
    {
        if (! $this->stripe) {
            return [
                'success' => false,
                'error' => 'Stripe credentials not configured',
            ];
        }

        try {
            $refundData = ['payment_intent' => $paymentId];
            if ($amount) {
                $refundData['amount'] = (int) ($amount * 100); // Convert to cents
            }

            $refund = $this->stripe->refunds->create($refundData);

            return [
                'success' => true,
                'refund_id' => $refund->id,
            ];
        } catch (\Exception $e) {
            Log::error('Stripe refund error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function createSubscription(array $data)
    {
        if (! $this->stripe) {
            return [
                'success' => false,
                'error' => 'Stripe credentials not configured',
            ];
        }

        try {
            // Get or create customer
            $customerId = $this->getOrCreateStripeCustomer($data['user']);

            // Get the price ID from the product price
            $productPrice = $data['items'][0]['price'];
            $priceId = $this->getOrCreateStripePrice($productPrice);

            if (! $priceId) {
                throw new \Exception('Failed to get or create Stripe price');
            }

            $subscriptionData = [
                'customer' => $customerId,
                'items' => [['price' => $priceId]],
                'metadata' => [
                    'user_id' => $data['user']->id,
                    'subscription_id' => $data['subscription']->id ?? null,
                    'order_id' => $data['order']->id ?? null,
                ],
            ];

            $trialDays = (int) ($productPrice->trial_days ?? 0);
            if ($trialDays > 0) {
                $subscriptionData['trial_period_days'] = $trialDays;
            }

            $subscription = $this->stripe->subscriptions->create($subscriptionData);

            return [
                'success' => true,
                'id' => $subscription->id,
                'status' => $subscription->status,
            ];
        } catch (\Exception $e) {
            Log::error('Stripe subscription creation error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function cancelSubscription(string $subscriptionId, bool $immediately = false)
    {
        if (! $this->stripe) {
            return [
                'success' => false,
                'error' => 'Stripe credentials not configured',
            ];
        }

        try {
            if ($immediately) {
                $subscription = $this->stripe->subscriptions->cancel($subscriptionId);
            } else {
                $subscription = $this->stripe->subscriptions->update($subscriptionId, [
                    'cancel_at_period_end' => true,
                ]);
            }

            return [
                'success' => true,
                'status' => $subscription->status,
                'current_period_end' => $subscription->current_period_end,
                'canceled_at' => $subscription->canceled_at ?? now()->timestamp,
            ];
        } catch (\Exception $e) {
            Log::error('Stripe cancel subscription error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function resumeSubscription(string $subscriptionId)
    {
        if (! $this->stripe) {
            return [
                'success' => false,
                'error' => 'Stripe credentials not configured',
            ];
        }

        try {
            $subscription = $this->stripe->subscriptions->update($subscriptionId, [
                'cancel_at_period_end' => false,
            ]);

            return [
                'success' => true,
                'status' => $subscription->status,
            ];
        } catch (\Exception $e) {
            Log::error('Stripe resume subscription error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function changeSubscriptionPlan(string $subscriptionId, string $newPlanId)
    {
        if (! $this->stripe) {
            return [
                'success' => false,
                'error' => 'Stripe credentials not configured',
            ];
        }

        try {
            $subscription = $this->stripe->subscriptions->retrieve($subscriptionId);
            $updatedSubscription = $this->stripe->subscriptions->update($subscriptionId, [
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'price' => $newPlanId,
                    ],
                ],
            ]);

            return [
                'success' => true,
                'subscription' => $updatedSubscription,
            ];
        } catch (\Exception $e) {
            Log::error('Stripe plan change error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a new Stripe price and update the ProductPrice model
     */
    protected function createStripePrice($productPrice)
    {
        if (! $this->stripe) {
            return null;
        }

        try {
            // First, create or get a Stripe product
            $productName = $productPrice->product->title.' - '.$productPrice->title;
            $stripeProduct = $this->createOrGetStripeProduct($productPrice->product, $productName);

            if (! $stripeProduct) {
                throw new \Exception("Failed to create Stripe product for '{$productName}'");
            }

            // Create the price in Stripe
            $priceData = [
                'unit_amount' => (int) ($productPrice->amount * 100), // Convert to cents
                'currency' => strtolower($productPrice->currency),
                'product' => $stripeProduct->id,
                'nickname' => $productPrice->title,
            ];

            // Handle recurring prices
            if ($productPrice->billing_period !== 'once') {
                $priceData['recurring'] = [
                    'interval' => $this->mapBillingPeriodToStripe($productPrice->billing_period),
                ];
            }

            $stripePrice = $this->stripe->prices->create($priceData);

            // Update the ProductPrice model with the new price ID
            $currentGatewayData = $productPrice->gateway_data ?? [];
            $currentGatewayData['stripe'] = [
                'price_id' => $stripePrice->id,
                'product_id' => $stripeProduct->id,
                'price_fingerprint' => $this->priceFingerprint($productPrice),
            ];

            $productPrice->update(['gateway_data' => $currentGatewayData]);

            Log::info("Created Stripe price: {$stripePrice->id} for ProductPrice: {$productPrice->title}");

            return $stripePrice->id;
        } catch (\Exception $e) {
            Log::error("Failed to create Stripe price for ProductPrice ID {$productPrice->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Create or get existing Stripe product
     */
    protected function createOrGetStripeProduct($product, $productName)
    {
        try {
            // Try to get existing product ID from gateway_data
            $stripeProductId = $product->gateway_data['stripe']['product_id'] ?? null;

            if ($stripeProductId) {
                try {
                    // Verify the product still exists in Stripe
                    return $this->stripe->products->retrieve($stripeProductId);
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    // Product doesn't exist, create a new one
                    Log::warning("Stripe product {$stripeProductId} not found, creating new one");
                }
            }

            // Create new Stripe product
            $stripeProduct = $this->stripe->products->create([
                'name' => $productName,
                'description' => $product->description,
                'metadata' => [
                    'product_id' => $product->id,
                    'product_slug' => $product->slug,
                ],
            ]);

            // Update the Product model with Stripe product ID
            $currentGatewayData = $product->gateway_data ?? [];
            $currentGatewayData['stripe'] = [
                'product_id' => $stripeProduct->id,
            ];

            $product->update(['gateway_data' => $currentGatewayData]);

            return $stripeProduct;
        } catch (\Exception $e) {
            Log::error("Failed to create Stripe product for Product ID {$product->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Map billing period to Stripe recurring interval
     */
    protected function mapBillingPeriodToStripe($billingPeriod)
    {
        $mapping = [
            'daily' => 'day',
            'weekly' => 'week',
            'monthly' => 'month',
            'yearly' => 'year',
        ];

        return $mapping[$billingPeriod] ?? 'month';
    }

    /**
     * Check if a price ID is invalid/fake/demo
     */
    protected function isInvalidPriceId($priceId)
    {
        // Real Stripe price IDs start with 'price_' and are at least 28 characters
        // Demo/fake IDs are usually shorter or don't follow Stripe's format
        if (strlen($priceId) < 28) {
            return true;
        }

        // Common patterns for fake/demo price IDs
        $fakePatterns = [
            'price_digital_',
            'price_physical_',
            'price_service_',
            'price_subscription_',
            'price_course_',
            'price_headphones',
            'price_fitness_',
        ];

        foreach ($fakePatterns as $pattern) {
            if (str_starts_with($priceId, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get or create Stripe price for product price
     */
    public function getOrCreateStripePrice($productPrice)
    {
        // Get price ID from gateway_data JSON
        $priceId = $productPrice->gateway_data['stripe']['price_id'] ?? null;
        $storedFingerprint = $productPrice->gateway_data['stripe']['price_fingerprint'] ?? null;

        // Recreate when missing, invalid (fake demo ID), or the price details changed
        // (Stripe prices are immutable once created)
        if (! $priceId || $this->isInvalidPriceId($priceId) || $storedFingerprint !== $this->priceFingerprint($productPrice)) {
            Log::info("Creating Stripe price for ProductPrice: {$productPrice->title}");
            $priceId = $this->createStripePrice($productPrice);

            if (! $priceId) {
                throw new \Exception("Failed to create Stripe price for '{$productPrice->title}'. Please contact support.");
            }
        }

        return $priceId;
    }

    /**
     * Fingerprint of the price attributes a Stripe price is built from
     */
    protected function priceFingerprint($productPrice): string
    {
        return implode('|', [
            number_format($productPrice->amount, 2, '.', ''),
            strtolower($productPrice->currency),
            $productPrice->billing_period,
        ]);
    }

    /**
     * Create Stripe Customer Portal session for payment method management
     */
    public function createCustomerPortalSession(User $user, string $returnUrl)
    {
        if (! $this->stripe) {
            return [
                'success' => false,
                'error' => 'Stripe credentials not configured',
            ];
        }

        try {
            // Get or create Stripe customer
            $stripeCustomerId = $this->getOrCreateStripeCustomer($user);

            if (! $stripeCustomerId) {
                throw new \Exception('Failed to create or retrieve Stripe customer');
            }

            // Create billing portal session
            $session = $this->stripe->billingPortal->sessions->create([
                'customer' => $stripeCustomerId,
                'return_url' => $returnUrl,
            ]);

            return [
                'success' => true,
                'url' => $session->url,
                'session_id' => $session->id,
            ];
        } catch (\Exception $e) {
            Log::error('Stripe customer portal session creation error: '.$e->getMessage(), [
                'user_id' => $user->id,
                'return_url' => $returnUrl,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get or create Stripe customer for a user
     */
    protected function getOrCreateStripeCustomer(User $user)
    {
        if (! $this->stripe) {
            Log::error('Stripe client not initialized', ['user_id' => $user->id]);

            return null;
        }

        try {
            // Check if user already has a Stripe customer ID stored
            $existingCustomerId = $user->gateway_data['stripe']['customer_id'] ?? null;

            if ($existingCustomerId) {
                // Verify the customer exists in Stripe
                try {
                    $customer = $this->stripe->customers->retrieve($existingCustomerId);
                    if ($customer && ! $customer->deleted) {
                        return $existingCustomerId;
                    }
                } catch (\Exception $e) {
                    Log::warning('Stored Stripe customer ID is invalid, creating new one', [
                        'user_id' => $user->id,
                        'stored_customer_id' => $existingCustomerId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Create new Stripe customer
            $customer = $this->stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ]);

            // Store customer ID in user's gateway data
            $gatewayData = $user->gateway_data ?? [];
            $gatewayData['stripe'] = array_merge($gatewayData['stripe'] ?? [], [
                'customer_id' => $customer->id,
            ]);

            $user->update(['gateway_data' => $gatewayData]);

            return $customer->id;
        } catch (\Exception $e) {
            Log::error('Failed to get or create Stripe customer: '.$e->getMessage(), [
                'user_id' => $user->id,
            ]);

            return null;
        }
    }
}
