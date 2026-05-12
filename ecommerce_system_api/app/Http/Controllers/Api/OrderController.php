<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\OrderServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Resources\Order\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Get authenticated user's orders
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $perPage = $request->get('per_page', 15);
            
            $orders = $this->orderService->getUserOrders($user->id, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => OrderResource::collection($orders),
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ],
                'message' => 'Orders retrieved successfully'
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve orders',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get single order by ID
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $isAdmin = $user->role === 'admin';
            
            $order = $this->orderService->getOrderById($id, $user->id, $isAdmin);
            
            return response()->json([
                'success' => true,
                'data' => new OrderResource($order),
                'message' => 'Order retrieved successfully'
            ], 200);
            
        } catch (Exception $e) {
            $status = $e->getMessage() === 'Order not found' ? 404 : 403;
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $status);
        }
    }

    /**
     * Create a new order from cart
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $cookieId = $request->cookie('cart_cookie_id');
            
            $order = $this->orderService->createOrderFromCart(
                $request->validated(),
                $user->id,
                $cookieId
            );
            
            return response()->json([
                'success' => true,
                'data' => new OrderResource($order),
                'message' => 'Order created successfully'
            ], 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            $status = str_contains($e->getMessage(), 'stock') ? 400 : 500;
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $status);
        }
    }

    /**
     * Update order status (Admin only)
     */
    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            
            $updated = $this->orderService->updateOrderStatus(
                $id,
                $request->status,
                $user->id
            );
            
            if ($updated) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'Order status updated successfully'
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to update order status',
                'errors' => []
            ], 400);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], 400);
        }
    }

    /**
     * Cancel an order
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $isAdmin = $user->role === 'admin';
            
            $cancelled = $this->orderService->cancelOrder($id, $user->id, $isAdmin);
            
            if ($cancelled) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'Order cancelled successfully'
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to cancel order',
                'errors' => []
            ], 400);
            
        } catch (Exception $e) {
            $status = $e->getMessage() === 'Order not found' ? 404 : 400;
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $status);
        }
    }

    /**
     * Get all orders (Admin only)
     */
    public function adminIndex(Request $request): JsonResponse
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
            $orders = $this->orderService->getAllOrders($perPage);
            
            return response()->json([
                'success' => true,
                'data' => OrderResource::collection($orders),
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ],
                'message' => 'All orders retrieved successfully'
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve orders',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}