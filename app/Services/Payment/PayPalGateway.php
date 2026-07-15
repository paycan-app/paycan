<?php

namespace App\Services\Payment;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Models\AmountWithBreakdown;
use PaypalServerSdkLib\Models\OrderApplicationContext;
use PaypalServerSdkLib\Models\OrderRequest;
use PaypalServerSdkLib\Models\PurchaseUnitRequest;
use PaypalServerSdkLib\Models\SubscriptionRequestPOST;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;

class PayPalGateway extends AbstractPaymentGateway
{
    protected $client;

    protected $gateway = 'paypal';

    public function __construct()
    {
        // Try to get credentials from settings first, then fall back to config
        $clientId = settings('paypal.client_id') ?? config('services.paypal.client_id');
        $clientSecret = settings('paypal.client_secret') ?? config('services.paypal.client_secret');

        // Only initialize if credentials are properly configured
        if ($clientId && $clientSecret &&
            ! str_starts_with($clientId, 'your_paypal_') &&
            ! str_starts_with($clientSecret, 'your_paypal_')) {

            $environment = (settings('paypal.mode') ?? config('services.paypal.mode')) === 'sandbox'
                ? Environment::SANDBOX
                : Environment::PRODUCTION;

            $this->client = PaypalServerSdkClientBuilder::init()
                ->clientCredentialsAuthCredentials(
                    ClientCredentialsAuthCredentialsBuilder::init(
                        $clientId,
                        $clientSecret
                    )
                )
                ->environment($environment)
                ->build();
        } else {
            // PayPal credentials not configured - will return errors for PayPal operations
            $this->client = null;
        }
    }

