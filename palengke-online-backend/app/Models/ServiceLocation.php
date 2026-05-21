<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceLocation extends Model
{
    protected $fillable = ['data'];

    protected $casts = [
        'data' => 'array',
    ];
}
