<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('licences', 'payment_method_name')) {
            Schema::table('licences', function (Blueprint $table) {
                $table->string('payment_method_name')->nullable()->after('amount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('licences', 'payment_method_name')) {
            Schema::table('licences', function (Blueprint $table) {
                $table->dropColumn('payment_method_name');
            });
        }
    }
};
