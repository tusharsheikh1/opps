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

class SendOrderConfirmationSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

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
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
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
                Log::error("Order not found for SMS confirmation: {$this->orderId}");
                return;
            }

            // Don't send confirmation SMS for canceled orders
            if ($order->status == 2) {
                Log::info("Skipping SMS for canceled order: {$order->invoice}");
                return;
            }

            // Check if phone number exists
            if (empty($order->phone)) {
                Log::warning("No phone number found for order: {$order->invoice}");
                return;
            }

            $smsService = new SmsService();
            $message = $smsService->getOrderConfirmationMessage($order);
            
            $sent = $smsService->sendSms(
                $order->phone, 
                $message, 
                'order_confirmation', 
                $order->id
            );
            
            if ($sent) {
                Log::info("Order confirmation SMS sent for order: {$order->invoice}");
            } else {
                Log::error("Failed to send order confirmation SMS for order: {$order->invoice}");
            }

        } catch (\Exception $e) {
            Log::error("Error sending order confirmation SMS: " . $e->getMessage());
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
        Log::error("Order confirmation SMS job failed for order ID {$this->orderId}: " . $exception->getMessage());
        
        // Update the SMS log to mark as failed if needed
        try {
            $order = Order::find($this->orderId);
            if ($order) {
                Log::error("Failed to send order confirmation SMS for order {$order->invoice} after {$this->tries} attempts");
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