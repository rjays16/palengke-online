<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestrictionCode extends Model
{
    protected $fillable = ['code', 'label', 'enabled'];

    protected $casts = [
        'enabled' => 'boolean',
    ];
}
