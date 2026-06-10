<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    protected $fillable = [
        'seller_registration_id',
        'plan_id',
        'plan_name',
        'amount',
        'payment_method_id',
        'payment_method_name',
        'status',
        'subscribed_at',
        'plan_start',
        'plan_end',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'subscribed_at' => 'datetime',
        'plan_start' => 'date',
        'plan_end' => 'date',
    ];

    public function seller()
    {
        return $this->belongsTo(SellerRegistration::class, 'seller_registration_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
