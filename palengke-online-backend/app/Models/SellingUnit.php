<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellingUnit extends Model
{
    protected $fillable = [
        'code',
        'label',
        'plural',
        'icon',
        'has_min_order',
        'example_stock',
        'enabled',
        'sort_order',
    ];

    protected $casts = [
        'has_min_order' => 'boolean',
        'enabled' => 'boolean',
    ];
}
