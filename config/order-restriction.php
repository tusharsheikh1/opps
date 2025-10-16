<?php

return [
    'interval_minutes' => env('ORDER_INTERVAL_MINUTES', 5),
    'cache_duration' => env('ORDER_RESTRICTION_CACHE_DURATION', 300),
    'cleanup_after_days' => env('ORDER_CLEANUP_DAYS', 30),
    
    'risk_thresholds' => [
        'high' => env('SPAM_RISK_HIGH_THRESHOLD', 70),
        'medium' => env('SPAM_RISK_MEDIUM_THRESHOLD', 40),
    ],
    
    'tracking' => [
        'device_fingerprint' => env('TRACK_DEVICE_FINGERPRINT', true),
        'canvas_fingerprint' => env('TRACK_CANVAS_FINGERPRINT', true),
        'audio_fingerprint' => env('TRACK_AUDIO_FINGERPRINT', true),
        'ip_subnet' => env('TRACK_IP_SUBNET', true),
    ],
    
    'admin' => [
        'alerts_enabled' => env('ORDER_RESTRICTION_ADMIN_ALERTS', true),
        'max_manual_restriction_hours' => 24,
    ],
];