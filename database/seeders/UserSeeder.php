<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 🔑 Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@yopmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('12345678'),
            ]
        );
        $admin->assignRole('Admin');

        // 🧑‍🏫 Warden User
        $warden = User::firstOrCreate(
            ['email' => 'warden@yopmail.com'],
            [
                'name' => 'Hostel Warden',
                'password' => Hash::make('12345678'),
            ]
        );
        $warden->assignRole('Hostel Warden');

        // 🍽️ Mess Manager User
        $messManager = User::firstOrCreate(
            ['email' => 'mess@yopmail.com'],
            [
                'name' => 'Mess Manager',
                'password' => Hash::make('12345678'),
            ]
        );
        $messManager->assignRole('Mess Manager');
    }
}
