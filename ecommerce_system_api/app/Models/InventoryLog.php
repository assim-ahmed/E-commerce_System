<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// استيراد الموديلات المرتبطة
use App\Models\Product;
use App\Models\ProductVariant;

class InventoryLog extends Model
{
    // لا يحتاج SoftDeletes لأنها سجل تاريخي فقط
    // لا يحتاج timestamps افتراضياً، لكنه مفيد للـ created_at

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'type',
        'quantity_change',
        'quantity_before',
        'quantity_after'
    ];

    protected $casts = [
        'quantity_change' => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer'
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Scopes
    public function scopePurchase($query)
    {
        return $query->where('type', 'purchase');
    }

    public function scopeSale($query)
    {
        return $query->where('type', 'sale');
    }

    public function scopeReturn($query)
    {
        return $query->where('type', 'return');
    }

    public function scopeAdjustment($query)
    {
        return $query->where('type', 'adjustment');
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByVariant($query, $variantId)
    {
        return $query->where('product_variant_id', $variantId);
    }

    // Methods
    public function isAddition()
    {
        return in_array($this->type, ['purchase', 'return', 'adjustment']) && $this->quantity_change > 0;
    }

    public function isSubtraction()
    {
        return in_array($this->type, ['sale', 'adjustment']) && $this->quantity_change < 0;
    }

    // Accessors
    public function getTypeNameAttribute()
    {
        $types = [
            'purchase' => 'شراء',
            'sale' => 'بيع',
            'return' => 'مرتجع',
            'adjustment' => 'تعديل يدوي'
        ];
        return $types[$this->type] ?? $this->type;
    }

    // Auto-log inventory changes
    protected static function booted()
    {
        static::creating(function ($log) {
            if (!$log->quantity_before) {
                if ($log->variant) {
                    $log->quantity_before = $log->variant->stock_quantity;
                } else if ($log->product) {
                    $log->quantity_before = $log->product->stock_quantity;
                }
            }

            if (!$log->quantity_after && $log->quantity_before) {
                $log->quantity_after = $log->quantity_before + $log->quantity_change;
            }
        });
    }
}