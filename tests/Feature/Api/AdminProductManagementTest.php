<?php

use App\Models\Product;
use App\Models\ProductPrice;
use Tests\Feature\BaseApiTest;

class AdminProductManagementTest extends BaseApiTest
{
    protected function authenticateAdmin(): array
    {
        return ['X-API-Key' => 'test_admin_key_for_testing'];
    }

    public function test_admin_can_create_product()
    {
        $productData = [
            'title' => 'Test Product',
            'description' => 'A test product description',
            'type' => 'digital',
            'is_active' => true,
            'meta' => [
                'features' => ['Feature 1', 'Feature 2'],
                'category' => 'Software',
            ],
        ];

        $response = $this->postJson('/api/admin/products', $productData, $this->authenticateAdmin());

        $this->assertApiResponse($response, 201, [
            'product' => [
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
            'title' => 'Test Product',
            'type' => 'digital',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_update_product()
    {
        $product = Product::factory()->create([
            'title' => 'Original Title',
            'is_active' => true,
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'is_active' => false,
        ];

        $response = $this->putJson("/api/admin/products/{$product->id}", $updateData, $this->authenticateAdmin());

        $this->assertApiResponse($response, 200, [
            'product' => [
                'id',
                'title',
                'description',
                'is_active',
            ],
        ]);

        $product->refresh();
        $this->assertEquals('Updated Title', $product->title);
        $this->assertFalse($product->is_active);
    }

    public function test_admin_can_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/admin/products/{$product->id}", [], $this->authenticateAdmin());

        $this->assertApiResponse($response, 204);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_admin_can_create_product_price()
    {
        $product = Product::factory()->create();

        $priceData = [
            'amount' => 29.99,
            'currency' => 'USD',
            'billing_period' => 'once',
            'is_active' => true,
        ];

        $response = $this->postJson("/api/admin/products/{$product->id}/prices", $priceData, $this->authenticateAdmin());

        $this->assertApiResponse($response, 201, [
            'data' => [
                'id',
                'product_id',
                'amount',
                'currency',
                'is_active',
            ],
        ]);

        $this->assertDatabaseHas('product_prices', [
            'product_id' => $product->id,
            'amount' => 29.99,
            'billing_period' => 'once',
        ]);
    }

    public function test_admin_can_update_product_price()
    {
        $product = Product::factory()->create();
        $price = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'amount' => 19.99,
        ]);

        $updateData = [
            'amount' => 39.99,
            'is_active' => false,
        ];

        $response = $this->putJson("/api/admin/products/{$product->id}/prices/{$price->id}", $updateData, $this->authenticateAdmin());

        $this->assertApiResponse($response, 200);

        $price->refresh();
        $this->assertEquals(39.99, $price->amount);
        $this->assertFalse($price->is_active);
    }

    public function test_admin_can_delete_product_price()
    {
        $product = Product::factory()->create();
        $price = ProductPrice::factory()->create(['product_id' => $product->id]);

        $response = $this->deleteJson("/api/admin/products/{$product->id}/prices/{$price->id}", [], $this->authenticateAdmin());

        $this->assertApiResponse($response, 200);
        $this->assertNull(\App\Models\ProductPrice::find($price->id));
    }

    public function test_unauthorized_access_without_api_key()
    {
        $response = $this->getJson('/api/admin/products');

        $this->assertApiResponse($response, 401);
    }

    public function test_unauthorized_access_with_invalid_api_key()
    {
        $response = $this->getJson('/api/admin/products', ['Authorization' => 'Bearer invalid_key']);

        $this->assertApiResponse($response, 401);
    }

    public function test_admin_product_validation_errors()
    {
        // Test missing required fields
        $response = $this->postJson('/api/admin/products', [], $this->authenticateAdmin());

        $this->assertApiResponse($response, 422);
        $this->assertArrayHasKey('errors', $response->json());

        // Test invalid product type
        $response = $this->postJson('/api/admin/products', [
            'title' => 'Test Product',
            'type' => 'invalid_type',
        ], $this->authenticateAdmin());

        $this->assertApiResponse($response, 422);
        $this->assertArrayHasKey('type', $response->json('errors'));
    }

    public function test_admin_can_view_all_orders()
    {
        $orders = \App\Models\Order::factory()->count(5)->create();

        $response = $this->getJson('/api/admin/orders', $this->authenticateAdmin());

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
            'current_page',
            'per_page',
            'total',
        ]);

        $this->assertCount(5, $response->json('data'));
    }
}
