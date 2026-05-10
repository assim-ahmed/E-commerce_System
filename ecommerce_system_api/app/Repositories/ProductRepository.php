<?php
// app/Repositories/ProductRepository.php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\InventoryLog;
use App\Contracts\Repositories\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductRepository implements ProductRepositoryInterface
{
    protected Product $model;
    
    public function __construct(Product $model)
    {
        $this->model = $model;
    }
    
    public function getProductsWithFilters(array $filters): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->with(['category', 'brand'])
            ->where('is_active', true);
        
        // Search filter
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('short_description', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('description', 'LIKE', "%{$filters['search']}%");
            });
        }
        
        // Category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        // Brand filter
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }
        
        // Price range filter
        if (!empty($filters['min_price'])) {
            $query->where('base_price', '>=', $filters['min_price']);
        }
        
        if (!empty($filters['max_price'])) {
            $query->where('base_price', '<=', $filters['max_price']);
        }
        
        // Featured filter
        if (!empty($filters['is_featured'])) {
            $query->where('is_featured', true);
        }
        
        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $allowedSorts = ['name', 'base_price', 'created_at', 'views_count'];
        
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $perPage = $filters['per_page'] ?? 15;
        
        return $query->paginate($perPage);
    }
    
    public function getById(int $id, array $relations = [])
    {
        return $this->model->with($relations)->find($id);
    }
    
    public function getBySlug(string $slug, array $relations = [])
    {
        return $this->model->with($relations)->where('slug', $slug)->first();
    }
    
    public function createProduct(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Handle JSON fields
            if (isset($data['images']) && is_array($data['images'])) {
                $data['images'] = json_encode($data['images']);
            }
            
            if (isset($data['specifications']) && is_array($data['specifications'])) {
                $data['specifications'] = json_encode($data['specifications']);
            }
            
            $product = $this->model->create($data);
            
            // Create variants if provided
            if (!empty($data['variants']) && is_array($data['variants'])) {
                $this->createVariants($product->id, $data['variants']);
            }
            
            return $product->load(['category', 'brand', 'variants']);
        });
    }
    
    public function updateProduct(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $product = $this->getById($id);
            
            if (!$product) {
                return false;
            }
            
            // Handle JSON fields
            if (isset($data['images']) && is_array($data['images'])) {
                $data['images'] = json_encode($data['images']);
            }
            
            if (isset($data['specifications']) && is_array($data['specifications'])) {
                $data['specifications'] = json_encode($data['specifications']);
            }
            
            $updated = $product->update($data);
            
            // Update variants if provided
            if (!empty($data['variants'])) {
                $this->syncVariants($id, $data['variants']);
            }
            
            return $updated;
        });
    }
    
    public function deleteProduct(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $product = $this->getById($id);
            
            if (!$product) {
                return false;
            }
            
            // Delete variants first
            $product->variants()->delete();
            
            return $product->delete();
        });
    }
    
    public function updateStock(int $productId, int $quantity, string $type, ?int $variantId = null): bool
    {
        return DB::transaction(function () use ($productId, $quantity, $type, $variantId) {
            $product = $this->getById($productId);
            
            if (!$product) {
                return false;
            }
            
            $quantityBefore = $product->stock_quantity;
            
            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                if ($variant) {
                    $variant->stock_quantity += $quantity;
                    $variant->save();
                    $product->stock_quantity = $product->variants()->sum('stock_quantity');
                }
            } else {
                $product->stock_quantity += $quantity;
            }
            
            $product->is_low_stock = $product->stock_quantity <= $product->low_stock_threshold;
            $product->save();
            
            InventoryLog::create([
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'type' => $type,
                'quantity_change' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $product->stock_quantity,
            ]);
            
            return true;
        });
    }
    
    public function getFeaturedProducts(int $limit = 10): Collection
    {
        return $this->model->where('is_featured', true)
            ->where('is_active', true)
            ->with(['category', 'brand'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function getLowStockProducts(int $threshold = 5): Collection
    {
        return $this->model->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', $threshold)
            ->with(['category', 'brand'])
            ->get();
    }
    
    public function getRelatedProducts(int $productId, int $limit = 5): Collection
    {
        $product = $this->getById($productId);
        
        if (!$product) {
            return new Collection();
        }
        
        return $this->model->where('category_id', $product->category_id)
            ->where('id', '!=', $productId)
            ->where('is_active', true)
            ->with(['category', 'brand'])
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Create variants for product
     */
    protected function createVariants(int $productId, array $variants): void
    {
        foreach ($variants as $variant) {
            if (isset($variant['attributes']) && is_array($variant['attributes'])) {
                $variant['attributes'] = json_encode($variant['attributes']);
            }
            
            $variant['product_id'] = $productId;
            ProductVariant::create($variant);
        }
    }
    
    /**
     * Sync variants (create, update, delete)
     */
    protected function syncVariants(int $productId, array $variants): void
    {
        $product = $this->getById($productId);
        $existingVariants = $product->variants;
        $existingVariantIds = $existingVariants->pluck('id')->toArray();
        $updatedVariantIds = [];
        
        foreach ($variants as $variant) {
            if (isset($variant['id']) && in_array($variant['id'], $existingVariantIds)) {
                $variantModel = ProductVariant::find($variant['id']);
                if ($variantModel) {
                    if (isset($variant['attributes']) && is_array($variant['attributes'])) {
                        $variant['attributes'] = json_encode($variant['attributes']);
                    }
                    $variantModel->update($variant);
                    $updatedVariantIds[] = $variantModel->id;
                }
            } else {
                if (isset($variant['attributes']) && is_array($variant['attributes'])) {
                    $variant['attributes'] = json_encode($variant['attributes']);
                }
                $variant['product_id'] = $productId;
                $newVariant = ProductVariant::create($variant);
                $updatedVariantIds[] = $newVariant->id;
            }
        }
        
        $toDelete = array_diff($existingVariantIds, $updatedVariantIds);
        if (!empty($toDelete)) {
            ProductVariant::whereIn('id', $toDelete)->delete();
        }
    }
}