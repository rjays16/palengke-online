<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CountryRegion extends Model
{
    protected $fillable = ['country_id', 'name', 'enabled'];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
