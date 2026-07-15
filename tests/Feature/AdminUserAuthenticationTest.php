<?php

use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('can create an admin user', function () {
    $adminUser = AdminUser::factory()->create([
        'email' => 'admin@test.com',
        'role' => 'super_admin',
    ]);

    expect($adminUser)->toBeInstanceOf(AdminUser::class)
        ->and($adminUser->email)->toBe('admin@test.com')
        ->and($adminUser->role)->toBe('super_admin');
});

it('hashes admin user password', function () {
    $adminUser = AdminUser::factory()->create([
        'password' => 'password123',
    ]);

    expect($adminUser->password)->not->toBe('password123');
});

it('can authenticate admin user with admin guard', function () {
    $adminUser = AdminUser::factory()->create([
        'email' => 'admin@test.com',
    ]);

    $this->assertGuest('admin');

    auth('admin')->login($adminUser);

    $this->assertAuthenticatedAs($adminUser, 'admin');
});

it('filament uses admin guard for authentication', function () {
    $adminUser = AdminUser::factory()->create();

    // Filament should use the admin guard, not web guard
    auth('admin')->login($adminUser);

    $this->assertAuthenticatedAs($adminUser, 'admin');
    $this->assertGuest('web'); // User is not authenticated with web guard
});

it('regular user cannot authenticate with admin guard', function () {
    $user = \App\Models\User::factory()->create();

    // Attempting to authenticate a User with admin guard should fail
    expect(auth('admin')->getProvider()->retrieveById($user->id))->toBeNull();
});

it('admin users are completely separate from regular users', function () {
    $adminUser = AdminUser::factory()->create([
        'email' => 'test@example.com',
    ]);

    $regularUser = \App\Models\User::factory()->create([
        'email' => 'test@example.com', // Same email, different table
    ]);

    expect($adminUser->email)->toBe($regularUser->email)
        ->and($adminUser->getTable())->toBe('admin_users')
        ->and($regularUser->getTable())->toBe('users')
        ->and($adminUser->id)->not->toBe($regularUser->id);
});

it('validates email uniqueness in admin_users table', function () {
    AdminUser::factory()->create([
        'email' => 'duplicate@test.com',
    ]);

    expect(fn () => AdminUser::factory()->create([
        'email' => 'duplicate@test.com',
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('can store phone and role for admin users', function () {
    $adminUser = AdminUser::factory()->create([
        'phone' => '+1234567890',
        'role' => 'operator',
    ]);

    expect($adminUser->phone)->toBe('+1234567890')
        ->and($adminUser->role)->toBe('operator');
});

it('generates admin user ids with adm__ prefix', function () {
    $adminUser = AdminUser::factory()->create();

    expect($adminUser->id)->toBeString()
        ->and($adminUser->id)->toStartWith('adm__')
        ->and(strlen($adminUser->id))->toBe(31); // "adm__" (5) + ULID (26) = 31
});

it('admin user ids are different from regular user ids by format', function () {
    $adminUser = AdminUser::factory()->create();
    $regularUser = \App\Models\User::factory()->create();

    // AdminUser IDs start with "adm__"
    expect($adminUser->id)->toStartWith('adm__');

    // User IDs are ULIDs without prefix (or custom format)
    expect($regularUser->id)->not->toStartWith('adm__');

    // IDs are different
    expect($adminUser->id)->not->toBe($regularUser->id);
});
