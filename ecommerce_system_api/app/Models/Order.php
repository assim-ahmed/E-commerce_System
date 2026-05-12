<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


// استيراد الموديلات المرتبطة
use App\Models\User;
use App\Models\Address;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Coupon;

class Order extends Model
{
    // ========== Status Constants ==========
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';
    // ========== Fillable ==========
    protected $fillable = [
        'order_number',
        'user_id',
        'address_id',
        'status',
        'total',
        'coupon_code'
    ];
    // ========== Casts ==========

    protected $casts = [
        'total' => 'decimal:2',

    ];
    // ========== Relationships ==========

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    // Accessors
    public function getSubtotalAttribute()
    {
        return $this->items->sum('total');
    }

    public function getItemsCountAttribute()
    {
        return $this->items->sum('quantity');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function updateStatus($newStatus)
    {
        $this->status = $newStatus;
        $this->save();
    }

    public function canBeReviewed()
    {
        return $this->status === 'completed';
    }

    // Auto-generate order number
    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . str_pad(static::max('id') + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}