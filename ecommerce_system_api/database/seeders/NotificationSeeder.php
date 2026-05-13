<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // جلب المستخدمين
        $customers = User::where('role', 'customer')->get();
        $admins = User::where('role', 'admin')->get();

        // ========== 1. إشعارات للمستخدمين العاديين (Customers) ==========

        foreach ($customers as $customer) {
            // إشعار ترحيبي
            Notification::create([
                'user_id' => $customer->id,
                'title' => 'Welcome to Our Store! 🎉',
                'message' => "Welcome {$customer->name}! We're excited to have you. Start shopping and enjoy exclusive deals.",
                'is_read' => false,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            // إشعار خصم خاص
            Notification::create([
                'user_id' => $customer->id,
                'title' => 'Special Discount for You! 🔥',
                'message' => 'Use code WELCOME10 to get 10% off on your first order. Limited time offer!',

                'is_read' => rand(0, 1) === 1,
                'created_at' => now()->subDays(rand(1, 20)),
            ]);

            // إشعار طلب (لو عنده طلبات)
            $order = Order::where('user_id', $customer->id)->first();
            if ($order) {
                Notification::create([
                    'user_id' => $customer->id,
                    'title' => 'Order Confirmed ✅',
                    'message' => "Your order #{$order->order_number} has been confirmed. Total: {$order->total} EGP",

                    'is_read' => rand(0, 1) === 1,
                    'created_at' => $order->created_at,
                ]);

                if ($order->status === 'delivered') {
                    Notification::create([
                        'user_id' => $customer->id,
                        'title' => 'Order Delivered! 🚚',
                        'message' => "Your order #{$order->order_number} has been delivered. Enjoy your purchase!",

                        'is_read' => rand(0, 1) === 1,
                        'created_at' => $order->updated_at,
                    ]);
                }
            }
        }

        // ========== 2. إشعارات خاصة للمستخدم الأول (Ahmed) ==========
        $ahmed = User::where('email', 'ahmed@example.com')->first();
        if ($ahmed) {
            // إشعار تقييم
            Notification::create([
                'user_id' => $ahmed->id,
                'title' => 'Review Approved! ⭐',
                'message' => 'Your review for "MacBook Pro 14" has been approved and is now visible to others.',

                'is_read' => false,
                'created_at' => now()->subDays(3),
            ]);

            // إشعار شحن
            Notification::create([
                'user_id' => $ahmed->id,
                'title' => 'Your Order Has Been Shipped 📦',
                'message' => 'Great news! Your order #ORD-12345 has been shipped and will arrive within 3-5 business days.',
                'is_read' => true,

                'created_at' => now()->subDays(5),
            ]);

            // إشعار منتج جديد
            Notification::create([
                'user_id' => $ahmed->id,
                'title' => 'New Products Just Arrived! 🆕',
                'message' => 'Check out our latest collection of smartphones and accessories. Limited stock available!',
                'is_read' => false,
                'created_at' => now()->subDays(1),
            ]);
        }

        // ========== 3. إشعارات خاصة للمستخدم الثاني (Sara) ==========
        $sara = User::where('email', 'sara@example.com')->first();
        if ($sara) {
            Notification::create([
                'user_id' => $sara->id,
                'title' => 'Flash Sale! ⚡',
                'message' => '24-hour flash sale! Get up to 50% off on selected items. Hurry up!',

                'is_read' => false,
                'created_at' => now()->subHours(5),
            ]);

            Notification::create([
                'user_id' => $sara->id,
                'title' => 'Cart Reminder 🛒',
                'message' => 'You have items in your cart. Complete your purchase before they run out!',
                'is_read' => false,
                'created_at' => now()->subDays(2),
            ]);
        }

        // ========== 4. إشعارات للمسؤولين (Admins) ==========
        foreach ($admins as $admin) {
            // إشعار طلبات جديدة
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'New Orders Waiting 📋',
                'message' => 'You have 3 new orders pending processing. Please review them.',
                'is_read' => false,
                'created_at' => now()->subHours(2),
            ]);

            // إشعار تقييمات بانتظار الموافقة
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'Reviews Pending Approval ⭐',
                'message' => '2 new customer reviews are waiting for your approval.',
                'is_read' => rand(0, 1) === 1,
                'created_at' => now()->subDays(1),
            ]);

            // إشعار مخزون منخفض
            $lowStockProducts = Product::where('stock_quantity', '<=', 10)->count();
            if ($lowStockProducts > 0) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => '⚠️ Low Stock Alert',
                    'message' => "{$lowStockProducts} products are running low on stock. Please restock soon.",

                    'is_read' => false,
                    'created_at' => now()->subHours(12),
                ]);
            }

            // إشعار مبيعات
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'Weekly Sales Report 📊',
                'message' => 'This week\'s sales: 45 orders totaling 125,000 EGP. Great performance!',

                'is_read' => true,
                'created_at' => now()->subDays(2),
            ]);

            // إشعار مستخدمين جدد
            $newUsersCount = User::where('created_at', '>=', now()->subWeek())->count();
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'New Users Registered 👤',
                'message' => "{$newUsersCount} new customers joined this week. Welcome them!",
                'is_read' => false,
                'created_at' => now()->subDays(1),
            ]);
        }

        // ========== 5. إشعارات عامة إضافية ==========

        // إشعار صيانة (للكل)
        $allUsers = User::all();
        foreach ($allUsers as $user) {
            if (rand(1, 100) <= 30) { // 30% chance
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Site Maintenance Notice 🔧',
                    'message' => 'We will be performing scheduled maintenance on Sunday from 2 AM to 4 AM. Service may be temporarily unavailable.',

                    'is_read' => false,
                    'created_at' => now()->addDays(3), // مستقبلي
                ]);
            }
        }

        // إشعار عيد ميلاد للمستخدم الأول
        if ($ahmed) {
            Notification::create([
                'user_id' => $ahmed->id,
                'title' => 'Happy Birthday! 🎂🎉',
                'message' => "Happy Birthday {$ahmed->name}! Enjoy 20% discount on your next purchase. Use code: BDAY20",
                'is_read' => false,
                'created_at' => now()->subDays(10),
            ]);
        }

        $this->command->info('Notifications seeded successfully!');
        $this->command->info('Total notifications: ' . Notification::count());
        $this->command->info('Unread notifications: ' . Notification::where('is_read', false)->count());
        $this->command->info('Read notifications: ' . Notification::where('is_read', true)->count());

        // عرض إحصائيات لكل مستخدم
        $this->command->info("\n📊 Notifications per user:");
        $users = User::all();
        foreach ($users as $user) {
            $count = Notification::where('user_id', $user->id)->count();
            $unread = Notification::where('user_id', $user->id)->where('is_read', false)->count();
            $this->command->info("  - {$user->name} ({$user->email}): {$count} total, {$unread} unread");
        }
    }
}
