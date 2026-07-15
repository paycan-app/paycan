<?php

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Subscription;
use App\Models\User;
use Tests\Feature\BaseApiTest;

class UserSubscriptionLifecycleTest extends BaseApiTest
{
    public function test_can_create_subscription_with_valid_data()
    {
        $user = $this->authenticateUser();
        $product = Product::factory()->create(['type' => 'subscription', 'is_active' => true]);
        $price = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'billing_period' => 'monthly',
            'amount' => 29.99,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/user/subscriptions', [
            'product_price_id' => $price->id,
            'gateway' => 'stripe',
        ]);

        $this->assertApiResponse($response, 201, [
            'subscription' => [
                'id',
                'product_id',
                'product_price_id',
                'status',
                'gateway',
            ],
            'checkout_url',
        ]);

        // Subscription starts incomplete; the webhook activates it after payment
        $this->assertDatabaseHasSubscription([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'product_price_id' => $price->id,
            'status' => 'incomplete',
        ]);
    }

    public function test_can_cancel_active_subscription()
    {
        $user = $this->authenticateUser();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'gateway' => 'stripe',
            'gateway_subscription_id' => 'sub_test_123',
        ]);

        $response = $this->postJson("/api/user/subscriptions/{$subscription->id}/cancel");

        $this->assertApiResponse($response, 200, [
            'subscription' => [
                'id',
                'status',
                'canceled_at',
            ],
        ]);

        $subscription->refresh();
        $this->assertEquals('canceled', $subscription->status);
        $this->assertNotNull($subscription->canceled_at);
    }

    public function test_cancel_incomplete_subscription_is_local_only()
    {
        $user = $this->authenticateUser();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'incomplete',
            'gateway' => 'stripe',
            'gateway_subscription_id' => null,
        ]);

        $response = $this->postJson("/api/user/subscriptions/{$subscription->id}/cancel");

        $this->assertApiResponse($response, 200);

        $subscription->refresh();
        $this->assertEquals('canceled', $subscription->status);
    }

    public function test_can_resume_canceled_subscription()
    {
        $user = $this->authenticateUser();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'canceled',
            'gateway' => 'stripe',
            'gateway_subscription_id' => 'sub_test_123',
            'canceled_at' => now()->subDays(1),
            'ends_at' => now()->addWeeks(2),
        ]);

        $response = $this->postJson("/api/user/subscriptions/{$subscription->id}/resume");

        $this->assertApiResponse($response, 200, [
            'subscription' => [
                'id',
                'status',
            ],
        ]);

        $subscription->refresh();
        $this->assertEquals('active', $subscription->status);
        $this->assertNull($subscription->canceled_at);
    }

    public function test_cannot_resume_expired_subscription()
    {
        $user = $this->authenticateUser();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'canceled',
            'gateway' => 'stripe',
            'gateway_subscription_id' => 'sub_test_123',
            'canceled_at' => now()->subMonth(),
            'ends_at' => now()->subDays(1),
        ]);

        $response = $this->postJson("/api/user/subscriptions/{$subscription->id}/resume");

        $this->assertApiResponse($response, 422);
    }

    public function test_can_change_subscription_plan()
    {
        $user = $this->authenticateUser();
        $product = Product::factory()->create(['type' => 'subscription', 'is_active' => true]);
        $oldPrice = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'billing_period' => 'monthly',
            'amount' => 29.99,
            'is_active' => true,
        ]);
        $newPrice = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'billing_period' => 'monthly',
            'amount' => 49.99,
            'is_active' => true,
        ]);

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'product_price_id' => $oldPrice->id,
            'status' => 'active',
            'gateway' => 'stripe',
            'gateway_subscription_id' => 'sub_test_123',
        ]);

        $response = $this->postJson("/api/user/subscriptions/{$subscription->id}/change", [
            'product_price_id' => $newPrice->id,
        ]);

        $this->assertApiResponse($response, 200, [
            'subscription' => [
                'id',
                'product_price_id',
                'status',
            ],
        ]);

        $subscription->refresh();
        $this->assertEquals($newPrice->id, $subscription->product_price_id);
    }

    public function test_cannot_cancel_already_canceled_subscription()
    {
        $user = $this->authenticateUser();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'canceled',
            'canceled_at' => now()->subDays(1),
        ]);

        $response = $this->postJson("/api/user/subscriptions/{$subscription->id}/cancel");

        $this->assertApiResponse($response, 422);
        $this->assertStringContainsString('already canceled', $response->json('error'));
    }

    public function test_cannot_access_other_users_subscription()
    {
        $user = $this->authenticateUser();
        $otherUser = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->postJson("/api/user/subscriptions/{$subscription->id}/cancel");

        $this->assertApiResponse($response, 404);
    }

    public function test_subscription_validation_errors()
    {
        $user = $this->authenticateUser();

        // Test missing required fields
        $response = $this->postJson('/api/user/subscriptions', []);

        $this->assertApiResponse($response, 422);
        $this->assertArrayHasKey('errors', $response->json());

        // Test invalid product price and gateway
        $response = $this->postJson('/api/user/subscriptions', [
            'product_price_id' => 999999,
            'gateway' => 'invalid_gateway',
        ]);

        $this->assertApiResponse($response, 422);
        $errors = $response->json('errors');
        $this->assertArrayHasKey('product_price_id', $errors);
        $this->assertArrayHasKey('gateway', $errors);
    }
}
