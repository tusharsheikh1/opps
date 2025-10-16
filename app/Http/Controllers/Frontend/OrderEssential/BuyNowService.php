<?php

namespace App\Http\Controllers\Frontend\OrderEssential;

use App\Models\Commission;
use App\Models\Order;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\User;
use App\Models\VendorAccount;
use App\Models\PartialPayment;
use App\Jobs\SendOrderConfirmationSms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Library\UddoktaPay;
use Carbon\Carbon;
use DB;
use Exception;

class BuyNowService
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
     * Store customer buy now order (minimal)
     */
    public function orderBuyNowStore_minimal(Request $request)
    {
        $phoneMinDigits = empty(setting('phone_min_dgt')) ? 11 : setting('phone_min_dgt');
        $phoneMaxDigits = empty(setting('phone_max_dgt')) ? 11 : setting('phone_max_dgt');
        $request->email = empty($request->email) ? 'noreply@lems.shop' : $request->email;

        $this->validate($request, [
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'nullable|string|max:255',
            'company'         => 'nullable|string|max:255',
            'country'         => 'nullable|string|max:255',
            'address'         => 'required|string|max:255',
            'city'            => 'nullable|string|max:255',
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
            if ($this->checkGuestOrderInterval($request->phone, $request->email)) {
                $remainingSeconds = $this->getRemainingTime($request->phone, $request->email);
                $remainingMinutes = ceil($remainingSeconds / 60);
                $whatsappNumber = setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX';
                
                notify()->warning("অপেক্ষা করুন! আপনি ইতিমধ্যে একটা অর্ডার করেছেন। আপনি {$remainingMinutes} মিনিট পর আবার অর্ডার করতে পারবেন। এটি ভুয়া অর্ডার প্রতিরোধের জন্য। অর্ডারের যেকোন পরিবর্তনের জন্য আমাদের WhatsApp {$whatsappNumber} এ নক করুন।", "অর্ডার সীমাবদ্ধতা");
                return redirect()->back();
            }
        }

        $product = Product::find($request->id);
        if (!$product) {
            notify()->error("Product not found", "Error");
            return redirect()->back();
        }

        $shipping_charge = $this->calculateShippingForProduct($product, $request);
        $total_refer = $product->regular_price / 100;
        $subtotal = $this->calculateSubtotal($product, $request);

        $coupon_code = '';
        $discount = 0;
        $total = $subtotal + $shipping_charge;

        if (Session::has('coupon')) {
            $coupon_code = Session::get('coupon')['name'];
            $discount = Session::get('coupon')['discount'];
            $total = ($subtotal + $shipping_charge) - $discount;
        }

        $wl = Session::has('coupon') ? $total : $subtotal + $shipping_charge;

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

        $order = $this->createBuyNowOrder($request, $product, $shipping_charge, $subtotal, $discount, $total, $coupon_code);
        $this->generateOrderId($order);

        $price = $this->getProductPrice($product, $request);
        $gt = $this->calculateGrandTotal($product, $price, $request->qty);

        $order->orderDetails()->create([
            'product_id'  => $product->id,
            'seller_id'   => $product->user_id,
            'title'       => $product->title,
            'color'       => $request->color,
            'size'        => $request->size,
            'qty'         => $request->qty,
            'price'       => $price,
            'total_price' => $total ?? $subtotal + $shipping_charge,
            'g_total'     => $gt
        ]);

        DB::table('multi_order')->insert([
            'vendor_id' => $product->user_id,
            'order_id' => $order->id,
            'partial_pay' => 0,
            'status' => 0,
            'total' => $subtotal + $shipping_charge
        ]);

        if ($request->payment_method == 'wallate') {
            $order->update([
                'pay_staus' => 1,
                'pay_date'  => date('d-m-y'),
            ]);
        }

        $this->handleCouponDiscount($order, $discount);
        $this->updateProductAndVendor($product, $request, $order, $price, $subtotal, $shipping_charge, $total);

        Session::forget('coupon');
        $order->update(['refer_bonus' => $total_refer]);

        // Dispatch SMS confirmation after 5 minutes
        if (!empty($order->phone)) {
            SendOrderConfirmationSms::dispatch($order->id)->delay(now()->addMinutes(5));
        }

        $data = $this->prepareOrderData($order, $request);

        return $this->handlePaymentMethod($request, $data, $total ?? $subtotal + $shipping_charge, $order, $product);
    }

    /**
     * Store customer buy now order (guest)
     */
    public function orderBuyNowStore_guest(Request $request)
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

        // Check order interval for guests
        if ($this->checkGuestOrderInterval($request->phone, $request->email)) {
            $remainingSeconds = $this->getRemainingTime($request->phone, $request->email);
            $remainingMinutes = ceil($remainingSeconds / 60);
            $whatsappNumber = setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX';
            
            notify()->warning("অপেক্ষা করুন! আপনি ইতিমধ্যে একটা অর্ডার করেছেন। আপনি {$remainingMinutes} মিনিট পর আবার অর্ডার করতে পারবেন। এটি ভুয়া অর্ডার প্রতিরোধের জন্য। অর্ডারের যেকোন পরিবর্তনের জন্য আমাদের WhatsApp {$whatsappNumber} এ নক করুন।", "অর্ডার সীমাবদ্ধতা");
            return redirect()->back();
        }

        return $this->processBuyNowOrder($request, true);
    }

    /**
     * Store customer buy now order (authenticated)
     */
    public function orderBuyNowStore(Request $request)
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

        return $this->processBuyNowOrder($request, false);
    }

    /**
     * Buy product
     */
    public function buyProduct(Request $request)
    {
        $this->validate($request, [
            'id'    => 'required|integer',
            'qty'   => 'required|integer',
            'color' => 'nullable|string',
            'size'  => 'nullable|string'
        ]);

        $product = Product::find($request->id);

        if ($product->colors->count() > 0 && $request->color == 'blank') {
            notify()->warning("Please Choose a Colour", "Something Wrong");
            return back();
        }

        $attributes = Attribute::all();
        foreach ($attributes as $attribute) {
            $slug = $attribute->slug;
            $s = $request->$slug;
            if ($s == 'blank') {
                notify()->warning("Sorry, Please Fill All Attribute", "Something Wrong");
                return back();
            }
        }

        if (!$product) {
            notify()->warning("Sorry, please try again", "Something Wrong");
            return back();
        }

        return view('frontend.checkout', compact('request', 'product'));
    }

    // Helper methods
    private function calculateShippingForProduct($product, $request)
    {
        if ($product->download_able != 1) {
            if ($request->stotal > setting('shipping_free_above')) {
                return 0;
            } else {
                if ($request->shipping_range == 1) {
                    return setting('shipping_charge');
                } else {
                    return setting('shipping_charge_out_of_range');
                }
            }
        } else {
            return 0;
        }
    }

    private function calculateShippingByCity($product, $city, $stotal)
    {
        if ($product->download_able != 1) {
            if ($stotal > setting('shipping_free_above')) {
                return 0;
            } else {
                if ($city == 'Dhaka') {
                    return setting('shipping_charge');
                } else {
                    return setting('shipping_charge_out_of_range');
                }
            }
        } else {
            return 0;
        }
    }

    private function calculateSubtotal($product, $request)
    {
        if ($request->qty >= 6 && $product->whole_price > 0) {
            return $product->whole_price * $request->qty;
        } else {
            return $request->dynamic_prices * $request->qty;
        }
    }

    private function getProductPrice($product, $request)
    {
        if ($request->qty >= 6 && $product->whole_price > 0) {
            return $product->whole_price;
        } else {
            return $request->dynamic_prices;
        }
    }

    private function calculateGrandTotal($product, $price, $qty)
    {
        // Check if product exists
        if (!$product || !$product->user_id) {
            return $price * $qty; // Fallback to simple calculation
        }

        $vendor = User::find($product->user_id);
        $vp = $price * $qty;
        
        // Check if vendor exists
        if (!$vendor) {
            return $vp; // Fallback to simple calculation
        }
        
        // Check if vendor is admin (role_id = 1)
        if ($vendor->role_id == 1) {
            return $vp;
        } else {
            // Check if vendor has shop_info relationship
            if (!$vendor->shop_info) {
                // If no shop_info, use default commission
                $defaultCommission = setting('shop_commission') ?? 0;
                $commission = ($defaultCommission / 100) * $vp;
                return $vp - $commission;
            }
            
            // Check if commission is null or not set
            if ($vendor->shop_info->commission == NULL || $vendor->shop_info->commission == '') {
                $defaultCommission = setting('shop_commission') ?? 0;
                $commission = ($defaultCommission / 100) * $vp;
                return $vp - $commission;
            } else {
                $commission = ($vendor->shop_info->commission / 100) * $vp;
                return $vp - $commission;
            }
        }
    }

    private function createBuyNowOrder($request, $product, $shipping_charge, $subtotal, $discount, $total, $coupon_code)
    {
        return Order::create([
            'user_id'         => auth()->id(),
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
            'meet_time'       => $request->meet
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

    private function updateProductAndVendor($product, $request, $order, $price, $subtotal, $shipping_charge, $total)
    {
        if ($product && $product->user_id) {
            $vendor = User::find($product->user_id);
            
            if (!$vendor) {
                return; // Skip if vendor not found
            }
            
            $wl = Session::has('coupon') ? $total : $subtotal + $shipping_charge;
            
            if ($vendor->role_id == 1) {
                $account = VendorAccount::where('vendor_id', 1)->first();
                if ($account) {
                    $account->pending_amount += $wl;
                    $account->save();
                }
            } else {
                if (Session::has('coupon')) {
                    $grand_total = $total - $shipping_charge;
                } else {
                    $grand_total = $subtotal;
                }
                
                // Check if vendor has shop_info
                if ($vendor->shop_info && $vendor->shop_info->commission !== null) {
                    $commission = ($vendor->shop_info->commission / 100) * $grand_total;
                } else {
                    $defaultCommission = setting('shop_commission') ?? 0;
                    $commission = ($defaultCommission / 100) * $grand_total;
                }
                
                $vendor_amount = $grand_total - $commission;
                
                $adminAccount = VendorAccount::where('vendor_id', 1)->first();
                if ($adminAccount) {
                    $adminAccount->update([
                        'pending_amount' => $adminAccount->pending_amount + $commission
                    ]);
                }

                // Check if vendor has vendorAccount relationship
                if ($vendor->vendorAccount) {
                    $vendor->vendorAccount()->update([
                        'pending_amount' => $vendor->vendorAccount->pending_amount + $vendor_amount
                    ]);
                }

                Commission::create([
                    'user_id'  => $product->user_id,
                    'order_id' => $order->id,
                    'amount'   => $commission,
                    'status'   => '0',
                ]);
            }
            
            // Update product quantity
            if ($product->quantity >= $request->qty) {
                $product->quantity = $product->quantity - $request->qty;
                $product->save();
            }
        }
    }

    private function updateUserPoints($product, $request, $order)
    {
        if (auth()->user()) {
            $userPoint = User::find(auth()->id());
            $pointp = $product->point * $request->qty;
            if (setting('is_point') == 1) {
                $point = $pointp;
            } else {
                $point = 0;
            }
            $userPoint->pen_point += $point;
            $userPoint->update();
            $order->point += $point;
            $order->save();
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

    private function handlePaymentMethod($request, $data, $amount, $order, $product)
    {
        if ($request->payment_method == 'aamarpay') {
            // Handle Aamarpay payment
            return $this->processAamarpay($request, $amount, $order->id);
        } elseif ($request->payment_method == 'uddoktapay') {
            // Handle UddoktaPay payment
            $url = $this->processUddoktapay($request, $amount, $order->id);
            if ($url) {
                return Redirect::to($url);
            }
        } else {
            // Send email if configured
            if (env('mail_config') == 1) {
                Mail::send('frontend.invoice-mail', $data, function ($mail) use ($data) {
                    $mail->from(config('mail.from.address'), config('app.name'))
                        ->to($data['email'], $data['name'])
                        ->subject('Order Invoice');
                });
            }

            return view('frontend.order_success', compact('data'));
        }
    }

    private function processBuyNowOrder($request, $isGuest = false)
    {
        $product = Product::find($request->id);
        
        if (!$product) {
            notify()->error("Product not found", "Error");
            return redirect()->back();
        }
        
        $shipping_charge = $this->calculateShippingByCity($product, $request->city, $request->stotal);
        $total_refer = $product->regular_price / 100;
        
        // Update user points only for authenticated users
        if (!$isGuest && auth()->user()) {
            $this->updateUserPoints($product, $request, new Order()); // Temporary order object
        }

        $subtotal = $this->calculateSubtotal($product, $request);

        $coupon_code = '';
        $discount = 0;
        $total = $subtotal + $shipping_charge;

        if (Session::has('coupon')) {
            $coupon_code = Session::get('coupon')['name'];
            $discount = Session::get('coupon')['discount'];
            $total = ($subtotal + $shipping_charge) - $discount;
        }

        $wl = Session::has('coupon') ? $total : $subtotal + $shipping_charge;

        // Wallet payment validation (only for authenticated users)
        if (!$isGuest && $request->payment_method == 'wallate') {
            if (!auth()->user() || $wl > auth()->user()->wallate) {
                notify()->warning("don't have enough balance in wallate", "Warning");
                return redirect()->back();
            } else {
                $user = User::find(auth()->id());
                $user->wallate = $user->wallate - $wl;
                $user->update();
            }
        }

        // Handle partial payment (only for authenticated users)
        if (!$isGuest && isset($request->partial_paid) && $request->partial_paid > 0) {
            if (!auth()->user() || $request->partial_paid > auth()->user()->wallate) {
                notify()->warning("don't have enough balance in wallate", "Warning");
                return redirect()->back();
            } else {
                $user = User::find(auth()->id());
                $user->wallate = $user->wallate - $request->partial_paid;
                $user->update();
            }
        }

        $order = $this->createBuyNowOrderWithAuth($request, $product, $shipping_charge, $subtotal, $discount, $total, $coupon_code, $isGuest);
        $this->generateOrderId($order);

        $price = $this->getProductPrice($product, $request);
        $gt = $this->calculateGrandTotal($product, $price, $request->qty);

        $order->orderDetails()->create([
            'product_id'  => $product->id,
            'seller_id'   => $product->user_id,
            'title'       => $product->title,
            'color'       => $request->color,
            'size'        => $request->size,
            'qty'         => $request->qty,
            'price'       => $price,
            'total_price' => $total ?? $subtotal + $shipping_charge,
            'g_total'     => $gt
        ]);

        DB::table('multi_order')->insert([
            'vendor_id' => $product->user_id,
            'order_id' => $order->id,
            'partial_pay' => 0,
            'status' => 0,
            'total' => $subtotal + $shipping_charge
        ]);

        if ($request->payment_method == 'wallate') {
            $order->update([
                'pay_staus' => 1,
                'pay_date'  => date('d-m-y'),
            ]);
        }

        // Handle partial payment for authenticated users
        if (!$isGuest && isset($request->partial_paid) && $request->partial_paid > 0 && auth()->user() && $request->partial_paid < auth()->user()->wallate) {
            $this->handlePartialPayment($request, $order);
        }

        $this->handleCouponDiscount($order, $discount);
        $this->updateProductAndVendor($product, $request, $order, $price, $subtotal, $shipping_charge, $total);

        Session::forget('coupon');
        $order->update(['refer_bonus' => $total_refer]);

        // Dispatch SMS confirmation after 5 minutes
        if (!empty($order->phone)) {
            SendOrderConfirmationSms::dispatch($order->id)->delay(now()->addMinutes(5));
        }

        $data = $this->prepareOrderData($order, $request);

        return $this->handlePaymentMethod($request, $data, $total ?? $subtotal + $shipping_charge, $order, $product);
    }

    private function createBuyNowOrderWithAuth($request, $product, $shipping_charge, $subtotal, $discount, $total, $coupon_code, $isGuest)
    {
        $orderData = [
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
            'meet_time'       => $request->meet
        ];

        if (!$isGuest && auth()->user()) {
            $orderData['user_id'] = auth()->id();
            $orderData['refer_id'] = auth()->user()->refer;
        }

        return Order::create($orderData);
    }

    private function handlePartialPayment($request, $order)
    {
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

        // Create partial payment record
        // PartialPayment::create([
        //     'order_id' => $order->id,
        //     'payment_method' => 'wall',
        //     'amount' => $request->partial_paid,
        //     'status' => 1,
        // ]);
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