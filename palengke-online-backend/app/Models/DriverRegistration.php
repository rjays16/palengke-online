<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class DriverRegistration extends Model
{
    use HasApiTokens;
    protected $fillable = [
        'email', 'country_code', 'phone', 'full_phone', 'email_verified',
        'first_name', 'middle_name', 'last_name', 'country', 'birthdate', 'birthplace', 'current_address',
        'license_number', 'license_type', 'restriction_code', 'expiry_date',
        'vehicle_type', 'plate_number', 'vehicle_model', 'vehicle_color',
        'license_front_photo', 'license_back_photo', 'vehicle_reg_photo',
        'nbi_clearance_photo', 'birth_cert_photo', 'selfie_photo',
        'pin', 'auto_accept', 'bulky_enabled', 'express_enabled', 'preferred_locations', 'status', 'rejection_reason', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'email_verified' => 'boolean',
        'reviewed_at' => 'datetime',
        'preferred_locations' => 'array',
    ];

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'driver_registration_id');
    }
}
