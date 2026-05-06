<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;

class CategoryBrandSeeder extends Seeder
{
    public function run(): void
    {
        // Categories
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and gadgets'],
            ['name' => 'Clothing', 'description' => 'Fashion and apparel'],
            ['name' => 'Books', 'description' => 'Books and publications'],
            ['name' => 'Home & Garden', 'description' => 'Home decor and gardening tools'],
            ['name' => 'Sports', 'description' => 'Sports equipment and accessories'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
            ]);
        }

        // Brands
        $brands = [
            ['name' => 'Samsung'],
            ['name' => 'Apple'],
            ['name' => 'Nike'],
            ['name' => 'Adidas'],
            ['name' => 'Sony'],
            ['name' => 'LG'],
            ['name' => 'HP'],
            ['name' => 'Dell'],
        ];

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand['name'],
                'slug' => Str::slug($brand['name']),
            ]);
        }

        $this->command->info('Categories and Brands seeded successfully!');
    }
}