<?php

namespace Database\Seeders;

use App\Models\ContactSupport;
use Illuminate\Database\Seeder;

class ContactSupportSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            [
                'key' => 'live_chat',
                'title' => 'Live Chat',
                'subtitle' => 'Chat with our support team',
                'type' => 'chat',
                'value' => null,
                'icon' => 'chat',
                'enabled' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'call',
                'title' => 'Call Us',
                'subtitle' => '+63 912 345 6789',
                'type' => 'phone',
                'value' => '+639123456789',
                'icon' => 'phone',
                'enabled' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'email',
                'title' => 'Email Support',
                'subtitle' => 'support@palengke-online.com',
                'type' => 'email',
                'value' => 'support@palengke-online.com',
                'icon' => 'email',
                'enabled' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'facebook',
                'title' => 'Facebook',
                'subtitle' => 'Message us on Facebook',
                'type' => 'facebook',
                'value' => 'https://facebook.com/PalengkeOnlinePH',
                'icon' => 'facebook',
                'enabled' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($contacts as $contact) {
            ContactSupport::updateOrCreate(
                ['key' => $contact['key']],
                $contact
            );
        }
    }
}
