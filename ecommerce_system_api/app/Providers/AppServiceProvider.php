<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Contracts\Services\AuthServiceInterface;
use App\Services\AuthService;


use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Contracts\Services\CategoryServiceInterface;
use App\Services\CategoryService;
use App\Contracts\Repositories\BrandRepositoryInterface;
use App\Repositories\BrandRepository;
use App\Contracts\Services\BrandServiceInterface;
use App\Services\BrandService;


use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\Repositories\ProductRepository;
use App\Services\ProductService;


use App\Contracts\Repositories\CartRepositoryInterface;
use App\Contracts\Services\CartServiceInterface;
use App\Repositories\CartRepository;
use App\Services\CartService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Existing bindings
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);

        // New Category bindings
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);

        // New Brand bindings
        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
        $this->app->bind(BrandServiceInterface::class, BrandService::class);


        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        $this->app->bind(
            ProductServiceInterface::class,
            ProductService::class
        );


        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
        $this->app->bind(CartServiceInterface::class, CartService::class);
    }

    public function boot(): void
    {
        //
    }
}
