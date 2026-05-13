<?php

namespace App\Repositories;

use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\Models\Review;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReviewRepository implements ReviewRepositoryInterface
{
    protected $model;
    
    public function __construct(Review $review)
    {
        $this->model = $review;
    }
    
    public function getProductReviews(int $productId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('is_approved', true)
            ->with(['user' => function($q) {
                $q->select('id', 'name');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function getAllReviews(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['user', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function findById(int $reviewId)
    {
        return $this->model->find($reviewId);
    }
    
    public function findByIdWithRelations(int $reviewId)
    {
        return $this->model
            ->with(['user', 'product'])
            ->find($reviewId);
    }
    
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    
    public function update(int $reviewId, array $data): bool
    {
        $review = $this->model->find($reviewId);
        if (!$review) {
            return false;
        }
        return $review->update($data);
    }
    
    public function delete(int $reviewId): bool
    {
        $review = $this->model->find($reviewId);
        if (!$review) {
            return false;
        }
        return $review->delete();
    }
    
    public function approve(int $reviewId): bool
    {
        $review = $this->model->find($reviewId);
        if (!$review) {
            return false;
        }
        
        DB::beginTransaction();
        try {
            $review->update(['is_approved' => true]);
            $this->updateProductRating($review->product_id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    
    public function hasUserReviewedProduct(int $userId, int $productId): bool
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }
    
    public function getUserProductReview(int $userId, int $productId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }
    
    public function hasUserPurchasedProduct(int $userId, int $productId): bool
    {
        return OrderItem::whereHas('order', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('status', 'completed');
        })->where('product_id', $productId)
          ->exists();
    }
    
    public function updateProductRating(int $productId): void
    {
        $product = Product::find($productId);
        if ($product) {
            $avgRating = $product->approvedReviews()->avg('rating') ?? 0;
            $reviewsCount = $product->approvedReviews()->count();
            
            $product->update([
                'average_rating' => round($avgRating, 1),
                'reviews_count' => $reviewsCount,
            ]);
        }
    }
}