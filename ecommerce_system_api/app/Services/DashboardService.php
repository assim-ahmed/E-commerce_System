<?php
// app/Services/DashboardService.php

namespace App\Services;

use App\Contracts\Service\DashboardServiceInterface;
use App\Contracts\Repository\DashboardRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class DashboardService implements DashboardServiceInterface
{
    protected $dashboardRepository;
    
    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }
    
    /**
     * Get dashboard statistics with caching
     */
    public function getStats(): array
    {
        // Cache for 10 minutes
        return Cache::remember('dashboard_stats', 600, function () {
            return $this->dashboardRepository->getStats();
        });
    }
    
    /**
     * Get sales report
     */
    public function getSalesReport(string $period = 'monthly'): array
    {
        $cacheKey = 'sales_report_' . $period;
        
        return Cache::remember($cacheKey, 600, function () use ($period) {
            return $this->dashboardRepository->getSalesReport($period);
        });
    }
    
    /**
     * Get top products
     */
    public function getTopProducts(int $limit = 10): array
    {
        $cacheKey = 'top_products_' . $limit;
        
        return Cache::remember($cacheKey, 300, function () use ($limit) {
            return $this->dashboardRepository->getTopProducts($limit);
        });
    }
    
    /**
     * Get recent orders
     */
    public function getRecentOrders(int $limit = 10): array
    {
        // No cache for recent orders (real-time data)
        return $this->dashboardRepository->getRecentOrders($limit);
    }
    
    /**
     * Get inventory summary
     */
    public function getInventorySummary(): array
    {
        return Cache::remember('inventory_summary', 300, function () {
            return $this->dashboardRepository->getInventorySummary();
        });
    }
    
    /**
     * Clear dashboard cache
     */
    public function clearCache(): bool
    {
        Cache::forget('dashboard_stats');
        Cache::forget('inventory_summary');
        
        // Clear sales reports cache
        $periods = ['daily', 'weekly', 'monthly', 'yearly'];
        foreach ($periods as $period) {
            Cache::forget('sales_report_' . $period);
        }
        
        // Clear top products cache for different limits
        $limits = [5, 10, 20, 50];
        foreach ($limits as $limit) {
            Cache::forget('top_products_' . $limit);
        }
        
        return true;
    }
}