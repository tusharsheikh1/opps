<?php

namespace App\Observers;

use App\Models\Order;
use App\Jobs\SendOrderStatusUpdateSms;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     * This runs after the model is updated
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        // Check if status has changed
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            // Only send SMS if status actually changed and phone number exists
            if ($oldStatus != $newStatus && !empty($order->phone)) {
                Log::info("Order status changed for {$order->invoice}: {$oldStatus} -> {$newStatus}");
                
                // Dispatch SMS job immediately for status updates
                SendOrderStatusUpdateSms::dispatch($order->id, $oldStatus, $newStatus);
            }
        }
    }

    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        // This will be handled in the controller to dispatch delayed SMS
        Log::info("New order created: {$order->invoice}");
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        Log::info("Order deleted: {$order->invoice}");
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}