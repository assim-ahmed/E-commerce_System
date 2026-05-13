<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ReviewServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\CreateReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Http\Requests\Review\ApproveReviewRequest;
use App\Http\Resources\Review\ReviewResource;
use App\Http\Resources\Review\ReviewCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewServiceInterface $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Get approved reviews for a specific product
     */
    public function productReviews(Request $request, int $productId): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $reviews = $this->reviewService->getProductReviews($productId, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => new ReviewCollection($reviews),
                'message' => 'Product reviews retrieved successfully'
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve reviews',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get all reviews (Admin only)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Unauthorized',
                    'errors' => ['Admin access required']
                ], 403);
            }
            
            $perPage = $request->get('per_page', 15);
            $reviews = $this->reviewService->getAllReviews($perPage);
            
            return response()->json([
                'success' => true,
                'data' => new ReviewCollection($reviews),
                'message' => 'All reviews retrieved successfully'
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve reviews',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get single review by ID
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $isAdmin = $user->role === 'admin';
            
            $review = $this->reviewService->getReviewById($id, $user->id, $isAdmin);
            
            return response()->json([
                'success' => true,
                'data' => new ReviewResource($review),
                'message' => 'Review retrieved successfully'
            ], 200);
            
        } catch (Exception $e) {
            $status = $e->getMessage() === 'Review not found' ? 404 : 403;
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $status);
        }
    }

    /**
     * Create a new review
     */
    public function store(CreateReviewRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $result = $this->reviewService->createReview(
                $request->validated(),
                $user->id
            );
            
            return response()->json([
                'success' => true,
                'data' => new ReviewResource($result['review']),
                'message' => $result['message']
            ], 201);
            
        } catch (Exception $e) {
            $status = str_contains($e->getMessage(), 'purchased') || 
                      str_contains($e->getMessage(), 'already reviewed') ? 422 : 500;
            
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $status);
        }
    }

    /**
     * Update a review
     */
    public function update(UpdateReviewRequest $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            
            $updated = $this->reviewService->updateReview(
                $id,
                $request->validated(),
                $user->id
            );
            
            if ($updated) {
                $review = $this->reviewService->getReviewById($id, $user->id, false);
                
                return response()->json([
                    'success' => true,
                    'data' => new ReviewResource($review),
                    'message' => 'Review updated successfully'
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to update review',
                'errors' => []
            ], 400);
            
        } catch (Exception $e) {
            $status = $e->getMessage() === 'Review not found' ? 404 : 
                     ($e->getMessage() === 'Approved reviews cannot be edited' ? 403 : 500);
            
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $status);
        }
    }

    /**
     * Delete a review
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $isAdmin = $user->role === 'admin';
            
            $deleted = $this->reviewService->deleteReview($id, $user->id, $isAdmin);
            
            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'Review deleted successfully'
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to delete review',
                'errors' => []
            ], 400);
            
        } catch (Exception $e) {
            $status = $e->getMessage() === 'Review not found' ? 404 : 403;
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $status);
        }
    }

    /**
     * Approve a review (Admin only)
     */
    public function approve(ApproveReviewRequest $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            
            $approved = $this->reviewService->approveReview($id, $user->id);
            
            if ($approved) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'Review approved successfully'
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to approve review',
                'errors' => []
            ], 400);
            
        } catch (Exception $e) {
            $status = $e->getMessage() === 'Review not found' ? 404 : 400;
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $status);
        }
    }
}