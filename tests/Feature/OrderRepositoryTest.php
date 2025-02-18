<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepository;
use Tests\TestCase;

class OrderRepositoryTest extends TestCase
{
    protected OrderRepository $orderRepository;
    protected Customer $customer;
    protected Product $cheapProduct;
    protected Product $expensiveProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderRepository = new OrderRepository();

        $this->customer = Customer::factory()->create();
        $this->cheapProduct = Product::factory()->create([
            'price' => 10.00,
            'currency' => 'GBP'
        ]);

        $this->expensiveProduct = Product::factory()->create([
            'price' => 200.00,
            'currency' => 'GBP'
        ]);
    }

    protected function tearDown(): void
    {
        Order::query()->delete();
        Customer::query()->delete();
        Product::query()->delete();
        
        parent::tearDown();
    }

    public function test_can_retrieve_paginated_orders(): void
    {
        Order::factory()->count(15)->create([
            'customer_id' => $this->customer->_id,
            'customer_snapshot' => $this->customer->toArray(),
            'product_id' => $this->cheapProduct->_id,
            'product_snapshot' => $this->cheapProduct->toArray()
        ]);

        $result = $this->orderRepository->getOrders([]);

        $this->assertCount(10, $result['orders']->items());
        $this->assertEquals(150.00, $result['total_price']);
    }

    public function test_can_filter_orders_by_price_min(): void
    {
        Order::factory()->create([
            'customer_id' => $this->customer->_id,
            'product_id' => $this->cheapProduct->_id,
            'product_snapshot' => $this->cheapProduct->toArray()
        ]);

        Order::factory()->create([
            'customer_id' => $this->customer->_id,
            'product_id' => $this->expensiveProduct->_id,
            'product_snapshot' => $this->expensiveProduct->toArray()
        ]);

        $result = $this->orderRepository->getOrders(['price_min' => 100]);

        $this->assertCount(1, $result['orders']->items());
        $this->assertEquals(200.00, $result['total_price']);
    }

    public function test_can_filter_orders_by_price_max(): void
    {
        Order::factory()->create([
            'customer_id' => $this->customer->_id,
            'product_id' => $this->cheapProduct->_id,
            'product_snapshot' => $this->cheapProduct->toArray()
        ]);

        Order::factory()->create([
            'customer_id' => $this->customer->_id,
            'product_id' => $this->expensiveProduct->_id,
            'product_snapshot' => $this->expensiveProduct->toArray()
        ]);

        $result = $this->orderRepository->getOrders(['price_max' => 50]);

        $this->assertCount(1, $result['orders']->items());
        $this->assertEquals(10.00, $result['total_price']);
    }

    public function test_can_filter_orders_by_currency(): void
    {
        $productEUR = Product::factory()->create(['price' => 100.00, 'currency' => 'EUR']);

        Order::factory()->create([
            'customer_id' => $this->customer->_id,
            'product_id' => $this->cheapProduct->_id,
            'product_snapshot' => $this->cheapProduct->toArray()
        ]);

        Order::factory()->create([
            'customer_id' => $this->customer->_id,
            'product_id' => $productEUR->_id,
            'product_snapshot' => $productEUR->toArray()
        ]);

        $result = $this->orderRepository->getOrders(['currency' => 'GBP']);

        $this->assertCount(1, $result['orders']->items());
        $this->assertEquals(10.00, $result['total_price']);
    }

    public function test_can_filter_orders_by_billing_county(): void
    {
        $customerLondon = Customer::factory()->create(['billing_address' => ['county' => 'London']]);
        $customerYorkshire = Customer::factory()->create(['billing_address' => ['county' => 'Yorkshire']]);

        Order::factory()->create([
            'customer_id' => $customerLondon->_id,
            'customer_snapshot' => $customerLondon->toArray()
        ]);

        Order::factory()->create([
            'customer_id' => $customerYorkshire->_id,
            'customer_snapshot' => $customerYorkshire->toArray()
        ]);

        $result = $this->orderRepository->getOrders(['billing_county' => 'London']);

        $this->assertCount(1, $result['orders']->items());
        $this->assertEquals('London', $result['orders']->items()[0]['customer_snapshot']['billing_address']['county']);
    }

    public function test_can_filter_orders_by_shipping_county(): void
    {
        $customerManchester = Customer::factory()->create(['shipping_address' => ['county' => 'Manchester']]);
        $customerLeeds = Customer::factory()->create(['shipping_address' => ['county' => 'Leeds']]);

        Order::factory()->create([
            'customer_id' => $customerManchester->_id,
            'customer_snapshot' => $customerManchester->toArray()
        ]);

        Order::factory()->create([
            'customer_id' => $customerLeeds->_id,
            'customer_snapshot' => $customerLeeds->toArray()
        ]);

        $result = $this->orderRepository->getOrders(['shipping_county' => 'Manchester']);

        $this->assertCount(1, $result['orders']->items());
        $this->assertEquals('Manchester', $result['orders']->items()[0]['customer_snapshot']['shipping_address']['county']);
    }
}
