<?php

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;

beforeEach(function () {
    $this->withHeaders([
        'X-API-Key' => 'test_admin_key_for_testing',
    ]);
});

it('requires api key for admin wallet operations', function () {
    $user = User::factory()->create();

    $this->flushHeaders();

    $response = $this->getJson("/api/admin/users/{$user->id}/wallets");
    $response->assertUnauthorized();

    $deductResponse = $this->postJson('/api/admin/wallets/deduct', [
        'user_id' => $user->id,
        'amount' => 10,
    ]);
    $deductResponse->assertUnauthorized();
});

it('can list user wallets via admin api', function () {
    $user = User::factory()->create();

    $response = $this->getJson("/api/admin/users/{$user->id}/wallets");

    $response->assertSuccessful()
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

    $data = $response->json('data');
    expect($data)->toHaveCount(2);
    expect(collect($data)->pluck('type')->toArray())->toContain('basic', 'premium');
});

it('can get wallet transactions via admin api', function () {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->create([
        'user_id' => $user->id,
        'type' => 'basic',
        'balance' => 100,
    ]);

    WalletTransaction::create([
        'wallet_id' => $wallet->id,
        'user_id' => $user->id,
        'type' => 'credit',
        'action' => 'manual_adjustment',
        'amount' => 100,
        'balance_after' => 100,
        'description' => 'Initial credit',
    ]);

    $response = $this->getJson("/api/admin/users/{$user->id}/wallets/basic/transactions");

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
                ],
            ],
        ]);

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.action'))->toBe('manual_adjustment');
});

it('can deduct credits via admin api for ai agent usage', function () {
    $user = User::factory()->create();
    $wallet = $user->getWallet('premium');
    $wallet->update(['balance' => 50.0]);

    $response = $this->postJson('/api/admin/wallets/deduct', [
        'user_id' => $user->id,
        'wallet_type' => 'premium',
        'amount' => 12.5,
        'reference_id' => 'agent_run_123',
        'description' => 'Agent execution - Claude 3.5 Sonnet',
        'meta' => [
            'model' => 'claude-3-5-sonnet',
            'prompt_tokens' => 800,
            'completion_tokens' => 450,
        ],
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.wallet.type', 'premium')
        ->assertJsonPath('data.wallet.balance', 37.5);

    $wallet->refresh();
    expect((float) $wallet->balance)->toBe(37.5);

    $this->assertDatabaseHas('wallet_transactions', [
        'wallet_id' => $wallet->id,
        'user_id' => $user->id,
        'type' => 'debit',
        'action' => 'usage',
        'amount' => 12.5,
        'reference_id' => 'agent_run_123',
    ]);
});

it('returns 422 with insufficient_credits when balance is lower than deduction amount', function () {
    $user = User::factory()->create();
    $wallet = $user->getWallet('basic');
    $wallet->update(['balance' => 5.0]);

    $response = $this->postJson('/api/admin/wallets/deduct', [
        'user_id' => $user->id,
        'wallet_type' => 'basic',
        'amount' => 20.0,
        'description' => 'Overspending attempt',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonPath('error', 'insufficient_credits')
        ->assertJsonPath('data.wallet_type', 'basic')
        ->assertJsonPath('data.current_balance', 5)
        ->assertJsonPath('data.required_amount', 20);

    $wallet->refresh();
    expect((float) $wallet->balance)->toBe(5.0); // Balance unchanged
});

it('can top up credits via admin api', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/admin/wallets/topup', [
        'user_id' => $user->id,
        'wallet_type' => 'basic',
        'amount' => 250.0,
        'description' => 'Promotional top-up',
        'reference_id' => 'promo_2026',
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.wallet.balance', 250);

    $wallet = $user->getWallet('basic');
    expect((float) $wallet->balance)->toBe(250.0);
});

it('can get all customer wallet transactions across all wallets via admin api', function () {
    $user = User::factory()->create();
    $basicWallet = $user->getWallet('basic');
    $premiumWallet = $user->getWallet('premium');

    WalletTransaction::create([
        'wallet_id' => $basicWallet->id,
        'user_id' => $user->id,
        'type' => 'credit',
        'action' => 'subscription_grant',
        'amount' => 100,
        'balance_after' => 100,
    ]);

    WalletTransaction::create([
        'wallet_id' => $premiumWallet->id,
        'user_id' => $user->id,
        'type' => 'credit',
        'action' => 'order_purchase',
        'amount' => 50,
        'balance_after' => 50,
    ]);

    $response = $this->getJson("/api/admin/users/{$user->id}/wallets/transactions");

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(2);

    // Can filter by wallet_type
    $filteredResponse = $this->getJson("/api/admin/users/{$user->id}/wallets/transactions?filter[wallet_type]=premium");
    $filteredResponse->assertSuccessful();
    expect($filteredResponse->json('data'))->toHaveCount(1);
    expect($filteredResponse->json('data.0.wallet_id'))->toBe($premiumWallet->id);
});

it('can get customer usage logs via admin api', function () {
    $user = User::factory()->create();
    $wallet = $user->getWallet('premium');

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
        'amount' => 5,
        'balance_after' => 95,
        'reference_id' => 'agent_task_abc',
        'description' => 'GPT-4o reasoning task',
        'meta' => ['tokens' => 2500],
    ]);

    $response = $this->getJson("/api/admin/users/{$user->id}/wallets/usages");

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.action'))->toBe('usage');
    expect($response->json('data.0.reference_id'))->toBe('agent_task_abc');
    expect($response->json('data.0.meta.tokens'))->toBe(2500);
});

it('can get global wallet transactions and usages via admin api', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $w1 = $user1->getWallet('basic');
    $w2 = $user2->getWallet('premium');

    WalletTransaction::create([
        'wallet_id' => $w1->id,
        'user_id' => $user1->id,
        'type' => 'credit',
        'action' => 'subscription_grant',
        'amount' => 100,
        'balance_after' => 100,
    ]);

    WalletTransaction::create([
        'wallet_id' => $w2->id,
        'user_id' => $user2->id,
        'type' => 'debit',
        'action' => 'usage',
        'amount' => 10,
        'balance_after' => 40,
        'reference_id' => 'global_run_99',
    ]);

    $txResponse = $this->getJson('/api/admin/wallets/transactions');
    $txResponse->assertSuccessful();
    expect(count($txResponse->json('data')))->toBeGreaterThanOrEqual(2);

    $usageResponse = $this->getJson('/api/admin/wallets/usages');
    $usageResponse->assertSuccessful();
    expect(count($usageResponse->json('data')))->toBeGreaterThanOrEqual(1);
    expect($usageResponse->json('data.0.action'))->toBe('usage');
});
