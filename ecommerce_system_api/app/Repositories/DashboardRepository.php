<?php
// app/Repositories/DashboardRepository.php

namespace App\Repositories;

use App\Contracts\Repository\DashboardRepositoryInterface;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardRepository implements DashboardRepositoryInterface
{
    /**
     * Get dashboard statistics
     */
    public function getStats(): array
    {
        try {
            // Total sales (completed orders)
            $totalSales = Order::where('status', 'completed')
                ->sum('total');
            
            // Total orders count
            $totalOrders = Order::count();
            
            // Total customers
            $totalCustomers = User::where('role', 'customer')->count();
            
            // Total products
            $totalProducts = Product::count();
            
            // Low stock products count
            $lowStockProducts = Product::where('is_low_stock', true)->count();
            
            // Pending reviews count
            $pendingReviews = Review::where('is_approved', false)->count();
            
            // Average order value
            $averageOrderValue = $totalOrders > 0 
                ? $totalSales / $totalOrders 
                : 0;
            
            // Today's sales
            $todaySales = Order::where('status', 'completed')
                ->whereDate('created_at', Carbon::today())
                ->sum('total');
            
            // This month sales
            $thisMonthSales = Order::where('status', 'completed')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total');
            
            // Last month sales
            $lastMonthSales = Order::where('status', 'completed')
                ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->sum('total');
            
            // Growth percentage
            $salesGrowth = $lastMonthSales > 0 
                ? (($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100 
                : 0;
            
            return [
                'total_sales' => round($totalSales, 2),
                'total_orders' => $totalOrders,
                'total_customers' => $totalCustomers,
                'total_products' => $totalProducts,
                'low_stock_products' => $lowStockProducts,
                'pending_reviews' => $pendingReviews,
                'average_order_value' => round($averageOrderValue, 2),
                'today_sales' => round($todaySales, 2),
                'this_month_sales' => round($thisMonthSales, 2),
                'sales_growth_percentage' => round($salesGrowth, 2),
            ];
        } catch (\Exception $e) {
            throw new \Exception('Error fetching dashboard stats: ' . $e->getMessage());
        }
    }
    
    /**
     * Get sales reports
     */
    public function getSalesReport(string $period = 'monthly'): array
    {
        try {
            $dates = $this->getDateRange($period);
            $labels = [];
            $data = [];
            
            foreach ($dates as $date) {
                $labels[] = $date['label'];
                
                $sales = Order::where('status', 'completed')
                    ->whereBetween('created_at', [$date['start'], $date['end']])
                    ->sum('total');
                
                $data[] = round($sales, 2);
            }
            
            // Get total orders count per period
            $orders = [];
            foreach ($dates as $date) {
                $orders[] = Order::where('status', 'completed')
                    ->whereBetween('created_at', [$date['start'], $date['end']])
                    ->count();
            }
            
            return [
                'period' => $period,
                'labels' => $labels,
                'sales_data' => $data,
                'orders_data' => $orders,
                'total_sales' => round(array_sum($data), 2),
                'total_orders' => array_sum($orders),
            ];
        } catch (\Exception $e) {
            throw new \Exception('Error fetching sales report: ' . $e->getMessage());
        }
    }
    
    /**
     * Get top selling products
     */
    public function getTopProducts(int $limit = 10): array
    {
        try {
            $topProducts = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->select(
                    'products.id',
                    'products.name',
                    'products.slug',
                    'products.base_price',
                    'products.images',
                    DB::raw('SUM(order_items.quantity) as total_quantity_sold'),
                    DB::raw('SUM(order_items.total) as total_revenue')
                )
                ->where('orders.status', 'completed')
                ->groupBy('products.id', 'products.name', 'products.slug', 'products.base_price', 'products.images')
                ->orderBy('total_quantity_sold', 'DESC')
                ->limit($limit)
                ->get();
            
            return $topProducts->toArray();
        } catch (\Exception $e) {
            throw new \Exception('Error fetching top products: ' . $e->getMessage());
        }
    }
    
    /**
     * Get recent orders
     */
    public function getRecentOrders(int $limit = 10): array
    {
        try {
            $recentOrders = Order::with(['user', 'address'])
                ->orderBy('created_at', 'DESC')
                ->limit($limit)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_name' => $order->user->name,
                        'customer_email' => $order->user->email,
                        'total' => round($order->total, 2),
                        'status' => $order->status,
                        'created_at' => $order->created_at->toISOString(),
                        'created_at_formatted' => $order->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            
            return $recentOrders->toArray();
        } catch (\Exception $e) {
            throw new \Exception('Error fetching recent orders: ' . $e->getMessage());
        }
    }
    
    /**
     * Get inventory summary
     */
    public function getInventorySummary(): array
    {
        try {
            // Total products
            $totalProducts = Product::count();
            
            // Total stock quantity
            $totalStock = Product::sum('stock_quantity');
            
            // Low stock products (detailed)
            $lowStockProducts = Product::where('is_low_stock', true)
                ->select('id', 'name', 'sku', 'stock_quantity', 'low_stock_threshold')
                ->orderBy('stock_quantity', 'ASC')
                ->limit(20)
                ->get()
                ->toArray();
            
            // Out of stock products
            $outOfStockProducts = Product::where('stock_quantity', '<=', 0)
                ->count();
            
            // Most stocked products
            $mostStocked = Product::orderBy('stock_quantity', 'DESC')
                ->select('id', 'name', 'sku', 'stock_quantity')
                ->limit(5)
                ->get()
                ->toArray();
            
            // Categories with product count
            $categoriesSummary = DB::table('categories')
                ->leftJoin('products', 'categories.id', '=', 'products.category_id')
                ->select(
                    'categories.id',
                    'categories.name',
                    DB::raw('COUNT(products.id) as products_count'),
                    DB::raw('SUM(products.stock_quantity) as total_stock')
                )
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('products_count', 'DESC')
                ->get()
                ->toArray();
            
            return [
                'total_products' => $totalProducts,
                'total_stock_quantity' => $totalStock,
                'out_of_stock_products' => $outOfStockProducts,
                'low_stock_count' => count($lowStockProducts),
                'low_stock_products' => $lowStockProducts,
                'most_stocked_products' => $mostStocked,
                'categories_summary' => $categoriesSummary,
            ];
        } catch (\Exception $e) {
            throw new \Exception('Error fetching inventory summary: ' . $e->getMessage());
        }
    }
    
    /**
     * Get date range for sales report
     */
    private function getDateRange(string $period): array
    {
        $dates = [];
        
        switch ($period) {
            case 'daily':
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $dates[] = [
                        'label' => $date->format('D, M j'),
                        'start' => $date->copy()->startOfDay(),
                        'end' => $date->copy()->endOfDay(),
                    ];
                }
                break;
                
            case 'weekly':
                for ($i = 5; $i >= 0; $i--) {
                    $date = Carbon::now()->subWeeks($i);
                    $dates[] = [
                        'label' => 'Week ' . $date->weekOfYear,
                        'start' => $date->copy()->startOfWeek(),
                        'end' => $date->copy()->endOfWeek(),
                    ];
                }
                break;
                
            case 'yearly':
                for ($i = 5; $i >= 0; $i--) {
                    $date = Carbon::now()->subYears($i);
                    $dates[] = [
                        'label' => $date->format('Y'),
                        'start' => $date->copy()->startOfYear(),
                        'end' => $date->copy()->endOfYear(),
                    ];
                }
                break;
                
            case 'monthly':
            default:
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $dates[] = [
                        'label' => $date->format('M Y'),
                        'start' => $date->copy()->startOfMonth(),
                        'end' => $date->copy()->endOfMonth(),
                    ];
                }
                break;
        }
        
        return $dates;
    }
}