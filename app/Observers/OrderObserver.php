<?php

namespace App\Observers;

use App\Models\Order;
use App\Notifications\AdminPaymentFailedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     * Send notifications when order status changes
     */
    public function updated(Order $order): void
    {
        if (! $order->wasChanged('status')) {
            return;
        }

        // Handle successful payment statuses
        if (in_array($order->status, ['paid', 'completed'])) {
            $this->handleSuccessfulPayment($order);
        }

        // Handle failed payment status
        if ($order->status === 'failed') {
            $this->handleFailedPayment($order);
        }
    }

    /**
     * Handle successful payment notifications
     */
    protected function handleSuccessfulPayment(Order $order): void
    {
        // Transaction creation is now handled by OrderService/WebhookProcessingService
        // This observer focuses on side effects: fulfillment and notifications

        // Trigger fulfillment for the purchase
        try {
            app(\App\Services\Fulfillment\FulfillmentService::class)->processPurchaseFulfillment($order);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to process fulfillment', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Send notification to user
        try {
            $order->user->notify(new \App\Notifications\PaymentSuccessNotification($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send payment success notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Send notification to admin if enabled
        try {
            $adminEmail = settings('notifications.admin_email');
            if ($adminEmail && settings('notifications.notify_admin_new_order', false)) {
                \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)
                    ->notify(new \App\Notifications\AdminNewOrderNotification($order));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send admin new order notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle failed payment notifications
     */
    protected function handleFailedPayment(Order $order): void
    {
        // Send notification to admin if enabled
        try {
            $adminEmail = settings('notifications.admin_email');
            if ($adminEmail && settings('notifications.notify_admin_failed_payment', true)) {
                Notification::route('mail', $adminEmail)
                    ->notify(new AdminPaymentFailedNotification($order));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send admin payment failed notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
