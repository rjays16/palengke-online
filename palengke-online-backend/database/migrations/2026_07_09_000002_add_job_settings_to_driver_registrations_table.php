<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driver_registrations', function (Blueprint $table) {
            $table->boolean('auto_accept')->default(false)->after('pin');
            $table->boolean('bulky_enabled')->default(false)->after('auto_accept');
        });
    }

    public function down(): void
    {
        Schema::table('driver_registrations', function (Blueprint $table) {
            $table->dropColumn(['auto_accept', 'bulky_enabled']);
        });
    }
};
