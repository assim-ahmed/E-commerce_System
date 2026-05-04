<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// ✅ لازم تستدعي كل الموديلات اللي هتستخدمها في العلاقات
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Review;
use App\Models\Notification;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function addresses()
    {
        return $this->hasMany(Address::class);  // ← محتاج use App\Models\Address
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);      // ← محتاج use App\Models\Cart
    }

    public function orders()
    {
        return $this->hasMany(Order::class);    // ← محتاج use App\Models\Order
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);   // ← محتاج use App\Models\Review
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class); // ← محتاج use App\Models\Notification
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }
}