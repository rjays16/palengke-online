<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('help_topics', function (Blueprint $table) {
            $table->dropUnique(['key']);
            $table->unique(['key', 'role']);
        });
    }

    public function down(): void
    {
        Schema::table('help_topics', function (Blueprint $table) {
            $table->dropUnique(['key', 'role']);
            $table->unique('key');
        });
    }
};
