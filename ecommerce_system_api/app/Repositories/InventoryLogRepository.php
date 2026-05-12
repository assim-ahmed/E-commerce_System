<?php

namespace App\Repositories;

use App\Contracts\Repositories\InventoryLogRepositoryInterface;
use App\Models\InventoryLog;
use App\Models\Product;

class InventoryLogRepository implements InventoryLogRepositoryInterface
{
    protected $model;
    
    public function __construct(InventoryLog $inventoryLog)
    {
        $this->model = $inventoryLog;
    }
    
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    
    public function getForProduct(int $productId, int $limit = 50)
    {
        return $this->model
            ->where('product_id', $productId)
            ->whereNull('product_variant_id')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function getForVariant(int $variantId, int $limit = 50)
    {
        return $this->model
            ->where('product_variant_id', $variantId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function getLowStockProducts(int $threshold = null)
    {
        $threshold = $threshold ?? 5;
        
        return Product::where('stock_quantity', '<=', $threshold)
            ->where('stock_quantity', '>', 0)
            ->get();
    }
}