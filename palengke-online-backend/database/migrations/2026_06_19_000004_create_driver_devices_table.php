<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_registration_id')->constrained()->cascadeOnDelete();
            $table->string('device_id');
            $table->string('device_name')->nullable();
            $table->unsignedBigInteger('device_token_id')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['driver_registration_id', 'device_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_devices');
    }
};
