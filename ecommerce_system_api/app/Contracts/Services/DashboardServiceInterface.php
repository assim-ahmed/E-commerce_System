<?php
// app/Contracts/Service/DashboardServiceInterface.php

namespace App\Contracts\Service;

interface DashboardServiceInterface
{
    /**
     * Get dashboard statistics with caching
     * @return array
     */
    public function getStats(): array;
    
    /**
     * Get sales report
     * @param string $period
     * @return array
     */
    public function getSalesReport(string $period = 'monthly'): array;
    
    /**
     * Get top selling products
     * @param int $limit
     * @return array
     */
    public function getTopProducts(int $limit = 10): array;
    
    /**
     * Get recent orders
     * @param int $limit
     * @return array
     */
    public function getRecentOrders(int $limit = 10): array;
    
    /**
     * Get inventory summary
     * @return array
     */
    public function getInventorySummary(): array;
    
    /**
     * Clear dashboard cache
     * @return bool
     */
    public function clearCache(): bool;
}