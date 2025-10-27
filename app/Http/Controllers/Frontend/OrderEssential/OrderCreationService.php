<?php

namespace App\Http\Controllers\Frontend\OrderEssential;

use App\Models\Commission;
use App\Models\Order;
use App\Models\CartInfo;
use App\Models\PartialPayment;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorAccount;
use App\Jobs\SendOrderConfirmationSms;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Library\UddoktaPay;
use Carbon\Carbon;
use DB;
use Exception;

class OrderCreationService
{
    /**
     * Check if guest user has placed an order within the last 5 minutes
     */
    private function checkGuestOrderInterval($phone, $email = null)
    {
        $fiveMinutesAgo = Carbon::now()->subMinutes(5);

        $query = Order::where('created_at', '>=', $fiveMinutesAgo)
            ->where('phone', $phone);

        // Also check by email if provided and not empty
        if (!empty($email) && $email !== 'noreply@lems.shop') {
            $query->orWhere(function ($q) use ($email, $fiveMinutesAgo) {
                $q->where('email', $email)
                  ->where('created_at', '>=', $fiveMinutesAgo);
            });
        }

        return $query->exists();
    }

    /**
     * Get remaining time until next order is allowed
     */
    private function getRemainingTime($phone, $email = null)
    {
        $query = Order::where('phone', $phone);

        if (!empty($email) && $email !== 'noreply@lems.shop') {
            $query->orWhere('email', $email);
        }

        $lastOrder = $query->orderBy('created_at', 'desc')->first();

        if (!$lastOrder) {
            return 0;
        }

        $nextAllowedTime = $lastOrder->created_at->addMinutes(5);
        $now = Carbon::now();

        if ($nextAllowedTime > $now) {
            return $nextAllowedTime->diffInSeconds($now);
        }

        return 0;
    }

    /**
     * Store Guest order (minimal)
     */
    public function orderStore_minimal(Request $request)
    {
        $phoneMinDigits = empty(setting('phone_min_dgt')) ? 11 : setting('phone_min_dgt');
        $phoneMaxDigits = empty(setting('phone_max_dgt')) ? 11 : setting('phone_max_dgt');

        $this->validate($request, [
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'nullable|string|max:255',
            'company'         => 'nullable|string|max:255',
            'country'         => 'nullable|string|max:255',
            'address'         => 'required|string|max:255',
            'city'            => 'nullable|string|max:255',
            'shipping_range'  => 'required|integer|max:255',
            'district'        => 'nullable|string|max:255',
            'postcode'        => 'nullable|string|max:255',
            'phone'           => 'required|string|max:' . $phoneMaxDigits . '|min:' . $phoneMinDigits,
            'email'           => 'nullable|email|string|max:255',
            'shipping_method' => 'nullable|string|max:255',
            'payment_method'  => 'nullable|string|max:255',
            'mobile_number'   => 'nullable|string|max:255',
            'transaction_id'  => 'nullable|string|max:255',
            'bank_name'       => 'nullable|string|max:255',
            'account_number'  => 'nullable|string|max:255',
            'holder_name'     => 'nullable|string|max:255',
            'branch'          => 'nullable|string|max:255',
            'routing'         => 'nullable|string|max:255',
            // Added validation for cart-specific fields if they exist
            'seller_count'    => 'sometimes|required|integer|min:1',
            'stotal'          => 'sometimes|required|numeric|min:0',
        ]);

        // Check order interval for guests (users not logged in)
        if (!auth()->check()) {
            $email = $request->email ?: 'noreply@lems.shop';

            if ($this->checkGuestOrderInterval($request->phone, $email)) {
                $remainingSeconds = $this->getRemainingTime($request->phone, $email);
                $remainingMinutes = ceil($remainingSeconds / 60);
                $whatsappNumber = setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX';

                notify()->warning("à¦…à¦ªà§‡à¦•à§à¦·à¦¾ à¦•à¦°à§à¦¨! à¦†à¦ªà¦¨à¦¿ à¦‡à¦¤à¦¿à¦®à¦§à§à¦¯à§‡ à¦à¦•à¦Ÿà¦¾ à¦…à¦°à§à¦¡à¦¾à¦° à¦•à¦°à§‡à¦›à§‡à¦¨à¥¤ à¦†à¦ªà¦¨à¦¿ {$remainingMinutes} à¦®à¦¿à¦¨à¦¿à¦Ÿ à¦ªà¦° à¦†à¦¬à¦¾à¦° à¦…à¦°à§à¦¡à¦¾à¦° à¦•à¦°à¦¤à§‡ à¦ªà¦¾à¦°à¦¬à§‡à¦¨à¥¤ à¦à¦Ÿà¦¿ à¦­à§à¦¯à¦¼à¦¾ à¦…à¦°à§à¦¡à¦¾à¦° à¦ªà§à¦°à¦¤à¦¿à¦°à§‹à¦§à§‡à¦° à¦œà¦¨à§à¦¯à¥¤ à¦…à¦°à§à¦¡à¦¾à¦°à§‡à¦° à¦¯à§‡à¦•à§‹à¦¨ à¦ªà¦°à¦¿à¦¬à¦°à§à¦¤à¦¨à§‡à¦° à¦œà¦¨à§à¦¯ à¦†à¦®à¦¾à¦¦à§‡à¦° WhatsApp {$whatsappNumber} à¦ à¦¨à¦• à¦•à¦°à§à¦¨à¥¤", "à¦…à¦°à§à¦¡à¦¾à¦° à¦¸à§€à¦®à¦¾à¦¬à¦¦à§à¦§à¦¤à¦¾");
                return redirect()->back();
            }
        }

        // Use stotal and seller_count passed from the form (validated above)
        $cart_subtotal = $request->stotal ?? 0; // Default to 0 if not provided
        $seller_count = $request->seller_count ?? 1; // Default to 1 if not provided

        $shipping_charge = $this->calculateShippingCharge($cart_subtotal, $request->shipping_range, $seller_count);
        $single_charge = $this->calculateSingleCharge($cart_subtotal, $request->shipping_range);

        $coupon_code = '';
        $discount = 0;
        $total = $cart_subtotal + $shipping_charge;

        if (Session::has('coupon')) {
            $coupon_code = Session::get('coupon')['name'];
            $discount = Session::get('coupon')['discount'];
            $total = ($cart_subtotal + $shipping_charge) - $discount;
        }

        $order = $this->createOrder($request, $shipping_charge, $single_charge, $coupon_code, $cart_subtotal, $discount, $total);
        $this->generateOrderId($order);

        $total_refer = 0;
        $usids = [];

        foreach (Cart::content() as $item) {
            $pp = Product::find($item->id);
            // Ensure $pp is not null before proceeding
             if (!$pp) {
                \Log::warning('Product not found in cart processing: ID ' . $item->id);
                continue; // Skip this item if product doesn't exist
            }

            if (!in_array("$pp->user_id", $usids)) {
                $usids[] = $pp->user_id;
            }

            $total_refer += (($item->price / 100) * $item->qty);
            $price = ($item->qty >= 6 && $pp->whole_price > 0) ? $pp->whole_price : $item->price;

            $vendor = User::find($pp->user_id); // Vendor can be null
            $vp = $price * $item->qty;
            $gt = $this->calculateGrandTotal($vendor, $vp); // Pass null-safe vendor

            // --- START: VARIATION DATA FIX (Location 1: orderStore_minimal) ---
            $attributes = $item->options->attributes ?? [];
            $colorValue = $item->options->color ?? 'blank';
            $sizeJsonData = null;

            if (!empty($attributes) && is_array($attributes)) {
                if (isset($attributes['size'])) {
                    $sizeId = $attributes['size'];
                    $sizeJsonData = json_encode(['size_id' => $sizeId]);
                } else {
                    $attributeData = [];
                    foreach ($attributes as $attrSlug => $attrValueId) {
                        $attributeData[] = ['attribute_value_id' => $attrValueId];
                    }
                    $sizeJsonData = json_encode($attributeData);
                }
            } else {
                $sizeJsonData = json_encode([]);
            }
            // --- END: VARIATION DATA FIX ---

            $order->orderDetails()->create([
                'product_id'  => $item->id,
                'seller_id'   => $pp->user_id, // Can be null
                'title'       => $item->name,
                'color'       => $colorValue,
                'size'        => $sizeJsonData, // CORRECTED: Use transformed JSON data
                'qty'         => $item->qty,
                'price'       => $price,
                'total_price' => $price * $item->qty,
                'g_total'     => $gt
            ]);

            $this->updateUserPoints($item, $order);
            $this->updateVendorAccount($pp, $item, $order, $price); // This method is now null-safe
        }

        $this->createMultiOrder($usids, $order->id, $single_charge);
        $this->handleWalletPayment($request, $order);
        $this->handlePartialPayment($request, $order);
        $this->handleCouponDiscount($order, $discount);

        Cart::destroy();
        Session::forget('coupon');
        $order->update(['refer_bonus' => $total_refer]);

        // Dispatch SMS confirmation after 5 minutes
        if (!empty($order->phone)) {
            SendOrderConfirmationSms::dispatch($order->id)->delay(now()->addMinutes(5));
        }

        $data = $this->prepareOrderData($order, $request);

        return $this->handlePaymentMethod($request, $data, $total ?? $cart_subtotal + $shipping_charge, $order);
    }

