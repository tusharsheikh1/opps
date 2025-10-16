<?php

namespace App\Services;

use Exception;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class SmsService
{
    /**
     * Send SMS using the configured SMS API
     *
     * @param string $phone
     * @param string $message
     * @param string $type
     * @param int|null $orderId
     * @param int|null $userId
     * @return bool
     */
    public function sendSms($phone, $message, $type = 'custom', $orderId = null, $userId = null)
    {
        // Check if SMS is configured and enabled
        if (!setting('sms_config_status') || setting('sms_config_status') != 1) {
            Log::info('SMS service is disabled');
            $this->logSms($phone, $message, $type, 'failed', 'SMS service is disabled', $orderId, $userId);
            return false;
        }

        // Create SMS log entry
        $smsLog = $this->logSms($phone, $message, $type, 'pending', null, $orderId, $userId);

        try {
            $apiUrl = setting('SMS_API_URL');
            $apiKey = setting('SMS_API_KEY');
            $senderId = setting('SMS_API_SENDER_ID');

            if (empty($apiUrl) || empty($apiKey) || empty($senderId)) {
                Log::error('SMS configuration is incomplete');
                $this->updateSmsLog($smsLog, 'failed', 'SMS configuration is incomplete');
                return false;
            }

            // Format phone number (ensure it starts with country code)
            $formattedPhone = $this->formatPhoneNumber($phone);

            Log::info("Attempting to send SMS to: {$formattedPhone}");
            Log::info("SMS Message: " . substr($message, 0, 100) . '...');

            // BulkSMSBD API expects GET request with URL parameters
            $smsUrl = $apiUrl . '?' . http_build_query([
                'api_key' => $apiKey,
                'type' => 'text',
                'number' => $formattedPhone,
                'senderid' => $senderId,
                'message' => $message,
            ]);

            Log::info("SMS API URL: " . $smsUrl);

            // Send SMS via HTTP GET request (as per BulkSMSBD documentation)
            $response = Http::timeout(30)->get($smsUrl);

            Log::info("SMS API Response Status: " . $response->status());
            Log::info("SMS API Response Body: " . $response->body());

            if ($response->successful()) {
                $responseBody = $response->body();
                
                // Check if response contains success code (202)
                if (strpos($responseBody, '202') !== false || strpos($responseBody, 'SMS Submitted Successfully') !== false) {
                    Log::info("SMS sent successfully to {$formattedPhone}");
                    $this->updateSmsLog($smsLog, 'sent', $responseBody);
                    return true;
                } else {
                    // Parse error codes and provide meaningful messages
                    $errorMessage = $this->parseErrorResponse($responseBody);
                    Log::error("SMS sending failed to {$formattedPhone}. Error: " . $errorMessage);
                    $this->updateSmsLog($smsLog, 'failed', $errorMessage);
                    return false;
                }
            } else {
                $errorResponse = "HTTP Error: " . $response->status() . " - " . $response->body();
                Log::error("Failed to send SMS to {$formattedPhone}. Response: " . $errorResponse);
                $this->updateSmsLog($smsLog, 'failed', $errorResponse);
                return false;
            }

        } catch (Exception $e) {
            $errorMessage = "SMS sending failed: " . $e->getMessage();
            Log::error($errorMessage);
            $this->updateSmsLog($smsLog, 'failed', $errorMessage);
            return false;
        }
    }

    /**
     * Parse error response codes from BulkSMSBD API
     *
     * @param string $response
     * @return string
     */
    private function parseErrorResponse($response)
    {
        $errorCodes = [
            '1001' => 'Invalid Number',
            '1002' => 'Sender ID not correct/Sender ID is disabled',
            '1003' => 'Please Required all fields/Contact Your System Administrator',
            '1005' => 'Internal Error',
            '1006' => 'Balance Validity Not Available',
            '1007' => 'Balance Insufficient',
            '1011' => 'User ID not found',
            '1012' => 'Masking SMS must be sent in Bengali',
            '1013' => 'Sender ID has not found Gateway by api key',
            '1014' => 'Sender Type Name not found using this sender by api key',
            '1015' => 'Sender ID has not found Any Valid Gateway by api key',
            '1016' => 'Sender Type Name Active Price Info not found by this sender id',
            '1017' => 'Sender Type Name Price Info not found by this sender id',
            '1018' => 'The Owner of this (username) Account is disabled',
            '1019' => 'The (sender type name) Price of this (username) Account is disabled',
            '1020' => 'The parent of this account is not found',
            '1021' => 'The parent active (sender type name) price of this account is not found',
            '1031' => 'Your Account Not Verified, Please Contact Administrator',
            '1032' => 'IP Not whitelisted',
        ];

        foreach ($errorCodes as $code => $message) {
            if (strpos($response, $code) !== false) {
                return "Error {$code}: {$message}";
            }
        }

        return "Unknown error: " . $response;
    }

    /**
     * Format phone number for SMS API
     *
     * @param string $phone
     * @return string
     */
    private function formatPhoneNumber($phone)
    {
        // Remove any non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add country code if missing (assuming Bangladesh +880)
        if (strlen($phone) == 11 && substr($phone, 0, 1) == '0') {
            $phone = '880' . substr($phone, 1);
        } elseif (strlen($phone) == 10) {
            $phone = '880' . $phone;
        }
        
        return $phone;
    }

    /**
     * Get current SMS balance from API
     *
     * @return array
     */
    public function getBalance()
    {
        try {
            $apiKey = setting('SMS_API_KEY');
            
            if (empty($apiKey)) {
                return ['success' => false, 'message' => 'API Key not configured'];
            }

            $balanceUrl = "http://bulksmsbd.net/api/getBalanceApi?api_key=" . $apiKey;
            
            $response = Http::timeout(10)->get($balanceUrl);
            
            if ($response->successful()) {
                $balance = $response->body();
                return ['success' => true, 'balance' => $balance];
            } else {
                return ['success' => false, 'message' => 'Failed to fetch balance'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Generate order confirmation SMS message
     *
     * @param object $order
     * @return string
     */
    public function getOrderConfirmationMessage($order)
    {
        $currencyIcon = setting('CURRENCY_ICON') ?? '৳';
        $shopName = setting('APP_NAME') ?? 'Our Shop';
        
        return "Order Confirmed! Invoice: {$order->invoice}. "
             . "Total: {$currencyIcon}{$order->total}. "
             . "Payment: {$order->payment_method}. "
             . "We'll update you on delivery. Thank you for shopping with {$shopName}!";
    }

    /**
     * Generate order status update SMS message
     *
     * @param object $order
     * @param string $oldStatus
     * @param string $newStatus
     * @return string
     */
    public function getStatusUpdateMessage($order, $oldStatus, $newStatus)
    {
        $statusMessages = [
            0 => 'Pending',
            1 => 'Processing',
            2 => 'Canceled',
            3 => 'Delivered',
            4 => 'Shipping',
            5 => 'Refund',
            6 => 'Return Requested',
            7 => 'Return Accepted',
            8 => 'Returned'
        ];

        $statusText = $statusMessages[$newStatus] ?? 'Updated';
        $shopName = setting('APP_NAME') ?? 'Our Shop';
        
        $message = "Order Update from {$shopName}! Invoice: {$order->invoice} status changed to: {$statusText}.";
        
        // Add specific messages for certain statuses
        switch ($newStatus) {
            case 1:
                $message .= " Your order is being processed and will be ready soon.";
                break;
            case 2:
                $message .= " Your order has been canceled. Contact us for more info.";
                break;
            case 3:
                $message .= " Your order has been delivered! Thank you for shopping with us.";
                break;
            case 4:
                $message .= " Your order is out for delivery. Please keep your phone available.";
                break;
            case 5:
                $message .= " Refund has been initiated for your order.";
                break;
            case 6:
                $message .= " Your return request has been received and is being processed.";
                break;
            case 7:
                $message .= " Your return request has been accepted. Please prepare the items for return.";
                break;
            case 8:
                $message .= " Your return has been completed successfully.";
                break;
        }
        
        return $message;
    }

    /**
     * Log SMS to database
     *
     * @param string $phone
     * @param string $message
     * @param string $type
     * @param string $status
     * @param string|null $response
     * @param int|null $orderId
     * @param int|null $userId
     * @return SmsLog
     */
    private function logSms($phone, $message, $type, $status, $response = null, $orderId = null, $userId = null)
    {
        // If userId is not provided, try to get the current authenticated user
        if (!$userId && Auth::check()) {
            $userId = Auth::id();
        }

        // If still no user (like console commands), try to get first admin user
        if (!$userId && app()->runningInConsole()) {
            $adminUser = User::where('role_id', 1)->first();
            $userId = $adminUser ? $adminUser->id : null;
        }

        return SmsLog::create([
            'order_id' => $orderId,
            'user_id' => $userId, // This can be null for console commands
            'phone' => $phone,
            'message' => $message,
            'type' => $type,
            'status' => $status,
            'response' => $response,
            'sent_at' => $status === 'sent' ? now() : null,
        ]);
    }

    /**
     * Update SMS log status
     *
     * @param SmsLog $smsLog
     * @param string $status
     * @param string|null $response
     * @return bool
     */
    private function updateSmsLog($smsLog, $status, $response = null)
    {
        if (!$smsLog) {
            return false;
        }

        return $smsLog->update([
            'status' => $status,
            'response' => $response,
            'sent_at' => $status === 'sent' ? now() : $smsLog->sent_at,
        ]);
    }

    /**
     * Get SMS statistics
     *
     * @param int|null $orderId
     * @return array
     */
    public function getSmsStats($orderId = null)
    {
        $query = SmsLog::query();
        
        if ($orderId) {
            $query->where('order_id', $orderId);
        }

        return [
            'total' => $query->count(),
            'sent' => $query->where('status', 'sent')->count(),
            'failed' => $query->where('status', 'failed')->count(),
            'pending' => $query->where('status', 'pending')->count(),
        ];
    }

    /**
     * Get recent SMS logs
     *
     * @param int $limit
     * @param int|null $orderId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentSmsLogs($limit = 10, $orderId = null)
    {
        $query = SmsLog::with(['order', 'user'])
                       ->orderBy('created_at', 'desc')
                       ->limit($limit);
        
        if ($orderId) {
            $query->where('order_id', $orderId);
        }

        return $query->get();
    }

    /**
     * Test SMS configuration
     *
     * @param string $testPhone
     * @return array
     */
    public function testSmsConfiguration($testPhone)
    {
        $testMessage = "Test SMS from " . (setting('APP_NAME') ?? 'Your Website') . ". SMS service is working correctly!";
        
        $result = $this->sendSms($testPhone, $testMessage, 'custom');
        
        return [
            'success' => $result,
            'message' => $result ? 'Test SMS sent successfully!' : 'Failed to send test SMS. Please check your configuration.',
        ];
    }

    /**
     * Send custom SMS with variable replacement
     *
     * @param string $phone
     * @param string $message
     * @param array $variables
     * @param string $type
     * @param int|null $orderId
     * @param int|null $userId
     * @return bool
     */
    public function sendCustomSms($phone, $message, $variables = [], $type = 'custom', $orderId = null, $userId = null)
    {
        // Replace variables in message
        foreach ($variables as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        return $this->sendSms($phone, $message, $type, $orderId, $userId);
    }
}