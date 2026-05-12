<?php

namespace App\Contracts\Repositories;

interface InventoryLogRepositoryInterface
{
    /**
     * Create a new inventory log entry
     */
    public function create(array $data);
    
    /**
     * Get inventory logs for a specific product
     */
    public function getForProduct(int $productId, int $limit = 50);
    
    /**
     * Get inventory logs for a specific product variant
     */
    public function getForVariant(int $variantId, int $limit = 50);
    
    /**
     * Get low stock products (with alert)
     */
    public function getLowStockProducts(int $threshold = null);
}