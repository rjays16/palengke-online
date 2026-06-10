<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'question', 'answer', 'active', 'sort_order',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
