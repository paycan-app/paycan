<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $title = fake()->words(3, true);

        return [
            'id' => Str::ulid()->toString(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->sentences(3, true),
            'type' => fake()->randomElement(Product::getTypes()),
            'image' => fake()->imageUrl(640, 480, 'products'),
            'file' => fake()->optional()->url(),
            'is_active' => true,
            'meta' => [
                'features' => fake()->words(5),
                'category' => fake()->word(),
            ],
        ];
    }

    public function physical()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'physical',
        ]);
    }

    public function digital()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'digital',
        ]);
    }

    public function service()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'service',
        ]);
    }

    public function subscription()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'subscription',
        ]);
    }

    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
