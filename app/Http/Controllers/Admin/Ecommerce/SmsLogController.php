<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\SmsLog;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsLogController extends Controller
{
    /**
     * Display a listing of SMS logs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = SmsLog::with(['order', 'user'])->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by phone
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $smsLogs = $query->paginate(50);
        
        // Get statistics
        $smsService = new SmsService();
        $stats = $smsService->getSmsStats();

        return view('admin.e-commerce.sms.index', compact('smsLogs', 'stats'));
    }

    /**
     * Show the form for testing SMS configuration.
     *
     * @return \Illuminate\Http\Response
     */
    public function testForm()
    {
        return view('admin.e-commerce.sms.test');
    }

    /**
     * Test SMS configuration by sending a test message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
            'message' => 'nullable|string|max:500',
        ]);

        try {
            $smsService = new SmsService();
            
            $message = $request->message ?: "Test SMS from " . setting('APP_NAME') . ". SMS service is working correctly!";
            
            $result = $smsService->sendSms(
                $request->phone, 
                $message, 
                'custom', 
                null, 
                auth()->id()
            );
            
            if ($result) {
                notify()->success("Test SMS sent successfully to {$request->phone}", "Success");
                Log::info("Admin " . auth()->user()->name . " sent test SMS to {$request->phone}");
            } else {
                notify()->error("Failed to send test SMS. Please check your SMS configuration.", "Error");
            }
            
        } catch (\Exception $e) {
            Log::error("Error sending test SMS: " . $e->getMessage());
            notify()->error("An error occurred while sending test SMS: " . $e->getMessage(), "Error");
        }
        
        return back();
    }

    /**
     * Show SMS configuration status and statistics.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $smsService = new SmsService();
        
        // Get overall statistics
        $stats = $smsService->getSmsStats();
        
        // Get today's statistics
        $todayStats = [
            'total' => SmsLog::whereDate('created_at', today())->count(),
            'sent' => SmsLog::whereDate('created_at', today())->where('status', 'sent')->count(),
            'failed' => SmsLog::whereDate('created_at', today())->where('status', 'failed')->count(),
            'pending' => SmsLog::whereDate('created_at', today())->where('status', 'pending')->count(),
        ];
        
        // Get recent SMS logs
        $recentLogs = $smsService->getRecentSmsLogs(10);
        
        // Get SMS configuration status
        $configStatus = [
            'enabled' => setting('sms_config_status') == 1,
            'api_url' => !empty(setting('SMS_API_URL')),
            'api_key' => !empty(setting('SMS_API_KEY')),
            'sender_id' => !empty(setting('SMS_API_SENDER_ID')),
        ];
        
        return view('admin.e-commerce.sms.dashboard', compact('stats', 'todayStats', 'recentLogs', 'configStatus'));
    }

    /**
     * Show the details of a specific SMS log.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $smsLog = SmsLog::with(['order', 'user'])->findOrFail($id);
        
        return view('admin.e-commerce.sms.show', compact('smsLog'));
    }

    /**
     * Delete a SMS log.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $smsLog = SmsLog::findOrFail($id);
            $smsLog->delete();
            
            notify()->success("SMS log deleted successfully", "Success");
            Log::info("Admin " . auth()->user()->name . " deleted SMS log ID: {$id}");
            
        } catch (\Exception $e) {
            Log::error("Error deleting SMS log: " . $e->getMessage());
            notify()->error("Failed to delete SMS log", "Error");
        }
        
        return back();
    }

    /**
     * Bulk delete SMS logs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:sms_logs,id'
        ]);

        try {
            $deletedCount = SmsLog::whereIn('id', $request->ids)->delete();
            
            notify()->success("Successfully deleted {$deletedCount} SMS logs", "Success");
            Log::info("Admin " . auth()->user()->name . " bulk deleted {$deletedCount} SMS logs");
            
        } catch (\Exception $e) {
            Log::error("Error bulk deleting SMS logs: " . $e->getMessage());
            notify()->error("Failed to delete SMS logs", "Error");
        }
        
        return back();
    }

    /**
     * Clear old SMS logs (older than specified days).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function clearOld(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        try {
            $cutoffDate = now()->subDays($request->days);
            $deletedCount = SmsLog::where('created_at', '<', $cutoffDate)->delete();
            
            notify()->success("Successfully deleted {$deletedCount} old SMS logs", "Success");
            Log::info("Admin " . auth()->user()->name . " cleared SMS logs older than {$request->days} days. Deleted: {$deletedCount}");
            
        } catch (\Exception $e) {
            Log::error("Error clearing old SMS logs: " . $e->getMessage());
            notify()->error("Failed to clear old SMS logs", "Error");
        }
        
        return back();
    }
}