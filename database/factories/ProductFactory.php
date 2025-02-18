<?php

namespace Database\Factories;

use App\Models\Product;
use MongoDB\BSON\ObjectId;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            '_id' => (string) new ObjectId(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'currency' => $this->faker->randomElement(['GBP', 'USD', 'EUR']),
            'price' => $this->faker->randomFloat(2, 1, 500),
            'url' => $this->faker->url,
        ];
    }
}
