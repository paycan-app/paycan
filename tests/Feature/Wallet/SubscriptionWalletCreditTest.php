<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Fulfillment\FulfillmentService;
use App\Services\Payment\WebhookProcessingService;
use App\Services\Subscription\SubscriptionService;

it('automatically charges wallet credits when subscription is activated', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['type' => 'subscription']);
    $price = ProductPrice::factory()->create([
        'product_id' => $product->id,
        'billing_period' => 'monthly',
        'credit_allocations' => [
            'basic' => 2500,
            'premium' => 50,
        ],
    ]);

    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'product_price_id' => $price->id,
        'status' => 'incomplete',
        'title' => 'Starter AI Plan',
    ]);

    $subscriptionService = app(SubscriptionService::class);
    $subscriptionService->activateSubscription($subscription, [
        'gateway_subscription_id' => 'sub_test_123',
    ]);

    $basicWallet = $user->getWallet('basic');
    $premiumWallet = $user->getWallet('premium');

    expect((float) $basicWallet->balance)->toBe(2500.0);
    expect((float) $premiumWallet->balance)->toBe(50.0);
});

it('automatically adds renewal credits when recurring subscription transaction webhook arrives', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['type' => 'subscription']);
    $price = ProductPrice::factory()->create([
        'product_id' => $product->id,
        'billing_period' => 'monthly',
        'credit_allocations' => [
            'basic' => 1000,
            'premium' => 20,
        ],
        'credit_renewal_policy' => 'accumulate',
    ]);

    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'product_price_id' => $price->id,
        'gateway_subscription_id' => 'sub_recurring_999',
        'status' => 'active',
        'title' => 'AI Pro Plan',
    ]);

    // Initial balance before renewal: 500 basic, 5 premium
    $user->getWallet('basic')->update(['balance' => 500]);
    $user->getWallet('premium')->update(['balance' => 5]);

    $webhookService = app(WebhookProcessingService::class);
    $webhookService->processWebhookAction([
        'action' => 'create_subscription_transaction',
        'subscription_id' => 'sub_recurring_999',
        'transaction_data' => [
            'gateway_transaction_id' => 'txn_renewal_001',
            'amount' => 29.00,
            'status' => 'completed',
            'type' => 'subscription_payment',
        ],
    ]);

    $basicWallet = $user->getWallet('basic');
    $premiumWallet = $user->getWallet('premium');

    // 500 + 1000 = 1500, 5 + 20 = 25
    expect((float) $basicWallet->balance)->toBe(1500.0);
    expect((float) $premiumWallet->balance)->toBe(25.0);
});

it('credits wallet when one-time purchase fulfillment is processed', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['type' => 'digital']);
    $price = ProductPrice::factory()->create([
        'product_id' => $product->id,
        'billing_period' => 'once',
        'credit_allocations' => [
            'basic' => 500,
        ],
    ]);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'product_price_id' => $price->id,
        'status' => 'paid',
        'quantity' => 1,
    ]);

    $fulfillmentService = app(FulfillmentService::class);
    $fulfillmentService->processPurchaseFulfillment($order);

    $basicWallet = $user->getWallet('basic');
    expect((float) $basicWallet->balance)->toBe(500.0);
});
