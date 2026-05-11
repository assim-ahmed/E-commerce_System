<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'cart_id' => $this['cart']->id,
            'items' => CartItemResource::collection($this['cart']->items),
            'subtotal' => (float) $this['subtotal'],
            'total' => (float) $this['total'],
            'items_count' => $this['items_count'],
            'price_changed' => $this['price_changed']
        ];
    }
}