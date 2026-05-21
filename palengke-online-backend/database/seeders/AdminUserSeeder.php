<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@palengke.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('superadmin123'),
                'role' => 'superadmin',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@palengke.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );
    }
}
