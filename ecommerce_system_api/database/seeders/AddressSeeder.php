<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'customer')->get();
        
        foreach ($users as $index => $user) {
            // Default address
            Address::create([
                'user_id' => $user->id,
                'address_line_1' => "{$user->name} Street, Building No. " . ($index + 1),
                'city' => 'Cairo',
                'country' => 'Egypt',
                'is_default' => true,
            ]);
            
            // Second address for some users
            if ($index % 2 == 0) {
                Address::create([
                    'user_id' => $user->id,
                    'address_line_1' => "Second Address, District " . ($index + 1),
                    'city' => 'Alexandria',
                    'country' => 'Egypt',
                    'is_default' => false,
                ]);
            }
        }
        
        $this->command->info('Addresses seeded successfully! Total: ' . Address::count());
    }
}