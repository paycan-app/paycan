<?php

use App\Models\Product;
use App\Models\ProductPrice;
use Tests\Feature\BaseApiTest;

uses(BaseApiTest::class);

test('checkout rejects price that does not belong to specified product', function () {
    $user = $this->authenticateUser();

    // Create two different products
    $product1 = Product::factory()->create([
        'type' => 'subscription',
        'is_active' => true,
    ]);

    $product2 = Product::factory()->create([
        'type' => 'subscription',
        'is_active' => true,
    ]);

    // Create a price for product1
    $price1 = ProductPrice::factory()->create([
        'product_id' => $product1->id,
        'is_active' => true,
    ]);

    // Create a price for product2
    $price2 = ProductPrice::factory()->create([
        'product_id' => $product2->id,
        'is_active' => true,
    ]);

    // Try to checkout product1 with price from product2 (should fail)
    $response = $this->actingAs($user, 'sanctum')->postJson('/api/user/checkout', [
        'product_id' => $product1->id,
        'product_price_id' => $price2->id, // Wrong price!
        'gateway' => 'stripe',
        'quantity' => 1,
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('product_price_id');
});

test('checkout accepts price that belongs to specified product', function () {
    $user = $this->authenticateUser();

    // Create a product
    $product = Product::factory()->create([
        'type' => 'subscription',
        'is_active' => true,
    ]);

    // Create a price for the product
    $price = ProductPrice::factory()->create([
        'product_id' => $product->id,
        'is_active' => true,
        'billing_period' => 'monthly',
    ]);

    // Try to checkout with correct product and price
    $response = $this->actingAs($user, 'sanctum')->postJson('/api/user/checkout', [
        'product_id' => $product->id,
        'product_price_id' => $price->id, // Correct price!
        'gateway' => 'stripe',
        'quantity' => 1,
    ]);

    $response->assertCreated();
    $response->assertJsonStructure([
        'checkout' => [
            'session_id',
            'checkout_url',
            'order_id',
        ],
    ]);
});

test('checkout with multiple prices for same product works correctly', function () {
    $user = $this->authenticateUser();

    // Create a product
    $product = Product::factory()->create([
        'type' => 'subscription',
        'is_active' => true,
    ]);

    // Create multiple prices for the same product
    $monthlyPrice = ProductPrice::factory()->create([
        'product_id' => $product->id,
        'title' => 'Monthly',
        'is_active' => true,
        'billing_period' => 'monthly',
    ]);

    $yearlyPrice = ProductPrice::factory()->create([
        'product_id' => $product->id,
        'title' => 'Yearly',
        'is_active' => true,
        'billing_period' => 'yearly',
    ]);

    // Checkout with monthly price
    $response = $this->actingAs($user, 'sanctum')->postJson('/api/user/checkout', [
        'product_id' => $product->id,
        'product_price_id' => $monthlyPrice->id,
        'gateway' => 'stripe',
        'quantity' => 1,
    ]);

    $response->assertCreated();

    // Checkout with yearly price
    $response = $this->actingAs($user, 'sanctum')->postJson('/api/user/checkout', [
        'product_id' => $product->id,
        'product_price_id' => $yearlyPrice->id,
        'gateway' => 'stripe',
        'quantity' => 1,
    ]);

    $response->assertCreated();
});
