<?php

namespace App\Http\Controllers\Admin\Ecommerce\OrderEssential;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OrderSearchController
{
    /**
     * Show customer order list with server-side filtering
     *
     * @return void
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        
        // Start building the query
        $query = Order::query()->latest('id');
        
        // Check if we have any filtering parameters
        $hasFilters = $request->hasAny(['search', 'status_filter', 'date_from', 'date_to']);
        
        // Apply search filters if they exist
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%")
                  ->orWhere('payment_method', 'LIKE', "%{$search}%")
                  ->orWhere('total', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($request->filled('status_filter') && $request->status_filter !== '') {
            $statusMap = $this->getStatusMap();
            
            if (isset($statusMap[$request->status_filter])) {
                $query->where('status', $statusMap[$request->status_filter]);
            }
        }
        
        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Get paginated results
        $orders = $query->paginate($perPage);
        
        // Append query parameters to pagination links
        $orders->appends($request->all());
        
        // If this is an AJAX request, return JSON response
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.e-commerce.order.partials.order-table', compact('orders'))->render(),
                'pagination' => $orders->links()->render(),
                'total' => $orders->total(),
                'showing' => [
                    'from' => $orders->firstItem(),
                    'to' => $orders->lastItem(),
                    'total' => $orders->total()
                ]
            ]);
        }
        
        // For regular page loads, if we have filters or pagination, we should treat it as filtered
        // This ensures proper behavior when refreshing filtered pages
        if ($hasFilters || $request->has('page')) {
            // Pass a flag to the view to indicate this is a filtered/paginated view
            return view('admin.e-commerce.order.index', compact('orders'))->with('isFiltered', true);
        }
        
        return view('admin.e-commerce.order.index', compact('orders'));
    }

    /**
     * Ajax method for live search
     */
    public function searchOrders(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $statusFilter = $request->get('status_filter', '');
            $dateFrom = $request->get('date_from', '');
            $dateTo = $request->get('date_to', '');
            $perPage = $request->get('per_page', 10);
            
            $query = Order::query()->latest('id');
            
            // Apply search
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('invoice', 'LIKE', "%{$search}%")
                      ->orWhere('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('address', 'LIKE', "%{$search}%")
                      ->orWhere('payment_method', 'LIKE', "%{$search}%")
                      ->orWhere('total', 'LIKE', "%{$search}%");
                });
            }
            
            // Apply status filter
            if (!empty($statusFilter)) {
                $statusMap = $this->getStatusMap();
                
                if (isset($statusMap[$statusFilter])) {
                    $query->where('status', $statusMap[$statusFilter]);
                }
            }
            
            // Apply date filters
            if (!empty($dateFrom)) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            
            if (!empty($dateTo)) {
                $query->whereDate('created_at', '<=', $dateTo);
            }
            
            $orders = $query->paginate($perPage);
            $orders->appends($request->all());
            
            return response()->json([
                'success' => true,
                'html' => view('admin.e-commerce.order.partials.order-table', compact('orders'))->render(),
                'pagination' => $orders->links()->render(),
                'total' => $orders->total(),
                'showing' => [
                    'from' => $orders->firstItem(),
                    'to' => $orders->lastItem(),
                    'total' => $orders->total()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Order search failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Search failed. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order history by phone number
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getPhoneHistory(Request $request)
    {
        $phone = $request->input('phone');
        
        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number is required'
            ], 400);
        }

        try {
            // Clean phone number (remove spaces, dashes, etc.)
            $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
            
            // Search for orders with exact match and similar patterns
            $orders = Order::where(function($query) use ($phone, $cleanPhone) {
                $query->where('phone', $phone)
                      ->orWhere('phone', $cleanPhone);
                
                // Also search for variations (with/without country code)
                if (strlen($cleanPhone) >= 10) {
                    // Try with +88 prefix if not present
                    if (!str_starts_with($cleanPhone, '+88')) {
                        $query->orWhere('phone', '+88' . ltrim($cleanPhone, '0'));
                    }
                    
                    // Try without +88 prefix if present
                    if (str_starts_with($cleanPhone, '+88')) {
                        $query->orWhere('phone', '0' . substr($cleanPhone, 3));
                        $query->orWhere('phone', substr($cleanPhone, 3));
                    }
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'orders' => [],
                    'message' => 'No orders found for this phone number',
                    'summary' => $this->getEmptyOrderSummary()
                ]);
            }

            // Calculate summary statistics
            $summary = $this->calculateOrderSummary($orders);

            // Format orders for response
            $formattedOrders = $this->formatOrdersForResponse($orders);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'orders' => $formattedOrders,
                    'summary' => $summary,
                    'phone' => $phone
                ]);
            }

            return view('admin.e-commerce.order.phone-history', compact('orders', 'summary', 'phone'));

        } catch (\Exception $e) {
            Log::error('Phone history fetch failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order history'
            ], 500);
        }
    }

    /**
     * Advanced search with multiple criteria
     */
    public function advancedSearch(Request $request)
    {
        try {
            $query = Order::query();
            
            // Basic search criteria
            if ($request->filled('invoice')) {
                $query->where('invoice', 'LIKE', "%{$request->invoice}%");
            }
            
            if ($request->filled('customer_name')) {
                $query->where(function($q) use ($request) {
                    $q->where('first_name', 'LIKE', "%{$request->customer_name}%")
                      ->orWhere('last_name', 'LIKE', "%{$request->customer_name}%");
                });
            }
            
            if ($request->filled('phone')) {
                $query->where('phone', 'LIKE', "%{$request->phone}%");
            }
            
            if ($request->filled('email')) {
                $query->where('email', 'LIKE', "%{$request->email}%");
            }
            
            // Status filters
            if ($request->filled('status') && is_array($request->status)) {
                $query->whereIn('status', $request->status);
            }
            
            // Payment method filter
            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }
            
            // Amount range filters
            if ($request->filled('min_amount')) {
                $query->where('total', '>=', $request->min_amount);
            }
            
            if ($request->filled('max_amount')) {
                $query->where('total', '<=', $request->max_amount);
            }
            
            // Date range filters
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            // Location filters
            if ($request->filled('district')) {
                $query->where('district', 'LIKE', "%{$request->district}%");
            }
            
            if ($request->filled('town')) {
                $query->where('town', 'LIKE', "%{$request->town}%");
            }
            
            // Sort options
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            $perPage = $request->get('per_page', 10);
            $orders = $query->paginate($perPage);
            $orders->appends($request->all());
            
            return response()->json([
                'success' => true,
                'orders' => $this->formatOrdersForResponse($orders),
                'pagination' => $orders->links()->render(),
                'total' => $orders->total(),
                'showing' => [
                    'from' => $orders->firstItem(),
                    'to' => $orders->lastItem(),
                    'total' => $orders->total()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Advanced search failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Advanced search failed. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status mapping
     */
    private function getStatusMap()
    {
        return [
            'Pending' => 0,
            'Processing' => 1,
            'order confirm' => 1,
            'Canceled' => 2,
            'Delivered' => 3,
            'Shipping' => 4,
            'refund' => 5,
            'Return Requested' => 6,
            'Return Accepted' => 7,
            'Returned' => 8
        ];
    }

    /**
     * Calculate order summary statistics
     */
    private function calculateOrderSummary($orders)
    {
        $totalOrders = $orders->count();
        $totalSpent = $orders->sum('total');
        $completedOrders = $orders->where('status', 3)->count();
        $cancelledOrders = $orders->where('status', 2)->count();
        $pendingOrders = $orders->whereIn('status', [0, 1, 4])->count();
        $customerSince = $orders->min('created_at');
        $lastOrder = $orders->max('created_at');
        $avgOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;

        // Get unique customer names from orders
        $customerNames = $orders->pluck('first_name')->filter()->unique()->values();
        $primaryName = $customerNames->first();

        return [
            'total_orders' => $totalOrders,
            'total_spent' => round($totalSpent, 2),
            'completed_orders' => $completedOrders,
            'cancelled_orders' => $cancelledOrders,
            'pending_orders' => $pendingOrders,
            'customer_since' => $customerSince ? Carbon::parse($customerSince)->format('d M Y') : null,
            'last_order' => $lastOrder ? Carbon::parse($lastOrder)->format('d M Y') : null,
            'avg_order_value' => round($avgOrderValue, 2),
            'customer_names' => $customerNames,
            'primary_name' => $primaryName
        ];
    }

    /**
     * Get empty order summary
     */
    private function getEmptyOrderSummary()
    {
        return [
            'total_orders' => 0,
            'total_spent' => 0,
            'completed_orders' => 0,
            'cancelled_orders' => 0,
            'pending_orders' => 0,
            'customer_since' => null,
            'last_order' => null,
            'avg_order_value' => 0
        ];
    }

    /**
     * Format orders for API response
     */
    private function formatOrdersForResponse($orders)
    {
        return $orders->map(function($order) {
            return [
                'id' => $order->id,
                'invoice' => $order->invoice,
                'customer_name' => $order->first_name . ' ' . $order->last_name,
                'email' => $order->email,
                'phone' => $order->phone,
                'total' => $order->total,
                'payment_method' => $order->payment_method,
                'status' => $order->status,
                'status_text' => $this->getStatusText($order->status),
                'created_at' => $order->created_at->format('d M Y H:i'),
                'address' => $order->address . ', ' . $order->town . ', ' . $order->district,
                'items_count' => $order->orderDetails ? $order->orderDetails->count() : 0
            ];
        });
    }

    /**
     * Get status text from status code
     */
    private function getStatusText($status)
    {
        $statusMap = [
            0 => 'Pending',
            1 => 'Processing',
            2 => 'Cancelled',
            3 => 'Delivered',
            4 => 'Shipping',
            5 => 'Refund',
            6 => 'Return Requested',
            7 => 'Return Accepted',
            8 => 'Returned',
            9 => 'Sent to Courier'
        ];

        return $statusMap[$status] ?? 'Unknown';
    }
}