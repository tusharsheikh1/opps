<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class OrderIntervalService
{
    const INTERVAL_MINUTES = 5;
    const PRODUCT_RESTRICTION_DAYS = 10; // New constant for product restriction
    const CACHE_PREFIX = 'order_interval_';
    const CACHE_DURATION = 300; // 5 minutes in seconds

    /**
     * NEW: Check if the same product was ordered within the last 10 days
     * Uses device fingerprint and IP address like order interval system
     */
    public function checkProductRestriction(string $phone, int $productId, string $email = null): array
    {
        try {
            $restrictionDays = self::PRODUCT_RESTRICTION_DAYS;
            $cutoffDate = Carbon::now()->subDays($restrictionDays);
            
            // Get device identifiers (same as order interval system)
            $identifiers = $this->getDeviceIdentifiers($phone, $email);
            
            // Check by multiple criteria: phone, device fingerprint, and IP address
            $existingOrder = Order::where('created_at', '>=', $cutoffDate)
                ->whereHas('orderDetails', function($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                ->where(function($query) use ($identifiers) {
                    // Primary check: Same phone number
                    $query->where('phone', $identifiers['phone'])
                    // Secondary check: Same device fingerprint (prevents device bypass)
                    ->orWhere('browser_fingerprint', $identifiers['fingerprint'])
                    // Tertiary check: Same IP address (additional security)
                    ->orWhere('ip_address', $identifiers['ip_address']);
                })
                ->with(['orderDetails' => function($query) use ($productId) {
                    $query->where('product_id', $productId);
                }])
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existingOrder) {
                $lastOrderDate = $existingOrder->created_at;
                $nextAllowedDate = $lastOrderDate->copy()->addDays($restrictionDays);
                $remainingDays = Carbon::now()->diffInDays($nextAllowedDate, false);
                
                if ($remainingDays > 0) {
                    // Determine restriction reason based on what matched
                    $restrictionReason = 'phone';
                    if ($existingOrder->browser_fingerprint === $identifiers['fingerprint']) {
                        $restrictionReason = 'device';
                    } elseif ($existingOrder->ip_address === $identifiers['ip_address']) {
                        $restrictionReason = 'ip';
                    }
                    
                    return [
                        'restricted' => true,
                        'remaining_days' => $remainingDays,
                        'last_order_date' => $lastOrderDate->format('d-m-Y'),
                        'next_allowed_date' => $nextAllowedDate->format('d-m-Y'),
                        'restriction_type' => 'product',
                        'restriction_reason' => $restrictionReason,
                        'whatsapp_number' => setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX',
                        'order_id' => $existingOrder->order_id ?? $existingOrder->id,
                        'last_order_phone' => $existingOrder->phone,
                        'current_phone' => $identifiers['phone'],
                        'device_matched' => $existingOrder->browser_fingerprint === $identifiers['fingerprint'],
                        'ip_matched' => $existingOrder->ip_address === $identifiers['ip_address'],
                    ];
                }
            }

            return ['restricted' => false];
            
        } catch (\Exception $e) {
            Log::error('Product restriction check failed', [
                'error' => $e->getMessage(),
                'phone' => $phone,
                'product_id' => $productId
            ]);
            
            return ['restricted' => false];
        }
    }

    /**
     * Generate a unique fingerprint for the device/browser
     */
    public function generateUserFingerprint(): string
    {
        $agent = new Agent();
        
        $fingerprint = collect([
            'ip' => $this->getClientIpAddress(),
            'user_agent' => $agent->getUserAgent(),
            'browser' => $agent->browser(),
            'browser_version' => $agent->version($agent->browser()),
            'platform' => $agent->platform(),
            'platform_version' => $agent->version($agent->platform()),
            'device' => $agent->device(),
            'languages' => $this->getAcceptLanguages(),
            'screen_resolution' => request()->header('X-Screen-Resolution', ''),
            'timezone' => request()->header('X-Timezone', ''),
            'session_id' => session()->getId(),
        ])->filter()->implode('|');

        return hash('sha256', $fingerprint);
    }

    /**
     * Get the real client IP address
     */
    private function getClientIpAddress(): string
    {
        $ipHeaders = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];

        foreach ($ipHeaders as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return request()->ip() ?? '127.0.0.1';
    }

    /**
     * Get accept languages from request header
     */
    private function getAcceptLanguages(): string
    {
        return request()->header('Accept-Language', '');
    }

    /**
     * Check if device should be restricted from placing an order
     * FIXED: Now checks by device fingerprint primarily, prevents phone number changes
     */
    public function checkOrderRestriction(string $phone, string $email = null): array
    {
        // Only apply to guest users
        if (auth()->check()) {
            return ['restricted' => false];
        }

        // Validate inputs first
        if (empty($phone) || strlen($phone) < 10) {
            return ['restricted' => false];
        }

        $identifiers = $this->getDeviceIdentifiers($phone, $email);
        
        // Primary check: Device-based restriction (prevents phone number changes)
        $deviceRestriction = $this->checkDeviceRestriction($identifiers);
        if ($deviceRestriction['restricted']) {
            return $deviceRestriction;
        }
        
        // Secondary check: Database for recent orders from this device
        $dbRestriction = $this->checkDatabaseRestriction($identifiers);
        if ($dbRestriction['restricted']) {
            return $dbRestriction;
        }

        // Additional check: Cache-based restrictions
        $cacheRestriction = $this->checkCacheRestriction($identifiers);
        if ($cacheRestriction['restricted']) {
            return $cacheRestriction;
        }

        return ['restricted' => false];
    }

    /**
     * Get all device identifiers for tracking
     */
    private function getDeviceIdentifiers(string $phone, string $email = null): array
    {
        $ip = $this->getClientIpAddress();
        $fingerprint = $this->generateUserFingerprint();
        
        return [
            'phone' => $this->normalizePhone($phone),
            'email' => $email ?: 'noreply@lems.shop',
            'ip_address' => $ip,
            'fingerprint' => $fingerprint,
            'ip_subnet' => $this->getIpSubnet($ip),
            'device_key' => $this->generateDeviceKey($ip, $fingerprint), // Primary device identifier
        ];
    }

    /**
     * Generate a unique device key combining IP and fingerprint
     */
    private function generateDeviceKey(string $ip, string $fingerprint): string
    {
        return hash('md5', $ip . '|' . $fingerprint);
    }

    /**
     * Normalize phone number format
     */
    private function normalizePhone(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Convert +880 to 0 prefix for Bangladesh numbers
        if (strlen($phone) === 13 && substr($phone, 0, 3) === '880') {
            $phone = '0' . substr($phone, 3);
        }
        
        return $phone;
    }

    /**
     * Get IP subnet (first 3 octets for IPv4)
     */
    private function getIpSubnet(string $ip): string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            return implode('.', array_slice($parts, 0, 3)) . '.0';
        }
        
        // For IPv6, take first 64 bits
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            return implode(':', array_slice($parts, 0, 4)) . '::';
        }
        
        return $ip;
    }

    /**
     * Check device-based restrictions (PRIMARY CHECK)
     * This prevents phone number changes from bypassing restrictions
     */
    private function checkDeviceRestriction(array $identifiers): array
    {
        try {
            // Primary device restriction key
            $deviceCacheKey = self::CACHE_PREFIX . 'device_' . $identifiers['device_key'];
            $cachedData = Cache::get($deviceCacheKey);
            
            if ($cachedData && isset($cachedData['expires_at'])) {
                $remainingSeconds = $cachedData['expires_at'] - time();
                
                if ($remainingSeconds > 0) {
                    // Check if user is trying to change phone number
                    $phoneChanged = isset($cachedData['last_phone']) && 
                                   $cachedData['last_phone'] !== $identifiers['phone'];
                    
                    $restrictionReason = $phoneChanged 
                        ? 'Device restriction: Phone number change detected' 
                        : 'Device restriction: Recent order from this device';
                    
                    return [
                        'restricted' => true,
                        'remaining_seconds' => $remainingSeconds,
                        'restriction_reason' => $restrictionReason,
                        'restriction_type' => 'device',
                        'whatsapp_number' => setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX',
                        'phone_changed' => $phoneChanged,
                        'last_phone' => $cachedData['last_phone'] ?? null,
                        'current_phone' => $identifiers['phone'],
                    ];
                } else {
                    // Clean up expired cache entry
                    Cache::forget($deviceCacheKey);
                }
            }

            return ['restricted' => false];
            
        } catch (\Exception $e) {
            Log::error('Device restriction check failed', [
                'error' => $e->getMessage(),
                'device_key' => $identifiers['device_key'] ?? 'unknown'
            ]);
            
            return ['restricted' => false];
        }
    }

    /**
     * Check database for recent orders from this device
     */
    private function checkDatabaseRestriction(array $identifiers): array
    {
        try {
            $cutoffTime = Carbon::now()->subMinutes(self::INTERVAL_MINUTES);
            
            // Check for recent orders from this device (IP + fingerprint combination)
            $recentOrder = Order::where('created_at', '>=', $cutoffTime)
                ->whereNull('user_id') // Only guest orders
                ->where(function($query) use ($identifiers) {
                    // Primary check: Same IP AND same fingerprint (same device)
                    $query->where(function($q) use ($identifiers) {
                        $q->where('ip_address', $identifiers['ip_address'])
                          ->where('browser_fingerprint', $identifiers['fingerprint']);
                    })
                    // Secondary check: Same fingerprint with different IP (mobile network change)
                    ->orWhere('browser_fingerprint', $identifiers['fingerprint']);
                })
                ->orderBy('created_at', 'desc')
                ->first();

            if ($recentOrder) {
                $nextAllowedTime = $recentOrder->created_at->addMinutes(self::INTERVAL_MINUTES);
                $now = Carbon::now();
                
                if ($nextAllowedTime > $now) {
                    $remainingSeconds = $nextAllowedTime->diffInSeconds($now);
                    
                    // Check if phone number changed
                    $phoneChanged = $recentOrder->phone !== $identifiers['phone'];
                    
                    $restrictionReason = $phoneChanged 
                        ? 'Device restriction: Phone number change detected from recent order'
                        : 'Device restriction: Recent order from this device';
                    
                    return [
                        'restricted' => true,
                        'remaining_seconds' => $remainingSeconds,
                        'restriction_reason' => $restrictionReason,
                        'restriction_type' => 'device',
                        'whatsapp_number' => setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX',
                        'last_order_time' => $recentOrder->created_at,
                        'last_order_id' => $recentOrder->id,
                        'phone_changed' => $phoneChanged,
                        'last_phone' => $recentOrder->phone,
                        'current_phone' => $identifiers['phone'],
                    ];
                }
            }

            return ['restricted' => false];
            
        } catch (\Exception $e) {
            Log::error('Order restriction DB check failed', [
                'error' => $e->getMessage(),
                'device_key' => $identifiers['device_key'] ?? 'unknown'
            ]);
            
            return ['restricted' => false];
        }
    }

    /**
     * Check cache-based restrictions
     */
    private function checkCacheRestriction(array $identifiers): array
    {
        try {
            // Check various cache keys for restrictions
            $cacheKeys = [
                'ip_' . $identifiers['ip_address'],
                'fingerprint_' . $identifiers['fingerprint'],
                'subnet_' . $identifiers['ip_subnet'],
            ];

            foreach ($cacheKeys as $key) {
                $cacheKey = self::CACHE_PREFIX . $key;
                $cachedData = Cache::get($cacheKey);
                
                if ($cachedData && isset($cachedData['expires_at'])) {
                    $remainingSeconds = $cachedData['expires_at'] - time();
                    
                    if ($remainingSeconds > 0) {
                        return [
                            'restricted' => true,
                            'remaining_seconds' => $remainingSeconds,
                            'restriction_reason' => 'Cache-based device restriction: ' . $key,
                            'restriction_type' => 'device',
                            'whatsapp_number' => setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX',
                        ];
                    } else {
                        Cache::forget($cacheKey);
                    }
                }
            }

            return ['restricted' => false];
            
        } catch (\Exception $e) {
            Log::error('Cache restriction check failed', [
                'error' => $e->getMessage(),
                'device_key' => $identifiers['device_key'] ?? 'unknown'
            ]);
            
            return ['restricted' => false];
        }
    }

    /**
     * Record order placement to prevent future orders from this device
     */
    public function recordOrderPlacement(string $phone, string $email = null): void
    {
        // Only record for guest users
        if (auth()->check()) {
            return;
        }

        try {
            $identifiers = $this->getDeviceIdentifiers($phone, $email);
            $expiresAt = time() + self::CACHE_DURATION;
            
            $cacheData = [
                'phone' => $identifiers['phone'],
                'last_phone' => $identifiers['phone'], // Track last used phone
                'email' => $email,
                'ip_address' => $identifiers['ip_address'],
                'fingerprint' => $identifiers['fingerprint'],
                'device_key' => $identifiers['device_key'],
                'created_at' => time(),
                'expires_at' => $expiresAt,
            ];

            // PRIMARY: Device-based restriction (prevents phone number changes)
            $deviceCacheKey = self::CACHE_PREFIX . 'device_' . $identifiers['device_key'];
            Cache::put($deviceCacheKey, $cacheData, self::CACHE_DURATION);

            // SECONDARY: Additional tracking for comprehensive coverage
            $additionalKeys = [
                'ip_' . $identifiers['ip_address'],
                'fingerprint_' . $identifiers['fingerprint'],
                'subnet_' . $identifiers['ip_subnet'],
            ];

            foreach ($additionalKeys as $key) {
                $fullKey = self::CACHE_PREFIX . $key;
                Cache::put($fullKey, $cacheData, self::CACHE_DURATION);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to record order placement', [
                'error' => $e->getMessage(),
                'phone' => $phone
            ]);
        }
    }

    /**
     * Clean up expired cache entries
     */
    public function cleanupExpiredEntries(): int
    {
        $cleanedCount = 0;
        
        try {
            $pattern = self::CACHE_PREFIX . '*';
            
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $keys = Cache::getRedis()->keys($pattern);
                
                foreach ($keys as $key) {
                    $data = Cache::get($key);
                    if ($data && isset($data['expires_at']) && $data['expires_at'] < time()) {
                        Cache::forget($key);
                        $cleanedCount++;
                    }
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Cache cleanup failed', ['error' => $e->getMessage()]);
        }
        
        return $cleanedCount;
    }

    /**
     * Get statistics about current restrictions
     */
    public function getRestrictionStats(): array
    {
        try {
            $cutoffTime = Carbon::now()->subMinutes(self::INTERVAL_MINUTES);
            
            $recentOrders = Order::where('created_at', '>=', $cutoffTime)
                ->whereNull('user_id')
                ->count();
                
            $uniqueDevices = Order::where('created_at', '>=', $cutoffTime)
                ->whereNull('user_id')
                ->select(\DB::raw('COUNT(DISTINCT CONCAT(ip_address, browser_fingerprint)) as unique_devices'))
                ->first();
                
            $uniquePhones = Order::where('created_at', '>=', $cutoffTime)
                ->whereNull('user_id')
                ->distinct('phone')
                ->count('phone');
                
            $phoneChanges = Order::where('created_at', '>=', $cutoffTime)
                ->whereNull('user_id')
                ->select('ip_address', 'browser_fingerprint', \DB::raw('COUNT(DISTINCT phone) as phone_count'))
                ->groupBy('ip_address', 'browser_fingerprint')
                ->having('phone_count', '>', 1)
                ->count();
                
            $cacheEntries = 0;
            try {
                if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                    $pattern = self::CACHE_PREFIX . 'device_*';
                    $keys = Cache::getRedis()->keys($pattern);
                    $cacheEntries = count($keys);
                }
            } catch (\Exception $e) {
                // Handle case where Redis is not available
            }

            return [
                'recent_guest_orders' => $recentOrders,
                'unique_devices_in_interval' => $uniqueDevices->unique_devices ?? 0,
                'unique_phones_in_interval' => $uniquePhones,
                'devices_with_phone_changes' => $phoneChanges,
                'active_device_restrictions' => $cacheEntries,
                'interval_minutes' => self::INTERVAL_MINUTES,
                'product_restriction_days' => self::PRODUCT_RESTRICTION_DAYS, // NEW
                'cache_duration_seconds' => self::CACHE_DURATION,
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to get restriction stats', ['error' => $e->getMessage()]);
            
            return [
                'recent_guest_orders' => 0,
                'unique_devices_in_interval' => 0,
                'unique_phones_in_interval' => 0,
                'devices_with_phone_changes' => 0,
                'active_device_restrictions' => 0,
                'interval_minutes' => self::INTERVAL_MINUTES,
                'product_restriction_days' => self::PRODUCT_RESTRICTION_DAYS, // NEW
                'cache_duration_seconds' => self::CACHE_DURATION,
                'error' => 'Stats unavailable',
            ];
        }
    }

    /**
     * Force remove restriction for a specific identifier
     */
    public function removeRestriction(string $identifier, string $type = 'device'): bool
    {
        try {
            $removed = false;
            
            if ($type === 'device') {
                // Remove device-specific restriction
                $deviceKey = self::CACHE_PREFIX . 'device_' . $identifier;
                if (Cache::has($deviceKey)) {
                    Cache::forget($deviceKey);
                    $removed = true;
                }
            } elseif ($type === 'phone') {
                // Find and remove device restrictions for orders with this phone
                $recentOrder = Order::where('phone', $this->normalizePhone($identifier))
                    ->whereNull('user_id')
                    ->latest()
                    ->first();
                    
                if ($recentOrder && $recentOrder->ip_address && $recentOrder->browser_fingerprint) {
                    $deviceKey = $this->generateDeviceKey($recentOrder->ip_address, $recentOrder->browser_fingerprint);
                    $cacheKey = self::CACHE_PREFIX . 'device_' . $deviceKey;
                    
                    if (Cache::has($cacheKey)) {
                        Cache::forget($cacheKey);
                        $removed = true;
                    }
                }
            } else {
                // Remove other types
                $cacheKey = self::CACHE_PREFIX . $type . '_' . $identifier;
                if (Cache::has($cacheKey)) {
                    Cache::forget($cacheKey);
                    $removed = true;
                }
            }
            
            if ($removed) {
                Log::info('Order restriction removed', [
                    'identifier' => $identifier,
                    'type' => $type,
                    'admin_id' => auth()->id() ?? 'system'
                ]);
            }
            
            return $removed;
            
        } catch (\Exception $e) {
            Log::error('Failed to remove restriction', [
                'identifier' => $identifier,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Check if device has been flagged for suspicious behavior
     */
    public function checkSpammerFlags(array $identifiers): array
    {
        try {
            $cutoffTime = Carbon::now()->subHours(24);
            
            // Count orders from this device in last 24 hours
            $deviceOrderCount = Order::where('created_at', '>=', $cutoffTime)
                ->whereNull('user_id')
                ->where('ip_address', $identifiers['ip_address'])
                ->where('browser_fingerprint', $identifiers['fingerprint'])
                ->count();
                
            // Count different phone numbers used by this device
            $phoneVariations = Order::where('created_at', '>=', $cutoffTime)
                ->whereNull('user_id')
                ->where('ip_address', $identifiers['ip_address'])
                ->where('browser_fingerprint', $identifiers['fingerprint'])
                ->distinct('phone')
                ->count('phone');

            // Count rapid orders (within 30 minutes)
            $rapidOrders = Order::where('created_at', '>=', Carbon::now()->subMinutes(30))
                ->whereNull('user_id')
                ->where('ip_address', $identifiers['ip_address'])
                ->where('browser_fingerprint', $identifiers['fingerprint'])
                ->count();

            $flags = [];
            
            // High device activity
            if ($deviceOrderCount > 10) {
                $flags[] = 'high_device_activity';
            }
            
            // Multiple phone numbers from same device (clear abuse)
            if ($phoneVariations > 3) {
                $flags[] = 'multiple_phone_numbers';
            }
            
            // Rapid ordering pattern
            if ($rapidOrders > 5) {
                $flags[] = 'rapid_ordering';
            }

            return [
                'is_flagged' => !empty($flags),
                'flags' => $flags,
                'risk_score' => $this->calculateRiskScore($deviceOrderCount, $phoneVariations, $rapidOrders),
                'device_order_count' => $deviceOrderCount,
                'phone_variations' => $phoneVariations,
                'rapid_orders' => $rapidOrders,
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to check spammer flags', [
                'error' => $e->getMessage(),
                'device_key' => $identifiers['device_key'] ?? 'unknown'
            ]);
            
            return [
                'is_flagged' => false,
                'flags' => [],
                'risk_score' => 0,
                'error' => 'Check failed',
            ];
        }
    }

    /**
     * Calculate risk score based on device behavior
     */
    private function calculateRiskScore(int $deviceCount, int $phoneVariations, int $rapidOrders): int
    {
        $score = 0;
        
        // Device activity score (legitimate users might order multiple times)
        $score += min($deviceCount * 2, 20);
        
        // Phone variations score (strong indicator of abuse)
        $score += min($phoneVariations * 15, 60);
        
        // Rapid ordering score (suspicious behavior)
        $score += min($rapidOrders * 4, 20);
        
        return min($score, 100); // Cap at 100
    }

    /**
     * Get active restrictions for monitoring
     */
    public function getActiveRestrictions(): array
    {
        $restrictions = [];
        
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $pattern = self::CACHE_PREFIX . 'device_*';
                $keys = Cache::getRedis()->keys($pattern);
                
                foreach ($keys as $key) {
                    $data = Cache::get($key);
                    if ($data && isset($data['expires_at']) && $data['expires_at'] > time()) {
                        $restrictions[] = [
                            'key' => str_replace(self::CACHE_PREFIX, '', $key),
                            'type' => 'device_restriction',
                            'expires_at' => $data['expires_at'],
                            'remaining_seconds' => $data['expires_at'] - time(),
                            'current_phone' => $data['phone'] ?? 'unknown',
                            'last_phone' => $data['last_phone'] ?? 'unknown',
                            'ip_address' => $data['ip_address'] ?? 'unknown',
                            'device_key' => $data['device_key'] ?? 'unknown',
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to get active restrictions', ['error' => $e->getMessage()]);
        }
        
        return $restrictions;
    }
}