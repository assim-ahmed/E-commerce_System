<?php

namespace App\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;

interface OrderServiceInterface
{
    /**
     * Get orders for authenticated user
     */
    public function getUserOrders(int $userId, int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Get all orders (Admin only)
     */
    public function getAllOrders(int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Get single order by ID
     */
    public function getOrderById(int $orderId, int $userId, bool $isAdmin = false);
    
    /**
     * Create a new order from cart
     */
    public function createOrderFromCart(array $data, int $userId, ?string $cookieId = null);
    
    /**
     * Update order status (Admin only)
     */
    public function updateOrderStatus(int $orderId, string $status, int $adminId): bool;
    
    /**
     * Cancel an order
     */
    public function cancelOrder(int $orderId, int $userId, bool $isAdmin = false): bool;
    
    /**
     * Check if order can be cancelled
     */
    public function canCancelOrder(string $status): bool;
    
    /**
     * Check if status transition is valid
     */
    public function isValidStatusTransition(string $currentStatus, string $newStatus): bool;
}