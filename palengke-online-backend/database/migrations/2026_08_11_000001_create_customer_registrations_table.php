<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('country_code');
            $table->string('phone');
            $table->string('full_phone');
            $table->boolean('email_verified')->default(false);
            $table->string('pin')->nullable();

            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('birthdate');

            $table->string('region');
            $table->string('city');
            $table->string('barangay');
            $table->string('street');
            $table->string('landmark')->nullable();
            $table->string('address_label')->nullable();

            $table->boolean('accept_marketing')->default(false);
            $table->boolean('terms_accepted')->default(false);

            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_registrations');
    }
};
