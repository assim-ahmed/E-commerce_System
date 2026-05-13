<?php

namespace App\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;

interface ReviewServiceInterface
{
    /**
     * Get approved reviews for a product
     */
    public function getProductReviews(int $productId, int $perPage = 10): LengthAwarePaginator;
    
    /**
     * Get all reviews (Admin only)
     */
    public function getAllReviews(int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Get single review by ID
     */
    public function getReviewById(int $reviewId, int $userId, bool $isAdmin = false);
    
    /**
     * Create a new review
     */
    public function createReview(array $data, int $userId): array;
    
    /**
     * Update a review
     */
    public function updateReview(int $reviewId, array $data, int $userId): bool;
    
    /**
     * Delete a review
     */
    public function deleteReview(int $reviewId, int $userId, bool $isAdmin = false): bool;
    
    /**
     * Approve a review (Admin only)
     */
    public function approveReview(int $reviewId, int $adminId): bool;
}