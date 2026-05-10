<?php
// app/Http/Controllers/Api/ProductController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\ProductServiceInterface;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductCollection;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductServiceInterface $productService;
    
    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }
    
    public function index(Request $request)
    {
        $filters = $request->only([
            'search', 'category_id', 'brand_id', 'min_price', 'max_price',
            'is_featured', 'sort_by', 'sort_order', 'per_page'
        ]);
        
        $products = $this->productService->getProducts($filters);
        
        return (new ProductCollection($products))
            ->additional(['message' => 'Products retrieved successfully']);
    }
    
    public function show(int $id)
    {
        $product = $this->productService->getProductById($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Product not found',
                'errors' => ['id' => ['No product found with ID: ' . $id]]
            ], 404);
        }
        
        $relatedProducts = $this->productService->getRelatedProducts($id);
        
        return (new ProductResource($product))
            ->additional([
                'related_products' => $relatedProducts,
                'message' => 'Product retrieved successfully'
            ]);
    }
    
    public function showBySlug(string $slug)
    {
        $product = $this->productService->getProductBySlug($slug);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Product not found',
                'errors' => ['slug' => ['No product found with slug: ' . $slug]]
            ], 404);
        }
        
        $relatedProducts = $this->productService->getRelatedProducts($product->id);
        
        return (new ProductResource($product))
            ->additional([
                'related_products' => $relatedProducts,
                'message' => 'Product retrieved successfully'
            ]);
    }
    
    public function store(ProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated());
        
        return (new ProductResource($product))
            ->additional(['message' => 'Product created successfully'])
            ->response()
            ->setStatusCode(201);
    }
    
    public function update(ProductRequest $request, int $id)
    {
        $product = $this->productService->updateProduct($id, $request->validated());
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Product not found',
                'errors' => ['id' => ['No product found with ID: ' . $id]]
            ], 404);
        }
        
        return (new ProductResource($product))
            ->additional(['message' => 'Product updated successfully']);
    }
    
    public function destroy(int $id)
    {
        $deleted = $this->productService->deleteProduct($id);
        
        if (!$deleted) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Product not found',
                'errors' => ['id' => ['No product found with ID: ' . $id]]
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Product deleted successfully'
        ], 200);
    }
    
    public function updateStock(Request $request, int $id)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:purchase,sale,return,adjustment',
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);
        
        $product = $this->productService->updateStock($id, $request->only(['quantity', 'type', 'variant_id']));
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Product not found',
                'errors' => ['id' => ['No product found with ID: ' . $id]]
            ], 404);
        }
        
        return (new ProductResource($product))
            ->additional([
                'stock_quantity' => $product->stock_quantity,
                'is_low_stock' => $product->is_low_stock,
                'message' => 'Stock updated successfully'
            ]);
    }
    
    public function featured(Request $request)
    {
        $limit = $request->get('limit', 10);
        $products = $this->productService->getFeaturedProducts($limit);
        
        return (new ProductCollection($products))
            ->additional(['message' => 'Featured products retrieved successfully']);
    }
    
    public function lowStock()
    {
        $products = $this->productService->getLowStockProducts();
        
        return (new ProductCollection($products))
            ->additional(['message' => 'Low stock products retrieved successfully']);
    }
}