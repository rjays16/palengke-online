<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = ['code', 'name', 'dial_code', 'digit_count', 'enabled'];

    public function regions(): HasMany
    {
        return $this->hasMany(CountryRegion::class);
    }

    protected $casts = [
        'enabled' => 'boolean',
    ];
}
