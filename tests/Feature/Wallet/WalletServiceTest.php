<?php

use App\Exceptions\InsufficientCreditsException;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Wallet\WalletService;

beforeEach(function () {
    $this->walletService = app(WalletService::class);
});

it('creates default wallets for a user', function () {
    $user = User::factory()->create();

    $wallets = $this->walletService->getWalletsForUser($user);

    expect($wallets)->toHaveCount(2);
    expect($wallets->pluck('type')->toArray())->toContain('basic', 'premium');
    expect((float) $wallets->firstWhere('type', 'basic')->balance)->toBe(0.0);
});

it('can add credits to a wallet', function () {
    $user = User::factory()->create();

    $tx = $this->walletService->addCredits(
        target: $user,
        amount: 150.50,
        type: 'basic',
        description: 'Test top-up',
        referenceId: 'ref_123'
    );

    expect($tx)->not->toBeNull();
    expect((float) $tx->amount)->toBe(150.50);
    expect((float) $tx->balance_after)->toBe(150.50);
    expect($tx->type)->toBe('credit');
    expect($tx->reference_id)->toBe('ref_123');

    $wallet = $user->getWallet('basic');
    expect((float) $wallet->balance)->toBe(150.50);
});

it('can deduct credits atomically from a wallet', function () {
    $user = User::factory()->create();
    $this->walletService->addCredits($user, 100.0, 'premium');

    $tx = $this->walletService->deductCredits(
        target: $user,
        amount: 35.5,
        type: 'premium',
        description: 'AI model run',
        referenceId: 'run_999',
        meta: ['tokens' => 1200]
    );

    expect($tx)->not->toBeNull();
    expect((float) $tx->amount)->toBe(35.5);
    expect((float) $tx->balance_after)->toBe(64.5);
    expect($tx->type)->toBe('debit');
    expect($tx->action)->toBe('usage');
    expect($tx->meta)->toBe(['tokens' => 1200]);

    $wallet = $user->getWallet('premium');
    expect((float) $wallet->balance)->toBe(64.5);
});

it('throws InsufficientCreditsException when balance is too low', function () {
    $user = User::factory()->create();
    $this->walletService->addCredits($user, 10.0, 'basic');

    $this->walletService->deductCredits($user, 50.0, 'basic');
})->throws(InsufficientCreditsException::class);

it('applies subscription credit allocations upon activation', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['type' => 'subscription']);
    $price = ProductPrice::factory()->create([
        'product_id' => $product->id,
        'billing_period' => 'monthly',
        'credit_allocations' => [
            'basic' => 5000,
            'premium' => 100,
        ],
        'credit_renewal_policy' => 'accumulate',
    ]);

    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'product_price_id' => $price->id,
        'status' => 'active',
        'title' => 'Pro AI Plan',
    ]);

    $this->walletService->applySubscriptionAllocation($subscription, false);

    $basicWallet = $user->getWallet('basic');
    $premiumWallet = $user->getWallet('premium');

    expect((float) $basicWallet->balance)->toBe(5000.0);
    expect((float) $premiumWallet->balance)->toBe(100.0);
});

it('resets credits upon renewal when renewal policy is reset', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['type' => 'subscription']);
    $price = ProductPrice::factory()->create([
        'product_id' => $product->id,
        'billing_period' => 'monthly',
        'credit_allocations' => [
            'premium' => 200,
        ],
        'credit_renewal_policy' => 'reset',
    ]);

    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'product_price_id' => $price->id,
        'status' => 'active',
        'title' => 'Pro AI Plan',
    ]);

    // User has 50 leftover credits before renewal
    $this->walletService->addCredits($user, 50, 'premium');

    // Subscription renews with policy = reset
    $this->walletService->applySubscriptionAllocation($subscription, true);

    $premiumWallet = $user->getWallet('premium');
    expect((float) $premiumWallet->balance)->toBe(200.0);
});

it('applies order credit allocations for one-time purchases', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['type' => 'service']);
    $price = ProductPrice::factory()->create([
        'product_id' => $product->id,
        'billing_period' => 'once',
        'credit_allocations' => [
            'basic' => 1000,
            'premium' => 25,
        ],
    ]);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'product_price_id' => $price->id,
        'status' => 'paid',
        'quantity' => 2,
    ]);

    $this->walletService->applyOrderAllocation($order);

    $basicWallet = $user->getWallet('basic');
    $premiumWallet = $user->getWallet('premium');

    // 1000 * 2 = 2000 basic, 25 * 2 = 50 premium
    expect((float) $basicWallet->balance)->toBe(2000.0);
    expect((float) $premiumWallet->balance)->toBe(50.0);
});
