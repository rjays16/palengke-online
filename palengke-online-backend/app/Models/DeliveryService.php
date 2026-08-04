<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryService extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'color', 'tag', 'tag_type', 'always_on', 'driver_setting', 'sort_order',
    ];

    protected $casts = [
        'always_on' => 'boolean',
    ];
}
