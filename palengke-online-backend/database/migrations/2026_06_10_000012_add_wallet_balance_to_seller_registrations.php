<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('seller_registrations', function (Blueprint $table) {
            $table->decimal('wallet_balance', 12, 2)->default(0)->after('plan_ends_at');
        });

        DB::table('seller_registrations')->where('email', 'rjaycondiman81@gmail.com')->update(['wallet_balance' => 1250.00]);
    }

    public function down()
    {
        Schema::table('seller_registrations', function (Blueprint $table) {
            $table->dropColumn('wallet_balance');
        });
    }
};
