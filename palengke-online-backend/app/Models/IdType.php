<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdType extends Model
{
    protected $fillable = ['key', 'name', 'group', 'enabled'];

    protected $casts = [
        'enabled' => 'boolean',
    ];
}
