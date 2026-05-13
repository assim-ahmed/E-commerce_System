<?php

namespace App\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;

interface NotificationServiceInterface
{
    /**
     * Get user notifications
     */
    public function getUserNotifications(int $userId, int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Get unread notifications
     */
    public function getUserUnreadNotifications(int $userId);
    
    /**
     * Get single notification
     */
    public function getNotificationById(int $notificationId, int $userId);
    
    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, int $userId): bool;
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(int $userId): int;
    
    /**
     * Delete notification
     */
    public function deleteNotification(int $notificationId, int $userId): bool;
    
    /**
     * Get unread count
     */
    public function getUnreadCount(int $userId): int;
}