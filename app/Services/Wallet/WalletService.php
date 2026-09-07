<?php

namespace App\Services\Wallet;

use App\Exceptions\InsufficientCreditsException;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletService
{
    /**
     * Get or create a wallet of a specific type for a user
     */
    public function getOrCreateWallet(User|string $user, string $type = 'basic'): Wallet
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Wallet::firstOrCreate(
            [
                'user_id' => $userId,
                'type' => $type,
            ],
            [
                'balance' => 0.0000,
                'currency' => 'credits',
                'is_active' => true,
            ]
        );
    }

    /**
     * Get all wallets for a user, ensuring default basic and premium wallets exist
     */
    public function getWalletsForUser(User|string $user): Collection
    {
        $userId = $user instanceof User ? $user->id : $user;

        // Ensure basic and premium wallets exist
        $this->getOrCreateWallet($userId, 'basic');
        $this->getOrCreateWallet($userId, 'premium');

        return Wallet::where('user_id', $userId)->get();
    }

    /**
     * Check if user/wallet has sufficient credits
     */
    public function hasSufficientCredits(User|Wallet $target, float $amount, string $type = 'basic'): bool
    {
        $wallet = $target instanceof Wallet ? $target : $this->getOrCreateWallet($target, $type);

        return (float) $wallet->balance >= $amount;
    }

    /**
     * Deduct credits from a wallet with row locking for atomic AI usage
     *
     * @throws InsufficientCreditsException
     */
    public function deductCredits(
        User|Wallet $target,
        float $amount,
        string $type = 'basic',
        ?string $description = null,
        ?string $referenceId = null,
        array $meta = []
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Deduction amount must be greater than zero.');
        }

        $walletToFind = $target instanceof Wallet ? $target : $this->getOrCreateWallet($target, $type);

        return DB::transaction(function () use ($walletToFind, $amount, $description, $referenceId, $meta) {
            /** @var Wallet|null $wallet */
            $wallet = Wallet::where('id', $walletToFind->id)->lockForUpdate()->first();

            if (! $wallet) {
                throw new \RuntimeException('Wallet not found for deduction.');
            }

            $currentBalance = (float) $wallet->balance;

            if ($currentBalance < $amount) {
                Log::warning('Insufficient credits for deduction', [
                    'wallet_id' => $wallet->id,
                    'user_id' => $wallet->user_id,
                    'type' => $wallet->type,
                    'current_balance' => $currentBalance,
                    'required' => $amount,
                ]);

                throw new InsufficientCreditsException(
                    $wallet->type,
                    $currentBalance,
                    $amount,
                    "Insufficient credits in {$wallet->type} wallet. Current balance: {$currentBalance}, required: {$amount}."
                );
            }

            $newBalance = round($currentBalance - $amount, 4);
            $wallet->update(['balance' => $newBalance]);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'type' => 'debit',
                'action' => 'usage',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference_id' => $referenceId,
                'description' => $description ?? "Usage deduction of {$amount} credits",
                'meta' => $meta,
            ]);

            Log::info('Credits deducted successfully', [
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'type' => $wallet->type,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference_id' => $referenceId,
            ]);

            return $transaction;
        });
    }

    /**
     * Add credits to a wallet
     */
    public function addCredits(
        User|Wallet $target,
        float $amount,
        string $type = 'basic',
        string $action = 'manual_adjustment',
        ?string $description = null,
        ?string $referenceId = null,
        array $meta = []
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit amount must be greater than zero.');
        }

        $walletToFind = $target instanceof Wallet ? $target : $this->getOrCreateWallet($target, $type);

        return DB::transaction(function () use ($walletToFind, $amount, $action, $description, $referenceId, $meta) {
            /** @var Wallet|null $wallet */
            $wallet = Wallet::where('id', $walletToFind->id)->lockForUpdate()->first();

            if (! $wallet) {
                throw new \RuntimeException('Wallet not found for crediting.');
            }

            $newBalance = round((float) $wallet->balance + $amount, 4);
            $wallet->update(['balance' => $newBalance]);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'type' => 'credit',
                'action' => $action,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference_id' => $referenceId,
                'description' => $description ?? "Credited {$amount} credits",
                'meta' => $meta,
            ]);

            Log::info('Credits added successfully', [
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'type' => $wallet->type,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'action' => $action,
            ]);

            return $transaction;
        });
    }

    /**
     * Reset wallet balance to a specific amount (e.g. for subscription reset policy)
     */
    public function resetCredits(
        User|Wallet $target,
        float $newBalance,
        string $type = 'basic',
        string $action = 'subscription_reset',
        ?string $description = null,
        ?string $referenceId = null,
        array $meta = []
    ): WalletTransaction {
        $walletToFind = $target instanceof Wallet ? $target : $this->getOrCreateWallet($target, $type);

        return DB::transaction(function () use ($walletToFind, $newBalance, $action, $description, $referenceId, $meta) {
            /** @var Wallet|null $wallet */
            $wallet = Wallet::where('id', $walletToFind->id)->lockForUpdate()->first();

            if (! $wallet) {
                throw new \RuntimeException('Wallet not found for reset.');
            }

            $previousBalance = (float) $wallet->balance;
            $diff = round($newBalance - $previousBalance, 4);
            $wallet->update(['balance' => $newBalance]);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'type' => $diff >= 0 ? 'credit' : 'debit',
                'action' => $action,
                'amount' => abs($diff),
                'balance_after' => $newBalance,
                'reference_id' => $referenceId,
                'description' => $description ?? "Quota reset to {$newBalance} credits",
                'meta' => array_merge($meta, [
                    'previous_balance' => $previousBalance,
                    'target_balance' => $newBalance,
                ]),
            ]);

            Log::info('Credits reset successfully', [
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'type' => $wallet->type,
                'previous_balance' => $previousBalance,
                'new_balance' => $newBalance,
            ]);

            return $transaction;
        });
    }

    /**
     * Apply credit allocations from a subscription (creation or renewal)
     */
    public function applySubscriptionAllocation(Subscription $subscription, bool $isRenewal = false): void
    {
        $price = $subscription->productPrice;

        if (! $price || ! $price->hasCreditAllocations()) {
            return;
        }

        $user = $subscription->user;
        if (! $user) {
            return;
        }

        $allocations = $price->credit_allocations ?? [];
        $policy = $price->credit_renewal_policy ?? 'accumulate';

        foreach ($allocations as $walletType => $amount) {
            $amount = (float) $amount;
            if ($amount <= 0) {
                continue;
            }

            if ($isRenewal && $policy === 'reset') {
                $this->resetCredits(
                    $user,
                    $amount,
                    $walletType,
                    'subscription_renewal',
                    "Subscription quota reset ({$subscription->title})",
                    (string) $subscription->id,
                    ['subscription_id' => $subscription->id, 'renewal' => true]
                );
            } else {
                $action = $isRenewal ? 'subscription_renewal' : 'subscription_grant';
                $description = $isRenewal
                    ? "Subscription renewal allocation - {$subscription->title}"
                    : "Subscription grant - {$subscription->title}";

                $this->addCredits(
                    $user,
                    $amount,
                    $walletType,
                    $action,
                    $description,
                    (string) $subscription->id,
                    ['subscription_id' => $subscription->id, 'is_renewal' => $isRenewal]
                );
            }
        }
    }

    /**
     * Apply credit allocations from a one-time product order
     */
    public function applyOrderAllocation(Order $order): void
    {
        $price = $order->productPrice;

        if (! $price || ! $price->hasCreditAllocations()) {
            return;
        }

        // If this order is associated with a subscription, the subscription activation handles it
        if ($order->subscription()->exists()) {
            return;
        }

        $user = $order->user;
        if (! $user) {
            return;
        }

        $allocations = $price->credit_allocations ?? [];

        foreach ($allocations as $walletType => $amount) {
            $amount = (float) $amount;
            if ($amount <= 0) {
                continue;
            }

            $totalAmount = $amount * ($order->quantity ?: 1);

            $this->addCredits(
                $user,
                $totalAmount,
                $walletType,
                'order_purchase',
                "One-time purchase credit top-up - Order #{$order->order_number}",
                (string) $order->id,
                ['order_id' => $order->id, 'order_number' => $order->order_number]
            );
        }
    }
}
