<?php
// app/Http/Controllers/Api/Admin/DashboardController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Service\DashboardServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    protected $dashboardService;
    
    public function __construct(DashboardServiceInterface $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }
    
    /**
     * Get dashboard statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = $this->dashboardService->getStats();
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Dashboard statistics retrieved successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve dashboard statistics',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get sales report
     */
    public function getSalesReport(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'period' => 'sometimes|in:daily,weekly,monthly,yearly'
            ]);
            
            $period = $request->get('period', 'monthly');
            $report = $this->dashboardService->getSalesReport($period);
            
            return response()->json([
                'success' => true,
                'data' => $report,
                'message' => 'Sales report retrieved successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve sales report',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get top selling products
     */
    public function getTopProducts(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'limit' => 'sometimes|integer|min:1|max:100'
            ]);
            
            $limit = $request->get('limit', 10);
            $topProducts = $this->dashboardService->getTopProducts($limit);
            
            return response()->json([
                'success' => true,
                'data' => $topProducts,
                'message' => 'Top products retrieved successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve top products',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get recent orders
     */
    public function getRecentOrders(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'limit' => 'sometimes|integer|min:1|max:50'
            ]);
            
            $limit = $request->get('limit', 10);
            $recentOrders = $this->dashboardService->getRecentOrders($limit);
            
            return response()->json([
                'success' => true,
                'data' => $recentOrders,
                'message' => 'Recent orders retrieved successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve recent orders',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get inventory summary
     */
    public function getInventorySummary(): JsonResponse
    {
        try {
            $inventory = $this->dashboardService->getInventorySummary();
            
            return response()->json([
                'success' => true,
                'data' => $inventory,
                'message' => 'Inventory summary retrieved successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve inventory summary',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Clear dashboard cache (admin only)
     */
    public function clearCache(): JsonResponse
    {
        try {
            $this->dashboardService->clearCache();
            
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Dashboard cache cleared successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to clear dashboard cache',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}