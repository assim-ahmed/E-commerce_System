<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


// استيراد الموديلات المرتبطة
use App\Models\Product;

class Brand extends Model
{


    protected $fillable = [
        'name',
        'slug'
    ];

    protected $casts = [
       
    ];

    // Relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Scopes
    public function scopeHasProducts($query)
    {
        return $query->has('products');
    }

    public function scopeWithProductsCount($query)
    {
        return $query->withCount('products');
    }

    // Route Binding (للاستخدام في API)
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Accessors
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }

    public function getUrlAttribute()
    {
        return route('brands.show', $this->slug);
    }
}