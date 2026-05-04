<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



// ✅ استدعاء الموديلات المرتبطة
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\InventoryLog;


class Product extends Model
{


    protected $fillable = [
        'name', 'slug', 'description', 'short_description',
        'category_id', 'brand_id', 'base_price', 'compare_price',
        'stock_quantity', 'low_stock_threshold', 'is_low_stock',
        'sku', 'is_featured', 'views_count', 'images', 'specifications'
    ];

    protected $casts = [
        'images' => 'array',
        'specifications' => 'array',
        'base_price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'is_low_stock' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    // Accessors
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    public function getFinalPriceAttribute()
    {
        return $this->compare_price && $this->compare_price < $this->base_price 
            ? $this->compare_price 
            : $this->base_price;
    }

    // Scopes
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->where('is_low_stock', true);
    }

    // Events (في booted method)
    protected static function booted()
    {
        static::saving(function ($product) {
            $product->is_low_stock = $product->stock_quantity <= $product->low_stock_threshold;
        });
    }
}