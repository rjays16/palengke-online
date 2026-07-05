<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_documents', function (Blueprint $table) {
            $table->id();
            $table->string('driver_email');
            $table->string('name');
            $table->string('type');
            $table->text('file_path')->nullable();
            $table->string('expiry_date')->nullable();
            $table->string('status')->default('missing');
            $table->timestamps();

            $table->index('driver_email');
            $table->unique(['driver_email', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_documents');
    }
};
