<?php

use App\Models\Fulfillment;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;

beforeEach(function () {
    $this->user = $this->authenticateUser();

    // Create test products
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
});

it('can list user orders', function () {
    // Create test orders for the user
    Order::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'product_id' => $this->digitalProduct->id,
        'product_price_id' => $this->digitalPrice->id,
    ]);

    // Create order for another user (should not appear)
    $otherUser = User::factory()->create();
    Order::factory()->create([
        'user_id' => $otherUser->id,
        'product_id' => $this->digitalProduct->id,
        'product_price_id' => $this->digitalPrice->id,
    ]);

    $response = $this->getJson('/api/user/orders');

    $this->assertApiResponse($response, 200, [
        'data' => [
            '*' => [
                'id',
                'order_number',
                'status',
                'total',
                'currency',
                'created_at',
            ],
        ],
        'current_page',
        'per_page',
        'total',
    ]);

    expect($response->json('data'))->toHaveCount(3);
});

it('can show a specific order', function () {
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->digitalProduct->id,
        'product_price_id' => $this->digitalPrice->id,
        'status' => 'completed',
    ]);

    $response = $this->getJson("/api/user/orders/{$order->id}");

    $this->assertApiResponse($response, 200, [
        'data' => [
            'id',
            'order_number',
            'status',
            'total',
            'currency',
            'billing_email',
            'billing_name',
            'created_at',
            'updated_at',
        ],
    ]);

    expect($response->json('data.id'))->toBe($order->id);
});

it('can get order downloads', function () {
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->digitalProduct->id,
        'product_price_id' => $this->digitalPrice->id,
        'status' => 'completed',
    ]);

    // Create completed fulfillments with download links
    Fulfillment::factory()->count(2)->create([
        'order_id' => $order->id,
        'type' => 'download',
        'status' => 'completed',
        'meta' => [
            'download_url' => 'https://example.com/download/file.zip',
            'filename' => 'product-file.zip',
            'expires_at' => now()->addDays(30)->toISOString(),
        ],
    ]);

    $response = $this->getJson("/api/user/orders/{$order->id}/downloads");

    $this->assertApiResponse($response, 200, [
        'order_id',
        'downloads' => [
            '*' => [
                'product_id',
                'product_title',
                'download_url',
                'expires_at',
            ],
        ],
    ]);

    expect($response->json('downloads'))->toHaveCount(2);
    expect($response->json('downloads.0.download_url'))->toBe('https://example.com/download/file.zip');
});

it('can get order licenses', function () {
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->digitalProduct->id,
        'product_price_id' => $this->digitalPrice->id,
        'status' => 'completed',
    ]);

    // Create completed fulfillments with license keys
    Fulfillment::factory()->count(2)->create([
        'order_id' => $order->id,
        'type' => 'license',
        'status' => 'completed',
        'meta' => [
            'license_key' => 'ABCD-EFGH-IJKL-MNOP',
            'activation_limit' => 5,
            'activations_used' => 0,
        ],
    ]);

    $response = $this->getJson("/api/user/orders/{$order->id}/licenses");

    $this->assertApiResponse($response, 200, [
        'order_id',
        'licenses' => [
            '*' => [
                'product_id',
                'product_title',
                'license_key',
            ],
        ],
    ]);

    expect($response->json('licenses'))->toHaveCount(2);
    expect($response->json('licenses.0.license_key'))->toBe('ABCD-EFGH-IJKL-MNOP');
});

it('cannot access other users orders', function () {
    $otherUser = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $otherUser->id,
        'product_id' => $this->digitalProduct->id,
        'product_price_id' => $this->digitalPrice->id,
    ]);

    $response = $this->getJson("/api/user/orders/{$order->id}");
    $this->assertApiResponse($response, 404);

    $response = $this->getJson("/api/user/orders/{$order->id}/downloads");
    $this->assertApiResponse($response, 404);

    $response = $this->getJson("/api/user/orders/{$order->id}/licenses");
    $this->assertApiResponse($response, 404);
});

it('filters orders by status', function () {
    Order::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'completed',
    ]);

    Order::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'pending',
    ]);

    $response = $this->getJson('/api/user/orders?filter[status]=completed');

    $this->assertApiResponse($response, 200);
    $orders = $response->json('data');

    expect($orders)->toHaveCount(1);
    expect($orders[0]['status'])->toBe('completed');
});

it('sorts orders by date', function () {
    $oldOrder = Order::factory()->create([
        'user_id' => $this->user->id,
        'created_at' => now()->subDays(5),
    ]);

    $newOrder = Order::factory()->create([
        'user_id' => $this->user->id,
        'created_at' => now(),
    ]);

    $response = $this->getJson('/api/user/orders?sort=-created_at');

    $this->assertApiResponse($response, 200);
    $orders = $response->json('data');

    expect($orders[0]['id'])->toBe($newOrder->id);
    expect($orders[1]['id'])->toBe($oldOrder->id);
});

it('returns empty downloads for orders without fulfillments', function () {
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->digitalProduct->id,
        'product_price_id' => $this->digitalPrice->id,
        'status' => 'pending',
    ]);

    $response = $this->getJson("/api/user/orders/{$order->id}/downloads");

    $this->assertApiResponse($response, 200);

    expect($response->json('downloads'))->toBeEmpty();
});

it('only exposes downloads from completed fulfillments', function () {
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->digitalProduct->id,
        'product_price_id' => $this->digitalPrice->id,
        'status' => 'completed',
    ]);

    Fulfillment::factory()->create([
        'order_id' => $order->id,
        'type' => 'download',
        'status' => 'completed',
        'meta' => ['download_url' => 'https://example.com/completed.zip'],
    ]);

    Fulfillment::factory()->create([
        'order_id' => $order->id,
        'type' => 'download',
        'status' => 'pending',
        'meta' => ['download_url' => 'https://example.com/pending.zip'],
    ]);

    $response = $this->getJson("/api/user/orders/{$order->id}/downloads");

    $this->assertApiResponse($response, 200);
    expect($response->json('downloads'))->toHaveCount(1);
    expect($response->json('downloads.0.download_url'))->toBe('https://example.com/completed.zip');
});
