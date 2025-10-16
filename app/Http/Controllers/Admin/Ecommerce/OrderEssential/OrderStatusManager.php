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
use DB;
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
            $order->status = 0;
            DB::table('multi_order')->where('order_id', $id)->update(['status' => 0]);
            $order->save();
            // You might want a specific notification for this action
            // $this->notificationService->sendNotification('pending', $order->invoice, $order->user_id);
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
        if($order->status == 3) {
            $this->helperService->return_helper($order);
            foreach ($order->orderDetails as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $vendor = User::find($product->user_id);
                    if ($vendor->role_id == 1) {
                        $amount = $vendor->vendorAccount->amount;
                        $vendor->vendorAccount()->update([
                            'amount' => $amount - $item->g_total
                        ]);
                    }
                    else {
                        $grand_total = $item->g_total;
                        $admin_amount = Commission::where('order_id',$order->id)->first();
                        $admin_amount->status = 0;
                        $admin_amount->update();
                        $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                        $vendor_amount = $grand_total;
                        $amount = $adminAccount->amount;

                        $vendor->vendorAccount()->update([
                            'amount' => $vendor->vendorAccount->amount - $vendor_amount
                        ]);
                    }
                    
                    $product->quantity = $product->quantity - $item->qty;
                    $product->save();
                }
            }
            if ($vendor->role_id != 1) {
                $adminAccount->update([
                    'amount' => $amount - $admin_amount->amount
                ]);
            }
            $order->status = 1;
            DB::table('multi_order')->where('order_id',$id)->update(['status'=>1]);
            $order->save();
            $user = User::find($order->user_id);
            if ($user !== null) {
                $user->point -= $order->point;
                $user->update();
            }
            if ($isBulk) { return true; }
            notify()->success("Order status processing successfully", "Congratulations");
            return back();
        } elseif ($order->status == 0) {
            $order->status = 1;
            DB::table('multi_order')->where('order_id',$id)->update(['status'=>1]);
            $order->save();
            $this->notificationService->sendNotification('pross',$order->invoice,$order->user_id);
            if ($isBulk) { return true; }
            notify()->success("Order status processing successfully", "Congratulations");
            return back();
        } elseif ($order->status == 2) {
            $this->helperService->return_helper($order);
            $this->notificationService->sendNotification('cancel',$order->invoice,$order->user_id);
            if ($isBulk) { return true; }
            notify()->success("Order status processing successfully", "Congratulations");
            return back();
        }

        if ($isBulk) { return false; }
        notify()->warning("This order status not pending", "Something Wrong");
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

        DB::table('multi_order')->where('order_id',$id)->where('vendor_id',auth()->id())->update(['status'=>4]);
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
     * Change order status pending/processing to delivered
     *
     * @param  mixed $id
     * @param  bool $isBulk
     * @return mixed
     */
    public function statusDelivered($id, $isBulk = false)
    {
        $order = Order::findOrFail($id);
        if ($order->status == 0 || $order->status == 1 || $order->status == 4) {
            
            $this->helperService->cancel_helper($order);
            foreach ($order->orderDetails as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $vendor = User::find($product->user_id);
                    if ($vendor->role_id == 1) {
                        $amount = $vendor->vendorAccount->amount;
                        $vendor->vendorAccount()->update([
                            'amount' => $amount + $item->g_total
                        ]);
                    }
                    else {
                        $grand_total = $item->g_total;
                        $admin_amount = Commission::where('order_id',$order->id)->first();
                        $admin_amount->status = 1;
                        $admin_amount->update();
                        $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                        $vendor_amount = $grand_total;
                        $amount = $adminAccount->amount;

                        $vendor->vendorAccount()->update([
                            'amount' => $vendor->vendorAccount->amount + $vendor_amount
                        ]);
                    }
                    
                    $product->quantity = $product->quantity - $item->qty;
                    $product->save();
                }
            }
            if ($vendor->role_id != 1) {
                $adminAccount->update([
                    'amount' => $amount + $admin_amount->amount
                ]);
            }
            $order->status = 3;
            DB::table('multi_order')->where('order_id',$id)->update(['status'=>3]);
            $order->save();
            $user = User::find($order->user_id);
            if ($user !== null) {
                $user->point += $order->point;
                if($order->payment_method == 'wallate') {
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
                Mail::send('frontend.invoice-mail', $data, function($mail) use ($data)
                {
                    $mail->from(config('mail.from.address'),  config('app.name'))
                        ->to($data['email'], $data['name'])
                        ->subject('Order Invoice');
                });
            }
            $this->notificationService->sendNotification('delevery',$order->invoice,$order->user_id);
            if ($isBulk) { return true; }
            notify()->success("Order delivered successfully", "Congratulations");
            return back();
        }
        if ($isBulk) { return false; }
        notify()->warning("This order status not pending/processing", "Something Wrong");
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
            $this->notificationService->sendNotification('delevery', $order->invoice, $order->user_id);
            if ($isBulk) { return true; }
            notify()->success("Order return accepted successfully", "Congratulations");
            return back();
        }
        if ($isBulk) { return false; }
        notify()->warning("This order status not return accepted", "Something Wrong");
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
            
            $this->notificationService->sendNotification('delevery', $order->invoice, $order->user_id);
            if ($isBulk) { return true; }
            notify()->success("Order Returned back successfully", "Congratulations");
            return back();
        }
        if ($isBulk) { return false; }
        notify()->warning("This order retrun system not completed yet", "Something Wrong");
        return back();
    }

    /**
     * Process refund
     */
    public function refund(Request $request)
    {
        $order = Order::find($request->order);
        if($order->refund_method == null) {
            $this->helperService->refund_helper($order);
            foreach ($order->orderDetails as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $vendor = User::find($product->user_id);
                    if ($vendor->role_id == 1) {
                        $amount = $vendor->vendorAccount->amount;
                        $vendor->vendorAccount()->update([
                            'amount' => $amount - $item->g_total
                        ]);
                    }
                    else {
                        $grand_total = $item->g_total;
                        $admin_amount = Commission::where('order_id',$order->id)->first();
                        $admin_amount->status = 0;
                        $admin_amount->update();
                        $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                        $vendor_amount = $grand_total ;
                        $amount = $adminAccount->amount;

                        $vendor->vendorAccount()->update([
                            'amount' => $vendor->vendorAccount->amount - $vendor_amount
                        ]);
                    }
                    
                    $product->quantity = $product->quantity - $item->qty;
                    $product->save();
                }
            }
            if ($vendor->role_id != 1) {
                $adminAccount->update([
                    'amount' => $amount - $admin_amount->amount
                ]);
            }
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
                    $errors[] = "Order ID {$orderId}: " . $e->getMessage();
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