<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product->name ?? 'N/A',
            'product_slug' => $this->product->slug ?? null,
            'variant_id' => $this->product_variant_id,
            'variant_name' => $this->variant->name ?? null,
            'quantity' => $this->quantity,
            'price_at_add' => (float) $this->price_at_time,
            'current_price' => (float) ($this->current_price ?? $this->price_at_time),
            'line_total' => (float) ($this->line_total ?? $this->quantity * $this->price_at_time),
            'stock_available' => $this->product->stock_quantity ?? 0
        ];
    }
}