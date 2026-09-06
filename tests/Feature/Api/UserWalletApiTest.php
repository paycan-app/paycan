<?php

use App\Models\User;
use App\Models\WalletTransaction;
use Laravel\Sanctum\Sanctum;

it('requires authentication for user wallet endpoints', function () {
    $this->flushHeaders();

    $response = $this->getJson('/api/user/wallets');
    $response->assertUnauthorized();

    $deductResponse = $this->postJson('/api/user/wallets/deduct', [
        'amount' => 5,
    ]);
    $deductResponse->assertUnauthorized();
});

it('lists authenticated users wallets', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/user/wallets');

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'type',
                    'balance',
                    'currency',
                    'is_active',
                ],
            ],
        ]);

    $wallets = $response->json('data');
    expect($wallets)->toHaveCount(2);
    expect(collect($wallets)->pluck('type')->toArray())->toContain('basic', 'premium');
});

it('gets specific wallet by type for authenticated user', function () {
    $user = User::factory()->create();
    $wallet = $user->getWallet('premium');
    $wallet->update(['balance' => 88.0]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/user/wallets/premium');

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.type', 'premium')
        ->assertJsonPath('data.balance', '88.0000');
});

it('gets paginated wallet transactions for authenticated user', function () {
    $user = User::factory()->create();
    $wallet = $user->getWallet('basic');

    WalletTransaction::create([
        'wallet_id' => $wallet->id,
        'user_id' => $user->id,
        'type' => 'credit',
        'action' => 'subscription_grant',
        'amount' => 1000,
        'balance_after' => 1000,
        'description' => 'Subscription grant',
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/user/wallets/basic/transactions');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'wallet_id',
                    'type',
                    'action',
                    'amount',
                    'balance_after',
                    'description',
                ],
            ],
            'current_page',
            'per_page',
            'total',
        ]);

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.action'))->toBe('subscription_grant');
});

it('does not allow user to access another users transactions', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $wallet2 = $user2->getWallet('basic');
    WalletTransaction::create([
        'wallet_id' => $wallet2->id,
        'user_id' => $user2->id,
        'type' => 'credit',
        'action' => 'subscription_grant',
        'amount' => 500,
        'balance_after' => 500,
        'description' => 'User 2 private grant',
    ]);

    Sanctum::actingAs($user1);

    // User 1 asks for their basic transactions, should not see user 2's transaction
    $response = $this->getJson('/api/user/wallets/basic/transactions');

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(0);
});

it('allows authenticated user to deduct credits', function () {
    $user = User::factory()->create();
    $wallet = $user->getWallet('basic');
    $wallet->update(['balance' => 45.0]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/user/wallets/deduct', [
        'wallet_type' => 'basic',
        'amount' => 15.0,
        'description' => 'Agent action executed',
        'reference_id' => 'act_555',
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('success', true);

    expect((float) $response->json('data.wallet.balance'))->toBe(30.0);

    $wallet->refresh();
    expect((float) $wallet->balance)->toBe(30.0);
});

it('returns 422 when user has insufficient credits for deduction', function () {
    $user = User::factory()->create();
    $wallet = $user->getWallet('premium');
    $wallet->update(['balance' => 2.0]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/user/wallets/deduct', [
        'wallet_type' => 'premium',
        'amount' => 10.0,
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonPath('error', 'insufficient_credits')
        ->assertJsonPath('data.wallet_type', 'premium')
        ->assertJsonPath('data.current_balance', 2)
        ->assertJsonPath('data.required_amount', 10);
});

it('gets all wallet transactions across all wallets for authenticated user', function () {
    $user = User::factory()->create();
    $basic = $user->getWallet('basic');
    $premium = $user->getWallet('premium');

    WalletTransaction::create([
        'wallet_id' => $basic->id,
        'user_id' => $user->id,
        'type' => 'credit',
        'action' => 'subscription_grant',
        'amount' => 500,
        'balance_after' => 500,
    ]);

    WalletTransaction::create([
        'wallet_id' => $premium->id,
        'user_id' => $user->id,
        'type' => 'credit',
        'action' => 'order_purchase',
        'amount' => 20,
        'balance_after' => 20,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/user/wallets/transactions');

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(2);

    $filteredResponse = $this->getJson('/api/user/wallets/transactions?filter[wallet_type]=basic');
    $filteredResponse->assertSuccessful();
    expect($filteredResponse->json('data'))->toHaveCount(1);
    expect($filteredResponse->json('data.0.wallet_id'))->toBe($basic->id);

    $directFiltered = $this->getJson('/api/user/wallets/transactions?wallet_type=basic');
    $directFiltered->assertSuccessful();
    expect($directFiltered->json('data'))->toHaveCount(1);
    expect($directFiltered->json('data.0.wallet_id'))->toBe($basic->id);
});

it('gets credit usage logs for authenticated user', function () {
    $user = User::factory()->create();
    $wallet = $user->getWallet('basic');

    WalletTransaction::create([
        'wallet_id' => $wallet->id,
        'user_id' => $user->id,
        'type' => 'credit',
        'action' => 'subscription_grant',
        'amount' => 100,
        'balance_after' => 100,
    ]);

    WalletTransaction::create([
        'wallet_id' => $wallet->id,
        'user_id' => $user->id,
        'type' => 'debit',
        'action' => 'usage',
        'amount' => 3,
        'balance_after' => 97,
        'reference_id' => 'agent_user_run_1',
        'description' => 'Fast agent query',
        'meta' => ['tokens' => 500],
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/user/wallets/usages');

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.action'))->toBe('usage');
    expect($response->json('data.0.reference_id'))->toBe('agent_user_run_1');

    $refFiltered = $this->getJson('/api/user/wallets/usages?reference_id=agent_user_run_1');
    $refFiltered->assertSuccessful();
    expect($refFiltered->json('data'))->toHaveCount(1);

    $nonMatch = $this->getJson('/api/user/wallets/usages?reference_id=nonexistent');
    $nonMatch->assertSuccessful();
    expect($nonMatch->json('data'))->toBeEmpty();
});
