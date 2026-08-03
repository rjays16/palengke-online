<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'seller_registration_id',
        'driver_registration_id',
        'customer_name',
        'customer_phone',
        'delivery_address',
        'latitude',
        'longitude',
        'items',
        'total',
        'status',
        'payment_method',
        'delivery_fee',
        'tip_amount',
        'tip_message',
        'tip_customer_name',
    ];

    protected $casts = [
        'items' => 'array',
        'total' => 'float',
        'delivery_fee' => 'float',
        'tip_amount' => 'float',
    ];

    public function seller()
    {
        return $this->belongsTo(SellerRegistration::class, 'seller_registration_id');
    }

    public function driver()
    {
        return $this->belongsTo(DriverRegistration::class, 'driver_registration_id');
    }
}
