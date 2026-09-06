<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletTransactionFactory extends Factory
{
    protected $model = WalletTransaction::class;

    public function definition(): array
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);
        $type = $this->faker->randomElement(['credit', 'debit']);
        $amount = $this->faker->randomFloat(4, 1, 100);

        return [
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'type' => $type,
            'action' => $type === 'credit' ? 'subscription_grant' : 'usage',
            'amount' => $amount,
            'balance_after' => $amount,
            'reference_id' => $this->faker->uuid(),
            'description' => $this->faker->sentence(),
            'meta' => null,
        ];
    }

    public function credit(): static
    {
        return $this->state(fn () => [
            'type' => 'credit',
            'action' => 'subscription_grant',
        ]);
    }

    public function debit(): static
    {
        return $this->state(fn () => [
            'type' => 'debit',
            'action' => 'usage',
        ]);
    }
}
