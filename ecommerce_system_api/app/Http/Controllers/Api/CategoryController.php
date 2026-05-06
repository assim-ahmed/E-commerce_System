<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Contracts\Services\CategoryServiceInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryServiceInterface $categoryService;

    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        
        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
            'message' => 'Categories retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created category
     */
    public function store(CategoryRequest $request)
    {
        $category = $this->categoryService->createCategory($request->validated());
        
        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
            'message' => 'Category created successfully'
        ], 201);
    }

    /**
     * Display the specified category
     */
    public function show($id)
    {
        $category = $this->categoryService->getCategory($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Category not found',
                'errors' => null
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category->loadCount('products')),
            'message' => 'Category retrieved successfully'
        ], 200);
    }

    /**
     * Display category by slug
     */
    public function showBySlug($slug)
    {
        $category = $this->categoryService->getCategoryBySlug($slug);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Category not found',
                'errors' => null
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category->loadCount('products')),
            'message' => 'Category retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified category
     */
    public function update(CategoryRequest $request, $id)
    {
        $category = $this->categoryService->getCategory($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Category not found',
                'errors' => null
            ], 404);
        }
        
        $updated = $this->categoryService->updateCategory($id, $request->validated());
        
        if (!$updated) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to update category',
                'errors' => null
            ], 400);
        }
        
        $category = $this->categoryService->getCategory($id);
        
        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
            'message' => 'Category updated successfully'
        ], 200);
    }

    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        $category = $this->categoryService->getCategory($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Category not found',
                'errors' => null
            ], 404);
        }
        
        // Check if category has products
        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Cannot delete category with associated products',
                'errors' => null
            ], 400);
        }
        
        $this->categoryService->deleteCategory($id);
        
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Category deleted successfully'
        ], 200);
    }
}