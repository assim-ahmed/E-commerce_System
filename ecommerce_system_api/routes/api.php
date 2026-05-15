<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// =============================================
// 1. PUBLIC ROUTES (No Authentication)
// =============================================

// Auth Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed'])
    ->name('verification.verify');

// Categories Public Routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/categories/slug/{slug}', [CategoryController::class, 'showBySlug']);

// Brands Public Routes
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/brands/{id}', [BrandController::class, 'show']);
Route::get('/brands/slug/{slug}', [BrandController::class, 'showBySlug']);

// Products Public Routes (must be before routes with {id})
Route::prefix('products')->group(function () {
    Route::get('/featured', [ProductController::class, 'featured']);
    Route::get('/slug/{slug}', [ProductController::class, 'showBySlug']);
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
});

// Coupons Public Routes
Route::get('/coupons/validate/{code}', [CouponController::class, 'validateCoupon']);

// Reviews Public Routes
Route::get('/products/{id}/reviews', [ReviewController::class, 'productReviews']);

// Test Cookie Route
Route::get('/test-cookie', function () {
    return response()
        ->json(['message' => 'Cookie test'])
        ->cookie('test_cookie', 'hello123', 60);
});

// =============================================
// 2. PROTECTED ROUTES (Authentication + Email Verification)
// =============================================
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    
    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/email/verification-notification', [AuthController::class, 'sendVerificationEmail']);
    
    // Address Routes
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
    
    // Cart Routes
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/items', [CartController::class, 'addItem']);
        Route::put('/items/{cartItemId}', [CartController::class, 'updateItem']);
        Route::delete('/items/{cartItemId}', [CartController::class, 'removeItem']);
        Route::delete('/clear', [CartController::class, 'clear']);
    });
    
    // Cart Coupon Routes
    Route::post('/cart/apply-coupon', [CouponController::class, 'applyCoupon']);
    Route::delete('/cart/coupon', [CouponController::class, 'removeCoupon']);
    
    // Orders Routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::delete('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    
    // Reviews Routes (Authenticated users)
    Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);
    Route::get('/reviews/{id}', [ReviewController::class, 'show']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
    
    // Notifications Routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unread']);
        Route::get('/unread/count', [NotificationController::class, 'unreadCount']);
        Route::get('/{id}', [NotificationController::class, 'show']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });
    
    // =============================================
    // 3. ADMIN ROUTES (Authentication + Email Verification + Admin Role)
    // =============================================
    Route::middleware(['admin'])->group(function () {
        
        // Category Management
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
        
        // Brand Management
        Route::post('/brands', [BrandController::class, 'store']);
        Route::put('/brands/{id}', [BrandController::class, 'update']);
        Route::delete('/brands/{id}', [BrandController::class, 'destroy']);
        
        // Products Management
        Route::prefix('products')->group(function () {
            Route::get('/low-stock', [ProductController::class, 'lowStock']);
            Route::post('/', [ProductController::class, 'store']);
            Route::post('/{id}/stock', [ProductController::class, 'updateStock']);
            Route::put('/{id}', [ProductController::class, 'update']);
            Route::delete('/{id}', [ProductController::class, 'destroy']);
        });
        
        // Orders Management
        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
        Route::get('/admin/orders', [OrderController::class, 'adminIndex']);
        
        // Reviews Management
        Route::get('/admin/reviews', [ReviewController::class, 'index']);
        Route::put('/admin/reviews/{id}/approve', [ReviewController::class, 'approve']);
        
        // Coupons Management
        Route::prefix('admin')->group(function () {
            Route::get('/coupons', [CouponController::class, 'index']);
            Route::get('/coupons/active', [CouponController::class, 'getActiveCoupons']);
            Route::get('/coupons/{id}', [CouponController::class, 'show']);
            Route::post('/coupons', [CouponController::class, 'store']);
            Route::put('/coupons/{id}', [CouponController::class, 'update']);
            Route::delete('/coupons/{id}', [CouponController::class, 'destroy']);
        });
        
        // =============================================
        // 4. DASHBOARD ROUTES (Admin Only)
        // =============================================
        Route::prefix('admin/dashboard')->group(function () {
            // Statistics
            Route::get('/stats', [DashboardController::class, 'getStats']);
            
            // Sales Reports (period: daily, weekly, monthly, yearly)
            Route::get('/sales', [DashboardController::class, 'getSalesReport']);
            
            // Top Selling Products
            Route::get('/top-products', [DashboardController::class, 'getTopProducts']);
            
            // Recent Orders
            Route::get('/recent-orders', [DashboardController::class, 'getRecentOrders']);
            
            // Inventory Summary
            Route::get('/inventory', [DashboardController::class, 'getInventorySummary']);
            
            // Clear Cache (optional)
            Route::post('/clear-cache', [DashboardController::class, 'clearCache']);
        });
    });
});