<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\SteadfastCourierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeliveryTrackingController extends Controller
{
    protected $steadfastService;

    public function __construct()
    {
        $this->steadfastService = new SteadfastCourierService();
    }

    /**
     * Display delivery tracking dashboard
     */
    public function index(Request $request)
    {
        $query = Order::sentToCourier()->with(['user']);

        // Apply filters
        if ($request->filled('delivery_status')) {
            $query->where('delivery_status', $request->delivery_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice', 'like', "%{$search}%")
                  ->orWhere('consignment_id', 'like', "%{$search}%")
                  ->orWhere('tracking_code', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(50);

        // Get statistics
        $stats = $this->getDeliveryStats();

        return view('admin.e-commerce.delivery.index', compact('orders', 'stats'));
    }

    /**
     * Show single order delivery details
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        
        // Get latest delivery status
        if ($order->hasBeenSentToCourier()) {
            $this->updateSingleOrderStatus($order);
        }

        return view('admin.e-commerce.delivery.show', compact('order'));
    }

    /**
     * Send order to Steadfast courier
     */
    public function sendToCourier(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->hasBeenSentToCourier()) {
            notify()->warning('Order has already been sent to courier', 'Warning');
            return back();
        }

        if (!$this->steadfastService->isConfigured()) {
            notify()->error('Steadfast courier service is not configured properly', 'Error');
            return back();
        }

        $result = $order->sendToSteadfast($request->note);

        if ($result['success']) {
            notify()->success('Order sent to courier successfully', 'Success');
            Log::info('Order sent to courier via admin', [
                'order_id' => $order->id,
                'invoice' => $order->invoice,
                'admin_id' => auth()->id(),
                'consignment_id' => $result['data']['consignment_id'] ?? null,
            ]);
        } else {
            notify()->error($result['message'], 'Error');
        }

        return back();
    }

    /**
     * Update delivery status for single order
     */
    public function updateStatus($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->hasBeenSentToCourier()) {
            notify()->warning('Order has not been sent to courier yet', 'Warning');
            return back();
        }

        $updated = $this->updateSingleOrderStatus($order);

        if ($updated) {
            notify()->success('Delivery status updated successfully', 'Success');
        } else {
            notify()->warning('Failed to update delivery status', 'Warning');
        }

        return back();
    }

    /**
     * Bulk update delivery statuses
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'integer|exists:orders,id'
        ]);

        $orders = Order::whereIn('id', $request->order_ids)
                      ->sentToCourier()
                      ->get();

        $successCount = 0;
        $errorCount = 0;

        foreach ($orders as $order) {
            try {
                if ($this->updateSingleOrderStatus($order)) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Bulk delivery status update error', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        notify()->success("Updated {$successCount} orders successfully. {$errorCount} errors.", 'Bulk Update Complete');

        return back();
    }

    /**
     * Get courier balance
     */
    public function getBalance()
    {
        if (!$this->steadfastService->isConfigured()) {
            notify()->error('Steadfast courier service is not configured', 'Error');
            return back();
        }

        $response = $this->steadfastService->getCurrentBalance();

        if ($response['status'] === 200) {
            $balance = $response['current_balance'];
            notify()->success("Current courier balance: ৳{$balance}", 'Balance Check');
        } else {
            notify()->error('Failed to check courier balance', 'Error');
        }

        return back();
    }

    /**
     * Export delivery report
     */
    public function exportReport(Request $request)
    {
        $query = Order::sentToCourier()->with(['user']);

        // Apply same filters as index
        if ($request->filled('delivery_status')) {
            $query->where('delivery_status', $request->delivery_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice', 'like', "%{$search}%")
                  ->orWhere('consignment_id', 'like', "%{$search}%")
                  ->orWhere('tracking_code', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'delivery_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        return response()->streamDownload(function() use ($orders) {
            $handle = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($handle, [
                'Invoice',
                'Customer Name',
                'Phone',
                'Address',
                'Order Date',
                'Total Amount',
                'COD Amount',
                'Consignment ID',
                'Tracking Code',
                'Delivery Status',
                'Status Updated At',
                'Internal Status'
            ]);

            // CSV Data
            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->invoice,
                    $order->first_name . ' ' . $order->last_name,
                    $order->phone,
                    $order->address . ', ' . $order->town . ', ' . $order->district,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->total,
                    $order->pay_staus == 1 ? 0 : $order->total,
                    $order->consignment_id,
                    $order->tracking_code,
                    $order->delivery_status_text,
                    $order->delivery_status_updated_at ? $order->delivery_status_updated_at->format('Y-m-d H:i:s') : '',
                    $order->status_text
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Get delivery statistics
     */
    private function getDeliveryStats()
    {
        $totalSent = Order::sentToCourier()->count();
        
        $statusStats = Order::sentToCourier()
                           ->selectRaw('delivery_status, COUNT(*) as count')
                           ->groupBy('delivery_status')
                           ->pluck('count', 'delivery_status')
                           ->toArray();

        $todayStats = Order::sentToCourier()
                          ->whereDate('created_at', today())
                          ->selectRaw('delivery_status, COUNT(*) as count')
                          ->groupBy('delivery_status')
                          ->pluck('count', 'delivery_status')
                          ->toArray();

        return [
            'total_sent' => $totalSent,
            'delivered' => $statusStats['delivered'] ?? 0,
            'pending' => $statusStats['pending'] ?? 0,
            'in_review' => $statusStats['in_review'] ?? 0,
            'cancelled' => $statusStats['cancelled'] ?? 0,
            'on_hold' => $statusStats['hold'] ?? 0,
            'today_sent' => array_sum($todayStats),
            'today_delivered' => $todayStats['delivered'] ?? 0,
            'status_breakdown' => $statusStats,
        ];
    }

    /**
     * Update single order delivery status
     */
    private function updateSingleOrderStatus($order)
    {
        try {
            return $order->updateDeliveryStatus();
        } catch (\Exception $e) {
            Log::error('Single order delivery status update failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}