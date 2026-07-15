<?php

use App\Models\Product;
use App\Models\ProductPrice;
use Tests\Feature\BaseApiTest;

class CheckoutPreviewTotalsTest extends BaseApiTest
{
    public function test_preview_and_checkout_totals_match()
    {
        $user = $this->authenticateUser();

        // Create product and price
        $product = Product::factory()->create([
            'type' => 'digital',
            'is_active' => true,
            'title' => 'Totals Match Product',
        ]);

        $price = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'amount' => 50.00,
            'currency' => 'USD',
            'billing_period' => 'once',
            'is_active' => true,
            'title' => 'One-time $50',
        ]);

        // Preview totals
        $query = http_build_query([
            'product_price_id' => $price->id,
            'quantity' => 2,
        ]);

        $previewResponse = $this->getJson("/api/user/checkout/preview?{$query}");
        $this->assertApiResponse($previewResponse, 200);

        $selected = $previewResponse->json('selected_price');
        $this->assertNotEmpty($selected, 'Preview selected_price should be present');

        // Tax is handled by the payment gateway, so previews are pre-tax
        $this->assertEquals(100.00, (float) $selected['subtotal']);
        $this->assertEquals(100.00, (float) $selected['final_price']);

        $expectedTotal = (float) $selected['final_price'];

        // Create checkout with the same inputs
        $checkoutData = [
            'product_id' => $product->id,
            'product_price_id' => $price->id,
            'quantity' => 2,
            'billing_email' => $user->email,
            'billing_name' => $user->name,
            'gateway' => 'stripe',
        ];

        $createResponse = $this->postJson('/api/user/checkout', $checkoutData);
        $this->assertApiResponse($createResponse, 201);

        $orderId = $createResponse->json('checkout.order_id');
        $order = \App\Models\Order::findOrFail($orderId);

        // Assert totals match
        $this->assertEquals($expectedTotal, (float) $order->total, 'Order total should match preview final_price');
        $this->assertEquals(0.0, (float) $order->tax, 'Tax is collected by the gateway, not recorded locally');
        $this->assertEquals(2, $order->quantity, 'Order quantity should match preview quantity');
        $this->assertEquals('USD', $order->currency);
    }
}
