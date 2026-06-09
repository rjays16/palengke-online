<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $sellerId = 6; // first seller ID

        $orders = [
            [
                'seller_registration_id' => $sellerId,
                'customer_name' => 'Maria Santos',
                'customer_phone' => '+63 917 123 4567',
                'delivery_address' => '123 Main St, Cagayan de Oro City',
                'latitude' => 8.4542,
                'longitude' => 124.6319,
                'items' => json_encode([
                    ['name' => 'Tomatoes', 'qty' => '1', 'unit' => 'kg', 'price' => 80],
                    ['name' => 'Lettuce', 'qty' => '1/2', 'unit' => 'kg', 'price' => 45],
                    ['name' => 'Carrots', 'qty' => '1.5', 'unit' => 'kg', 'price' => 60],
                    ['name' => 'Onions', 'qty' => '2', 'unit' => 'kg', 'price' => 50],
                    ['name' => 'Potatoes', 'qty' => '3', 'unit' => 'kg', 'price' => 100],
                ]),
                'total' => 335,
                'status' => 'pending',
                'payment_method' => 'cod',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'seller_registration_id' => $sellerId,
                'customer_name' => 'Juan Dela Cruz',
                'customer_phone' => '+63 918 234 5678',
                'delivery_address' => '456 Oak Ave, Cagayan de Oro City',
                'latitude' => 8.4621,
                'longitude' => 124.6285,
                'items' => json_encode([
                    ['name' => 'Cabbage', 'qty' => '2', 'unit' => 'kg', 'price' => 70],
                    ['name' => 'Eggplant', 'qty' => '1.5', 'unit' => 'kg', 'price' => 120],
                    ['name' => 'Bell Peppers', 'qty' => '1', 'unit' => 'kg', 'price' => 150],
                ]),
                'total' => 340,
                'status' => 'processing',
                'payment_method' => 'gcash',
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(4),
            ],
            [
                'seller_registration_id' => $sellerId,
                'customer_name' => 'Ana Reyes',
                'customer_phone' => '+63 919 345 6789',
                'delivery_address' => '789 Pine Rd, Cagayan de Oro City',
                'latitude' => 8.4478,
                'longitude' => 124.6402,
                'items' => json_encode([
                    ['name' => 'Mangoes', 'qty' => '2', 'unit' => 'kg', 'price' => 300],
                    ['name' => 'Bananas', 'qty' => '3', 'unit' => 'kg', 'price' => 150],
                    ['name' => 'Oranges', 'qty' => '2.5', 'unit' => 'kg', 'price' => 200],
                ]),
                'total' => 650,
                'status' => 'completed',
                'payment_method' => 'cod',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay()->addHours(2),
            ],
            [
                'seller_registration_id' => $sellerId,
                'customer_name' => 'Pedro Garcia',
                'customer_phone' => '+63 920 456 7890',
                'delivery_address' => '321 Elm St, Cagayan de Oro City',
                'latitude' => 8.4610,
                'longitude' => 124.6350,
                'items' => json_encode([
                    ['name' => 'Garlic', 'qty' => '1/2', 'unit' => 'kg', 'price' => 90],
                    ['name' => 'Ginger', 'qty' => '1/4', 'unit' => 'kg', 'price' => 80],
                ]),
                'total' => 170,
                'status' => 'cancelled',
                'payment_method' => 'gcash',
                'created_at' => now()->subDay()->subHours(3),
                'updated_at' => now()->subDay()->subHours(2),
            ],
            [
                'seller_registration_id' => $sellerId,
                'customer_name' => 'Lisa Ramos',
                'customer_phone' => '+63 921 567 8901',
                'delivery_address' => '654 Maple Dr, Cagayan de Oro City',
                'latitude' => 8.4495,
                'longitude' => 124.6330,
                'items' => json_encode([
                    ['name' => 'Pork (Liempo)', 'qty' => '1.5', 'unit' => 'kg', 'price' => 350],
                    ['name' => 'Chicken', 'qty' => '2', 'unit' => 'kg', 'price' => 280],
                    ['name' => 'Bangus', 'qty' => '3', 'unit' => 'pcs', 'price' => 200],
                    ['name' => 'Shrimp', 'qty' => '1/2', 'unit' => 'kg', 'price' => 150],
                ]),
                'total' => 980,
                'status' => 'pending',
                'payment_method' => 'cod',
                'created_at' => now()->subHour(),
                'updated_at' => now()->subHour(),
            ],
        ];

        DB::table('orders')->insert($orders);
    }
}
