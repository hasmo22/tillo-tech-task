<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $fillable = [
        'uuid',
        'customer_id',
        'customer_snapshot',
        'product_id',
        'product_snapshot',
        'created_at'
    ];

    public function customer()
    {
        return $this->referencesOne(Customer::class, 'customer_id');
    }

    public function product()
    {
        return $this->referencesOne(Product::class, 'product_id');
    }
}
