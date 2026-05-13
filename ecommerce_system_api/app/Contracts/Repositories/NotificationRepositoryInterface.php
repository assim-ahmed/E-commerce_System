<?php

namespace App\Contracts\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;

interface NotificationRepositoryInterface
{
    /**
     * Get notifications for a user with pagination
     */
    public function getUserNotifications(int $userId, int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Get unread notifications for a user
     */
    public function getUserUnreadNotifications(int $userId);
    
    /**
     * Get a single notification by ID
     */
    public function findById(int $notificationId);
    
    /**
     * Get a single notification by ID and verify ownership
     */
    public function findByIdAndUser(int $notificationId, int $userId);
    
    /**
     * Create a new notification
     */
    public function create(array $data);
    
    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): bool;
    
    /**
     * Mark all user notifications as read
     */
    public function markAllAsRead(int $userId): int;
    
    /**
     * Delete a notification
     */
    public function delete(int $notificationId): bool;
    
    /**
     * Get unread count for a user
     */
    public function getUnreadCount(int $userId): int;
    
    /**
     * Create notification for order status change
     */
    public function notifyOrderStatusChange(int $userId, int $orderId, string $oldStatus, string $newStatus);
    
    /**
     * Create notification for low stock
     */
    public function notifyLowStock(int $productId, string $productName, int $currentStock);
}