<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("seller_registrations", function (Blueprint $table) {
            $table->text("operating_hours")->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table("seller_registrations", function (Blueprint $table) {
            $table->string("operating_hours", 255)->nullable()->change();
        });
    }
};
