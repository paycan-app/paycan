<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Subscription;
use App\Services\Order\OrderService;
use App\Services\Subscription\SubscriptionService;
use Illuminate\Support\Facades\Log;

class WebhookProcessingService
{
    public function __construct(
        protected OrderService $orderService,
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Process normalized webhook data from any gateway
     */
    public function processWebhookAction(array $normalizedData): array
    {
        if (! isset($normalizedData['action'])) {
            return ['success' => false, 'error' => 'No action specified'];
        }

        $action = $normalizedData['action'];

        return match ($action) {
            'noop' => ['success' => true, 'message' => $normalizedData['message'] ?? 'No operation required'],
            'mark_order_paid' => $this->handleOrderPayment($normalizedData),
            'mark_order_paid_by_payment_intent' => $this->handleOrderPaymentByPaymentIntent($normalizedData),
            'mark_order_paid_paypal' => $this->handlePayPalOrderPayment($normalizedData),
            'mark_order_failed' => $this->handleOrderFailure($normalizedData),
            'mark_order_failed_by_payment_intent' => $this->handleOrderFailureByPaymentIntent($normalizedData),
            'activate_subscription' => $this->handleSubscriptionActivation($normalizedData),
            'update_subscription_status' => $this->handleSubscriptionStatusUpdate($normalizedData),
            'cancel_subscription' => $this->handleSubscriptionCancellation($normalizedData),
            'create_subscription_transaction' => $this->handleSubscriptionTransaction($normalizedData),
            'handle_invoice_payment_failed' => $this->handleInvoicePaymentFailed($normalizedData),
            default => ['success' => true, 'message' => 'Unhandled action type'],
        };
    }

    /**
     * Handle order payment (Stripe checkout completed)
     */
    protected function handleOrderPayment(array $data): array
    {
        $gatewayOrderId = $data['gateway_order_id'] ?? null;

        if (! $gatewayOrderId) {
            return ['success' => false, 'error' => 'Missing gateway order ID'];
        }

        $order = Order::where('gateway_order_id', $gatewayOrderId)->first();

        if (! $order) {
            Log::warning('Order not found for gateway order ID', ['gateway_order_id' => $gatewayOrderId]);

            return ['success' => false, 'error' => 'Order not found'];
        }

        // Mark order as paid
        $this->orderService->markOrderAsPaid($order, $data['gateway_data'] ?? []);

        // Create transaction
        if (isset($data['transaction_data'])) {
            $this->orderService->createTransactionForOrder($order, array_merge(
                ['type' => 'payment', 'status' => 'completed'],
                $data['transaction_data']
            ));
        }

        // Handle subscription if present
        if (isset($data['subscription_action']) && $data['subscription_action'] === 'activate') {
            $this->handleSubscriptionFromCheckout($order, $data['subscription_data'] ?? []);
        }

        return ['success' => true, 'message' => 'Order payment processed successfully'];
    }

    /**
     * Handle order payment by payment intent ID
     */
    protected function handleOrderPaymentByPaymentIntent(array $data): array
    {
        $paymentIntentId = $data['payment_intent_id'] ?? null;

        if (! $paymentIntentId) {
            return ['success' => false, 'error' => 'Missing payment intent ID'];
        }

        $order = $this->findOrderForPaymentIntent($data);

        if ($order && $order->status === 'pending') {
            $this->orderService->markOrderAsPaid($order, $data['gateway_data'] ?? []);

            $this->orderService->createTransactionForOrder($order, [
                'type' => 'payment',
                'status' => 'completed',
                'gateway_transaction_id' => $paymentIntentId,
                'gateway_data' => $data['gateway_data'] ?? [],
            ]);
        }

        return ['success' => true, 'message' => 'Payment intent processed successfully'];
    }

    /**
     * Handle PayPal order payment
     */
    protected function handlePayPalOrderPayment(array $data): array
    {
        $captureId = $data['capture_id'] ?? null;
        $paypalOrderId = $data['paypal_order_id'] ?? null;
        $customId = $data['custom_id'] ?? null;

        if (! $captureId) {
            return ['success' => false, 'error' => 'No capture ID found'];
        }

        // Try to find order by:
        // 1. Custom ID (our internal order ID from PayPal's custom_id field)
        // 2. PayPal order ID in gateway_order_id field
        // 3. Capture ID in meta field
        $order = null;

        if ($customId) {
            $order = Order::with('productPrice')->find($customId);
        }

        if (! $order && $paypalOrderId) {
            $order = Order::with('productPrice')->where('gateway_order_id', $paypalOrderId)->first();
        }

        if (! $order) {
            $order = Order::with('productPrice')->whereJsonContains('meta->paypal_capture_id', $captureId)->first();
        }

        if (! $order) {
            Log::warning('Order not found for PayPal capture', [
                'capture_id' => $captureId,
                'paypal_order_id' => $paypalOrderId,
                'custom_id' => $customId,
            ]);

            return ['success' => false, 'error' => 'Order not found'];
        }

        // Mark order as paid
        $this->orderService->markOrderAsPaid($order, $data['gateway_data'] ?? []);

        // Create transaction
        if (isset($data['transaction_data'])) {
            $this->orderService->createTransactionForOrder($order, array_merge(
                ['type' => 'payment', 'status' => 'completed'],
                $data['transaction_data']
            ));
        }

        // Handle subscription if this was a subscription order
        if ($order->productPrice && $order->productPrice->billing_period !== 'once') {
            $this->handlePayPalSubscriptionFromCheckout($order, $paypalOrderId, $captureId);
        }

        return ['success' => true, 'message' => 'Capture processed successfully'];
    }

    /**
     * Handle order failure
     */
    protected function handleOrderFailure(array $data): array
    {
        $gatewayOrderId = $data['gateway_order_id'] ?? null;

        if (! $gatewayOrderId) {
            return ['success' => false, 'error' => 'Missing gateway order ID'];
        }

        $order = Order::where('gateway_order_id', $gatewayOrderId)->first();

        if ($order) {
            $this->orderService->markOrderAsFailed(
                $order,
                $data['failure_reason'] ?? 'Payment failed',
                $data['gateway_data'] ?? []
            );
        }

        return ['success' => true, 'message' => 'Payment failure processed'];
    }

    /**
     * Handle order failure by payment intent
     */
    protected function handleOrderFailureByPaymentIntent(array $data): array
    {
        $paymentIntentId = $data['payment_intent_id'] ?? null;

        if (! $paymentIntentId) {
            return ['success' => false, 'error' => 'Missing payment intent ID'];
        }

        $order = $this->findOrderForPaymentIntent($data);

        if ($order && $order->status === 'pending') {
            $this->orderService->markOrderAsFailed(
                $order,
                $data['failure_reason'] ?? 'Payment failed',
                $data['gateway_data'] ?? []
            );
        }

        return ['success' => true, 'message' => 'Payment failure processed successfully'];
    }

    /**
     * Locate the order a Stripe payment intent belongs to.
     *
     * Prefers the order_id the gateway stamped into the payment intent's
     * metadata at checkout-session creation; falls back to orders that stored
     * the intent ID in meta (legacy records).
     */
    protected function findOrderForPaymentIntent(array $data): ?Order
    {
        if (! empty($data['order_id'])) {
            $order = Order::find($data['order_id']);

            if ($order) {
                return $order;
            }
        }

        return Order::whereJsonContains('meta->stripe_payment_intent', $data['payment_intent_id'])->first();
    }

    /**
     * Handle subscription activation from checkout.
     *
     * The initial payment transaction is not recorded here: Stripe's
     * invoice.payment_succeeded webhook creates it with the real invoice
     * amount, which avoids duplicate transaction records for one charge.
     */
    protected function handleSubscriptionFromCheckout(Order $order, array $subscriptionData): void
    {
        $subscription = Subscription::where('order_id', $order->id)->first();

        if (! $subscription || $subscription->status !== 'incomplete') {
            return;
        }

        $this->subscriptionService->activateSubscription($subscription, $subscriptionData);
    }

    /**
     * Handle PayPal subscription from checkout.
     *
     * The payment itself is already recorded as the order's transaction, so
     * only the subscription state is updated here (no duplicate transaction).
     */
    protected function handlePayPalSubscriptionFromCheckout(Order $order, ?string $paypalOrderId, string $captureId): void
    {
        $subscription = Subscription::where('order_id', $order->id)->first();

        if (! $subscription || $subscription->status !== 'incomplete') {
            return;
        }

        $this->subscriptionService->activateSubscription($subscription, [
            'gateway_subscription_id' => $paypalOrderId ?? $subscription->gateway_subscription_id,
            'gateway_status' => 'active',
            'paypal_order_id' => $paypalOrderId,
            'paypal_capture_id' => $captureId,
        ]);
    }

    /**
     * Handle subscription activation
     */
    protected function handleSubscriptionActivation(array $data): array
    {
        $subscriptionId = $data['subscription_id'] ?? null;

        if (! $subscriptionId) {
            return ['success' => false, 'error' => 'Missing subscription ID'];
        }

        $subscription = Subscription::find($subscriptionId);

        if ($subscription) {
            $this->subscriptionService->activateSubscription($subscription, $data['subscription_data'] ?? []);

            // Update related order if needed and exists
            if ($data['mark_order_paid'] ?? false) {
                if ($subscription->order_id) {
                    $order = Order::find($subscription->order_id);
                    if ($order && $order->status === 'pending') {
                        $this->orderService->markOrderAsPaid($order, $data['subscription_data'] ?? []);
                    }
                }
            }

            // Create transaction if provided
            if (isset($data['transaction_data'])) {
                $this->subscriptionService->createSubscriptionTransaction(
                    $subscription,
                    array_merge(['order_id' => $subscription->order_id], $data['transaction_data'])
                );
            }
        }

        return ['success' => true, 'message' => 'Subscription activated'];
    }

    /**
     * Handle subscription status update
     */
    protected function handleSubscriptionStatusUpdate(array $data): array
    {
        $subscriptionId = $data['subscription_id'] ?? null;

        if (! $subscriptionId) {
            return ['success' => false, 'error' => 'Missing subscription ID'];
        }

        $subscription = Subscription::where('gateway_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $this->subscriptionService->updateSubscriptionStatus(
                $subscription,
                $data['status'] ?? 'unknown',
                $data['gateway_data'] ?? []
            );
        }

        return ['success' => true, 'message' => 'Subscription status updated'];
    }

