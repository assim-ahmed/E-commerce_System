<?php

namespace App\Repositories;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    protected $model;
    
    public function __construct(Order $order)
    {
        $this->model = $order;
    }
    
    public function getUserOrders(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['items', 'address'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function getAllOrders(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['items', 'user', 'address'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function findById(int $orderId)
    {
        return $this->model->find($orderId);
    }
    
    public function findByIdWithItems(int $orderId)
    {
        return $this->model->with(['items'])->find($orderId);
    }
    
    public function findByIdWithDetails(int $orderId)
    {
        return $this->model
            ->with(['items', 'user', 'address'])
            ->find($orderId);
    }
    
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    
    public function updateStatus(int $orderId, string $status): bool
    {
        return $this->model->where('id', $orderId)->update(['status' => $status]);
    }
    
    public function cancel(int $orderId): bool
    {
        return $this->model->where('id', $orderId)->update(['status' => Order::STATUS_CANCELLED]);
    }
    
    public function createOrderItems(int $orderId, array $orderItems): void
    {
        foreach ($orderItems as $item) {
            $item['order_id'] = $orderId;
            OrderItem::create($item);
        }
    }
    
    public function belongsToUser(int $orderId, int $userId): bool
    {
        return $this->model->where('id', $orderId)->where('user_id', $userId)->exists();
    }
    
    public function getStatus(int $orderId): ?string
    {
        $order = $this->model->find($orderId);
        return $order ? $order->status : null;
    }
    
    public function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(uniqid());
        } while ($this->model->where('order_number', $orderNumber)->exists());
        
        return $orderNumber;
    }
}