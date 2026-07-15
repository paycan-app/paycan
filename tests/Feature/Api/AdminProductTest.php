<?php

use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\Settings\SettingsManager;

beforeEach(function () {
    // Set API key for admin authentication using SettingsManager
    app(SettingsManager::class)->set('app.api_key', 'test-api-key', 'string');
});

it('can list all products including inactive ones with api key', function () {
    Product::factory()->count(3)->create(['is_active' => true]);
    Product::factory()->count(2)->create(['is_active' => false]);

    $response = $this->getJson('/api/admin/products', [
        'X-API-Key' => 'test-api-key',
    ]);

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

    expect($response->json('total'))->toBe(5);
});

it('can filter products by type', function () {
    Product::factory()->create(['type' => 'digital', 'is_active' => true]);
    Product::factory()->create(['type' => 'subscription', 'is_active' => true]);

    $response = $this->getJson('/api/admin/products?filter[type]=digital', [
        'X-API-Key' => 'test-api-key',
    ]);

    $response->assertSuccessful();
    $products = $response->json('data');
    expect($products)->toHaveCount(1);
    expect($products[0]['type'])->toBe('digital');
});

it('can filter products by active status', function () {
    Product::factory()->count(2)->create(['is_active' => true]);
    Product::factory()->create(['is_active' => false]);

    $response = $this->getJson('/api/admin/products?filter[is_active]=1', [
        'X-API-Key' => 'test-api-key',
    ]);

    $response->assertSuccessful();
    $products = $response->json('data');
    expect($products)->toHaveCount(2);
});

it('can show single product with includes', function () {
    $product = Product::factory()->create();
    ProductPrice::factory()->count(2)->create(['product_id' => $product->id]);

    $response = $this->getJson("/api/admin/products/{$product->id}?include=prices", [
        'X-API-Key' => 'test-api-key',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'prices',
            ],
        ]);
});

it('requires api key for admin access', function () {
    $response = $this->getJson('/api/admin/products');

    $response->assertUnauthorized();
});

it('can list product prices', function () {
    $product = Product::factory()->create();
    ProductPrice::factory()->count(3)->create(['product_id' => $product->id]);

    $response = $this->getJson("/api/admin/products/{$product->id}/prices", [
        'X-API-Key' => 'test-api-key',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'amount',
                    'currency',
                    'billing_period',
                ],
            ],
        ]);

    expect($response->json('data'))->toHaveCount(3);
});

it('can create product price', function () {
    $product = Product::factory()->create();

    $priceData = [
        'title' => 'Monthly Plan',
        'amount' => 9.99,
        'currency' => 'USD',
        'billing_period' => 'monthly',
        'trial_days' => 7,
        'is_active' => true,
    ];

    $response = $this->postJson("/api/admin/products/{$product->id}/prices", $priceData, [
        'X-API-Key' => 'test-api-key',
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'amount',
                'currency',
                'billing_period',
                'trial_days',
                'is_active',
            ],
        ]);

    expect($response->json('data.title'))->toBe('Monthly Plan');
    expect($response->json('data.amount'))->toBe('9.99');
});

it('validates required fields when creating price', function () {
    $product = Product::factory()->create();

    $response = $this->postJson("/api/admin/products/{$product->id}/prices", [], [
        'X-API-Key' => 'test-api-key',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['amount', 'currency', 'billing_period']);
});

it('can update product price', function () {
    $product = Product::factory()->create();
    $price = ProductPrice::factory()->create(['product_id' => $product->id, 'amount' => 9.99]);

    $response = $this->putJson("/api/admin/products/{$product->id}/prices/{$price->id}", [
        'amount' => 19.99,
    ], [
        'X-API-Key' => 'test-api-key',
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'id' => $price->id,
                'amount' => '19.99',
            ],
        ]);
});

it('cannot update price for different product', function () {
    $product1 = Product::factory()->create();
    $product2 = Product::factory()->create();
    $price = ProductPrice::factory()->create(['product_id' => $product1->id]);

    $response = $this->putJson("/api/admin/products/{$product2->id}/prices/{$price->id}", [
        'amount' => 19.99,
    ], [
        'X-API-Key' => 'test-api-key',
    ]);

    $response->assertNotFound();
});

it('can delete product price', function () {
    $product = Product::factory()->create();
    $price = ProductPrice::factory()->create(['product_id' => $product->id]);

    $response = $this->deleteJson("/api/admin/products/{$product->id}/prices/{$price->id}", [], [
        'X-API-Key' => 'test-api-key',
    ]);

    $response->assertSuccessful();
    expect(ProductPrice::find($price->id))->toBeNull();
});

it('cannot delete price for different product', function () {
    $product1 = Product::factory()->create();
    $product2 = Product::factory()->create();
    $price = ProductPrice::factory()->create(['product_id' => $product1->id]);

    $response = $this->deleteJson("/api/admin/products/{$product2->id}/prices/{$price->id}", [], [
        'X-API-Key' => 'test-api-key',
    ]);

    $response->assertNotFound();
    expect(ProductPrice::find($price->id))->not->toBeNull();
});
