<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    /**
     * Create or update a customer.
     *
     * @param array $customerData Customer data from the order
     * @return Customer
     */
    public function createOrUpdateCustomer(array $customerData): Customer
    {
        $customer = Customer::updateOrCreate(
            ['email' => $customerData['email']],
            [
                'first_name'       => $customerData['name']['first'],
                'last_name'        => $customerData['name']['last'],
                'phone'            => $customerData['phone'],
                'billing_address'  => $customerData['billing_address'],
                'shipping_address' => $customerData['shipping_address'],
            ]
        );

        return $customer;
    }
}
