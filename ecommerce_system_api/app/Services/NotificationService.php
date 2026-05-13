<?php

namespace App\Services;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Services\NotificationServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;

class NotificationService implements NotificationServiceInterface
{
    protected $notificationRepository;
    
    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }
    
    public function getUserNotifications(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->notificationRepository->getUserNotifications($userId, $perPage);
    }
    
    public function getUserUnreadNotifications(int $userId)
    {
        return $this->notificationRepository->getUserUnreadNotifications($userId);
    }
    
    public function getNotificationById(int $notificationId, int $userId)
    {
        $notification = $this->notificationRepository->findByIdAndUser($notificationId, $userId);
        
        if (!$notification) {
            throw new Exception('Notification not found');
        }
        
        return $notification;
    }
    
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = $this->getNotificationById($notificationId, $userId);
        
        if ($notification->is_read) {
            throw new Exception('Notification is already read');
        }
        
        return $this->notificationRepository->markAsRead($notificationId);
    }
    
    public function markAllAsRead(int $userId): int
    {
        return $this->notificationRepository->markAllAsRead($userId);
    }
    
    public function deleteNotification(int $notificationId, int $userId): bool
    {
        $this->getNotificationById($notificationId, $userId);
        return $this->notificationRepository->delete($notificationId);
    }
    
    public function getUnreadCount(int $userId): int
    {
        return $this->notificationRepository->getUnreadCount($userId);
    }
}