<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_registration_id')->constrained()->onDelete('cascade');
            $table->string('participant_type'); // customer, driver
            $table->string('participant_name');
            $table->string('participant_email')->nullable();
            $table->string('participant_avatar')->nullable();
            $table->string('order_id')->nullable();
            $table->text('last_message')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->integer('unread_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
