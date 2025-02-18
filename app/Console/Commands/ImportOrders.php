<?php

namespace App\Console\Commands;

use App\Repositories\CustomerRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;

class ImportOrders extends Command
{
    protected $signature = 'import:orders {--file= : Path to the orders JSON file}';
    protected $description = 'Import Tillo orders from orders.json file';
    private const DEFAULT_FILE_PATH = 'orders.json';

    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private CustomerRepository $customerRepository;

    public function __construct(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        CustomerRepository $customerRepository
    ) {
        parent::__construct();

        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int Returns 0 on success, 1 on failure
     */
    public function handle(): int
    {        
        // Use the provided file path or fall back to the default
        $filePath = $this->option('file') 
            ? base_path($this->option('file')) 
            : base_path(self::DEFAULT_FILE_PATH);

        if (!$this->validateFile($filePath)) {
            Log::error('File validation failed');
            return 1;
        }

        $orders = $this->parseOrdersFile($filePath);
        
        if ($orders === null) {
            $this->error('Failed to parse orders file');
            Log::error('Orders parsing failed');
            return 1;
        }

        try {
            $this->processOrders($orders);
            $this->info('Orders imported successfully.');
            return 0;
        } catch (\Exception $e) {
            Log::error('Import failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error("Import failed: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Validate that the orders file exists.
     *
     * @param string $filePath Full path to the orders file
     * @return bool True if file exists, false otherwise
     */
    private function validateFile(string $filePath): bool
    {
        if (!File::exists($filePath)) {
            $this->error("File not found: $filePath");
            return false;
        }

        return true;
    }

    /**
     * Parse the orders JSON file.
     *
     * @param string $filePath Full path to the orders file
     * @return array<int, array>|null Array of order data or null if parsing fails
     */
    private function parseOrdersFile(string $filePath): ?array
    {
        $orders = json_decode(File::get($filePath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON file: ' . json_last_error_msg());
            return null;
        }
        
        return $orders;
    }

    /**
     * Process and import the orders data.
     *
     * @param array<int, array> $orders Array of order data to process
     * @throws \Exception If order processing fails
     * @return void
     */
    private function processOrders(array $orders): void
    {
        foreach ($orders as $orderData) {
            $product = $this->productRepository->createOrUpdateProduct($orderData);
            $customer = $this->customerRepository->createOrUpdateCustomer($orderData['customer']);

            $this->orderRepository->createOrUpdateOrder(
                $orderData,
                $customer->_id,
                [
                    'first_name'       => $customer->first_name,
                    'last_name'        => $customer->last_name,
                    'email'            => $customer->email,
                    'billing_address'  => $customer->billing_address,
                    'shipping_address' => $customer->shipping_address,
                ],
                $product->_id,
                [
                    'title'    => $product->title,
                    'price'    => $product->price,
                    'currency' => $product->currency,
                ]
            );
        }
    }
}
