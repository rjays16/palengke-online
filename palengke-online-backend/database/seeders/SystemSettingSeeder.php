<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        SystemSetting::setValue('enable_seller_payment_methods', 'true');
        SystemSetting::setValue('allow_skip_verification', 'true');
        SystemSetting::setValue('enable_calling', 'true');
    }
}
