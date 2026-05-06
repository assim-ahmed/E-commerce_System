<?php

namespace App\Contracts\Repositories;



interface BrandRepositoryInterface extends RepositoryInterface
{
    // هذه الميثودز خاصة بـ Brand
    public function findBySlug(string $slug);
    
}