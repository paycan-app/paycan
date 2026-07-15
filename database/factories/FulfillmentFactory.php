<?php

namespace Database\Factories;

use App\Models\Fulfillment;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class FulfillmentFactory extends Factory
{
    protected $model = Fulfillment::class;

    public function definition(): array
    {
        $types = ['download', 'license', 'physical'];
        $type = $this->faker->randomElement($types);

        $data = match ($type) {
            'download' => [
                'download_url' => $this->faker->url(),
                'filename' => $this->faker->word().'.zip',
                'file_size' => $this->faker->randomNumber(6).' MB',
                'expires_at' => now()->addDays(30)->toISOString(),
            ],
            'license' => [
                'license_key' => strtoupper($this->faker->bothify('????-????-????-????')),
                'activation_limit' => $this->faker->numberBetween(1, 10),
                'activations_used' => 0,
                'expires_at' => now()->addYear()->toISOString(),
            ],
            'physical' => [
                'tracking_number' => $this->faker->bothify('##??######'),
                'carrier' => $this->faker->randomElement(['UPS', 'FedEx', 'DHL', 'USPS']),
                'estimated_delivery' => now()->addDays($this->faker->numberBetween(3, 10))->toISOString(),
            ],
        };

        return [
            'order_id' => Order::factory(),
            'type' => $type,
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'meta' => $data,
            'fulfilled_at' => $type === 'fulfilled' ? now() : null,
        ];
    }

    public function fulfilled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'fulfilled_at' => now(),
        ]);
    }

    public function download(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'download',
            'meta' => [
                'download_url' => $this->faker->url(),
                'filename' => $this->faker->word().'.zip',
                'file_size' => $this->faker->randomNumber(6).' MB',
                'expires_at' => now()->addDays(30)->toISOString(),
            ],
        ]);
    }

    public function license(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'license',
            'meta' => [
                'license_key' => strtoupper($this->faker->bothify('????-????-????-????')),
                'activation_limit' => $this->faker->numberBetween(1, 10),
                'activations_used' => 0,
                'expires_at' => now()->addYear()->toISOString(),
            ],
        ]);
    }
}
