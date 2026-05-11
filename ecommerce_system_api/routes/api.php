<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;


use Illuminate\Support\Facades\Route;

// Public Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed'])
    ->name('verification.verify');

// Public Category Routes (anyone can view)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/categories/slug/{slug}', [CategoryController::class, 'showBySlug']);

// Public Brand Routes (anyone can view)
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/brands/{id}', [BrandController::class, 'show']);
Route::get('/brands/slug/{slug}', [BrandController::class, 'showBySlug']);

// Protected Routes (require authentication + email verification)
Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/email/verification-notification', [AuthController::class, 'sendVerificationEmail']);
    
    // Admin only routes
    Route::middleware(['admin'])->group(function () {
        // Category Management
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
        
        // Brand Management
        Route::post('/brands', [BrandController::class, 'store']);
        Route::put('/brands/{id}', [BrandController::class, 'update']);
        Route::delete('/brands/{id}', [BrandController::class, 'destroy']);
    });

});



// Products Routes
Route::prefix('products')->group(function () {
    
    // ========== Public routes (must come BEFORE routes with {id}) ==========
    Route::get('/featured', [ProductController::class, 'featured']);
    Route::get('/slug/{slug}', [ProductController::class, 'showBySlug']);
    
    // ========== Admin only routes (must come BEFORE routes with {id}) ==========
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/low-stock', [ProductController::class, 'lowStock']);
        Route::post('/', [ProductController::class, 'store']);
        Route::post('/{id}/stock', [ProductController::class, 'updateStock']);
    });
    
    // ========== Routes with {id} (must come LAST) ==========
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
    
    // ========== Admin routes with {id} ==========
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });
});


Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/items', [CartController::class, 'addItem']);
    Route::put('/items/{cartItemId}', [CartController::class, 'updateItem']);
    Route::delete('/items/{cartItemId}', [CartController::class, 'removeItem']);
    Route::delete('/clear', [CartController::class, 'clear']);
});


Route::get('/test-cookie', function() {
    return response()
        ->json(['message' => 'Cookie test'])
        ->cookie('test_cookie', 'hello123', 60);
});