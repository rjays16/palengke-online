<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\CountryRegion;
use Illuminate\Database\Seeder;

class CountryRegionSeeder extends Seeder
{
    public function run(): void
    {
        $philippines = Country::where('code', 'PH')->first();
        if (!$philippines) return;

        $regions = ['Luzon', 'Visayas', 'Mindanao'];

        foreach ($regions as $name) {
            CountryRegion::updateOrCreate(
                ['country_id' => $philippines->id, 'name' => $name],
                ['enabled' => false]
            );
        }
    }
}
