<?php

namespace App\Services\Subscription;

use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * Activate a subscription (transition from incomplete to active)
     */
    public function activateSubscription(Subscription $subscription, array $gatewayData = []): bool
    {
        // Prevent duplicate activation
        if (in_array($subscription->status, ['active', 'trialing'])) {
            Log::info('Subscription already active', [
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
            ]);

            return true;
        }

        // Only activate incomplete subscriptions
        if ($subscription->status !== 'incomplete') {
            Log::warning('Cannot activate subscription - invalid status', [
                'subscription_id' => $subscription->id,
                'current_status' => $subscription->status,
            ]);

            return false;
        }

        return DB::transaction(function () use ($subscription, $gatewayData) {
            // Determine if subscription should be trialing or active
            $status = 'active';
            if ($subscription->trial_ends_at && $subscription->trial_ends_at->isFuture()) {
                $status = 'trialing';
            }

            $subscription->update([
                'status' => $status,
                'gateway_subscription_id' => $gatewayData['gateway_subscription_id'] ?? $subscription->gateway_subscription_id,
                'gateway_status' => $gatewayData['gateway_status'] ?? $status,
                'gateway_data' => array_merge($subscription->gateway_data ?? [], [
                    'activated_at' => now()->toIso8601String(),
                    'activation_data' => $gatewayData,
                ]),
            ]);

            Log::info('Subscription activated', [
                'subscription_id' => $subscription->id,
                'status' => $status,
                'gateway' => $subscription->gateway,
            ]);

            return true;
        });
    }

    /**
     * Update subscription status based on gateway events
     */
    public function updateSubscriptionStatus(
        Subscription $subscription,
        string $status,
        array $gatewayData = []
    ): bool {
        $validStatuses = ['active', 'trialing', 'past_due', 'canceled', 'paused', 'expired'];

        if (! in_array($status, $validStatuses)) {
            Log::warning('Invalid subscription status', [
                'subscription_id' => $subscription->id,
                'attempted_status' => $status,
            ]);

            return false;
        }

        // Idempotent - if already in this status, return true
        if ($subscription->status === $status) {
            return true;
        }

        $updateData = [
            'status' => $status,
            'gateway_status' => $gatewayData['gateway_status'] ?? $status,
            'gateway_data' => array_merge($subscription->gateway_data ?? [], [
                'status_updated_at' => now()->toIso8601String(),
                'status_update_data' => $gatewayData,
            ]),
        ];

        // Set canceled_at timestamp if status is canceled
        if ($status === 'canceled' && ! $subscription->canceled_at) {
            $updateData['canceled_at'] = now();
        }

        // Set ends_at based on gateway data or current period end
        if ($status === 'canceled' && isset($gatewayData['current_period_end'])) {
            $updateData['ends_at'] = $gatewayData['current_period_end'];
        }

        // Keep the local billing date in sync with the gateway's period end
        if (in_array($status, ['active', 'trialing']) && ! empty($gatewayData['current_period_end'])) {
            $updateData['next_billing_date'] = $gatewayData['current_period_end'];
        }

        $subscription->update($updateData);

        Log::info('Subscription status updated', [
            'subscription_id' => $subscription->id,
            'old_status' => $subscription->getOriginal('status'),
            'new_status' => $status,
        ]);

        return true;
    }

    /**
     * Create a transaction record for a subscription payment
     */
    public function createSubscriptionTransaction(Subscription $subscription, array $data): ?Transaction
    {
        // Check if transaction already exists to prevent duplicates
        $existingTransaction = Transaction::where('subscription_id', $subscription->id)
            ->where('gateway_transaction_id', $data['gateway_transaction_id'] ?? null)
            ->where('type', $data['type'] ?? 'subscription_payment')
            ->first();

        if ($existingTransaction) {
            Log::info('Transaction already exists for subscription', [
                'subscription_id' => $subscription->id,
                'transaction_id' => $existingTransaction->id,
            ]);

            return $existingTransaction;
        }

        try {
            $transaction = Transaction::create([
                'user_id' => $subscription->user_id,
                'order_id' => $data['order_id'] ?? $subscription->order_id,
                'subscription_id' => $subscription->id,
                'type' => $data['type'] ?? 'subscription_payment',
                'status' => $data['status'] ?? 'completed',
                'gateway' => $subscription->gateway,
                'amount' => $data['amount'] ?? null,
                'currency' => $data['currency'] ?? null,
                'gateway_transaction_id' => $data['gateway_transaction_id'] ?? null,
                'gateway_data' => $data['gateway_data'] ?? [],
                'meta' => $data['meta'] ?? [],
            ]);

            Log::info('Transaction created for subscription', [
                'subscription_id' => $subscription->id,
                'transaction_id' => $transaction->id,
                'type' => $transaction->type,
                'amount' => $transaction->amount,
            ]);

            return $transaction;
        } catch (\Exception $e) {
            Log::error("Failed to create transaction for subscription {$subscription->id}", [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return null;
        }
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(Subscription $subscription, array $gatewayData = []): bool
    {
        // Idempotent - if already canceled, return true
        if ($subscription->status === 'canceled') {
            return true;
        }

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'ends_at' => $gatewayData['current_period_end'] ?? now(),
            'gateway_status' => $gatewayData['gateway_status'] ?? 'canceled',
            'gateway_data' => array_merge($subscription->gateway_data ?? [], [
                'canceled_at' => now()->toIso8601String(),
                'cancellation_data' => $gatewayData,
            ]),
        ]);

        Log::info('Subscription canceled', [
            'subscription_id' => $subscription->id,
            'gateway' => $subscription->gateway,
        ]);

        return true;
    }
}
