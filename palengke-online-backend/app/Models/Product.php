<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'seller_registration_id',
        'name',
        'category',
        'description',
        'selling_unit',
        'price',
        'minimum_order',
        'has_discount',
        'discount_price',
        'stock_quantity',
        'sku',
        'origin',
        'brand',
        'is_featured',
        'is_active',
        'images',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
        'has_discount' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'float',
        'discount_price' => 'float',
        'stock_quantity' => 'float',
        'minimum_order' => 'float',
    ];

    public function seller()
    {
        return $this->belongsTo(SellerRegistration::class, 'seller_registration_id');
    }
}
