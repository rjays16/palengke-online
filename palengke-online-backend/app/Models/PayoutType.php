<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutType extends Model
{
    protected $fillable = ['key', 'name', 'enabled'];

    protected $casts = [
        'enabled' => 'boolean',
    ];
}
