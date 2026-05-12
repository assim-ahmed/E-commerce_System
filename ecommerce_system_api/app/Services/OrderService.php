<?php

namespace App\Services;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\CartRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\InventoryLogRepositoryInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderService implements OrderServiceInterface
{
    protected $orderRepository;
    protected $cartRepository;
    protected $productRepository;
    protected $inventoryLogRepository;
    
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CartRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository,
        InventoryLogRepositoryInterface $inventoryLogRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->inventoryLogRepository = $inventoryLogRepository;
    }
    
    public function getUserOrders(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->getUserOrders($userId, $perPage);
    }
    
    public function getAllOrders(int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->getAllOrders($perPage);
    }
    
    public function getOrderById(int $orderId, int $userId, bool $isAdmin = false)
    {
        $order = $this->orderRepository->findByIdWithDetails($orderId);
        
        if (!$order) {
            throw new Exception('Order not found');
        }
        
        if (!$isAdmin && $order->user_id !== $userId) {
            throw new Exception('Unauthorized to view this order');
        }
        
        return $order;
    }
    
    public function createOrderFromCart(array $data, int $userId, ?string $cookieId = null)
    {
        // Get user's cart based on whether user is logged in or guest
        $cart = null;
        
        if ($userId) {
            // Logged in user
            $cart = $this->cartRepository->findOrCreateCartByUser($userId);
        } elseif ($cookieId) {
            // Guest user
            $cart = $this->cartRepository->findOrCreateCartByCookie($cookieId);
        } else {
            throw new Exception('No cart found. Please add items to cart first.');
        }
        
        // Get cart with items
        $cartWithItems = $this->cartRepository->getCartWithItems($cart->id);
        
        if (!$cartWithItems || $cartWithItems->items->isEmpty()) {
            throw new Exception('Cart is empty. Cannot create order.');
        }
        
        $cartItems = $cartWithItems->items;
        
        // Validate address belongs to user
        $address = \App\Models\Address::where('id', $data['address_id'])
            ->where('user_id', $userId)
            ->first();
            
        if (!$address) {
            throw new Exception('Invalid address selected');
        }
        
        DB::beginTransaction();
        
        try {
            $total = 0;
            $orderItemsData = [];
            
            // Process each cart item
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                $variant = $cartItem->variant;
                
                // Check stock availability
                $currentStock = $variant ? $variant->stock_quantity : $product->stock_quantity;
                
                if ($currentStock < $cartItem->quantity) {
                    throw new Exception("Insufficient stock for product: {$product->name}");
                }
                
                // Calculate item total
                $itemTotal = $cartItem->price_at_time * $cartItem->quantity;
                $total += $itemTotal;
                
                // Prepare order item snapshot
                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant ? $variant->id : null,
                    'product_name_snapshot' => $product->name . ($variant ? " ({$variant->name})" : ''),
                    'price_snapshot' => $cartItem->price_at_time,
                    'quantity' => $cartItem->quantity,
                    'total' => $itemTotal,
                ];
                
                // Update stock
                if ($variant) {
                    $newStock = $variant->stock_quantity - $cartItem->quantity;
                    $variant->update(['stock_quantity' => $newStock]);
                    
                    // Log inventory change for variant
                    $this->inventoryLogRepository->create([
                        'product_id' => $product->id,
                        'product_variant_id' => $variant->id,
                        'type' => 'sale',
                        'quantity_change' => -$cartItem->quantity,
                        'quantity_before' => $currentStock,
                        'quantity_after' => $newStock,
                    ]);
                } else {
                    $newStock = $product->stock_quantity - $cartItem->quantity;
                    $product->update(['stock_quantity' => $newStock]);
                    
                    // Log inventory change for product
                    $this->inventoryLogRepository->create([
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                        'type' => 'sale',
                        'quantity_change' => -$cartItem->quantity,
                        'quantity_before' => $currentStock,
                        'quantity_after' => $newStock,
                    ]);
                }
            }
            
            // Create order
            $orderData = [
                'order_number' => $this->orderRepository->generateOrderNumber(),
                'user_id' => $userId,
                'address_id' => $data['address_id'],
                'status' => Order::STATUS_PENDING,
                'total' => $total,
                'coupon_code' => null,
            ];
            
            $order = $this->orderRepository->create($orderData);
            
            // Create order items
            $this->orderRepository->createOrderItems($order->id, $orderItemsData);
            
            // Clear cart after successful order
            $this->cartRepository->clearCart($cart->id);
            
            DB::commit();
            
            // Return order with details
            return $this->orderRepository->findByIdWithDetails($order->id);
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function updateOrderStatus(int $orderId, string $status, int $adminId): bool
    {
        $order = $this->orderRepository->findById($orderId);
        
        if (!$order) {
            throw new Exception('Order not found');
        }
        
        $currentStatus = $order->status;
        
        if (!$this->isValidStatusTransition($currentStatus, $status)) {
            throw new Exception("Cannot change status from {$currentStatus} to {$status}");
        }
        
        DB::beginTransaction();
        
        try {
            $updated = $this->orderRepository->updateStatus($orderId, $status);
            DB::commit();
            return $updated;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Order status update failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function cancelOrder(int $orderId, int $userId, bool $isAdmin = false): bool
    {
        $order = $this->orderRepository->findById($orderId);
        
        if (!$order) {
            throw new Exception('Order not found');
        }
        
        if (!$isAdmin && $order->user_id !== $userId) {
            throw new Exception('Unauthorized to cancel this order');
        }
        
        if (!$this->canCancelOrder($order->status)) {
            throw new Exception("Order with status '{$order->status}' cannot be cancelled");
        }
        
        DB::beginTransaction();
        
        try {
            $cancelled = $this->orderRepository->cancel($orderId);
            DB::commit();
            return $cancelled;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function canCancelOrder(string $status): bool
    {
        return in_array($status, [
            Order::STATUS_PENDING,
            Order::STATUS_PROCESSING
        ]);
    }
    
    public function isValidStatusTransition(string $currentStatus, string $newStatus): bool
    {
        $validTransitions = [
            Order::STATUS_PENDING => [
                Order::STATUS_PROCESSING,
                Order::STATUS_CANCELLED,
            ],
            Order::STATUS_PROCESSING => [
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ],
            Order::STATUS_COMPLETED => [],
            Order::STATUS_CANCELLED => [],
            Order::STATUS_REFUNDED => [],
        ];
        
        return isset($validTransitions[$currentStatus]) && 
               in_array($newStatus, $validTransitions[$currentStatus]);
    }
}