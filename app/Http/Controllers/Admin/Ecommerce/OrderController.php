<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Ecommerce\OrderEssential\OrderStatusManager;
use App\Http\Controllers\Admin\Ecommerce\OrderEssential\OrderAnalyticsController;
use App\Http\Controllers\Admin\Ecommerce\OrderEssential\OrderSearchController;
use App\Http\Controllers\Admin\Ecommerce\OrderEssential\OrderNotificationService;
use App\Services\BdCourierService;
use App\Models\Order;
use App\Models\PartialPayment;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DB;

class OrderController extends Controller
{
    protected $statusManager;
    protected $analyticsController;
    protected $searchController;
    protected $notificationService;
    protected $bdCourierService;
    
    public function __construct()
    {
        $this->statusManager = new OrderStatusManager();
        $this->analyticsController = new OrderAnalyticsController();
        $this->searchController = new OrderSearchController();
        $this->notificationService = new OrderNotificationService();
        $this->bdCourierService = new BdCourierService();
    }
    
    /**
     * Show customer order list with server-side filtering
     *
     * @return void
     */
    public function index(Request $request)
    {
        return $this->searchController->index($request);
    }

    /**
     * Ajax method for live search
     */
    public function searchOrders(Request $request)
    {
        return $this->searchController->searchOrders($request);
    }

    /**
     * Get analytics data for dashboard
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAnalytics(Request $request)
    {
        return $this->analyticsController->getAnalytics($request);
    }
    
    /**
     * Show commission list
     */
    public function comission()
    {
        $comissions = \App\Models\Commission::latest('id')->where('status','1')->get();
        return view('admin.e-commerce.order.comission', compact('comissions'));
    }
    
    /**
     * Show customer pending order list
     */
    public function pending()
    {
        $orders = Order::where('status', 0)->where('is_pre','0')->latest('id')->get();
        return view('admin.e-commerce.order.pending', compact('orders'));
    }
    
    /**
     * Show pre-orders
     */
    public function pre()
    {
        $orders = Order::where('status', 0)->where('is_pre','1')->latest('id')->get();
        return view('admin.e-commerce.order.pending', compact('orders'));
    }
    
    /**
     * Show customer processing order list
     */
    public function processing()
    {
        $orders = Order::where('status', 1)->latest('id')->get();
        return view('admin.e-commerce.order.processing', compact('orders'));
    }
    
    /**
     * Show customer cancel order list
     */
    public function cancel()
    {
        $orders = Order::where('status', 2)->latest('id')->get();
        return view('admin.e-commerce.order.cancel', compact('orders'));
    }
    
    /**
     * Show customer delivered order list
     */
    public function delivered()
    {
        $orders = Order::where('status', 3)->latest('id')->get();
        return view('admin.e-commerce.order.delivered', compact('orders'));
    }
    
    /**
     * Show order details
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.e-commerce.order.show', compact('order'));
    }
    
    /**
     * Update order details
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Debug: Log the incoming request
            Log::info('Order update attempt', [
                'order_id' => $id,
                'request_data' => $request->all(),
                'admin_id' => auth()->id()
            ]);

            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:500',
                'town' => 'nullable|string|max:100',
                'district' => 'nullable|string|max:100',
                'post_code' => 'nullable|string|max:20',
                'shipping_charge' => 'nullable|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'coupon_code' => 'nullable|string|max:100',
                'admin_notes' => 'nullable|string|max:1000',
            ]);

            $order = Order::findOrFail($id);
            
            // Store original values for logging
            $originalData = $order->toArray();
            
            // Prepare update data
            $updateData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'town' => $request->town,
                'district' => $request->district,
                'post_code' => $request->post_code,
                'shipping_charge' => $request->shipping_charge ?? 0,
                'discount' => $request->discount ?? 0,
                'coupon_code' => $request->coupon_code,
            ];

            // Only add admin_notes if the column exists
            if (Schema::hasColumn('orders', 'admin_notes')) {
                $updateData['admin_notes'] = $request->admin_notes;
            }
            
            // Update order details
            $order->update($updateData);
            
            // Recalculate total if shipping or discount changed
            if ($request->has('shipping_charge') || $request->has('discount')) {
                $subtotal = $order->subtotal;
                $shipping = $request->shipping_charge ?? 0;
                $discount = $request->discount ?? 0;
                $newTotal = $subtotal + $shipping - $discount;
                
                $order->update(['total' => max(0, $newTotal)]);
            }
            
            // Log the update for audit trail
            Log::info("Order {$order->invoice} updated successfully by admin " . auth()->user()->name, [
                'order_id' => $order->id,
                'admin_id' => auth()->id(),
                'changes' => array_diff_assoc($order->fresh()->toArray(), $originalData)
            ]);
            
            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order updated successfully',
                    'order' => [
                        'invoice' => $order->invoice,
                        'total' => $order->total,
                        'updated_at' => $order->updated_at->format('d M Y, h:i A')
                    ]
                ]);
            }
            
            notify()->success("Order updated successfully", "Success");
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Order update validation failed for order {$id}", [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            notify()->error("Validation failed: " . implode(', ', array_flatten($e->errors())), "Validation Error");
            
        } catch (\Exception $e) {
            Log::error("Error updating order {$id}: " . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update order: ' . $e->getMessage()
                ], 500);
            }
            
            notify()->error("Failed to update order: " . $e->getMessage(), "Error");
        }
        
        return back();
    }
    
    /**
     * Order print
     *
     * @param  mixed $id
     * @return void
     */
    public function print($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.e-commerce.order.invoice', compact('order'));
    }
    