    /**
     * Handle subscription cancellation
     */
    protected function handleSubscriptionCancellation(array $data): array
    {
        $subscriptionId = $data['subscription_id'] ?? null;

        if (! $subscriptionId) {
            return ['success' => false, 'error' => 'Missing subscription ID'];
        }

        $subscription = Subscription::where('gateway_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $this->subscriptionService->cancelSubscription($subscription, $data['gateway_data'] ?? []);
        }

        return ['success' => true, 'message' => 'Subscription cancellation processed'];
    }

    /**
     * Handle subscription transaction
     */
    protected function handleSubscriptionTransaction(array $data): array
    {
        $subscriptionId = $data['subscription_id'] ?? null;

        if (! $subscriptionId) {
            return ['success' => false, 'error' => 'Missing subscription ID'];
        }

        $subscription = Subscription::where('gateway_subscription_id', $subscriptionId)->first();

        if (! $subscription) {
            // The activation webhook may not have linked the gateway subscription ID
            // yet; fail so the gateway retries this event later
            Log::warning('Subscription not found for transaction webhook, requesting retry', [
                'gateway_subscription_id' => $subscriptionId,
            ]);

            return ['success' => false, 'error' => 'Subscription not found yet'];
        }

        if (isset($data['transaction_data'])) {
            $this->subscriptionService->createSubscriptionTransaction($subscription, $data['transaction_data']);
        }

        return ['success' => true, 'message' => 'Transaction processed successfully'];
    }

    /**
     * Handle invoice payment failure
     */
    protected function handleInvoicePaymentFailed(array $data): array
    {
        $subscriptionId = $data['subscription_id'] ?? null;

        if (! $subscriptionId) {
            return ['success' => false, 'error' => 'Missing subscription ID'];
        }

        $subscription = Subscription::where('gateway_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            // Create failed transaction record
            if (isset($data['transaction_data'])) {
                $this->subscriptionService->createSubscriptionTransaction($subscription, $data['transaction_data']);
            }

            // Update subscription status if needed
            if ($subscription->status === 'active') {
                $this->subscriptionService->updateSubscriptionStatus($subscription, 'past_due', [
                    'gateway_status' => 'past_due',
                ]);
            }
        }

        return ['success' => true, 'message' => 'Invoice payment failure processed successfully'];
    }
}