    /**
     * Store guest order
     */
    public function orderStore_guest(Request $request)
    {
        $phoneMinDigits = empty(setting('phone_min_dgt')) ? 11 : setting('phone_min_dgt');
        $phoneMaxDigits = empty(setting('phone_max_dgt')) ? 11 : setting('phone_max_dgt');

        $this->validate($request, [
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'nullable|string|max:255',
            'company'         => 'nullable|string|max:255',
            'country'         => 'required|string|max:255',
            'address'         => 'required|string|max:255',
            'city'            => 'required|string|max:255',
            'district'        => 'required|string|max:255',
            'postcode'        => 'nullable|string|max:255',
            'phone'           => 'required|string|max:' . $phoneMaxDigits . '|min:' . $phoneMinDigits,
            'email'           => 'nullable|email|string|max:255',
            'shipping_method' => 'nullable|string|max:255',
            'payment_method'  => 'required|string|max:255',
            'mobile_number'   => 'nullable|string|max:255',
            'transaction_id'  => 'nullable|string|max:255',
            'bank_name'       => 'nullable|string|max:255',
            'account_number'  => 'nullable|string|max:255',
            'holder_name'     => 'nullable|string|max:255',
            'branch'          => 'nullable|string|max:255',
            'routing'         => 'nullable|string|max:255',
             // Added validation for cart-specific fields if they exist
            'seller_count'    => 'sometimes|required|integer|min:1',
            'stotal'          => 'sometimes|required|numeric|min:0',
        ]);

        // Check order interval for guests
        $email = $request->email ?: 'noreply@lems.shop';

        if ($this->checkGuestOrderInterval($request->phone, $email)) {
            $remainingSeconds = $this->getRemainingTime($request->phone, $email);
            $remainingMinutes = ceil($remainingSeconds / 60);
            $whatsappNumber = setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX';

            notify()->warning("Please wait! You have already placed an order. You can order again after {$remainingMinutes} minutes. This is to prevent fake orders. For any changes to your order, please knock us on our WhatsApp {$whatsappNumber}.", "Order Restriction");
            return redirect()->back();
        }

        // Use stotal and seller_count passed from the form (validated above)
        $cart_subtotal = $request->stotal ?? 0; // Default to 0 if not provided
        $seller_count = $request->seller_count ?? 1; // Default to 1 if not provided

        $shipping_charge = $this->calculateShippingChargeByCity($cart_subtotal, $request->city, $seller_count);
        $single_charge = $this->calculateSingleChargeByCity($cart_subtotal, $request->city);

        // Rest of the logic is similar to orderStore_minimal
        return $this->processGuestOrder($request, $shipping_charge, $single_charge, $cart_subtotal);
    }

