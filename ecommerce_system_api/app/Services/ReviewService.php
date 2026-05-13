<?php

namespace App\Services;

use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\Contracts\Services\ReviewServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ReviewService implements ReviewServiceInterface
{
    protected $reviewRepository;
    
    public function __construct(ReviewRepositoryInterface $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }
    
    public function getProductReviews(int $productId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->reviewRepository->getProductReviews($productId, $perPage);
    }
    
    public function getAllReviews(int $perPage = 15): LengthAwarePaginator
    {
        return $this->reviewRepository->getAllReviews($perPage);
    }
    
    public function getReviewById(int $reviewId, int $userId, bool $isAdmin = false)
    {
        $review = $this->reviewRepository->findByIdWithRelations($reviewId);
        
        if (!$review) {
            throw new Exception('Review not found');
        }
        
        // Check if user is authorized to view this review
        if (!$isAdmin && $review->user_id !== $userId) {
            throw new Exception('Unauthorized to view this review');
        }
        
        return $review;
    }
    
    public function createReview(array $data, int $userId): array
    {
        // Check if user has purchased the product
        if (!$this->reviewRepository->hasUserPurchasedProduct($userId, $data['product_id'])) {
            throw new Exception('You can only review products you have purchased');
        }
        
        // Check if user has already reviewed this product
        if ($this->reviewRepository->hasUserReviewedProduct($userId, $data['product_id'])) {
            throw new Exception('You have already reviewed this product');
        }
        
        DB::beginTransaction();
        
        try {
            $reviewData = [
                'user_id' => $userId,
                'product_id' => $data['product_id'],
                'order_id' => $data['order_id'],
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
                'images' => $data['images'] ?? null,
                'is_approved' => false, // Requires admin approval
            ];
            
            $review = $this->reviewRepository->create($reviewData);
            
            DB::commit();
            
            return [
                'review' => $review,
                'message' => 'Review submitted successfully and pending admin approval'
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Review creation failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function updateReview(int $reviewId, array $data, int $userId): bool
    {
        $review = $this->reviewRepository->findById($reviewId);
        
        if (!$review) {
            throw new Exception('Review not found');
        }
        
        // Check if user owns the review
        if ($review->user_id !== $userId) {
            throw new Exception('Unauthorized to update this review');
        }
        
        // Check if review can be edited (not approved yet)
        if ($review->is_approved) {
            throw new Exception('Approved reviews cannot be edited');
        }
        
        DB::beginTransaction();
        
        try {
            $updateData = [];
            
            if (isset($data['rating'])) {
                $updateData['rating'] = $data['rating'];
            }
            if (isset($data['comment'])) {
                $updateData['comment'] = $data['comment'];
            }
            if (isset($data['images'])) {
                $updateData['images'] = $data['images'];
            }
            
            $updated = $this->reviewRepository->update($reviewId, $updateData);
            
            DB::commit();
            return $updated;
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Review update failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function deleteReview(int $reviewId, int $userId, bool $isAdmin = false): bool
    {
        $review = $this->reviewRepository->findById($reviewId);
        
        if (!$review) {
            throw new Exception('Review not found');
        }
        
        // Check authorization
        if (!$isAdmin && $review->user_id !== $userId) {
            throw new Exception('Unauthorized to delete this review');
        }
        
        DB::beginTransaction();
        
        try {
            $deleted = $this->reviewRepository->delete($reviewId);
            
            // Update product rating after deletion
            if ($deleted && $review->is_approved) {
                $this->reviewRepository->updateProductRating($review->product_id);
            }
            
            DB::commit();
            return $deleted;
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Review deletion failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function approveReview(int $reviewId, int $adminId): bool
    {
        $review = $this->reviewRepository->findById($reviewId);
        
        if (!$review) {
            throw new Exception('Review not found');
        }
        
        if ($review->is_approved) {
            throw new Exception('Review is already approved');
        }
        
        DB::beginTransaction();
        
        try {
            $approved = $this->reviewRepository->approve($reviewId);
            DB::commit();
            return $approved;
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Review approval failed: ' . $e->getMessage());
            throw $e;
        }
    }
}