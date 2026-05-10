<?php
// app/Http/Resources/Product/ProductResource.php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'base_price' => number_format($this->base_price, 2),
            'compare_price' => $this->compare_price ? number_format($this->compare_price, 2) : null,
            'discount_percentage' => $this->discount_percentage,
            'stock_quantity' => $this->stock_quantity,
            'low_stock_threshold' => $this->low_stock_threshold,
            'is_low_stock' => $this->is_low_stock,
            'in_stock' => $this->stock_quantity > 0,
            'sku' => $this->sku,
            'is_featured' => $this->is_featured,
            'views_count' => $this->views_count,
            'images' => $this->images ? json_decode($this->images) : [],
            'specifications' => $this->specifications ? json_decode($this->specifications) : [],
            'average_rating' => $this->average_rating,
            'reviews_count' => $this->reviews_count,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Relationships
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            
            'brand' => $this->whenLoaded('brand', function () {
                return $this->brand ? [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name,
                    'slug' => $this->brand->slug,
                ] : null;
            }),
            
            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'attributes' => $variant->attributes ? json_decode($variant->attributes) : [],
                        'price_adjustment' => $variant->price_adjustment,
                        'stock_quantity' => $variant->stock_quantity,
                    ];
                });
            }),
        ];
    }
    
    /**
     * Add additional data to the response
     */
    public function with($request)
    {
        return [
            'success' => true,
        ];
    }
}