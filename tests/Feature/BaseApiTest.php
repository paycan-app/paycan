<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class BaseApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set test environment configurations
        config([
            'app.api_secret_key' => 'test_admin_key_for_testing',
            'services.stripe.secret' => 'sk_test_stripe_key',
            'services.stripe.public' => 'pk_test_stripe_key',
            'services.stripe.webhook_secret' => 'whsec_test_stripe_webhook_secret',
            'services.paypal.client_id' => 'test_paypal_client_id',
            'services.paypal.client_secret' => 'test_paypal_secret',
            'services.paypal.webhook_id' => 'test_paypal_webhook_id',
            'mail.default' => 'array', // Prevent actual emails
            'queue.default' => 'sync', // Run jobs synchronously
        ]);

        // Use RefreshDatabase trait instead of migrate:fresh to avoid VACUUM issues
        // The RefreshDatabase trait handles SQLite properly

        // Mock payment gateways to avoid real API calls
        $this->mockPaymentGateways();

        // Seed essential test data
        $this->seedTestData();
    }

    protected function seedTestData(): void
    {
        // Create default settings
        \App\Models\Setting::create(['group' => 'app', 'key' => 'app_name', 'value' => 'PayCan Test']);
        \App\Models\Setting::create(['group' => 'app', 'key' => 'api_key', 'value' => 'test_admin_key_for_testing']);
        \App\Models\Setting::create(['group' => 'payment', 'key' => 'stripe_public_key', 'value' => 'pk_test_123']);
        \App\Models\Setting::create(['group' => 'payment', 'key' => 'paypal_client_id', 'value' => 'test_client_id']);
        // Enable gateways for tests so PaymentGatewayRegistry::enabled() returns them
        \App\Models\Setting::create(['group' => 'stripe', 'key' => 'enabled', 'value' => '1', 'type' => 'boolean', 'is_public' => false]);
        \App\Models\Setting::create(['group' => 'paypal', 'key' => 'enabled', 'value' => '1', 'type' => 'boolean', 'is_public' => false]);

        // Note: Products and prices are not seeded here by default
        // Tests should create their own test data as needed
    }

    protected function seedProductsAndPrices(): void
    {
        // Create test products and prices - call this method in tests that need it
        $digitalProduct = \App\Models\Product::factory()->create([
            'title' => 'Test Digital Product',
            'type' => 'digital',
            'is_active' => true,
        ]);

        $subscriptionProduct = \App\Models\Product::factory()->create([
            'title' => 'Test Subscription Product',
            'type' => 'subscription',
            'is_active' => true,
        ]);

        // Create product prices
        \App\Models\ProductPrice::factory()->create([
            'product_id' => $digitalProduct->id,
            'amount' => 2999, // $29.99
            'currency' => 'USD',
            'is_active' => true,
        ]);

        \App\Models\ProductPrice::factory()->create([
            'product_id' => $subscriptionProduct->id,
            'amount' => 999, // $9.99
            'currency' => 'USD',
            'billing_period' => 'monthly',
            'is_active' => true,
        ]);
    }

    protected function authenticateUser($user = null): User
    {
        $user = $user ?: User::factory()->create();
        Sanctum::actingAs($user);

        return $user;
    }

    protected function createGuestUser(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
        ];
    }

    protected function mockPaymentGateways(): void
    {
        // Mock Stripe Gateway
        $this->mock(\App\Services\Payment\StripeGateway::class, function ($mock) {
            $mock->shouldIgnoreMissing();

            // Mock checkout session creation
            $mock->shouldReceive('createCheckoutSession')
                ->andReturn([
                    'success' => true,
                    'id' => 'cs_test_'.uniqid(),
                    'url' => 'https://checkout.stripe.com/pay/cs_test_'.uniqid(),
                ]);

            // Mock subscription creation
            $mock->shouldReceive('createSubscription')
                ->andReturn([
                    'success' => true,
                    'id' => 'sub_'.uniqid(),
                    'status' => 'active',
                ]);

            // Mock subscription cancellation
            $mock->shouldReceive('cancelSubscription')
                ->andReturn([
                    'success' => true,
                    'status' => 'canceled',
                    'current_period_end' => now()->addMonth()->timestamp,
                    'canceled_at' => now()->timestamp,
                ]);

            // Mock subscription resumption
            $mock->shouldReceive('resumeSubscription')
                ->andReturn([
                    'success' => true,
                    'status' => 'active',
                ]);

            // Mock subscription plan change
            $mock->shouldReceive('changeSubscriptionPlan')
                ->andReturn([
                    'success' => true,
                    'subscription_id' => 'sub_'.uniqid(),
                ]);

            // Mock Stripe price resolution
            $mock->shouldReceive('getOrCreateStripePrice')
                ->andReturn('price_'.str_pad(uniqid(), 24, '0'));

            // Mock customer portal session
            $mock->makePartial()
                ->shouldReceive('createCustomerPortalSession')
                ->withAnyArgs()
                ->andReturn([
                    'success' => true,
                    'url' => 'https://billing.stripe.com/session/'.uniqid(),
                    'id' => 'bps_'.uniqid(),
                ]);

            // Mock webhook handling
            $mock->shouldReceive('handleWebhook')
                ->andReturn([
                    'success' => true,
                    'message' => 'Webhook processed',
                ]);
        });

        // Mock PayPal Gateway
        $this->mock(\App\Services\Payment\PayPalGateway::class, function ($mock) {
            // Mock checkout session creation
            $mock->shouldReceive('createCheckoutSession')
                ->andReturn([
                    'success' => true,
                    'id' => 'PAYPAL'.uniqid(),
                    'url' => 'https://www.paypal.com/checkoutnow?token=PAYPAL'.uniqid(),
                ]);

            // Mock subscription creation
            $mock->shouldReceive('createSubscription')
                ->andReturn([
                    'success' => true,
                    'id' => 'I-'.uniqid(),
                    'status' => 'APPROVAL_PENDING',
                    'url' => 'https://www.paypal.com/webapps/billing/subscriptions?ba_token=BA-'.uniqid(),
                ]);

            // Mock subscription cancellation (suspend at period end)
            $mock->shouldReceive('cancelSubscription')
                ->andReturn([
                    'success' => true,
                    'gateway_status' => 'SUSPENDED',
                    'current_period_end' => now()->addMonth()->toIso8601String(),
                    'canceled_at' => now()->timestamp,
                ]);

            // Mock subscription resumption
            $mock->shouldReceive('resumeSubscription')
                ->andReturn([
                    'success' => true,
                    'status' => 'ACTIVE',
                ]);

            // Mock subscription plan change
            $mock->shouldReceive('changeSubscriptionPlan')
                ->andReturn([
                    'success' => true,
                    'subscription_id' => 'I-'.uniqid(),
                    'approval_url' => 'https://www.paypal.com/webapps/billing/subscriptions/update?ba_token=BA-'.uniqid(),
                ]);

            // Mock customer portal session
            $mock->shouldReceive('createCustomerPortalSession')
                ->andReturn([
                    'success' => true,
                    'url' => 'https://www.paypal.com/myaccount/autopay/',
                ]);

            // Mock webhook handling
            $mock->shouldReceive('handleWebhook')
                ->andReturn([
                    'success' => true,
                    'message' => 'Webhook processed',
                ]);
        });
    }

    /**
     * Post a Stripe webhook with a valid signature computed from the configured secret.
     */
    protected function postStripeWebhook(array $payload)
    {
        $json = json_encode($payload);
        $timestamp = time();
        $secret = config('services.stripe.webhook_secret');
        $signature = hash_hmac('sha256', "{$timestamp}.{$json}", $secret);

        return $this->call(
            'POST',
            '/api/webhooks/stripe',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
            ],
            $json
        );
    }

    // Helper methods for API testing
    protected function assertApiResponse($response, int $expectedStatus): void
    {
        $response->assertStatus($expectedStatus);
    }

    protected function assertApiSuccess($response, $expectedStatus = 200): void
    {
        $response->assertStatus($expectedStatus);
        $response->assertJsonStructure(['success', 'data']);
        $response->assertJson(['success' => true]);
    }

    protected function assertApiError($response, $expectedStatus = 400): void
    {
        $response->assertStatus($expectedStatus);
        $response->assertJsonStructure(['success', 'message']);
        $response->assertJson(['success' => false]);
    }

    protected function assertApiValidationError($response, $field = null): void
    {
        $response->assertStatus(422);
        $response->assertJsonStructure(['success', 'message', 'errors']);
        $response->assertJson(['success' => false]);

        if ($field) {
            $response->assertJsonValidationErrors($field);
        }
    }

    protected function assertHasErrorKey($response, $key): void
    {
        $data = $response->json();
        $this->assertArrayHasKey($key, $data['errors'] ?? []);
    }

    // Database assertion helpers
    protected function assertDatabaseHasOrder($attributes = []): void
    {
        $this->assertDatabaseHas('orders', $attributes);
    }

    protected function assertDatabaseHasSubscription($attributes = []): void
    {
        $this->assertDatabaseHas('subscriptions', $attributes);
    }

    protected function assertDatabaseHasTransaction($attributes = []): void
    {
        $this->assertDatabaseHas('transactions', $attributes);
    }

    protected function assertDatabaseHasFulfillment($attributes = []): void
    {
        $this->assertDatabaseHas('fulfillments', $attributes);
    }
}
