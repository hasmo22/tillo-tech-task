<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Mockery\MockInterface;
use MongoDB\BSON\ObjectId;
use Illuminate\Support\Facades\File;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Artisan;
use App\Repositories\CustomerRepository;

class ImportOrdersTest extends TestCase
{
    private string $ordersFilePath;
    private string $fullOrdersFilePath;
    private MockInterface $productRepositoryMock;
    private MockInterface $customerRepositoryMock;
    private MockInterface $orderRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Define file paths
        $this->ordersFilePath = 'tests/Feature/fixtures/orders.json';
        $this->fullOrdersFilePath = base_path($this->ordersFilePath);

        // Ensure directory exists
        if (!File::exists(dirname($this->fullOrdersFilePath))) {
            File::makeDirectory(dirname($this->fullOrdersFilePath), 0755, true, true);
        }

        // Mock repositories
        $this->productRepositoryMock = Mockery::mock(ProductRepository::class);
        $this->customerRepositoryMock = Mockery::mock(CustomerRepository::class);
        $this->orderRepositoryMock = Mockery::mock(OrderRepository::class);

        // Bind mocks to Laravel container
        $this->app->instance(ProductRepository::class, $this->productRepositoryMock);
        $this->app->instance(CustomerRepository::class, $this->customerRepositoryMock);
        $this->app->instance(OrderRepository::class, $this->orderRepositoryMock);
    }

    protected function tearDown(): void
    {
        // Remove test file
        if (File::exists($this->fullOrdersFilePath)) {
            File::delete($this->fullOrdersFilePath);
        }

        // Clean up the fixtures directory if it's empty
        $fixturesDir = dirname($this->fullOrdersFilePath);
        if (File::exists($fixturesDir) && count(File::files($fixturesDir)) === 0) {
            File::deleteDirectory($fixturesDir);
        }

        Mockery::close();
        parent::tearDown();
    }

    public function test_it_successfully_imports_orders()
    {
        $orderData = $this->getOrderData();
        
        // Create test file
        File::put($this->fullOrdersFilePath, json_encode([$orderData]));

        // Mock product repository call
        $product = new Product([
            'title' => $orderData['title'],
            'price' => (float)$orderData['price'],
            'currency' => $orderData['currency'],
            'url' => $orderData['url']
        ]);

        $product->_id = new ObjectId();

        $this->productRepositoryMock->shouldReceive('createOrUpdateProduct')
            ->once()
            ->with($orderData)
            ->andReturn($product);

        // Mock customer repository call
        $customer = new Customer([
            'first_name'  => $orderData['customer']['name']['first'],
            'last_name'   => $orderData['customer']['name']['last'],
            'email'       => $orderData['customer']['email'],
            'billing_address'  => $orderData['customer']['billing_address'],
            'shipping_address' => $orderData['customer']['shipping_address']
        ]);   
        $customer->_id = new ObjectId();  

        $this->customerRepositoryMock->shouldReceive('createOrUpdateCustomer')
            ->once()
            ->with($orderData['customer'])
            ->andReturn($customer);

        // Mock order repository call
        $this->orderRepositoryMock->shouldReceive('createOrUpdateOrder')
            ->once()
            ->with(
                $orderData,
                $customer->_id,
                [
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'email' => $customer->email,
                    'billing_address' => $customer->billing_address,
                    'shipping_address' => $customer->shipping_address,
                ],
                $product->_id,
                [
                    'title' => $product->title,
                    'price' => $product->price,
                    'currency' => $product->currency,
                ]
            );

        // Run import command
        $exitCode = Artisan::call('import:orders', [
            '--file' => $this->ordersFilePath
        ]);

        $this->assertEquals(0, $exitCode);
    }

    public function test_it_fails_when_file_does_not_exist()
    {
        if (File::exists($this->fullOrdersFilePath)) {
            File::delete($this->fullOrdersFilePath);
        }

        $exitCode = Artisan::call('import:orders');
        $this->assertEquals(1, $exitCode);
    }

    public function test_it_fails_with_invalid_json()
    {
        File::put($this->fullOrdersFilePath, 'invalid json');

        $exitCode = Artisan::call('import:orders', [
            '--file' => $this->ordersFilePath
        ]);

        $this->assertEquals(1, $exitCode);
    }

    public function test_it_updates_existing_records()
    {
        $orderData = $this->getOrderData();

        // Create test file
        File::put($this->fullOrdersFilePath, json_encode([$orderData]));

        // Mock repositories for both runs
        $product = new Product();
        $product->_id = new ObjectId();

        $customer = new Customer();
        $customer->_id = new ObjectId();        

        $this->productRepositoryMock->shouldReceive('createOrUpdateProduct')
            ->twice()
            ->with($orderData)
            ->andReturn($product);

        $this->customerRepositoryMock->shouldReceive('createOrUpdateCustomer')
            ->twice()
            ->with($orderData['customer'])
            ->andReturn($customer);

        $this->orderRepositoryMock->shouldReceive('createOrUpdateOrder')
            ->twice()
            ->with(
                $orderData,
                $customer->_id,
                Mockery::type('array'),
                $product->_id,
                Mockery::type('array')
            );

        // Run command twice
        Artisan::call('import:orders', ['--file' => $this->ordersFilePath]);
        $exitCode = Artisan::call('import:orders', ['--file' => $this->ordersFilePath]);

        $this->assertEquals(0, $exitCode);
    }

    /**
     * Return order data for testing.
     *
     * @return array
     */
    private function getOrderData(): array
    {
        return [
            'id' => '12345',
            'uuid' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
            'title' => 'Tillo Product',
            'description' => 'A test product description',
            'price' => '99.99',
            'currency' => 'GBP',
            'url' => 'https://example.com/product/12345',
            'created_at' => '2024-02-18T12:00:00Z',
            'customer' => [
                'name' => [
                    'first' => 'Hass',
                    'last' => 'Mohammed'
                ],
                'email' => 'hass@example.com',
                'phone' => '07999999999',
                'billing_address' => [
                    'street' => '123 Fake Street',
                    'city' => 'London',
                    'county' => 'Essex'
                ],
                'shipping_address' => [
                    'street' => '123 Fake St',
                    'city' => 'London',
                    'county' => 'Essex'
                ]
            ]
        ];
    }
}
