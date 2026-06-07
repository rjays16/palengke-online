<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerPaymentMethod extends Model
{
    protected $fillable = [
        'seller_registration_id',
        'type',
        'account_name',
        'account_number',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function seller()
    {
        return $this->belongsTo(SellerRegistration::class, 'seller_registration_id');
    }
}
