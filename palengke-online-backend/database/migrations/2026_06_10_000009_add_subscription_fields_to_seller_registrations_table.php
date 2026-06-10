<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_registrations', function (Blueprint $table) {
            $table->timestamp('trial_started_at')->nullable()->after('monthly_orders_target');
            $table->timestamp('trial_ends_at')->nullable()->after('trial_started_at');
            $table->string('subscription_status')->default('trial')->after('trial_ends_at');
            $table->string('plan_id')->nullable()->after('subscription_status');
            $table->timestamp('plan_started_at')->nullable()->after('plan_id');
            $table->timestamp('plan_ends_at')->nullable()->after('plan_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('seller_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'trial_started_at', 'trial_ends_at', 'subscription_status',
                'plan_id', 'plan_started_at', 'plan_ends_at',
            ]);
        });
    }
};
