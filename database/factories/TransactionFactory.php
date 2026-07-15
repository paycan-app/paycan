<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $types = ['payment', 'refund', 'subscription_payment', 'subscription_refund'];
        $type = $this->faker->randomElement($types);
        $amount = $this->faker->randomFloat(2, 1, 1000);

        // Create order first to get user_id
        $order = Order::factory()->create();

        return [
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'subscription_id' => $type === 'subscription_payment' || $type === 'subscription_refund'
                ? Subscription::factory()->create(['user_id' => $order->user_id])->id
                : null,
            'type' => $type,
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'canceled']),
            'amount' => $amount,
            'currency' => 'USD',
            'gateway' => $this->faker->randomElement(['stripe', 'paypal']),
            'gateway_transaction_id' => $this->faker->bothify('txn_##??##??##??##??'),
            'gateway_data' => [
                'payment_method' => $this->faker->randomElement(['card', 'paypal', 'bank_transfer']),
                'last_four' => $this->faker->numerify('####'),
                'brand' => $this->faker->randomElement(['visa', 'mastercard', 'amex']),
            ],
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function payment(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'payment',
        ]);
    }

    public function refund(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'refund',
            'amount' => -abs($attributes['amount'] ?? $this->faker->randomFloat(2, 1, 1000)),
        ]);
    }
}
