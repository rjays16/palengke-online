<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['code' => 'PH', 'name' => 'Philippines', 'enabled' => true],
            ['code' => 'US', 'name' => 'United States', 'enabled' => false],
            ['code' => 'SG', 'name' => 'Singapore', 'enabled' => false],
            ['code' => 'JP', 'name' => 'Japan', 'enabled' => false],
            ['code' => 'AU', 'name' => 'Australia', 'enabled' => false],
            ['code' => 'CA', 'name' => 'Canada', 'enabled' => false],
            ['code' => 'GB', 'name' => 'United Kingdom', 'enabled' => false],
            ['code' => 'DE', 'name' => 'Germany', 'enabled' => false],
            ['code' => 'FR', 'name' => 'France', 'enabled' => false],
            ['code' => 'KR', 'name' => 'South Korea', 'enabled' => false],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                $country
            );
        }
    }
}
