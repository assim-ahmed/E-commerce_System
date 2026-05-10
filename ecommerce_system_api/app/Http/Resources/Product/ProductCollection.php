<?php
// app/Http/Resources/Product/ProductCollection.php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     */
    public $collects = ProductResource::class;
    
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'products' => $this->collection,
        ];
    }
    
    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with($request)
    {
        return [
            'success' => true,
        ];
    }
    
    /**
     * Customize the pagination information for the resource.
     */
    public function paginationInformation($request, $paginated, $default)
    {
        return [
            'meta' => [
                'current_page' => $paginated['current_page'],
                'from' => $paginated['from'],
                'to' => $paginated['to'],
                'per_page' => $paginated['per_page'],
                'total' => $paginated['total'],
                'last_page' => $paginated['last_page'],
                'path' => $paginated['path'],
                'first_page_url' => $paginated['first_page_url'],
                'last_page_url' => $paginated['last_page_url'],
                'next_page_url' => $paginated['next_page_url'],
                'prev_page_url' => $paginated['prev_page_url'],
            ],
        ];
    }
}