<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreignId('seller_registration_id')
                ->nullable()
                ->constrained('seller_registrations')
                ->nullOnDelete()
                ->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['seller_registration_id']);
            $table->dropColumn('seller_registration_id');
        });
    }
};
