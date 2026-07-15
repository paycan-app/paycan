<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductPriceFactory extends Factory
{
    protected $model = ProductPrice::class;

    public function definition(): array
    {
        return [
            'id' => Str::ulid()->toString(),
            'product_id' => Product::factory(),
            'title' => fake()->randomElement(['Basic', 'Standard', 'Premium', 'Enterprise']),
            'slug' => fake()->slug(),
            'amount' => fake()->randomFloat(2, 9.99, 999.99),
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP']),
            'billing_period' => 'once',
            'trial_days' => 0,
            'gateway_data' => [
                'stripe' => [
                    'price_id' => 'price_'.fake()->regexify('[A-Za-z0-9]{24}'),
                ],
                'paypal' => [
                    'plan_id' => 'P-'.fake()->regexify('[A-Z0-9]{17}'),
                ],
            ],
            'is_active' => true,
        ];
    }

    public function oneTime()
    {
        return $this->state(fn (array $attributes) => [
            'billing_period' => 'once',
            'trial_days' => 0,
        ]);
    }

    public function monthly()
    {
        return $this->state(fn (array $attributes) => [
            'billing_period' => 'monthly',
            'trial_days' => fake()->randomElement([0, 7, 14, 30]),
        ]);
    }

    public function yearly()
    {
        return $this->state(fn (array $attributes) => [
            'billing_period' => 'yearly',
            'trial_days' => fake()->randomElement([0, 7, 30]),
        ]);
    }

    public function withTrial(int $days = 14)
    {
        return $this->state(fn (array $attributes) => [
            'trial_days' => $days,
        ]);
    }

    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
