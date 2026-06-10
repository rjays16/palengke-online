<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name', 'description', 'color', 'visible', 'coming_soon', 'sort_order',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'coming_soon' => 'boolean',
    ];
}
