<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\ProductPrice;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function createCheckoutSession(User $user, ProductPrice $productPrice, array $options = [])
    {
        $gateway = $options['gateway'] ?? 'stripe';

        try {
            // Validate user has email
            if (empty($user->email)) {
                throw new \Exception('User email is required for payment processing');
            }

            // Note: Gateway configuration will be created dynamically if needed

            $paymentGateway = PaymentGatewayFactory::create($gateway);

            // Create an order
            $quantity = $options['quantity'] ?? 1;
            $total = $productPrice->amount * $quantity;

            $order = Order::create([
                'user_id' => $user->id,
                'product_id' => $productPrice->product_id,
                'product_price_id' => $productPrice->id,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'total' => $total,
                'currency' => $productPrice->currency,
                'tax' => 0,
                'billing_email' => $user->email,
                'billing_name' => $user->name,
                'gateway' => $gateway,
                'notes' => $options['customer_note'] ?? null,
            ]);

            $successUrl = $options['success_url'] ?? route('payment.success', ['order' => $order->id]);
            $cancelUrl = $options['cancel_url'] ?? route('payment.cancel', ['order' => $order->id]);

            // Ensure product relationship is loaded for price creation
            $productPrice->load('product');

            $data = [
                'user' => $user,
                'order' => $order,
                'items' => [
                    [
                        'price' => $productPrice,
                        'name' => $productPrice->product->title.' - '.$productPrice->title,
                        'amount' => $productPrice->amount,
                        'currency' => $productPrice->currency,
                        'quantity' => $options['quantity'] ?? 1,
                    ],
                ],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'currency' => $productPrice->currency,
            ];

            $response = $paymentGateway->createCheckoutSession($data);

            if ($response['success']) {
                $order->update([
                    'gateway_order_id' => $response['id'],
                    'gateway_data' => [
                        'checkout_url' => $response['url'],
                    ],
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Payment service error: '.$e->getMessage());
            throw $e;
        }
    }

    public function createSubscription(User $user, ProductPrice $productPrice, array $options = [])
    {
        $gateway = $options['gateway'] ?? 'stripe';

        try {
            $paymentGateway = PaymentGatewayFactory::create($gateway);

            // Validate that product price is a subscription
            if ($productPrice->billing_period === 'once') {
                throw new \Exception('Product price is not a subscription');
            }

            // Check for existing active subscription to prevent concurrent creation
            // Only consider truly active subscriptions, not incomplete ones
            $existingSubscription = Subscription::where('user_id', $user->id)
                ->where('product_price_id', $productPrice->id)
                ->whereIn('status', ['active', 'trialing'])
                ->first();

            if ($existingSubscription) {
                throw new \Exception('User already has an active subscription for this product');
            }

            // Create an order for the initial payment
            $order = Order::create([
                'user_id' => $user->id,
                'product_id' => $productPrice->product_id,
                'product_price_id' => $productPrice->id,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'total' => $productPrice->amount,
                'currency' => $productPrice->currency,
                'tax' => 0,
                'billing_email' => $user->email,
                'billing_name' => $user->name,
                'gateway' => $gateway,
            ]);

            // Create a subscription record
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'product_id' => $productPrice->product_id,
                'product_price_id' => $productPrice->id,
                'order_id' => $order->id,
                'title' => $productPrice->product->title.' - '.$productPrice->title,
                'status' => 'incomplete',
                'gateway' => $gateway,
                'trial_ends_at' => $productPrice->trial_days > 0
                    ? now()->addDays($productPrice->trial_days)
                    : null,
            ]);

            $successUrl = $options['success_url'] ?? route('payment.success', ['order' => $order->id]);
            $cancelUrl = $options['cancel_url'] ?? route('payment.cancel', ['order' => $order->id]);

            // Ensure product relationship is loaded for price creation
            $productPrice->load('product');

            $data = [
                'user' => $user,
                'order' => $order,
                'subscription' => $subscription,
                'items' => [
                    [
                        'price' => $productPrice,
                        'name' => $productPrice->product->title.' - '.$productPrice->title,
                        'amount' => $productPrice->amount,
                        'currency' => $productPrice->currency,
                        'quantity' => 1, // Subscriptions are always quantity 1
                    ],
                ],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'currency' => $productPrice->currency,
            ];

            // Use createCheckoutSession for subscriptions - it will automatically detect subscription mode
            $response = $paymentGateway->createCheckoutSession($data);

            if ($response['success']) {
                $subscription->update([
                    'gateway_subscription_id' => $response['id'],
                    'gateway_status' => $response['status'] ?? 'incomplete', // Default status for checkout sessions
                    'gateway_data' => $response,
                ]);

                $order->update([
                    'gateway_order_id' => $response['id'],
                    'gateway_data' => [
                        'checkout_url' => $response['url'] ?? null,
                    ],
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Subscription service error: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Cancel a subscription at the payment gateway and sync local state.
     *
     * By default the subscription is cancelled at the end of the current billing
     * period. Pass $immediately = true to stop it right away.
     */
    public function cancelSubscription(Subscription $subscription, bool $immediately = false)
    {
        try {
            $paymentGateway = PaymentGatewayFactory::create($subscription->gateway);
            $response = $paymentGateway->cancelSubscription($subscription->gateway_subscription_id, $immediately);

            if ($response['success']) {
                $endsAt = null;
                if (isset($response['current_period_end'])) {
                    $cpe = $response['current_period_end'];
                    $endsAt = is_numeric($cpe)
                        ? \Carbon\Carbon::createFromTimestamp($cpe)
                        : \Carbon\Carbon::parse($cpe);
                }

                $canceledAt = null;
                if (isset($response['canceled_at'])) {
                    $ca = $response['canceled_at'];
                    $canceledAt = is_numeric($ca)
                        ? \Carbon\Carbon::createFromTimestamp($ca)
                        : \Carbon\Carbon::parse($ca);
                }

                $subscription->update([
                    'status' => 'canceled',
                    'gateway_status' => $response['gateway_status'] ?? ($response['status'] ?? null),
                    'canceled_at' => $canceledAt ?? now(),
                    'ends_at' => $immediately ? now() : ($endsAt ?? now()),
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Subscription cancellation error: '.$e->getMessage());
            throw $e;
        }
    }

    public function resumeSubscription(Subscription $subscription)
    {
        try {
            $paymentGateway = PaymentGatewayFactory::create($subscription->gateway);
            $response = $paymentGateway->resumeSubscription($subscription->gateway_subscription_id);

            if ($response['success']) {
                $subscription->update([
                    'status' => 'active',
                    'gateway_status' => $response['status'] ?? null,
                    'canceled_at' => null,
                    'ends_at' => null,
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Subscription resumption error: '.$e->getMessage());
            throw $e;
        }
    }

    public function changeSubscriptionPlan(Subscription $subscription, ProductPrice $newProductPrice)
    {
        try {
            // Validate that new product price is a subscription
            if ($newProductPrice->billing_period === 'once') {
                throw new \Exception('New product price is not a subscription');
            }

            $paymentGateway = PaymentGatewayFactory::create($subscription->gateway);

            // Get gateway-specific price ID, with dynamic creation support
            $newPriceId = null;

            if ($subscription->gateway === 'stripe') {
                // For Stripe, let the gateway handle price creation dynamically
                $newPriceId = $paymentGateway->getOrCreateStripePrice($newProductPrice);

                if (! $newPriceId) {
                    throw new \Exception('Failed to get or create Stripe price for the new plan');
                }
            } else {
                // For other gateways, use the stored price ID
                $newPriceId = $newProductPrice->gateway_data[$subscription->gateway]['price_id'] ?? null;

                if (! $newPriceId) {
                    throw new \Exception('No price ID found for this gateway');
                }
            }

            $response = $paymentGateway->changeSubscriptionPlan(
                $subscription->gateway_subscription_id,
                $newPriceId
            );

            // When the gateway requires customer approval (PayPal revise), keep the
            // local plan unchanged until the approval webhook confirms the switch
            if ($response['success'] && empty($response['approval_url'])) {
                $subscription->update([
                    'product_price_id' => $newProductPrice->id,
                    'title' => $newProductPrice->product->title.' - '.$newProductPrice->title,
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Subscription plan change error: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Create customer portal session for payment method management
     */
    public function createCustomerPortalSession(User $user, string $gateway, string $returnUrl)
    {
        try {
            $paymentGateway = PaymentGatewayFactory::create($gateway);

            return $paymentGateway->createCustomerPortalSession($user, $returnUrl);
        } catch (\Exception $e) {
            Log::error('Customer portal session creation error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function generateOrderNumber()
    {
        return 'ORD-'.time().'-'.rand(1000, 9999);
    }

    /**
     * Calculate order totals for a product price.
     *
     * Tax is intentionally not calculated here: it is handled by the payment
     * gateway (Stripe Tax / PayPal tax settings), so the recorded total always
     * matches the amount the gateway charges.
     *
     * @return array{subtotal: float, total: float, currency: string}
     */
    public function calculateTotals(ProductPrice $price, int $quantity = 1): array
    {
        $subtotal = round($price->amount * $quantity, 2);

        return [
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'currency' => $price->currency,
        ];
    }
}
