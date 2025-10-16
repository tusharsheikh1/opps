<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderStatusUpdateSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;
    protected $oldStatus;
    protected $newStatus;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @param int $orderId
     * @param int $oldStatus
     * @param int $newStatus
     */
    public function __construct($orderId, $oldStatus, $newStatus)
    {
        $this->orderId = $orderId;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $order = Order::find($this->orderId);
            
            if (!$order) {
                Log::error("Order not found for SMS status update: {$this->orderId}");
                return;
            }

            // Check if phone number exists
            if (empty($order->phone)) {
                Log::warning("No phone number found for order status update: {$order->invoice}");
                return;
            }

            // Don't send SMS for certain transitions
            $skipStatuses = [
                0 => [1], // Don't send SMS when going from pending to processing (covered by confirmation SMS)
            ];

            if (isset($skipStatuses[$this->oldStatus]) && 
                in_array($this->newStatus, $skipStatuses[$this->oldStatus])) {
                Log::info("Skipping SMS for status transition {$this->oldStatus} -> {$this->newStatus} for order: {$order->invoice}");
                return;
            }

            $smsService = new SmsService();
            $message = $smsService->getStatusUpdateMessage($order, $this->oldStatus, $this->newStatus);
            
            $sent = $smsService->sendSms(
                $order->phone, 
                $message, 
                'status_update', 
                $order->id
            );
            
            if ($sent) {
                Log::info("Order status update SMS sent for order: {$order->invoice} (Status: {$this->oldStatus} -> {$this->newStatus})");
            } else {
                Log::error("Failed to send order status update SMS for order: {$order->invoice}");
            }

        } catch (\Exception $e) {
            Log::error("Error sending order status update SMS: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error("Order status update SMS job failed for order ID {$this->orderId}: " . $exception->getMessage());
        
        // Log additional context
        try {
            $order = Order::find($this->orderId);
            if ($order) {
                Log::error("Failed to send status update SMS for order {$order->invoice} (Status: {$this->oldStatus} -> {$this->newStatus}) after {$this->tries} attempts");
            }
        } catch (\Exception $e) {
            Log::error("Error in failed job handler: " . $e->getMessage());
        }
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return int
     */
    public function backoff()
    {
        return [30, 60, 120]; // Wait 30s, then 60s, then 120s before retrying
    }
}