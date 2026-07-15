<?php

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;

beforeEach(function () {
    $this->user = $this->authenticateUser();

    $this->digitalProduct = Product::factory()->create([
        'type' => 'digital',
        'is_active' => true,
        'title' => 'Digital Product',
    ]);

    $this->digitalPrice = ProductPrice::factory()->create([
        'product_id' => $this->digitalProduct->id,
        'amount' => 49.99,
        'currency' => 'USD',
        'billing_period' => 'once',
        'is_active' => true,
    ]);

    $this->subscriptionProduct = Product::factory()->create([
        'type' => 'subscription',
        'is_active' => true,
        'title' => 'Subscription Product',
    ]);

    $this->subscriptionPrice = ProductPrice::factory()->create([
        'product_id' => $this->subscriptionProduct->id,
        'amount' => 29.99,
        'currency' => 'USD',
        'billing_period' => 'monthly',
        'is_active' => true,
    ]);
});

it('can create checkout session for digital product', function () {
    $response = $this->postJson('/api/user/checkout', [
        'product_id' => $this->digitalProduct->id,
        'product_price_id' => $this->digitalPrice->id,
        'gateway' => 'stripe',
        'billing_email' => 'test@example.com',
        'billing_name' => 'John Doe',
        'quantity' => 1,
    ]);

    $this->assertApiResponse($response, 201, [
        'checkout' => [
            'session_id',
            'checkout_url',
            'order_id',
        ],
    ]);

    $this->assertDatabaseHasOrder([
        'id' => $response->json('checkout.order_id'),
        'product_id' => $this->digitalProduct->id,
        'status' => 'pending',
        'billing_email' => 'test@example.com',
        'billing_name' => 'John Doe',
    ]);
});

it('can create checkout session for subscription', function () {
    $response = $this->postJson('/api/user/checkout', [
        'product_id' => $this->subscriptionProduct->id,
        'product_price_id' => $this->subscriptionPrice->id,
        'gateway' => 'stripe',
        'billing_email' => 'test@example.com',
        'billing_name' => 'John Doe',
    ]);

    $this->assertApiResponse($response, 201, [
        'checkout' => [
            'session_id',
            'checkout_url',
            'order_id',
            'subscription_id',
        ],
    ]);

    expect($response->json('checkout.subscription_id'))->not->toBeNull();

    $this->assertDatabaseHas('subscriptions', [
        'id' => $response->json('checkout.subscription_id'),
        'order_id' => $response->json('checkout.order_id'),
        'status' => 'incomplete',
    ]);
});

it('can create checkout session with PayPal', function () {
    $response = $this->postJson('/api/user/checkout', [
        'product_id' => $this->digitalProduct->id,
        'product_price_id' => $this->digitalPrice->id,
        'gateway' => 'paypal',
        'billing_email' => 'test@example.com',
        'billing_name' => 'John Doe',
    ]);

    $this->assertApiResponse($response, 201, [
        'checkout' => [
            'session_id',
            'checkout_url',
            'order_id',
        ],
    ]);

    $this->assertDatabaseHasOrder([
        'id' => $response->json('checkout.order_id'),
        'gateway' => 'paypal',
    ]);
});

it('validates checkout data', function () {
    $response = $this->postJson('/api/user/checkout', [
        'product_id' => 999, // Non-existent
        'product_price_id' => 999, // Non-existent
        'gateway' => 'invalid',
        'billing_email' => 'invalid-email',
        'quantity' => -1,
    ]);

    $this->assertApiResponse($response, 422, [
        'errors' => [
            'product_id',
            'product_price_id',
            'gateway',
            'billing_email',
            'quantity',
        ],
    ]);
});

it('cannot checkout inactive products', function () {
    $inactiveProduct = Product::factory()->create([
        'is_active' => false,
    ]);

    $inactivePrice = ProductPrice::factory()->create([
        'product_id' => $inactiveProduct->id,
        'is_active' => false,
    ]);

    $response = $this->postJson('/api/user/checkout', [
        'product_id' => $inactiveProduct->id,
        'product_price_id' => $inactivePrice->id,
        'gateway' => 'stripe',
        'billing_email' => 'test@example.com',
        'billing_name' => 'John Doe',
    ]);

    $this->assertApiResponse($response, 422);
});

it('can create customer portal session', function () {
    $response = $this->postJson('/api/user/checkout/portal', [
        'gateway' => 'stripe',
        'return_url' => 'https://example.com/return',
    ]);

    $this->assertApiResponse($response, 200, [
        'portal' => [
            'url',
        ],
    ]);

    expect($response->json('portal.url'))->toContain('billing.stripe.com');
});

it('calculates correct totals with quantity', function () {
    $response = $this->postJson('/api/user/checkout', [
        'product_id' => $this->digitalProduct->id,
        'product_price_id' => $this->digitalPrice->id,
        'gateway' => 'stripe',
        'billing_email' => 'test@example.com',
        'billing_name' => 'John Doe',
        'quantity' => 3,
    ]);

    $this->assertApiResponse($response, 201);

    $expectedTotal = round($this->digitalPrice->amount * 3, 2);

    $this->assertDatabaseHas('orders', [
        'id' => $response->json('checkout.order_id'),
        'quantity' => 3,
        'total' => $expectedTotal,
    ]);
});

it('handles guest checkout by attaching the order to the billing email account', function () {
    // No authentication - simulate a guest hitting the public endpoint
    auth()->forgetGuards();
    $this->app['auth']->guard('web')->logout();

    $response = $this->postJson('/api/user/checkout', [
        'product_id' => $this->digitalProduct->id,
        'product_price_id' => $this->digitalPrice->id,
        'gateway' => 'stripe',
        'billing_email' => 'guest@example.com',
        'billing_name' => 'Guest User',
    ]);

    $this->assertApiResponse($response, 201);

    $guestUser = User::where('email', 'guest@example.com')->first();
    expect($guestUser)->not->toBeNull();

    $this->assertDatabaseHas('orders', [
        'user_id' => $guestUser->id,
        'billing_email' => 'guest@example.com',
        'billing_name' => 'Guest User',
    ]);
});

it('allows repeat guest checkout with an existing email', function () {
    auth()->forgetGuards();
    $this->app['auth']->guard('web')->logout();

    $existingUser = User::factory()->create(['email' => 'repeat@example.com']);

    $response = $this->postJson('/api/user/checkout', [
        'product_id' => $this->digitalProduct->id,
        'product_price_id' => $this->digitalPrice->id,
        'gateway' => 'stripe',
        'billing_email' => 'repeat@example.com',
        'billing_name' => 'Repeat Buyer',
    ]);

    $this->assertApiResponse($response, 201);

    // The order is attached to the existing account, not a duplicate one
    expect(User::where('email', 'repeat@example.com')->count())->toBe(1);

    $this->assertDatabaseHas('orders', [
        'user_id' => $existingUser->id,
        'billing_email' => 'repeat@example.com',
    ]);
});
