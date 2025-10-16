<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Services\OrderIntervalService;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class OrderRestrictionController extends Controller
{
    protected $orderIntervalService;

    public function __construct(OrderIntervalService $orderIntervalService)
    {
        $this->orderIntervalService = $orderIntervalService;
    }

    /**
     * Show order restriction dashboard
     */
    public function index()
    {
        $stats = $this->getDetailedStats();
        $recentRestrictions = $this->getRecentRestrictions();
        $suspiciousActivity = $this->getSuspiciousActivity();
        
        return view('admin.order-restrictions.index', compact('stats', 'recentRestrictions', 'suspiciousActivity'));
    }

    /**
     * Get detailed statistics about order restrictions
     */
    public function getStats()
    {
        $stats = $this->getDetailedStats();
        return response()->json($stats);
    }

    /**
     * Get recent restriction attempts
     */
    public function getRecentRestrictions()
    {
        $cutoffTime = Carbon::now()->subHours(24);
        
        $restrictions = Order::where('created_at', '>=', $cutoffTime)
            ->whereNull('user_id')
            ->select([
                'phone',
                'email', 
                'ip_address',
                'browser_fingerprint',
                'created_at',
                'first_name',
                DB::raw('COUNT(*) as attempt_count')
            ])
            ->groupBy(['phone', 'email', 'ip_address', 'browser_fingerprint', 'first_name'])
            ->having('attempt_count', '>', 1)
            ->orderBy('attempt_count', 'desc')
            ->limit(50)
            ->get();

        return $restrictions;
    }

    /**
     * Get suspicious activity patterns
     */
    public function getSuspiciousActivity()
    {
        $cutoffTime = Carbon::now()->subHours(24);
        
        $suspiciousActivity = [
            'high_frequency_ips' => $this->getHighFrequencyIPs($cutoffTime),
            'multiple_phones_same_ip' => $this->getMultiplePhonesFromSameIP($cutoffTime),
            'rapid_order_patterns' => $this->getRapidOrderPatterns($cutoffTime),
            'suspicious_fingerprints' => $this->getSuspiciousFingerprints($cutoffTime),
        ];

        return $suspiciousActivity;
    }

    /**
     * Remove restriction for specific identifier
     */
    public function removeRestriction(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'type' => 'required|in:phone,ip,fingerprint,subnet'
        ]);

        $result = $this->orderIntervalService->removeRestriction(
            $request->identifier,
            $request->type
        );

        if ($result) {
            // Log the admin action
            \Log::info('Order restriction removed by admin', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'identifier' => $request->identifier,
                'type' => $request->type,
                'timestamp' => now(),
            ]);

            notify()->success('Restriction removed successfully', 'Success');
        } else {
            notify()->error('Failed to remove restriction', 'Error');
        }

        return response()->json([
            'success' => $result,
            'message' => $result ? 'Restriction removed successfully' : 'Failed to remove restriction'
        ]);
    }

    /**
     * Bulk remove restrictions
     */
    public function bulkRemoveRestrictions(Request $request)
    {
        $request->validate([
            'identifiers' => 'required|array',
            'identifiers.*.identifier' => 'required|string',
            'identifiers.*.type' => 'required|in:phone,ip,fingerprint,subnet'
        ]);

        $successCount = 0;
        $failureCount = 0;

        foreach ($request->identifiers as $item) {
            $result = $this->orderIntervalService->removeRestriction(
                $item['identifier'],
                $item['type']
            );

            if ($result) {
                $successCount++;
            } else {
                $failureCount++;
            }
        }

        // Log bulk action
        \Log::info('Bulk order restrictions removed by admin', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'total_items' => count($request->identifiers),
            'timestamp' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Removed {$successCount} restrictions successfully. {$failureCount} failed.",
            'success_count' => $successCount,
            'failure_count' => $failureCount
        ]);
    }

    /**
     * Add manual restriction
     */
    public function addRestriction(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'type' => 'required|in:phone,ip,fingerprint,subnet',
            'duration_minutes' => 'required|integer|min:1|max:1440', // Max 24 hours
            'reason' => 'nullable|string|max:255'
        ]);

        try {
            // Add to cache manually
            $cacheKey = 'order_interval_' . $request->type . '_' . $request->identifier;
            $expiresAt = time() + ($request->duration_minutes * 60);
            
            $cacheData = [
                'identifier' => $request->identifier,
                'type' => $request->type,
                'created_at' => time(),
                'expires_at' => $expiresAt,
                'reason' => $request->reason,
                'added_by_admin' => auth()->id(),
            ];

            \Cache::put($cacheKey, $cacheData, $request->duration_minutes * 60);

            // Log the action
            \Log::info('Manual order restriction added by admin', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'identifier' => $request->identifier,
                'type' => $request->type,
                'duration_minutes' => $request->duration_minutes,
                'reason' => $request->reason,
                'timestamp' => now(),
            ]);

            notify()->success('Restriction added successfully', 'Success');

            return response()->json([
                'success' => true,
                'message' => 'Restriction added successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to add manual restriction', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
                'identifier' => $request->identifier,
                'type' => $request->type,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add restriction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed statistics
     */
    private function getDetailedStats()
    {
        $stats = $this->orderIntervalService->getRestrictionStats();
        
        // Add more detailed analytics
        $now = Carbon::now();
        $last24Hours = $now->copy()->subHours(24);
        $last7Days = $now->copy()->subDays(7);
        $last30Days = $now->copy()->subDays(30);

        // Guest orders analysis
        $guestOrderStats = [
            'last_24h' => Order::whereNull('user_id')->where('created_at', '>=', $last24Hours)->count(),
            'last_7d' => Order::whereNull('user_id')->where('created_at', '>=', $last7Days)->count(),
            'last_30d' => Order::whereNull('user_id')->where('created_at', '>=', $last30Days)->count(),
        ];

        // IP analysis
        $ipStats = [
            'unique_ips_24h' => Order::whereNull('user_id')
                ->where('created_at', '>=', $last24Hours)
                ->distinct('ip_address')
                ->count('ip_address'),
            'high_frequency_ips' => Order::whereNull('user_id')
                ->where('created_at', '>=', $last24Hours)
                ->select('ip_address', DB::raw('COUNT(*) as order_count'))
                ->groupBy('ip_address')
                ->having('order_count', '>', 5)
                ->count(),
        ];

        // Phone number variations
        $phoneStats = [
            'unique_phones_24h' => Order::whereNull('user_id')
                ->where('created_at', '>=', $last24Hours)
                ->distinct('phone')
                ->count('phone'),
            'suspicious_phone_patterns' => $this->getSuspiciousPhonePatterns($last24Hours),
        ];

        // Browser fingerprint analysis
        $fingerprintStats = [
            'unique_fingerprints_24h' => Order::whereNull('user_id')
                ->where('created_at', '>=', $last24Hours)
                ->whereNotNull('browser_fingerprint')
                ->distinct('browser_fingerprint')
                ->count('browser_fingerprint'),
            'reused_fingerprints' => Order::whereNull('user_id')
                ->where('created_at', '>=', $last24Hours)
                ->whereNotNull('browser_fingerprint')
                ->select('browser_fingerprint', DB::raw('COUNT(*) as usage_count'))
                ->groupBy('browser_fingerprint')
                ->having('usage_count', '>', 2)
                ->count(),
        ];

        return array_merge($stats, [
            'guest_orders' => $guestOrderStats,
            'ip_analysis' => $ipStats,
            'phone_analysis' => $phoneStats,
            'fingerprint_analysis' => $fingerprintStats,
            'restriction_effectiveness' => $this->calculateRestrictionEffectiveness(),
        ]);
    }

    /**
     * Get high frequency IPs
     */
    private function getHighFrequencyIPs($cutoffTime)
    {
        return Order::whereNull('user_id')
            ->where('created_at', '>=', $cutoffTime)
            ->select('ip_address', DB::raw('COUNT(*) as order_count'), DB::raw('COUNT(DISTINCT phone) as unique_phones'))
            ->groupBy('ip_address')
            ->having('order_count', '>', 10)
            ->orderBy('order_count', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get multiple phones from same IP
     */
    private function getMultiplePhonesFromSameIP($cutoffTime)
    {
        return Order::whereNull('user_id')
            ->where('created_at', '>=', $cutoffTime)
            ->select('ip_address', DB::raw('COUNT(DISTINCT phone) as unique_phones'), DB::raw('COUNT(*) as total_orders'))
            ->groupBy('ip_address')
            ->having('unique_phones', '>', 5)
            ->orderBy('unique_phones', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get rapid order patterns
     */
    private function getRapidOrderPatterns($cutoffTime)
    {
        return DB::select("
            SELECT 
                phone,
                ip_address,
                COUNT(*) as order_count,
                MIN(created_at) as first_order,
                MAX(created_at) as last_order,
                TIMESTAMPDIFF(MINUTE, MIN(created_at), MAX(created_at)) as time_span_minutes
            FROM orders 
            WHERE user_id IS NULL 
                AND created_at >= ? 
            GROUP BY phone, ip_address
            HAVING order_count > 1 
                AND time_span_minutes < 30
            ORDER BY order_count DESC, time_span_minutes ASC
            LIMIT 20
        ", [$cutoffTime]);
    }

    /**
     * Get suspicious fingerprints
     */
    private function getSuspiciousFingerprints($cutoffTime)
    {
        return Order::whereNull('user_id')
            ->where('created_at', '>=', $cutoffTime)
            ->whereNotNull('browser_fingerprint')
            ->select('browser_fingerprint', DB::raw('COUNT(*) as usage_count'), DB::raw('COUNT(DISTINCT phone) as unique_phones'), DB::raw('COUNT(DISTINCT ip_address) as unique_ips'))
            ->groupBy('browser_fingerprint')
            ->having('usage_count', '>', 5)
            ->orderBy('usage_count', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get suspicious phone patterns
     */
    private function getSuspiciousPhonePatterns($cutoffTime)
    {
        // Look for sequential phone numbers or patterns
        return DB::select("
            SELECT 
                SUBSTRING(phone, 1, 8) as phone_prefix,
                COUNT(*) as count,
                GROUP_CONCAT(DISTINCT phone ORDER BY phone) as phones
            FROM orders 
            WHERE user_id IS NULL 
                AND created_at >= ?
                AND phone IS NOT NULL
            GROUP BY SUBSTRING(phone, 1, 8)
            HAVING count > 3
            ORDER BY count DESC
            LIMIT 10
        ", [$cutoffTime]);
    }

    /**
     * Calculate restriction effectiveness
     */
    private function calculateRestrictionEffectiveness()
    {
        $last7Days = Carbon::now()->subDays(7);
        
        // Get orders before and after restriction implementation
        $ordersBeforeRestriction = Order::whereNull('user_id')
            ->where('created_at', '>=', $last7Days->copy()->subDays(7))
            ->where('created_at', '<', $last7Days)
            ->count();
            
        $ordersAfterRestriction = Order::whereNull('user_id')
            ->where('created_at', '>=', $last7Days)
            ->count();
            
        $reductionPercentage = $ordersBeforeRestriction > 0 
            ? round((($ordersBeforeRestriction - $ordersAfterRestriction) / $ordersBeforeRestriction) * 100, 2)
            : 0;

        return [
            'orders_before_7d' => $ordersBeforeRestriction,
            'orders_after_7d' => $ordersAfterRestriction,
            'reduction_percentage' => $reductionPercentage,
            'effectiveness_score' => min(max($reductionPercentage, 0), 100), // Clamp between 0-100
        ];
    }

    /**
     * Export restriction data
     */
    public function exportData(Request $request)
    {
        $request->validate([
            'type' => 'required|in:restrictions,suspicious_activity,statistics',
            'format' => 'required|in:csv,json'
        ]);

        switch ($request->type) {
            case 'restrictions':
                $data = $this->getRecentRestrictions();
                break;
            case 'suspicious_activity':
                $data = $this->getSuspiciousActivity();
                break;
            case 'statistics':
                $data = $this->getDetailedStats();
                break;
            default:
                return response()->json(['error' => 'Invalid export type'], 400);
        }

        if ($request->format === 'csv') {
            return $this->exportToCsv($data, $request->type);
        } else {
            return response()->json($data);
        }
    }

    /**
     * Export data to CSV
     */
    private function exportToCsv($data, $type)
    {
        $filename = "order_restrictions_{$type}_" . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $type) {
            $file = fopen('php://output', 'w');
            
            // Write headers based on type
            if ($type === 'restrictions') {
                fputcsv($file, ['Phone', 'Email', 'IP Address', 'Fingerprint', 'First Name', 'Attempt Count', 'Last Attempt']);
                
                foreach ($data as $item) {
                    fputcsv($file, [
                        $item->phone,
                        $item->email,
                        $item->ip_address,
                        $item->browser_fingerprint,
                        $item->first_name,
                        $item->attempt_count,
                        $item->created_at,
                    ]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get real-time monitoring data
     */
    public function getRealtimeData()
    {
        $last5Minutes = Carbon::now()->subMinutes(5);
        
        $realtimeData = [
            'recent_orders' => Order::whereNull('user_id')
                ->where('created_at', '>=', $last5Minutes)
                ->select(['phone', 'ip_address', 'created_at', 'first_name'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
                
            'active_restrictions' => $this->getActiveRestrictions(),
            
            'current_risk_levels' => $this->getCurrentRiskLevels(),
        ];

        return response()->json($realtimeData);
    }

    /**
     * Get active restrictions from cache
     */
    private function getActiveRestrictions()
    {
        try {
            $pattern = 'order_interval_*';
            $keys = \Cache::getRedis()->keys($pattern);
            
            $activeRestrictions = [];
            foreach ($keys as $key) {
                $data = \Cache::get($key);
                if ($data && isset($data['expires_at']) && $data['expires_at'] > time()) {
                    $activeRestrictions[] = [
                        'key' => $key,
                        'identifier' => $data['identifier'] ?? 'unknown',
                        'type' => $data['type'] ?? 'unknown',
                        'expires_at' => $data['expires_at'],
                        'remaining_seconds' => $data['expires_at'] - time(),
                    ];
                }
            }
            
            return $activeRestrictions;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get current risk levels
     */
    private function getCurrentRiskLevels()
    {
        $cutoffTime = Carbon::now()->subMinutes(30);
        
        $recentActivity = Order::whereNull('user_id')
            ->where('created_at', '>=', $cutoffTime)
            ->select('ip_address', DB::raw('COUNT(*) as activity_count'))
            ->groupBy('ip_address')
            ->orderBy('activity_count', 'desc')
            ->limit(10)
            ->get();

        $riskLevels = [];
        foreach ($recentActivity as $activity) {
            $risk = 'low';
            if ($activity->activity_count > 10) {
                $risk = 'high';
            } elseif ($activity->activity_count > 5) {
                $risk = 'medium';
            }
            
            $riskLevels[] = [
                'ip_address' => $activity->ip_address,
                'activity_count' => $activity->activity_count,
                'risk_level' => $risk,
            ];
        }

        return $riskLevels;
    }
}