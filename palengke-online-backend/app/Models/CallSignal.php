<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallSignal extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'caller_email',
        'caller_name',
        'caller_type',
        'receiver_email',
        'status',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
