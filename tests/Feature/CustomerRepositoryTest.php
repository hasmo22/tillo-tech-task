<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Repositories\CustomerRepository;

class CustomerRepositoryTest extends TestCase
{
    protected CustomerRepository $customerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerRepository = new CustomerRepository();
    }

    protected function tearDown(): void
    {
        Customer::query()->delete();
        parent::tearDown();
    }

    public function test_it_creates_new_customer_if_none_exists(): void
    {
        $customerData = [
            'name' => [
                'first' => 'Hass',
                'last' => 'Mohammed'
            ],
            'email' => 'hass@example.com',
            'phone' => '07123456789',
            'billing_address' => [
                'street' => '123 Billing St',
                'city'   => 'BillingCity',
                'county' => 'Essex'
            ],
            'shipping_address' => [
                'street' => '123 Ship Rd',
                'city'   => 'ShipCity',
                'county' => 'Essex'
            ]
        ];

        $customer = $this->customerRepository->createOrUpdateCustomer($customerData);

        $this->assertNotNull($customer->_id, 'Customer `_id` should not be null.');
        $this->assertEquals('Hass', $customer->first_name);
        $this->assertEquals('hass@example.com', $customer->email);

        // Check the db directly
        $this->assertDatabaseHas('customers', [
            '_id'       => $customer->_id,
            'first_name' => 'Hass',
            'email'      => 'hass@example.com',
        ]);
    }

    /**
     * Test that createOrUpdateCustomer() updates a customer if matching email exists.
     */
    public function test_it_updates_existing_customer_if_already_present(): void
    {
        // original data
        $firstCustomer = [
            'name' => [
                'first' => 'Hass',
                'last'  => 'Mohammed'
            ],
            'email' => 'hass@example.com',
            'phone' => '07123456789',
            'billing_address' => [
                'street' => '1 Posh Road',
                'city'   => 'London',
                'county' => 'Essex'
            ],
            'shipping_address' => [
                'street' => '456 Another Rd',
                'city'   => 'Some Town',
                'county' => 'Essex'
            ]
        ];

        // updated data
        $customerData = [
            'name' => [
                'first' => 'Obi-Wan',
                'last'  => 'Mohammed'
            ],
            'email' => 'hass@example.com',
            'phone' => '07123456789',
            'billing_address' => [
                'street' => '1 Posh Road',
                'city'   => 'London',
                'county' => 'Kent'
            ],
            'shipping_address' => [
                'street' => '456 Another Rd',
                'city'   => 'Some Town',
                'county' => 'Essex'
            ]
        ];

        $existing = $this->customerRepository->createOrUpdateCustomer($firstCustomer);
        $updatedCustomer = $this->customerRepository->createOrUpdateCustomer($customerData);

        // ids should be matching
        $this->assertEquals($existing->_id, $updatedCustomer->_id, 'Should update the same customer record.');
        $this->assertEquals('Obi-Wan', $updatedCustomer->first_name, 'First name should be updated.');
        $this->assertEquals('Kent', $updatedCustomer->billing_address['county'], 'Billing county should be updated.');

        // Check the DB record was updated, not duplicated
        $this->assertDatabaseHas('customers', [
            '_id'         => $existing->_id,
            'first_name'  => 'Obi-Wan',
            'email'       => 'hass@example.com',
            'billing_address->county' => 'Kent'
        ]);

        $this->assertEquals(1, Customer::where('email', 'hass@example.com')->count(), 'There should be exactly 1 record with this email.');
    }
}
