<?php

namespace Database\Seeders;

use App\Models\RestrictionCode;
use Illuminate\Database\Seeder;

class RestrictionCodeSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            ['code' => 'A', 'label' => 'Motorcycles/Tricycles (Automatic Clutch)', 'vehicle_types' => ['Scooter', 'Motorcycle (Automatic)', 'Motorized Tricycle']],
            ['code' => 'A1', 'label' => 'Motorcycles/Tricycles (Manual Clutch)', 'vehicle_types' => ['Motorcycle (Manual)', 'Underbone Motorcycle', 'Motorized Tricycle']],
            ['code' => 'B', 'label' => 'Light Vehicles — Automatic Transmission', 'vehicle_types' => ['Sedan', 'AUV', 'SUV', 'Pickup', 'Van', 'Jeepney', 'Multicab', 'UV Express']],
            ['code' => 'B1', 'label' => 'Light Vehicles — Manual Transmission', 'vehicle_types' => ['Sedan', 'AUV', 'SUV', 'Pickup', 'Van', 'Jeepney', 'Multicab', 'UV Express']],
            ['code' => 'B2', 'label' => 'Heavy Vehicles — Automatic Transmission', 'vehicle_types' => ['Bus', 'Delivery Van', 'Wing Van Truck', 'Dump Truck']],
            ['code' => 'C', 'label' => 'Heavy Vehicles — Manual Transmission', 'vehicle_types' => ['Bus', 'Delivery Van', 'Wing Van Truck', 'Dump Truck']],
            ['code' => 'D', 'label' => 'Articulated Vehicles', 'vehicle_types' => ['Trailer Truck', 'Container Truck', 'Articulated Bus']],
            ['code' => 'BE', 'label' => 'Vehicles with Trailer', 'vehicle_types' => ['Car with Trailer', 'Van with Trailer']],
        ];

        foreach ($codes as $code) {
            RestrictionCode::updateOrCreate(['code' => $code['code']], $code);
        }
    }
}
