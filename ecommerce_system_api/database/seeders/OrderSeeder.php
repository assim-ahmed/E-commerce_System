<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\InventoryLog;
use App\Models\User;
use App\Models\Address;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'customer')->get();
        $products = Product::all();
        
        if ($products->isEmpty()) {
            $this->command->error('No products found! Please run ProductSeeder first.');
            return;
        }
        
        foreach ($users as $user) {
            $address = Address::where('user_id', $user->id)->where('is_default', true)->first();
            
            if (!$address) {
                $address = Address::where('user_id', $user->id)->first();
            }
            
            if (!$address) continue;
            
            // Create 1-2 completed orders per user
            $numOrders = rand(1, 2);
            
            for ($i = 0; $i < $numOrders; $i++) {
                // Select random products for this order (1-3 products)
                $orderProducts = $products->random(rand(1, 3));
                $total = 0;
                $orderItemsData = [];
                
                // Calculate total first
                foreach ($orderProducts as $product) {
                    $quantity = rand(1, 2);
                    $price = $product->base_price;
                    $itemTotal = $price * $quantity;
                    $total += $itemTotal;
                    
                    $orderItemsData[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $itemTotal,
                    ];
                }
                
                // Create order
                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(Str::random(13)),
                    'user_id' => $user->id,
                    'address_id' => $address->id,
                    'status' => 'completed',
                    'total' => $total,
                    'coupon_code' => null,
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now(),
                ]);
                
                // Create order items and update stock
                foreach ($orderItemsData as $item) {
                    $product = $item['product'];
                    $quantity = $item['quantity'];
                    $oldStock = $product->stock_quantity;
                    $newStock = $oldStock - $quantity;
                    
                    // Create order item with snapshot
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                        'product_name_snapshot' => $product->name,
                        'price_snapshot' => $item['price'],
                        'quantity' => $quantity,
                        'total' => $item['total'],
                    ]);
                    
                    // Update product stock
                    $product->update(['stock_quantity' => $newStock]);
                    
                    // Create inventory log
                    InventoryLog::create([
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                        'type' => 'sale',
                        'quantity_change' => -$quantity,
                        'quantity_before' => $oldStock,
                        'quantity_after' => $newStock,
                        'created_at' => $order->created_at,
                    ]);
                }
            }
        }
        
        $this->command->info('Orders seeded successfully! Total: ' . Order::count());
        $this->command->info('Order Items seeded successfully! Total: ' . OrderItem::count());
        $this->command->info('Inventory Logs seeded successfully! Total: ' . InventoryLog::count());
    }
}