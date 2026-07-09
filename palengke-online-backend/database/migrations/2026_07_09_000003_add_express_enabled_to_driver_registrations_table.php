<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driver_registrations', function (Blueprint $table) {
            $table->boolean('express_enabled')->default(false)->after('bulky_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('driver_registrations', function (Blueprint $table) {
            $table->dropColumn('express_enabled');
        });
    }
};
