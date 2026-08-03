<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('notifications', 'driver_registration_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->unsignedBigInteger('driver_registration_id')->nullable()->after('seller_registration_id');
            });
        }

        if (!Schema::hasColumn('notifications', 'category')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('category')->nullable()->after('title');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('notifications', 'category')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }

        if (Schema::hasColumn('notifications', 'driver_registration_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('driver_registration_id');
            });
        }
    }
};
