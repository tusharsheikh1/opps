<?php

namespace App\Http\Controllers\Admin\Ecommerce\OrderEssential;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DB;

class OrderAnalyticsController
{
    /**
     * Get analytics data for dashboard
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAnalytics(Request $request)
    {
        try {
            $period = $request->get('period', 'this_month');
            
            // Get date range based on period
            $dateRange = $this->getDateRange($period);
            $previousDateRange = $this->getPreviousDateRange($period);
            
            // Current period data
            $currentData = $this->getOrderAnalytics($dateRange['start'], $dateRange['end']);
            
            // Previous period data for comparison
            $previousData = $this->getOrderAnalytics($previousDateRange['start'], $previousDateRange['end']);
            
            // Calculate trends
            $trends = $this->calculateTrends($currentData, $previousData);
            
            // Get chart data
            $chartData = $this->getChartData($period, $dateRange['start'], $dateRange['end']);
            
            return response()->json([
                'success' => true,
                'current' => $currentData,
                'previous' => $previousData,
                'trends' => $trends,
                'charts' => $chartData,
                'period' => $period
            ]);
            
        } catch (\Exception $e) {
            Log::error('Analytics fetch failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch analytics data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get date range based on period
     *
     * @param string $period
     * @return array
     */
    private function getDateRange($period)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'today':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
                
            case 'yesterday':
                return [
                    'start' => $now->copy()->subDay()->startOfDay(),
                    'end' => $now->copy()->subDay()->endOfDay()
                ];
                
            case 'this_week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
                
            case 'last_week':
                return [
                    'start' => $now->copy()->subWeek()->startOfWeek(),
                    'end' => $now->copy()->subWeek()->endOfWeek()
                ];
                
            case 'this_month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
                
            case 'last_month':
                return [
                    'start' => $now->copy()->subMonth()->startOfMonth(),
                    'end' => $now->copy()->subMonth()->endOfMonth()
                ];
                
