<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seller_registrations', function (Blueprint $table) {
            $table->id();

            // Step 1 — Contact
            $table->string('email')->unique();
            $table->string('country_code');
            $table->string('phone');
            $table->string('full_phone');
            $table->boolean('email_verified')->default(false);
            $table->string('password');

            // Step 2 — Personal
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('country');
            $table->date('birthdate');
            $table->string('birthplace');
            $table->text('current_address');

            // Step 3 — Store
            $table->string('store_name');
            $table->string('region');
            $table->string('city');
            $table->string('barangay');
            $table->string('store_place');
            $table->text('complete_address');

            // Step 4 — Verification choice
            $table->string('verification_choice')->nullable();
            $table->string('verification_status')->default('not_verified');

            // Step 5 — ID type
            $table->string('id_type')->nullable();
            $table->string('id_type_name')->nullable();

            // Steps 6 & 7 — Photos (base64)
            $table->longText('id_front_photo')->nullable();
            $table->longText('id_back_photo')->nullable();
            $table->longText('selfie_photo')->nullable();

            // Admin review
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_registrations');
    }
};
