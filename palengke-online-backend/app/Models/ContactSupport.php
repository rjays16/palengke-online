<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSupport extends Model
{
    protected $fillable = [
        'key', 'title', 'subtitle', 'type', 'value', 'icon', 'enabled', 'sort_order',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];
}
