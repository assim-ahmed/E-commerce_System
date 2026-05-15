<?php
// database/seeders/OrderItemSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();
        $products = Product::all();
        
        foreach ($orders as $order) {
            // Each order has 1-5 items
            $numItems = rand(1, 5);
            $orderTotal = 0;
            
            for ($i = 0; $i < $numItems; $i++) {
                $product = $products->random();
                $variant = ProductVariant::where('product_id', $product->id)->first();
                $quantity = rand(1, 3);
                
                $price = $variant 
                    ? $product->base_price + $variant->price_adjustment 
                    : $product->base_price;
                
                $total = $price * $quantity;
                $orderTotal += $total;
                
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name_snapshot' => $product->name,
                    'price_snapshot' => $price,
                    'quantity' => $quantity,
                    'total' => $total,
                ]);
                
                // Update product stock
                if ($order->status === 'completed') {
                    $product->decrement('stock_quantity', $quantity);
                }
            }
            
            // Update order total to match items sum
            if ($order->status !== 'cancelled') {
                $order->update(['total' => $orderTotal]);
            }
        }
        
        $this->command->info('✅ Order items seeded successfully');
    }
}