<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Licence extends Model
{
    protected $fillable = [
        'driver_registration_id', 'licence_id', 'trial_started_at',
        'trial_ends_at', 'is_paid', 'paid_at', 'amount',
    ];

    protected $casts = [
        'trial_started_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'paid_at' => 'datetime',
        'is_paid' => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(DriverRegistration::class, 'driver_registration_id');
    }
}