    public function createCheckoutSession(array $data)
    {
        if (! $this->client) {
            return [
                'success' => false,
                'message' => 'PayPal payments are currently unavailable. Please try Stripe or contact support.',
            ];
        }

        try {
            // Check if this is a subscription
            if (! empty($data['items'])) {
                $firstItem = $data['items'][0];
                if (isset($firstItem['price']) && $firstItem['price']->billing_period !== 'once') {
                    return $this->createSubscription($data);
                }
            }

            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $totalAmount += $item['amount'] * ($item['quantity'] ?? 1);
            }

            // Create purchase unit
            $amount = new AmountWithBreakdown(
                $data['currency'],
                number_format($totalAmount, 2, '.', '')
            );

            $purchaseUnit = new PurchaseUnitRequest($amount);
            $purchaseUnit->setCustomId((string) $data['order']->id);

            // Create application context
            $applicationContext = new OrderApplicationContext;
            $applicationContext->setReturnUrl($data['success_url']);
            $applicationContext->setCancelUrl($data['cancel_url']);
            $applicationContext->setBrandName(config('app.name'));
            $applicationContext->setLandingPage('BILLING');
            $applicationContext->setUserAction('PAY_NOW');

            // Create order request
            $orderRequest = new OrderRequest('CAPTURE', [$purchaseUnit]);
            $orderRequest->setApplicationContext($applicationContext);

            $response = $this->client->getOrdersController()->createOrder(['body' => $orderRequest]);

            if ($response->isError()) {
                throw new \Exception('PayPal API Error: '.json_encode($response->getError()));
            }

            $order = $response->getResult();
            $approvalUrl = null;

            if ($order->getLinks()) {
                foreach ($order->getLinks() as $link) {
                    if ($link->getRel() === 'approve') {
                        $approvalUrl = $link->getHref();
                        break;
                    }
                }
            }

            return [
                'success' => true,
                'id' => $order->getId(),
                'url' => $approvalUrl,
            ];
        } catch (\Exception $e) {
            Log::error('PayPal checkout session error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function handleWebhook(array $event): array
    {
        $eventType = $event['event_type'] ?? null;

        Log::info('Processing PayPal webhook event', [
            'event_type' => $eventType,
            'event_id' => $event['id'] ?? 'unknown',
        ]);

        return match ($eventType) {
            'CHECKOUT.ORDER.APPROVED' => $this->handleOrderApproved($event),
            'PAYMENT.CAPTURE.COMPLETED' => $this->handlePaymentSuccess($event),
            'PAYMENT.CAPTURE.DENIED' => $this->handlePaymentFailure($event),
            'PAYMENT.SALE.COMPLETED' => $this->handleSaleCompleted($event),
            'BILLING.SUBSCRIPTION.ACTIVATED' => $this->handleSubscriptionActivated($event),
            'BILLING.SUBSCRIPTION.SUSPENDED' => $this->handleSubscriptionSuspended($event),
            'BILLING.SUBSCRIPTION.EXPIRED' => $this->handleSubscriptionExpired($event),
            'BILLING.SUBSCRIPTION.CANCELLED' => $this->handleSubscriptionCancelled($event),
            'BILLING.SUBSCRIPTION.PAYMENT.FAILED' => $this->handleSubscriptionPaymentFailed($event),
            default => ['success' => true, 'action' => 'noop', 'message' => 'Unhandled event type'],
        };
    }

    public function handlePaymentSuccess(array $payload): array
    {
        $captureId = $payload['resource']['id'] ?? null;
        $supplementaryData = $payload['resource']['supplementary_data'] ?? $payload['supplementary_data'] ?? [];
        $relatedIds = $supplementaryData['related_ids'] ?? [];
        $paypalOrderId = $relatedIds['order_id'] ?? null;
        $customId = $payload['resource']['custom_id'] ?? null;

        if (! $captureId) {
            return ['success' => false, 'error' => 'No capture ID found'];
        }

        return [
            'success' => true,
            'action' => 'mark_order_paid_paypal',
            'capture_id' => $captureId,
            'paypal_order_id' => $paypalOrderId,
            'custom_id' => $customId,
            'gateway_data' => [
                'paypal_capture_id' => $captureId,
                'paypal_order_id' => $paypalOrderId,
                'capture_completed' => $payload,
            ],
            'transaction_data' => [
                'gateway_transaction_id' => $captureId,
                'gateway_data' => [
                    'paypal_order_id' => $paypalOrderId,
                    'capture_id' => $captureId,
                ],
            ],
        ];
    }

    public function handlePaymentFailure(array $payload): array
    {
        $orderId = $payload['resource']['id'] ?? null;

        if (! $orderId) {
            return ['success' => false, 'error' => 'No order ID found'];
        }

        return [
            'success' => true,
            'action' => 'mark_order_failed',
            'gateway_order_id' => $orderId,
            'failure_reason' => 'Payment capture denied',
            'gateway_data' => [
                'paypal_order_id' => $orderId,
                'denial_data' => $payload,
            ],
        ];
    }

    protected function handleOrderApproved(array $payload): array
    {
        $paypalOrderId = $payload['resource']['id'] ?? null;

        if (! $paypalOrderId) {
            return ['success' => false, 'action' => 'noop', 'error' => 'No PayPal order ID found'];
        }

        // Approval does NOT capture the payment. The buyer's return to our success
        // page triggers a capture, but that redirect is not guaranteed (closed tab,
        // network issues), so this webhook captures server-side as the reliable path.
        try {
            $orderDetails = $this->getOrderDetails($paypalOrderId);

            Log::info('PayPal order details fetched', [
                'paypal_order_id' => $paypalOrderId,
                'success' => $orderDetails['success'],
                'status' => $orderDetails['status'] ?? null,
                'custom_id' => $orderDetails['custom_id'] ?? null,
                'capture_id' => $orderDetails['capture_id'] ?? null,
            ]);

            if (! $orderDetails['success']) {
                Log::warning('Failed to fetch PayPal order details after approval', [
                    'paypal_order_id' => $paypalOrderId,
                    'error' => $orderDetails['error'] ?? 'Unknown error',
                ]);

                return ['success' => true, 'action' => 'noop', 'message' => 'Order approved but could not verify capture'];
            }

            // Check if order was captured
            if ($orderDetails['status'] === 'COMPLETED') {
                // Payment was captured - extract capture details
                $captureId = $orderDetails['capture_id'] ?? null;
                $customId = $orderDetails['custom_id'] ?? null;

                return [
                    'success' => true,
                    'action' => 'mark_order_paid_paypal',
                    'capture_id' => $captureId,
                    'paypal_order_id' => $paypalOrderId,
                    'custom_id' => $customId,
                    'gateway_data' => [
                        'paypal_capture_id' => $captureId,
                        'paypal_order_id' => $paypalOrderId,
                        'capture_verified_from_approval' => true,
                    ],
                    'transaction_data' => [
                        'gateway_transaction_id' => $captureId,
                        'gateway_data' => [
                            'paypal_order_id' => $paypalOrderId,
                            'capture_id' => $captureId,
                        ],
                    ],
                ];
            }

            // Approved but not captured yet - capture now so payment does not depend
            // on the buyer's browser returning to the success page
            if ($orderDetails['status'] === 'APPROVED') {
                $captureResult = $this->captureOrder($paypalOrderId);

                if ($captureResult['success'] && $captureResult['status'] === 'COMPLETED') {
                    // captureOrder() already marked the order paid and recorded the transaction
                    return ['success' => true, 'action' => 'noop', 'message' => 'Order captured from approval webhook'];
                }

                Log::warning('PayPal capture from approval webhook did not complete', [
                    'paypal_order_id' => $paypalOrderId,
                    'result' => $captureResult,
                ]);

                return ['success' => true, 'action' => 'noop', 'message' => 'Order approved, capture pending'];
            }

            return ['success' => true, 'action' => 'noop', 'message' => 'Order approved, waiting for capture'];
        } catch (\Exception $e) {
            Log::error('Error handling PayPal order approval', [
                'paypal_order_id' => $paypalOrderId,
                'error' => $e->getMessage(),
            ]);

            return ['success' => true, 'action' => 'noop', 'message' => 'Order approved but verification failed'];
        }
    }

    protected function handleSubscriptionActivated(array $payload): array
    {
        $subscriptionId = $payload['resource']['id'] ?? null;
        $customId = $payload['resource']['custom_id'] ?? null;

        if (! $subscriptionId || ! $customId) {
            return ['success' => false, 'error' => 'Missing subscription ID or custom ID'];
        }

        // Payment transactions (initial and renewals) are recorded uniformly by
        // the PAYMENT.SALE.COMPLETED webhook, keyed by the PayPal sale ID
        return [
            'success' => true,
            'action' => 'activate_subscription',
            'subscription_id' => $customId,
            'subscription_data' => [
                'gateway_subscription_id' => $subscriptionId,
                'gateway_status' => 'ACTIVE',
                'paypal_subscription_data' => $payload,
            ],
            'mark_order_paid' => true,
        ];
    }

    /**
     * Handle a completed PayPal sale (subscription payments, both initial and renewals)
     */
    protected function handleSaleCompleted(array $payload): array
    {
        $resource = $payload['resource'] ?? [];
        $saleId = $resource['id'] ?? null;
        $subscriptionId = $resource['billing_agreement_id'] ?? null;

        if (! $subscriptionId) {
            // Not a subscription payment (one-time orders are handled via capture events)
            return ['success' => true, 'action' => 'noop', 'message' => 'Sale without billing agreement ignored'];
        }

        if (! $saleId) {
            return ['success' => false, 'error' => 'Missing sale ID'];
        }

        return [
            'success' => true,
            'action' => 'create_subscription_transaction',
            'subscription_id' => $subscriptionId,
            'transaction_data' => [
                'type' => 'subscription_payment',
                'status' => 'completed',
                'amount' => $resource['amount']['total'] ?? null,
                'currency' => $resource['amount']['currency'] ?? null,
                'gateway_transaction_id' => $saleId,
                'gateway_data' => [
                    'paypal_subscription_id' => $subscriptionId,
                    'paypal_sale_id' => $saleId,
                ],
            ],
        ];
    }

    /**
     * Handle subscription suspension (our "cancel at period end" state)
     */
    protected function handleSubscriptionSuspended(array $payload): array
    {
        $subscriptionId = $payload['resource']['id'] ?? null;

        if (! $subscriptionId) {
            return ['success' => false, 'error' => 'Missing subscription ID'];
        }

        return [
            'success' => true,
            'action' => 'update_subscription_status',
            'subscription_id' => $subscriptionId,
            'status' => 'paused',
            'gateway_data' => [
                'gateway_status' => 'SUSPENDED',
                'paypal_suspension_data' => $payload,
            ],
        ];
    }

    /**
     * Handle subscription expiry
     */
    protected function handleSubscriptionExpired(array $payload): array
    {
        $subscriptionId = $payload['resource']['id'] ?? null;

        if (! $subscriptionId) {
            return ['success' => false, 'error' => 'Missing subscription ID'];
        }

        return [
            'success' => true,
            'action' => 'update_subscription_status',
            'subscription_id' => $subscriptionId,
            'status' => 'expired',
            'gateway_data' => [
                'gateway_status' => 'EXPIRED',
                'paypal_expiration_data' => $payload,
            ],
        ];
    }

    protected function handleSubscriptionCancelled(array $payload): array
    {
        $subscriptionId = $payload['resource']['id'] ?? null;

        if (! $subscriptionId) {
            return ['success' => false, 'error' => 'Missing subscription ID'];
        }

        return [
            'success' => true,
            'action' => 'cancel_subscription',
            'subscription_id' => $subscriptionId,
            'gateway_data' => [
                'gateway_status' => 'CANCELLED',
                'paypal_cancellation_data' => $payload,
            ],
        ];
    }

    protected function handleSubscriptionPaymentFailed(array $payload): array
    {
        $subscriptionId = $payload['resource']['id'] ?? null;

        if (! $subscriptionId) {
            return ['success' => false, 'error' => 'Missing subscription ID'];
        }

        return [
            'success' => true,
            'action' => 'update_subscription_status',
            'subscription_id' => $subscriptionId,
            'status' => 'past_due',
            'gateway_data' => [
                'gateway_status' => 'SUSPENDED',
                'paypal_failure_data' => $payload,
            ],
        ];
    }

    public function getPaymentDetails(string $paymentId)
    {
        if (! $this->client) {
            return [
                'success' => false,
                'error' => 'PayPal credentials not configured',
            ];
        }

        try {
            // Get PayPal order details
            $response = $this->client->getOrdersController()->getOrder(['id' => $paymentId]);

            if ($response->isError()) {
                throw new \Exception('PayPal API Error: '.json_encode($response->getError()));
            }

            $order = $response->getResult();

            return [
                'success' => true,
                'data' => [
                    'id' => $order->getId(),
                    'status' => $order->getStatus(),
                    'intent' => $order->getIntent(),
                    'amount' => $order->getPurchaseUnits()[0]->getAmount()->getValue(),
                    'currency' => $order->getPurchaseUnits()[0]->getAmount()->getCurrencyCode(),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('PayPal get payment details error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get detailed order information including capture details
     */
    protected function getOrderDetails(string $paypalOrderId): array
    {
        if (! $this->client) {
            return [
                'success' => false,
                'error' => 'PayPal credentials not configured',
            ];
        }

        try {
            $response = $this->client->getOrdersController()->getOrder(['id' => $paypalOrderId]);

            if ($response->isError()) {
                throw new \Exception('PayPal API Error: '.json_encode($response->getError()));
            }

            $order = $response->getResult();
            $purchaseUnits = $order->getPurchaseUnits();
            $status = $order->getStatus();
            $customId = $purchaseUnits[0]->getCustomId() ?? null;

            // Extract capture details if available
            $captureId = null;
            $payments = $purchaseUnits[0]->getPayments();
            if ($payments && $payments->getCaptures()) {
                $captures = $payments->getCaptures();
                if (! empty($captures)) {
                    $captureId = $captures[0]->getId();
                }
            }

            return [
                'success' => true,
                'status' => $status,
                'custom_id' => $customId,
                'capture_id' => $captureId,
                'order' => $order,
            ];
        } catch (\Exception $e) {
            Log::error('PayPal get order details error: '.$e->getMessage(), [
                'paypal_order_id' => $paypalOrderId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Capture a PayPal order
     */
    public function captureOrder(string $orderId): array
    {
        if (! $this->client) {
            return [
                'success' => false,
                'error' => 'PayPal credentials not configured',
            ];
        }

        try {
            $response = $this->client->getOrdersController()->captureOrder(['id' => $orderId]);

            if ($response->isError()) {
                throw new \Exception('PayPal API Error: '.json_encode($response->getError()));
            }

            $order = $response->getResult();
            $status = $order->getStatus();
            $purchaseUnits = $order->getPurchaseUnits();
            $customId = $purchaseUnits[0]->getCustomId() ?? null;

            // Extract capture details
            $captureId = null;
            $payments = $purchaseUnits[0]->getPayments();
            if ($payments && $payments->getCaptures()) {
                $captures = $payments->getCaptures();
                if (! empty($captures)) {
                    $captureId = $captures[0]->getId();
                }
            }

            // If capture was successful, mark the order as paid
            if ($status === 'COMPLETED' && $captureId && $customId) {
                $internalOrder = \App\Models\Order::find($customId);
                if ($internalOrder && $internalOrder->status === 'pending') {
                    $orderService = app(\App\Services\Order\OrderService::class);
                    $orderService->markOrderAsPaid($internalOrder, [
                        'paypal_capture_id' => $captureId,
                        'paypal_order_id' => $orderId,
                        'captured_on_return' => true,
                    ]);

                    // Create transaction
                    $orderService->createTransactionForOrder($internalOrder, [
                        'type' => 'payment',
                        'status' => 'completed',
                        'gateway_transaction_id' => $captureId,
                        'gateway_data' => [
                            'paypal_order_id' => $orderId,
                            'capture_id' => $captureId,
                        ],
                    ]);
                }
            }

            return [
                'success' => true,
                'status' => $status,
                'capture_id' => $captureId,
                'custom_id' => $customId,
            ];
        } catch (\Exception $e) {
            Log::error('PayPal capture order error: '.$e->getMessage(), [
                'order_id' => $orderId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function refundPayment(string $paymentId, ?float $amount = null)
    {
        if (! $this->client) {
            return [
                'success' => false,
                'error' => 'PayPal credentials not configured',
            ];
        }

        try {
            // Get the capture ID from the order
            $orderResponse = $this->client->getOrdersController()->getOrder(['id' => $paymentId]);

            if ($orderResponse->isError()) {
                throw new \Exception('Failed to get PayPal order: '.json_encode($orderResponse->getError()));
            }

            $order = $orderResponse->getResult();
            $purchaseUnits = $order->getPurchaseUnits();

            if (empty($purchaseUnits)) {
                throw new \Exception('No purchase units found in PayPal order');
            }

            $payments = $purchaseUnits[0]->getPayments();
            $captures = $payments ? $payments->getCaptures() : null;

            if (empty($captures)) {
                throw new \Exception('No captures found for this PayPal order');
            }

            // Get the first capture ID
            $captureId = $captures[0]->getId();

            // Prepare refund request
            $refundRequest = [];

            if ($amount !== null) {
                // Partial refund
                $refundRequest['amount'] = [
                    'value' => number_format($amount, 2, '.', ''),
                    'currency_code' => $captures[0]->getAmount()->getCurrencyCode(),
                ];
            }
            // If amount is null, it will be a full refund (no amount specified)

            // Process the refund
            $refundResponse = $this->client->getPaymentsController()->capturesRefund(
                $captureId,
                $refundRequest
            );

            if ($refundResponse->isError()) {
                throw new \Exception('PayPal Refund API Error: '.json_encode($refundResponse->getError()));
            }

            $refund = $refundResponse->getResult();

            return [
                'success' => true,
                'refund_id' => $refund->getId(),
                'status' => $refund->getStatus(),
                'amount' => $refund->getAmount() ? $refund->getAmount()->getValue() : null,
                'currency' => $refund->getAmount() ? $refund->getAmount()->getCurrencyCode() : null,
                'create_time' => $refund->getCreateTime(),
            ];
        } catch (\Exception $e) {
            Log::error('PayPal refund error: '.$e->getMessage(), [
                'payment_id' => $paymentId,
                'amount' => $amount,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function createSubscription(array $data)
    {
        if (! $this->client) {
            return [
                'success' => false,
                'error' => 'PayPal credentials not configured',
            ];
        }

        try {
            $productPrice = $data['items'][0]['price'];
            $planId = $this->getOrCreatePayPalPlan($productPrice);

            if (! $planId) {
                throw new \Exception('Failed to create or get PayPal billing plan');
            }

            // Create subscription request
            $subscriptionRequest = SubscriptionRequestPOST::fromArray([
                'plan_id' => $planId,
                'subscriber' => [
                    'email_address' => $data['user']->email,
                    'name' => [
                        'given_name' => explode(' ', $data['user']->name)[0] ?? 'Customer',
                        'surname' => explode(' ', $data['user']->name)[1] ?? '',
                    ],
                ],
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'locale' => 'en-US',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'SUBSCRIBE_NOW',
                    'payment_method' => [
                        'payer_selected' => 'PAYPAL',
                        'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                    ],
                    'return_url' => $data['success_url'],
                    'cancel_url' => $data['cancel_url'],
                ],
                'custom_id' => (string) $data['subscription']->id,
            ]);

            $response = $this->client->getSubscriptionsController()->subscriptionsCreate($subscriptionRequest);

            if ($response->isError()) {
                throw new \Exception('PayPal Subscription API Error: '.json_encode($response->getError()));
            }

            $subscription = $response->getResult();
            $approvalUrl = null;

            if ($subscription->getLinks()) {
                foreach ($subscription->getLinks() as $link) {
                    if ($link->getRel() === 'approve') {
                        $approvalUrl = $link->getHref();
                        break;
                    }
                }
            }

            return [
                'success' => true,
                'id' => $subscription->getId(),
                'status' => $subscription->getStatus(),
                'url' => $approvalUrl,
            ];
        } catch (\Exception $e) {
            Log::error('PayPal subscription creation error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function cancelSubscription(string $subscriptionId, bool $immediately = false)
    {
        if (! $this->client) {
            return [
                'success' => false,
                'error' => 'PayPal credentials not configured',
            ];
        }

        try {
            $clientId = settings('paypal.client_id') ?? config('services.paypal.client_id');
            $clientSecret = settings('paypal.client_secret') ?? config('services.paypal.client_secret');

            $mode = (settings('paypal.mode') ?? config('services.paypal.mode')) === 'sandbox' ? 'sandbox' : 'live';
            $baseUrl = $mode === 'sandbox'
                ? 'https://api-m.sandbox.paypal.com'
                : 'https://api-m.paypal.com';

            $token = $this->getPayPalAccessToken($clientId, $clientSecret, $baseUrl);
            if (! $token) {
                return [
                    'success' => false,
                    'error' => 'Failed to obtain PayPal access token',
                ];
            }

            // Fetch details first so we can report the paid-through date (ISO8601)
            $getResponse = Http::withToken($token)
                ->get("{$baseUrl}/v1/billing/subscriptions/{$subscriptionId}");

            $currentPeriodEnd = $getResponse->successful()
                ? $getResponse->json('billing_info.next_billing_time')
                : null;

            if ($immediately) {
                // Irreversible on PayPal - the subscription cannot be resumed afterwards
                $response = Http::withToken($token)
                    ->post("{$baseUrl}/v1/billing/subscriptions/{$subscriptionId}/cancel", [
                        'reason' => 'User requested cancellation',
                    ]);
                $gatewayStatus = 'CANCELLED';
            } else {
                // Suspend so the user can still resume before the period ends
                $response = Http::withToken($token)
                    ->post("{$baseUrl}/v1/billing/subscriptions/{$subscriptionId}/suspend", [
                        'reason' => 'User requested cancellation',
                    ]);
                $gatewayStatus = 'SUSPENDED';
            }

            if (! $response->successful()) {
                return [
                    'success' => false,
                    'error' => 'PayPal '.($immediately ? 'cancel' : 'suspend').' failed: '.$response->body(),
                ];
            }

            return [
                'success' => true,
                'gateway_status' => $gatewayStatus,
                'current_period_end' => $currentPeriodEnd, // ISO8601
                'canceled_at' => now()->timestamp,
            ];
        } catch (\Exception $e) {
            Log::error('PayPal cancel subscription error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function resumeSubscription(string $subscriptionId)
    {
        if (! $this->client) {
            return [
                'success' => false,
                'error' => 'PayPal credentials not configured',
            ];
        }

        try {
            $clientId = settings('paypal.client_id') ?? config('services.paypal.client_id');
            $clientSecret = settings('paypal.client_secret') ?? config('services.paypal.client_secret');

            $mode = (settings('paypal.mode') ?? config('services.paypal.mode')) === 'sandbox' ? 'sandbox' : 'live';
            $baseUrl = $mode === 'sandbox'
                ? 'https://api-m.sandbox.paypal.com'
                : 'https://api-m.paypal.com';

            $token = $this->getPayPalAccessToken($clientId, $clientSecret, $baseUrl);
            if (! $token) {
                return [
                    'success' => false,
                    'error' => 'Failed to obtain PayPal access token',
                ];
            }

            // Fetch subscription to check current status
            $getResponse = Http::withToken($token)
                ->get("{$baseUrl}/v1/billing/subscriptions/{$subscriptionId}");

            if (! $getResponse->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to fetch PayPal subscription: '.$getResponse->body(),
                ];
            }

            $status = $getResponse->json('status');

            if ($status === 'CANCELLED') {
                return [
                    'success' => false,
                    'error' => 'Cancelled subscriptions cannot be resumed on PayPal. Create a new subscription.',
                ];
            }

            // Reactivate if suspended
            $activateResponse = Http::withToken($token)
                ->post("{$baseUrl}/v1/billing/subscriptions/{$subscriptionId}/activate", [
                    'reason' => 'User requested reactivation',
                ]);

            if (! $activateResponse->successful()) {
                return [
                    'success' => false,
                    'error' => 'PayPal activate failed: '.$activateResponse->body(),
                ];
            }

            return [
                'success' => true,
                'status' => 'ACTIVE',
            ];
        } catch (\Exception $e) {
            Log::error('PayPal resume subscription error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getPayPalAccessToken(string $clientId, string $clientSecret, string $baseUrl): ?string
    {
        try {
            $response = Http::asForm()
                ->withBasicAuth($clientId, $clientSecret)
                ->post("{$baseUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            if (! $response->successful()) {
                Log::error('PayPal OAuth failed', ['body' => $response->body()]);

                return null;
            }

            return $response->json('access_token');
        } catch (\Throwable $e) {
            Log::error('PayPal OAuth exception', ['error' => $e->getMessage()]);

            return null;
        }
    }

    public function changeSubscriptionPlan(string $subscriptionId, string $newPlanId)
    {
        if (! $this->client) {
            return [
                'success' => false,
                'error' => 'PayPal credentials not configured',
            ];
        }

        try {
            $response = $this->client->getSubscriptionsController()->subscriptionsRevise(
                $subscriptionId,
                ['plan_id' => $newPlanId]
            );

            if ($response->isError()) {
                throw new \Exception('PayPal Revise Subscription API Error: '.json_encode($response->getError()));
            }

            // PayPal requires the customer to approve the plan change; extract the
            // approval link so the caller can redirect them
            $approvalUrl = null;
            $result = $response->getResult();

            if (is_object($result) && method_exists($result, 'getLinks') && $result->getLinks()) {
                foreach ($result->getLinks() as $link) {
                    if ($link->getRel() === 'approve') {
                        $approvalUrl = $link->getHref();
                        break;
                    }
                }
            }

            return [
                'success' => true,
                'subscription_id' => $subscriptionId,
                'approval_url' => $approvalUrl,
            ];
        } catch (\Exception $e) {
            Log::error('PayPal plan change error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create PayPal Customer Portal equivalent (redirect to PayPal account management)
     */
    public function createCustomerPortalSession(User $user, string $returnUrl)
    {
        try {
            // PayPal doesn't have a direct customer portal like Stripe
            // Instead, we direct users to their PayPal account management
            $paypalAccountUrl = (settings('paypal.mode') ?? config('services.paypal.mode')) === 'sandbox'
                ? 'https://www.sandbox.paypal.com/myaccount/autopay/'
                : 'https://www.paypal.com/myaccount/autopay/';

            return [
                'success' => true,
                'url' => $paypalAccountUrl,
                'message' => 'PayPal payments are managed through your PayPal account',
            ];
        } catch (\Exception $e) {
            Log::error('PayPal customer portal session creation error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get or create PayPal billing plan for subscription
     */
    protected function getOrCreatePayPalPlan($productPrice)
    {
        try {
            // Reuse the stored plan only when the price details it was built from
            // are unchanged (PayPal plans are immutable once created)
            $planId = $productPrice->gateway_data['paypal']['plan_id'] ?? null;
            $storedFingerprint = $productPrice->gateway_data['paypal']['plan_fingerprint'] ?? null;
            $fingerprint = $this->planFingerprint($productPrice);

            if ($planId && ! $this->isInvalidPlanId($planId) && $storedFingerprint === $fingerprint) {
                return $planId;
            }

            // Create new PayPal billing plan
            $planId = $this->createPayPalPlan($productPrice);

            if ($planId) {
                // Update the ProductPrice model with PayPal plan ID
                $currentGatewayData = $productPrice->gateway_data ?? [];
                $currentGatewayData['paypal'] = [
                    'plan_id' => $planId,
                    'plan_fingerprint' => $fingerprint,
                ];

                $productPrice->update(['gateway_data' => $currentGatewayData]);

                Log::info("Created PayPal plan: {$planId} for ProductPrice: {$productPrice->title}");
            }

            return $planId;
        } catch (\Exception $e) {
            Log::error("Failed to get or create PayPal plan for ProductPrice ID {$productPrice->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Fingerprint of the price attributes a PayPal plan is built from
     */
    protected function planFingerprint($productPrice): string
    {
        return implode('|', [
            number_format($productPrice->amount, 2, '.', ''),
            strtoupper($productPrice->currency),
            $productPrice->billing_period,
            (int) ($productPrice->trial_days ?? 0),
        ]);
    }

    /**
     * Create a new PayPal billing plan
     */
    protected function createPayPalPlan($productPrice)
    {
        if (! $this->client) {
            return null;
        }

        try {
            $productName = $productPrice->product->title.' - '.$productPrice->title;

            $billingCycles = [];
            $sequence = 1;

            // Free trial cycle so the customer is not charged during the trial
            $trialDays = (int) ($productPrice->trial_days ?? 0);
            if ($trialDays > 0) {
                $billingCycles[] = [
                    'frequency' => [
                        'interval_unit' => 'DAY',
                        'interval_count' => $trialDays,
                    ],
                    'tenure_type' => 'TRIAL',
                    'sequence' => $sequence++,
                    'total_cycles' => 1,
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => '0',
                            'currency_code' => strtoupper($productPrice->currency),
                        ],
                    ],
                ];
            }

            $billingCycles[] = [
                'frequency' => [
                    'interval_unit' => $this->mapBillingPeriodToPayPal($productPrice->billing_period),
                    'interval_count' => 1,
                ],
                'tenure_type' => 'REGULAR',
                'sequence' => $sequence,
                'total_cycles' => 0, // 0 = infinite
                'pricing_scheme' => [
                    'fixed_price' => [
                        'value' => number_format($productPrice->amount, 2, '.', ''),
                        'currency_code' => strtoupper($productPrice->currency),
                    ],
                ],
            ];

            // Create billing plan request
            $planRequest = [
                'product_id' => $this->getOrCreatePayPalProduct($productPrice->product),
                'name' => $productName,
                'description' => $productPrice->product->description ?: "Subscription to {$productName}",
                'status' => 'ACTIVE',
                'billing_cycles' => $billingCycles,
                'payment_preferences' => [
                    'auto_bill_outstanding' => true,
                    'setup_fee_failure_action' => 'CONTINUE',
                    'payment_failure_threshold' => 3,
                ],
            ];

            $response = $this->client->getBillingPlansController()->plansCreate($planRequest);

            if ($response->isError()) {
                throw new \Exception('PayPal Create Plan API Error: '.json_encode($response->getError()));
            }

            $plan = $response->getResult();

            return $plan->getId();
        } catch (\Exception $e) {
            Log::error("Failed to create PayPal plan for ProductPrice ID {$productPrice->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Get or create PayPal product for billing plan
     */
    protected function getOrCreatePayPalProduct($product)
    {
        try {
            // Check if product already exists in gateway_data
            $productId = $product->gateway_data['paypal']['product_id'] ?? null;

            if ($productId) {
                return $productId;
            }

            // Create new PayPal product
            $productRequest = [
                'id' => 'PROD_'.strtoupper(uniqid()),
                'name' => $product->title,
                'description' => $product->description ?: "Product: {$product->title}",
                'type' => 'SERVICE', // PayPal product type for subscriptions
                'category' => 'SOFTWARE',
            ];

            $response = $this->client->getCatalogProductsController()->productsCreate($productRequest);

            if ($response->isError()) {
                throw new \Exception('PayPal Create Product API Error: '.json_encode($response->getError()));
            }

            $paypalProduct = $response->getResult();

            // Update the Product model with PayPal product ID
            $currentGatewayData = $product->gateway_data ?? [];
            $currentGatewayData['paypal'] = [
                'product_id' => $paypalProduct->getId(),
            ];

            $product->update(['gateway_data' => $currentGatewayData]);

            return $paypalProduct->getId();
        } catch (\Exception $e) {
            Log::error("Failed to create PayPal product for Product ID {$product->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Map billing period to PayPal interval unit
     */
    protected function mapBillingPeriodToPayPal($billingPeriod)
    {
        $mapping = [
            'daily' => 'DAY',
            'weekly' => 'WEEK',
            'monthly' => 'MONTH',
            'yearly' => 'YEAR',
        ];

        return $mapping[$billingPeriod] ?? 'MONTH';
    }

    /**
     * Check if a plan ID is invalid/fake/demo
     */
    protected function isInvalidPlanId($planId)
    {
        // Real PayPal plan IDs start with 'P-' and are longer
        if (strlen($planId) < 10 || ! str_starts_with($planId, 'P-')) {
            return true;
        }

        // Common patterns for fake/demo plan IDs
        $fakePatterns = [
            'P-digital_',
            'P-physical_',
            'P-service_',
            'P-subscription_',
        ];

        foreach ($fakePatterns as $pattern) {
            if (str_starts_with($planId, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
