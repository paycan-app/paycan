<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPrice;

beforeEach(function () {
    // Set up API key authentication
    $this->withHeaders([
        'X-API-Key' => 'test_admin_key_for_testing',
    ]);
});

describe('Admin Product Management', function () {
    it('can list all products', function () {
        Product::factory()->count(5)->create();

        $response = $this->getJson('/api/admin/products');

        $this->assertApiResponse($response, 200, [
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

    it('can create a new product', function () {
        $productData = [
            'title' => 'New Digital Product',
            'description' => 'A great digital product',
            'type' => 'digital',
            'is_active' => true,
            'meta' => [
                'download_url' => 'https://example.com/download',
                'file_size' => '100MB',
            ],
        ];

        $response = $this->postJson('/api/admin/products', $productData);

        $this->assertApiResponse($response, 201, [
            'data' => [
                'id',
                'title',
                'description',
                'type',
                'is_active',
                'meta',
                'created_at',
            ],
        ]);

        $this->assertDatabaseHas('products', [
            'title' => 'New Digital Product',
            'type' => 'digital',
            'is_active' => true,
        ]);
    });

    it('can update a product', function () {
        $product = Product::factory()->create();

        $updateData = [
            'title' => 'Updated Product Title',
            'description' => 'Updated description',
            'is_active' => false,
        ];

        $response = $this->putJson("/api/admin/products/{$product->id}", $updateData);

        $this->assertApiResponse($response, 200, [
            'data' => [
                'id',
                'title',
                'description',
                'is_active',
                'updated_at',
            ],
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => 'Updated Product Title',
            'is_active' => false,
        ]);
    });

    it('can delete a product', function () {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/admin/products/{$product->id}");

        $this->assertApiResponse($response, 204);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    });

    it('requires authentication for product management', function () {
        // Make requests without API key headers
        $this->flushHeaders();

        $response = $this->getJson('/api/admin/products');
        $response->assertUnauthorized();

        $response = $this->postJson('/api/admin/products', []);
        $response->assertUnauthorized();
    });

    it('validates product creation data', function () {
        $response = $this->postJson('/api/admin/products', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'type']);
    });
});

describe('Admin Product Price Management', function () {
    it('can create product price', function () {
        $product = Product::factory()->create();

        $priceData = [
            'amount' => 2999, // $29.99
            'currency' => 'USD',
            'billing_period' => 'once', // One-time payment
            'is_active' => true,
        ];

        $response = $this->postJson("/api/admin/products/{$product->id}/prices", $priceData);

        $this->assertApiResponse($response, 201);
        expect($response->json('data'))->toHaveKeys(['id', 'product_id', 'amount', 'currency', 'billing_period', 'is_active']);

        $this->assertDatabaseHas('product_prices', [
            'product_id' => $product->id,
            'amount' => 2999,
            'currency' => 'USD',
        ]);
    });

    it('can update product price', function () {
        $price = ProductPrice::factory()->create();

        $updateData = [
            'amount' => 3999, // $39.99
            'is_active' => false,
        ];

        $response = $this->putJson("/api/admin/products/{$price->product_id}/prices/{$price->id}", $updateData);

        $this->assertApiResponse($response, 200);
        expect($response->json('data'))->toHaveKeys(['id', 'amount', 'is_active']);

        $this->assertDatabaseHas('product_prices', [
            'id' => $price->id,
            'amount' => 3999,
            'is_active' => false,
        ]);
    });

    it('can delete product price', function () {
        $price = ProductPrice::factory()->create();

        $response = $this->deleteJson("/api/admin/products/{$price->product_id}/prices/{$price->id}");

        $response->assertStatus(200);
        expect($response->json('message'))->toBe('Price deleted successfully');
        $this->assertSoftDeleted('product_prices', ['id' => $price->id]);
    });

    it('validates price creation data', function () {
        $product = Product::factory()->create();
        $response = $this->postJson("/api/admin/products/{$product->id}/prices", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['amount', 'currency', 'billing_period']);
    });
});

describe('Admin Order Management', function () {
    it('can view all orders', function () {
        $orders = Order::factory()->count(5)->create();

        $response = $this->getJson('/api/admin/orders');

        $this->assertApiResponse($response, 200, [
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'product_id',
                    'status',
                    'total',
                    'created_at',
                ],
            ],
        ]);

        expect($response->json('data'))->toHaveCount(5);
    });

    it('can view specific order', function () {
        $order = Order::factory()->create();

        $response = $this->getJson("/api/admin/orders/{$order->id}");

        $this->assertApiResponse($response, 200, [
            'data' => [
                'id',
                'order_number',
                'user_id',
                'product_id',
                'status',
                'total',
                'billing_email',
                'billing_name',
                'created_at',
                'updated_at',
            ],
        ]);
    });

    it('can filter orders by status', function () {
        Order::factory()->create(['status' => 'completed']);
        Order::factory()->create(['status' => 'pending']);
        Order::factory()->create(['status' => 'completed']);

        $response = $this->getJson('/api/admin/orders?filter[status]=completed');

        $this->assertApiResponse($response, 200);
        expect($response->json('data'))->toHaveCount(2);
    });

    it('can search orders by email', function () {
        Order::factory()->create(['billing_email' => 'test@example.com']);
        Order::factory()->create(['billing_email' => 'other@example.com']);

        $response = $this->getJson('/api/admin/orders?search=test@example.com');

        $this->assertApiResponse($response, 200);
        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.billing_email'))->toBe('test@example.com');
    });
});

describe('Admin Settings Management', function () {
    it('can view all settings', function () {
        $response = $this->getJson('/api/admin/settings');

        $response->assertStatus(200);
        expect($response->json('data'))->toBeArray();
    });

    it('can update settings', function () {
        $response = $this->putJson('/api/admin/settings', [
            'settings' => [
                ['key' => 'app.name', 'value' => 'Updated PayCan', 'type' => 'string'],
            ],
        ]);

        $response->assertStatus(200);
        expect(settings('app.name'))->toBe('Updated PayCan');
    });

    it('rejects settings payload without settings array', function () {
        $response = $this->putJson('/api/admin/settings', [
            'invalid_key' => 'value',
        ]);

        $response->assertStatus(422);
    });
});

describe('Admin Transaction Management', function () {
    it('can view all transactions', function () {
        $transactions = \App\Models\Transaction::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/transactions');

        $this->assertApiResponse($response, 200, [
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'amount',
                    'currency',
                    'status',
                    'gateway',
                    'created_at',
                ],
            ],
        ]);

        expect($response->json('data'))->toHaveCount(3);
    });

    it('can view specific transaction', function () {
        $transaction = \App\Models\Transaction::factory()->create();

        $response = $this->getJson("/api/admin/transactions/{$transaction->id}");

        $this->assertApiResponse($response, 200, [
            'data' => [
                'id',
                'type',
                'amount',
                'currency',
                'status',
                'gateway',
                'gateway_transaction_id',
                'processed_at',
                'created_at',
            ],
        ]);
    });

    it('can filter transactions by gateway', function () {
        \App\Models\Transaction::factory()->create(['gateway' => 'stripe']);
        \App\Models\Transaction::factory()->create(['gateway' => 'paypal']);
        \App\Models\Transaction::factory()->create(['gateway' => 'stripe']);

        $response = $this->getJson('/api/admin/transactions?filter[gateway]=stripe');

        $this->assertApiResponse($response, 200);
        expect($response->json('data'))->toHaveCount(2);
    });
});
