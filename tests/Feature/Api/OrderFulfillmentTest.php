<?php

use App\Models\Fulfillment;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Tests\Feature\BaseApiTest;

class OrderFulfillmentTest extends BaseApiTest
{
    public function test_can_access_order_downloads_when_fulfilled()
    {
        $user = $this->authenticateUser();
        $product = Product::factory()->create(['type' => 'digital']);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => 'completed',
        ]);

        Fulfillment::factory()->create([
            'order_id' => $order->id,
            'type' => 'download',
            'status' => 'completed',
            'meta' => [
                'download_url' => 'https://example.com/download/file.zip',
                'filename' => 'Product File.zip',
            ],
        ]);

        $response = $this->getJson("/api/user/orders/{$order->id}/downloads");

        $this->assertApiResponse($response, 200, [
            'order_id',
            'downloads' => [
                '*' => [
                    'product_id',
                    'product_title',
                    'download_url',
                ],
            ],
        ]);

        $downloads = $response->json('downloads');
        $this->assertCount(1, $downloads);
        $this->assertEquals('https://example.com/download/file.zip', $downloads[0]['download_url']);
    }

    public function test_can_access_order_licenses_when_fulfilled()
    {
        $user = $this->authenticateUser();
        $product = Product::factory()->create(['type' => 'digital']);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => 'completed',
        ]);

        Fulfillment::factory()->create([
            'order_id' => $order->id,
            'type' => 'license',
            'status' => 'completed',
            'meta' => [
                'license_key' => 'ABC123-DEF456-GHI789',
                'activation_limit' => 5,
                'activations_used' => 0,
            ],
        ]);

        $response = $this->getJson("/api/user/orders/{$order->id}/licenses");

        $this->assertApiResponse($response, 200, [
            'order_id',
            'licenses' => [
                '*' => [
                    'product_id',
                    'product_title',
                    'license_key',
                ],
            ],
        ]);

        $licenses = $response->json('licenses');
        $this->assertCount(1, $licenses);
        $this->assertEquals('ABC123-DEF456-GHI789', $licenses[0]['license_key']);
    }

    public function test_pending_order_has_no_downloads()
    {
        $user = $this->authenticateUser();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $response = $this->getJson("/api/user/orders/{$order->id}/downloads");

        // Fulfillments are only created after payment, so a pending order has none
        $this->assertApiResponse($response, 200);
        $this->assertEmpty($response->json('downloads'));
    }

    public function test_cannot_access_other_users_order_downloads()
    {
        $user = $this->authenticateUser();
        $otherUser = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'completed',
        ]);

        $response = $this->getJson("/api/user/orders/{$order->id}/downloads");

        $this->assertApiResponse($response, 404);
    }

    public function test_empty_downloads_for_order_without_fulfillments()
    {
        $user = $this->authenticateUser();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
        ]);

        $response = $this->getJson("/api/user/orders/{$order->id}/downloads");

        $this->assertApiResponse($response, 200);
        $this->assertEmpty($response->json('downloads'));
    }

    public function test_order_with_multiple_fulfillment_types()
    {
        $user = $this->authenticateUser();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
        ]);

        // Create multiple fulfillments
        Fulfillment::factory()->create([
            'order_id' => $order->id,
            'type' => 'download',
            'status' => 'completed',
            'meta' => ['download_url' => 'https://example.com/file1.zip'],
        ]);

        Fulfillment::factory()->create([
            'order_id' => $order->id,
            'type' => 'license',
            'status' => 'completed',
            'meta' => ['license_key' => 'LICENSE-123'],
        ]);

        Fulfillment::factory()->create([
            'order_id' => $order->id,
            'type' => 'download',
            'status' => 'completed',
            'meta' => ['download_url' => 'https://example.com/file2.zip'],
        ]);

        // Test downloads endpoint
        $response = $this->getJson("/api/user/orders/{$order->id}/downloads");
        $this->assertApiResponse($response, 200);
        $this->assertCount(2, $response->json('downloads'));

        // Test licenses endpoint
        $response = $this->getJson("/api/user/orders/{$order->id}/licenses");
        $this->assertApiResponse($response, 200);
        $this->assertCount(1, $response->json('licenses'));
    }
}
