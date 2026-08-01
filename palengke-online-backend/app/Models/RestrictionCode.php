<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestrictionCode extends Model
{
    protected $fillable = ['code', 'label', 'enabled', 'vehicle_types'];

    protected $casts = [
        'enabled' => 'boolean',
        'vehicle_types' => 'array',
    ];
}
