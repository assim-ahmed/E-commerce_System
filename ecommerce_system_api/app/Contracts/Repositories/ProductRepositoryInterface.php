<?php
// app/Contracts/Repositories/ProductRepositoryInterface.php

namespace App\Contracts\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    /**
     * Get products with filters and pagination
     */
    public function getProductsWithFilters(array $filters): LengthAwarePaginator;
    
    /**
     * Get product by ID
     */
    public function getById(int $id, array $relations = []);
    
    /**
     * Get product by slug
     */
    public function getBySlug(string $slug, array $relations = []);
    
    /**
     * Create new product
     */
    public function createProduct(array $data);
    
    /**
     * Update existing product
     */
    public function updateProduct(int $id, array $data): bool;
    
    /**
     * Delete product
     */
    public function deleteProduct(int $id): bool;
    
    /**
     * Update product stock
     */
    public function updateStock(int $productId, int $quantity, string $type, ?int $variantId = null): bool;
    
    /**
     * Get featured products
     */
    public function getFeaturedProducts(int $limit = 10): Collection;
    
    /**
     * Get low stock products
     */
    public function getLowStockProducts(int $threshold = 5): Collection;
    
    /**
     * Get related products
     */
    public function getRelatedProducts(int $productId, int $limit = 5): Collection;
}