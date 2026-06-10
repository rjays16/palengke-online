<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_registration_id');
            $table->string('plan_id');
            $table->string('plan_name');
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('payment_method_name')->nullable();
            $table->string('status')->default('completed');
            $table->timestamp('subscribed_at');
            $table->date('plan_start');
            $table->date('plan_end');
            $table->timestamps();

            $table->foreign('seller_registration_id')
                ->references('id')
                ->on('seller_registrations')
                ->onDelete('cascade');

            $table->foreign('payment_method_id')
                ->references('id')
                ->on('payment_methods')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
