<?php

namespace App\Http\Controllers\Admin\Ecommerce\OrderEssential;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorAccount;
use App\Models\Commission;
use App\Http\Controllers\Admin\Ecommerce\OrderEssential\OrderNotificationService;
use App\Http\Controllers\Admin\Ecommerce\OrderEssential\OrderHelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderStatusManager
{
    protected $notificationService;
    protected $helperService;
    
    public function __construct()
    {
        $this->notificationService = new OrderNotificationService();
        $this->helperService = new OrderHelperService();
    }
    
    /**
     * Change order status to pending
     *
     * @param  mixed $id
     * @param  bool $isBulk
     * @return mixed
     */
    public function statusPending($id, $isBulk = false)
    {
        $order = Order::findOrFail($id);
        
        // Add logic here to reverse transactions if necessary, e.g., if moving from "delivered"
        // This is a simplified version.
        if ($order->status != 0) {
            
            // If moving from Cancelled (2), stock was restocked. We need to decrease it.
            if ($order->status == 2) {
                foreach ($order->orderDetails as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) $this->helperService->decreaseProductStock($product, $item);
                }
            }
            // If moving from Delivered (3), stock was not touched, but payment was.
            // Reverting from Delivered should be a formal "Return" process.
            // For simplicity, we just change status. A full reversal would be complex.
            
            $order->status = 0;
            DB::table('multi_order')->where('order_id', $id)->update(['status' => 0]);
            $order->save();
            
            $this->notificationService->sendNotification('pending', $order->invoice, $order->user_id);
            if ($isBulk) {
                return true;
            }
            notify()->success("Order status changed to Pending", "Success");
            return back();
        }

        if ($isBulk) {
            return false;
        } // No change was made
        notify()->info("Order is already Pending", "Info");
        return back();
    }

    /**
     * Change order status pending to processing
     *
     * @param  mixed $id
     * @param  bool $isBulk
     * @return mixed
     */
    public function statusProcessing($id, $isBulk = false)
    {
        $order = Order::findOrFail($id);
        
        // Case 1: Moving from Delivered (3) back to Processing (1)
        // This is effectively a "return". We must RESTOCK the item.
        if($order->status == 3) {
            Log::info("Order {$id}: Changing status from Delivered(3) to Processing(1)");
            foreach ($order->orderDetails as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    // RESTOCK the item
                    $this->helperService->increaseProductStock($product, $item);

                    // Reverse vendor payment
                    $vendor = User::find($product->user_id);
                    if ($vendor && $vendor->role_id == 1) {
                        $amount = $vendor->vendorAccount->amount;
                        $vendor->vendorAccount()->update([
                            'amount' => $amount - $item->g_total
                        ]);
                    } elseif ($vendor) {
                        $grand_total = $item->g_total;
                        $admin_amount = Commission::where('order_id',$order->id)->first();
                        if ($admin_amount) {
                            $admin_amount->status = 0;
                            $admin_amount->update();
                            $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                            $vendor_amount = $grand_total;
                            $amount = $adminAccount->amount;

                            $vendor->vendorAccount()->update([
                                'amount' => $vendor->vendorAccount->amount - $vendor_amount
                            ]);
                            $adminAccount->update([
                                'amount' => $amount - $admin_amount->amount
                            ]);
                        }
                    }
                }
            }
            
            // Reverse user points
            $user = User::find($order->user_id);
            if ($user !== null) {
                $user->point -= $order->point;
                $user->update();
            }
        
        // Case 2: Moving from Cancelled (2) to Processing (1)
        // This reverses the cancellation. We must DECREASE stock again.
        } elseif ($order->status == 2) {
            Log::info("Order {$id}: Changing status from Cancelled(2) to Processing(1)");
            // Original logic calls return_helper, which now correctly calls decreaseProductStock.
            $this->helperService->return_helper($order);
            $this->notificationService->sendNotification('pross',$order->invoice,$order->user_id);
            if ($isBulk) { return true; }
            notify()->success("Order status processing successfully", "Congratulations");
            return back();
            
        // Case 3: Moving from Pending (0) to Processing (1)
        // This is the standard flow. No stock change needed.
        } elseif ($order->status == 0) {
            Log::info("Order {$id}: Changing status from Pending(0) to Processing(1)");
            // No stock change, just update status
        
        } else {
            if ($isBulk) { return false; }
            notify()->warning("This order status is not valid for this action", "Something Wrong");
            return back();
        }

        // Common status update for cases 0 and 3
        $order->status = 1;
        DB::table('multi_order')->where('order_id',$id)->update(['status'=>1]);
        $order->save();
        $this->notificationService->sendNotification('pross',$order->invoice,$order->user_id);
        if ($isBulk) { return true; }
        notify()->success("Order status processing successfully", "Congratulations");
        return back();
    }
    
    /**
     * Change order status to shipping
     *
     * @param  mixed $id
     * @param  bool $isBulk
     * @return mixed
     */
    public function statusShipping($id, $isBulk = false)
    {
        $order = Order::findOrFail($id);
        
        $order->status = 4;
        DB::table('multi_order')->where('order_id',$id)->update(['status'=>4]);
        $order->save();

        // This line seems vendor-specific, might need review if admin is not a vendor
        // DB::table('multi_order')->where('order_id',$id)->where('vendor_id',auth()->id())->update(['status'=>4]);
        
        $this->notificationService->sendNotification('shipping',$order->invoice,$order->user_id);
        if ($isBulk) { return true; }
        notify()->success("Order status Shipping successfully", "Congratulations");
        return back();
    }
    
    /**
     * Change order status pending/processing to cancel
     *
     * @param  mixed $id
     * @param  bool $isBulk
     * @return mixed
     */
    public function statusCancel($id, $isBulk = false)
    {
        $order = Order::findOrFail($id);
        
        if ($order->status == 0 || $order->status == 1) {
            Log::info("Order {$id}: Changing status to Cancelled(2)");
            $this->helperService->cancel_helper($order);
            $this->notificationService->sendNotification('cancel',$order->invoice,$order->user_id);
            if ($isBulk) { return true; }
            notify()->success("Order cancel successfully", "Congratulations");
            return back();
        }

        if ($isBulk) { return false; }
        notify()->warning("This order status not pending/processing", "Something Wrong");
        return back();
    }
    
    /**
     * Change order status to delivered
     *
     * @param  mixed $id
     * @param  bool $isBulk
     * @return mixed
     */
    public function statusDelivered($id, $isBulk = false)
    {
        $order = Order::findOrFail($id);
        $wasCancelled = ($order->status == 2);
        
        // Valid previous statuses: Pending(0), Processing(1), Shipping(4), or reversing a Cancel(2)
        if ($order->status == 0 || $order->status == 1 || $order->status == 4 || $order->status == 2) {
            Log::info("Order {$id}: Changing status to Delivered(3) from ({$order->status})");

            // If it was cancelled(2), we need to reverse the stock and payment cancellation.
            if ($wasCancelled) {
                // We need to DECREASE stock again (undoing the cancellation restock).
                foreach ($order->orderDetails as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $this->helperService->decreaseProductStock($product, $item); // Use the new helper
                    }
                }
            }

            // Pay the vendor (this logic moves from pending to final)
            // This runs whether it was cancelled or not, as it pays out the final amount.
            foreach ($order->orderDetails as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $vendor = User::find($product->user_id);
                    if ($vendor && $vendor->role_id == 1) {
                        $amount = $vendor->vendorAccount->amount;
                        $vendor->vendorAccount()->update([
                            'amount' => $amount + $item->g_total
                        ]);
                    }
                    elseif ($vendor) {
                        $grand_total = $item->g_total;
                        $admin_amount = Commission::where('order_id',$order->id)->first();
                        
                        if($admin_amount) { // Check if commission record exists
                            $admin_amount->status = 1;
                            $admin_amount->update();
                            $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                            $vendor_amount = $grand_total;
                            $amount = $adminAccount->amount;

                            $vendor->vendorAccount()->update([
                                'amount' => $vendor->vendorAccount->amount + $vendor_amount
                            ]);
                            $adminAccount->update([
                                'amount' => $amount + $admin_amount->amount
                            ]);
                        }
                    }
                    
                    // DO NOT TOUCH STOCK HERE. 
                    // Stock was reduced on initial order placement.
                    // The only exception is reversing a cancellation, which is done above.
                }
            }
            
            $order->status = 3;
            DB::table('multi_order')->where('order_id',$id)->update(['status'=>3]);
            $order->save();
            $user = User::find($order->user_id);
            if ($user !== null) {
                $user->point += $order->point;
                // If it was a cancelled wallet order, helper_cancel would have refunded.
                // Now we take the money back from the wallet.
                if($order->payment_method == 'wallate' && $wasCancelled) {
                    $user->wallate = $user->wallate - $order->total;
                }
                $user->save();
            }
             
            // Send email if configured
            if(setting('mail_config') == 1) {
                $data = [
                    'order_id'        => $order->order_id,
                    'invoice'         => $order->invoice,
                    'name'            => $order->first_name,
                    'email'           => $order->email,
                    'address'         => $order->address,
                    'coupon_code'     => $order->coupon_code,
                    'subtotal'        => $order->subtotal,
                    'shipping_charge' => $order->shipping_charge,
                    'discount'        => $order->discount,
                    'total'           => $order->total,
                    'date'            => $order->created_at,
                    'payment_method'  => $order->payment_method,
                    'pay_status'      => $order->pay_staus,
                    'pay_date'        => $order->pay_date,
                    'orderDetails'    => $order->orderDetails,
                    'phone'           => $order->phone,
                ];
                try {
                    Mail::send('frontend.invoice-mail', $data, function($mail) use ($data)
                    {
                        $mail->from(config('mail.from.address'),  config('app.name'))
                            ->to($data['email'], $data['name'])
                            ->subject('Order Invoice');
                    });
                } catch (\Exception $e) {
                    Log::error("Order {$id}: Failed to send invoice email. " . $e->getMessage());
                }
            }
            $this->notificationService->sendNotification('delevery',$order->invoice,$order->user_id);
            if ($isBulk) { return true; }
            notify()->success("Order delivered successfully", "Congratulations");
            return back();
        }
        if ($isBulk) { return false; }
        notify()->warning("This order status cannot be set to delivered", "Something Wrong");
        return back();
    }
   
    /**
     * Return Accept by Admin
     */
    public function returnAccept($id, $isBulk = false)
    {
        $order = Order::findOrFail($id);
        if ($order->status == 6) {
            $order->status = 7; // return accept status
            DB::table('multi_order')->where('order_id', $id)->update(['status' => 7]);
            $order->save();
            $this->notificationService->sendNotification('return_accept', $order->invoice, $order->user_id);
            if ($isBulk) { return true; }
            notify()->success("Order return accepted successfully", "Congratulations");
            return back();
        }
        if ($isBulk) { return false; }
        notify()->warning("This order status not return requested", "Something Wrong");
        return back();
    }
    
    /**
     * Complete return by admin as got the product from customer
     */
    public function returnComplete($id, $isBulk = false)
    {
        $order = Order::findOrFail($id);
        if ($order->status == 7) {
            $order->status = 8; // return completed status
            DB::table('multi_order')->where('order_id', $id)->update(['status' => 8]);
            $order->save();
            
            // When return is complete, we should restock the item.
            foreach ($order->orderDetails as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $this->helperService->increaseProductStock($product, $item);
                }
            }
            
            $this->notificationService->sendNotification('return_complete', $order->invoice, $order->user_id);
            if ($isBulk) { return true; }
            notify()->success("Order Returned back successfully", "Congratulations");
            return back();
        }
        if ($isBulk) { return false; }
        notify()->warning("This order return system not completed yet", "Something Wrong");
        return back();
    }

    /**
     * Process refund
     */
    public function refund(Request $request)
    {
        $order = Order::find($request->order);
        if($order->refund_method == null) {
            // This implies this is the FIRST refund action.
            // refund_helper will RESTOCK the item.
            $this->helperService->refund_helper($order);
            foreach ($order->orderDetails as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $vendor = User::find($product->user_id);
                    if ($vendor && $vendor->role_id == 1) {
                        $amount = $vendor->vendorAccount->amount;
                        $vendor->vendorAccount()->update([
                            'amount' => $amount - $item->g_total
                        ]);
                    }
                    elseif ($vendor) {
                        $grand_total = $item->g_total;
                        $admin_amount = Commission::where('order_id',$order->id)->first();
                        if ($admin_amount) {
                            $admin_amount->status = 0;
                            $admin_amount->update();
                            $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                            $vendor_amount = $grand_total ;
                            $amount = $adminAccount->amount;

                            $vendor->vendorAccount()->update([
                                'amount' => $vendor->vendorAccount->amount - $vendor_amount
                            ]);
                            
                            $adminAccount->update([
                                'amount' => $amount - $admin_amount->amount
                            ]);
                        }
                    }
                    
                    // The helper service already restocked.
                    // This manual stock reduction is a BUG.
                    // $product->quantity = $product->quantity - $item->qty;
                    // $product->save();
                }
            }
            // This logic is also buggy.
            // if ($vendor && $vendor->role_id != 1) {
            //     $adminAccount->update([
            //         'amount' => $amount - $admin_amount->amount
            //     ]);
            // }
            $order->status = 5;
            DB::table('multi_order')->where('order_id',$order->id)->update(['status'=>5]);
            $order->refund_amount = $request->amount;
            $order->refund_method = $request->method;
            $order->save();
            $user = User::find($order->user_id);
            if ($user !== null) {
                if($order->refund_method == 'wallate') {
                    $user->wallate = $request->amount + $user->wallate;
                }
                $user->point -= $order->point;
                $user->update();
            }
        } else {
            // Order already refunded, just updating details
            $order->status = 5;
            DB::table('multi_order')->where('order_id',$order->id)->update(['status'=>5]);
            $order->refund_amount = $request->amount;
            $order->refund_method = $request->method;
            $order->save();
            $user = User::find($order->user_id);
            if($order->refund_method == 'wallate') {
                $user->wallate = $user->wallate + $request->amount;
                $user->update();
            }
        }
        $this->notificationService->sendNotification('refund',$order->invoice,$order->user_id);
        notify()->success("Order Refund successfully", "Congratulations");
        return back();
    }
    
    /**
     * Process refund (alternative method)
     */
    public function refund_two(Request $request)
    {
        $order = Order::find($request->order);
        $order->status = 5;
        DB::table('multi_order')->where('order_id',$order->id)->update(['status'=>5]);
        $order->refund_amount = $request->amount;
        $order->refund_method = $request->method;
        $order->save();
        $user = User::find($order->user_id);
        if($order->refund_method == 'wallate') {
            $user->wallate = $user->wallate + $request->amount;
            $user->update();
        }
        $this->notificationService->sendNotification('refund',$order->invoice,$order->user_id);
        notify()->success("Order Refund successfully", "Congratulations");
        return back();
    }

    /**
     * Bulk status update for orders
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkStatusUpdate(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'order_ids'   => 'required|array|min:1',
                'order_ids.*' => 'exists:orders,id',
                'status'      => 'required|integer|in:0,1,2,3,4,5,6,7,8',
            ]);

            $orderIds = $request->order_ids;
            $newStatus = (int)$request->status;
            
            $successCount = 0;
            $failedCount = 0;
            $skippedCount = 0;
            $errors = [];

            // Process each order
            foreach ($orderIds as $orderId) {
                try {
                    $order = Order::find($orderId);
                    
                    if (!$order) {
                        $failedCount++;
                        $errors[] = "Order ID {$orderId} not found";
                        continue;
                    }

                    // Check if the order is already in the target status
                    if ($order->status == $newStatus) {
                        $skippedCount++;
                        continue;
                    }

                    $success = false;

                    // Call the appropriate status method based on the new status
                    switch ($newStatus) {
                        case 0: // Pending
                            $success = $this->statusPending($orderId, true);
                            break;
                        case 1: // Processing
                            $success = $this->statusProcessing($orderId, true);
                            break;
                        case 2: // Cancel
                            $success = $this->statusCancel($orderId, true);
                            break;
                        case 3: // Delivered
                            $success = $this->statusDelivered($orderId, true);
                            break;
                        case 4: // Shipping
                            $success = $this->statusShipping($orderId, true);
                            break;
                        case 7: // Return Accepted
                            $success = $this->returnAccept($orderId, true);
                            break;
                        case 8: // Return Complete
                            $success = $this->returnComplete($orderId, true);
                            break;
                        default:
                            // For other statuses, update directly
                            $order->status = $newStatus;
                            DB::table('multi_order')->where('order_id', $orderId)->update(['status' => $newStatus]);
                            $order->save();
                            $success = true;
                            break;
                    }

                    if ($success) {
                        $successCount++;
                    } else {
                        $failedCount++;
                        $errors[] = "Order {$order->invoice} could not be updated (invalid state transition)";
                    }

                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Order ID {$orderId}: ". substr($e->getMessage(), 0, 100) . "...";
                    Log::error("Bulk update failed for order {$orderId}: " . $e->getMessage());
                }
            }

            // Prepare response message
            $messages = [];
            
            if ($successCount > 0) {
                $statusName = $this->getStatusName($newStatus);
                $messages[] = "Successfully updated {$successCount} order(s) to {$statusName}";
            }
            
            if ($skippedCount > 0) {
                $messages[] = "{$skippedCount} order(s) were already in the target status";
            }
            
            if ($failedCount > 0) {
                $messages[] = "{$failedCount} order(s) could not be updated";
            }

            // Log the bulk operation
            Log::info("Bulk status update completed", [
                'admin_id' => auth()->id(),
                'target_status' => $newStatus,
                'total_orders' => count($orderIds),
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'skipped_count' => $skippedCount,
                'errors' => $errors
            ]);

            // Return appropriate response
            if ($successCount > 0) {
                $message = implode('. ', $messages);
                notify()->success($message, "Bulk Update Completed");
            } elseif ($skippedCount > 0 && $failedCount == 0) {
                notify()->info(implode('. ', $messages), "No Changes Made");
            } else {
                $message = implode('. ', $messages);
                if (!empty($errors)) {
                    $message .= ". Errors: " . implode(', ', array_slice($errors, 0, 3));
                    if (count($errors) > 3) {
                        $message .= " and " . (count($errors) - 3) . " more...";
                    }
                }
                notify()->warning($message, "Bulk Update Issues");
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            notify()->error("Invalid request data. Please check your selection.", "Validation Error");
        } catch (\Exception $e) {
            Log::error("Bulk status update failed: " . $e->getMessage());
            notify()->error("An error occurred during bulk update. Please try again.", "Error");
        }

        return back();
    }

    /**
     * Get status name for display
     *
     * @param int $status
     * @return string
     */
    private function getStatusName($status)
    {
        $statusNames = [
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

        return $statusNames[$status] ?? 'Unknown';
    }
}