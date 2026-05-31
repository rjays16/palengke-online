<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class SellerRegistration extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'email', 'country_code', 'phone', 'full_phone', 'email_verified', 'password', 'pin',
        'first_name', 'middle_name', 'last_name', 'country', 'birthdate', 'birthplace', 'current_address',
        'store_name', 'region', 'city', 'barangay', 'store_place', 'complete_address',
        'verification_choice', 'verification_status',
        'id_type', 'id_type_name',
        'id_front_photo', 'id_back_photo', 'selfie_photo',
        'status', 'rejection_reason', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'email_verified' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
