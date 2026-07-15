<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $productPrice = ProductPrice::factory()->create();

        return [
            'user_id' => User::factory(),
            'product_id' => $productPrice->product_id,
            'product_price_id' => $productPrice->id,
            'order_number' => 'ORD-'.fake()->unique()->numerify('########'),
            'status' => fake()->randomElement(Order::getStatuses()),
            'total' => fake()->randomFloat(2, 9.99, 999.99),
            'currency' => 'USD',
            'tax' => fake()->randomFloat(2, 0, 50),
            'billing_email' => fake()->safeEmail(),
            'billing_name' => fake()->name(),
            'billing_address' => fake()->streetAddress(),
            'billing_city' => fake()->city(),
            'billing_state' => fake()->state(),
            'billing_zipcode' => fake()->postcode(),
            'billing_country' => fake()->countryCode(),
            'gateway' => fake()->randomElement(Order::getGateways()),
            'gateway_order_id' => fake()->regexify('[A-Za-z0-9]{20}'),
            'gateway_data' => [
                'session_id' => fake()->regexify('[A-Za-z0-9]{50}'),
                'payment_intent' => fake()->regexify('[A-Za-z0-9]{27}'),
            ],
            'customer_note' => fake()->optional()->sentence(),
            'quantity' => fake()->numberBetween(1, 5),
            'meta' => [
                'source' => fake()->randomElement(['web', 'mobile', 'api']),
                'referrer' => fake()->optional()->url(),
            ],
        ];
    }

    public function pending()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function completed()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function failed()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }

    public function forStripe()
    {
        return $this->state(fn (array $attributes) => [
            'gateway' => 'stripe',
            'gateway_order_id' => 'cs_'.fake()->regexify('[A-Za-z0-9]{50}'),
        ]);
    }

    public function forPaypal()
    {
        return $this->state(fn (array $attributes) => [
            'gateway' => 'paypal',
            'gateway_order_id' => fake()->regexify('[A-Z0-9]{17}'),
        ]);
    }
}
