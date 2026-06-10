<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        PaymentMethod::insert([
            [
                'name' => 'GCash',
                'description' => 'Mobile wallet payments',
                'color' => '#059669',
                'visible' => true,
                'coming_soon' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'PayPal',
                'description' => 'Pay with PayPal account',
                'color' => '#2563eb',
                'visible' => true,
                'coming_soon' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Stripe',
                'description' => 'Credit/debit card payments',
                'color' => '#7c3aed',
                'visible' => true,
                'coming_soon' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Cash on Delivery',
                'description' => 'Pay when order arrives',
                'color' => '#d97706',
                'visible' => true,
                'coming_soon' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Palengke Wallet',
                'description' => 'Pay using your Palengke Wallet balance',
                'color' => '#059669',
                'visible' => true,
                'coming_soon' => false,
                'sort_order' => 0,
            ],
        ]);
    }
}
