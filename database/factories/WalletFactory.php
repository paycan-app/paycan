<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['basic', 'premium']),
            'balance' => $this->faker->randomFloat(4, 0, 5000),
            'currency' => 'credits',
            'is_active' => true,
            'meta' => null,
        ];
    }

    public function basic(): static
    {
        return $this->state(fn () => [
            'type' => 'basic',
        ]);
    }

    public function premium(): static
    {
        return $this->state(fn () => [
            'type' => 'premium',
        ]);
    }

    public function withBalance(float $balance): static
    {
        return $this->state(fn () => [
            'balance' => $balance,
        ]);
    }
}
