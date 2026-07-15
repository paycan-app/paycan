<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use App\Services\Settings\SettingsManager;

class ApiValidationAndErrorTest extends BaseApiTest
{
    /**
     * Test admin user sync validation
     */
    public function test_admin_user_sync_validation()
    {
        $apiKey = 'pk_test123456789012345678901234567890';
        app(SettingsManager::class)->set('app.api_key', $apiKey, 'string', false);

        // Test missing required fields
        $response = $this->withHeader('X-API-Key', $apiKey)
            ->postJson('/api/admin/users/sync', []);
        $this->assertApiResponse($response, 422);
        $response->assertJsonValidationErrors(['user_id']);

        // Test missing user object for new user
        $response = $this->withHeader('X-API-Key', $apiKey)
            ->postJson('/api/admin/users/sync', [
                'user_id' => 'usr_test123',
            ]);
        $this->assertApiResponse($response, 422);
        $response->assertJsonValidationErrors(['user', 'user.name', 'user.email']);

        // Test invalid email format
        $response = $this->withHeader('X-API-Key', $apiKey)
            ->postJson('/api/admin/users/sync', [
                'user_id' => 'usr_test456',
                'user' => [
                    'name' => 'Test User',
                    'email' => 'invalid-email',
                ],
            ]);
        $this->assertApiResponse($response, 422);
        $response->assertJsonValidationErrors(['user.email']);

        // Test duplicate email
        User::factory()->create(['email' => 'existing@example.com']);
        $response = $this->withHeader('X-API-Key', $apiKey)
            ->postJson('/api/admin/users/sync', [
                'user_id' => 'usr_duplicate',
                'user' => [
                    'name' => 'Test User',
                    'email' => 'existing@example.com',
                ],
            ]);
        $this->assertApiResponse($response, 422);
        $response->assertJsonValidationErrors(['user.email']);
    }

    /**
     * Test authentication requirements for user routes
     */
    public function test_authentication_requirements()
    {
        // Test accessing protected user endpoints without token
        $protectedEndpoints = [
            'GET' => ['/api/user/orders', '/api/user/transactions'],
        ];

        foreach ($protectedEndpoints as $method => $endpoints) {
            foreach ($endpoints as $endpoint) {
                $response = $this->json($method, $endpoint);
                $this->assertApiResponse($response, 401);
            }
        }

        // Test with invalid token
        $headers = ['Authorization' => 'Bearer invalid_token'];
        $response = $this->getJson('/api/user/orders', $headers);
        $this->assertApiResponse($response, 401);
    }

    /**
     * Test checkout validation
     */
    public function test_checkout_validation()
    {
        $this->authenticateUser();

        // Test missing required fields
        $response = $this->postJson('/api/user/checkout', []);
        $this->assertApiResponse($response, 422);
        $response->assertJsonValidationErrors(['product_price_id', 'gateway']);

        // Test invalid product_price_id
        $response = $this->postJson('/api/user/checkout', [
            'product_price_id' => 99999,
            'gateway' => 'stripe',
        ]);
        // Validation catches this, returns 422
        $this->assertTrue(in_array($response->status(), [404, 422]));

        // Test invalid gateway
        $product = Product::factory()->create();
        $productPrice = ProductPrice::factory()->create(['product_id' => $product->id]);
        $response = $this->postJson('/api/user/checkout', [
            'product_price_id' => $productPrice->id,
            'gateway' => 'invalid_gateway',
        ]);
        $this->assertApiResponse($response, 422);
        $response->assertJsonValidationErrors(['gateway']);
    }

    /**
     * Test authorization checks
     */
    public function test_authorization_checks()
    {
        $this->authenticateUser();
        $user2 = User::factory()->create();

        // Create order for user2
        $product = Product::factory()->create();
        $productPrice = ProductPrice::factory()->create(['product_id' => $product->id]);
        $order = Order::factory()->create([
            'user_id' => $user2->id,
            'product_price_id' => $productPrice->id,
        ]);

        // User1 trying to access user2's order
        $response = $this->getJson("/api/user/orders/{$order->id}");
        $this->assertApiResponse($response, 404);
    }

    /**
     * Test admin API key requirements
     */
    public function test_admin_api_key_requirements()
    {
        // Test accessing admin endpoints without API key
        $adminEndpoints = [
            'GET' => ['/api/admin/products', '/api/admin/orders', '/api/admin/transactions'],
            'POST' => ['/api/admin/users/sync'],
        ];

        foreach ($adminEndpoints as $method => $endpoints) {
            foreach ($endpoints as $endpoint) {
                $response = $this->json($method, $endpoint);
                $this->assertApiResponse($response, 401);
                $this->assertEquals('Unauthorized', $response->json('error'));
                $this->assertEquals('API key is required', $response->json('message'));
            }
        }
    }

    /**
     * Test error response format consistency
     */
    public function test_error_response_format_consistency()
    {
        $this->authenticateUser();

        // Test various error scenarios and ensure consistent response format
        $errorScenarios = [
            ['method' => 'GET', 'url' => '/api/user/orders/99999', 'expectedStatus' => 404],
            ['method' => 'POST', 'url' => '/api/user/checkout', 'data' => [], 'expectedStatus' => 422],
            ['method' => 'GET', 'url' => '/api/user/products', 'expectedStatus' => 200],
        ];

        foreach ($errorScenarios as $scenario) {
            $response = $this->json(
                $scenario['method'],
                $scenario['url'],
                $scenario['data'] ?? []
            );

            $this->assertEquals($scenario['expectedStatus'], $response->status());

            // All error responses should have consistent structure
            if ($response->status() >= 400) {
                $this->assertTrue(
                    $response->json() !== null,
                    'Error response should be valid JSON'
                );
            }
        }
    }

    /**
     * Test large payload handling
     */
    public function test_large_payload_handling()
    {
        $apiKey = 'pk_test123456789012345678901234567890';
        app(SettingsManager::class)->set('app.api_key', $apiKey, 'string', false);

        // Create very large payload
        $largeString = str_repeat('A', 10000); // 10KB string

        $response = $this->withHeader('X-API-Key', $apiKey)
            ->postJson('/api/admin/users/sync', [
                'user_id' => 'usr_large',
                'user' => [
                    'name' => $largeString,
                    'email' => 'test@example.com',
                ],
            ]);

        // Should be rejected due to validation rules
        $this->assertApiResponse($response, 422);
        $response->assertJsonValidationErrors(['user.name']);
    }
}
