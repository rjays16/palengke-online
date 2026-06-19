<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class SellerRegistration extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'email', 'country_code', 'phone', 'full_phone', 'email_verified', 'pin',
        'first_name', 'middle_name', 'last_name', 'country', 'birthdate', 'birthplace', 'current_address',
        'store_name', 'region', 'city', 'barangay', 'store_place', 'complete_address',
        'verification_choice', 'verification_status',
        'id_type', 'id_type_name',
        'id_front_photo', 'id_back_photo', 'selfie_photo',
        'operating_hours', 'description', 'categories', 'avatar',
        'monthly_sales_target', 'monthly_orders_target',
        'trial_started_at', 'trial_ends_at', 'subscription_status',
        'plan_id', 'plan_started_at', 'plan_ends_at',
        'wallet_balance',
        'status', 'rejection_reason', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'email_verified' => 'boolean',
        'reviewed_at' => 'datetime',
        'categories' => 'array',
        'trial_started_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'plan_started_at' => 'datetime',
        'plan_ends_at' => 'datetime',
        'wallet_balance' => 'decimal:2',
    ];

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_registration_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'seller_registration_id');
    }
}
