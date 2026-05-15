<?php
// app/Contracts/Services/ProductServiceInterface.php

namespace App\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;

interface ProductServiceInterface
{
    public function getAllProducts(array $filters = []): LengthAwarePaginator;
    public function getProductById(int $id): ?Product;
    public function getProductBySlug(string $slug): ?Product;
    public function createProduct(array $data): Product;
    public function updateProduct(int $id, array $data): ?Product;
    public function deleteProduct(int $id): bool;
    public function updateProductStock(int $id, int $quantity): ?Product;
    public function getFeaturedProducts(int $limit = 10): Collection;
    public function getLowStockProducts(): Collection;
    public function getRelatedProducts(int $productId, int $limit = 5): Collection;
}