    /**
     * Order invoice
     *
     * @param  mixed $id
     * @return void
     */
    public function invoice($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.e-commerce.order.invoice', compact('order'));
    }
    
    /**
     * Send custom SMS to customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendCustomSms(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'message' => 'required|string|max:500',
        ]);

        try {
            $order = Order::findOrFail($request->order_id);
            
            if (empty($order->phone)) {
                notify()->error("Cannot send SMS: Customer phone number not found", "Error");
                return back();
            }

            $smsService = new SmsService();
            
            // Prepare the message with order info
            $customMessage = str_replace(['{invoice}', '{customer_name}', '{total}'], 
                                       [$order->invoice, $order->first_name, $order->total], 
                                       $request->message);
            
            $sent = $smsService->sendSms($order->phone, $customMessage);
            
            if ($sent) {
                // Log the SMS sending for admin records
                Log::info("Custom SMS sent to order {$order->invoice} by admin " . auth()->user()->name . ": {$customMessage}");
                notify()->success("SMS sent successfully to {$order->phone}", "Success");
            } else {
                Log::error("Failed to send custom SMS to order {$order->invoice}: SMS service failed");
                notify()->error("Failed to send SMS. Please check SMS configuration.", "Error");
            }
            
        } catch (\Exception $e) {
            Log::error("Error sending custom SMS: " . $e->getMessage());
            notify()->error("An error occurred while sending SMS", "Error");
        }
        
        return back();
    }
    
    /**
     * Show partial payments
     */
    public function partials()
    {
        $partials = PartialPayment::orderBy('id','desc')->get();
        return view('admin.e-commerce.order.partials', compact('partials'));
    }
    
    /**
     * Update partial payment status
     */
    public function partialStatus($id, $st)
    {
        $partials = PartialPayment::find($id);
        $order = Order::findOrFail($partials->order_id);
        $partials->update(['status' => $st]);
                
        if($st == 1) {
            $parts = DB::table('multi_order')->where('order_id', $partials->order_id)->get();
            $amount = $partials->amount;
            foreach($parts as $part) {
                if($amount > 0) {
                    if($part->partial_pay != $part->total) {
                        $total_requested = $part->partial_pay + $amount;
                          
                        if($total_requested > $part->total) {
                            $new_balance = $total_requested - $part->total;
                            $slice = $amount - $new_balance;
                            $amount -= $slice;
                        } else {
                            $slice = $amount;
                            $amount -= $slice;
                        }
                        
                        DB::table('multi_order')->where('id', $part->id)->update(['partial_pay' => $part->partial_pay + $slice]);
                    }
                }
            }   
        }
        
        $this->notificationService->sendNotification('pertials', $order->invoice, $order->user_id);
        notify()->success("Successfully", "Congratulations");
        return back();
    }
    
    /**
     * Delete order
     */
    public function delete($id)
    {
        $order = Order::findOrFail($id);
        DB::table('order_details')->where('order_id', $id)->delete();
        PartialPayment::where('order_id', $id)->delete();
        \App\Models\OrderDetails::where('order_id', $id)->delete();
        $order->delete();
        notify()->success("Order Delete successfully", "Congratulations");
        return redirect()->route('admin.order.index');
    }
    
    /**
     * Update sub order status
     */
    public function sub_status($id, $status, $vendor)
    {
        DB::table('multi_order')->where('order_id', $id)->where('vendor_id', $vendor)->update(['status' => $status]);
        notify()->success("Order status successfully changed", "Congratulations");
        return back();
    }

