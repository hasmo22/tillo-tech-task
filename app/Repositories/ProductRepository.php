<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    /**
     * Create or update a product.
     *
     * @param array $productData Product data from the order
     * @return Product
     */
    public function createOrUpdateProduct(array $productData): Product
    {
        return Product::updateOrCreate(
            [
                'title' => $productData['title'],
                'url' => $productData['url']
            ],
            [
                'title'       => $productData['title'],
                'description' => $productData['description'],
                'currency'    => $productData['currency'],
                'price'       => (float) $productData['price'],
                'url'         => $productData['url'],
                'legacy_id'   => $productData['id']
            ]
        );
    }
}
