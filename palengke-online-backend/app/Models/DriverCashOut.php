<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverCashOut extends Model
{
    protected $fillable = [
        'driver_registration_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function driver()
    {
        return $this->belongsTo(DriverRegistration::class, 'driver_registration_id');
    }
}
