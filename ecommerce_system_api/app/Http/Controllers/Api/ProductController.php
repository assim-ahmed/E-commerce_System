<?php
// app/Http/Controllers/Api/ProductController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Resources\Product\ProductResource;

use App\Contracts\Services\ProductServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'category_id', 'brand_id', 'search', 'min_price', 'max_price',
            'featured', 'sort_by', 'sort_order', 'per_page'
        ]);

        $products = $this->productService->getAllProducts($filters);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total()
            ],
            'message' => 'Products retrieved successfully'
        ]);
    }

    public function featured(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $products = $this->productService->getFeaturedProducts($limit);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
            'message' => 'Featured products retrieved successfully'
        ]);
    }

    public function showBySlug(string $slug): JsonResponse
    {
        $product = $this->productService->getProductBySlug($slug);

        if (!$product) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
            'message' => 'Product retrieved successfully'
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
            'message' => 'Product retrieved successfully'
        ]);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $data = $request->validatedWithFiles();
            $product = $this->productService->createProduct($data);

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
                'message' => 'Product created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create product',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function update(ProductRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validatedWithFiles();
            $product = $this->productService->updateProduct($id, $data);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
                'message' => 'Product updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to update product',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->productService->deleteProduct($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Product deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to delete product',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStock(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:0'
        ]);

        try {
            $product = $this->productService->updateProductStock($id, $request->stock_quantity);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
                'message' => 'Stock updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to update stock',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function lowStock(): JsonResponse
    {
        $products = $this->productService->getLowStockProducts();

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
            'message' => 'Low stock products retrieved successfully'
        ]);
    }
}