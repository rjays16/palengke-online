<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SellerRegistration;

class Notification extends Model
{
    protected $fillable = [
        'seller_registration_id', 'type', 'title', 'message', 'link', 'data', 'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function seller()
    {
        $this->belongsTo(SellerRegistration::class, 'seller_registration_id');
    }
}
