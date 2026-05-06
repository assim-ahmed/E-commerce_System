<?php

namespace App\Contracts\Services;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;

interface BrandServiceInterface
{
    public function getAllBrands(): Collection;
    public function getBrand(int $id): ?Brand;
    public function getBrandBySlug(string $slug): ?Brand;
    public function createBrand(array $data): Brand;
    public function updateBrand(int $id, array $data): bool;
    public function deleteBrand(int $id): bool;
}