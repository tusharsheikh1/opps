<?php

namespace App\Http\Controllers\Frontend\OrderEssential;

use App\Models\Commission;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorAccount;
use App\Models\PartialPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Library\UddoktaPay;
use DB;
use Exception;

class OrderHelperService
{
    /**
     * Calculate shipping charge based on subtotal and range
     */
    public function calculateShippingCharge($stotal, $shipping_range, $seller_count)
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

    /**
     * Calculate single shipping charge
     */
    public function calculateSingleCharge($stotal, $shipping_range)
    {
        if ($stotal > setting('shipping_free_above')) {
            return 0;
        }
        
        return ($shipping_range == 1) ? setting('shipping_charge') : setting('shipping_charge_out_of_range');
    }

    /**
     * Calculate shipping charge by city
     */
    public function calculateShippingChargeByCity($stotal, $city, $seller_count)
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

    /**
     * Calculate single shipping charge by city
     */
    public function calculateSingleChargeByCity($stotal, $city)
    {
        if ($stotal > setting('shipping_free_above')) {
            return 0;
        }
        
        return ($city == 'Dhaka') ? setting('shipping_charge') : setting('shipping_charge_out_of_range');
    }

    /**
     * Generate unique order ID
     */
    public function generateOrderId($order)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $order_id = substr(str_shuffle($chars), 0, 10);

        $order->update([
            'order_id' => $order_id,
            'invoice'  => '#' . str_pad($order->id, 5, 0, STR_PAD_LEFT),
        ]);
    }

    /**
     * Calculate grand total with commission
     */
    public function calculateGrandTotal($vendor, $vp)
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

    /**
     * Update user points
     */
    public function updateUserPoints($item, $order)
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

    /**
     * Update vendor account and commission
     */
    public function updateVendorAccount($pp, $item, $order, $price)
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

    /**
     * Create multi-order records
     */
    public function createMultiOrder($usids, $orderId, $single_charge)
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

    /**
     * Handle wallet payment
     */
    public function handleWalletPayment($request, $order)
    {
        if ($request->payment_method == 'wallate') {
            $order->update([
                'pay_staus' => 1,
                'pay_date'  => date('d-m-y'),
            ]);
        }
    }

    /**
     * Handle partial payment
     */
    public function handlePartialPayment($request, $order)
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

    /**
     * Handle coupon discount
     */
    public function handleCouponDiscount($order, $discount)
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

    /**
     * Prepare order data for emails and views
     */
    public function prepareOrderData($order, $request)
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

    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmationEmail($data)
    {
        if (setting('mail_config') == 1) {
            Mail::send('frontend.invoice-mail', $data, function ($mail) use ($data) {
                $mail->from(config('mail.from.address'), config('app.name'))
                    ->to($data['email'], $data['name'])
                    ->subject('Order Invoice');
            });
        }
    }

    /**
     * Handle payment method routing
     */
    public function handlePaymentMethod($request, $data, $amount, $order)
    {
        if ($request->payment_method == 'aamarpay') {
            return $this->processAamarpay($request, $amount, $order->id);
        } elseif ($request->payment_method == 'uddoktapay') {
            $url = $this->processUddoktapay($request, $amount, $order->id);
            if ($url) {
                return Redirect::to($url);
            }
        } else {
            $this->sendOrderConfirmationEmail($data);
            return view('frontend.order_success', compact('data'));
        }
    }

    /**
     * Process AamarPay payment
     */
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

    /**
     * Process UddoktaPay payment
     */
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

    /**
     * Redirect to merchant for payment processing
     */
    private function redirect_to_merchant($url)
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

    /**
     * Validate order request
     */
    public function validateOrderRequest($request, $phoneMinDigits, $phoneMaxDigits, $isGuest = false)
    {
        $rules = [
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'nullable|string|max:255',
            'company'         => 'nullable|string|max:255',
            'address'         => 'required|string|max:255',
            'phone'           => 'required|string|max:' . $phoneMaxDigits . '|min:' . $phoneMinDigits,
            'shipping_method' => 'nullable|string|max:255',
            'mobile_number'   => 'nullable|string|max:255',
            'transaction_id'  => 'nullable|string|max:255',
            'bank_name'       => 'nullable|string|max:255',
            'account_number'  => 'nullable|string|max:255',
            'holder_name'     => 'nullable|string|max:255',
            'branch'          => 'nullable|string|max:255',
            'routing'         => 'nullable|string|max:255',
        ];

        if ($isGuest) {
            $rules = array_merge($rules, [
                'country'         => 'required|string|max:255',
                'city'            => 'required|string|max:255',
                'district'        => 'required|string|max:255',
                'postcode'        => 'nullable|string|max:255',
                'email'           => 'required|email|string|max:255',
                'payment_method'  => 'required|string|max:255',
            ]);
        } else {
            $rules = array_merge($rules, [
                'country'         => 'nullable|string|max:255',
                'city'            => 'nullable|string|max:255',
                'district'        => 'nullable|string|max:255',
                'postcode'        => 'nullable|string|max:255',
                'email'           => 'nullable|email|string|max:255',
                'payment_method'  => 'nullable|string|max:255',
                'shipping_range'  => 'required|integer|max:255',
            ]);
        }

        return $request->validate($rules);
    }

    /**
     * Calculate product pricing for bulk orders
     */
    public function calculateProductPrice($product, $qty, $dynamic_price = null)
    {
        if ($qty >= 6 && $product->whole_price > 0) {
            return $product->whole_price;
        } else {
            return $dynamic_price ?? $product->regular_price;
        }
    }

    /**
     * Calculate product subtotal
     */
    public function calculateProductSubtotal($product, $qty, $dynamic_price = null)
    {
        $price = $this->calculateProductPrice($product, $qty, $dynamic_price);
        return $price * $qty;
    }

    /**
     * Get phone validation limits
     */
    public function getPhoneValidationLimits()
    {
        $phoneMinDigits = empty(setting('phone_min_dgt')) ? 11 : setting('phone_min_dgt');
        $phoneMaxDigits = empty(setting('phone_max_dgt')) ? 11 : setting('phone_max_dgt');
        
        return [$phoneMinDigits, $phoneMaxDigits];
    }

    /**
     * Calculate refer bonus
     */
    public function calculateReferBonus($price)
    {
        return $price / 100;
    }

    /**
     * Update product quantity after order
     */
    public function updateProductQuantity($productId, $qty)
    {
        $product = Product::find($productId);
        if ($product) {
            $product->quantity = $product->quantity - $qty;
            $product->save();
        }
    }

    /**
     * Check if shipping is free
     */
    public function isShippingFree($subtotal)
    {
        return $subtotal > setting('shipping_free_above');
    }

    /**
     * Get shipping charge by location
     */
    public function getShippingCharge($subtotal, $location, $sellerCount = 1)
    {
        if ($this->isShippingFree($subtotal)) {
            return 0;
        }

        if ($location === 'Dhaka' || $location == 1) {
            return setting('shipping_charge') * $sellerCount;
        } else {
            return setting('shipping_charge_out_of_range') * $sellerCount;
        }
    }
}