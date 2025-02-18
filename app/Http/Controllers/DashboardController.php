<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Illuminate\Http\Client\Response;

/**
 * Helper dashboard controller to get the values we need to populate our dashboard on app start.
 */
class DashboardController extends Controller
{
    /**
     * Display the dashboard with order statistics.
     *
     * @return View
     */
    public function index(): View
    {
        $baseUrl = url('/api/orders');

        // Fetch all the required data
        $data = [
            'freeOrdersCount' => $this->getFreeOrdersCount($baseUrl),
            'gbpOrdersCount' => $this->getGbpOrdersCount($baseUrl),
            'essexOrdersCount' => $this->getEssexOrdersCount($baseUrl),
            'gbpOver100Sum' => $this->getGbpOver100Sum($baseUrl),
            'allGbpSum' => $this->getAllGbpSum($baseUrl),
            'gbpEssexSum' => $this->getGbpEssexSum($baseUrl),
        ];

        return view('dashboard', $data);
    }

    /**
     * Get the count of free orders (price = 0).
     *
     * @param string $baseUrl The base URL for the API
     * @return string|int The count of free orders or 'N/A' if request fails
     */
    private function getFreeOrdersCount(string $baseUrl): string|int
    {
        $response = Http::get("$baseUrl", [
            'price_min' => 0,
            'price_max' => 0
        ]);
        return $response->successful() ? $response->json()['pagination']['total'] : 'N/A';
    }

    /**
     * Get the count of orders in GBP currency.
     *
     * @param string $baseUrl The base URL for the API
     * @return string|int The count of GBP orders or 'N/A' if request fails
     */
    private function getGbpOrdersCount(string $baseUrl): string|int
    {
        $response = Http::get("$baseUrl", [
            'currency' => 'gbp'
        ]);
        return $response->successful() ? $response->json()['pagination']['total'] : 'N/A';
    }

    /**
     * Get the count of orders shipped to Essex.
     *
     * @param string $baseUrl The base URL for the API
     * @return string|int The count of Essex orders or 'N/A' if request fails
     */
    private function getEssexOrdersCount(string $baseUrl): string|int
    {
        $response = Http::get("$baseUrl", [
            'shipping_county' => 'essex'
        ]);
        return $response->successful() ? $response->json()['pagination']['total'] : 'N/A';
    }

    /**
     * Get the sum of GBP orders with value >= £100.
     *
     * @param string $baseUrl The base URL for the API
     * @return string The formatted sum of high-value GBP orders or 'N/A' if request fails
     */
    private function getGbpOver100Sum(string $baseUrl): string
    {
        $response = Http::get("$baseUrl", [
            'currency' => 'gbp',
            'price_min' => 100
        ]);
        return $response->successful() ? number_format($response->json()['total_price'], 2) : 'N/A';
    }

    /**
     * Get the sum of all orders in GBP.
     *
     * @param string $baseUrl The base URL for the API
     * @return string The formatted sum of all GBP orders or 'N/A' if request fails
     */
    private function getAllGbpSum(string $baseUrl): string
    {
        $response = Http::get("$baseUrl", [
            'currency' => 'gbp'
        ]);
        return $response->successful() ? number_format($response->json()['total_price'], 2) : 'N/A';
    }

    /**
     * Get the sum of GBP orders shipped to Essex.
     *
     * @param string $baseUrl The base URL for the API
     * @return string The formatted sum of Essex GBP orders or 'N/A' if request fails
     */
    private function getGbpEssexSum(string $baseUrl): string
    {
        $response = Http::get("$baseUrl", [
            'currency' => 'gbp',
            'shipping_county' => 'essex'
        ]);
        return $response->successful() ? number_format($response->json()['total_price'], 2) : 'N/A';
    }
}
