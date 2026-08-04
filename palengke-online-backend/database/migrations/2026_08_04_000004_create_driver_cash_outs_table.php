<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_cash_outs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_registration_id');
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->foreign('driver_registration_id')
                ->references('id')
                ->on('driver_registrations')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_cash_outs');
    }
};
