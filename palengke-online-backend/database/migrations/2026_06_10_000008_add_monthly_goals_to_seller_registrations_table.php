<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_registrations', function (Blueprint $table) {
            $table->decimal('monthly_sales_target', 12, 2)->default(50000)->after('avatar');
            $table->integer('monthly_orders_target')->default(100)->after('monthly_sales_target');
        });
    }

    public function down(): void
    {
        Schema::table('seller_registrations', function (Blueprint $table) {
            $table->dropColumn(['monthly_sales_target', 'monthly_orders_target']);
        });
    }
};
