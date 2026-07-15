<?php

use App\Models\Transaction;
use App\Models\User;

beforeEach(function () {
    $this->withHeaders([
        'X-API-Key' => 'test_admin_key_for_testing',
    ]);
});

it('can list all transactions', function () {
    Transaction::factory()->count(5)->create();

    $response = $this->getJson('/api/admin/transactions');

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(5);
    expect($response->json('data.0'))->toHaveKeys(['id', 'type', 'amount', 'currency', 'status', 'gateway']);
});

it('can view specific transaction', function () {
    $transaction = Transaction::factory()->create();

    $response = $this->getJson("/api/admin/transactions/{$transaction->id}");

    $response->assertSuccessful();
    expect($response->json('data.id'))->toBe($transaction->id);
    expect($response->json('data'))->toHaveKeys(['id', 'type', 'amount', 'currency', 'status', 'gateway']);
});

it('can filter transactions by status', function () {
    Transaction::factory()->create(['status' => 'completed']);
    Transaction::factory()->create(['status' => 'pending']);
    Transaction::factory()->create(['status' => 'completed']);

    $response = $this->getJson('/api/admin/transactions?filter[status]=completed');

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(2);
    expect($response->json('data.0.status'))->toBe('completed');
});

it('can filter transactions by gateway', function () {
    Transaction::factory()->create(['gateway' => 'stripe']);
    Transaction::factory()->create(['gateway' => 'paypal']);
    Transaction::factory()->create(['gateway' => 'stripe']);

    $response = $this->getJson('/api/admin/transactions?filter[gateway]=stripe');

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(2);
    expect($response->json('data.0.gateway'))->toBe('stripe');
});

it('can filter transactions by amount range', function () {
    Transaction::factory()->create(['amount' => 1000]); // $10.00
    Transaction::factory()->create(['amount' => 5000]); // $50.00
    Transaction::factory()->create(['amount' => 10000]); // $100.00

    $response = $this->getJson('/api/admin/transactions?filter[amount_min]=2000&filter[amount_max]=8000');

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(1);
    expect((float) $response->json('data.0.amount'))->toBe(5000.0);
});

it('can sort transactions by created date', function () {
    $transaction1 = Transaction::factory()->create(['created_at' => now()->subDays(2)]);
    $transaction2 = Transaction::factory()->create(['created_at' => now()->subDays(1)]);
    $transaction3 = Transaction::factory()->create(['created_at' => now()]);

    $response = $this->getJson('/api/admin/transactions?sort=-created_at');

    $response->assertSuccessful();
    $data = $response->json('data');
    expect($data[0]['id'])->toBe($transaction3->id);
    expect($data[1]['id'])->toBe($transaction2->id);
    expect($data[2]['id'])->toBe($transaction1->id);
});

it('can include transaction relationships', function () {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create(['user_id' => $user->id]);

    $response = $this->getJson("/api/admin/transactions/{$transaction->id}?include=user");

    $response->assertSuccessful();
    expect($response->json('data.user.id'))->toBe($user->id);
    expect($response->json('data.user'))->toHaveKeys(['id', 'name', 'email']);
});

it('can search transactions by user email', function () {
    $user1 = User::factory()->create(['email' => 'john@example.com']);
    $user2 = User::factory()->create(['email' => 'jane@test.com']);

    Transaction::factory()->create(['user_id' => $user1->id]);
    Transaction::factory()->create(['user_id' => $user2->id]);

    $response = $this->getJson('/api/admin/transactions?search=john@example.com');

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(1);
});

it('can paginate transactions', function () {
    Transaction::factory()->count(25)->create();

    $response = $this->getJson('/api/admin/transactions?per_page=10&page=2');

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(10);
    expect($response->json('current_page'))->toBe(2);
    expect($response->json('per_page'))->toBe(10);
    expect($response->json('total'))->toBe(25);
});

it('requires authentication', function () {
    $this->flushHeaders();

    $response = $this->getJson('/api/admin/transactions');

    $response->assertUnauthorized();
});

it('rejects invalid api key', function () {
    $response = $this->withHeaders(['X-API-Key' => 'invalid-key'])->getJson('/api/admin/transactions');

    $response->assertUnauthorized();
});

it('returns 404 for non-existent transaction', function () {
    $response = $this->getJson('/api/admin/transactions/non-existent-id');

    $response->assertNotFound();
});

it('can access any users transaction', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $transaction1 = Transaction::factory()->create(['user_id' => $user1->id]);
    $transaction2 = Transaction::factory()->create(['user_id' => $user2->id]);

    $response1 = $this->getJson("/api/admin/transactions/{$transaction1->id}", [
        'X-API-Key' => 'test_admin_key_for_testing',
    ]);

    $response2 = $this->getJson("/api/admin/transactions/{$transaction2->id}", [
        'X-API-Key' => 'test_admin_key_for_testing',
    ]);

    $response1->assertSuccessful();
    $response2->assertSuccessful();
    expect($response1->json('data.user_id'))->toBe($user1->id);
    expect($response2->json('data.user_id'))->toBe($user2->id);
});
