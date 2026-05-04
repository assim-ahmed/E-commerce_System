<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// استيراد الموديلات المرتبطة
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_variant_id',
        'product_id',
        'product_name_snapshot',
        'price_snapshot',
        'quantity',
        'total'
    ];

    protected $casts = [
        'price_snapshot' => 'decimal:2',
        'quantity' => 'integer',
        'total' => 'decimal:2'
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Accessors
    public function getUnitPriceAttribute()
    {
        return $this->price_snapshot;
    }

    // Auto-calculate total before saving
    protected static function booted()
    {
        static::saving(function ($orderItem) {
            $orderItem->total = $orderItem->price_snapshot * $orderItem->quantity;
        });
    }
}