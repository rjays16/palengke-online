<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'seller_registration_id',
        'participant_type',
        'participant_name',
        'participant_email',
        'participant_avatar',
        'order_id',
        'last_message',
        'last_message_at',
        'unread_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'unread_count' => 'integer',
    ];

    public function seller()
    {
        return $this->belongsTo(SellerRegistration::class, 'seller_registration_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
