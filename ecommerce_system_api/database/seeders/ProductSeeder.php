<?php
// database/seeders/ProductSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // جلب التصنيفات والماركات الموجودة
        $categories = Category::all();
        $brands = Brand::all();
        
        if ($categories->isEmpty() || $brands->isEmpty()) {
            $this->command->error('Please run CategoryBrandSeeder first!');
            return;
        }
        
        // الحصول على ماركة افتراضية (أول ماركة في الجدول)
        $defaultBrand = $brands->first();
        
        $products = [];
        
        // Electronics Products (Category: Electronics)
        $electronicsCategory = $categories->where('name', 'Electronics')->first();
        if ($electronicsCategory) {
            $electronicsProducts = [
                ['name' => 'iPhone 15 Pro', 'price' => 4999.00, 'compare' => 5499.00, 'stock' => 45, 'featured' => true, 'brand_name' => 'Apple'],
                ['name' => 'iPhone 15', 'price' => 3999.00, 'compare' => 4499.00, 'stock' => 60, 'featured' => true, 'brand_name' => 'Apple'],
                ['name' => 'iPhone 14 Pro', 'price' => 3499.00, 'compare' => 3999.00, 'stock' => 25, 'featured' => false, 'brand_name' => 'Apple'],
                ['name' => 'Samsung Galaxy S24 Ultra', 'price' => 4299.00, 'compare' => 4799.00, 'stock' => 30, 'featured' => true, 'brand_name' => 'Samsung'],
                ['name' => 'Samsung Galaxy S24', 'price' => 3299.00, 'compare' => 3699.00, 'stock' => 40, 'featured' => false, 'brand_name' => 'Samsung'],
                ['name' => 'Samsung Galaxy Z Fold 5', 'price' => 5999.00, 'compare' => 6499.00, 'stock' => 15, 'featured' => true, 'brand_name' => 'Samsung'],
                ['name' => 'Xiaomi 14 Ultra', 'price' => 2799.00, 'compare' => 3299.00, 'stock' => 50, 'featured' => false, 'brand_name' => 'Samsung'],
                ['name' => 'Xiaomi 14', 'price' => 1999.00, 'compare' => 2499.00, 'stock' => 70, 'featured' => false, 'brand_name' => 'Samsung'],
                ['name' => 'Google Pixel 8 Pro', 'price' => 3599.00, 'compare' => 4099.00, 'stock' => 20, 'featured' => false, 'brand_name' => 'Samsung'],
                ['name' => 'OnePlus 12', 'price' => 2899.00, 'compare' => 3399.00, 'stock' => 35, 'featured' => false, 'brand_name' => 'Samsung'],
                ['name' => 'MacBook Pro 14"', 'price' => 12999.00, 'compare' => 13999.00, 'stock' => 10, 'featured' => true, 'brand_name' => 'Apple'],
                ['name' => 'MacBook Air M3', 'price' => 8999.00, 'compare' => 9999.00, 'stock' => 18, 'featured' => true, 'brand_name' => 'Apple'],
                ['name' => 'Dell XPS 15', 'price' => 7999.00, 'compare' => 8999.00, 'stock' => 12, 'featured' => false, 'brand_name' => 'Dell'],
                ['name' => 'HP Spectre x360', 'price' => 6999.00, 'compare' => 7999.00, 'stock' => 8, 'featured' => false, 'brand_name' => 'HP'],
                ['name' => 'Lenovo ThinkPad X1', 'price' => 7499.00, 'compare' => 8499.00, 'stock' => 14, 'featured' => false, 'brand_name' => 'Samsung'],
                ['name' => 'iPad Pro 12.9"', 'price' => 4499.00, 'compare' => 4999.00, 'stock' => 22, 'featured' => true, 'brand_name' => 'Apple'],
                ['name' => 'iPad Air', 'price' => 2999.00, 'compare' => 3499.00, 'stock' => 35, 'featured' => false, 'brand_name' => 'Apple'],
                ['name' => 'Apple Watch Ultra 2', 'price' => 3299.00, 'compare' => 3799.00, 'stock' => 28, 'featured' => false, 'brand_name' => 'Apple'],
                ['name' => 'Samsung Galaxy Watch 6', 'price' => 1499.00, 'compare' => 1799.00, 'stock' => 42, 'featured' => false, 'brand_name' => 'Samsung'],
                ['name' => 'Sony WH-1000XM5', 'price' => 1999.00, 'compare' => 2499.00, 'stock' => 55, 'featured' => true, 'brand_name' => 'Sony'],
                ['name' => 'AirPods Pro 2', 'price' => 1899.00, 'compare' => 2299.00, 'stock' => 65, 'featured' => true, 'brand_name' => 'Apple'],
                ['name' => 'Samsung Buds 2 Pro', 'price' => 999.00, 'compare' => 1299.00, 'stock' => 80, 'featured' => false, 'brand_name' => 'Samsung'],
                ['name' => 'LG C3 OLED TV 65"', 'price' => 15999.00, 'compare' => 17999.00, 'stock' => 5, 'featured' => true, 'brand_name' => 'LG'],
                ['name' => 'Samsung QLED 55"', 'price' => 8999.00, 'compare' => 9999.00, 'stock' => 7, 'featured' => false, 'brand_name' => 'Samsung'],
                ['name' => 'Sony Bravia XR 65"', 'price' => 12999.00, 'compare' => 14999.00, 'stock' => 4, 'featured' => false, 'brand_name' => 'Sony'],
            ];
            
            foreach ($electronicsProducts as $product) {
                // البحث عن الماركة
                $brand = $brands->where('name', $product['brand_name'])->first();
                $brandId = $brand ? $brand->id : $defaultBrand->id;
                
                Product::create([
                    'name' => $product['name'],
                    'slug' => Str::slug($product['name']) . '-' . Str::random(4),
                    'description' => "Experience the best with {$product['name']}. High quality product with excellent features.",
                    'short_description' => "Premium {$product['name']} with great performance",
                    'category_id' => $electronicsCategory->id,
                    'brand_id' => $brandId,
                    'base_price' => $product['price'],
                    'compare_price' => $product['compare'],
                    'stock_quantity' => $product['stock'],
                    'low_stock_threshold' => 10,
                    'is_low_stock' => $product['stock'] <= 10,
                    'sku' => strtoupper(Str::random(8)),
                    'is_featured' => $product['featured'],
                    'is_active' => true,
                    'views_count' => rand(0, 500),
                    'images' => json_encode(["products/{$product['name']}_1.jpg", "products/{$product['name']}_2.jpg"]),
                    'specifications' => json_encode([
                        'brand' => $product['brand_name'],
                        'condition' => 'new',
                        'warranty' => '1 year'
                    ]),
                ]);
            }
        }
        
        // Clothing Products (Category: Clothing)
        $clothingCategory = $categories->where('name', 'Clothing')->first();
        if ($clothingCategory) {
            $nikeBrand = $brands->where('name', 'Nike')->first();
            $adidasBrand = $brands->where('name', 'Adidas')->first();
            
            $clothingProducts = [
                ['name' => 'Nike Air Max 90', 'price' => 899.00, 'compare' => 1099.00, 'stock' => 85, 'brand' => $nikeBrand, 'brand_name' => 'Nike'],
                ['name' => 'Nike Revolution 6', 'price' => 599.00, 'compare' => 799.00, 'stock' => 120, 'brand' => $nikeBrand, 'brand_name' => 'Nike'],
                ['name' => 'Nike Dunk Low', 'price' => 1099.00, 'compare' => 1299.00, 'stock' => 45, 'brand' => $nikeBrand, 'brand_name' => 'Nike'],
                ['name' => 'Adidas Ultraboost 23', 'price' => 1199.00, 'compare' => 1499.00, 'stock' => 60, 'brand' => $adidasBrand, 'brand_name' => 'Adidas'],
                ['name' => 'Adidas Samba OG', 'price' => 799.00, 'compare' => 999.00, 'stock' => 55, 'brand' => $adidasBrand, 'brand_name' => 'Adidas'],
                ['name' => 'Adidas Gazelle', 'price' => 699.00, 'compare' => 899.00, 'stock' => 70, 'brand' => $adidasBrand, 'brand_name' => 'Adidas'],
                ['name' => 'Nike Sportswear Hoodie', 'price' => 399.00, 'compare' => 499.00, 'stock' => 150, 'brand' => $nikeBrand, 'brand_name' => 'Nike'],
                ['name' => 'Adidas Originals T-Shirt', 'price' => 199.00, 'compare' => 299.00, 'stock' => 200, 'brand' => $adidasBrand, 'brand_name' => 'Adidas'],
                ['name' => 'Nike Pro Shorts', 'price' => 249.00, 'compare' => 349.00, 'stock' => 180, 'brand' => $nikeBrand, 'brand_name' => 'Nike'],
                ['name' => 'Adidas Training Pants', 'price' => 349.00, 'compare' => 449.00, 'stock' => 130, 'brand' => $adidasBrand, 'brand_name' => 'Adidas'],
                ['name' => 'Nike Air Force 1', 'price' => 999.00, 'compare' => 1199.00, 'stock' => 95, 'brand' => $nikeBrand, 'brand_name' => 'Nike'],
                ['name' => 'Adidas Forum Low', 'price' => 899.00, 'compare' => 1099.00, 'stock' => 65, 'brand' => $adidasBrand, 'brand_name' => 'Adidas'],
                ['name' => 'Nike Windrunner Jacket', 'price' => 599.00, 'compare' => 799.00, 'stock' => 75, 'brand' => $nikeBrand, 'brand_name' => 'Nike'],
                ['name' => 'Adidas Winter Jacket', 'price' => 699.00, 'compare' => 899.00, 'stock' => 40, 'brand' => $adidasBrand, 'brand_name' => 'Adidas'],
                ['name' => 'Nike Elite Backpack', 'price' => 449.00, 'compare' => 599.00, 'stock' => 110, 'brand' => $nikeBrand, 'brand_name' => 'Nike'],
            ];
            
            foreach ($clothingProducts as $product) {
                $brandId = $product['brand'] ? $product['brand']->id : $defaultBrand->id;
                
                Product::create([
                    'name' => $product['name'],
                    'slug' => Str::slug($product['name']) . '-' . Str::random(4),
                    'description' => "Stylish and comfortable {$product['name']}. Perfect for daily wear.",
                    'short_description' => "Premium {$product['name']} - comfortable and durable",
                    'category_id' => $clothingCategory->id,
                    'brand_id' => $brandId,
                    'base_price' => $product['price'],
                    'compare_price' => $product['compare'],
                    'stock_quantity' => $product['stock'],
                    'low_stock_threshold' => 15,
                    'is_low_stock' => $product['stock'] <= 15,
                    'sku' => strtoupper(Str::random(8)),
                    'is_featured' => rand(0, 1) === 1,
                    'is_active' => true,
                    'views_count' => rand(0, 500),
                    'images' => json_encode(["clothing/{$product['name']}_1.jpg", "clothing/{$product['name']}_2.jpg"]),
                    'specifications' => json_encode([
                        'brand' => $product['brand_name'],
                        'material' => 'Premium quality',
                        'sizes' => 'S, M, L, XL',
                        'care' => 'Machine wash'
                    ]),
                ]);
            }
        }
        
        // Books Category (بدون ماركة - استخدم default brand)
        $booksCategory = $categories->where('name', 'Books')->first();
        if ($booksCategory) {
            $booksProducts = [
                ['name' => 'The Psychology of Money', 'price' => 149.00, 'stock' => 200],
                ['name' => 'Atomic Habits', 'price' => 179.00, 'stock' => 185],
                ['name' => 'Deep Work', 'price' => 129.00, 'stock' => 150],
                ['name' => 'Think and Grow Rich', 'price' => 99.00, 'stock' => 220],
                ['name' => 'The 7 Habits of Highly Effective People', 'price' => 199.00, 'stock' => 130],
                ['name' => 'Rich Dad Poor Dad', 'price' => 119.00, 'stock' => 250],
                ['name' => 'The Intelligent Investor', 'price' => 249.00, 'stock' => 80],
                ['name' => 'Zero to One', 'price' => 159.00, 'stock' => 110],
                ['name' => 'The Lean Startup', 'price' => 169.00, 'stock' => 95],
                ['name' => 'Good to Great', 'price' => 189.00, 'stock' => 75],
            ];
            
            foreach ($booksProducts as $product) {
                Product::create([
                    'name' => $product['name'],
                    'slug' => Str::slug($product['name']) . '-' . Str::random(4),
                    'description' => "Bestselling book: {$product['name']}. A must-read for personal growth.",
                    'short_description' => "{$product['name']} - inspiring and transformative",
                    'category_id' => $booksCategory->id,
                    'brand_id' => $defaultBrand->id,  // استخدام الماركة الافتراضية
                    'base_price' => $product['price'],
                    'compare_price' => null,
                    'stock_quantity' => $product['stock'],
                    'low_stock_threshold' => 20,
                    'is_low_stock' => $product['stock'] <= 20,
                    'sku' => strtoupper(Str::random(8)),
                    'is_featured' => rand(0, 1) === 1,
                    'is_active' => true,
                    'views_count' => rand(0, 300),
                    'images' => json_encode(["books/{$product['name']}.jpg"]),
                    'specifications' => json_encode([
                        'author' => 'Bestselling author',
                        'pages' => rand(200, 400),
                        'language' => 'English'
                    ]),
                ]);
            }
        }
        
        // Home & Garden Category (بدون ماركة محددة - استخدم default brand)
        $homeCategory = $categories->where('name', 'Home & Garden')->first();
        if ($homeCategory) {
            $homeProducts = [
                ['name' => 'LED Desk Lamp', 'price' => 249.00, 'stock' => 300],
                ['name' => 'Electric Kettle', 'price' => 399.00, 'stock' => 150],
                ['name' => 'Air Fryer 5L', 'price' => 1499.00, 'stock' => 85],
                ['name' => 'Robot Vacuum', 'price' => 2499.00, 'stock' => 40],
                ['name' => 'Coffee Maker', 'price' => 899.00, 'stock' => 95],
                ['name' => 'Blender', 'price' => 599.00, 'stock' => 120],
                ['name' => 'Microwave Oven', 'price' => 1999.00, 'stock' => 55],
                ['name' => 'Iron', 'price' => 349.00, 'stock' => 180],
                ['name' => 'Laundry Basket', 'price' => 89.00, 'stock' => 400],
                ['name' => 'Garden Tools Set', 'price' => 299.00, 'stock' => 250],
            ];
            
            foreach ($homeProducts as $product) {
                Product::create([
                    'name' => $product['name'],
                    'slug' => Str::slug($product['name']) . '-' . Str::random(4),
                    'description' => "High quality {$product['name']} for your home. Durable and efficient.",
                    'short_description' => "{$product['name']} - perfect for everyday use",
                    'category_id' => $homeCategory->id,
                    'brand_id' => $defaultBrand->id,  // استخدام الماركة الافتراضية
                    'base_price' => $product['price'],
                    'compare_price' => $product['price'] + 100,
                    'stock_quantity' => $product['stock'],
                    'low_stock_threshold' => 30,
                    'is_low_stock' => $product['stock'] <= 30,
                    'sku' => strtoupper(Str::random(8)),
                    'is_featured' => rand(0, 1) === 1,
                    'is_active' => true,
                    'views_count' => rand(0, 300),
                    'images' => json_encode(["home/{$product['name']}.jpg"]),
                    'specifications' => json_encode([
                        'material' => 'High quality',
                        'warranty' => '2 years'
                    ]),
                ]);
            }
        }
        
        // Sports Category (بدون ماركة محددة - استخدم default brand)
        $sportsCategory = $categories->where('name', 'Sports')->first();
        if ($sportsCategory) {
            $sportsProducts = [
                ['name' => 'Yoga Mat', 'price' => 199.00, 'stock' => 350],
                ['name' => 'Dumbbells Set 10kg', 'price' => 499.00, 'stock' => 120],
                ['name' => 'Jump Rope', 'price' => 49.00, 'stock' => 500],
                ['name' => 'Resistance Bands', 'price' => 149.00, 'stock' => 280],
                ['name' => 'Basketball', 'price' => 299.00, 'stock' => 150],
                ['name' => 'Football', 'price' => 249.00, 'stock' => 170],
                ['name' => 'Tennis Racket', 'price' => 599.00, 'stock' => 90],
                ['name' => 'Fitness Tracker', 'price' => 399.00, 'stock' => 200],
                ['name' => 'Gym Bag', 'price' => 349.00, 'stock' => 160],
                ['name' => 'Water Bottle', 'price' => 79.00, 'stock' => 600],
            ];
            
            foreach ($sportsProducts as $product) {
                Product::create([
                    'name' => $product['name'],
                    'slug' => Str::slug($product['name']) . '-' . Str::random(4),
                    'description' => "Professional {$product['name']} for your fitness journey.",
                    'short_description' => "{$product['name']} - enhance your workout",
                    'category_id' => $sportsCategory->id,
                    'brand_id' => $defaultBrand->id,  // استخدام الماركة الافتراضية
                    'base_price' => $product['price'],
                    'compare_price' => null,
                    'stock_quantity' => $product['stock'],
                    'low_stock_threshold' => 25,
                    'is_low_stock' => $product['stock'] <= 25,
                    'sku' => strtoupper(Str::random(8)),
                    'is_featured' => rand(0, 1) === 1,
                    'is_active' => true,
                    'views_count' => rand(0, 300),
                    'images' => json_encode(["sports/{$product['name']}.jpg"]),
                    'specifications' => json_encode([
                        'material' => 'Premium quality',
                        'suitable_for' => 'All ages'
                    ]),
                ]);
            }
        }
        
        $this->command->info('Products seeded successfully! Total: ' . Product::count());
        
        // عرض إحصائيات
        $this->command->info('Category breakdown:');
        foreach ($categories as $category) {
            $count = Product::where('category_id', $category->id)->count();
            $this->command->info("  - {$category->name}: {$count} products");
        }
    }
}