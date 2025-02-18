<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;

class OrderControllerTest extends TestCase
{
    protected Customer $customer;
    protected Product $product;
    protected Product $expensiveProduct;

    protected function setUp(): void
    {
        parent::setUp();
        Order::query()->delete();
        $this->customer = Customer::factory()->create();
        $this->product = Product::factory()->create(['price' => 50.00, 'currency' => 'GBP']);
        $this->expensiveProduct = Product::factory()->create(['price' => 200.00, 'currency' => 'GBP']);
    }

    protected function tearDown(): void
    {
        Order::query()->delete();
        Customer::query()->delete();
        Product::query()->delete();

        parent::tearDown();
    }

    public function test_can_fetch_paginated_orders(): void
    {
        Order::factory()->count(15)->create();
    
        $response = $this->getJson('/api/orders?page=2');
    
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'orders',
                     'pagination' => ['total', 'per_page', 'current_page', 'last_page'],
                     'total_price'
                 ]);
    
        $responseData = $response->json();

        $this->assertEquals(10, $responseData['pagination']['per_page']);
        $this->assertEquals(2, $responseData['pagination']['current_page']);
        $this->assertEquals(2, $responseData['pagination']['last_page']);
        $this->assertCount(5, $responseData['orders']);
    }

    public function test_can_filter_orders_by_price_min(): void
    {
        Order::factory()->create([
            'customer_id' => $this->customer->_id,
            'product_snapshot' => ['price' => 50.00]
        ]);
        Order::factory()->create([
            'customer_id' => $this->customer->_id,
            'product_snapshot' => ['price' => 200.00]
        ]);

        $response = $this->getJson('/api/orders?price_min=100');

        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertEquals(200.00, $responseData['total_price']);
        $this->assertCount(1, $responseData['orders']);
    }

    public function test_can_filter_orders_by_currency(): void
    {
        Order::factory()->create([
            'customer_id' => $this->customer->_id,
            'product_snapshot' => ['price' => 50.00, 'currency' => 'GBP']
        ]);
        Order::factory()->create([
            'customer_id' => $this->customer->_id,
            'product_snapshot' => ['price' => 100.00, 'currency' => 'EUR']
        ]);

        $response = $this->getJson('/api/orders?currency=GBP');

        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertEquals(50.00, $responseData['total_price']);
        $this->assertCount(1, $responseData['orders']);
    }

    public function test_can_filter_orders_by_billing_county(): void
    {
        $customerEssex = Customer::factory()->create(['billing_address' => ['county' => 'Essex']]);
        $customerKent = Customer::factory()->create(['billing_address' => ['county' => 'Kent']]);

        Order::factory()->create(['customer_id' => $customerEssex->_id, 'customer_snapshot' => $customerEssex->toArray()]);
        Order::factory()->create(['customer_id' => $customerKent->_id, 'customer_snapshot' => $customerKent->toArray()]);

        $response = $this->getJson('/api/orders?billing_county=Essex');

        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertCount(1, $responseData['orders']);
        $this->assertEquals('Essex', $responseData['orders'][0]['customer_snapshot']['billing_address']['county']);
    }

    public function test_can_filter_orders_by_shipping_county(): void
    {
        $customerLondon = Customer::factory()->create(['shipping_address' => ['county' => 'London']]);
        $customerYorkshire = Customer::factory()->create(['shipping_address' => ['county' => 'Yorkshire']]);

        Order::factory()->create(['customer_id' => $customerLondon->_id, 'customer_snapshot' => $customerLondon->toArray()]);
        Order::factory()->create(['customer_id' => $customerYorkshire->_id, 'customer_snapshot' => $customerYorkshire->toArray()]);

        $response = $this->getJson('/api/orders?shipping_county=London');

        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertCount(1, $responseData['orders']);
        $this->assertEquals('London', $responseData['orders'][0]['customer_snapshot']['shipping_address']['county']);
    }
}
