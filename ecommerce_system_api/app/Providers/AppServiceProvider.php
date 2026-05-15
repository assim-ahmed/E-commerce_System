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


use App\Contracts\Repositories\CouponRepositoryInterface;
use App\Repositories\CouponRepository;

use App\Contracts\Services\CouponServiceInterface;
use App\Services\CouponService;





use App\Contracts\Repositories\InventoryLogRepositoryInterface;
use App\Repositories\InventoryLogRepository;


use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Repositories\OrderRepository;
use App\Contracts\Services\OrderServiceInterface;
use App\Services\OrderService;


use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\Repositories\ReviewRepository;
use App\Contracts\Services\ReviewServiceInterface;
use App\Services\ReviewService;



use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Repositories\NotificationRepository;
use App\Contracts\Services\NotificationServiceInterface;
use App\Services\NotificationService;


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

        $this->app->bind(InventoryLogRepositoryInterface::class, InventoryLogRepository::class);

        // Register Order Repository
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);

        // Register Order Service
        $this->app->bind(OrderServiceInterface::class, OrderService::class);


        // Register Review Repository
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);

        // Register Review Service
        $this->app->bind(ReviewServiceInterface::class, ReviewService::class);

        // Register Notification Repository
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);

        // Register Notification Service
        $this->app->bind(NotificationServiceInterface::class, NotificationService::class);


        // Coupon
        $this->app->bind(CouponRepositoryInterface::class, CouponRepository::class);
        $this->app->bind(CouponServiceInterface::class, CouponService::class);
    }



    public function boot(): void
    {
        //
    }
}
