<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\BrandRequest;
use App\Http\Resources\BrandResource;
use App\Contracts\Services\BrandServiceInterface;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected BrandServiceInterface $brandService;

    public function __construct(BrandServiceInterface $brandService)
    {
        $this->brandService = $brandService;
    }

    /**
     * Display a listing of brands
     */
    public function index()
    {
        $brands = $this->brandService->getAllBrands();
        
        return response()->json([
            'success' => true,
            'data' => BrandResource::collection($brands),
            'message' => 'Brands retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created brand
     */
    public function store(BrandRequest $request)
    {
        $brand = $this->brandService->createBrand($request->validated());
        
        return response()->json([
            'success' => true,
            'data' => new BrandResource($brand),
            'message' => 'Brand created successfully'
        ], 201);
    }

    /**
     * Display the specified brand
     */
    public function show($id)
    {
        $brand = $this->brandService->getBrand($id);
        
        if (!$brand) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Brand not found',
                'errors' => null
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new BrandResource($brand->loadCount('products')),
            'message' => 'Brand retrieved successfully'
        ], 200);
    }

    /**
     * Display brand by slug
     */
    public function showBySlug($slug)
    {
        $brand = $this->brandService->getBrandBySlug($slug);
        
        if (!$brand) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Brand not found',
                'errors' => null
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new BrandResource($brand->loadCount('products')),
            'message' => 'Brand retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified brand
     */
    public function update(BrandRequest $request, $id)
    {
        $brand = $this->brandService->getBrand($id);
        
        if (!$brand) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Brand not found',
                'errors' => null
            ], 404);
        }
        
        $updated = $this->brandService->updateBrand($id, $request->validated());
        
        if (!$updated) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to update brand',
                'errors' => null
            ], 400);
        }
        
        $brand = $this->brandService->getBrand($id);
        
        return response()->json([
            'success' => true,
            'data' => new BrandResource($brand),
            'message' => 'Brand updated successfully'
        ], 200);
    }

    /**
     * Remove the specified brand
     */
    public function destroy($id)
    {
        $brand = $this->brandService->getBrand($id);
        
        if (!$brand) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Brand not found',
                'errors' => null
            ], 404);
        }
        
        // Check if brand has products
        if ($brand->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Cannot delete brand with associated products',
                'errors' => null
            ], 400);
        }
        
        $this->brandService->deleteBrand($id);
        
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Brand deleted successfully'
        ], 200);
    }
}