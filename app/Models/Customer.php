<?php

namespace App\Models;

use MongoDB\BSON\ObjectId;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'customers';
    
    protected $primaryKey = '_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'billing_address',
        'shipping_address'
    ];

    public function orders()
    {
        return $this->referencesMany(Order::class, 'customer_id');
    }

    /**
     * Boot method to ensure `_id` is assigned.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->_id)) {
                $customer->_id = (string) new ObjectId();
            }
        });
    }
}
