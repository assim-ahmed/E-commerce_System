<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            AdminUserSeeder::class,
            UserSeeder::class,          
            CategoryBrandSeeder::class, 
            ProductSeeder::class,
            ProductVariantSeeder::class,        
            AddressSeeder::class,       
            CouponSeeder::class ,         
            ReviewSeeder::class,   
            NotificationSeeder::class,      
        ]);
    }
}
