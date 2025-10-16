<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\RequestException;
use Exception;

class BdCourierService
{
    private $apiUrl = 'https://bdcourier.com/api/pro/courier-check'; // Fixed URL - added 'pro'
    private $apiKey;
    private $maxRetries = 3;
    private $baseDelay = 0.3; // seconds
    private $backoffFactor = 2;
    private $timeout = 20; // increased timeout

    public function __construct()
    {
        $this->apiKey = env('BD_COURIER_API_KEY');
    }

    /**
     * Get courier history for a phone number
     *
     * @param string $phone
     * @return array
     */
    public function getCourierHistory($phone)
    {
        try {
            // Clean phone number
            $cleanPhone = $this->cleanPhoneNumber($phone);
            
            // Cache key for 1 hour to avoid frequent API calls
            $cacheKey = 'bd_courier_' . $cleanPhone;
            
            return Cache::remember($cacheKey, 3600, function () use ($cleanPhone) {
                return $this->fetchCourierDataWithRetry($cleanPhone);
            });

        } catch (Exception $e) {
            Log::error('BD Courier API Error: ' . $e->getMessage());
            
            // Return demo data for testing (remove this in production)
            if (env('APP_ENV') === 'local' && env('BD_COURIER_DEMO_MODE', false)) {
                return $this->getDemoData();
            }
            
            return [
                'success' => false,
                'message' => 'Unable to fetch courier data',
                'data' => null
            ];
        }
    }

