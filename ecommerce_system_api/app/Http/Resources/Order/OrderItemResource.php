<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_variant_id' => $this->product_variant_id,
            'product_name' => $this->product_name_snapshot,
            'price' => number_format($this->price_snapshot, 2),
            'quantity' => $this->quantity,
            'total' => number_format($this->total, 2),
        ];
    }
}