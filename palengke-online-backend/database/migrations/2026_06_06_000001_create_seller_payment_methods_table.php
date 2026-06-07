<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_registration_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('account_name');
            $table->string('account_number');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_payment_methods');
    }
};
