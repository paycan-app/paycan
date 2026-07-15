<?php

use App\Models\AdminUser;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

// Order Policy Tests

it('admin user can view all orders', function () {
    $adminUser = AdminUser::factory()->create();
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    expect($adminUser->can('viewAny', Order::class))->toBeTrue()
        ->and($adminUser->can('view', $order))->toBeTrue();
});

it('regular user can only view own orders', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $ownOrder = Order::factory()->create(['user_id' => $user->id]);
    $otherOrder = Order::factory()->create(['user_id' => $otherUser->id]);

    expect($user->can('view', $ownOrder))->toBeTrue()
        ->and($user->can('view', $otherOrder))->toBeFalse();
});

it('regular user cannot create orders in filament', function () {
    $user = User::factory()->create();

    expect($user->can('create', Order::class))->toBeFalse();
});

it('admin user can create orders', function () {
    $adminUser = AdminUser::factory()->create();

    expect($adminUser->can('create', Order::class))->toBeTrue();
});

it('regular user cannot update orders', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    expect($user->can('update', $order))->toBeFalse();
});

it('admin user can update any order', function () {
    $adminUser = AdminUser::factory()->create();
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    expect($adminUser->can('update', $order))->toBeTrue();
});

it('regular user cannot delete orders', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    expect($user->can('delete', $order))->toBeFalse();
});

it('admin user can delete any order', function () {
    $adminUser = AdminUser::factory()->create();
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    expect($adminUser->can('delete', $order))->toBeTrue();
});

// Product Policy Tests

it('admin user can view all products', function () {
    $adminUser = AdminUser::factory()->create();
    $activeProduct = Product::factory()->create(['is_active' => true]);
    $inactiveProduct = Product::factory()->create(['is_active' => false]);

    expect($adminUser->can('viewAny', Product::class))->toBeTrue()
        ->and($adminUser->can('view', $activeProduct))->toBeTrue()
        ->and($adminUser->can('view', $inactiveProduct))->toBeTrue();
});

it('regular user can only view active products', function () {
    $user = User::factory()->create();
    $activeProduct = Product::factory()->create(['is_active' => true]);
    $inactiveProduct = Product::factory()->create(['is_active' => false]);

    expect($user->can('viewAny', Product::class))->toBeTrue()
        ->and($user->can('view', $activeProduct))->toBeTrue()
        ->and($user->can('view', $inactiveProduct))->toBeFalse();
});

it('regular user cannot create products', function () {
    $user = User::factory()->create();

    expect($user->can('create', Product::class))->toBeFalse();
});

it('admin user can create products', function () {
    $adminUser = AdminUser::factory()->create();

    expect($adminUser->can('create', Product::class))->toBeTrue();
});

it('regular user cannot update products', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    expect($user->can('update', $product))->toBeFalse();
});

it('admin user can update any product', function () {
    $adminUser = AdminUser::factory()->create();
    $product = Product::factory()->create();

    expect($adminUser->can('update', $product))->toBeTrue();
});

it('regular user cannot delete products', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    expect($user->can('delete', $product))->toBeFalse();
});

it('admin user can delete any product', function () {
    $adminUser = AdminUser::factory()->create();
    $product = Product::factory()->create();

    expect($adminUser->can('delete', $product))->toBeTrue();
});
