<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_registrations', function (Blueprint $table) {
            $table->string('operating_hours')->nullable()->after('complete_address');
            $table->text('description')->nullable()->after('operating_hours');
        });
    }

    public function down(): void
    {
        Schema::table('seller_registrations', function (Blueprint $table) {
            $table->dropColumn(['operating_hours', 'description']);
        });
    }
};
