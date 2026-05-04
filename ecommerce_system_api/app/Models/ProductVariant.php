<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use App\Models\Product;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\InventoryLog;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'attributes',
        'price_adjustment',
        'stock_quantity'
    ];

    protected $casts = [
        'attributes' => 'array',
        'price_adjustment' => 'decimal:2',
        'stock_quantity' => 'integer'
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    // Accessors
    public function getFinalPriceAttribute()
    {
        return $this->product->base_price + $this->price_adjustment;
    }

    public function getIsInStockAttribute()
    {
        return $this->stock_quantity > 0;
    }
}