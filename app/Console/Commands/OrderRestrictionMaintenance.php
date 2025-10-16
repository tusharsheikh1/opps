<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrderIntervalService;
use App\Models\Order;
use Carbon\Carbon;
use DB;

class OrderRestrictionMaintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:restriction-maintenance 
                           {action=cleanup : The action to perform (cleanup, analyze, report, monitor)}
                           {--hours=24 : Number of hours to analyze}
                           {--dry-run : Run without making changes}
                           {--verbose : Show detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Maintain and analyze order restriction system';

    protected $orderIntervalService;

    /**
     * Create a new command instance.
     */
    public function __construct(OrderIntervalService $orderIntervalService)
    {
        parent::__construct();
        $this->orderIntervalService = $orderIntervalService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        $this->info("Starting order restriction maintenance: {$action}");
        
        switch ($action) {
            case 'cleanup':
                return $this->cleanup();
            case 'analyze':
                return $this->analyze();
            case 'report':
                return $this->report();
            case 'monitor':
                return $this->monitor();
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }
    }

    /**
     * Clean up expired cache entries and old data
     */
    protected function cleanup()
    {
        $this->info('Starting cleanup process...');
        
        $dryRun = $this->option('dry-run');
        $cleanupCount = 0;
        
        try {
            // Clean up expired cache entries
            $this->info('Cleaning up expired cache entries...');
            if (!$dryRun) {
                $this->orderIntervalService->cleanupExpiredEntries();
            }
            
            // Clean up old order tracking data (older than 30 days)
            $cutoffDate = Carbon::now()->subDays(30);
            $oldOrdersQuery = Order::where('created_at', '<', $cutoffDate)
                ->whereNull('user_id');
                
            $oldOrdersCount = $oldOrdersQuery->count();
            
            if ($oldOrdersCount > 0) {
                $this->info("Found {$oldOrdersCount} old guest orders to clean up...");
                
                if (!$dryRun) {
                    // Archive or clean up old tracking data
                    $oldOrdersQuery->update([
                        'ip_address' => null,
                        'browser_fingerprint' => null,
                        'user_agent' => null,
                        'device_info' => null,
                    ]);
                    
                    $cleanupCount += $oldOrdersCount;
                }
            }
            
            // Clean up duplicate fingerprints (keep only latest)
            $this->info('Cleaning up duplicate fingerprints...');
            $duplicateFingerprints = $this->findDuplicateFingerprints();
            
            foreach ($duplicateFingerprints as $fingerprint) {
                if ($this->option('verbose')) {
                    $this->line("Processing fingerprint: {$fingerprint->browser_fingerprint} ({$fingerprint->count} duplicates)");
                }
                
                if (!$dryRun) {
                    // Keep only the latest order for each fingerprint
                    Order::where('browser_fingerprint', $fingerprint->browser_fingerprint)
                        ->whereNull('user_id')
                        ->orderBy('created_at', 'desc')
                        ->skip(1)
                        ->take($fingerprint->count - 1)
                        ->update(['browser_fingerprint' => null]);
                }
            }
            
            if ($dryRun) {
                $this->info('Dry run completed. No changes were made.');
            } else {
                $this->info("Cleanup completed. Processed {$cleanupCount} records.");
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Cleanup failed: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Analyze order patterns and detect anomalies
     */
    protected function analyze()
    {
        $hours = $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($hours);
        
        $this->info("Analyzing order patterns for the last {$hours} hours...");
        
        // Analyze IP patterns
        $this->analyzeIpPatterns($cutoffTime);
        
        // Analyze phone patterns
        $this->analyzePhonePatterns($cutoffTime);
        
        // Analyze fingerprint patterns
        $this->analyzeFingerprintPatterns($cutoffTime);
        
        // Analyze time patterns
        $this->analyzeTimePatterns($cutoffTime);
        
        // Generate recommendations
        $this->generateRecommendations($cutoffTime);
        
        return 0;
    }

    /**
     * Generate comprehensive report
     */
    protected function report()
    {
        $hours = $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($hours);
        
        $this->info("Generating order restriction report for the last {$hours} hours...");
        
        // Get statistics
        $stats = $this->orderIntervalService->getRestrictionStats();
        
        // Display report
        $this->displayReport($stats, $cutoffTime);
        
        return 0;
    }

    /**
     * Monitor real-time activity
     */
    protected function monitor()
    {
        $this->info('Starting real-time monitoring (Press Ctrl+C to stop)...');
        
        $lastCheck = Carbon::now();
        
        while (true) {
            $currentTime = Carbon::now();
            
            // Check for new orders since last check
            $newOrders = Order::whereNull('user_id')
                ->where('created_at', '>=', $lastCheck)
                ->get();
                
            foreach ($newOrders as $order) {
                $riskLevel = $this->assessRiskLevel($order);
                $color = $this->getRiskColor($riskLevel);
                
                $this->line(
                    "<{$color}>[{$order->created_at}] New order: {$order->phone} from {$order->ip_address} (Risk: {$riskLevel})</{$color}>"
                );
            }
            
            $lastCheck = $currentTime;
            sleep(5); // Check every 5 seconds
        }
        
        return 0;
    }

    /**
     * Analyze IP patterns
     */
    protected function analyzeIpPatterns($cutoffTime)
    {
        $this->line("\n<info>IP Pattern Analysis:</info>");
        
        $highFrequencyIps = Order::whereNull('user_id')
            ->where('created_at', '>=', $cutoffTime)
            ->select('ip_address', DB::raw('COUNT(*) as order_count'), DB::raw('COUNT(DISTINCT phone) as unique_phones'))
            ->groupBy('ip_address')
            ->having('order_count', '>', 5)
            ->orderBy('order_count', 'desc')
            ->limit(10)
            ->get();
            
        if ($highFrequencyIps->count() > 0) {
            $this->warn("Found {$highFrequencyIps->count()} high-frequency IP addresses:");
            
            $headers = ['IP Address', 'Orders', 'Unique Phones', 'Risk Level'];
            $rows = [];
            
            foreach ($highFrequencyIps as $ip) {
                $riskLevel = $this->assessIpRiskLevel($ip);
                $rows[] = [
                    $ip->ip_address,
                    $ip->order_count,
                    $ip->unique_phones,
                    $riskLevel
                ];
            }
            
            $this->table($headers, $rows);
        } else {
            $this->info("No high-frequency IP addresses detected.");
        }
    }

    /**
     * Analyze phone patterns
     */
    protected function analyzePhonePatterns($cutoffTime)
    {
        $this->line("\n<info>Phone Pattern Analysis:</info>");
        
        // Sequential phone numbers
        $sequentialPhones = DB::select("
            SELECT 
                SUBSTRING(phone, 1, 8) as phone_prefix,
                COUNT(*) as count,
                GROUP_CONCAT(DISTINCT phone ORDER BY phone) as phones
            FROM orders 
            WHERE user_id IS NULL 
                AND created_at >= ?
                AND phone IS NOT NULL
            GROUP BY SUBSTRING(phone, 1, 8)
            HAVING count > 2
            ORDER BY count DESC
            LIMIT 5
        ", [$cutoffTime]);
        
        if (!empty($sequentialPhones)) {
            $this->warn("Detected potential sequential phone number patterns:");
            
            foreach ($sequentialPhones as $pattern) {
                $this->line("Prefix: {$pattern->phone_prefix}* ({$pattern->count} numbers)");
                if ($this->option('verbose')) {
                    $this->line("  Numbers: {$pattern->phones}");
                }
            }
        } else {
            $this->info("No suspicious phone patterns detected.");
        }
    }

    /**
     * Analyze fingerprint patterns
     */
    protected function analyzeFingerprintPatterns($cutoffTime)
    {
        $this->line("\n<info>Browser Fingerprint Analysis:</info>");
        
        $suspiciousFingerprints = Order::whereNull('user_id')
            ->where('created_at', '>=', $cutoffTime)
            ->whereNotNull('browser_fingerprint')
            ->select('browser_fingerprint', DB::raw('COUNT(*) as usage_count'), DB::raw('COUNT(DISTINCT phone) as unique_phones'))
            ->groupBy('browser_fingerprint')
            ->having('usage_count', '>', 3)
            ->orderBy('usage_count', 'desc')
            ->limit(10)
            ->get();
            
        if ($suspiciousFingerprints->count() > 0) {
            $this->warn("Found {$suspiciousFingerprints->count()} suspicious browser fingerprints:");
            
            $headers = ['Fingerprint (first 8 chars)', 'Usage Count', 'Unique Phones', 'Risk Level'];
            $rows = [];
            
            foreach ($suspiciousFingerprints as $fingerprint) {
                $riskLevel = $this->assessFingerprintRiskLevel($fingerprint);
                $rows[] = [
                    substr($fingerprint->browser_fingerprint, 0, 8) . '...',
                    $fingerprint->usage_count,
                    $fingerprint->unique_phones,
                    $riskLevel
                ];
            }
            
            $this->table($headers, $rows);
        } else {
            $this->info("No suspicious fingerprint patterns detected.");
        }
    }

    /**
     * Analyze time patterns
     */
    protected function analyzeTimePatterns($cutoffTime)
    {
        $this->line("\n<info>Time Pattern Analysis:</info>");
        
        $hourlyDistribution = Order::whereNull('user_id')
            ->where('created_at', '>=', $cutoffTime)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as order_count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
            
        if ($hourlyDistribution->count() > 0) {
            $this->info("Hourly order distribution:");
            
            foreach ($hourlyDistribution as $hour) {
                $bar = str_repeat('█', min($hour->order_count, 50));
                $this->line(sprintf("%02d:00 |%s %d orders", $hour->hour, $bar, $hour->order_count));
            }
            
            // Detect unusual spikes
            $averageOrders = $hourlyDistribution->avg('order_count');
            $spikes = $hourlyDistribution->filter(function($hour) use ($averageOrders) {
                return $hour->order_count > ($averageOrders * 3);
            });
            
            if ($spikes->count() > 0) {
                $this->warn("Detected unusual activity spikes at hours: " . $spikes->pluck('hour')->implode(', '));
            }
        }
    }

    /**
     * Generate recommendations
     */
    protected function generateRecommendations($cutoffTime)
    {
        $this->line("\n<info>Recommendations:</info>");
        
        $recommendations = [];
        
        // Check restriction effectiveness
        $stats = $this->orderIntervalService->getRestrictionStats();
        
        if ($stats['recent_guest_orders'] > 100) {
            $recommendations[] = "Consider reducing the order interval from 5 minutes to 3 minutes due to high volume.";
        }
        
        if ($stats['unique_ips_in_interval'] < ($stats['recent_guest_orders'] * 0.8)) {
            $recommendations[] = "Many orders are coming from repeat IPs - consider IP-based rate limiting.";
        }
        
        // Check for high-risk patterns
        $highRiskIps = Order::whereNull('user_id')
            ->where('created_at', '>=', $cutoffTime)
            ->select('ip_address', DB::raw('COUNT(*) as order_count'))
            ->groupBy('ip_address')
            ->having('order_count', '>', 20)
            ->count();
            
        if ($highRiskIps > 0) {
            $recommendations[] = "Consider blocking or flagging {$highRiskIps} high-frequency IP addresses.";
        }
        
        if (empty($recommendations)) {
            $this->info("System appears to be working well. No immediate recommendations.");
        } else {
            foreach ($recommendations as $index => $recommendation) {
                $this->line(($index + 1) . ". " . $recommendation);
            }
        }
    }

    /**
     * Display comprehensive report
     */
    protected function displayReport($stats, $cutoffTime)
    {
        $this->line("\n<info>=============== ORDER RESTRICTION REPORT ===============</info>");
        $this->line("Report generated: " . Carbon::now()->format('Y-m-d H:i:s'));
        $this->line("Analysis period: " . $cutoffTime->format('Y-m-d H:i:s') . " to " . Carbon::now()->format('Y-m-d H:i:s'));
        
        // Basic statistics
        $this->line("\n<info>Basic Statistics:</info>");
        $this->line("Recent guest orders: " . $stats['recent_guest_orders']);
        $this->line("Unique IPs in interval: " . $stats['unique_ips_in_interval']);
        $this->line("Active cache restrictions: " . $stats['active_cache_restrictions']);
        $this->line("Interval minutes: " . $stats['interval_minutes']);
        
        // Effectiveness metrics
        if (isset($stats['restriction_effectiveness'])) {
            $effectiveness = $stats['restriction_effectiveness'];
            $this->line("\n<info>Restriction Effectiveness:</info>");
            $this->line("Orders before restrictions (7d ago): " . $effectiveness['orders_before_7d']);
            $this->line("Orders after restrictions (last 7d): " . $effectiveness['orders_after_7d']);
            $this->line("Reduction percentage: " . $effectiveness['reduction_percentage'] . "%");
            $this->line("Effectiveness score: " . $effectiveness['effectiveness_score'] . "/100");
        }
        
        // Risk assessment
        $this->line("\n<info>Current Risk Assessment:</info>");
        $riskLevel = $this->calculateOverallRiskLevel($stats);
        $riskColor = $this->getRiskColor($riskLevel);
        $this->line("<{$riskColor}>Overall Risk Level: {$riskLevel}</{$riskColor}>");
        
        $this->line("\n<info>======================================================</info>");
    }

    /**
     * Find duplicate fingerprints
     */
    protected function findDuplicateFingerprints()
    {
        return Order::whereNull('user_id')
            ->whereNotNull('browser_fingerprint')
            ->select('browser_fingerprint', DB::raw('COUNT(*) as count'))
            ->groupBy('browser_fingerprint')
            ->having('count', '>', 1)
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Assess risk level for an order
     */
    protected function assessRiskLevel($order)
    {
        $riskScore = 0;
        
        // Check recent orders from same IP
        $ipOrderCount = Order::whereNull('user_id')
            ->where('ip_address', $order->ip_address)
            ->where('created_at', '>=', Carbon::now()->subHours(1))
            ->count();
            
        if ($ipOrderCount > 5) {
            $riskScore += 30;
        } elseif ($ipOrderCount > 2) {
            $riskScore += 15;
        }
        
        // Check phone pattern
        $phonePrefix = substr($order->phone, 0, 8);
        $similarPhones = Order::whereNull('user_id')
            ->where('phone', 'LIKE', $phonePrefix . '%')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->count();
            
        if ($similarPhones > 3) {
            $riskScore += 25;
        }
        
        // Check fingerprint reuse
        if ($order->browser_fingerprint) {
            $fingerprintUsage = Order::whereNull('user_id')
                ->where('browser_fingerprint', $order->browser_fingerprint)
                ->where('created_at', '>=', Carbon::now()->subHours(24))
                ->count();
                
            if ($fingerprintUsage > 2) {
                $riskScore += 20;
            }
        }
        
        // Check order frequency
        $recentOrders = Order::whereNull('user_id')
            ->where('phone', $order->phone)
            ->where('created_at', '>=', Carbon::now()->subMinutes(30))
            ->count();
            
        if ($recentOrders > 1) {
            $riskScore += 25;
        }
        
        // Determine risk level
        if ($riskScore >= 60) {
            return 'high';
        } elseif ($riskScore >= 30) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Assess IP risk level
     */
    protected function assessIpRiskLevel($ipData)
    {
        $ratio = $ipData->unique_phones / $ipData->order_count;
        
        if ($ipData->order_count > 20 || $ratio > 0.8) {
            return 'high';
        } elseif ($ipData->order_count > 10 || $ratio > 0.5) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Assess fingerprint risk level
     */
    protected function assessFingerprintRiskLevel($fingerprintData)
    {
        $ratio = $fingerprintData->unique_phones / $fingerprintData->usage_count;
        
        if ($fingerprintData->usage_count > 10 || $ratio > 0.7) {
            return 'high';
        } elseif ($fingerprintData->usage_count > 5 || $ratio > 0.4) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Calculate overall risk level
     */
    protected function calculateOverallRiskLevel($stats)
    {
        $riskScore = 0;
        
        // High volume of recent orders
        if ($stats['recent_guest_orders'] > 200) {
            $riskScore += 30;
        } elseif ($stats['recent_guest_orders'] > 100) {
            $riskScore += 15;
        }
        
        // Low IP diversity
        if ($stats['unique_ips_in_interval'] < ($stats['recent_guest_orders'] * 0.3)) {
            $riskScore += 25;
        }
        
        // High cache usage
        if ($stats['active_cache_restrictions'] > 50) {
            $riskScore += 20;
        }
        
        // Poor effectiveness
        if (isset($stats['restriction_effectiveness']) && $stats['restriction_effectiveness']['effectiveness_score'] < 30) {
            $riskScore += 25;
        }
        
        if ($riskScore >= 60) {
            return 'high';
        } elseif ($riskScore >= 30) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Get color for risk level
     */
    protected function getRiskColor($riskLevel)
    {
        switch ($riskLevel) {
            case 'high':
                return 'error';
            case 'medium':
                return 'comment';
            case 'low':
            default:
                return 'info';
        }
    }
}