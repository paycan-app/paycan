<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Mark an order as paid and create the payment transaction
     */
    public function markOrderAsPaid(Order $order, array $gatewayData = []): bool
    {
        // Prevent duplicate processing
        if (in_array($order->status, ['paid', 'completed'])) {
            Log::info("Order {$order->order_number} already processed", [
                'order_id' => $order->id,
                'status' => $order->status,
            ]);

            return true;
        }

        // Validate that order is in a valid state to be marked as paid
        if (! in_array($order->status, ['pending', 'processing'])) {
            Log::warning('Cannot mark order as paid - invalid status', [
                'order_id' => $order->id,
                'current_status' => $order->status,
            ]);

            return false;
        }

        return DB::transaction(function () use ($order, $gatewayData) {
            // Update order status
            $order->update([
                'status' => 'paid',
                'meta' => array_merge($order->meta ?? [], [
                    'payment_completed_at' => now()->toIso8601String(),
                    'gateway_payment_data' => $gatewayData,
                ]),
            ]);

            Log::info("Order {$order->order_number} marked as paid", [
                'order_id' => $order->id,
                'gateway' => $order->gateway,
            ]);

            return true;
        });
    }

    /**
     * Mark an order as failed
     */
    public function markOrderAsFailed(Order $order, string $reason, array $gatewayData = []): bool
    {
        // Idempotent - if already failed, just return true
        if ($order->status === 'failed') {
            return true;
        }

        // Only allow failing pending/processing orders
        if (! in_array($order->status, ['pending', 'processing'])) {
            Log::warning('Cannot mark order as failed - invalid status', [
                'order_id' => $order->id,
                'current_status' => $order->status,
            ]);

            return false;
        }

        $order->update([
            'status' => 'failed',
            'meta' => array_merge($order->meta ?? [], [
                'payment_failed_at' => now()->toIso8601String(),
                'failure_reason' => $reason,
                'gateway_failure_data' => $gatewayData,
            ]),
        ]);

        Log::info("Order {$order->order_number} marked as failed", [
            'order_id' => $order->id,
            'reason' => $reason,
        ]);

        return true;
    }

    /**
     * Create a transaction record for an order
     */
    public function createTransactionForOrder(Order $order, array $data): ?Transaction
    {
        // Check if transaction already exists to prevent duplicates
        $existingTransaction = Transaction::where('order_id', $order->id)
            ->where('gateway_transaction_id', $data['gateway_transaction_id'] ?? null)
            ->where('type', $data['type'] ?? 'payment')
            ->first();

        if ($existingTransaction) {
            Log::info('Transaction already exists for order', [
                'order_id' => $order->id,
                'transaction_id' => $existingTransaction->id,
            ]);

            return $existingTransaction;
        }

        try {
            $transaction = Transaction::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'subscription_id' => $data['subscription_id'] ?? null,
                'type' => $data['type'] ?? 'payment',
                'status' => $data['status'] ?? 'completed',
                'gateway' => $order->gateway,
                'amount' => $data['amount'] ?? $order->total,
                'currency' => $data['currency'] ?? $order->currency,
                'gateway_transaction_id' => $data['gateway_transaction_id'] ?? null,
                'gateway_data' => $data['gateway_data'] ?? [],
                'meta' => $data['meta'] ?? [],
            ]);

            Log::info("Transaction created for order {$order->order_number}", [
                'transaction_id' => $transaction->id,
                'type' => $transaction->type,
                'amount' => $transaction->amount,
            ]);

            return $transaction;
        } catch (\Exception $e) {
            Log::error("Failed to create transaction for order {$order->id}", [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return null;
        }
    }
}
