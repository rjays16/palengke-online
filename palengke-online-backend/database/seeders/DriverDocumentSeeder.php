<?php

namespace Database\Seeders;

use App\Models\DriverDocument;
use App\Models\DriverRegistration;
use Illuminate\Database\Seeder;

class DriverDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['type' => 'drivers_license', 'name' => "Driver's License"],
            ['type' => 'vehicle_registration', 'name' => 'Vehicle Registration (OR/CR)'],
            ['type' => 'nbi_clearance', 'name' => 'NBI or Police Clearance'],
            ['type' => 'birth_certificate', 'name' => 'Birth Certificate'],
            ['type' => 'selfie_photo', 'name' => 'Selfie Photo'],
            ['type' => 'official_receipt', 'name' => 'Official Receipt (OR)'],
            ['type' => 'business_permit', 'name' => 'Business or Delivery Permit'],
            ['type' => 'identity_card', 'name' => 'Identity Card'],
        ];

        $registrations = DriverRegistration::all();

        foreach ($registrations as $reg) {
            foreach ($types as $typeInfo) {
                DriverDocument::firstOrCreate(
                    [
                        'driver_email' => $reg->email,
                        'type' => $typeInfo['type'],
                    ],
                    [
                        'name' => $typeInfo['name'],
                        'file_path' => null,
                        'expiry_date' => null,
                        'status' => 'missing',
                    ]
                );
            }
        }

        $this->command->info('Seeded driver documents for ' . $registrations->count() . ' registrations.');
    }
}
