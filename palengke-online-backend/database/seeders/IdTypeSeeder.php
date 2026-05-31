<?php

namespace Database\Seeders;

use App\Models\IdType;
use Illuminate\Database\Seeder;

class IdTypeSeeder extends Seeder
{
    public function run(): void
    {
        $idTypes = [
            // Primary IDs
            ['key' => 'passport', 'name' => 'Philippine Passport', 'group' => 'primary'],
            ['key' => 'drivers_license', 'name' => "Driver's License", 'group' => 'primary'],
            ['key' => 'umid', 'name' => 'UMID (Unified Multi-Purpose ID)', 'group' => 'primary'],
            ['key' => 'sss', 'name' => 'SSS ID', 'group' => 'primary'],
            ['key' => 'philhealth', 'name' => 'PhilHealth ID', 'group' => 'primary'],
            ['key' => 'pagibig', 'name' => 'Pag-IBIG ID', 'group' => 'primary'],
            ['key' => 'postal', 'name' => 'Postal ID', 'group' => 'primary'],

            // Government IDs
            ['key' => 'voters', 'name' => "Voter's ID", 'group' => 'government'],
            ['key' => 'tin', 'name' => 'TIN ID', 'group' => 'government'],
            ['key' => 'national_id', 'name' => 'National ID (PhilSys)', 'group' => 'government'],
            ['key' => 'senior_citizen', 'name' => 'Senior Citizen ID', 'group' => 'government'],
            ['key' => 'pwd', 'name' => 'PWD ID', 'group' => 'government'],
            ['key' => 'owwa', 'name' => 'OWWA ID', 'group' => 'government'],
            ['key' => 'seaman', 'name' => "Seaman's Book", 'group' => 'government'],

            // Professional IDs
            ['key' => 'prc', 'name' => 'PRC ID (Professional Regulation Commission)', 'group' => 'professional'],
            ['key' => 'company', 'name' => 'Company ID (with DTI/SEC registration)', 'group' => 'professional'],
            ['key' => 'student', 'name' => 'School ID (with NSO Birth Certificate)', 'group' => 'professional'],
        ];

        foreach ($idTypes as $idType) {
            IdType::updateOrCreate(
                ['key' => $idType['key']],
                $idType
            );
        }
    }
}
