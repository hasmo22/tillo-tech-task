<?php

namespace App\Repositories;

use App\Models\Order;
use MongoDB\Laravel\Eloquent\Casts\ObjectId;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository
{
    /**
     * Number of items to display per page.
     */
    private const PER_PAGE = 10;

    /**
     * Retrieve a filtered and paginated list of orders with total price.
     *
     * @param array $filters Associative array of filter criteria
     *                      [
     *                          'price_min' => float|null,
     *                          'price_max' => float|null,
     *                          'currency'  => string|null,
     *                          'county'    => string|null
     *                      ]
     * 
     * @return array{
     *     orders: LengthAwarePaginator,
     *     total_price: float
     * }
     */
    public function getOrders(array $filters): array
    {
        $query = Order::query();
        
        $mongoQuery = $this->buildMongoQuery($filters);
        $query->whereRaw($mongoQuery);

        $orders = $query->paginate(self::PER_PAGE);
        
        $totalPrice = Order::raw(function($collection) use ($mongoQuery) {
            $pipeline = [
                [
                    '$match' => (object) $mongoQuery
                ],
                [
                    '$group' => [
                        '_id' => null,
                        'total' => [
                            '$sum' => [
                                '$toDouble' => '$product_snapshot.price'
                            ]
                        ]
                    ]
                ]
            ];

            $result = $collection->aggregate($pipeline);
            $results = iterator_to_array($result);
            
            return !empty($results) ? (float) $results[0]['total'] : 0.0;
        });

        return [
            'orders' => $orders,
            'total_price' => $totalPrice
        ];
    }

    /**
     * Build a MongoDB query array based on provided filters.
     *
     * @param array $filters Associative array of filter criteria
     *                      [
     *                          'price_min' => float|null,
     *                          'price_max' => float|null,
     *                          'currency'  => string|null,
     *                          'county'    => string|null
     *                      ]
     * 
     * @return array MongoDB query array with applied filters
     */
    private function buildMongoQuery(array $filters): array
    {
        $query = [];

        // Apply price range filters
        if (isset($filters['price_min']) || isset($filters['price_max'])) {
            $priceConditions = [];
            
            if (isset($filters['price_min'])) {
                $priceMin = (float) $filters['price_min'];
                $priceConditions['$gte'] = $priceMin;
            }
            
            if (isset($filters['price_max'])) {
                $priceMax = (float) $filters['price_max'];
                $priceConditions['$lte'] = $priceMax;
            }

            if (!empty($priceConditions)) {
                $query['product_snapshot.price'] = $priceConditions;
            }
        }

        // Filter by currency
        if (!empty($filters['currency'])) {
            $query['product_snapshot.currency'] = strtoupper($filters['currency']);
        }

        // Filter by order placed in county
        if (!empty($filters['billing_county'])) {
            $query['customer_snapshot.billing_address.county'] = ucfirst(strtolower($filters['billing_county']));
        }

        // Filter by shipped to county
        if (!empty($filters['shipping_county'])) {
            $query['customer_snapshot.shipping_address.county'] = ucfirst(strtolower($filters['shipping_county']));
        }

        return $query;
    }

    /**
     * Create or update an order.
     *
     * @param array $orderData The original order data
     * @param string $customerId The associated customer ID
     * @param array $customerSnapshot Snapshot of the customer data
     * @param string $productId The associated product ID
     * @param array $productSnapshot Snapshot of the product data
     * @return Order The created or updated order
     */
    public function createOrUpdateOrder(
        array $orderData,
        string $customerId,
        array $customerSnapshot,
        string $productId,
        array $productSnapshot
    ): Order {
        return Order::updateOrCreate(
            ['uuid' => $orderData['uuid']],
            [
                '_id'               => new ObjectId(),
                'uuid'              => $orderData['uuid'],
                'customer_id'       => $customerId,
                'customer_snapshot' => $customerSnapshot,
                'product_id'        => $productId,
                'product_snapshot'  => $productSnapshot,
                'created_at'        => $orderData['created_at'],
            ]
        );
    }
}