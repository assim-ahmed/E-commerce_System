<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'customer')->get();
        
        foreach ($users as $user) {
            // Get products the user purchased (from order_items)
            $purchasedProductIds = OrderItem::whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'completed');
            })->pluck('product_id')->unique();
            
            $products = Product::whereIn('id', $purchasedProductIds)->get();
            
            foreach ($products as $product) {
                // 60% chance to review
                if (rand(1, 100) <= 60) {
                    // Get the order_id for this purchase
                    $orderItem = OrderItem::where('product_id', $product->id)
                        ->whereHas('order', function ($q) use ($user) {
                            $q->where('user_id', $user->id)->where('status', 'completed');
                        })->first();
                    
                    if (!$orderItem) continue;
                    
                    Review::create([
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'order_id' => $orderItem->order_id,
                        'rating' => rand(3, 5),
                        'comment' => $this->getRandomReviewComment($product->name),
                        'images' => null,
                        'is_approved' => rand(0, 1) === 1, // 50% approved
                        'created_at' => now()->subDays(rand(1, 15)),
                    ]);
                }
            }
        }
        
        // ❌ Comment or remove this line
        // $this->updateProductRatings();
        
        $this->command->info('Reviews seeded successfully! Total: ' . Review::count());
        $this->command->info('Approved reviews: ' . Review::where('is_approved', true)->count());
    }
    
    private function getRandomReviewComment($productName): string
    {
        $comments = [
            "Excellent {$productName}! Very satisfied with my purchase.",
            "Good quality product. Would recommend to others.",
            "Nice {$productName}, works as expected.",
            "Average product, nothing special but gets the job done.",
            "Great value for money. Happy with this purchase.",
            "The {$productName} exceeded my expectations!",
            "Fast shipping and good product quality.",
            "Decent product for the price. No complaints.",
        ];
        
        return $comments[array_rand($comments)];
    }
    

}