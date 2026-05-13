<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\NotificationServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\Notification\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class NotificationController extends Controller
{
    protected $notificationService;
    
    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    
    /**
     * Get user notifications
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $perPage = $request->get('per_page', 15);
            
            $notifications = $this->notificationService->getUserNotifications($user->id, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => NotificationResource::collection($notifications),
                    'unread_count' => $this->notificationService->getUnreadCount($user->id),
                    'meta' => [
                        'current_page' => $notifications->currentPage(),
                        'last_page' => $notifications->lastPage(),
                        'per_page' => $notifications->perPage(),
                        'total' => $notifications->total(),
                    ],
                ],
                'message' => 'Notifications retrieved successfully'
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve notifications',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
    
    /**
     * Get unread notifications only
     */
    public function unread(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $notifications = $this->notificationService->getUserUnreadNotifications($user->id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => NotificationResource::collection($notifications),
                    'count' => $notifications->count(),
                ],
                'message' => 'Unread notifications retrieved successfully'
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve unread notifications',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
    
    /**
     * Get single notification
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            
            $notification = $this->notificationService->getNotificationById($id, $user->id);
            
            return response()->json([
                'success' => true,
                'data' => new NotificationResource($notification),
                'message' => 'Notification retrieved successfully'
            ], 200);
            
        } catch (Exception $e) {
            $status = $e->getMessage() === 'Notification not found' ? 404 : 403;
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $status);
        }
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            
            $this->notificationService->markAsRead($id, $user->id);
            
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Notification marked as read'
            ], 200);
            
        } catch (Exception $e) {
            $status = $e->getMessage() === 'Notification not found' ? 404 : 
                      ($e->getMessage() === 'Notification is already read' ? 400 : 403);
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $status);
        }
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $count = $this->notificationService->markAllAsRead($user->id);
            
            return response()->json([
                'success' => true,
                'data' => ['marked_count' => $count],
                'message' => "{$count} notification(s) marked as read"
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to mark notifications as read',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
    
    /**
     * Delete notification
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            
            $this->notificationService->deleteNotification($id, $user->id);
            
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Notification deleted successfully'
            ], 200);
            
        } catch (Exception $e) {
            $status = $e->getMessage() === 'Notification not found' ? 404 : 403;
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $status);
        }
    }
    
    /**
     * Get unread count only
     */
    public function unreadCount(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $count = $this->notificationService->getUnreadCount($user->id);
            
            return response()->json([
                'success' => true,
                'data' => ['unread_count' => $count],
                'message' => 'Unread count retrieved successfully'
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to get unread count',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}