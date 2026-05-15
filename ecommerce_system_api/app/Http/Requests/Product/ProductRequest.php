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
                // Basic Info
                'name' => 'sometimes|required|string|max:255',
                'slug' => 'nullable|string|unique:products,slug,' . $productId,
                'description' => 'nullable|string',
                'short_description' => 'nullable|string|max:500',
                'category_id' => 'sometimes|required|exists:categories,id',
                'brand_id' => 'sometimes|required|exists:brands,id',
                
                // Pricing
                'base_price' => 'sometimes|required|numeric|min:0',
                'compare_price' => 'nullable|numeric|min:0',
                
                // Stock
                'stock_quantity' => 'sometimes|required|integer|min:0',
                'low_stock_threshold' => 'nullable|integer|min:0',
                'sku' => ['sometimes', 'required', 'string', Rule::unique('products', 'sku')->ignore($productId)],
                
                // Flags
                'is_featured' => 'boolean',
                
                // ✅ IMAGES - File upload support
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Each file
                
                'existing_images' => 'nullable|array', // Keep existing images
                'existing_images.*' => 'string|url', // URLs of existing images
                
                'delete_images' => 'nullable|array', // Images to delete
                'delete_images.*' => 'string|url',
                
                // Main image (single)
                'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'existing_main_image' => 'nullable|string|url',
                
                // JSON fields
                'specifications' => 'nullable|array',
                
                // Variants
                'variants' => 'nullable|array',
                'variants.*.id' => 'nullable|exists:product_variants,id',
                'variants.*.name' => 'required_with:variants|string',
                'variants.*.attributes' => 'nullable|array',
                'variants.*.price_adjustment' => 'nullable|numeric',
                'variants.*.stock_quantity' => 'required_with:variants|integer|min:0',
            ];
        }
        
        // قواعد الإنشاء (كل الحقول مطلوبة)
        return [
            // Basic Info
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            
            // Pricing
            'base_price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            
            // Stock
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'sku' => 'required|string|unique:products,sku',
            
            // Flags
            'is_featured' => 'boolean',
            
            // ✅ IMAGES - File upload support
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            
            // Main image
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            
            // JSON fields
            'specifications' => 'nullable|array',
            
            // Variants
            'variants' => 'nullable|array',
            'variants.*.name' => 'required_with:variants|string',
            'variants.*.attributes' => 'nullable|array',
            'variants.*.price_adjustment' => 'nullable|numeric',
            'variants.*.stock_quantity' => 'required_with:variants|integer|min:0',
        ];
    }
    
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Image validation messages
            'images.*.image' => 'Each file must be an image',
            'images.*.mimes' => 'Images must be of type: jpeg, png, jpg, gif, or webp',
            'images.*.max' => 'Each image must not exceed 2MB',
            'main_image.image' => 'Main image must be an image file',
            'main_image.mimes' => 'Main image must be of type: jpeg, png, jpg, or webp',
            'main_image.max' => 'Main image must not exceed 2MB',
            
            // Other validation messages
            'name.required' => 'Product name is required',
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Selected category does not exist',
            'brand_id.required' => 'Brand is required',
            'brand_id.exists' => 'Selected brand does not exist',
            'base_price.required' => 'Base price is required',
            'base_price.numeric' => 'Base price must be a number',
            'stock_quantity.required' => 'Stock quantity is required',
            'stock_quantity.integer' => 'Stock quantity must be an integer',
            'sku.required' => 'SKU is required',
            'sku.unique' => 'This SKU is already taken',
        ];
    }
    
    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->is_featured ?? false,
            'low_stock_threshold' => $this->low_stock_threshold ?? 10,
            'description' => $this->description ?? '',
            'short_description' => $this->short_description ?? '',
        ]);
    }
    
    /**
     * Get all validated data including files.
     */
    public function validatedWithFiles(): array
    {
        $data = $this->validated();
        
        // Handle images upload
        if ($this->hasFile('images')) {
            $data['images'] = $this->file('images');
        }
        
        // Handle main image upload
        if ($this->hasFile('main_image')) {
            $data['main_image'] = $this->file('main_image');
        }
        
        return $data;
    }
}