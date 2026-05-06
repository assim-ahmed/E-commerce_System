<?php

namespace App\Services;

use App\Contracts\Repositories\BrandRepositoryInterface;
use App\Contracts\Services\BrandServiceInterface;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class BrandService implements BrandServiceInterface
{
    protected BrandRepositoryInterface $brandRepository;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function getAllBrands(): Collection
    {
        return $this->brandRepository->all();
    }

    public function getBrand(int $id): ?Brand
    {
        return $this->brandRepository->find($id);
    }

    public function getBrandBySlug(string $slug): ?Brand
    {
        return $this->brandRepository->findBySlug($slug);
    }

    public function createBrand(array $data): Brand
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        return $this->brandRepository->create($data);
    }

    public function updateBrand(int $id, array $data): bool
    {
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        return $this->brandRepository->update($id, $data);
    }

    public function deleteBrand(int $id): bool
    {
        return $this->brandRepository->delete($id);
    }
}