    /**
     * Store customer order
     */
    public function orderStore(Request $request)
    {
        $phoneMinDigits = empty(setting('phone_min_dgt')) ? 11 : setting('phone_min_dgt');
        $phoneMaxDigits = empty(setting('phone_max_dgt')) ? 11 : setting('phone_max_dgt');

        $this->validate($request, [
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'nullable|string|max:255',
            'company'         => 'nullable|string|max:255',
            'country'         => 'required|string|max:255',
            'address'         => 'required|string|max:255',
            'city'            => 'required|string|max:255',
            'district'        => 'required|string|max:255',
            'postcode'        => 'nullable|string|max:255',
            'phone'           => 'required|string|max:' . $phoneMaxDigits . '|min:' . $phoneMinDigits,
            'email'           => 'required|email|string|max:255',
            'shipping_method' => 'nullable|string|max:255',
            'payment_method'  => 'required|string|max:255',
            'mobile_number'   => 'nullable|string|max:255',
            'transaction_id'  => 'nullable|string|max:255',
            'bank_name'       => 'nullable|string|max:255',
            'account_number'  => 'nullable|string|max:255',
            'holder_name'     => 'nullable|string|max:255',
            'branch'          => 'nullable|string|max:255',
            'routing'         => 'nullable|string|max:255',
            // Added validation for cart-specific fields if they exist
            'seller_count'    => 'sometimes|required|integer|min:1',
            'stotal'          => 'sometimes|required|numeric|min:0',
        ]);

        // Use stotal and seller_count passed from the form (validated above)
        $cart_subtotal = $request->stotal ?? 0; // Default to 0 if not provided
        $seller_count = $request->seller_count ?? 1; // Default to 1 if not provided

        $shipping_charge = $this->calculateShippingChargeByCity($cart_subtotal, $request->city, $seller_count);
        $single_charge = $this->calculateSingleChargeByCity($cart_subtotal, $request->city);

        $coupon_code = '';
        $discount = 0;
        $total = $cart_subtotal + $shipping_charge;

        if (Session::has('coupon')) {
            $coupon_code = Session::get('coupon')['name'];
            $discount = Session::get('coupon')['discount'];
            $total = ($cart_subtotal + $shipping_charge) - $discount;
        }

        $wl = Session::has('coupon') ? $total : $cart_subtotal + $shipping_charge;

        // Wallet payment validation
        if ($request->payment_method == 'wallate') {
             if (!auth()->check() || $wl > auth()->user()->wallate) { // Added auth check
                notify()->warning("don't have enough balance in wallate", "Warning");
                return redirect()->back();
            } else {
                $user = User::find(auth()->id());
                $user->wallate = $user->wallate - $wl;
                $user->update();
            }
        }

        if ($request->partial_paid > 0) {
             if (!auth()->check() || $request->partial_paid > auth()->user()->wallate) { // Added auth check
                notify()->warning("don't have enough balance in wallate", "Warning");
                return redirect()->back();
            } else {
                $user = User::find(auth()->id());
                $user->wallate = $user->wallate - $request->partial_paid;
                $user->update();
            }
        }

        $order = Order::create([
            'user_id'         => auth()->id(),
            'refer_id'        => auth()->user()->refer,
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'company_name'    => $request->company,
            'country'         => $request->country,
            'address'         => $request->address,
            'town'            => $request->city,
            'district'        => $request->district,
            'thana'           => $request->thana,
            'post_code'       => $request->postcode,
            'phone'           => $request->phone,
            'email'           => $request->email,
            'shipping_method' => $request->shipping_method,
            'shipping_charge' => $shipping_charge,
            'single_charge'   => $single_charge,
            'payment_method'  => $request->payment_method,
            'mobile_number'   => $request->mobile_number,
            'transaction_id'  => $request->transaction_id,
            'bank_name'       => $request->bank_name,
            'account_number'  => $request->account_number,
            'holder_name'     => $request->holder_name,
            'branch_name'     => $request->branch,
            'routing_number'  => $request->routing,
            'coupon_code'     => $coupon_code,
            'subtotal'        => $cart_subtotal,
            'discount'        => $discount,
            'is_pre'          => $request->pr ?? 0,
            'total'           => $total,
            'cart_type'       => 1,
        ]);

        $this->generateOrderId($order);

        // Process cart items similar to other methods
        $this->processCartItems($order); // This method is now null-safe
        $this->handleWalletPayment($request, $order);
        $this->handlePartialPayment($request, $order);
        $this->handleCouponDiscount($order, $discount);

        Cart::destroy();
        Session::forget('coupon');

        // Dispatch SMS confirmation after 5 minutes
        if (!empty($order->phone)) {
            SendOrderConfirmationSms::dispatch($order->id)->delay(now()->addMinutes(5));
        }

        $data = $this->prepareOrderData($order, $request);
        $cart = CartInfo::where('user_id', auth()->id())->delete();

        return $this->handlePaymentMethod($request, $data, $total ?? $cart_subtotal + $shipping_charge, $order);
    }

    // Helper methods
    private function calculateShippingCharge($stotal, $shipping_range, $seller_count)
    {
         // Ensure seller_count is at least 1
        $seller_count = max(1, $seller_count);

        if ($stotal > setting('shipping_free_above')) {
            return 0;
        }

        if ($shipping_range == 1) {
            return setting('shipping_charge') * $seller_count;
        } else {
            return setting('shipping_charge_out_of_range') * $seller_count;
        }
    }

    private function calculateSingleCharge($stotal, $shipping_range)
    {
        if ($stotal > setting('shipping_free_above')) {
            return 0;
        }

        return ($shipping_range == 1) ? setting('shipping_charge') : setting('shipping_charge_out_of_range');
    }

    private function calculateShippingChargeByCity($stotal, $city, $seller_count)
    {
        // Ensure seller_count is at least 1
        $seller_count = max(1, $seller_count);

        if ($stotal > setting('shipping_free_above')) {
            return 0;
        }

        if ($city == 'Dhaka') {
            return setting('shipping_charge') * $seller_count;
        } else {
            return setting('shipping_charge_out_of_range') * $seller_count;
        }
    }

    private function calculateSingleChargeByCity($stotal, $city)
    {
        if ($stotal > setting('shipping_free_above')) {
            return 0;
        }

        return ($city == 'Dhaka') ? setting('shipping_charge') : setting('shipping_charge_out_of_range');
    }

    private function createOrder($request, $shipping_charge, $single_charge, $coupon_code, $subtotal, $discount, $total)
    {
        return Order::create([
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'company_name'    => $request->company,
            'country'         => $request->country,
            'address'         => $request->address,
            'town'            => $request->city,
            'district'        => $request->district,
            'thana'           => $request->thana,
            'post_code'       => $request->postcode,
            'phone'           => $request->phone,
            'email'           => $request->email,
            'shipping_method' => $request->shipping_method,
            'shipping_charge' => $shipping_charge,
            'single_charge'   => $single_charge,
            'payment_method'  => $request->payment_method,
            'mobile_number'   => $request->mobile_number,
            'transaction_id'  => $request->transaction_id,
            'bank_name'       => $request->bank_name,
            'account_number'  => $request->account_number,
            'holder_name'     => $request->holder_name,
            'branch_name'     => $request->branch,
            'routing_number'  => $request->routing,
            'coupon_code'     => $coupon_code,
            'subtotal'        => $subtotal,
            'discount'        => $discount,
            'is_pre'          => $request->pr ?? 0,
            'total'           => $total,
            'cart_type'       => 1,
        ]);
    }

    private function generateOrderId($order)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $order_id = substr(str_shuffle($chars), 0, 10);

