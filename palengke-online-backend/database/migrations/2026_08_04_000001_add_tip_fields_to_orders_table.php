<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('delivery_fee', 10, 2)->nullable()->after('total');
            $table->decimal('tip_amount', 10, 2)->nullable()->after('delivery_fee');
            $table->text('tip_message')->nullable()->after('tip_amount');
            $table->string('tip_customer_name')->nullable()->after('tip_message');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_fee', 'tip_amount', 'tip_message', 'tip_customer_name']);
        });
    }
};
