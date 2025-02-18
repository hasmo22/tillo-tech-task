<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use MongoDB\BSON\ObjectId;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        return [
            '_id' => (string) new ObjectId(),
            'uuid' => (string) new ObjectId(),
            'customer_id' => $customer->_id,
            'customer_snapshot' => $customer->toArray(),
            'product_id' => $product->_id,
            'product_snapshot' => [
                'title' => $product->title,
                'price' => $product->price,
                'currency' => $product->currency,
            ],
            'created_at' => now(),
        ];
    }
}
