<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'total' => number_format($this->total, 2),
            'items_count' => $this->items->count(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'address' => $this->whenLoaded('address', function () {
                return [
                    'address_line' => $this->address->address_line_1,
                    'city' => $this->address->city,
                    'country' => $this->address->country,
                ];
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}