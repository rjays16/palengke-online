<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerDevice extends Model
{
    protected $fillable = [
        'seller_registration_id',
        'device_id',
        'device_name',
        'pin',
        'ip_address',
        'user_agent',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function seller()
    {
        return $this->belongsTo(SellerRegistration::class, 'seller_registration_id');
    }
}
