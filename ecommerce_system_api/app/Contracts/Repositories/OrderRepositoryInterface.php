<?php

namespace App\Contracts\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    /**
     * Get orders for a specific user with pagination
     */
    public function getUserOrders(int $userId, int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Get all orders with pagination (Admin only)
     */
    public function getAllOrders(int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Get a single order by ID
     */
    public function findById(int $orderId);
    
    /**
     * Get a single order by ID with its items
     */
    public function findByIdWithItems(int $orderId);
    
    /**
     * Get a single order by ID with items, user, and address
     */
    public function findByIdWithDetails(int $orderId);
    
    /**
     * Create a new order
     */
    public function create(array $data);
    
    /**
     * Update order status
     */
    public function updateStatus(int $orderId, string $status): bool;
    
    /**
     * Cancel an order
     */
    public function cancel(int $orderId): bool;
    
    /**
     * Create order items from cart items
     */
    public function createOrderItems(int $orderId, array $orderItems): void;
    
    /**
     * Check if an order belongs to a user
     */
    public function belongsToUser(int $orderId, int $userId): bool;
    
    /**
     * Get order status
     */
    public function getStatus(int $orderId): ?string;
    
    /**
     * Generate unique order number
     */
    public function generateOrderNumber(): string;
}