<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDevice extends Model
{
    protected $fillable = [
        'customer_registration_id',
        'device_id',
        'device_name',
        'device_token_id',
        'ip_address',
        'user_agent',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(CustomerRegistration::class, 'customer_registration_id');
    }
}
