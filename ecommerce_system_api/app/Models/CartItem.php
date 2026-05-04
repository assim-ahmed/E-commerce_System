<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// استيراد الموديلات المرتبطة
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'price_at_time'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_at_time' => 'decimal:2'
    ];

    // Relationships
    public function cart()
    {
        return $this->belongsTo(Cart::class);
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
    public function getSubtotalAttribute()
    {
        return $this->price_at_time * $this->quantity;
    }

    // Methods
    public function updatePrice()
    {
        if ($this->product) {
            $this->price_at_time = $this->product->final_price;
            $this->save();
        }
    }
}