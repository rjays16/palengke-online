<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpTopic extends Model
{
    protected $fillable = [
        'key', 'name', 'icon', 'role', 'enabled', 'sort_order',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];
}
