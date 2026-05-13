<?php

namespace App\Repositories;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationRepository implements NotificationRepositoryInterface
{
    protected $model;
    
    public function __construct(Notification $notification)
    {
        $this->model = $notification;
    }
    
    public function getUserNotifications(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function getUserUnreadNotifications(int $userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    public function findById(int $notificationId)
    {
        return $this->model->find($notificationId);
    }
    
    public function findByIdAndUser(int $notificationId, int $userId)
    {
        return $this->model
            ->where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();
    }
    
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    
    public function markAsRead(int $notificationId): bool
    {
        $notification = $this->model->find($notificationId);
        if (!$notification) {
            return false;
        }
        $notification->markAsRead();
        return true;
    }
    
    public function markAllAsRead(int $userId): int
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }
    
    public function delete(int $notificationId): bool
    {
        $notification = $this->model->find($notificationId);
        if (!$notification) {
            return false;
        }
        return $notification->delete();
    }
    
    public function getUnreadCount(int $userId): int
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
    
    public function notifyOrderStatusChange(int $userId, int $orderId, string $oldStatus, string $newStatus): void
    {
        $statusMessages = [
            'processing' => 'Your order #%d is now being processed.',
            'shipped' => 'Great news! Your order #%d has been shipped.',
            'delivered' => 'Your order #%d has been delivered. Enjoy!',
            'cancelled' => 'Your order #%d has been cancelled.',
        ];
        
        $message = sprintf($statusMessages[$newStatus] ?? "Your order #%d status changed to {$newStatus}", $orderId);
        
        $this->create([
            'user_id' => $userId,
            'title' => 'Order Status Update',
            'message' => $message,
            'type' => 'info',
            'is_read' => false,
        ]);
    }
    
    public function notifyLowStock(int $productId, string $productName, int $currentStock): void
    {
        // Notify admin users
        $admins = \App\Models\User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            $this->create([
                'user_id' => $admin->id,
                'title' => 'Low Stock Alert',
                'message' => "Product '{$productName}' has only {$currentStock} units left in stock.",
                'type' => 'warning',
                'is_read' => false,
            ]);
        }
    }
}