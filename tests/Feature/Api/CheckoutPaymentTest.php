<?php

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Tests\Feature\BaseApiTest;

class CheckoutPaymentTest extends BaseApiTest
{
    public function test_can_create_checkout_session_for_one_time_product()
    {
        $user = $this->authenticateUser();
        $product = Product::factory()->create(['type' => 'digital', 'is_active' => true]);
        $price = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'billing_period' => 'once',
            'amount' => 29.99,
        ]);

        $checkoutData = [
            'product_id' => $product->id,
            'product_price_id' => $price->id,
            'quantity' => 1,
            'billing_email' => $user->email,
            'billing_name' => $user->name,
            'gateway' => 'stripe',
        ];

        $response = $this->postJson('/api/user/checkout', $checkoutData);

        $this->assertApiResponse($response, 201, [
            'checkout' => [
                'session_id',
                'checkout_url',
                'order_id',
            ],
        ]);

        $checkout = $response->json('checkout');
        $this->assertNotEmpty($checkout['session_id']);
        $this->assertStringContainsString('checkout.stripe.com', $checkout['checkout_url']);

        $this->assertDatabaseHas('orders', [
            'id' => $checkout['order_id'],
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => 'pending',
            'gateway' => 'stripe',
        ]);
    }

    public function test_can_create_checkout_session_for_subscription()
    {
        $user = $this->authenticateUser();
        $product = Product::factory()->create(['type' => 'subscription', 'is_active' => true]);
        $price = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'billing_period' => 'monthly',
            'amount' => 19.99,
        ]);

        $checkoutData = [
            'product_id' => $product->id,
            'product_price_id' => $price->id,
            'billing_email' => $user->email,
            'billing_name' => $user->name,
            'gateway' => 'stripe',
        ];

        $response = $this->postJson('/api/user/checkout', $checkoutData);

        $this->assertApiResponse($response, 201, [
            'checkout' => [
                'session_id',
                'checkout_url',
                'order_id',
            ],
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => 'pending',
        ]);
    }

    public function test_can_create_guest_checkout()
    {
        $product = Product::factory()->create(['type' => 'digital', 'is_active' => true]);
        $price = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'billing_period' => 'once',
            'amount' => 29.99,
        ]);

        $guestData = $this->createGuestUser();
        $checkoutData = [
            'product_id' => $product->id,
            'product_price_id' => $price->id,
            'quantity' => 1,
            'billing_email' => $guestData['email'],
            'billing_name' => $guestData['name'],
            'gateway' => 'paypal',
        ];

        $response = $this->postJson('/api/user/checkout', $checkoutData);

        $this->assertApiResponse($response, 201);

        // Should create a user for the guest
        $this->assertDatabaseHas('users', [
            'email' => $guestData['email'],
            'name' => $guestData['name'],
        ]);
    }

    public function test_checkout_validation_errors()
    {
        $user = $this->authenticateUser();

        // Test missing required fields
        $response = $this->postJson('/api/user/checkout', []);

        $this->assertApiResponse($response, 422);
        $errors = $response->json('errors');
        $this->assertArrayHasKey('product_id', $errors);
        $this->assertArrayHasKey('product_price_id', $errors);

        // Test invalid product
        $response = $this->postJson('/api/user/checkout', [
            'product_id' => 999999,
            'product_price_id' => 999999,
            'billing_email' => 'invalid-email',
            'gateway' => 'invalid_gateway',
        ]);

        $this->assertApiResponse($response, 422);
        $errors = $response->json('errors');
        $this->assertArrayHasKey('product_id', $errors);
        $this->assertArrayHasKey('billing_email', $errors);
        $this->assertArrayHasKey('gateway', $errors);
    }

    public function test_cannot_checkout_inactive_product()
    {
        $user = $this->authenticateUser();
        $product = Product::factory()->create(['is_active' => false]);
        $price = ProductPrice::factory()->create(['product_id' => $product->id]);

        $checkoutData = [
            'product_id' => $product->id,
            'product_price_id' => $price->id,
            'billing_email' => $user->email,
            'billing_name' => $user->name,
            'gateway' => 'stripe',
        ];

        $response = $this->postJson('/api/user/checkout', $checkoutData);

        $this->assertApiResponse($response, 422);
        $this->assertStringContainsString('not available', $response->json('errors.product_price_id.0'));
    }

    public function test_can_create_customer_portal_session()
    {
        $user = $this->authenticateUser();

        $response = $this->postJson('/api/user/checkout/portal', [
            'return_url' => 'https://example.com/return',
        ]);

        $this->assertApiResponse($response, 200, [
            'portal' => [
                'url',
                'session_id',
            ],
        ]);

        $portal = $response->json('portal');
        $this->assertStringContainsString('billing.stripe.com', $portal['url']);
    }

    public function test_checkout_with_different_gateways()
    {
        $user = $this->authenticateUser();
        $product = Product::factory()->create(['type' => 'digital', 'is_active' => true]);
        $price = ProductPrice::factory()->create(['product_id' => $product->id]);

        $checkoutData = [
            'product_id' => $product->id,
            'product_price_id' => $price->id,
            'billing_email' => $user->email,
            'billing_name' => $user->name,
        ];

        // Test Stripe
        $stripeResponse = $this->postJson('/api/user/checkout', array_merge($checkoutData, ['gateway' => 'stripe']));
        $this->assertApiResponse($stripeResponse, 201);
        $this->assertStringContainsString('stripe.com', $stripeResponse->json('checkout.checkout_url'));

        // Test PayPal
        $paypalResponse = $this->postJson('/api/user/checkout', array_merge($checkoutData, ['gateway' => 'paypal']));
        $this->assertApiResponse($paypalResponse, 201);
        $this->assertStringContainsString('paypal.com', $paypalResponse->json('checkout.checkout_url'));
    }

    public function test_checkout_with_quantity_and_tax_calculation()
    {
        $user = $this->authenticateUser();
        $product = Product::factory()->create(['type' => 'digital', 'is_active' => true]);
        $price = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'amount' => 10.00,
        ]);

        $checkoutData = [
            'product_id' => $product->id,
            'product_price_id' => $price->id,
            'quantity' => 3,
            'billing_email' => $user->email,
            'billing_name' => $user->name,
            'billing_country' => 'US',
            'billing_state' => 'CA',
            'gateway' => 'stripe',
        ];

        $response = $this->postJson('/api/user/checkout', $checkoutData);

        $this->assertApiResponse($response, 201);

        $order = \App\Models\Order::find($response->json('checkout.order_id'));
        $this->assertEquals(3, $order->quantity);
        $this->assertEquals(30.00, $order->total); // 3 * $10.00
    }
}
