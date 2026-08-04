<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliveryService;

class DeliveryServiceSeeder extends Seeder
{
    public function run()
    {
        DeliveryService::insert([
            [
                'name' => 'Palengke Standard Delivery',
                'slug' => 'standard',
                'description' => 'Regular delivery orders',
                'icon' => 'bicycle',
                'color' => '#4CAF50',
                'tag' => null,
                'tag_type' => null,
                'always_on' => true,
                'driver_setting' => null,
                'sort_order' => 1,
            ],
            [
                'name' => 'Palengke Express Delivery',
                'slug' => 'express',
                'description' => '15% higher earnings than standard',
                'icon' => 'flash',
                'color' => '#FF9800',
                'tag' => 'Higher Earnings',
                'tag_type' => 'earnings',
                'always_on' => false,
                'driver_setting' => 'express_enabled',
                'sort_order' => 2,
            ],
            [
                'name' => 'Bulky Item Delivery',
                'slug' => 'bulky',
                'description' => 'Large or heavy orders',
                'icon' => 'cube',
                'color' => '#2196F3',
                'tag' => null,
                'tag_type' => null,
                'always_on' => false,
                'driver_setting' => 'bulky_enabled',
                'sort_order' => 3,
            ],
        ]);
    }
}
