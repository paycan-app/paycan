<?php

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('can list active products only for authenticated user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Product::factory()->count(3)->create(['is_active' => true]);
    Product::factory()->count(2)->create(['is_active' => false]); // Should not appear

    $response = $this->getJson('/api/user/products');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'type',
                    'is_active',
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

it('can filter products by type', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Product::factory()->create(['type' => 'digital', 'is_active' => true]);
    Product::factory()->create(['type' => 'subscription', 'is_active' => true]);
    Product::factory()->create(['type' => 'digital', 'is_active' => true]);

    $response = $this->getJson('/api/user/products?filter[type]=digital');

    $response->assertSuccessful();
    $products = $response->json('data');
    expect($products)->toHaveCount(2);
    expect($products[0]['type'])->toBe('digital');
});

it('can include prices and automatically filters to active only', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create(['is_active' => true]);
    ProductPrice::factory()->count(2)->create(['product_id' => $product->id, 'is_active' => true]);
    ProductPrice::factory()->create(['product_id' => $product->id, 'is_active' => false]);

    $response = $this->getJson('/api/user/products?include=prices');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'prices',
                ],
            ],
        ]);

    $product = $response->json('data')[0];
    // Should only include 2 active prices, not the inactive one
    expect($product['prices'])->toHaveCount(2);
});

it('can sort products by title', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Product::factory()->create(['title' => 'Zebra Product', 'is_active' => true]);
    Product::factory()->create(['title' => 'Alpha Product', 'is_active' => true]);

    $response = $this->getJson('/api/user/products?sort=title');

    $response->assertSuccessful();
    $products = $response->json('data');
    expect($products[0]['title'])->toBe('Alpha Product');
    expect($products[1]['title'])->toBe('Zebra Product');
});

it('allows listing active products without authentication', function () {
    Product::factory()->create(['is_active' => true]);

    $response = $this->getJson('/api/user/products');

    $response->assertSuccessful();
});

it('can show single active product with includes', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create(['is_active' => true]);
    ProductPrice::factory()->count(2)->create(['product_id' => $product->id]);

    $response = $this->getJson("/api/user/products/{$product->id}?include=prices");

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'prices',
            ],
        ]);
});

it('cannot access inactive product', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create(['is_active' => false]);

    $response = $this->getJson("/api/user/products/{$product->id}");

    $response->assertNotFound();
});

it('allows viewing a single active product without authentication', function () {
    $product = Product::factory()->create(['is_active' => true]);

    $response = $this->getJson("/api/user/products/{$product->id}");

    $response->assertSuccessful();
});
