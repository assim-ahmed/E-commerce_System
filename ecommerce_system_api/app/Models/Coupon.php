<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// لا يحتاج استيراد موديلات أخرى لأن العلاقة مع orders اختيارية وغير مباشرة

class Coupon extends Model
{


    protected $fillable = [
        'code',
        'type',
        'value',
        'minimum_order_amount',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class, 'coupon_code', 'code');
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }

    public function scopeFixed($query)
    {
        return $query->where('type', 'fixed');
    }

    public function scopePercentage($query)
    {
        return $query->where('type', 'percentage');
    }

    // Methods
    public function calculateDiscount($subtotal)
    {
        if ($this->minimum_order_amount && $subtotal < $this->minimum_order_amount) {
            return 0;
        }

        if ($this->type === 'fixed') {
            return min($this->value, $subtotal);
        }

        // percentage
        return ($subtotal * $this->value) / 100;
    }

    public function isValid()
    {
        return $this->start_date <= now() && $this->end_date >= now();
    }
}