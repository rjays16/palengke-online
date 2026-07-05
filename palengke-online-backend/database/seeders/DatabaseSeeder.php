<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            CountrySeeder::class,
            IdTypeSeeder::class,
            ContactSupportSeeder::class,
            SystemSettingSeeder::class,
            OrderSeeder::class,
            PaymentMethodSeeder::class,
            RestrictionCodeSeeder::class,
            HelpTopicSeeder::class,
            FaqSeeder::class,
            CountryRegionSeeder::class,
            DriverDocumentSeeder::class,
        ]);
    }
}
