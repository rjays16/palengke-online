<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $fillable = ['name', 'icon', 'color', 'sort_order'];

    public function getIconUrlAttribute(): ?string
    {
        if (!$this->icon) return null;
        return Storage::url($this->icon);
    }
}
