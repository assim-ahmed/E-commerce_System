<?php
// app/Repositories/ProductRepository.php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function all(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('sku', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['min_price'])) {
            $query->where('base_price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('base_price', '<=', $filters['max_price']);
        }

        if (!empty($filters['featured'])) {
            $query->where('is_featured', true);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $filters['per_page'] ?? 15;

        return $query->with(['category', 'brand'])->paginate($perPage);
    }

    public function findById(int $id): ?Product
    {
        return $this->model->newQuery()
            ->with(['category', 'brand', 'variants'])
            ->find($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->model->newQuery()
            ->with(['category', 'brand', 'variants'])
            ->where('slug', $slug)
            ->first();
    }

    public function create(array $data): Product
    {
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Product
    {
        $product = $this->findById($id);

        if (!$product) {
            return null;
        }

        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name'], $id);
        }

        $product->update($data);

        return $product->fresh();
    }

    public function delete(int $id): bool
    {
        $product = $this->findById($id);

        if (!$product) {
            return false;
        }

        return $product->delete();
    }

    public function updateStock(int $id, int $quantity): ?Product
    {
        $product = $this->findById($id);

        if (!$product) {
            return null;
        }

        $threshold = $product->low_stock_threshold ?? 10;
        $isLowStock = $quantity <= $threshold;

        $product->update([
            'stock_quantity' => $quantity,
            'is_low_stock' => $isLowStock
        ]);

        return $product->fresh();
    }

    public function getFeatured(int $limit = 10): Collection
    {
        return $this->model->newQuery()
            ->where('is_featured', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getLowStock(): Collection
    {
        return $this->model->newQuery()
            ->where('is_low_stock', true)
            ->orWhereRaw('stock_quantity <= low_stock_threshold')
            ->orderBy('stock_quantity', 'asc')
            ->get();
    }

    public function getRelated(int $productId, int $limit = 5): Collection
    {
        $product = $this->findById($productId);

        if (!$product) {
            return new Collection();
        }

        $related = $this->model->newQuery()
            ->where('id', '!=', $productId)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id)
                      ->orWhere('brand_id', $product->brand_id);
            })
            ->where('stock_quantity', '>', 0)
            ->orderBy('is_featured', 'desc')
            ->limit($limit)
            ->get();

        return $related;
    }

    protected function generateSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        $query = $this->model->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $query = $this->model->where('slug', $slug);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            $counter++;
        }

        return $slug;
    }
}