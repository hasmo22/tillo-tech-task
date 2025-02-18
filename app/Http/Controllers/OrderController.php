<?php

namespace App\Http\Controllers;

use App\Repositories\OrderRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @param OrderRepository
     */
    protected OrderRepository $orderRepository;

    /**
     * Create a new OrderController instance.
     *
     * @param OrderRepository $orderRepository The repository for handling order operations
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Display a paginated list of orders with optional filters.
     *
     * @param Request $request The HTTP request instance
     * @return JsonResponse JSON response containing filtered orders and metadata
     *
     * The response includes:
     * - orders: Array of order items for the current page
     * - pagination: Pagination metadata
     * - total_price: Sum of prices for all filtered orders
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'price_min',
            'price_max', 
            'currency',
            'billing_county',
            'shipping_county'
        ]);

        $result = $this->orderRepository->getOrders($filters);
        $orders = $result['orders'];
    
        return response()->json([
            'orders' => $orders->items(),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ],
            'total_price' => round($result['total_price'], 2)
        ]);
    }
}