<?php

namespace App\Contracts\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;

interface ReviewRepositoryInterface
{
    /**
     * Get approved reviews for a product with pagination
     */
    public function getProductReviews(int $productId, int $perPage = 10): LengthAwarePaginator;
    
    /**
     * Get all reviews (Admin only) with pagination
     */
    public function getAllReviews(int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Get a single review by ID
     */
    public function findById(int $reviewId);
    
    /**
     * Get a single review by ID with relations (user, product)
     */
    public function findByIdWithRelations(int $reviewId);
    
    /**
     * Create a new review
     */
    public function create(array $data);
    
    /**
     * Update a review
     */
    public function update(int $reviewId, array $data): bool;
    
    /**
     * Delete a review
     */
    public function delete(int $reviewId): bool;
    
    /**
     * Approve a review
     */
    public function approve(int $reviewId): bool;
    
    /**
     * Check if user has already reviewed a product
     */
    public function hasUserReviewedProduct(int $userId, int $productId): bool;
    
    /**
     * Get user's review for a specific product
     */
    public function getUserProductReview(int $userId, int $productId);
    
    /**
     * Check if user purchased the product (completed order)
     */
    public function hasUserPurchasedProduct(int $userId, int $productId): bool;
    
    /**
     * Update product average rating
     */
    public function updateProductRating(int $productId): void;
}