<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Repositories\ProductRepository;

class ProductRepositoryTest extends TestCase
{
    protected ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = new ProductRepository();
    }

    protected function tearDown(): void
    {
        Product::query()->delete();

        parent::tearDown();
    }

    public function test_it_creates_new_product_if_none_exists(): void
    {
        $productData = [
            'id'          => '12345',
            'title'       => 'Test Product',
            'description' => 'A test product',
            'currency'    => 'GBP',
            'price'       => '19.99',
            'url'         => 'https://example.com/product/12345'
        ];

        $product = $this->productRepository->createOrUpdateProduct($productData);

        $this->assertNotNull($product->_id, 'Product `_id` should not be null.');
        $this->assertEquals('Test Product', $product->title);
        $this->assertEquals(19.99, $product->price);
        $this->assertEquals('GBP', $product->currency);

        // Check db
        $this->assertDatabaseHas('products', [
            '_id'    => $product->_id,
            'title'  => 'Test Product',
            'url'    => 'https://example.com/product/12345'
        ]);
    }

    public function test_it_updates_existing_product_if_already_present(): void
    {
        $originalData = [
            'id'          => '12345',
            'title'       => 'Test Product',
            'description' => 'Original description',
            'currency'    => 'GBP',
            'price'       => '19.99',
            'url'         => 'https://example.com/product/12345'
        ];

        $updatedData = [
            'id'          => '12345',
            'title'       => 'Test Product',
            'description' => 'Updated description',
            'currency'    => 'GBP',
            'price'       => '99.99',
            'url'         => 'https://example.com/product/12345'
        ];

        $originalProduct = $this->productRepository->createOrUpdateProduct($originalData);

        $updatedProduct = $this->productRepository->createOrUpdateProduct($updatedData);

        $this->assertEquals($originalProduct->_id, $updatedProduct->_id, 'Should update the same product record.');
        $this->assertEquals('Updated description', $updatedProduct->description);
        $this->assertEquals(99.99, $updatedProduct->price);

        // Check db for one record
        $this->assertDatabaseHas('products', [
            '_id'         => $originalProduct->_id,
            'description' => 'Updated description',
            'price'       => 99.99
        ]);
        $this->assertEquals(1, Product::where('title', 'Test Product')->count(), 'Should only be one product with the same title.');
    }
}
