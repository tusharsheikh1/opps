<?php

namespace App\Http\Controllers\Admin\Ecommerce\OrderEssential;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorAccount;
use App\Models\Commission;
use DB;

class OrderHelperService
{
    /**
     * Helper method for processing returns
     */
    public function return_helper($order)
    {
        foreach ($order->orderDetails as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $vendor = User::find($product->user_id);
                if ($vendor->role_id == 1) {
                    $amount = $vendor->vendorAccount->pending_amount;
                    $vendor->vendorAccount()->update([
                        'pending_amount' => $amount + $item->g_total
                    ]);
                } else {
                    $grand_total = $item->g_total;
                    $admin_amount = Commission::where('order_id', $order->id)->first();
                    $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                    $vendor_amount = $grand_total;
                    $amount = $adminAccount->pending_amount;

                    $vendor->vendorAccount()->update([
                        'pending_amount' => $vendor->vendorAccount->pending_amount + $vendor_amount
                    ]);
                }

                $product->quantity = $product->quantity - $item->qty;
                $product->save();
            }
        }
        if ($vendor->role_id != 1) {
            $adminAccount->update([
                'pending_amount' => $amount + $admin_amount->amount
            ]);
        }
        $order->status = 1;
        DB::table('multi_order')->where('order_id', $order->id)->update(['status' => 1]);
        $order->save();
        $user = User::find($order->user_id);
        if ($user !== null) {
            $user->pen_point += $order->point;
            if ($order->payment_method == 'wallate') {
                $user->wallate = $user->wallate - $order->total;
            }
            $user->update();
        }
    }
    
    /**
     * Helper method for processing refunds
     */
    public function refund_helper($order)
    {
        foreach ($order->orderDetails as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $vendor = User::find($product->user_id);
                if ($vendor->role_id == 1) {
                    $amount = $vendor->vendorAccount->pending_amount;
                    $vendor->vendorAccount()->update([
                        'pending_amount' => $amount + 0
                    ]);
                }
                else {
                    $admin_amount = Commission::where('order_id',$order->id)->first();
                    $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                    
                    $amount = $adminAccount->pending_amount;

                    $vendor->vendorAccount()->update([
                        'pending_amount' => $vendor->vendorAccount->pending_amount + 0
                    ]);
                }
                
                $product->quantity = $product->quantity + $item->qty;
                $product->save();
            }
        }
        if ($vendor->role_id != 1) {
            $adminAccount->update([
                'pending_amount' => $amount + 0
            ]);
        }
        $order->status = 1;
        DB::table('multi_order')->where('order_id',$order->id)->update(['status'=>1]);
        $order->save();
    }
    
    /**
     * Helper method for processing cancellations
     */
    public function cancel_helper($order)
    {
        foreach ($order->orderDetails as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $vendor = User::find($product->user_id);
                if ($vendor->role_id == 1) {
                    $amount = $vendor->vendorAccount->pending_amount;
                    $vendor->vendorAccount()->update([
                        'pending_amount' => $amount - $item->g_total
                    ]);
                } else {
                    $grand_total = $item->g_total;
                    $admin_amount = Commission::where('order_id', $order->id)->first();
                    $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                    $vendor_amount = $grand_total;
                    $amount = $adminAccount->pending_amount;

                    $vendor->vendorAccount()->update([
                        'pending_amount' => $vendor->vendorAccount->pending_amount - $vendor_amount
                    ]);
                }

                $product->quantity = $product->quantity + $item->qty;
                $product->save();
            }
        }
        if ($vendor->role_id != 1) {
            $adminAccount->update([
                'pending_amount' => $amount - $admin_amount->amount
            ]);
        }
        $order->status = 2;
        DB::table('multi_order')->where('order_id', $order->id)->update(['status' => 2]);
        $order->save();
        $user = User::find($order->user_id);
        if ($user !== null) {
            $user->pen_point -= $order->point;
            if ($order->payment_method == 'wallate') {
                $user->wallate = $user->wallate + $order->total;
            }
            $user->update();
        }
    }
    
    /**
     * Helper method for processing stock updates
     */
    public function updateProductStock($product, $quantity, $operation = 'decrease')
    {
        if (!$product) {
            return false;
        }
        
        if ($operation === 'decrease') {
            $product->quantity = max(0, $product->quantity - $quantity);
        } else {
            $product->quantity = $product->quantity + $quantity;
        }
        
        return $product->save();
    }
    
    /**
     * Helper method for updating vendor accounts
     */
    public function updateVendorAccount($vendor, $amount, $operation = 'add', $type = 'amount')
    {
        if (!$vendor || !$vendor->vendorAccount) {
            return false;
        }
        
        $field = $type === 'pending' ? 'pending_amount' : 'amount';
        $currentAmount = $vendor->vendorAccount->$field;
        
        if ($operation === 'add') {
            $newAmount = $currentAmount + $amount;
        } else {
            $newAmount = $currentAmount - $amount;
        }
        
        return $vendor->vendorAccount()->update([
            $field => $newAmount
        ]);
    }
    
    /**
     * Helper method for updating user points and wallet
     */
    public function updateUserPointsAndWallet($user, $order, $operation = 'add')
    {
        if (!$user) {
            return false;
        }
        
        if ($operation === 'add') {
            $user->point += $order->point;
            if ($order->payment_method == 'wallate') {
                $user->wallate = $user->wallate - $order->total;
            }
        } else {
            $user->point -= $order->point;
            if ($order->payment_method == 'wallate') {
                $user->wallate = $user->wallate + $order->total;
            }
        }
        
        return $user->save();
    }
    
    /**
     * Helper method for updating commission status
     */
    public function updateCommissionStatus($orderId, $status)
    {
        $commission = Commission::where('order_id', $orderId)->first();
        if ($commission) {
            $commission->status = $status;
            return $commission->save();
        }
        return false;
    }
    
    /**
     * Helper method for updating multi-order status
     */
    public function updateMultiOrderStatus($orderId, $status, $vendorId = null)
    {
        $query = DB::table('multi_order')->where('order_id', $orderId);
        
        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }
        
        return $query->update(['status' => $status]);
    }
}