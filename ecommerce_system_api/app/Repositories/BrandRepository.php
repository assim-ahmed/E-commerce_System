<?php

namespace App\Repositories;

use App\Contracts\Repositories\BrandRepositoryInterface;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;

class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    public function __construct(Brand $model)
    {
        parent::__construct($model);
    }

    public function all(): Collection
    {
        return $this->model->withCount('products')->get();
    }

    public function find(int $id): ?Brand
    {
        return $this->model->find($id);
    }

    public function create(array $data): Brand
    {
        return $this->model->create($data);
    }

    public function findBySlug(string $slug): ?Brand
    {
        return $this->findBy('slug', $slug);
    }
}