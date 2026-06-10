<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('payment_methods')->insert([
            'name' => 'Palengke Wallet',
            'description' => 'Pay using your Palengke Wallet balance',
            'color' => '#059669',
            'visible' => true,
            'coming_soon' => false,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        DB::table('payment_methods')->where('name', 'Palengke Wallet')->delete();
    }
};
