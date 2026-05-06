<?php

namespace App\Contracts\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface extends RepositoryInterface
{
    // هذه الميثودز خاصة بـ Category
    public function findBySlug(string $slug): ?Category;
    
    // إعادة تعريف الميثودز مع return type محدد (اختياري)
    public function find(int $id): ?Category;
    public function all(): Collection;
    public function create(array $data): Category;
}