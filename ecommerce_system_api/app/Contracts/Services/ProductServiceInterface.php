<?php
// app/Contracts/Services/ProductServiceInterface.php

namespace App\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductServiceInterface
{
    /**
     * Get products with filters and pagination
     */
    public function getProducts(array $filters): LengthAwarePaginator;
    
    /**
     * Get single product by ID
     */
    public function getProductById(int $id);
    
    /**
     * Get single product by slug
     */
    public function getProductBySlug(string $slug);
    
    /**
     * Create new product
     */
    public function createProduct(array $data);
    
    /**
     * Update existing product
     */
    public function updateProduct(int $id, array $data);
    
    /**
     * Delete product
     */
    public function deleteProduct(int $id): bool;
    
    /**
     * Update product stock
     */
    public function updateStock(int $id, array $data);
    
    /**
     * Get featured products
     */
    public function getFeaturedProducts(int $limit = 10): Collection;
    
    /**
     * Get low stock products
     */
    public function getLowStockProducts(): Collection;
    
    /**
     * Get related products
     */
    public function getRelatedProducts(int $productId, int $limit = 5): Collection;
}