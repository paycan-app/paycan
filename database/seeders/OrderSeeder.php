<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $productPrices = ProductPrice::take(5)->get();

        if (! $user || $productPrices->isEmpty()) {
            return;
        }

        $orderData = [
            [
                'user_id' => $user->id,
                'product_price_id' => $productPrices->get(0)->id,
                'order_number' => 'ORD-2025-001',
                'total' => $productPrices->get(0)->amount,
                'currency' => $productPrices->get(0)->currency,
                'status' => 'completed',
                'gateway' => 'stripe',
                'gateway_order_id' => 'pi_test_'.uniqid(),
                'quantity' => 1,
                'customer_note' => 'First test order',
                'billing_email' => $user->email,
                'billing_name' => $user->name,
                'billing_address' => '123 Test St',
                'billing_city' => 'Test City',
                'billing_state' => 'CA',
                'billing_zipcode' => '12345',
                'billing_country' => 'US',
            ],
            [
                'user_id' => $user->id,
                'product_price_id' => $productPrices->get(1)->id,
                'order_number' => 'ORD-2025-002',
                'total' => $productPrices->get(1)->amount,
                'currency' => $productPrices->get(1)->currency,
                'status' => 'pending',
                'gateway' => 'stripe',
                'gateway_order_id' => 'pi_test_'.uniqid(),
                'quantity' => 2,
                'customer_note' => 'Pending test order',
                'billing_email' => $user->email,
                'billing_name' => $user->name,
                'billing_address' => '456 Demo Ave',
                'billing_city' => 'Demo City',
                'billing_state' => 'NY',
                'billing_zipcode' => '67890',
                'billing_country' => 'US',
            ],
            [
                'user_id' => $user->id,
                'product_price_id' => $productPrices->get(2)->id,
                'order_number' => 'ORD-2025-003',
                'total' => $productPrices->get(2)->amount,
                'currency' => $productPrices->get(2)->currency,
                'status' => 'failed',
                'gateway' => 'paypal',
                'gateway_order_id' => 'paypal_test_'.uniqid(),
                'quantity' => 1,
                'customer_note' => 'Failed test order',
                'billing_email' => $user->email,
                'billing_name' => $user->name,
                'billing_address' => '789 Sample Blvd',
                'billing_city' => 'Sample City',
                'billing_state' => 'TX',
                'billing_zipcode' => '54321',
                'billing_country' => 'US',
            ],
        ];

        foreach ($orderData as $data) {
            // Get the product_id from the product_price
            $productPrice = ProductPrice::find($data['product_price_id']);
            $data['product_id'] = $productPrice->product_id;

            Order::create($data);
        }
    }
}
