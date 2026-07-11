<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = [
        'name', 'description', 'icon_name', 'color', 'criteria_type', 'criteria_value',
    ];

    public function drivers()
    {
        return $this->belongsToMany(DriverRegistration::class, 'driver_badge')
            ->withPivot('is_new', 'awarded_at')
            ->withTimestamps();
    }
}
