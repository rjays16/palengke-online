<?php

namespace Database\Seeders;

use App\Models\SellingUnit;
use Illuminate\Database\Seeder;

class SellingUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'code' => 'kilo',
                'label' => 'Per Kilo',
                'plural' => 'kilos',
                'icon' => 'kilo',
                'has_min_order' => true,
                'example_stock' => 50,
                'sort_order' => 1,
            ],
            [
                'code' => 'piece',
                'label' => 'Per Piece',
                'plural' => 'pieces',
                'icon' => 'piece',
                'has_min_order' => false,
                'example_stock' => 100,
                'sort_order' => 2,
            ],
            [
                'code' => 'pack',
                'label' => 'Per Pack',
                'plural' => 'packs',
                'icon' => 'pack',
                'has_min_order' => false,
                'example_stock' => 20,
                'sort_order' => 3,
            ],
        ];

        foreach ($units as $unit) {
            SellingUnit::updateOrCreate(
                ['code' => $unit['code']],
                $unit
            );
        }
    }
}
