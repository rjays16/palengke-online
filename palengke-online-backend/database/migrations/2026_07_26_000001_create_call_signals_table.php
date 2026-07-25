<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_signals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->string('caller_email');
            $table->string('caller_name');
            $table->enum('caller_type', ['seller', 'driver']);
            $table->string('receiver_email');
            $table->enum('status', ['ringing', 'accepted', 'declined', 'ended'])->default('ringing');
            $table->timestamps();

            $table->index('receiver_email');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_signals');
    }
};
