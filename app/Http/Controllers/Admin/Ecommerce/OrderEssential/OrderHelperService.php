<?php

namespace App\Http\Controllers\Admin\Ecommerce\OrderEssential;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorAccount;
use App\Models\Commission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderHelperService
{
    /**
     * Helper method for processing returns
     * Note: This is called when reversing a cancellation (e.g., Cancelled -> Processing)
     * It DECREASES stock, as the item is no longer "returned".
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

                // Use new helper to DECREASE stock for the specific variation
                $this->decreaseProductStock($product, $item);
            }
        }
        if (isset($vendor) && $vendor->role_id != 1) {
            if(isset($adminAccount) && isset($admin_amount)){
                $adminAccount->update([
                    'pending_amount' => $amount + $admin_amount->amount
                ]);
            }
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
     * This INCREASES stock, as the item is refunded/returned.
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
                
                // Use new helper to INCREASE stock for the specific variation
                $this->increaseProductStock($product, $item);
            }
        }
        if (isset($vendor) && $vendor->role_id != 1) {
            if(isset($adminAccount)){
                $adminAccount->update([
                    'pending_amount' => $amount + 0
                ]);
            }
        }
        $order->status = 1;
        DB::table('multi_order')->where('order_id',$order->id)->update(['status'=>1]);
        $order->save();
    }
    
    /**
     * Helper method for processing cancellations
     * This INCREASES stock, as the item is returned to inventory.
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

                // Use new helper to INCREASE stock for the specific variation
                $this->increaseProductStock($product, $item);
            }
        }
        if (isset($vendor) && $vendor->role_id != 1) {
             if(isset($adminAccount) && isset($admin_amount)){
                $adminAccount->update([
                    'pending_amount' => $amount - $admin_amount->amount
                ]);
             }
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

    /**
     * NEW HELPER: Increase stock for product based on variation type (for cancellations/returns)
     */
    private function increaseProductStock($product, $item)
    {
        $quantity = $item->qty;
        
        // Decode size/color data from order item
        $colorId = $item->color ?? null;
        $sizeData = json_decode($item->size, true);
        
        Log::info('Admin Stock Increase: Start', ['product_id' => $product->id, 'order_item_id' => $item->id, 'color' => $colorId, 'size_json' => $item->size]);

        // Priority 1: Check for Color-Size variation
        // Note: 'blank' is a string, not null, from the fixed frontend
        if ($colorId && $colorId !== 'blank' && !empty($sizeData)) {
            $sizeId = null;
            if (isset($sizeData['size_id'])) { // Handle new format
                $sizeId = $sizeData['size_id'];
            } elseif (is_array($sizeData)) { // Handle old format
                 foreach ($sizeData as $sizeInfo) {
                    if (isset($sizeInfo['size_id'])) {
                        $sizeId = $sizeInfo['size_id'];
                        break;
                    }
                }
            }

            if ($sizeId) {
                DB::table('color_size_product')
                    ->where('product_id', $product->id)
                    ->where('color_id', $colorId)
                    ->where('size_id', $sizeId)
                    ->increment('quantity', $quantity);
                
                Log::info('Admin Stock Increase: Color-Size', ['product_id' => $product->id, 'color_id' => $colorId, 'size_id' => $sizeId, 'qty' => $quantity]);
            }
        }
        // Priority 2: Check for Size-Only variation (no color)
        elseif (($colorId === null || $colorId === 'blank') && !empty($sizeData)) {
             $sizeId = null;
            if (isset($sizeData['size_id'])) { // Handle new format
                $sizeId = $sizeData['size_id'];
            } elseif (is_array($sizeData)) { // Handle old format
                 foreach ($sizeData as $sizeInfo) {
                    if (isset($sizeInfo['size_id'])) {
                        $sizeId = $sizeInfo['size_id'];
                        break;
                    }
                }
            }

            if ($sizeId) {
                DB::table('color_size_product')
                    ->where('product_id', $product->id)
                    ->where('size_id', $sizeId)
                    ->whereNull('color_id')
                    ->increment('quantity', $quantity);
                
                Log::info('Admin Stock Increase: Size-Only', ['product_id' => $product->id, 'size_id' => $sizeId, 'qty' => $quantity]);
            }
        }
        // Priority 3: Check for Attribute variation
        elseif (!empty($sizeData)) {
            $attributeValueId = null;
            // Check for modern format first
            if (is_array($sizeData) && array_key_exists(0, $sizeData) && isset($sizeData[0]['attribute_value_id'])) {
                 $attributeValueId = $sizeData[0]['attribute_value_id'];
            }
            // Check for legacy format
            else if (is_array($sizeData)) {
                 foreach ($sizeData as $attrInfo) {
                    if (isset($attrInfo['attribute_value_id'])) {
                        $attributeValueId = $attrInfo['attribute_value_id'];
                        break;
                    }
                }
            }


            if ($attributeValueId) {
                 DB::table('attribute_product')
                    ->where('product_id', $product->id)
                    ->where('attribute_value_id', $attributeValueId)
                    ->increment('qnty', $quantity);
                
                Log::info('Admin Stock Increase: Attribute', ['product_id' => $product->id, 'attr_val_id' => $attributeValueId, 'qty' => $quantity]);
            }
        }
        
        // Always increase main product quantity (for all product types)
        $product->increment('quantity', $quantity);
        
        Log::info('Admin Stock Increase: Main Product', ['product_id' => $product->id, 'qty' => $quantity]);
    }

    /**
     * NEW HELPER: Decrease stock for product based on variation type (for reversing cancellations)
     */
    public function decreaseProductStock($product, $item)
    {
        $quantity = $item->qty;
        
        // Decode size/color data from order item
        $colorId = $item->color ?? null;
        $sizeData = json_decode($item->size, true);

        Log::info('Admin Stock Decrease: Start', ['product_id' => $product->id, 'order_item_id' => $item->id, 'color' => $colorId, 'size_json' => $item->size]);
        
        // Priority 1: Check for Color-Size variation
        if ($colorId && $colorId !== 'blank' && !empty($sizeData)) {
            $sizeId = null;
            if (isset($sizeData['size_id'])) { // Handle new format
                $sizeId = $sizeData['size_id'];
            } elseif (is_array($sizeData)) { // Handle old format
                 foreach ($sizeData as $sizeInfo) {
                    if (isset($sizeInfo['size_id'])) {
                        $sizeId = $sizeInfo['size_id'];
                        break;
                    }
                }
            }

            if ($sizeId) {
                DB::table('color_size_product')
                    ->where('product_id', $product->id)
                    ->where('color_id', $colorId)
                    ->where('size_id', $sizeId)
                    ->decrement('quantity', $quantity);
                
                Log::info('Admin Stock Decrease: Color-Size', ['product_id' => $product->id, 'color_id' => $colorId, 'size_id' => $sizeId, 'qty' => $quantity]);
            }
        }
        // Priority 2: Check for Size-Only variation (no color)
        elseif (($colorId === null || $colorId === 'blank') && !empty($sizeData)) {
            $sizeId = null;
            if (isset($sizeData['size_id'])) { // Handle new format
                $sizeId = $sizeData['size_id'];
            } elseif (is_array($sizeData)) { // Handle old format
                 foreach ($sizeData as $sizeInfo) {
                    if (isset($sizeInfo['size_id'])) {
                        $sizeId = $sizeInfo['size_id'];
                        break;
                    }
                }
            }

            if ($sizeId) {
                DB::table('color_size_product')
                    ->where('product_id', $product->id)
                    ->where('size_id', $sizeId)
                    ->whereNull('color_id')
                    ->decrement('quantity', $quantity);
                
                Log::info('Admin Stock Decrease: Size-Only', ['product_id' => $product->id, 'size_id' => $sizeId, 'qty' => $quantity]);
            }
        }
        // Priority 3: Check for Attribute variation
        elseif (!empty($sizeData)) {
            $attributeValueId = null;
             // Check for modern format first
            if (is_array($sizeData) && array_key_exists(0, $sizeData) && isset($sizeData[0]['attribute_value_id'])) {
                 $attributeValueId = $sizeData[0]['attribute_value_id'];
            }
            // Check for legacy format
            else if (is_array($sizeData)) {
                 foreach ($sizeData as $attrInfo) {
                    if (isset($attrInfo['attribute_value_id'])) {
                        $attributeValueId = $attrInfo['attribute_value_id'];
                        break;
                    }
                }
            }

            if ($attributeValueId) {
                 DB::table('attribute_product')
                    ->where('product_id', $product->id)
                    ->where('attribute_value_id', $attributeValueId)
                    ->decrement('qnty', $quantity);
                
                Log::info('Admin Stock Decrease: Attribute', ['product_id' => $product->id, 'attr_val_id' => $attributeValueId, 'qty' => $quantity]);
            }
        }
        
        // Always decrease main product quantity (for all product types)
        $product->decrement('quantity', $quantity);
        
        Log::info('Admin Stock Decrease: Main Product', ['product_id' => $product->id, 'qty' => $quantity]);
    }
}