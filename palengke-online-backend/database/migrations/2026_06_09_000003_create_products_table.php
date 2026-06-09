<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_registration_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('category');
            $table->text('description')->nullable();
            $table->string('selling_unit')->default('kilo');
            $table->decimal('price', 10, 2);
            $table->decimal('minimum_order', 10, 2)->nullable();
            $table->boolean('has_discount')->default(false);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->decimal('stock_quantity', 10, 2)->default(0);
            $table->string('sku')->nullable();
            $table->boolean('track_inventory')->default(true);
            $table->string('origin')->nullable();
            $table->string('brand')->nullable();
            $table->boolean('is_organic')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('images')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
