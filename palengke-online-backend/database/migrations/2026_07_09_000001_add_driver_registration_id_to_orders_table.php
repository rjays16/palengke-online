<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('driver_registration_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->after('seller_registration_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['driver_registration_id']);
            $table->dropColumn('driver_registration_id');
        });
    }
};
