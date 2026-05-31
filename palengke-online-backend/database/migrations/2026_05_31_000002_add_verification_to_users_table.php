<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('verification_choice')->nullable()->after('status')
                ->comment('verify or skip');
            $table->string('verification_status')->default('not_verified')->after('verification_choice')
                ->comment('not_verified, pending, verified, rejected');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['verification_choice', 'verification_status']);
        });
    }
};
