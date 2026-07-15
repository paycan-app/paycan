<?php

namespace Database\Seeders;

use App\Models\Fulfillment;
use App\Models\Order;
use Illuminate\Database\Seeder;

class FulfillmentSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            // Create 1-2 fulfillments per order
            $fulfillmentCount = fake()->numberBetween(1, 2);

            Fulfillment::factory()
                ->count($fulfillmentCount)
                ->for($order)
                ->create();
        }

        // Create some specific fulfillment scenarios
        $completedOrders = Order::where('status', 'completed')->limit(5)->get();
        foreach ($completedOrders as $order) {
            Fulfillment::factory()
                ->completed()
                ->physical()
                ->for($order)
                ->create();
        }

        $pendingOrders = Order::where('status', 'pending')->limit(3)->get();
        foreach ($pendingOrders as $order) {
            Fulfillment::factory()
                ->pending()
                ->digital()
                ->for($order)
                ->create();
        }
    }
}
