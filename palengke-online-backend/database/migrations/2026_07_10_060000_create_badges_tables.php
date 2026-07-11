<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('icon_name');
            $table->string('color')->default('#4CAF50');
            $table->string('criteria_type')->default('trips_count');
            $table->integer('criteria_value');
            $table->timestamps();
        });

        Schema::create('driver_badge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_registration_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->boolean('is_new')->default(true);
            $table->timestamp('awarded_at')->useCurrent();
            $table->unique(['driver_registration_id', 'badge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_badge');
        Schema::dropIfExists('badges');
    }
};
