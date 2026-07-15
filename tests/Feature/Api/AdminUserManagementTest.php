<?php

use App\Models\User;
use Tests\Feature\BaseApiTest;

class AdminUserManagementTest extends BaseApiTest
{
    protected function authenticateAdmin(): array
    {
        return ['X-API-Key' => 'test_admin_key_for_testing'];
    }

    public function test_admin_can_list_users()
    {
        // Create some test users
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/admin/users', $this->authenticateAdmin());

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
            ],
            'current_page',
            'per_page',
            'total',
        ]);
    }

    public function test_admin_can_filter_users_by_email()
    {
        User::factory()->create(['email' => 'john@example.com']);
        User::factory()->create(['email' => 'jane@example.com']);
        User::factory()->create(['email' => 'bob@test.com']);

        $response = $this->getJson('/api/admin/users?filter[email]=example', $this->authenticateAdmin());

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_admin_can_filter_users_by_name()
    {
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Doe']);
        User::factory()->create(['name' => 'Bob Smith']);

        $response = $this->getJson('/api/admin/users?filter[name]=Doe', $this->authenticateAdmin());

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_admin_can_filter_verified_users()
    {
        User::factory()->create(['email_verified_at' => now()]);
        User::factory()->create(['email_verified_at' => null]);

        $response = $this->getJson('/api/admin/users?filter[email_verified]=true', $this->authenticateAdmin());

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_admin_can_sort_users()
    {
        $user1 = User::factory()->create(['name' => 'Alice', 'created_at' => now()->subDays(2)]);
        $user2 = User::factory()->create(['name' => 'Bob', 'created_at' => now()->subDays(1)]);
        $user3 = User::factory()->create(['name' => 'Charlie', 'created_at' => now()]);

        $response = $this->getJson('/api/admin/users?sort=name', $this->authenticateAdmin());

        $response->assertOk();
        $data = $response->json('data');
        $this->assertEquals('Alice', $data[0]['name']);
        $this->assertEquals('Bob', $data[1]['name']);
        $this->assertEquals('Charlie', $data[2]['name']);
    }

    public function test_admin_can_include_user_relationships()
    {
        $user = User::factory()->create();
        \App\Models\Order::factory()->create(['user_id' => $user->id]);
        \App\Models\Subscription::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/admin/users/{$user->id}?include=orders,subscriptions", $this->authenticateAdmin());

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'orders' => [
                    '*' => [
                        'id',
                        'status',
                        'total',
                    ],
                ],
                'subscriptions' => [
                    '*' => [
                        'id',
                        'status',
                    ],
                ],
            ],
        ]);
    }

    public function test_admin_can_view_specific_user()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/admin/users/{$user->id}", $this->authenticateAdmin());

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertEquals($user->id, $response->json('data.id'));
    }

    public function test_admin_can_create_user()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ];

        $response = $this->postJson('/api/admin/users', $userData, $this->authenticateAdmin());

        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'created_at',
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_admin_can_update_user()
    {
        $user = User::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->putJson("/api/admin/users/{$user->id}", $updateData, $this->authenticateAdmin());

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'updated_at',
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_admin_can_update_user_password()
    {
        $user = User::factory()->create();
        $originalPassword = $user->password;

        $updateData = [
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ];

        $response = $this->putJson("/api/admin/users/{$user->id}", $updateData, $this->authenticateAdmin());

        $response->assertOk();

        $user->refresh();
        $this->assertNotEquals($originalPassword, $user->password);
    }

    public function test_admin_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/admin/users/{$user->id}", [], $this->authenticateAdmin());

        $response->assertNoContent();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_user_validation_errors()
    {
        // Test missing required fields
        $response = $this->postJson('/api/admin/users', [], $this->authenticateAdmin());

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'email', 'password']);

        // Test invalid email format
        $response = $this->postJson('/api/admin/users', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $this->authenticateAdmin());

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email']);

        // Test password confirmation mismatch
        $response = $this->postJson('/api/admin/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
        ], $this->authenticateAdmin());

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_admin_can_sync_users()
    {
        $response = $this->postJson('/api/admin/users/sync', [
            'user_id' => 'ext_123',
            'user' => [
                'name' => 'Synced User 1',
                'email' => 'synced1@example.com',
            ],
        ], $this->authenticateAdmin());

        $response->assertOk();
        $response->assertJsonStructure(['token']);

        $this->assertDatabaseHas('users', [
            'id' => 'ext_123',
            'email' => 'synced1@example.com',
        ]);

        // Syncing again updates the existing user and returns a fresh token
        $response = $this->postJson('/api/admin/users/sync', [
            'user_id' => 'ext_123',
            'user' => [
                'name' => 'Synced User Renamed',
            ],
        ], $this->authenticateAdmin());

        $response->assertOk();
        $response->assertJsonStructure(['token']);

        $this->assertDatabaseHas('users', [
            'id' => 'ext_123',
            'name' => 'Synced User Renamed',
        ]);
    }

    public function test_unauthenticated_request_fails()
    {
        $response = $this->getJson('/api/admin/users');

        $response->assertUnauthorized();
    }

    public function test_invalid_api_key_fails()
    {
        $response = $this->getJson('/api/admin/users', ['X-API-Key' => 'invalid_key']);

        $response->assertUnauthorized();
    }

    public function test_admin_cannot_view_nonexistent_user()
    {
        $response = $this->getJson('/api/admin/users/nonexistent-id', $this->authenticateAdmin());

        $response->assertNotFound();
    }

    public function test_admin_cannot_update_nonexistent_user()
    {
        $updateData = [
            'name' => 'Updated Name',
        ];

        $response = $this->putJson('/api/admin/users/nonexistent-id', $updateData, $this->authenticateAdmin());

        $response->assertNotFound();
    }

    public function test_admin_cannot_delete_nonexistent_user()
    {
        $response = $this->deleteJson('/api/admin/users/nonexistent-id', [], $this->authenticateAdmin());

        $response->assertNotFound();
    }

    public function test_admin_can_search_users()
    {
        User::factory()->create(['name' => 'John Smith', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@test.com']);
        User::factory()->create(['name' => 'Bob Johnson', 'email' => 'bob@example.com']);

        $response = $this->getJson('/api/admin/users?search=john', $this->authenticateAdmin());

        $response->assertOk();
        $response->assertJsonCount(2, 'data'); // Should find John Smith and Bob Johnson
    }

    public function test_admin_can_paginate_users()
    {
        User::factory()->count(25)->create();

        $response = $this->getJson('/api/admin/users?per_page=10&page=2', $this->authenticateAdmin());

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
        $this->assertEquals(2, $response->json('current_page'));
        $this->assertEquals(10, $response->json('per_page'));
        $this->assertEquals(25, $response->json('total'));
    }
}
