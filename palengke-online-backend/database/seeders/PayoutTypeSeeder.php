<?php

namespace Database\Seeders;

use App\Models\PayoutType;
use Illuminate\Database\Seeder;

class PayoutTypeSeeder extends Seeder
{
    public function run(): void
    {
        $payoutTypes = [
            ['key' => 'gcash', 'name' => 'GCash'],
            ['key' => 'bdo', 'name' => 'BDO'],
            ['key' => 'bpi', 'name' => 'BPI'],
            ['key' => 'metrobank', 'name' => 'Metrobank'],
            ['key' => 'landbank', 'name' => 'Landbank'],
            ['key' => 'unionbank', 'name' => 'Unionbank'],
            ['key' => 'pnb', 'name' => 'PNB'],
        ];

        foreach ($payoutTypes as $payoutType) {
            PayoutType::updateOrCreate(
                ['key' => $payoutType['key']],
                $payoutType
            );
        }
    }
}
