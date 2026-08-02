<?php

namespace Database\Seeders;

use App\Models\LicenseType;
use Illuminate\Database\Seeder;

class LicenseTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Student Permit', 'description' => 'For student drivers (must be accompanied by a licensed driver)'],
            ['name' => 'Non-Professional', 'description' => 'For private, non-commercial driving'],
            ['name' => 'Professional', 'description' => 'For commercial and professional driving'],
        ];

        foreach ($types as $type) {
            LicenseType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}
