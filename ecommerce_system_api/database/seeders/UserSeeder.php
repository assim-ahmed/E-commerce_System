<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Ahmed Mohamed', 'email' => 'ahmed@example.com'],
            ['name' => 'Sara Ali', 'email' => 'sara@example.com'],
            ['name' => 'Mohamed Hassan', 'email' => 'mohamed@example.com'],
            ['name' => 'Fatima Essam', 'email' => 'fatima@example.com'],
            ['name' => 'Omar Khaled', 'email' => 'omar@example.com'],
            ['name' => 'Nour Ahmed', 'email' => 'nour@example.com'],
            ['name' => 'Youssef Emad', 'email' => 'youssef@example.com'],
            ['name' => 'Mariam Tarek', 'email' => 'mariam@example.com'],
        ];

        foreach ($customers as $customer) {
            User::updateOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'email' => $customer['email'],
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Users seeded successfully! Total: ' . User::where('role', 'customer')->count());
    }
}