    /**
     * Get order history by phone number (Enhanced with Courier Data)
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

            // Get courier history using BD Courier API
            $courierHistory = $this->bdCourierService->getCourierHistory($phone);
            $riskAssessment = $this->bdCourierService->getRiskAssessment($phone);

            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'orders' => [],
                    'message' => 'No orders found for this phone number',
                    'summary' => $this->getEmptyOrderSummary(),
                    'courier_history' => $courierHistory,
                    'risk_assessment' => $riskAssessment
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
                    'phone' => $phone,
                    'courier_history' => $courierHistory,
                    'risk_assessment' => $riskAssessment
                ]);
            }

            return view('admin.e-commerce.order.phone-history', compact('orders', 'summary', 'phone', 'courierHistory', 'riskAssessment'));

        } catch (\Exception $e) {
            Log::error('Phone history fetch failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order history'
            ], 500);
        }
    }

    /**
     * Test BD Courier API connection
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testBdCourierApi(Request $request)
    {
        try {
            $phone = $request->input('phone', '01700000000');
            
            Log::info('Testing BD Courier API', [
                'phone' => $phone, 
                'admin_id' => auth()->id(),
                'api_key_configured' => !empty(env('BD_COURIER_API_KEY')),
                'api_key_length' => strlen(env('BD_COURIER_API_KEY') ?? '')
            ]);
            
            $startTime = microtime(true);
            
            // Test courier history
            $courierResult = $this->bdCourierService->getCourierHistory($phone);
            $riskResult = $this->bdCourierService->getRiskAssessment($phone);
            
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2); // in milliseconds
            
            Log::info('BD Courier API test completed', [
                'phone' => $phone,
                'execution_time_ms' => $executionTime,
                'courier_success' => $courierResult['success'] ?? false,
                'has_courier_data' => isset($courierResult['data']) && !empty($courierResult['data'])
            ]);
            
            return response()->json([
                'success' => true,
                'phone' => $phone,
                'execution_time_ms' => $executionTime,
                'api_configuration' => [
                    'api_key_configured' => !empty(env('BD_COURIER_API_KEY')),
                    'api_key_length' => strlen(env('BD_COURIER_API_KEY') ?? ''),
                    'api_url' => 'https://bdcourier.com/api/pro/courier-check',
                    'environment' => env('APP_ENV'),
                ],
                'courier_result' => $courierResult,
                'risk_assessment' => $riskResult,
                'test_timestamp' => now()->toDateTimeString(),
                'test_performed_by' => auth()->user()->name ?? 'Unknown'
            ]);
            
        } catch (\Exception $e) {
            Log::error('BD Courier API test failed: ' . $e->getMessage(), [
                'phone' => $request->input('phone', '01700000000'),
                'admin_id' => auth()->id(),
                'exception' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'phone' => $request->input('phone', '01700000000'),
                'api_configuration' => [
                    'api_key_configured' => !empty(env('BD_COURIER_API_KEY')),
                    'api_key_length' => strlen(env('BD_COURIER_API_KEY') ?? ''),
                    'api_url' => 'https://bdcourier.com/api/pro/courier-check',
                    'environment' => env('APP_ENV'),
                ],
                'test_timestamp' => now()->toDateTimeString(),
            ], 500);
        }
    }

    /**
     * Get detailed API debug information
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function debugBdCourierApi(Request $request)
    {
        try {
            $phone = $request->input('phone', '01700000000');
            
            // Test connection without cache
            Cache::forget('bd_courier_' . $phone);
            
            $debugInfo = [
                'environment' => [
                    'app_env' => env('APP_ENV'),
                    'app_debug' => env('APP_DEBUG'),
                    'api_key_configured' => !empty(env('BD_COURIER_API_KEY')),
                    'api_key_preview' => substr(env('BD_COURIER_API_KEY') ?? '', 0, 10) . '...',
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'server_time' => now()->toDateTimeString(),
                ],
                'network_test' => [],
                'api_test' => []
            ];
            
            // Test basic network connectivity
            try {
                $response = Http::timeout(10)->get('https://bdcourier.com');
                $debugInfo['network_test'] = [
                    'bdcourier_reachable' => $response->successful(),
                    'status_code' => $response->status(),
                    'response_time_ms' => round($response->transferStats->getTransferTime() * 1000, 2)
                ];
            } catch (\Exception $e) {
                $debugInfo['network_test'] = [
                    'bdcourier_reachable' => false,
                    'error' => $e->getMessage()
                ];
            }
            
            // Test API endpoint specifically
            $courierResult = $this->bdCourierService->getCourierHistory($phone);
            $debugInfo['api_test'] = $courierResult;
            
            return response()->json([
                'success' => true,
                'debug_info' => $debugInfo,
                'phone_tested' => $phone,
                'timestamp' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'debug_info' => [
                    'environment' => [
                        'app_env' => env('APP_ENV'),
                        'api_key_configured' => !empty(env('BD_COURIER_API_KEY')),
                    ]
                ],
                'timestamp' => now()->toDateTimeString()
            ], 500);
        }
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

    // Status management methods - delegated to StatusManager
    public function statusPending($id, $isBulk = false)
    {
        return $this->statusManager->statusPending($id, $isBulk);
    }

    public function statusProcessing($id, $isBulk = false)
    {
        return $this->statusManager->statusProcessing($id, $isBulk);
    }

    public function statusShipping($id, $isBulk = false)
    {
        return $this->statusManager->statusShipping($id, $isBulk);
    }

    public function statusCancel($id, $isBulk = false)
    {
        return $this->statusManager->statusCancel($id, $isBulk);
    }

    public function statusDelivered($id, $isBulk = false)
    {
        return $this->statusManager->statusDelivered($id, $isBulk);
    }

    public function returnAccept($id, $isBulk = false)
    {
        return $this->statusManager->returnAccept($id, $isBulk);
    }

    public function returnComplete($id, $isBulk = false)
    {
        return $this->statusManager->returnComplete($id, $isBulk);
    }

    public function refund(Request $request)
    {
        return $this->statusManager->refund($request);
    }

    public function refund_two(Request $request)
    {
        return $this->statusManager->refund_two($request);
    }

    public function bulkStatusUpdate(Request $request)
    {
        return $this->statusManager->bulkStatusUpdate($request);
    }
}