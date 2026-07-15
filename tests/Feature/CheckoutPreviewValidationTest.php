<?php

use App\Models\Product;
use App\Models\ProductPrice;
use Tests\Feature\BaseApiTest;

class CheckoutPreviewValidationTest extends BaseApiTest
{
    public function test_returns_payment_methods_for_subscription_products_including_paypal()
    {
        $user = $this->authenticateUser();

        $product = Product::factory()->create([
            'type' => 'subscription',
            'is_active' => true,
        ]);

        $price = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'billing_period' => 'month',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/user/checkout/preview?product_price_id='.$price->id);

        $response->assertOk();
        $response->assertJsonStructure([
            'product',
            'selected_price',
            'prices',
            'payment_methods' => [
                '*' => [
                    'key',
                    'name',
                    'supports_subscriptions',
                ],
            ],
        ]);

        // Check that PayPal is included in payment methods for subscriptions
        $paymentMethods = $response->json('payment_methods');
        $paypalMethod = collect($paymentMethods)->firstWhere('key', 'paypal');

        $this->assertNotNull($paypalMethod, 'PayPal should be available for subscription products');
        $this->assertTrue($paypalMethod['supports_subscriptions'], 'PayPal should support subscriptions');
    }

    public function test_returns_payment_methods_for_one_time_payment_products()
    {
        $user = $this->authenticateUser();

        $product = Product::factory()->create([
            'type' => 'digital',
            'is_active' => true,
        ]);

        $price = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'billing_period' => 'once',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/user/checkout/preview?product_price_id='.$price->id);

        $response->assertOk();

        // Check that both Stripe and PayPal are available for one-time payments
        $paymentMethods = $response->json('payment_methods');
        $this->assertCount(2, $paymentMethods, 'Both Stripe and PayPal should be available');
    }

    public function test_validates_product_id_or_product_price_id_is_required()
    {
        $user = $this->authenticateUser();

        $response = $this->actingAs($user)
            ->getJson('/api/user/checkout/preview');

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'error' => 'Either product_id or product_price_id is required.',
        ]);
    }

    public function test_returns_404_for_inactive_products()
    {
        $user = $this->authenticateUser();

        $product = Product::factory()->create([
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/user/checkout/preview?product_id='.$product->id);

        $response->assertNotFound();
    }
}
