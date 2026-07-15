<?php

use App\Models\Order;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('can list orders with pagination for authenticated user only', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Sanctum::actingAs($user);

    Order::factory()->count(2)->create(['user_id' => $user->id]);
    Order::factory()->count(3)->create(['user_id' => $otherUser->id]); // Should not appear

    $response = $this->getJson('/api/user/orders');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'order_number',
                    'status',
                    'total',
                    'created_at',
                ],
            ],
            'current_page',
            'per_page',
            'total',
        ]);

    expect($response->json('data'))->toHaveCount(2);
    expect($response->json('total'))->toBe(2);
});

it('can filter orders by status for authenticated user only', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Sanctum::actingAs($user);

    Order::factory()->create(['status' => 'pending', 'user_id' => $user->id]);
    Order::factory()->create(['status' => 'completed', 'user_id' => $user->id]);
    Order::factory()->create(['status' => 'pending', 'user_id' => $otherUser->id]); // Should not appear

    $response = $this->getJson('/api/user/orders?filter[status]=pending');

    $response->assertSuccessful();
    $orders = $response->json('data');
    expect($orders)->toHaveCount(1);
    expect($orders[0]['status'])->toBe('pending');
    expect($orders[0]['user_id'])->toBe($user->id);
});

it('can include relationships', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $order = Order::factory()->create(['user_id' => $user->id]);

    $response = $this->getJson('/api/user/orders?include=productPrice');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'product_price',
                ],
            ],
        ]);
});

it('can sort orders by created_at', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $oldOrder = Order::factory()->create(['user_id' => $user->id, 'created_at' => now()->subDays(2)]);
    $newOrder = Order::factory()->create(['user_id' => $user->id, 'created_at' => now()]);

    $response = $this->getJson('/api/user/orders?sort=-created_at');

    $response->assertSuccessful();
    $orders = $response->json('data');
    expect($orders[0]['id'])->toBe($newOrder->id);
});

it('requires authentication', function () {
    $response = $this->getJson('/api/user/orders');

    $response->assertUnauthorized();
});

it('can show single order with includes for own order', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $order = Order::factory()->create(['user_id' => $user->id]);

    $response = $this->getJson("/api/user/orders/{$order->id}?include=productPrice");

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                'id',
                'order_number',
                'product_price',
            ],
        ]);
});

it('cannot access other users orders', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Sanctum::actingAs($user);

    $otherOrder = Order::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->getJson("/api/user/orders/{$otherOrder->id}");

    $response->assertNotFound();
});
