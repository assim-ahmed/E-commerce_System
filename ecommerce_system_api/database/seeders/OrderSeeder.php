<?php
// database/seeders/OrderSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Address;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'customer')->get();
        $addresses = Address::all();
        
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $orderNumbers = [];
        
        // Create 50 orders with different dates
        for ($i = 1; $i <= 50; $i++) {
            $user = $users->random();
            $address = $addresses->where('user_id', $user->id)->first() ?? $addresses->first();
            $status = $statuses[array_rand($statuses)];
            
            // Generate unique order number
            do {
                $orderNumber = 'ORD-' . strtoupper(uniqid());
            } while (in_array($orderNumber, $orderNumbers));
            $orderNumbers[] = $orderNumber;
            
            // Random date in last 6 months
            $createdAt = Carbon::now()->subDays(rand(0, 180));
            
            // Random total between 50 and 5000
            $total = rand(5000, 500000) / 100;
            
            Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'address_id' => $address->id,
                'status' => $status,
                'total' => $total,
                'coupon_code' => rand(0, 1) ? 'WELCOME10' : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
        
        $this->command->info('✅ Orders seeded: ' . Order::count() . ' orders created');
    }
}