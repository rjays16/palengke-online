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
        Schema::table('countries', function (Blueprint $table) {
            $table->string('dial_code', 10)->nullable()->after('name');
            $table->integer('digit_count')->nullable()->after('dial_code');
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['dial_code', 'digit_count']);
        });
    }
};
