<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverDevice extends Model
{
    protected $fillable = [
        'driver_registration_id',
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

    public function driver()
    {
        return $this->belongsTo(DriverRegistration::class, 'driver_registration_id');
    }
}
