<?php

namespace App\Contracts\Repositories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;

interface BrandRepositoryInterface extends RepositoryInterface
{
    // هذه الميثودز خاصة بـ Brand
    public function findBySlug(string $slug): ?Brand;
    
    // إعادة تعريف الميثودز مع return type محدد (اختياري)
    public function find(int $id): ?Brand;
    public function all(): Collection;
    public function create(array $data): Brand;
}