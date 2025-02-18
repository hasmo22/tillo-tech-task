<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = [
        'original_id',
        'title',
        'description',
        'currency',
        'price',
        'url'
    ];

    /**
     * The attributes that should be unique.
     *
     * @var array
     */
    protected $indexes = [
        ['key' => ['original_id' => 1], 'unique' => true]
    ];

    /**
     * Get the orders that reference this product.
     *
     * @return \MongoDB\Laravel\Relations\ReferencesMany
     */
    public function orders()
    {
        return $this->referencesMany(Order::class, 'product_id');
    }
}