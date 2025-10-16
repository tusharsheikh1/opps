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
            $query->orWhere(function($q) use ($email, $fiveMinutesAgo) {
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
        ]);

        // Check order interval for guests (users not logged in)
        if (!auth()->check()) {
            $email = $request->email ?: 'noreply@lems.shop';
            
            if ($this->checkGuestOrderInterval($request->phone, $email)) {
                $remainingSeconds = $this->getRemainingTime($request->phone, $email);
                $remainingMinutes = ceil($remainingSeconds / 60);
                $whatsappNumber = setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX';
                
                notify()->warning("অপেক্ষা করুন! আপনি ইতিমধ্যে একটা অর্ডার করেছেন। আপনি {$remainingMinutes} মিনিট পর আবার অর্ডার করতে পারবেন। এটি ভুয়া অর্ডার প্রতিরোধের জন্য। অর্ডারের যেকোন পরিবর্তনের জন্য আমাদের WhatsApp {$whatsappNumber} এ নক করুন।", "অর্ডার সীমাবদ্ধতা");
                return redirect()->back();
            }
        }

        $seller_count = $request->seller_count;
        $shipping_charge = $this->calculateShippingCharge($request->stotal, $request->shipping_range, $seller_count);
        $single_charge = $this->calculateSingleCharge($request->stotal, $request->shipping_range);

        $cart_subtotal = $request->stotal;
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
            if (!in_array("$pp->user_id", $usids)) {
                $usids[] = $pp->user_id;
            }

            $total_refer += (($item->price / 100) * $item->qty);
            $price = ($item->qty >= 6 && $pp->whole_price > 0) ? $pp->whole_price : $item->price;
            
            $vendor = User::find($pp->user_id);
            $vp = $price * $item->qty;
            $gt = $this->calculateGrandTotal($vendor, $vp);

            $order->orderDetails()->create([
                'product_id'  => $item->id,
                'seller_id'   => $pp->user_id,
                'title'       => $item->name,
                'color'       => $item->options->color,
                'size'        => json_encode($item->options->attributes),
                'qty'         => $item->qty,
                'price'       => $price,
                'total_price' => $price * $item->qty,
                'g_total'     => $gt
            ]);

            $this->updateUserPoints($item, $order);
            $this->updateVendorAccount($pp, $item, $order, $price);
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
        ]);

        // Check order interval for guests
        $email = $request->email ?: 'noreply@lems.shop';
        
        if ($this->checkGuestOrderInterval($request->phone, $email)) {
            $remainingSeconds = $this->getRemainingTime($request->phone, $email);
            $remainingMinutes = ceil($remainingSeconds / 60);
            $whatsappNumber = setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX';
            
            notify()->warning("অপেক্ষা করুন! আপনি ইতিমধ্যে একটা অর্ডার করেছেন। আপনি {$remainingMinutes} মিনিট পর আবার অর্ডার করতে পারবেন। এটি ভুয়া অর্ডার প্রতিরোধের জন্য। অর্ডারের যেকোন পরিবর্তনের জন্য আমাদের WhatsApp {$whatsappNumber} এ নক করুন।", "অর্ডার সীমাবদ্ধতা");
            return redirect()->back();
        }

        // Similar logic as orderStore_minimal but with Dhaka-based shipping
        $seller_count = $request->seller_count;
        $shipping_charge = $this->calculateShippingChargeByCity($request->stotal, $request->city, $seller_count);
        $single_charge = $this->calculateSingleChargeByCity($request->stotal, $request->city);

        // Rest of the logic is similar to orderStore_minimal
        return $this->processGuestOrder($request, $shipping_charge, $single_charge);
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
        ]);

        $seller_count = $request->seller_count;
        $shipping_charge = $this->calculateShippingChargeByCity($request->stotal, $request->city, $seller_count);
        $single_charge = $this->calculateSingleChargeByCity($request->stotal, $request->city);

        $cart_subtotal = $request->stotal;
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
            if ($wl > auth()->user()->wallate) {
                notify()->warning("don't have enough balance in wallate", "Warning");
                return redirect()->back();
            } else {
                $user = User::find(auth()->id());
                $user->wallate = $user->wallate - $wl;
                $user->update();
            }
        }

        if ($request->partial_paid > 0) {
            if ($request->partial_paid > auth()->user()->wallate) {
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
        $this->processCartItems($order);
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

    private function calculateGrandTotal($vendor, $vp)
    {
        if ($vendor->role_id == 1) {
            return $vp;
        } else {
            if ($vendor->shop_info->commission == NULL) {
                $commission = (setting('shop_commission') / 100) * $vp;
                return $vp - $commission;
            } else {
                $commission = ($vendor->shop_info->commission / 100) * $vp;
                return $vp - $commission;
            }
        }
    }

    private function updateUserPoints($item, $order)
    {
        $product = Product::find($item->id);
        if (auth()->user()) {
            $userPoint = User::find(auth()->id());
            $pointp = $product->point * $item->qty;
            if (setting('is_point') == 1) {
                $point = $pointp;
            } else {
                $point = 0;
            }
            $userPoint->pen_point += $point;
            $userPoint->update();
            $order->point += $point;
        }
        $order->save();
    }

    private function updateVendorAccount($pp, $item, $order, $price)
    {
        $product = Product::find($item->id);
        if ($product) {
            $vendor = User::find($product->user_id);
            $vp = $price * $item->qty;
            
            if ($vendor->role_id == 1) {
                $account = VendorAccount::where('vendor_id', 1)->first();
                $account->pending_amount += $vp;
                $account->save();
            } else {
                $grand_total = $price * $item->qty;

                if ($vendor->shop_info->commission == NULL) {
                    $commission = (setting('shop_commission') / 100) * $grand_total;
                    $amount = $grand_total - $commission;
                } else {
                    $commission = ($vendor->shop_info->commission / 100) * $grand_total;
                    $amount = $grand_total - $commission;
                }
                
                $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                $adminAccount->update([
                    'pending_amount' => $adminAccount->pending_amount + $commission
                ]);

                $vendor->vendorAccount()->update([
                    'pending_amount' => $vendor->vendorAccount->pending_amount + $amount
                ]);

                $check = Commission::where('user_id', $product->user_id)->where('order_id', $order->id)->first();
                if (!$check) {
                    Commission::create([
                        'user_id'  => $product->user_id,
                        'order_id' => $order->id,
                        'amount'   => $commission,
                        'status'   => '0',
                    ]);
                } else {
                    $check->amount = $check->amount + $commission;
                    $check->update();
                }
            }
            
            $product->quantity = $product->quantity - $item->qty;
            $product->save();
        }
    }

    private function createMultiOrder($usids, $orderId, $single_charge)
    {
        foreach ($usids as $seller) {
            $total = DB::table('order_details')->where('seller_id', $seller)->where('order_id', $orderId)->sum('total_price');
            $total += $single_charge;
            DB::table('multi_order')->insert([
                'vendor_id' => $seller, 
                'order_id' => $orderId, 
                'partial_pay' => 0, 
                'status' => 0, 
                'total' => $total
            ]);
        }
    }

    private function handleWalletPayment($request, $order)
    {
        if ($request->payment_method == 'wallate') {
            $order->update([
                'pay_staus' => 1,
                'pay_date'  => date('d-m-y'),
            ]);
        }
    }

    private function handlePartialPayment($request, $order)
    {
        if (auth()->user() && $request->partial_paid < auth()->user()->wallate && $request->partial_paid > 0) {
            $parts = DB::table('multi_order')->where('order_id', $order->id)->get();
            $amount = $request->partial_paid;
            
            foreach ($parts as $part) {
                if ($amount > 0) {
                    if ($part->partial_pay != $part->total) {
                        $total_requested = $part->partial_pay + $amount;

                        if ($total_requested > $part->total) {
                            $new_balance = $total_requested - $part->total;
                            $slice = $amount - $new_balance;
                            $amount -= $slice;
                        } else {
                            $slice = $amount;
                            $amount -= $slice;
                        }

                        DB::table('multi_order')->where('id', $part->id)->update(['partial_pay' => $part->partial_pay + $slice]);
                    }
                }
            }
            
            PartialPayment::create([
                'order_id' => $order->id,
                'payment_method' => 'wall',
                'amount' => $request->partial_paid,
                'status' => 1,
            ]);
        }
    }

    private function handleCouponDiscount($order, $discount)
    {
        if (Session::has('coupon')) {
            $n_parts = DB::table('multi_order')->where('order_id', $order->id)->get();
            $n_amount = $discount;
            
            foreach ($n_parts as $n_part) {
                if ($n_amount > 0) {
                    if ($n_part->partial_pay != $n_part->total) {
                        $n_total_requested = $n_part->discount + $n_amount;

                        if ($n_total_requested > $n_part->total) {
                            $n_new_balance = $n_total_requested - $n_part->total;
                            $n_slice = $n_amount - $n_new_balance;
                            $n_amount -= $n_slice;
                        } else {
                            $n_slice = $n_amount;
                            $n_amount -= $n_slice;
                        }

                        DB::table('multi_order')->where('id', $n_part->id)->update(['discount' => $n_part->partial_pay + $n_slice]);
                    }
                }
            }
        }
    }

    private function prepareOrderData($order, $request)
    {
        return [
            'order_id'        => $order->order_id,
            'invoice'         => $order->invoice,
            'name'            => $request->first_name,
            'email'           => $request->email,
            'address'         => $request->address,
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
            'phone'           => $request->phone,
        ];
    }

    private function handlePaymentMethod($request, $data, $amount, $order)
    {
        if ($request->payment_method == 'aamarpay') {
            return $this->processAamarpay($request, $amount, $order->id);
        } elseif ($request->payment_method == 'uddoktapay') {
            $url = $this->processUddoktapay($request, $amount, $order->id);
            if ($url) {
                return Redirect::to($url);
            }
        } else {
            return view('frontend.order_success', compact('data'));
        }
    }

    private function processGuestOrder($request, $shipping_charge, $single_charge)
    {
        $cart_subtotal = $request->stotal;
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

        $total_refer = 0;
        $usids = [];
        
        foreach (Cart::content() as $item) {
            $pp = Product::find($item->id);
            if (!in_array("$pp->user_id", $usids)) {
                $usids[] = $pp->user_id;
            }

            $total_refer += (($item->price / 100) * $item->qty);
            if ($item->qty >= 6 && $pp->whole_price > 0) {
                $price = $pp->whole_price;
            } else {
                $price = $item->price;
            }
            $vendor = User::find($pp->user_id);
            $vp = $price * $item->qty;
            if ($vendor->role_id == 1) {
                $gt = $vp;
            } else {
                if ($vendor->shop_info->commission == NULL) {
                    $commission = (setting('shop_commission') / 100) * $vp;
                    $gt = $vp - $commission;
                } else {
                    $commission = ($vendor->shop_info->commission / 100) * $vp;
                    $gt = $vp - $commission;
                }
            }
            
            $order->orderDetails()->create([
                'product_id'  => $item->id,
                'seller_id'   => $pp->user_id,
                'title'       => $item->name,
                'color'       => $item->options->color,
                'size'        => json_encode($item->options->attributes),
                'qty'         => $item->qty,
                'price'       => $price,
                'total_price' => $price * $item->qty,
                'g_total'     => $gt
            ]);

            $product = Product::find($item->id);

            if (auth()->user()) {
                $userPoint = User::find(auth()->id());
                $pointp = $product->point * $item->qty;
                if (setting('is_point') == 1) {
                    $point = $pointp;
                } else {
                    $point = 0;
                }
                $userPoint->pen_point += $point;
                $userPoint->update();
                $order->point += $point;
            }

            $order->save();
            
            if ($product) {
                $vendor = User::find($product->user_id);
                if ($vendor->role_id == 1) {
                    $account = VendorAccount::where('vendor_id', 1)->first();
                    $account->pending_amount += $vp;
                    $account->save();
                } else {
                    $grand_total = $price * $item->qty;

                    if ($vendor->shop_info->commission == NULL) {
                        $commission = (setting('shop_commission') / 100) * $grand_total;
                        $amount = $grand_total - $commission;
                    } else {
                        $commission = ($vendor->shop_info->commission / 100) * $grand_total;
                        $amount = $grand_total - $commission;
                    }
                    
                    $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                    $adminAccount->update([
                        'pending_amount' => $adminAccount->pending_amount + $commission
                    ]);

                    $vendor->vendorAccount()->update([
                        'pending_amount' => $vendor->vendorAccount->pending_amount + $amount
                    ]);

                    $check = Commission::where('user_id', $product->user_id)->where('order_id', $order->id)->first();
                    if (!$check) {
                        Commission::create([
                            'user_id'  => $product->user_id,
                            'order_id' => $order->id,
                            'amount'   => $commission,
                            'status'   => '0',
                        ]);
                    } else {
                        $check->amount = $check->amount + $commission;
                        $check->update();
                    }
                }
                $product->quantity = $product->quantity - $item->qty;
                $product->save();
            }
        }

        foreach ($usids as $seller) {
            $total = DB::table('order_details')->where('seller_id', $seller)->where('order_id', $order->id)->sum('total_price');
            $total += $single_charge;
            DB::table('multi_order')->insert([
                'vendor_id' => $seller, 
                'order_id' => $order->id, 
                'partial_pay' => 0, 
                'status' => 0, 
                'total' => $total
            ]);
        }
        
        if ($request->payment_method == 'wallate') {
            $order->update([
                'pay_staus' => 1,
                'pay_date'  => date('d-m-y'),
            ]);
        }

        if (auth()->user()) {
            if ($request->partial_paid < auth()->user()->wallate && $request->partial_paid > 0) {
                $parts = DB::table('multi_order')->where('order_id', $order->id)->get();
                $amount = $request->partial_paid;
                foreach ($parts as $part) {
                    if ($amount > 0) {
                        if ($part->partial_pay != $part->total) {
                            $total_requested = $part->partial_pay + $amount;

                            if ($total_requested > $part->total) {
                                $new_balance = $total_requested - $part->total;
                                $slice = $amount - $new_balance;
                                $amount -= $slice;
                            } else {
                                $slice = $amount;
                                $amount -= $slice;
                            }

                            DB::table('multi_order')->where('id', $part->id)->update(['partial_pay' => $part->partial_pay + $slice]);
                        }
                    }
                }
                PartialPayment::create([
                    'order_id' => $order->id,
                    'payment_method' => 'wall',
                    'amount' => $request->partial_paid,
                    'status' => 1,
                ]);
            }
        }

        if (Session::has('coupon')) {
            $n_parts = DB::table('multi_order')->where('order_id', $order->id)->get();
            $n_amount = $discount;
            foreach ($n_parts as $n_part) {
                if ($n_amount > 0) {
                    if ($n_part->partial_pay != $n_part->total) {
                        $n_total_requested = $n_part->discount + $n_amount;

                        if ($n_total_requested > $n_part->total) {
                            $n_new_balance = $n_total_requested - $n_part->total;
                            $n_slice = $n_amount - $n_new_balance;
                            $n_amount -= $n_slice;
                        } else {
                            $n_slice = $n_amount;
                            $n_amount -= $n_slice;
                        }

                        DB::table('multi_order')->where('id', $n_part->id)->update(['discount' => $n_part->partial_pay + $n_slice]);
                    }
                }
            }
        }
        
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

    private function processCartItems($order)
    {
        $total_refer = 0;
        $usids = [];
        
        foreach (Cart::content() as $item) {
            $pp = Product::find($item->id);
            if (!in_array("$pp->user_id", $usids)) {
                $usids[] = $pp->user_id;
            }

            $total_refer += (($item->price / 100) * $item->qty);
            if ($item->qty >= 6 && $pp->whole_price > 0) {
                $price = $pp->whole_price;
            } else {
                $price = $item->price;
            }
            $vendor = User::find($pp->user_id);
            $vp = $price * $item->qty;
            if ($vendor->role_id == 1) {
                $gt = $vp;
            } else {
                if ($vendor->shop_info->commission == NULL) {
                    $commission = (setting('shop_commission') / 100) * $vp;
                    $gt = $vp - $commission;
                } else {
                    $commission = ($vendor->shop_info->commission / 100) * $vp;
                    $gt = $vp - $commission;
                }
            }
            
            $order->orderDetails()->create([
                'product_id'  => $item->id,
                'seller_id'   => $pp->user_id,
                'title'       => $item->name,
                'color'       => $item->options->color,
                'size'        => json_encode($item->options->attributes),
                'qty'         => $item->qty,
                'price'       => $price,
                'total_price' => $price * $item->qty,
                'g_total'     => $gt
            ]);

            $product = Product::find($item->id);
            if (auth()->user()) {
                $userPoint = User::find(auth()->id());
                $pointp = $product->point * $item->qty;
                if (setting('is_point') == 1) {
                    $point = $pointp;
                } else {
                    $point = 0;
                }
                $userPoint->pen_point += $point;
                $userPoint->update();
                $order->point += $point;
            }
            $order->save();
            
            if ($product) {
                $vendor = User::find($product->user_id);
                if ($vendor->role_id == 1) {
                    $account = VendorAccount::where('vendor_id', 1)->first();
                    $account->pending_amount += $vp;
                    $account->save();
                } else {
                    $grand_total = $price * $item->qty;

                    if ($vendor->shop_info->commission == NULL) {
                        $commission = (setting('shop_commission') / 100) * $grand_total;
                        $amount = $grand_total - $commission;
                    } else {
                        $commission = ($vendor->shop_info->commission / 100) * $grand_total;
                        $amount = $grand_total - $commission;
                    }
                    
                    $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                    $adminAccount->update([
                        'pending_amount' => $adminAccount->pending_amount + $commission
                    ]);

                    $vendor->vendorAccount()->update([
                        'pending_amount' => $vendor->vendorAccount->pending_amount + $amount
                    ]);

                    $check = Commission::where('user_id', $product->user_id)->where('order_id', $order->id)->first();
                    if (!$check) {
                        Commission::create([
                            'user_id'  => $product->user_id,
                            'order_id' => $order->id,
                            'amount'   => $commission,
                            'status'   => '0',
                        ]);
                    } else {
                        $check->amount = $check->amount + $commission;
                        $check->update();
                    }
                }
                $product->quantity = $product->quantity - $item->qty;
                $product->save();
            }
        }

        return $total_refer;
    }

    private function processAamarpay($request, $amount, $orderId)
    {
        if (setting('amode') == '2') {
            $url = 'https://secure.aamarpay.com/request.php';
        } else {
            $url = 'https://sandbox.aamarpay.com/request.php';
        }

        $fields = array(
            'store_id' => setting('astore'),
            'amount' => $amount,
            'payment_type' => 'VISA',
            'currency' => 'BDT',
            'tran_id' => rand(1111111, 9999999),
            'cus_name' => $request->first_name . $request->last_name,
            'cus_email' => $request->email,
            'cus_add1' => $request->address,
            'cus_add2' => $request->address,
            'cus_city' => $request->city,
            'cus_state' => $request->city,
            'cus_postcode' => $request->postcode,
            'cus_country' => 'Bangladesh',
            'cus_phone' => $request->phone,
            'cus_fax' => 'Not¬Applicable',
            'ship_name' => $request->first_name . $request->last_name,
            'ship_add1' => $request->address,
            'ship_add2' => $request->address,
            'ship_city' => $request->city,
            'ship_state' => $request->city,
            'ship_postcode' => '1212',
            'ship_country' => 'Bangladesh',
            'desc' => 'Product Purches',
            'success_url' => route('success'),
            'fail_url' => route('fail'),
            'cancel_url' => 'http://localhost/foldername/cancel.php',
            'opt_a' => $orderId,
            'opt_b' => '',
            'opt_c' => '',
            'opt_d' => '',
            'signature_key' => setting('akey')
        );

        $fields_string = http_build_query($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $url_forward = str_replace('"', '', stripslashes(curl_exec($ch)));
        curl_close($ch);

        $this->redirect_to_merchant($url_forward);
    }

    private function processUddoktapay($request, $amount, $orderId)
    {
        $requestData = [
            'full_name'    => $request->first_name . $request->last_name,
            'email'        => $request->email,
            'amount'       => $amount,
            'metadata'     => [
                'order_id'   => $orderId,
                'metadata_1' => 'foo',
                'metadata_2' => 'bar',
            ],
            'redirect_url'  => route('uddoktapay.success'),
            'return_type'   => 'GET',
            'cancel_url'    => route('uddoktapay.cancel'),
            'webhook_url'   => route('uddoktapay.webhook'),
        ];

        try {
            $paymentUrl = UddoktaPay::init_payment($requestData);
            return $paymentUrl;
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    function redirect_to_merchant($url)
    {
        if (setting('amode') == '2') {
            $base = 'https://secure.aamarpay.com/';
        } else {
            $base = 'https://sandbox.aamarpay.com/';
        }
        ?>
        <html xmlns="http://www.w3.org/1999/xhtml">
          <head><script type="text/javascript">
            function closethisasap() { document.forms["redirectpost"].submit(); } 
          </script></head>
          <body onLoad="closethisasap();">
            <form name="redirectpost" method="post" action="<?php echo $base . $url; ?>"></form>
          </body>
        </html>
        <?php
    }

    private function validate($request, $rules)
    {
        return $request->validate($rules);
    }
}