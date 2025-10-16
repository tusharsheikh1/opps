<?php

namespace App\Http\Controllers\Admin\Ecommerce\OrderEssential;

use App\Models\DeviceId;
use Illuminate\Support\Facades\Log;

class OrderNotificationService
{
    private $SERVER_API_KEY = 'AAAA9Ek9F7U:APA91bEtCumEi8v_NmoBW6rQbm48iVNB4ctTguXS5G33Mj1FEmX48zlNYEHLWOd6WfLkPD3ByKZQPrlJVl0swAd1ZxFWPMHWOdPWXD30sGCOvu_xIV7nTW9PC6cGiL6n3FOBHl1bavE';
    
    /**
     * Send notification to user
     *
     * @param string $status
     * @param string $invoice
     * @param int $id
     * @return void
     */
    public function sendNotification($status, $invoice, $id)
    {
        try {
            $firebaseToken = DeviceId::where('user_id', $id)->pluck('device_id')->all();
            
            if (empty($firebaseToken)) {
                Log::info("No device tokens found for user {$id}");
                return;
            }
            
            $body = $this->getNotificationBody($status);
            $title = 'Invoice Is ' . $invoice . ' From ' . 'shopasiabd.com';
            
            $data = [
                "registration_ids" => $firebaseToken,
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                    "content_available" => true,
                    "priority" => "high",
                ]
            ];
            
            $this->sendFirebaseNotification($data);
            
            Log::info("Notification sent successfully", [
                'user_id' => $id,
                'status' => $status,
                'invoice' => $invoice,
                'token_count' => count($firebaseToken)
            ]);
            
        } catch (\Exception $e) {
            Log::error("Failed to send notification: " . $e->getMessage(), [
                'user_id' => $id,
                'status' => $status,
                'invoice' => $invoice
            ]);
        }
    }
    
    /**
     * Get notification body text based on status
     *
     * @param string $status
     * @return string
     */
    private function getNotificationBody($status)
    {
        $messages = [
            'cancel' => 'Your Order is Cancel By Admin',
            'pross' => 'Your order is in processing',
            'delevery' => 'Order Arrived and Picked Up! Thank you for your Purchase',
            'shipping' => 'Your order has been delivered by courier',
            'pertials' => 'Your partials payment success',
            'refund' => 'Your order refund has been processed',
            'return_accept' => 'Your return request has been accepted',
            'return_complete' => 'Your return has been completed',
            'pending' => 'Your order status has been changed to pending'
        ];
        
        return $messages[$status] ?? 'Your order status has been updated';
    }
    
    /**
     * Send Firebase Cloud Messaging notification
     *
     * @param array $data
     * @return bool
     */
    private function sendFirebaseNotification($data)
    {
        $dataString = json_encode($data);
        
        $headers = [
            'Authorization: key=' . $this->SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            Log::error("Firebase notification cURL error: " . $error);
            return false;
        }
        
        if ($httpCode !== 200) {
            Log::error("Firebase notification failed", [
                'http_code' => $httpCode,
                'response' => $response
            ]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Send bulk notifications to multiple users
     *
     * @param array $notifications
     * @return array
     */
    public function sendBulkNotifications($notifications)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        foreach ($notifications as $notification) {
            try {
                $this->sendNotification(
                    $notification['status'], 
                    $notification['invoice'], 
                    $notification['user_id']
                );
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'user_id' => $notification['user_id'],
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }
    
    /**
     * Send custom notification with custom title and body
     *
     * @param int $userId
     * @param string $title
     * @param string $body
     * @return bool
     */
    public function sendCustomNotification($userId, $title, $body)
    {
        try {
            $firebaseToken = DeviceId::where('user_id', $userId)->pluck('device_id')->all();
            
            if (empty($firebaseToken)) {
                Log::info("No device tokens found for user {$userId}");
                return false;
            }
            
            $data = [
                "registration_ids" => $firebaseToken,
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                    "content_available" => true,
                    "priority" => "high",
                ]
            ];
            
            return $this->sendFirebaseNotification($data);
            
        } catch (\Exception $e) {
            Log::error("Failed to send custom notification: " . $e->getMessage(), [
                'user_id' => $userId,
                'title' => $title
            ]);
            return false;
        }
    }
    
    /**
     * Send notification to multiple users with same message
     *
     * @param array $userIds
     * @param string $title
     * @param string $body
     * @return array
     */
    public function sendBroadcastNotification($userIds, $title, $body)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        foreach ($userIds as $userId) {
            if ($this->sendCustomNotification($userId, $title, $body)) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = "Failed to send to user {$userId}";
            }
        }
        
        return $results;
    }
}