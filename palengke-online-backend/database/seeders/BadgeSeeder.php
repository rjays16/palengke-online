<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        Badge::upsert([
            [
                'name' => 'First Delivery',
                'description' => 'Completed your first delivery',
                'icon_name' => 'checkmarkCircleOutline',
                'color' => '#4CAF50',
                'criteria_type' => 'trips_count',
                'criteria_value' => 1,
            ],
            [
                'name' => 'Rising Star',
                'description' => 'Completed 25 deliveries',
                'icon_name' => 'starOutline',
                'color' => '#FFD700',
                'criteria_type' => 'trips_count',
                'criteria_value' => 25,
            ],
            [
                'name' => 'Fast & Furious',
                'description' => 'Completed 50 deliveries',
                'icon_name' => 'flashOutline',
                'color' => '#2196F3',
                'criteria_type' => 'trips_count',
                'criteria_value' => 50,
            ],
            [
                'name' => 'Road Warrior',
                'description' => 'Completed 100 deliveries',
                'icon_name' => 'trophyOutline',
                'color' => '#FFD700',
                'criteria_type' => 'trips_count',
                'criteria_value' => 100,
            ],
            [
                'name' => 'Dedicated Driver',
                'description' => 'Completed 200 deliveries',
                'icon_name' => 'heartOutline',
                'color' => '#E91E63',
                'criteria_type' => 'trips_count',
                'criteria_value' => 200,
            ],
        ], ['criteria_type', 'criteria_value'], ['name', 'description', 'icon_name', 'color']);
    }
}
