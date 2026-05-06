<?php

namespace App\Contracts\Repositories;



interface CategoryRepositoryInterface extends RepositoryInterface
{
    // هذه الميثودز خاصة بـ Category
    public function findBySlug(string $slug);
    
}