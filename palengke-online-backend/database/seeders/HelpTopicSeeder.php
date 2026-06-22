<?php

namespace Database\Seeders;

use App\Models\HelpTopic;
use Illuminate\Database\Seeder;

class HelpTopicSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            // Driver topics
            ['key' => 'all', 'name' => 'All', 'icon' => 'help-circle', 'role' => 'driver', 'enabled' => true, 'sort_order' => 1],
            ['key' => 'earnings', 'name' => 'Earnings', 'icon' => 'cash', 'role' => 'driver', 'enabled' => true, 'sort_order' => 2],
            ['key' => 'orders', 'name' => 'Orders', 'icon' => 'cube', 'role' => 'driver', 'enabled' => true, 'sort_order' => 3],
            ['key' => 'account', 'name' => 'Account', 'icon' => 'person', 'role' => 'driver', 'enabled' => true, 'sort_order' => 4],
            ['key' => 'delivery', 'name' => 'Delivery', 'icon' => 'bicycle', 'role' => 'driver', 'enabled' => true, 'sort_order' => 5],
            // Seller topics
            ['key' => 'all', 'name' => 'All', 'icon' => 'help-circle', 'role' => 'seller', 'enabled' => true, 'sort_order' => 1],
            ['key' => 'getting-started', 'name' => 'Getting Started', 'icon' => 'rocket', 'role' => 'seller', 'enabled' => true, 'sort_order' => 2],
            ['key' => 'orders', 'name' => 'Managing Orders', 'icon' => 'cube', 'role' => 'seller', 'enabled' => true, 'sort_order' => 3],
            ['key' => 'products', 'name' => 'Products', 'icon' => 'storefront', 'role' => 'seller', 'enabled' => true, 'sort_order' => 4],
            ['key' => 'payments', 'name' => 'Payments', 'icon' => 'cash', 'role' => 'seller', 'enabled' => true, 'sort_order' => 5],
            ['key' => 'account', 'name' => 'Account Settings', 'icon' => 'person', 'role' => 'seller', 'enabled' => true, 'sort_order' => 6],
            ['key' => 'troubleshooting', 'name' => 'Troubleshooting', 'icon' => 'warning', 'role' => 'seller', 'enabled' => true, 'sort_order' => 7],
        ];

        foreach ($topics as $topic) {
            HelpTopic::updateOrCreate(
                ['key' => $topic['key'], 'role' => $topic['role']],
                $topic
            );
        }

        $this->command->info('Seeded ' . count($topics) . ' help topics.');
    }
}
