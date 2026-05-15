<?php
// database/seeders/InventoryLogSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\InventoryLog;
use App\Models\Order;
use Carbon\Carbon;

class InventoryLogSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::with('variants')->get();
        
        if ($products->isEmpty()) {
            $this->command->warn('⚠️ No products found. Run ProductSeeder first.');
            return;
        }
        
        $totalLogs = 0;
        
        foreach ($products as $product) {
            // Get initial stock (before any sales)
            $initialStock = $product->stock_quantity + $this->getTotalSoldQuantity($product);
            
            // 1. Create initial purchase log
            if ($initialStock > 0) {
                InventoryLog::create([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'type' => 'purchase',
                    'quantity_change' => $initialStock,
                    'quantity_before' => 0,
                    'quantity_after' => $initialStock,
                    'created_at' => Carbon::now()->subMonths(6),
                ]);
                $totalLogs++;
            }
            
            $runningStock = $initialStock;
            
            // 2. Create sales logs from completed orders
            $salesLogs = $this->getSalesLogsForProduct($product);
            foreach ($salesLogs as $sale) {
                $quantityBefore = $runningStock;
                $runningStock -= $sale['quantity'];
                
                InventoryLog::create([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'type' => 'sale',
                    'quantity_change' => -$sale['quantity'],
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $runningStock,
                    'created_at' => $sale['date'],
                ]);
                $totalLogs++;
            }
            
            // 3. Create purchase logs (restocking)
            $restockCount = rand(1, 5);
            for ($i = 0; $i < $restockCount; $i++) {
                $restockQuantity = rand(10, 50);
                $quantityBefore = $runningStock;
                $runningStock += $restockQuantity;
                
                InventoryLog::create([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'type' => 'purchase',
                    'quantity_change' => $restockQuantity,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $runningStock,
                    'created_at' => Carbon::now()->subDays(rand(1, 90)),
                ]);
                $totalLogs++;
            }
            
            // 4. Create adjustment logs (random adjustments)
            $adjustmentCount = rand(0, 3);
            for ($i = 0; $i < $adjustmentCount; $i++) {
                $adjustment = rand(-5, 10);
                if ($adjustment == 0) continue;
                
                $quantityBefore = $runningStock;
                $runningStock = max(0, $runningStock + $adjustment);
                
                InventoryLog::create([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'type' => 'adjustment',
                    'quantity_change' => $adjustment,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $runningStock,
                    'created_at' => Carbon::now()->subDays(rand(1, 60)),
                ]);
                $totalLogs++;
            }
            
            // 5. Create return logs (for completed orders)
            $returnCount = rand(0, 2);
            for ($i = 0; $i < $returnCount; $i++) {
                $returnQuantity = rand(1, 3);
                $quantityBefore = $runningStock;
                $runningStock += $returnQuantity;
                
                InventoryLog::create([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'type' => 'return',
                    'quantity_change' => $returnQuantity,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $runningStock,
                    'created_at' => Carbon::now()->subDays(rand(1, 45)),
                ]);
                $totalLogs++;
            }
            
            // Update final stock to match our calculations
            $product->update(['stock_quantity' => $runningStock]);
        }
        
        // Also create logs for product variants
        $variants = ProductVariant::all();
        foreach ($variants as $variant) {
            $variantLogs = rand(0, 5);
            for ($i = 0; $i < $variantLogs; $i++) {
                $change = rand(-5, 20);
                if ($change == 0) continue;
                
                $type = $change > 0 ? 'purchase' : 'sale';
                $quantityBefore = $variant->stock_quantity;
                $quantityAfter = max(0, $quantityBefore + $change);
                
                InventoryLog::create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'type' => $type,
                    'quantity_change' => $change,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $quantityAfter,
                    'created_at' => Carbon::now()->subDays(rand(1, 60)),
                ]);
                
                $variant->update(['stock_quantity' => $quantityAfter]);
                $totalLogs++;
            }
        }
        
        $this->command->info('✅ Inventory logs seeded: ' . $totalLogs . ' logs created');
        
        // Summary
        $this->command->info("\n📊 Inventory Summary:");
        $this->command->info("   - Total products: " . Product::count());
        $this->command->info("   - Total stock: " . Product::sum('stock_quantity'));
        $this->command->info("   - Low stock products: " . Product::where('is_low_stock', true)->count());
        $this->command->info("   - Out of stock: " . Product::where('stock_quantity', '<=', 0)->count());
    }
    
    /**
     * Get total sold quantity for a product from completed orders
     */
    private function getTotalSoldQuantity($product): int
    {
        return \App\Models\OrderItem::where('product_id', $product->id)
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed');
            })
            ->sum('quantity');
    }
    
    /**
     * Get sales logs with dates for a product
     */
    private function getSalesLogsForProduct($product): array
    {
        $orderItems = \App\Models\OrderItem::where('product_id', $product->id)
            ->with('order')
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed');
            })
            ->get();
        
        $logs = [];
        foreach ($orderItems as $item) {
            $logs[] = [
                'quantity' => $item->quantity,
                'date' => $item->order->created_at,
            ];
        }
        
        // Sort by date
        usort($logs, function ($a, $b) {
            return $a['date'] <=> $b['date'];
        });
        
        return $logs;
    }
}