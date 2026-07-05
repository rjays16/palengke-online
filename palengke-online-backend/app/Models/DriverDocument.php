<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverDocument extends Model
{
    protected $fillable = [
        'driver_email',
        'name',
        'type',
        'file_path',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];
}
