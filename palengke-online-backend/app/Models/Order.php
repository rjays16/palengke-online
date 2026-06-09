<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'seller_registration_id',
        'customer_name',
        'customer_phone',
        'delivery_address',
        'latitude',
        'longitude',
        'items',
        'total',
        'status',
        'payment_method',
    ];

    protected $casts = [
        'items' => 'array',
        'total' => 'float',
    ];

    public function seller()
    {
        return $this->belongsTo(SellerRegistration::class, 'seller_registration_id');
    }
}
