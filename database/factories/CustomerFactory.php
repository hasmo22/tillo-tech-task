<?php

namespace Database\Factories;

use App\Models\Customer;
use MongoDB\BSON\ObjectId;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            '_id' => (string) new ObjectId(),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'billing_address' => [
                'postcode' => $this->faker->postcode,
                'county' => 'Essex',
                'city' => $this->faker->city,
                'street' => $this->faker->streetAddress,
            ],
            'shipping_address' => [
                'postcode' => $this->faker->postcode,
                'county' => 'Essex',
                'city' => $this->faker->city,
                'street' => $this->faker->streetAddress,
            ],
        ];
    }
}
