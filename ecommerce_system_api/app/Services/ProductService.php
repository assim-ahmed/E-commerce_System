<?php
// app/Services/ProductService.php

namespace App\Services;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Services\ProductServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ProductService implements ProductServiceInterface
{
    protected ProductRepositoryInterface $repository;
    
    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    public function getProducts(array $filters): LengthAwarePaginator
    {
        return $this->repository->getProductsWithFilters($filters);
    }
    
    public function getProductById(int $id)
    {
        $product = $this->repository->getById($id, ['category', 'brand', 'variants']);
        
        if ($product) {
            $product->increment('views_count');
        }
        
        return $product;
    }
    
    public function getProductBySlug(string $slug)
    {
        $product = $this->repository->getBySlug($slug, ['category', 'brand', 'variants']);
        
        if ($product) {
            $product->increment('views_count');
        }
        
        return $product;
    }
    
    public function createProduct(array $data)
    {
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
            $originalSlug = $data['slug'];
            $counter = 1;
            
            while ($this->repository->getBySlug($data['slug'])) {
                $data['slug'] = $originalSlug . '-' . $counter++;
            }
        }
        
        return $this->repository->createProduct($data);
    }
    
    public function updateProduct(int $id, array $data)
    {
        // Handle slug update
        if (!empty($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
            $originalSlug = $data['slug'];
            $counter = 1;
            $existingProduct = $this->repository->getById($id);
            
            while ($existingProduct && 
                   $this->repository->getBySlug($data['slug']) && 
                   $this->repository->getBySlug($data['slug'])->id !== $id) {
                $data['slug'] = $originalSlug . '-' . $counter++;
            }
        }
        
        $updated = $this->repository->updateProduct($id, $data);
        
        if (!$updated) {
            return null;
        }
        
        return $this->repository->getById($id, ['category', 'brand', 'variants']);
    }
    
    public function deleteProduct(int $id): bool
    {
        return $this->repository->deleteProduct($id);
    }
    
    public function updateStock(int $id, array $data)
    {
        $quantity = $data['quantity'];
        $type = $data['type'];
        $variantId = $data['variant_id'] ?? null;
        
        $updated = $this->repository->updateStock($id, $quantity, $type, $variantId);
        
        if (!$updated) {
            return null;
        }
        
        return $this->repository->getById($id, ['category', 'brand', 'variants']);
    }
    
    public function getFeaturedProducts(int $limit = 10): Collection
    {
        return $this->repository->getFeaturedProducts($limit);
    }
    
    public function getLowStockProducts(): Collection
    {
        return $this->repository->getLowStockProducts();
    }
    
    public function getRelatedProducts(int $productId, int $limit = 5): Collection
    {
        return $this->repository->getRelatedProducts($productId, $limit);
    }
}