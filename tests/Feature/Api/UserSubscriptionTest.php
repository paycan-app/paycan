<?php

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Subscription;
use App\Models\User;

beforeEach(function () {
    $this->user = $this->authenticateUser();

    // Create test products and prices for subscriptions
    $this->subscriptionProduct = Product::factory()->create([
        'type' => 'subscription',
        'is_active' => true,
        'title' => 'Premium Subscription',
    ]);

    $this->monthlyPrice = ProductPrice::factory()->create([
        'product_id' => $this->subscriptionProduct->id,
        'amount' => 29.99,
        'currency' => 'USD',
        'billing_period' => 'monthly',
        'is_active' => true,
    ]);

    $this->yearlyPrice = ProductPrice::factory()->create([
        'product_id' => $this->subscriptionProduct->id,
        'amount' => 299.99,
        'currency' => 'USD',
        'billing_period' => 'yearly',
        'is_active' => true,
    ]);
});

it('can create a new subscription', function () {
    $response = $this->postJson('/api/user/subscriptions', [
        'product_id' => $this->subscriptionProduct->id,
        'product_price_id' => $this->monthlyPrice->id,
        'gateway' => 'stripe',
    ]);

    $this->assertApiResponse($response, 201, [
        'subscription' => [
            'id',
            'title',
            'status',
            'gateway',
        ],
        'checkout_url',
        'message',
    ]);

    // Subscription starts incomplete; the webhook activates it after payment
    $this->assertDatabaseHasSubscription([
        'user_id' => $this->user->id,
        'product_id' => $this->subscriptionProduct->id,
        'product_price_id' => $this->monthlyPrice->id,
        'status' => 'incomplete',
        'gateway' => 'stripe',
    ]);
});

it('can list user subscriptions', function () {
    // Create test subscriptions
    Subscription::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'product_id' => $this->subscriptionProduct->id,
        'product_price_id' => $this->monthlyPrice->id,
    ]);

    // Create subscription for another user (should not appear)
    $otherUser = User::factory()->create();
    Subscription::factory()->create([
        'user_id' => $otherUser->id,
        'product_id' => $this->subscriptionProduct->id,
        'product_price_id' => $this->monthlyPrice->id,
    ]);

    $response = $this->getJson('/api/user/subscriptions');

    $this->assertApiResponse($response, 200, [
        'data' => [
            '*' => [
                'id',
                'title',
                'status',
                'gateway',
            ],
        ],
        'current_page',
        'per_page',
        'total',
    ]);

    expect($response->json('data'))->toHaveCount(3);
});

it('can show a specific subscription', function () {
    $subscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->subscriptionProduct->id,
        'product_price_id' => $this->monthlyPrice->id,
    ]);

    $response = $this->getJson("/api/user/subscriptions/{$subscription->id}");

    $this->assertApiResponse($response, 200, [
        'data' => [
            'id',
            'title',
            'status',
            'gateway',
            'created_at',
            'updated_at',
        ],
    ]);

    expect($response->json('data.id'))->toBe($subscription->id);
});

it('can cancel a subscription', function () {
    $subscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->subscriptionProduct->id,
        'product_price_id' => $this->monthlyPrice->id,
        'status' => 'active',
    ]);

    $response = $this->postJson("/api/user/subscriptions/{$subscription->id}/cancel");

    $this->assertApiResponse($response, 200, [
        'subscription' => [
            'id',
            'status',
            'canceled_at',
        ],
        'message',
    ]);

    $this->assertDatabaseHas('subscriptions', [
        'id' => $subscription->id,
        'status' => 'canceled',
    ]);

    expect($response->json('subscription.status'))->toBe('canceled');
    expect($response->json('subscription.canceled_at'))->not()->toBeNull();
});

it('can resume a canceled subscription', function () {
    $subscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->subscriptionProduct->id,
        'product_price_id' => $this->monthlyPrice->id,
        'status' => 'canceled',
        'canceled_at' => now(),
    ]);

    $response = $this->postJson("/api/user/subscriptions/{$subscription->id}/resume");

    $this->assertApiResponse($response, 200, [
        'subscription' => [
            'id',
            'status',
            'canceled_at',
        ],
        'message',
    ]);

    $this->assertDatabaseHas('subscriptions', [
        'id' => $subscription->id,
        'status' => 'active',
        'canceled_at' => null,
    ]);

    expect($response->json('subscription.status'))->toBe('active');
    expect($response->json('subscription.canceled_at'))->toBeNull();
});

it('can change subscription plan', function () {
    $subscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->subscriptionProduct->id,
        'product_price_id' => $this->monthlyPrice->id,
        'status' => 'active',
        'gateway' => 'stripe',
        'gateway_subscription_id' => 'sub_test_change',
    ]);

    $response = $this->postJson("/api/user/subscriptions/{$subscription->id}/change", [
        'product_price_id' => $this->yearlyPrice->id,
    ]);

    $this->assertApiResponse($response, 200, [
        'subscription' => [
            'id',
            'product_price_id',
        ],
        'message',
    ]);

    $this->assertDatabaseHas('subscriptions', [
        'id' => $subscription->id,
        'product_price_id' => $this->yearlyPrice->id,
    ]);

    expect($response->json('subscription.product_price_id'))->toBe($this->yearlyPrice->id);
});

it('cannot access other users subscriptions', function () {
    $otherUser = User::factory()->create();
    $subscription = Subscription::factory()->create([
        'user_id' => $otherUser->id,
        'product_id' => $this->subscriptionProduct->id,
        'product_price_id' => $this->monthlyPrice->id,
    ]);

    $response = $this->getJson("/api/user/subscriptions/{$subscription->id}");
    $this->assertApiResponse($response, 404);

    $response = $this->postJson("/api/user/subscriptions/{$subscription->id}/cancel");
    $this->assertApiResponse($response, 404);
});

it('validates subscription creation data', function () {
    $response = $this->postJson('/api/user/subscriptions', [
        'product_id' => 999, // Non-existent product
        'product_price_id' => 999, // Non-existent price
        'gateway' => 'invalid_gateway',
    ]);

    $this->assertApiResponse($response, 422, [
        'errors' => [
            'product_id',
            'product_price_id',
            'gateway',
        ],
    ]);
});

it('cannot create subscription for inactive product', function () {
    $inactiveProduct = Product::factory()->create([
        'type' => 'subscription',
        'is_active' => false,
    ]);

    $inactivePrice = ProductPrice::factory()->create([
        'product_id' => $inactiveProduct->id,
        'is_active' => false,
    ]);

    $response = $this->postJson('/api/user/subscriptions', [
        'product_id' => $inactiveProduct->id,
        'product_price_id' => $inactivePrice->id,
        'gateway' => 'stripe',
    ]);

    $this->assertApiResponse($response, 422);
});

it('filters subscriptions by status', function () {
    Subscription::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'active',
    ]);

    Subscription::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'canceled',
    ]);

    $response = $this->getJson('/api/user/subscriptions?filter[status]=active');

    $this->assertApiResponse($response, 200);
    $subscriptions = $response->json('data');

    expect($subscriptions)->toHaveCount(1);
    expect($subscriptions[0]['status'])->toBe('active');
});
