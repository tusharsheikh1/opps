<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'refer_id',
        'order_id',
        'invoice',
        'first_name',
        'last_name',
        'company_name',
        'country',
        'address',
        'town',
        'district',
        'thana',
        'post_code',
        'phone',
        'email',
        'shipping_method',
        'shipping_charge',
        'single_charge',
        'payment_method',
        'mobile_number',
        'transaction_id',
        'bank_name',
        'account_number',
        'holder_name',
        'branch_name',
        'routing_number',
        'coupon_code',
        'subtotal',
        'discount',
        'is_pre',
        'total',
        'cart_type',
        'status',
        'pay_staus',
        'pay_date',
        'point',
        'refer_bonus',
        'meet_time',
        'admin_notes',
        'refund_amount',
        'refund_method',
        // Tracking fields
        'ip_address',
        'ip_subnet',
        'browser_fingerprint',
        'user_agent',
        'device_info',
        'order_placed_at',
    ];

    protected $casts = [
        'device_info' => 'array',
        'order_placed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'shipping_charge' => 'decimal:2',
        'refund_amount' => 'decimal:2',
    ];

    /**
     * Boot method to automatically capture tracking data
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            // Only capture for guest orders
            if (!auth()->check()) {
                $order->captureTrackingData();
            }
        });
    }

    /**
     * Capture tracking data for the order
     */
    public function captureTrackingData()
    {
        if (class_exists(Agent::class)) {
            $agent = new Agent();
            
            $this->ip_address = $this->getClientIpAddress();
            $this->ip_subnet = $this->getIpSubnet($this->ip_address);
            $this->browser_fingerprint = $this->generateBrowserFingerprint();
            $this->user_agent = $agent->getUserAgent();
            $this->device_info = [
                'browser' => $agent->browser(),
                'browser_version' => $agent->version($agent->browser()),
                'platform' => $agent->platform(),
                'platform_version' => $agent->version($agent->platform()),
                'device' => $agent->device(),
                'is_mobile' => $agent->isMobile(),
                'is_tablet' => $agent->isTablet(),
                'is_desktop' => $agent->isDesktop(),
                'languages' => request()->header('Accept-Language', ''),
                'screen_resolution' => request()->header('X-Screen-Resolution', ''),
                'timezone' => request()->header('X-Timezone', ''),
            ];
            $this->order_placed_at = now();
        }
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
     * Get IP subnet
     */
    private function getIpSubnet(string $ip): string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            return implode('.', array_slice($parts, 0, 3)) . '.0';
        }
        
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            return implode(':', array_slice($parts, 0, 4)) . '::';
        }
        
        return $ip;
    }

    /**
     * Generate browser fingerprint
     */
    private function generateBrowserFingerprint(): string
    {
        if (class_exists(Agent::class)) {
            $agent = new Agent();
            
            $fingerprint = collect([
                'ip' => $this->getClientIpAddress(),
                'user_agent' => $agent->getUserAgent(),
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
                'device' => $agent->device(),
                'languages' => request()->header('Accept-Language', ''),
                'screen_resolution' => request()->header('X-Screen-Resolution', ''),
                'timezone' => request()->header('X-Timezone', ''),
                'session_id' => session()->getId(),
            ])->filter()->implode('|');

            return hash('sha256', $fingerprint);
        }
        
        return '';
    }

    // Relationships
    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function smsLogs()
    {
        return $this->hasMany(SmsLog::class, 'order_id', 'id');
    }

    // Scopes for querying
    public function scopeGuestOrders($query)
    {
        return $query->whereNull('user_id');
    }

    public function scopeRecentOrders($query, $minutes = 5)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    public function scopeByIpAddress($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    public function scopeByFingerprint($query, $fingerprint)
    {
        return $query->where('browser_fingerprint', $fingerprint);
    }

    public function scopeByIpSubnet($query, $subnet)
    {
        return $query->where('ip_subnet', $subnet);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPhone($query, $phone)
    {
        return $query->where('phone', $phone);
    }

    // Helper methods
    public function isGuestOrder(): bool
    {
        return is_null($this->user_id);
    }

    public function getDeviceTypeAttribute(): string
    {
        if (!$this->device_info) {
            return 'unknown';
        }

        if ($this->device_info['is_mobile'] ?? false) {
            return 'mobile';
        } elseif ($this->device_info['is_tablet'] ?? false) {
            return 'tablet';
        } elseif ($this->device_info['is_desktop'] ?? false) {
            return 'desktop';
        }

        return 'unknown';
    }

    public function getBrowserInfoAttribute(): string
    {
        if (!$this->device_info) {
            return 'unknown';
        }

        $browser = $this->device_info['browser'] ?? 'unknown';
        $version = $this->device_info['browser_version'] ?? '';
        
        return $browser . ($version ? ' ' . $version : '');
    }

    public function getPlatformInfoAttribute(): string
    {
        if (!$this->device_info) {
            return 'unknown';
        }

        $platform = $this->device_info['platform'] ?? 'unknown';
        $version = $this->device_info['platform_version'] ?? '';
        
        return $platform . ($version ? ' ' . $version : '');
    }

    public function getStatusTextAttribute(): string
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

        return $statusMap[$this->status] ?? 'Unknown';
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getFullAddressAttribute(): string
    {
        $address = $this->address;
        if ($this->town) $address .= ', ' . $this->town;
        if ($this->district) $address .= ', ' . $this->district;
        if ($this->post_code) $address .= ' ' . $this->post_code;
        
        return $address;
    }
}