    /**
     * Fetch data from BD Courier API with retry logic
     *
     * @param string $phone
     * @return array
     */
    private function fetchCourierDataWithRetry($phone)
    {
        if (empty($this->apiKey)) {
            Log::warning('BD Courier API key not configured');
            return [
                'success' => false,
                'message' => 'BD Courier API key not configured',
                'data' => null
            ];
        }

        $lastError = null;

        for ($attempt = 0; $attempt <= $this->maxRetries; $attempt++) {
            try {
                // Add delay for retry attempts with jitter
                if ($attempt > 0) {
                    $delay = $this->baseDelay * pow($this->backoffFactor, $attempt) + (mt_rand(100, 500) / 1000);
                    Log::info("BD Courier API retry {$attempt} for {$phone} after {$delay}s delay");
                    usleep($delay * 1000000); // Convert to microseconds
                }

                Log::info('BD Courier API request attempt ' . ($attempt + 1), [
                    'phone' => $phone,
                    'api_url' => $this->apiUrl,
                    'api_key_length' => strlen($this->apiKey)
                ]);

                // Make the API request - matching Flask exactly
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'User-Agent' => 'BD-Courier-Checker-Laravel/1.0',
                    ])
                    ->retry(3, 1000) // Laravel's built-in retry
                    ->post($this->apiUrl, [
                        'phone' => $phone  // Send as JSON body, not query params
                    ]);

                Log::info('BD Courier API response attempt ' . ($attempt + 1), [
                    'status' => $response->status(),
                    'headers' => $response->headers(),
                    'body_preview' => substr($response->body(), 0, 200),
                    'phone' => $phone
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    Log::info('BD Courier API success for ' . $phone, [
                        'attempt' => $attempt + 1,
                        'has_courier_data' => isset($data['courierData'])
                    ]);
                    
                    return [
                        'success' => true,
                        'message' => 'Data retrieved successfully',
                        'data' => $this->formatCourierData($data),
                        'raw_response' => $data // Keep raw for debugging
                    ];
                }

                if ($response->status() === 429) {
                    $lastError = 'Rate limit exceeded';
                    Log::warning("BD Courier API rate limit hit for {$phone} (attempt " . ($attempt + 1) . ")");
                    
                    // Longer delay for rate limits
                    if ($attempt < $this->maxRetries) {
                        $rateLimitDelay = $this->baseDelay * pow(3, $attempt) + (mt_rand(1000, 3000) / 1000);
                        usleep($rateLimitDelay * 1000000);
                    }
                    continue;
                }

                $lastError = "API Error {$response->status()}: " . substr($response->body(), 0, 100);
                Log::warning("BD Courier API error for {$phone} (attempt " . ($attempt + 1) . ")", [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

            } catch (RequestException $e) {
                $lastError = 'Request timeout or connection error: ' . $e->getMessage();
                Log::warning("BD Courier API RequestException for {$phone} (attempt " . ($attempt + 1) . "): " . $e->getMessage());
                
            } catch (Exception $e) {
                $lastError = 'Unexpected error: ' . $e->getMessage();
                Log::error("BD Courier API Exception for {$phone} (attempt " . ($attempt + 1) . "): " . $e->getMessage());
                break; // Don't retry on unexpected errors
            }
        }

        Log::error("BD Courier API all attempts failed for {$phone}: {$lastError}");

        return [
            'success' => false,
            'message' => "API request failed after {$this->maxRetries} retries: {$lastError}",
            'data' => null
        ];
    }

    /**
     * Format courier data for display
     *
     * @param array $data
     * @return array
     */
    private function formatCourierData($data)
    {
        // Handle the expected response format from BD Courier API
        if (!isset($data['courierData']) || !is_array($data['courierData'])) {
            Log::warning('BD Courier API response missing courierData', [
                'response_keys' => array_keys($data ?? []),
                'response' => $data
            ]);
            
            return [
                'couriers' => [],
                'total_orders' => 0,
                'total_successful' => 0,
                'total_cancelled' => 0,
                'success_rate' => 0
            ];
        }

        $courierData = $data['courierData'];
        $couriers = [];
        $totalOrders = 0;
        $totalSuccessful = 0;
        $totalCancelled = 0;

        foreach ($courierData as $courierName => $stats) {
            // Skip summary entries
            if (strtolower($courierName) === 'summary') {
                continue;
            }

            $total = (int)($stats['total_parcel'] ?? 0);
            $successful = (int)($stats['success_parcel'] ?? 0);
            $cancelled = (int)($stats['cancelled_parcel'] ?? 0);

            $courierInfo = [
                'name' => ucfirst($courierName),
                'total' => $total,
                'successful' => $successful,
                'cancelled' => $cancelled,
                'success_rate' => $total > 0 ? round(($successful / $total) * 100, 2) : 0
            ];

            $couriers[] = $courierInfo;
            $totalOrders += $total;
            $totalSuccessful += $successful;
            $totalCancelled += $cancelled;
        }

        $overallSuccessRate = $totalOrders > 0 ? round(($totalSuccessful / $totalOrders) * 100, 2) : 0;

        return [
            'couriers' => $couriers,
            'total_orders' => $totalOrders,
            'total_successful' => $totalSuccessful,
            'total_cancelled' => $totalCancelled,
            'success_rate' => $overallSuccessRate
        ];
    }

    /**
     * Clean phone number (same as Flask version)
     *
     * @param string $phone
     * @return string
     */
    private function cleanPhoneNumber($phone)
    {
        // Remove all non-digits except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Handle Bangladesh numbers
        if (strlen($phone) == 11 && substr($phone, 0, 2) == '01') {
            return $phone;
        }
        
        if (strlen($phone) == 13 && substr($phone, 0, 4) == '+880') {
            return '0' . substr($phone, 4);
        }
        
        if (strlen($phone) == 12 && substr($phone, 0, 3) == '880') {
            return '0' . substr($phone, 3);
        }
        
        return $phone;
    }

    /**
     * Demo data for testing (same as Flask version)
     */
    private function getDemoData()
    {
        return [
            'success' => true,
            'message' => 'Demo data (testing mode)',
            'data' => [
                'couriers' => [
                    ['name' => 'Pathao', 'total' => 10, 'successful' => 8, 'cancelled' => 2, 'success_rate' => 80.0],
                    ['name' => 'SteadFast', 'total' => 15, 'successful' => 12, 'cancelled' => 3, 'success_rate' => 80.0],
                    ['name' => 'RedX', 'total' => 5, 'successful' => 4, 'cancelled' => 1, 'success_rate' => 80.0],
                ],
                'total_orders' => 30,
                'total_successful' => 24,
                'total_cancelled' => 6,
                'success_rate' => 80.0
            ]
        ];
    }

    /**
     * Get risk assessment based on courier history
     *
     * @param string $phone
     * @return array
     */
    public function getRiskAssessment($phone)
    {
        $courierData = $this->getCourierHistory($phone);
        
        if (!$courierData['success'] || !$courierData['data']) {
            return [
                'risk_level' => 'unknown',
                'recommendation' => 'No courier data available - ' . ($courierData['message'] ?? 'Unknown error'),
                'color' => 'secondary'
            ];
        }

        $data = $courierData['data'];
        $successRate = $data['success_rate'];
        $totalOrders = $data['total_orders'];

        // Risk assessment logic
        if ($totalOrders == 0) {
            return [
                'risk_level' => 'new',
                'recommendation' => 'New customer - proceed with caution',
                'color' => 'info'
            ];
        }

        if ($successRate >= 85) {
            return [
                'risk_level' => 'low',
                'recommendation' => 'Reliable customer - safe to proceed',
                'color' => 'success'
            ];
        }

        if ($successRate >= 70) {
            return [
                'risk_level' => 'medium',
                'recommendation' => 'Moderate risk - consider requiring advance payment',
                'color' => 'warning'
            ];
        }

        return [
            'risk_level' => 'high',
            'recommendation' => 'High risk - avoid COD, require advance payment',
            'color' => 'danger'
        ];
    }

    /**
     * Test the API connection
     *
     * @return array
     */
    public function testConnection()
    {
        return $this->fetchCourierDataWithRetry('01700000000'); // Test with a dummy number
    }
}