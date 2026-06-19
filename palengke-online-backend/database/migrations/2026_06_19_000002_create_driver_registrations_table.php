<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('country_code');
            $table->string('phone');
            $table->string('full_phone');
            $table->boolean('email_verified')->default(false);
            $table->string('password');

            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('country');
            $table->date('birthdate');
            $table->string('birthplace');
            $table->text('current_address');

            $table->string('license_number');
            $table->string('license_type');
            $table->string('restriction_code');
            $table->date('expiry_date');

            $table->string('vehicle_type');
            $table->string('plate_number');
            $table->string('vehicle_model');
            $table->string('vehicle_color')->nullable();

            $table->text('license_front_photo')->nullable();
            $table->text('license_back_photo')->nullable();
            $table->text('vehicle_reg_photo')->nullable();
            $table->text('nbi_clearance_photo')->nullable();
            $table->text('birth_cert_photo')->nullable();
            $table->text('selfie_photo')->nullable();

            $table->string('status')->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_registrations');
    }
};
