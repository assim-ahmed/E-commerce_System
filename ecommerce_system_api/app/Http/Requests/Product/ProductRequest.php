<?php
// app/Http/Requests/Product/ProductRequest.php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('id');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        
        if ($isUpdate) {
            // قواعد التحديث (كل الحقول اختيارية)
            return [
                'name' => 'sometimes|required|string|max:255',
                'slug' => 'nullable|string|unique:products,slug,' . $productId,
                'description' => 'nullable|string',
                'short_description' => 'nullable|string|max:500',
                'category_id' => 'sometimes|required|exists:categories,id',
                'brand_id' => 'sometimes|required|exists:brands,id',
                'base_price' => 'sometimes|required|numeric|min:0',
                'compare_price' => 'nullable|numeric|min:0',
                'stock_quantity' => 'sometimes|required|integer|min:0',
                'low_stock_threshold' => 'nullable|integer|min:0',
                'sku' => ['sometimes', 'required', 'string', Rule::unique('products', 'sku')->ignore($productId)],
                'is_featured' => 'boolean',
                'images' => 'nullable|array',
                'images.*' => 'string|url',
                'specifications' => 'nullable|array',
                'variants' => 'nullable|array',
                'variants.*.name' => 'required_with:variants|string',
                'variants.*.attributes' => 'nullable|array',
                'variants.*.price_adjustment' => 'nullable|numeric',
                'variants.*.stock_quantity' => 'required_with:variants|integer|min:0',
            ];
        }
        
        // قواعد الإنشاء (كل الحقول مطلوبة)
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'base_price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'sku' => 'required|string|unique:products,sku',
            'is_featured' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'string|url',
            'specifications' => 'nullable|array',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required_with:variants|string',
            'variants.*.attributes' => 'nullable|array',
            'variants.*.price_adjustment' => 'nullable|numeric',
            'variants.*.stock_quantity' => 'required_with:variants|integer|min:0',
        ];
    }
    
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->is_featured ?? false,
            'low_stock_threshold' => $this->low_stock_threshold ?? 10,
            'description' => $this->description ?? '',
            'short_description' => $this->short_description ?? '',
        ]);
    }
}