<?php
// app/Contracts/Repositories/ProductRepositoryInterface.php

namespace App\Contracts\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;

interface ProductRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?Product;
    public function findBySlug(string $slug): ?Product;
    public function create(array $data): Product;
    public function update(int $id, array $data): ?Product;
    public function delete(int $id): bool;
    public function updateStock(int $id, int $quantity): ?Product;
    public function getFeatured(int $limit = 10): Collection;
    public function getLowStock(): Collection;
    public function getRelated(int $productId, int $limit = 5): Collection;
}