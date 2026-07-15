<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\ProductPrice;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        $productPrice = ProductPrice::factory()->monthly()->create();

        return [
            'user_id' => User::factory(),
            'product_id' => $productPrice->product_id,
            'product_price_id' => $productPrice->id,
            'order_id' => Order::factory(),
            'title' => fake()->words(3, true),
            'status' => fake()->randomElement(Subscription::getStatuses()),
            'gateway' => fake()->randomElement(['stripe', 'paypal']),
            'gateway_subscription_id' => fake()->regexify('[A-Za-z0-9]{20}'),
            'gateway_status' => 'active',
            'gateway_data' => [
                'customer_id' => fake()->regexify('[A-Za-z0-9]{18}'),
                'latest_invoice' => fake()->regexify('[A-Za-z0-9]{27}'),
            ],
            'trial_ends_at' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'ends_at' => null,
            'next_billing_date' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'canceled_at' => null,
        ];
    }

    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'canceled_at' => null,
            'ends_at' => null,
        ]);
    }

    public function trialing()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'trialing',
            'trial_ends_at' => fake()->dateTimeBetween('+1 day', '+30 days'),
        ]);
    }

    public function canceled()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'canceled',
            'canceled_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'ends_at' => fake()->dateTimeBetween('now', '+30 days'),
        ]);
    }

    public function pastDue()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'past_due',
        ]);
    }

    public function withTrial(int $days = 14)
    {
        return $this->state(fn (array $attributes) => [
            'trial_ends_at' => now()->addDays($days),
            'status' => 'trialing',
        ]);
    }
}
