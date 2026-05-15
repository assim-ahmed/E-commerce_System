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
        $this->command->info('🚀 Starting database seeding...');

        // Order matters for foreign key constraints
        $this->call([
            AdminUserSeeder::class,
            UserSeeder::class,
            CategoryBrandSeeder::class,
            ProductSeeder::class,
            ProductVariantSeeder::class,
            AddressSeeder::class,
            CouponSeeder::class,
        ]);

        $this->command->info('✅ Products and variants seeded');

        // Orders and related data
        $this->call([
            OrderSeeder::class,
            OrderItemSeeder::class,
        ]);

        $this->command->info('✅ Orders seeded');

        // Reviews and inventory logs
        $this->call([
            ReviewSeeder::class,
            InventoryLogSeeder::class,
            NotificationSeeder::class,
        ]);

        $this->command->info('🎉 Database seeding completed successfully!');

        // Summary
        $this->command->info("\n📊 Final Statistics:");
        $this->command->info("   - Users: " . \App\Models\User::count());
        $this->command->info("   - Products: " . \App\Models\Product::count());
        $this->command->info("   - Orders: " . \App\Models\Order::count());
        $this->command->info("   - Reviews: " . \App\Models\Review::count());
        $this->command->info("   - Inventory Logs: " . \App\Models\InventoryLog::count());
    }
}
