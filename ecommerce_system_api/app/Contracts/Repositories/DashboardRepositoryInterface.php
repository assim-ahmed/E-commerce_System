<?php
// app/Contracts/Repository/DashboardRepositoryInterface.php

namespace App\Contracts\Repository;

interface DashboardRepositoryInterface
{
    /**
     * Get dashboard statistics
     * @return array
     */
    public function getStats(): array;
    
    /**
     * Get sales reports
     * @param string $period (daily, weekly, monthly, yearly)
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
}