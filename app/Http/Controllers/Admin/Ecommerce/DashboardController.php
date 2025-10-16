<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // Original data (keep your existing logic)
        $products          = DB::table('products')->count();
        $quantity          = DB::table('products')->sum('quantity');
        $orders            = DB::table('orders')->count();
        $pending_orders    = DB::table('orders')->where('status', 0)->count();
        $processing_orders = DB::table('orders')->where('status', 1)->count();
        $cancel_orders     = DB::table('orders')->where('status', 2)->count();
        $delivered_orders  = DB::table('orders')->where('status', 3)->count();
        $vendor_amount     = DB::table('vendor_accounts')->where('vendor_id', '!=', 1)->sum('amount');
        $admin_amount      = DB::table('vendor_accounts')->where('vendor_id', 1)->sum('amount');
        $pending_amount    = DB::table('vendor_accounts')->where('vendor_id', 1)->sum('pending_amount');
        $vendor_pamount    = DB::table('vendor_accounts')->where('vendor_id', '!=', 1)->sum('pending_amount');
        $vendors           = DB::table('users')->where('role_id', 2)->count();
        $customers         = DB::table('users')->where('role_id', 3)->count();
        $commission        = DB::table('commissions')->where('status', true)->sum('amount');
        
        // NEW: Add visualization data
        $growth_metrics = $this->calculateGrowthMetrics();
        $progress_data = $this->calculateProgressData($products, $orders, $quantity, $vendors);
        $chart_data = $this->getChartData();
        
        return view('admin.e-commerce.dashboard', compact(
            'products',
            'pending_amount',
            'vendor_pamount',
            'quantity',
            'orders',
            'pending_orders',
            'processing_orders',
            'cancel_orders',
            'delivered_orders',
            'vendor_amount',
            'admin_amount',
            'vendors',
            'customers',
            'commission',
            'growth_metrics',
            'progress_data',
            'chart_data'
        ));
    }
    
    /**
     * Calculate growth metrics compared to last month
     */
    private function calculateGrowthMetrics()
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        
        // Current month data
        $currentProducts = DB::table('products')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
            
        $currentOrders = DB::table('orders')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
            
        $currentVendors = DB::table('users')
            ->where('role_id', 2)
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();

        // Last month data
        $lastMonthProducts = DB::table('products')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();
            
        $lastMonthOrders = DB::table('orders')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();
            
        $lastMonthVendors = DB::table('users')
            ->where('role_id', 2)
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        // Calculate percentage changes
        $productsGrowth = $lastMonthProducts > 0 
            ? round((($currentProducts - $lastMonthProducts) / $lastMonthProducts) * 100, 1)
            : ($currentProducts > 0 ? 100 : 0);
            
        $ordersGrowth = $lastMonthOrders > 0 
            ? round((($currentOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1)
            : ($currentOrders > 0 ? 100 : 0);
            
        $vendorsGrowth = $lastMonthVendors > 0 
            ? round((($currentVendors - $lastMonthVendors) / $lastMonthVendors) * 100, 1)
            : ($currentVendors > 0 ? 100 : 0);

        return [
            'products' => $productsGrowth >= 0 ? "+{$productsGrowth}%" : "{$productsGrowth}%",
            'orders' => $ordersGrowth >= 0 ? "+{$ordersGrowth}%" : "{$ordersGrowth}%",
            'quantity' => 'Updated recently',
            'vendors' => $vendorsGrowth >= 0 ? "+{$vendorsGrowth}%" : "{$vendorsGrowth}%",
            'customers' => 'Growing steadily'
        ];
    }
    
    /**
     * Calculate progress data as percentage of platform goals
     */
    private function calculateProgressData($products, $orders, $quantity, $vendors)
    {
        // Define platform goals (adjust these based on your business targets)
        $goals = [
            'products' => 1000,     // Goal: 1000 total products
            'orders' => 500,        // Goal: 500 orders per month
            'quantity' => 10000,    // Goal: 10,000 total inventory
            'vendors' => 100,       // Goal: 100 vendors
            'customers' => 1000,    // Goal: 1000 customers
        ];

        $monthlyOrders = DB::table('orders')
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
            
        $totalCustomers = DB::table('users')->where('role_id', 3)->count();

        return [
            'products' => min(100, round(($products / $goals['products']) * 100)),
            'orders' => min(100, round(($monthlyOrders / $goals['orders']) * 100)),
            'quantity' => min(100, round(($quantity / $goals['quantity']) * 100)),
            'pending' => max(0, 100 - round((DB::table('orders')->where('status', 0)->count() / max($orders, 1)) * 100)),
            'processing' => round((DB::table('orders')->where('status', 1)->count() / max($orders, 1)) * 100),
            'delivered' => round((DB::table('orders')->where('status', 3)->count() / max($orders, 1)) * 100),
            'vendors' => min(100, round(($vendors / $goals['vendors']) * 100)),
            'customers' => min(100, round(($totalCustomers / $goals['customers']) * 100)),
        ];
    }
    
    /**
     * Get chart data for the last 7 days
     */
    private function getChartData()
    {
        $days = [];
        $orderCounts = [];
        $revenues = [];

        // Get data for last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('D'); // Mon, Tue, etc.

            // Orders for this day
            $dailyOrders = DB::table('orders')
                ->whereDate('created_at', $date)
                ->count();
                
            // Revenue calculation - simplified for compatibility
            $dailyRevenue = DB::table('vendor_accounts')
                ->whereDate('created_at', $date)
                ->sum('amount');
            
            // Handle null values
            $dailyRevenue = $dailyRevenue ?? 0;

            $orderCounts[] = $dailyOrders;
            $revenues[] = (int) $dailyRevenue;
        }

        return [
            'labels' => $days,
            'orders' => $orderCounts,
            'revenue' => $revenues,
        ];
    }
}