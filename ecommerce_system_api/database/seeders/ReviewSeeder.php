<?php
// database/seeders/ReviewSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Get completed orders with their items
        $completedOrders = Order::where('status', 'completed')->with('items.product')->get();
        
        if ($completedOrders->isEmpty()) {
            $this->command->warn('⚠️ No completed orders found. Run OrderSeeder and OrderItemSeeder first.');
            return;
        }
        
        $reviews = [];
        $existingReviews = []; // To prevent duplicate reviews
        
        $reviewTexts = [
            'منتج رائع جداً، أنصح به بشدة',
            'جودة ممتازة وسعر مناسب',
            'المنتج كما هو موصوف، شكراً',
            'تجربة شراء ممتازة، سأكررها',
            'منتج جيد لكن السعر مرتفع قليلاً',
            'الخامة ممتازة والتغليف جيد',
            'لم يعجبني المنتج، ليس كما توقعت',
            'جيد لكن يحتاج بعض التحسينات',
            'منتج ممتاز وسرعة في التوصيل',
            'يستحق الشراء، أنصح به',
        ];
        
        foreach ($completedOrders as $order) {
            foreach ($order->items as $item) {
                $product = $item->product;
                
                // Skip if already reviewed this product in this order
                $reviewKey = $order->user_id . '_' . $product->id . '_' . $order->id;
                if (in_array($reviewKey, $existingReviews)) {
                    continue;
                }
                
                // 70% chance to leave a review
                if (rand(1, 100) <= 70) {
                    $rating = rand(1, 5);
                    $comment = $reviewTexts[array_rand($reviewTexts)];
                    
                    // Add product-specific comment based on rating
                    if ($rating >= 4) {
                        $comment = '👍 ' . $comment;
                    } elseif ($rating <= 2) {
                        $comment = '👎 ' . $comment;
                    }
                    
                    $reviews[] = [
                        'user_id' => $order->user_id,
                        'product_id' => $product->id,
                        'order_id' => $order->id,
                        'rating' => $rating,
                        'comment' => $comment,
                        'images' => rand(0, 1) ? json_encode(['review_image_1.jpg']) : null,
                        'is_approved' => $rating >= 3 ? true : false, // Auto-approve good reviews
                        'created_at' => Carbon::now()->subDays(rand(0, 30)),
                        'updated_at' => Carbon::now(),
                    ];
                    
                    $existingReviews[] = $reviewKey;
                }
            }
        }
        
        // Insert reviews
        foreach ($reviews as $review) {
            Review::updateOrCreate(
                [
                    'user_id' => $review['user_id'],
                    'product_id' => $review['product_id'],
                    'order_id' => $review['order_id'],
                ],
                $review
            );
        }
        
        $this->command->info('✅ Reviews seeded: ' . Review::count() . ' reviews created');
        $this->command->info('   - Approved: ' . Review::where('is_approved', true)->count());
        $this->command->info('   - Pending: ' . Review::where('is_approved', false)->count());
    }
}