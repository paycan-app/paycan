<?php

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('can list transactions for authenticated user only', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Sanctum::actingAs($user);

    Transaction::factory()->count(3)->create(['user_id' => $user->id]);
    Transaction::factory()->count(2)->create(['user_id' => $otherUser->id]); // Should not appear

    $response = $this->getJson('/api/user/transactions');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'status',
                    'type',
                    'amount',
                    'gateway',
                    'created_at',
                ],
            ],
            'current_page',
            'per_page',
            'total',
        ]);

    expect($response->json('data'))->toHaveCount(3);
    expect($response->json('total'))->toBe(3);
});

it('can filter transactions by status', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Transaction::factory()->create(['status' => 'pending', 'user_id' => $user->id]);
    Transaction::factory()->create(['status' => 'completed', 'user_id' => $user->id]);
    Transaction::factory()->create(['status' => 'pending', 'user_id' => $user->id]);

    $response = $this->getJson('/api/user/transactions?filter[status]=pending');

    $response->assertSuccessful();
    $transactions = $response->json('data');
    expect($transactions)->toHaveCount(2);
    expect($transactions[0]['status'])->toBe('pending');
});

it('can filter transactions by type', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Transaction::factory()->create(['type' => 'payment', 'user_id' => $user->id]);
    Transaction::factory()->create(['type' => 'refund', 'user_id' => $user->id]);
    Transaction::factory()->create(['type' => 'payment', 'user_id' => $user->id]);

    $response = $this->getJson('/api/user/transactions?filter[type]=payment');

    $response->assertSuccessful();
    $transactions = $response->json('data');
    expect($transactions)->toHaveCount(2);
    expect($transactions[0]['type'])->toBe('payment');
});

it('can filter transactions by gateway', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Transaction::factory()->create(['gateway' => 'stripe', 'user_id' => $user->id]);
    Transaction::factory()->create(['gateway' => 'paypal', 'user_id' => $user->id]);

    $response = $this->getJson('/api/user/transactions?filter[gateway]=stripe');

    $response->assertSuccessful();
    $transactions = $response->json('data');
    expect($transactions)->toHaveCount(1);
    expect($transactions[0]['gateway'])->toBe('stripe');
});

it('can include order relationship', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $order = Order::factory()->create(['user_id' => $user->id]);
    Transaction::factory()->create(['user_id' => $user->id, 'order_id' => $order->id]);

    $response = $this->getJson('/api/user/transactions?include=order');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'order',
                ],
            ],
        ]);
});

it('can sort transactions by amount', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Transaction::factory()->create(['amount' => 1000, 'user_id' => $user->id]);
    Transaction::factory()->create(['amount' => 5000, 'user_id' => $user->id]);

    $response = $this->getJson('/api/user/transactions?sort=-amount');

    $response->assertSuccessful();
    $transactions = $response->json('data');
    expect($transactions[0]['amount'])->toBe('5000.00');
    expect($transactions[1]['amount'])->toBe('1000.00');
});

it('requires authentication to list transactions', function () {
    $response = $this->getJson('/api/user/transactions');

    $response->assertUnauthorized();
});

it('can show single transaction with includes for own transaction', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $order = Order::factory()->create(['user_id' => $user->id]);
    $transaction = Transaction::factory()->create(['user_id' => $user->id, 'order_id' => $order->id]);

    $response = $this->getJson("/api/user/transactions/{$transaction->id}?include=order");

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                'id',
                'amount',
                'order',
            ],
        ]);
});

it('cannot access other users transactions', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Sanctum::actingAs($user);

    $otherTransaction = Transaction::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->getJson("/api/user/transactions/{$otherTransaction->id}");

    $response->assertNotFound();
});
