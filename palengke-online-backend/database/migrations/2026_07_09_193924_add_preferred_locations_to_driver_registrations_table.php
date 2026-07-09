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
        Schema::table('driver_registrations', function (Blueprint $table) {
            $table->json('preferred_locations')->nullable()->after('express_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('driver_registrations', function (Blueprint $table) {
            $table->dropColumn('preferred_locations');
        });
    }
};
