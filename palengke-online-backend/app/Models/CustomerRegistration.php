<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class CustomerRegistration extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'email', 'country_code', 'phone', 'full_phone', 'email_verified', 'pin',
        'first_name', 'middle_name', 'last_name', 'birthdate',
        'region', 'city', 'barangay', 'street', 'landmark', 'address_label',
        'accept_marketing', 'terms_accepted', 'status',
    ];

    protected $casts = [
        'email_verified' => 'boolean',
        'accept_marketing' => 'boolean',
        'terms_accepted' => 'boolean',
    ];

    public function devices()
    {
        return $this->hasMany(CustomerDevice::class, 'customer_registration_id');
    }
}
