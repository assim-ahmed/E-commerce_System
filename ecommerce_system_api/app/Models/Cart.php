<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// استيراد الموديلات المرتبطة
use App\Models\User;
use App\Models\CartItem;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'cookie_id'
    ];

    protected $casts = [
        'user_id' => 'integer'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    // Accessors
    public function getTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->price_at_time * $item->quantity;
        });
    }

    public function getItemsCountAttribute()
    {
        return $this->items->sum('quantity');
    }

    // Methods
    public function clear()
    {
        return $this->items()->delete();
    }

    public function isEmpty()
    {
        return $this->items->isEmpty();
    }
}