            case 'this_year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear()
                ];
                
            case 'all_time':
                return [
                    'start' => Order::min('created_at') ?? $now->copy()->subYear(),
                    'end' => $now->copy()->endOfDay()
                ];
                
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
    }
    
    /**
     * Get previous date range for comparison
     *
     * @param string $period
     * @return array
     */
    private function getPreviousDateRange($period)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'today':
                return [
                    'start' => $now->copy()->subDay()->startOfDay(),
                    'end' => $now->copy()->subDay()->endOfDay()
                ];
                
            case 'yesterday':
                return [
                    'start' => $now->copy()->subDays(2)->startOfDay(),
                    'end' => $now->copy()->subDays(2)->endOfDay()
                ];
                
            case 'this_week':
                return [
                    'start' => $now->copy()->subWeek()->startOfWeek(),
                    'end' => $now->copy()->subWeek()->endOfWeek()
                ];
                
            case 'last_week':
                return [
                    'start' => $now->copy()->subWeeks(2)->startOfWeek(),
                    'end' => $now->copy()->subWeeks(2)->endOfWeek()
                ];
                
            case 'this_month':
                return [
                    'start' => $now->copy()->subMonth()->startOfMonth(),
                    'end' => $now->copy()->subMonth()->endOfMonth()
                ];
                
            case 'last_month':
                return [
                    'start' => $now->copy()->subMonths(2)->startOfMonth(),
                    'end' => $now->copy()->subMonths(2)->endOfMonth()
                ];
                
            case 'this_year':
                return [
                    'start' => $now->copy()->subYear()->startOfYear(),
                    'end' => $now->copy()->subYear()->endOfYear()
                ];
                
            case 'all_time':
                return [
                    'start' => Order::min('created_at') ?? $now->copy()->subYears(2),
                    'end' => $now->copy()->subYear()->endOfDay()
                ];
                
            default:
                return [
                    'start' => $now->copy()->subMonths(2)->startOfMonth(),
                    'end' => $now->copy()->subMonths(2)->endOfMonth()
                ];
        }
    }
    
    /**
     * Get order analytics data for date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getOrderAnalytics($startDate, $endDate)
    {
        $query = Order::whereBetween('created_at', [$startDate, $endDate]);
        
        // Total orders and sales
        $totalOrders = $query->count();
        $totalSales = $query->sum('total');
        
        // Order counts by status
        $pending = $query->where('status', 0)->count();
        $processing = $query->where('status', 1)->count();
        $cancelled = $query->where('status', 2)->count();
        $delivered = $query->where('status', 3)->count();
        $shipping = $query->where('status', 4)->count();
        $refund = $query->where('status', 5)->count();
        $returnRequested = $query->where('status', 6)->count();
        $returnAccepted = $query->where('status', 7)->count();
        $returned = $query->where('status', 8)->count();
        
        // Sales by status
        $deliveredSales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 3)->sum('total');
        
        // Average order value
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        
        // Payment methods breakdown
        $paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total) as total'))
            ->groupBy('payment_method')
            ->get();
        
        // Shipping charges
        $totalShippingCharges = $query->sum('shipping_charge');
        $totalDiscount = $query->sum('discount');
        
        return [
            'total_orders' => $totalOrders,
            'total_sales' => round($totalSales, 2),
            'delivered_sales' => round($deliveredSales, 2),
            'avg_order_value' => round($avgOrderValue, 2),
            'pending' => $pending,
            'processing' => $processing,
            'cancelled' => $cancelled,
            'delivered' => $delivered,
            'shipping' => $shipping,
            'refund' => $refund,
            'return_requested' => $returnRequested,
            'return_accepted' => $returnAccepted,
            'returned' => $returned,
            'shipping_charges' => round($totalShippingCharges, 2),
            'total_discount' => round($totalDiscount, 2),
            'payment_methods' => $paymentMethods,
            'completion_rate' => $totalOrders > 0 ? round(($delivered / $totalOrders) * 100, 1) : 0,
            'cancellation_rate' => $totalOrders > 0 ? round(($cancelled / $totalOrders) * 100, 1) : 0,
            'processing_efficiency' => $totalOrders > 0 ? round((($totalOrders - $pending) / $totalOrders) * 100, 1) : 0,
            'return_rate' => $totalOrders > 0 ? round((($refund + $returned) / $totalOrders) * 100, 1) : 0
        ];
    }
    
    /**
     * Calculate trends between current and previous periods
     *
     * @param array $current
     * @param array $previous
     * @return array
     */
    private function calculateTrends($current, $previous)
    {
        $trends = [];
        
        $fields = ['total_sales', 'total_orders', 'avg_order_value', 'delivered', 'cancelled'];
        
        foreach ($fields as $field) {
            $currentValue = $current[$field] ?? 0;
            $previousValue = $previous[$field] ?? 0;
            
            if ($previousValue > 0) {
                $change = (($currentValue - $previousValue) / $previousValue) * 100;
            } else {
                $change = $currentValue > 0 ? 100 : 0;
            }
            
            $trends[$field] = [
                'current' => $currentValue,
                'previous' => $previousValue,
                'change' => round($change, 1),
                'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'same')
            ];
        }
        
        return $trends;
    }
    
    /**
     * Get chart data for the specified period
     *
     * @param string $period
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getChartData($period, $startDate, $endDate)
    {
        // Sales trend data
        $salesTrend = $this->getSalesTrendData($period, $startDate, $endDate);
        
        // Status distribution
        $statusData = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                $statusNames = [
                    0 => 'Pending',
                    1 => 'Processing',
                    2 => 'Cancelled',
                    3 => 'Delivered',
                    4 => 'Shipping',
                    5 => 'Refund',
                    6 => 'Return Requested',
                    7 => 'Return Accepted',
                    8 => 'Returned'
                ];
                
                return [$statusNames[$item->status] ?? 'Unknown' => $item->count];
            });
        
        return [
            'sales_trend' => $salesTrend,
            'status_distribution' => $statusData
        ];
    }
    
    /**
     * Get sales trend data based on period
     *
     * @param string $period
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getSalesTrendData($period, $startDate, $endDate)
    {
        $labels = [];
        $salesData = [];
        $ordersData = [];
        
        if (in_array($period, ['today', 'yesterday'])) {
            // Hourly data for single days
            for ($hour = 0; $hour < 24; $hour++) {
                $hourStart = $startDate->copy()->addHours($hour);
                $hourEnd = $hourStart->copy()->addHour();
                
                $labels[] = $hourStart->format('H:00');
                $salesData[] = Order::whereBetween('created_at', [$hourStart, $hourEnd])->sum('total');
                $ordersData[] = Order::whereBetween('created_at', [$hourStart, $hourEnd])->count();
            }
        } elseif (in_array($period, ['this_week', 'last_week'])) {
            // Daily data for weeks
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dayEnd = $currentDate->copy()->endOfDay();
                
                $labels[] = $currentDate->format('M d');
                $salesData[] = Order::whereBetween('created_at', [$currentDate, $dayEnd])->sum('total');
                $ordersData[] = Order::whereBetween('created_at', [$currentDate, $dayEnd])->count();
                
                $currentDate->addDay();
            }
        } elseif (in_array($period, ['this_month', 'last_month'])) {
            // Weekly data for months
            $currentDate = $startDate->copy()->startOfWeek();
            $weekNumber = 1;
            
            while ($currentDate <= $endDate) {
                $weekEnd = $currentDate->copy()->endOfWeek();
                if ($weekEnd > $endDate) {
                    $weekEnd = $endDate->copy();
                }
                
                $labels[] = "Week {$weekNumber}";
                $salesData[] = Order::whereBetween('created_at', [$currentDate, $weekEnd])->sum('total');
                $ordersData[] = Order::whereBetween('created_at', [$currentDate, $weekEnd])->count();
                
                $currentDate->addWeek();
                $weekNumber++;
            }
        } else {
            // Monthly data for year/all-time
            $currentDate = $startDate->copy()->startOfMonth();
            
            while ($currentDate <= $endDate) {
                $monthEnd = $currentDate->copy()->endOfMonth();
                if ($monthEnd > $endDate) {
                    $monthEnd = $endDate->copy();
                }
                
                $labels[] = $currentDate->format('M Y');
                $salesData[] = Order::whereBetween('created_at', [$currentDate, $monthEnd])->sum('total');
                $ordersData[] = Order::whereBetween('created_at', [$currentDate, $monthEnd])->count();
                
                $currentDate->addMonth();
            }
        }
        
        return [
            'labels' => $labels,
            'sales' => $salesData,
            'orders' => $ordersData
        ];
    }

    /**
     * Get revenue analytics
     */
    public function getRevenueAnalytics(Request $request)
    {
        try {
            $period = $request->get('period', 'this_month');
            $dateRange = $this->getDateRange($period);
            
            $analytics = [
                'gross_revenue' => Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->sum('total'),
                'net_revenue' => Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->where('status', 3)
                    ->sum('total'),
                'total_discounts' => Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->sum('discount'),
                'shipping_revenue' => Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->sum('shipping_charge'),
                'refund_amount' => Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->where('status', 5)
                    ->sum('refund_amount')
            ];
            
            return response()->json([
                'success' => true,
                'analytics' => $analytics,
                'period' => $period
            ]);
            
        } catch (\Exception $e) {
            Log::error('Revenue analytics fetch failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch revenue analytics'
            ], 500);
        }
    }

    /**
     * Get customer analytics
     */
    public function getCustomerAnalytics(Request $request)
    {
        try {
            $period = $request->get('period', 'this_month');
            $dateRange = $this->getDateRange($period);
            
            $analytics = [
                'new_customers' => Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->distinct('email')
                    ->count('email'),
                'returning_customers' => Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->whereExists(function($query) use ($dateRange) {
                        $query->select(DB::raw(1))
                              ->from('orders as o2')
                              ->whereColumn('o2.email', 'orders.email')
                              ->where('o2.created_at', '<', $dateRange['start']);
                    })
                    ->distinct('email')
                    ->count('email'),
                'top_customers' => Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->select('email', 'first_name', 'last_name', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total) as total_spent'))
                    ->groupBy('email', 'first_name', 'last_name')
                    ->orderBy('total_spent', 'desc')
                    ->limit(10)
                    ->get()
            ];
            
            return response()->json([
                'success' => true,
                'analytics' => $analytics,
                'period' => $period
            ]);
            
        } catch (\Exception $e) {
            Log::error('Customer analytics fetch failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customer analytics'
            ], 500);
        }
    }
}