<?php

namespace App\Repositories;

use App\Contracts\Repositories\BrandRepositoryInterface;
use App\Models\Brand;


class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    public function __construct(Brand $model)
    {
        parent::__construct($model);
    }

    public function findBySlug(string $slug)
    {
        return $this->findBy('slug', $slug);
    }
}