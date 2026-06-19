<?php

namespace Database\Seeders;

use App\Models\RestrictionCode;
use Illuminate\Database\Seeder;

class RestrictionCodeSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            ['code' => 'A', 'label' => 'Motorcycles/Tricycles (Automatic Clutch)'],
            ['code' => 'A1', 'label' => 'Motorcycles/Tricycles (Manual Clutch)'],
            ['code' => 'B', 'label' => 'Light Vehicles — Automatic Transmission'],
            ['code' => 'B1', 'label' => 'Light Vehicles — Manual Transmission'],
            ['code' => 'B2', 'label' => 'Heavy Vehicles — Automatic Transmission'],
            ['code' => 'C', 'label' => 'Heavy Vehicles — Manual Transmission'],
            ['code' => 'D', 'label' => 'Articulated Vehicles'],
            ['code' => 'BE', 'label' => 'Vehicles with Trailer'],
        ];

        foreach ($codes as $code) {
            RestrictionCode::create($code);
        }
    }
}
