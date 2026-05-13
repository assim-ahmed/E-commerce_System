<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        // جلب المنتجات اللي ليها متغيرات
        $products = Product::whereIn('name', [
            'iPhone 15 Pro',
            'iPhone 15',
            'Samsung Galaxy S24 Ultra',
            'MacBook Pro 14"',
            'Nike Air Max 90',
            'Adidas Ultraboost 23'
        ])->get();
        
        foreach ($products as $product) {
            if ($product->name == 'iPhone 15 Pro') {
                $variants = [
                    ['name' => '128GB Black', 'attributes' => json_encode(['storage' => '128GB', 'color' => 'Black']), 'price_adjustment' => 0, 'stock_quantity' => 15],
                    ['name' => '256GB Black', 'attributes' => json_encode(['storage' => '256GB', 'color' => 'Black']), 'price_adjustment' => 500, 'stock_quantity' => 20],
                    ['name' => '256GB White', 'attributes' => json_encode(['storage' => '256GB', 'color' => 'White']), 'price_adjustment' => 500, 'stock_quantity' => 10],
                    ['name' => '512GB Black', 'attributes' => json_encode(['storage' => '512GB', 'color' => 'Black']), 'price_adjustment' => 1000, 'stock_quantity' => 5],
                ];
            } elseif ($product->name == 'iPhone 15') {
                $variants = [
                    ['name' => '128GB Blue', 'attributes' => json_encode(['storage' => '128GB', 'color' => 'Blue']), 'price_adjustment' => 0, 'stock_quantity' => 30],
                    ['name' => '256GB Blue', 'attributes' => json_encode(['storage' => '256GB', 'color' => 'Blue']), 'price_adjustment' => 400, 'stock_quantity' => 25],
                    ['name' => '256GB Pink', 'attributes' => json_encode(['storage' => '256GB', 'color' => 'Pink']), 'price_adjustment' => 400, 'stock_quantity' => 20],
                ];
            } elseif ($product->name == 'Samsung Galaxy S24 Ultra') {
                $variants = [
                    ['name' => '256GB Titanium', 'attributes' => json_encode(['storage' => '256GB', 'color' => 'Titanium']), 'price_adjustment' => 0, 'stock_quantity' => 12],
                    ['name' => '512GB Titanium', 'attributes' => json_encode(['storage' => '512GB', 'color' => 'Titanium']), 'price_adjustment' => 600, 'stock_quantity' => 8],
                    ['name' => '1TB Titanium', 'attributes' => json_encode(['storage' => '1TB', 'color' => 'Titanium']), 'price_adjustment' => 1200, 'stock_quantity' => 3],
                ];
            } elseif ($product->name == 'MacBook Pro 14"') {
                $variants = [
                    ['name' => 'M3 16GB/512GB', 'attributes' => json_encode(['chip' => 'M3', 'ram' => '16GB', 'storage' => '512GB']), 'price_adjustment' => 0, 'stock_quantity' => 8],
                    ['name' => 'M3 Pro 18GB/1TB', 'attributes' => json_encode(['chip' => 'M3 Pro', 'ram' => '18GB', 'storage' => '1TB']), 'price_adjustment' => 3000, 'stock_quantity' => 5],
                ];
            } elseif ($product->name == 'Nike Air Max 90') {
                $variants = [
                    ['name' => 'Size 40 White/Red', 'attributes' => json_encode(['size' => '40', 'color' => 'White/Red']), 'price_adjustment' => 0, 'stock_quantity' => 25],
                    ['name' => 'Size 42 White/Red', 'attributes' => json_encode(['size' => '42', 'color' => 'White/Red']), 'price_adjustment' => 0, 'stock_quantity' => 30],
                    ['name' => 'Size 44 White/Red', 'attributes' => json_encode(['size' => '44', 'color' => 'White/Red']), 'price_adjustment' => 0, 'stock_quantity' => 20],
                ];
            } elseif ($product->name == 'Adidas Ultraboost 23') {
                $variants = [
                    ['name' => 'Size 41 Black', 'attributes' => json_encode(['size' => '41', 'color' => 'Black']), 'price_adjustment' => 0, 'stock_quantity' => 18],
                    ['name' => 'Size 42 Black', 'attributes' => json_encode(['size' => '42', 'color' => 'Black']), 'price_adjustment' => 0, 'stock_quantity' => 22],
                    ['name' => 'Size 43 Black', 'attributes' => json_encode(['size' => '43', 'color' => 'Black']), 'price_adjustment' => 0, 'stock_quantity' => 15],
                ];
            } else {
                continue;
            }
            
            foreach ($variants as $variant) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => $variant['name'],
                    'attributes' => $variant['attributes'],
                    'price_adjustment' => $variant['price_adjustment'],
                    'stock_quantity' => $variant['stock_quantity'],
                ]);
            }
        }
        
        $this->command->info('Product variants seeded successfully! Total: ' . ProductVariant::count());
    }
}