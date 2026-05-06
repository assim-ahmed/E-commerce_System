<?php

namespace App\Contracts\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryServiceInterface
{
    public function getAllCategories(): Collection;
    public function getCategory(int $id): ?Category;
    public function getCategoryBySlug(string $slug): ?Category;
    public function createCategory(array $data): Category;
    public function updateCategory(int $id, array $data): bool;
    public function deleteCategory(int $id): bool;
}