        $order->update([
            'order_id' => $order_id,
            'invoice'  => '#' . str_pad($order->id, 5, 0, STR_PAD_LEFT),
        ]);
    }

    /**
     * --- THIS FUNCTION IS NOW NULL-SAFE ---
     * Calculate grand total with commission
     */
    private function calculateGrandTotal($vendor, $vp)
    {
        // FIX: Check if vendor is null. If so, apply default commission.
        // Assume default commission applies (funds go to admin)
        if (!$vendor) {
            $commissionRate = setting('shop_commission') ?? 0; // Get default commission rate
            $commission = ($commissionRate / 100) * $vp;
            return $vp - $commission; // The 'grand total' from seller perspective is after commission
        }

        // If vendor exists and is admin, no commission deducted for seller
        if ($vendor && $vendor->role_id == 1) {
            return $vp;
        } else {
            // Vendor exists and is not admin
            $commissionRate = $vendor->shop_info->commission ?? setting('shop_commission') ?? 0;
            $commission = ($commissionRate / 100) * $vp;
            return $vp - $commission; // Grand total for seller after commission
        }
    }

    private function updateUserPoints($item, $order)
    {
        $product = Product::find($item->id);
         // Safety check: Ensure product exists before accessing properties
        if (!$product) {
            \Log::warning('Product not found for points update: ID ' . $item->id);
            return;
        }
        if (auth()->check()) { // Use auth()->check() instead of auth()->user() for boolean check
            $userPoint = User::find(auth()->id());
            // Safety check for user
            if ($userPoint) {
                $pointp = $product->point * $item->qty;
                if (setting('is_point') == 1) {
                    $point = $pointp;
                } else {
                    $point = 0;
                }
                $userPoint->pen_point += $point;
                $userPoint->update();
                $order->point += $point;
                 $order->save(); // Save order after updating points
            }
        }
       // $order->save(); // Removed redundant save here
    }

    /**
     * --- THIS FUNCTION IS NOW NULL-SAFE ---
     * Update vendor account and commission
     */
    private function updateVendorAccount($pp, $item, $order, $price)
    {
        // Use $pp directly as it's already the product object
        $product = $pp;
        if ($product) {
            $vendor = User::find($product->user_id); // $vendor can be null
            $vp = $price * $item->qty;

            // FIX: Check if vendor is null. If no vendor, treat as admin sale (vendor_id = 1)
            if (!$vendor || $vendor->role_id == 1) {
                $account = VendorAccount::where('vendor_id', 1)->first();
                if ($account) { // Added safety check for account
                    $account->pending_amount += $vp;
                    $account->save();
                     \Log::info('Admin account updated (No Vendor or Admin Vendor)', ['order_id' => $order->id, 'amount' => $vp]);
                } else {
                     \Log::warning('Admin VendorAccount (ID 1) not found for order: ' . $order->id);
                }
            } else {
                // Vendor exists and is not admin
                $grand_total = $price * $item->qty;

                // Determine commission rate safely
                $commissionRate = $vendor->shop_info->commission ?? setting('shop_commission') ?? 0;
                $commission = ($commissionRate / 100) * $grand_total;
                $amount = $grand_total - $commission; // Amount vendor receives

                // Update Admin Account with commission
                $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                if ($adminAccount) { // Added safety check
                    $adminAccount->increment('pending_amount', $commission); // Use increment for atomicity
                     \Log::info('Admin account updated with commission', ['order_id' => $order->id, 'commission' => $commission]);
                } else {
                     \Log::warning('Admin VendorAccount (ID 1) not found for commission calculation on order: ' . $order->id);
                }

                // Update Vendor Account with their share
                if ($vendor->vendorAccount) { // Added safety check
                     $vendor->vendorAccount()->increment('pending_amount', $amount); // Use increment
                     \Log::info('Vendor account updated', ['vendor_id' => $vendor->id, 'order_id' => $order->id, 'amount' => $amount]);
                } else {
                     \Log::warning('VendorAccount not found for vendor: ' . $vendor->id . ' on order: ' . $order->id);
                }

                // Create or Update Commission Record
                Commission::updateOrCreate(
                    ['user_id' => $product->user_id, 'order_id' => $order->id],
                    ['amount' => DB::raw("amount + $commission"), 'status' => '0'] // Use DB::raw for atomic update
                );
                 \Log::info('Commission record created/updated', ['vendor_id' => $vendor->id, 'order_id' => $order->id, 'commission_added' => $commission]);
            }

            // === UPDATED STOCK REDUCTION LOGIC ===
            $this->reduceProductStock($product, $item);
        } else {
             \Log::warning('Product not found in updateVendorAccount: Item ID ' . $item->id);
        }
    }


    /**
     * Reduce stock for product based on variation type
     * Handles: Color-Size, Size-Only, Attributes, and Simple products
     */
    private function reduceProductStock($product, $item)
    {
        $quantity = $item->qty;

        // --- FIX: Remove json_decode() ---
        // $sizeData = json_decode($item->options->attributes, true);
        // Directly use the attributes, assuming they are already an array/object
        $sizeData = $item->options->attributes ?? []; // Use null coalescing for safety

        $colorId = $item->options->color ?? null;

        $stockReduced = false; // Flag to track if variation stock was reduced

        // Priority 1: Check for Color-Size variation
        // Check if $sizeData is an array or an object before trying to iterate
        if ($colorId && $colorId !== 'blank' && (is_array($sizeData) || is_object($sizeData)) && !empty($sizeData)) {
            foreach ($sizeData as $attributeSlug => $attributeValueId) {
                 // Assuming size attribute slug is 'size', adjust if different
                if ($attributeSlug === 'size') {
                    // Reduce from color_size_product table
                    DB::table('color_size_product')
                        ->where('product_id', $product->id)
                        ->where('color_id', $colorId) // Use the actual color ID if stored, otherwise need lookup
                        ->where('size_id', $attributeValueId) // Assuming value is the size_id
                        ->decrement('quantity', $quantity);

                    \Log::info('Stock reduced: Color-Size', [
                        'product_id' => $product->id,
                        'color_id' => $colorId, // Log the color slug/ID used
                        'size_id' => $attributeValueId,
                        'quantity' => $quantity
                    ]);
                    $stockReduced = true;
                    break; // Only reduce once per cart item for size
                }
            }
        }
        // Priority 2: Check for Size-Only variation (no color)
        elseif ((empty($colorId) || $colorId === 'blank') && (is_array($sizeData) || is_object($sizeData)) && !empty($sizeData)) {
             foreach ($sizeData as $attributeSlug => $attributeValueId) {
                // Assuming size attribute slug is 'size', adjust if different
                if ($attributeSlug === 'size') {
                    // Reduce from color_size_product table where color_id IS NULL
                    DB::table('color_size_product')
                        ->where('product_id', $product->id)
                        ->where('size_id', $attributeValueId) // Assuming value is the size_id
                        ->whereNull('color_id')
                        ->decrement('quantity', $quantity);

                    \Log::info('Stock reduced: Size-Only', [
                        'product_id' => $product->id,
                        'size_id' => $attributeValueId,
                        'quantity' => $quantity
                    ]);
                     $stockReduced = true;
                    break; // Only reduce once per cart item for size
                }
            }
        }
        // Priority 3: Check for Attribute variation (other than size/color handled above)
        if (!$stockReduced && (is_array($sizeData) || is_object($sizeData)) && !empty($sizeData)) {
             foreach ($sizeData as $attributeSlug => $attributeValueId) {
                  // Skip if it's the size attribute handled above
                 if ($attributeSlug === 'size') continue;

                 // Reduce from attribute_product table
                DB::table('attribute_product')
                    ->where('product_id', $product->id)
                    ->where('attribute_value_id', $attributeValueId)
                    ->decrement('qnty', $quantity);

                \Log::info('Stock reduced: Attribute', [
                    'product_id' => $product->id,
                    'attribute_value_id' => $attributeValueId,
                    'quantity' => $quantity
                ]);
                 $stockReduced = true; // Mark as reduced even for attributes
                 // Don't break here, allow reducing stock for multiple attributes if necessary
            }
        }

        // Always reduce from main product quantity (for all product types)
        // Check if stock hasn't already been reduced at a variation level
        // (Optional: Depends if main quantity tracks total or just base product without variations)
        // if (!$stockReduced) { // Only decrement main if no variation stock was touched
            $product->decrement('quantity', $quantity);

            \Log::info('Stock reduced: Main product quantity', [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'remaining_stock' => $product->fresh()->quantity // Use fresh() to get updated value
            ]);
        // }
    }


    private function createMultiOrder($usids, $orderId, $single_charge)
    {
         // Ensure $usids is an array and filter out potential nulls or empty values explicitly
         $valid_usids = array_filter((array)$usids, function($id) {
            return $id !== null && $id !== '';
        });

        // Add admin (ID 1) if there are items without a seller ID OR if $usids was empty
         $hasItemsWithoutSeller = Cart::content()->contains(function ($item) {
            $product = Product::find($item->id);
            return !$product || $product->user_id === null;
         });

         if ($hasItemsWithoutSeller || empty($valid_usids)) {
             if(!in_array(1, $valid_usids)) { // Add admin only if not already present
                $valid_usids[] = 1;
             }
         }

        foreach ($valid_usids as $seller_id) {
             // Calculate total for this specific seller_id OR null seller_id items assigned to admin (1)
             $total = DB::table('order_details')
                        ->where('order_id', $orderId)
                        // If current seller_id is 1 (admin), sum items where seller_id was originally null
                        ->when($seller_id == 1, function ($query) {
                            $query->whereNull('seller_id');
                        },
                        // Otherwise, sum items for the specific seller_id
                        function ($query) use ($seller_id) {
                            $query->where('seller_id', $seller_id);
                        })
                        ->sum('total_price');

            // Add shipping charge only once if applicable, typically to the first record or admin's record
            // For simplicity, let's add it to every multi_order record for now.
            // A more complex logic might distribute it or add it only to the admin's share.
            $total += $single_charge;

            // Avoid inserting if total is zero (e.g., only free items or calculation error)
            if ($total > 0) {
                 DB::table('multi_order')->insert([
                    'vendor_id' => $seller_id, // Use the actual seller_id (or 1 for admin/null)
                    'order_id' => $orderId,
                    'partial_pay' => 0,
                    'status' => 0,
                    'total' => $total
                ]);
            } else {
                 \Log::warning('Skipping multi_order insert for vendor/admin ID ' . $seller_id . ' on order ID ' . $orderId . ' because total is zero.');
            }
        }
    }


    private function handleWalletPayment($request, $order)
    {
        if ($request->payment_method == 'wallate') {
            $order->update([
                'pay_staus' => 1,
                'pay_date'  => date('d-m-y'), // Consider using Carbon::now() for consistency
            ]);
        }
    }

    private function handlePartialPayment($request, $order)
    {
        // Ensure user is authenticated and has sufficient balance
        if (auth()->check() && $request->partial_paid > 0 && $request->partial_paid <= auth()->user()->wallate) {
            $parts = DB::table('multi_order')->where('order_id', $order->id)->get();
            $amount = $request->partial_paid;

            foreach ($parts as $part) {
                if ($amount <= 0) break; // Stop if amount is fully allocated

                $payable = $part->total - $part->partial_pay - ($part->discount ?? 0); // Consider discount
                if ($payable > 0) {
                    $slice = min($amount, $payable); // Allocate the minimum of remaining amount or payable amount

                    DB::table('multi_order')->where('id', $part->id)->increment('partial_pay', $slice);
                    $amount -= $slice;
                }
            }

            // Record the partial payment only if some amount was actually used
             if ($request->partial_paid > $amount) { // Check if any amount was allocated
                 PartialPayment::create([
                    'order_id' => $order->id,
                    'user_id' => auth()->id(), // Add user_id for tracking
                    'payment_method' => 'wall', // Assuming 'wall' is wallet
                    'amount' => $request->partial_paid - $amount, // Record the amount actually used
                    'status' => 1, // Assuming 1 means successful
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                 // Deduct from user's wallet (ensure this happens only once)
                // Let's assume the wallet was already deducted in the main orderStore method.
                 \Log::info('Partial payment applied from wallet.', ['order_id' => $order->id, 'amount_applied' => $request->partial_paid - $amount]);
             }

             if ($amount > 0) {
                // This means the partial payment was more than the total due, which shouldn't happen if wallet balance check is correct. Log a warning.
                 \Log::warning('Partial payment amount exceeded total due.', ['order_id' => $order->id, 'excess_amount' => $amount]);
                // Optionally, refund the excess amount here.
            }
        } elseif ($request->partial_paid > 0) {
            // Log if partial payment was attempted without auth or sufficient funds
             \Log::warning('Partial payment skipped due to insufficient funds or user not authenticated.', ['order_id' => $order->id, 'attempted_amount' => $request->partial_paid]);
        }
    }


    private function handleCouponDiscount($order, $discount)
    {
        if (Session::has('coupon') && $discount > 0) {
            $n_parts = DB::table('multi_order')->where('order_id', $order->id)->get();
            $n_amount = $discount; // Total discount to distribute

            // Calculate the total payable amount across all parts BEFORE discount
             $totalPayableBeforeDiscount = $n_parts->sum(function ($part) {
                return $part->total - $part->partial_pay; // Consider payments already made
            });

             if ($totalPayableBeforeDiscount <= 0) return; // No need to apply discount if nothing is payable

            foreach ($n_parts as $n_part) {
                if ($n_amount <= 0) break; // Stop if discount is fully distributed

                $partPayable = $n_part->total - $n_part->partial_pay;
                if ($partPayable <= 0) continue; // Skip parts already fully paid

                // Calculate proportional discount for this part
                $proportion = $partPayable / $totalPayableBeforeDiscount;
                 $discountSlice = min($n_amount, round($discount * $proportion, 2)); // Allocate proportionally, ensure it doesn't exceed remaining discount

                // Ensure discount doesn't make the part negative
                 $discountSlice = min($discountSlice, $partPayable);

                 if ($discountSlice > 0) {
                    DB::table('multi_order')->where('id', $n_part->id)->increment('discount', $discountSlice);
                    $n_amount -= $discountSlice;
                }
            }

            // If there's any remaining discount due to rounding, apply it to the first part that can take it
            if ($n_amount > 0.005) { // Use a small threshold for floating point comparison
                foreach ($n_parts as $n_part) {
                    $partStillPayable = $n_part->total - $n_part->partial_pay - ($n_part->discount ?? 0); // Check again after initial distribution
                    if ($partStillPayable > 0) {
                         $finalSlice = min($n_amount, $partStillPayable);
                         DB::table('multi_order')->where('id', $n_part->id)->increment('discount', $finalSlice);
                         \Log::info('Applied remaining coupon discount due to rounding.', ['order_id' => $order->id, 'multi_order_id' => $n_part->id, 'amount' => $finalSlice]);
                        break;
                    }
                }
            }
        }
    }


    private function prepareOrderData($order, $request)
    {
         // Eager load orderDetails to avoid N+1 queries if accessing it later
         $order->load('orderDetails');

        return [
            'order_id'        => $order->order_id,
            'invoice'         => $order->invoice,
            'name'            => $request->first_name . ' ' . $request->last_name, // Combine names
            'email'           => $request->email ?? $order->email, // Use order email as fallback
            'address'         => $request->address ?? $order->address, // Use order address as fallback
            'coupon_code'     => $order->coupon_code,
            'subtotal'        => $order->subtotal,
            'shipping_charge' => $order->shipping_charge,
            'discount'        => $order->discount,
            'total'           => $order->total,
            'date'            => $order->created_at,
            'payment_method'  => $order->payment_method,
            'pay_status'      => $order->pay_staus,
            'pay_date'        => $order->pay_date,
            'orderDetails'    => $order->orderDetails, // Already loaded
            'phone'           => $request->phone ?? $order->phone, // Use order phone as fallback
        ];
    }


    private function handlePaymentMethod($request, $data, $amount, $order)
    {
         // Ensure amount is valid
         $amount = max(0, $amount); // Prevent negative amounts

        if ($request->payment_method == 'aamarpay') {
            return $this->processAamarpay($request, $amount, $order->id);
        } elseif ($request->payment_method == 'uddoktapay') {
            $url = $this->processUddoktapay($request, $amount, $order->id);
            if ($url) {
                return Redirect::to($url);
            } else {
                 // Handle failure to get payment URL
                 notify()->error('Failed to initiate UddoktaPay payment. Please try again or choose another method.', 'Payment Error');
                 // Optionally: Revert stock, cancel order parts, etc.
                 return redirect()->route('checkout'); // Redirect back to checkout
            }
        } else {
             // For non-gateway methods (COD, Bank, etc.) or if gateway fails before redirect
            try {
                 // Send confirmation email
                if (setting('mail_config') == 1 && !empty($data['email']) && $data['email'] !== 'noreply@lems.shop') {
                    Mail::send('frontend.invoice-mail', $data, function ($mail) use ($data) {
                        $mail->from(config('mail.from.address'), config('app.name'))
                            ->to($data['email'], $data['name'])
                            ->subject('Your Order Invoice - #' . $data['invoice']);
                    });
                }
            } catch (\Exception $e) {
                 \Log::error('Failed to send order confirmation email: ' . $e->getMessage(), ['order_id' => $order->id]);
                 // Don't fail the order placement, just log the email error
            }
             // Show success page
            return view('frontend.order_success', compact('data'));
        }
    }

    /**
     * --- THIS FUNCTION IS NOW NULL-SAFE ---
     */
    private function processGuestOrder($request, $shipping_charge, $single_charge, $cart_subtotal)
    {
        // $cart_subtotal is now passed as an argument
        $coupon_code = '';
        $discount = 0;
        $total = $cart_subtotal + $shipping_charge;

        if (Session::has('coupon')) {
            $coupon_code = Session::get('coupon')['name'];
            $discount = Session::get('coupon')['discount'];
            $total = ($cart_subtotal + $shipping_charge) - $discount;
        }

        $order = Order::create([
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'company_name'    => $request->company,
            'country'         => $request->country,
            'address'         => $request->address,
            'town'            => $request->city,
            'district'        => $request->district,
            'thana'           => $request->thana,
            'post_code'       => $request->postcode,
            'phone'           => $request->phone,
            'email'           => $request->email ?? 'noreply@lems.shop', // Ensure email has a default
            'shipping_method' => $request->shipping_method,
            'shipping_charge' => $shipping_charge,
            'single_charge'   => $single_charge,
            'payment_method'  => $request->payment_method,
            'mobile_number'   => $request->mobile_number,
            'transaction_id'  => $request->transaction_id,
            'bank_name'       => $request->bank_name,
            'account_number'  => $request->account_number,
            'holder_name'     => $request->holder_name,
            'branch_name'     => $request->branch,
            'routing_number'  => $request->routing,
            'coupon_code'     => $coupon_code,
            'subtotal'        => $cart_subtotal,
            'discount'        => $discount,
            'is_pre'          => $request->pr ?? 0,
            'total'           => $total,
            'cart_type'       => 1, // Assuming 1 is for regular cart orders
        ]);

        $this->generateOrderId($order);

        $total_refer = 0;
        $usids = [];

        foreach (Cart::content() as $item) {
             $pp = Product::find($item->id);
             // Ensure $pp is not null before proceeding
             if (!$pp) {
                \Log::warning('Product not found in GUEST cart processing: ID ' . $item->id);
                continue; // Skip this item if product doesn't exist
            }

            if (!in_array("$pp->user_id", $usids)) {
                $usids[] = $pp->user_id; // user_id can be null here
            }

            $total_refer += (($item->price / 100) * $item->qty); // Be careful if price is 0
            $price = ($item->qty >= 6 && $pp->whole_price > 0) ? $pp->whole_price : $item->price;

            $vendor = User::find($pp->user_id); // Vendor can be null
            $vp = $price * $item->qty;
            $gt = $this->calculateGrandTotal($vendor, $vp); // Handles null vendor

            // --- START: VARIATION DATA FIX (Location 3: processGuestOrder) ---
            $attributes = $item->options->attributes ?? [];
            $colorValue = $item->options->color ?? 'blank';
            $sizeJsonData = null;

            if (!empty($attributes) && is_array($attributes)) {
                if (isset($attributes['size'])) {
                    $sizeId = $attributes['size'];
                    $sizeJsonData = json_encode(['size_id' => $sizeId]);
                } else {
                    $attributeData = [];
                    foreach ($attributes as $attrSlug => $attrValueId) {
                        $attributeData[] = ['attribute_value_id' => $attrValueId];
                    }
                    $sizeJsonData = json_encode($attributeData);
                }
            } else {
                $sizeJsonData = json_encode([]);
            }
            // --- END: VARIATION DATA FIX ---

            $order->orderDetails()->create([
                'product_id'  => $item->id,
                'seller_id'   => $pp->user_id, // Can be null
                'title'       => $item->name,
                'color'       => $colorValue,
                'size'        => $sizeJsonData, // CORRECTED: Use transformed JSON data
                'qty'         => $item->qty,
                'price'       => $price,
                'total_price' => $price * $item->qty,
                'g_total'     => $gt
            ]);

            // Points update only happens for logged-in users, so skip here for guests
            // $this->updateUserPoints($item, $order);

            // Vendor account update handles null vendor
            $this->updateVendorAccount($pp, $item, $order, $price);
        }

        $this->createMultiOrder($usids, $order->id, $single_charge);

        // Wallet and partial payments are not applicable for guests
        // $this->handleWalletPayment($request, $order);
        // $this->handlePartialPayment($request, $order);

        $this->handleCouponDiscount($order, $discount); // Apply coupon if any

        Cart::destroy();
        Session::forget('coupon');
        $order->update(['refer_bonus' => $total_refer]);

        // Dispatch SMS confirmation after 5 minutes
        if (!empty($order->phone)) {
            SendOrderConfirmationSms::dispatch($order->id)->delay(now()->addMinutes(5));
        }

        $data = $this->prepareOrderData($order, $request);

        return $this->handlePaymentMethod($request, $data, $total ?? $cart_subtotal + $shipping_charge, $order);
    }


    /**
     * --- THIS FUNCTION IS NOW NULL-SAFE ---
     */
    private function processCartItems($order)
    {
        $total_refer = 0;
        $usids = [];

        foreach (Cart::content() as $item) {
             $pp = Product::find($item->id);
             // Ensure $pp is not null before proceeding
             if (!$pp) {
                \Log::warning('Product not found in AUTH cart processing: ID ' . $item->id);
                continue; // Skip this item if product doesn't exist
            }

            // user_id can be null
            if (!in_array($pp->user_id, $usids)) {
                $usids[] = $pp->user_id;
            }

            $total_refer += (($item->price / 100) * $item->qty); // Be careful if price is 0
             $price = ($item->qty >= 6 && $pp->whole_price > 0) ? $pp->whole_price : $item->price;

             $vendor = User::find($pp->user_id); // Vendor can be null
             $vp = $price * $item->qty;
             $gt = $this->calculateGrandTotal($vendor, $vp); // Handles null vendor

             // --- START: VARIATION DATA FIX (Location 2: processCartItems) ---
            $attributes = $item->options->attributes ?? [];
            $colorValue = $item->options->color ?? 'blank';
            $sizeJsonData = null;

            if (!empty($attributes) && is_array($attributes)) {
                if (isset($attributes['size'])) {
                    $sizeId = $attributes['size'];
                    $sizeJsonData = json_encode(['size_id' => $sizeId]);
                } else {
                    $attributeData = [];
                    foreach ($attributes as $attrSlug => $attrValueId) {
                        $attributeData[] = ['attribute_value_id' => $attrValueId];
                    }
                    $sizeJsonData = json_encode($attributeData);
                }
            } else {
                $sizeJsonData = json_encode([]);
            }
            // --- END: VARIATION DATA FIX ---

            $order->orderDetails()->create([
                'product_id'  => $item->id,
                'seller_id'   => $pp->user_id, // Can be null
                'title'       => $item->name,
                'color'       => $colorValue,
                'size'        => $sizeJsonData, // CORRECTED: Use transformed JSON data
                'qty'         => $item->qty,
                'price'       => $price,
                'total_price' => $price * $item->qty,
                'g_total'     => $gt
            ]);

            // Points update only for authenticated users
            $this->updateUserPoints($item, $order);

            // Vendor account update handles null vendor
            $this->updateVendorAccount($pp, $item, $order, $price);
        }

         // Update the order's total refer bonus after processing all items
         $order->update(['refer_bonus' => $total_refer]);

         // Return unique seller IDs including null if present, for createMultiOrder
         // No, createMultiOrder handles adding admin (1) if needed. Just return collected IDs.
         return $usids; // Return the collected user IDs (including nulls)
    }


    private function processAamarpay($request, $amount, $orderId)
    {
         // Ensure amount is at least 1 BDT for most gateways
         if ($amount < 1) {
            \Log::error('AamarPay Error: Amount must be at least 1 BDT.', ['order_id' => $orderId, 'amount' => $amount]);
            notify()->error('Invalid payment amount. Cannot proceed with AamarPay.', 'Payment Error');
            return redirect()->route('checkout'); // Or appropriate error page
        }

        if (setting('amode') == '2') {
            $url = 'https://secure.aamarpay.com/request.php';
        } else {
            $url = 'https://sandbox.aamarpay.com/request.php'; // Ensure sandbox URL is correct
        }

        // Validate required settings
         $store_id = setting('astore');
         $signature_key = setting('akey');
         if(empty($store_id) || empty($signature_key)) {
            \Log::error('AamarPay Error: Store ID or Signature Key is missing in settings.', ['order_id' => $orderId]);
            notify()->error('AamarPay configuration is incomplete. Please contact support.', 'Payment Error');
            return redirect()->route('checkout');
        }


        $fields = array(
            'store_id' => $store_id,
            'amount' => number_format($amount, 2, '.', ''), // Format amount correctly
            'payment_type' => 'VISA', // Usually 'creditcard' or 'mobilebanking', check AamarPay docs
            'currency' => 'BDT',
            'tran_id' => 'LEM' . $orderId . '_' . uniqid(), // Use a more unique transaction ID
            'cus_name' => $request->first_name . ' ' . $request->last_name,
            'cus_email' => $request->email ?? 'customer@example.com', // Provide a default if null
            'cus_add1' => $request->address ?? 'N/A', // Provide default
            'cus_add2' => $request->address ?? '', // Optional, can be empty
            'cus_city' => $request->city ?? 'N/A', // Provide default
            'cus_state' => $request->district ?? $request->city ?? 'N/A', // Use district or city
            'cus_postcode' => $request->postcode ?? '1200', // Provide default
            'cus_country' => $request->country ?? 'Bangladesh', // Provide default
            'cus_phone' => $request->phone,
            'cus_fax' => 'Not Applicable', // Corrected value
            'ship_name' => $request->first_name . ' ' . $request->last_name, // Should match cus_name if not different shipping
            'ship_add1' => $request->address ?? 'N/A',
            'ship_add2' => '',
            'ship_city' => $request->city ?? 'N/A',
            'ship_state' => $request->district ?? $request->city ?? 'N/A',
            'ship_postcode' => $request->postcode ?? '1200',
            'ship_country' => $request->country ?? 'Bangladesh',
            'desc' => 'Order #' . $orderId, // More specific description
            'success_url' => route('success'), // Ensure this route exists and handles POST
            'fail_url' => route('fail'),    // Ensure this route exists and handles POST
            'cancel_url' => route('uddoktapay.cancel'), // Use a named route, maybe create a general 'payment.cancel' route
            'opt_a' => $orderId, // Pass order ID
            'opt_b' => '',
            'opt_c' => '',
            'opt_d' => '',
            'signature_key' => $signature_key
        );

        $fields_string = http_build_query($fields);

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, false); // Usually false for production
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true); // Use POST method
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Set to true in production with proper CA certs
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // Add timeout
             curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Add timeout

            $response = curl_exec($ch);
             $curl_error = curl_error($ch);
             $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

             if ($response === false) {
                 \Log::error('AamarPay cURL Error: ' . $curl_error, ['order_id' => $orderId]);
                 throw new Exception('Payment gateway connection error.');
            }

             \Log::info('AamarPay Request Sent:', ['url' => $url, 'fields' => $fields]); // Log request
             \Log::info('AamarPay Response Received:', ['http_code' => $http_code, 'response' => $response]); // Log response


             // AamarPay typically returns a payment URL path directly if successful
            // Check if response looks like a URL path (e.g., starts with 'payment' or contains '?session')
             if ($http_code == 200 && is_string($response) && (strpos($response, 'payment') !== false || strpos($response, '?session') !== false)) {
                $url_forward = str_replace(['"', '\\'], '', $response); // Clean the response
                $this->redirect_to_merchant($url_forward); // Redirect using the provided function
                 exit; // Stop script execution after redirect header
            } else {
                 // Log the unexpected response
                 \Log::error('AamarPay Error: Unexpected response.', ['order_id' => $orderId, 'http_code' => $http_code, 'response' => $response]);
                 throw new Exception('Invalid response from payment gateway.');
            }

        } catch (Exception $e) {
            \Log::error('AamarPay Payment Initiation Failed: ' . $e->getMessage(), ['order_id' => $orderId]);
            notify()->error('Could not connect to AamarPay. Please try again later or choose another method.', 'Payment Error');
            return redirect()->route('checkout');
        }
    }


    private function processUddoktapay($request, $amount, $orderId)
    {
         // Ensure amount is valid (UddoktaPay might have minimum)
         if ($amount < 1) { // Check UddoktaPay minimum if applicable
            \Log::error('UddoktaPay Error: Invalid amount.', ['order_id' => $orderId, 'amount' => $amount]);
            notify()->error('Invalid payment amount. Cannot proceed with UddoktaPay.', 'Payment Error');
            return null; // Return null to indicate failure
        }

         // Validate required settings
         $api_key = setting('uapi'); // Assuming 'uapi' stores the API key
         $api_url = setting('uddoktapay_url'); // Add setting for UddoktaPay API URL

         if(empty($api_key) || empty($api_url)) {
            \Log::error('UddoktaPay Error: API Key or API URL is missing in settings.', ['order_id' => $orderId]);
            notify()->error('UddoktaPay configuration is incomplete. Please contact support.', 'Payment Error');
            return null;
        }


        $requestData = [
            'full_name'    => $request->first_name . ' ' . $request->last_name,
            'email'        => $request->email ?? 'customer@example.com', // Provide default
            'amount'       => number_format($amount, 2, '.', ''), // Format amount
            'metadata'     => json_encode([ // Metadata should be a JSON string
                'order_id'   => $orderId,
                'customer_phone' => $request->phone, // Example: Add phone to metadata
                // Add other relevant info if needed
            ]),
            'redirect_url'  => route('uddoktapay.success'), // Use GET as per your current setup
            'return_type'   => 'GET', // Make sure this matches your success route method
            'cancel_url'    => route('uddoktapay.cancel'),
            'webhook_url'   => route('uddoktapay.webhook'), // Ensure webhook URL is publicly accessible
        ];

        try {
             // Initialize UddoktaPay library/SDK correctly
             // Assuming your App\Library\UddoktaPay handles the API call
             // Make sure API key and URL are configured within the library or passed here
            \App\Library\UddoktaPay::setApiKey($api_key); // Example: Method to set API key
            \App\Library\UddoktaPay::setApiUrl($api_url); // Example: Method to set API URL

            $paymentUrl = \App\Library\UddoktaPay::init_payment($requestData); // Call the static method

            if (filter_var($paymentUrl, FILTER_VALIDATE_URL)) {
                 \Log::info('UddoktaPay payment initiated successfully.', ['order_id' => $orderId, 'redirect_url' => $paymentUrl]);
                return $paymentUrl; // Return the valid payment URL
            } else {
                 \Log::error('UddoktaPay Error: Invalid payment URL received.', ['order_id' => $orderId, 'response' => $paymentUrl]);
                 notify()->error('Received an invalid response from UddoktaPay.', 'Payment Error');
                 return null;
            }
        } catch (Exception $e) {
             \Log::error('UddoktaPay Payment Initiation Failed: ' . $e->getMessage(), ['order_id' => $orderId, 'request_data' => $requestData]);
            // Use a generic message for the user
            notify()->error('Could not initiate payment via UddoktaPay. Please try again or choose another method.', 'Payment Error');
            return null; // Return null on failure
        }
    }


    function redirect_to_merchant($url_path) // Parameter is likely path, not full URL for AamarPay
    {
        if (setting('amode') == '2') {
            $base = 'https://secure.aamarpay.com/'; // Use HTTPS
        } else {
            $base = 'https://sandbox.aamarpay.com/'; // Use HTTPS for sandbox too
        }

        // Construct the full URL carefully
         $full_url = rtrim($base, '/') . '/' . ltrim($url_path, '/');

         // Validate the URL before redirecting
         if (!filter_var($full_url, FILTER_VALIDATE_URL)) {
            \Log::error('AamarPay Redirect Error: Invalid redirect URL generated.', ['base' => $base, 'path' => $url_path, 'full_url' => $full_url]);
            notify()->error('Error redirecting to payment gateway.', 'Payment Error');
            // Don't output HTML here, redirect back using Laravel's redirect
            // return redirect()->route('checkout'); // Or handle error appropriately
             echo "Invalid Payment URL. Please contact support."; // Basic error for direct output case
             exit;
        }

        // Use Laravel's redirect for cleaner handling if possible
        // If this function is called where Laravel's redirect isn't available, the JS method is okay as fallback
        // return redirect()->away($full_url);

        // Fallback using JavaScript redirect (if not in a context where Laravel redirect works)
        ?>
        <!DOCTYPE html>
        <html xmlns="http://www.w3.org/1999/xhtml">
          <head>
              <meta charset="utf-8">
              <title>Redirecting to AamarPay...</title>
              <script type="text/javascript">
                function redirectToPayment() {
                    // Use window.location.replace for better history handling
                    window.location.replace("<?php echo htmlspecialchars($full_url, ENT_QUOTES, 'UTF-8'); ?>");
                }
              </script>
          </head>
          <body onLoad="redirectToPayment();">
            <p>Redirecting to AamarPay securely... If you are not redirected automatically, <a href="<?php echo htmlspecialchars($full_url, ENT_QUOTES, 'UTF-8'); ?>">click here</a>.</p>
             {{-- Use a POST form if required by the gateway response, but AamarPay usually expects redirect --}}
             {{-- Example POST form (if needed): --}}
             {{-- <form name="redirectpost" method="post" action="<?php echo htmlspecialchars($full_url, ENT_QUOTES, 'UTF-8'); ?>"> --}}
             {{-- Add any required hidden fields here --}}
             {{-- </form> --}}
             {{-- <script> document.forms["redirectpost"].submit(); </script> --}}
          </body>
        </html>
        <?php
         exit; // Ensure script stops after outputting redirect HTML
    }


    private function validate($request, $rules)
    {
        return $request->validate($rules);
    }
}