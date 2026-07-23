<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SellerRegistration;

class Notification extends Model
{
    protected $fillable = [
        'seller_registration_id', 'driver_registration_id', 'type', 'title', 'category', 'message', 'link', 'data', 'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
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
