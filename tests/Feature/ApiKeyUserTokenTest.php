<?php

use App\Models\User;
use App\Services\Settings\SettingsManager;
use Tests\Feature\BaseApiTest;

uses(BaseApiTest::class);

beforeEach(function () {
    $this->testApiKey = 'pk_test123456789012345678901234567890';
    app(SettingsManager::class)->set('app.api_key', $this->testApiKey, 'string', false);
});

test('requires api key to generate user token', function () {
    $response = $this->postJson('/api/admin/users/sync', [
        'user_id' => 'usr_123',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'error' => 'Unauthorized',
            'message' => 'API key is required',
        ]);
});

test('requires user object with name and email for new user', function () {
    $response = $this->withHeader('X-API-Key', $this->testApiKey)
        ->postJson('/api/admin/users/sync', [
            'user_id' => 'usr_123',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['user', 'user.name', 'user.email']);
});

test('creates new user with user_id and user object', function () {
    $response = $this->withHeader('X-API-Key', $this->testApiKey)
        ->postJson('/api/admin/users/sync', [
            'user_id' => 'usr_456',
            'user' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ],
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token']);

    $this->assertDatabaseHas('users', [
        'id' => 'usr_456',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

test('updates existing user with user object', function () {
    $user = User::factory()->create([
        'id' => 'usr_789',
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $originalUpdatedAt = $user->updated_at;

    // Wait a moment to ensure updated_at changes
    sleep(1);

    $response = $this->withHeader('X-API-Key', $this->testApiKey)
        ->postJson('/api/admin/users/sync', [
            'user_id' => 'usr_789',
            'user' => [
                'name' => 'New Name',
                'email' => 'new@example.com',
            ],
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token']);

    $user->refresh();
    expect($user->name)->toBe('New Name');
    expect($user->email)->toBe('new@example.com');
    expect($user->updated_at->isAfter($originalUpdatedAt))->toBeTrue();
});

test('returns token for existing user without updating when user object not provided', function () {
    $user = User::factory()->create([
        'id' => 'usr_999',
        'name' => 'Existing User',
        'email' => 'existing@example.com',
    ]);

    $originalUpdatedAt = $user->updated_at;

    sleep(1);

    $response = $this->withHeader('X-API-Key', $this->testApiKey)
        ->postJson('/api/admin/users/sync', [
            'user_id' => 'usr_999',
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token']);

    $user->refresh();
    expect($user->updated_at->eq($originalUpdatedAt))->toBeTrue();
});

test('validates user object fields', function () {
    $response = $this->withHeader('X-API-Key', $this->testApiKey)
        ->postJson('/api/admin/users/sync', [
            'user_id' => 'usr_invalid',
            'user' => [
                'email' => 'invalid-email',
            ],
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['user.email']);
});

test('requires user_id field', function () {
    $response = $this->withHeader('X-API-Key', $this->testApiKey)
        ->postJson('/api/admin/users/sync', [
            'user' => [
                'name' => 'Test User',
            ],
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['user_id']);
});

test('generated token can authenticate user', function () {
    $response = $this->withHeader('X-API-Key', $this->testApiKey)
        ->postJson('/api/admin/users/sync', [
            'user_id' => 'usr_auth_test',
            'user' => [
                'name' => 'Auth Test User',
                'email' => 'auth@example.com',
            ],
        ]);

    $response->assertStatus(200);

    $token = $response->json('token');
    $userId = 'usr_auth_test';

    // Test that the token works for authentication
    $authResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/user/me');

    $authResponse->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $userId,
                'name' => 'Auth Test User',
                'email' => 'auth@example.com',
            ],
        ]);
});

test('handles duplicate email when creating new user', function () {
    User::factory()->create([
        'email' => 'duplicate@example.com',
    ]);

    $response = $this->withHeader('X-API-Key', $this->testApiKey)
        ->postJson('/api/admin/users/sync', [
            'user_id' => 'usr_duplicate',
            'user' => [
                'name' => 'Duplicate Email User',
                'email' => 'duplicate@example.com',
            ],
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['user.email']);
});

test('allows updating existing user with same email', function () {
    $user = User::factory()->create([
        'id' => 'usr_same_email',
        'name' => 'Original User',
        'email' => 'same@example.com',
    ]);

    $response = $this->withHeader('X-API-Key', $this->testApiKey)
        ->postJson('/api/admin/users/sync', [
            'user_id' => 'usr_same_email',
            'user' => [
                'name' => 'Updated User',
                'email' => 'same@example.com',
            ],
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token']);

    $user->refresh();
    expect($user->name)->toBe('Updated User');
});

test('requires email when creating new user', function () {
    $response = $this->withHeader('X-API-Key', $this->testApiKey)
        ->postJson('/api/admin/users/sync', [
            'user_id' => 'usr_name_only',
            'user' => [
                'name' => 'Name Only User',
            ],
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['user.email']);
});

test('requires name when creating new user', function () {
    $response = $this->withHeader('X-API-Key', $this->testApiKey)
        ->postJson('/api/admin/users/sync', [
            'user_id' => 'usr_email_only',
            'user' => [
                'email' => 'emailonly@example.com',
            ],
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['user.name']);
});
