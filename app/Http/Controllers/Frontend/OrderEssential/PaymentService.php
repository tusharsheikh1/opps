<?php

namespace App\Http\Controllers\Frontend\OrderEssential;

use App\Models\Order;
use App\Models\PartialPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Library\UddoktaPay;
use DB;
use Exception;

class PaymentService
{
    /**
     * Payment form
     */
    public function payform($slug)
    {
        $order = Order::where('order_id', $slug)->first();
        $sum = PartialPayment::where('order_id', $order->id)->where('status', '1')->sum('amount');
        $partials = PartialPayment::where('order_id', $order->id)->get();
        
        if (empty($partials)) {
            $sum = '00';
        }
        
        return view('frontend.partials', compact('order', 'sum', 'partials'));
    }

    /**
     * Create payment
     */
    public function payCreate(Request $request, $slug)
    {
        $status = 0;
        
        if ($request->pm == 'wall') {
            if ($request->amount > auth()->user()->wallate) {
                notify()->warning("don't have enough balance in wallate", "Warning");
                return redirect()->back();
            } else {
                $user = User::find(auth()->id());
                $user->wallate = $user->wallate - $request->amount;
                $user->update();
                
                $parts = DB::table('multi_order')->where('order_id', $slug)->get();
                $amount = $request->amount;
                
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
                $status = 1;
            }
        }

        PartialPayment::create([
            'order_id' => $slug,
            'payment_method' => $request->pm,
            'transaction_id' => $request->tnx,
            'amount' => $request->amount,
            'status' => $status,
        ]);
        
        notify()->success("Success", "Success");
        return back();
    }

    /**
     * UddoktaPay payment processing
     */
    public function uddokpay($request, $amn, $id)
    {
        $requestData = [
            'full_name'    => $request->first_name . $request->last_name,
            'email'        => $request->email,
            'amount'       => $amn,
            'metadata'     => [
                'order_id'   => $id,
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
     * AamarPay payment processing
     */
    public function paynow($request, $amn, $id)
    {
        if (setting('amode') == '2') {
            $url = 'https://secure.aamarpay.com/request.php';
        } else {
            $url = 'https://sandbox.aamarpay.com/request.php';
        }

        $fields = array(
            'store_id' => setting('astore'), //store id will be aamarpay,  contact integration@aamarpay.com for test/live id
            'amount' => $amn, //transaction amount
            'payment_type' => 'VISA', //no need to change
            'currency' => 'BDT',  //currenct will be USD/BDT
            'tran_id' => rand(1111111, 9999999), //transaction id must be unique from your end
            'cus_name' => $request->first_name . $request->last_name,  //customer name
            'cus_email' => $request->email, //customer email address
            'cus_add1' => $request->address,  //customer address
            'cus_add2' => $request->address, //customer address
            'cus_city' => $request->city,  //customer city
            'cus_state' => $request->city,  //state
            'cus_postcode' => $request->postcode, //postcode or zipcode
            'cus_country' => 'Bangladesh',  //country
            'cus_phone' => $request->phone, //customer phone number
            'cus_fax' => 'Not¬Applicable',  //fax
            'ship_name' => $request->first_name . $request->last_name, //ship name
            'ship_add1' => $request->address,  //ship address
            'ship_add2' => $request->address,
            'ship_city' => $request->city,
            'ship_state' => $request->city,
            'ship_postcode' => '1212',
            'ship_country' => 'Bangladesh',
            'desc' => 'Product Purches',
            'success_url' => route('success'), //your success route
            'fail_url' => route('fail'), //your fail route
            'cancel_url' => 'http://localhost/foldername/cancel.php', //your cancel url
            'opt_a' => $id,  //optional paramter
            'opt_b' => '',
            'opt_c' => '',
            'opt_d' => '',
            'signature_key' => setting('akey') //signature key will provided aamarpay, contact integration@aamarpay.com for test/live signature key
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
     * Redirect to merchant
     */
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

    /**
     * UddoktaPay webhook
     */
    public function webhook(Request $request)
    {
        $headerAPI = isset($_SERVER['HTTP_RT_UDDOKTAPAY_API_KEY']) ? $_SERVER['HTTP_RT_UDDOKTAPAY_API_KEY'] : NULL;

        if (empty($headerAPI)) {
            return response("Api key not found", 403);
        }

        if ($headerAPI != setting("uapi")) {
            return response("Unauthorized Action", 403);
        }

        $bodyContent = trim($request->getContent());
        $bodyData = json_decode($bodyContent);
        $data = UddoktaPay::verify_payment($bodyData->invoice_id);
        
        if (isset($data['status']) && $data['status'] == 'COMPLETED') {
            // Do action with $data
        }
    }

    /**
     * UddoktaPay success callback
     */
    public function success2(Request $request)
    {
        if (empty($request->invoice_id)) {
            die('Invalid Request');
        }

        $data = UddoktaPay::verify_payment($request->invoice_id);
        
        if (isset($data['status']) && $data['status'] == 'COMPLETED') {
            $order_id = $data['metadata']['order_id'];

            $order = Order::find($order_id);
            $order->pay_staus = 1;
            $order->pay_date = date('m/d/y');
            $order->transaction_id = $data['transaction_id'];
            $order->save();
            
            $orderData = [
                'order_id'        => $order->order_id,
                'invoice'         => $order->invoice,
                'name'            => $order->first_name,
                'phone'           => $order->phone,
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
                'orderDetails'    => $order->orderDetails
            ];
            
            if (env('mail_config') == 1) {
                Mail::send('frontend.invoice-mail', $orderData, function ($mail) use ($orderData) {
                    $mail->from(config('mail.from.address'), config('app.name'))
                        ->to($orderData['email'], $orderData['name'])
                        ->subject('Order Invoice');
                });
            }
            
            notify()->success("Your order successfully done", "Congratulations");
            return redirect()->route('home');
        } else {
            notify()->warning("Your order successfully done, but payment is pending", "Congratulations");
            return redirect()->route('home');
        }
    }

    /**
     * AamarPay success callback
     */
    public function success(Request $request)
    {
        $order_id = $request->opt_a;
        $order = Order::find($order_id);
        $order->pay_staus = 1;
        $order->pay_date = date('m/d/y');
        $order->transaction_id = $request->tran_id;
        $order->save();
        
        $data = [
            'order_id'        => $order->order_id,
            'invoice'         => $order->invoice,
            'name'            => $request->first_name,
            'email'           => $request->cus_email,
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
            'orderDetails'    => $order->orderDetails
        ];
        
        if (env('mail_config') == 1) {
            Mail::send('frontend.invoice-mail', $data, function ($mail) use ($data) {
                $mail->from(config('mail.from.address'), config('app.name'))
                    ->to($data['email'], $data['name'])
                    ->subject('Order Invoice');
            });
        }
        
        notify()->success("Your order successfully done", "Congratulations");
        return redirect()->route('home');
    }

    /**
     * UddoktaPay cancel callback
     */
    public function cancel2()
    {
        notify()->warning("Your order Placed But Payment fail", "Warning");
        return redirect()->route('order');
    }

    /**
     * AamarPay fail callback
     */
    public function fail(Request $request)
    {
        notify()->warning("Your order Placed But Payment fail", "Warning");
        return redirect()->route('order');
    }

    /**
     * Process payment for order creation services
     */
    public function processPayment($request, $amount, $orderId, $paymentMethod)
    {
        if ($paymentMethod == 'aamarpay') {
            return $this->paynow($request, $amount, $orderId);
        } elseif ($paymentMethod == 'uddoktapay') {
            return $this->uddokpay($request, $amount, $orderId);
        }
        
        return null;
    }

    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmationEmail($orderData)
    {
        if (setting('mail_config') == 1) {
            Mail::send('frontend.invoice-mail', $orderData, function ($mail) use ($orderData) {
                $mail->from(config('mail.from.address'), config('app.name'))
                    ->to($orderData['email'], $orderData['name'])
                    ->subject('Order Invoice');
            });
        }
    }

    /**
     * Update order payment status
     */
    public function updateOrderPaymentStatus($orderId, $transactionId, $paymentMethod = null)
    {
        $order = Order::find($orderId);
        $order->pay_staus = 1;
        $order->pay_date = date('m/d/y');
        $order->transaction_id = $transactionId;
        
        if ($paymentMethod) {
            $order->payment_method = $paymentMethod;
        }
        
        $order->save();
        
        return $order;
    }

    /**
     * Validate payment webhook
     */
    public function validateWebhook($headerAPI, $expectedKey)
    {
        if (empty($headerAPI)) {
            return ['valid' => false, 'message' => 'Api key not found'];
        }

        if ($headerAPI != $expectedKey) {
            return ['valid' => false, 'message' => 'Unauthorized Action'];
        }

        return ['valid' => true, 'message' => 'Valid webhook'];
    }

    /**
     * Handle payment failure
     */
    public function handlePaymentFailure($orderId, $reason = 'Payment failed')
    {
        $order = Order::find($orderId);
        
        if ($order) {
            // Log the failure reason
            $order->payment_notes = $reason;
            $order->save();
        }
        
        notify()->warning("Your order was placed but payment failed", "Warning");
        return redirect()->route('order');
    }

    /**
     * Calculate payment fees
     */
    public function calculatePaymentFees($amount, $paymentMethod)
    {
        $fees = 0;
        
        switch ($paymentMethod) {
            case 'aamarpay':
                $fees = $amount * (setting('aamarpay_fee_percentage', 2.5) / 100);
                break;
            case 'uddoktapay':
                $fees = $amount * (setting('uddoktapay_fee_percentage', 2.0) / 100);
                break;
            default:
                $fees = 0;
        }
        
        return $fees;
    }

    /**
     * Get payment method display name
     */
    public function getPaymentMethodName($method)
    {
        $methods = [
            'aamarpay' => 'AamarPay',
            'uddoktapay' => 'UddoktaPay',
            'wallate' => 'Wallet',
            'cod' => 'Cash on Delivery',
            'bank' => 'Bank Transfer'
        ];
        
        return $methods[$method] ?? $method;
    }

    /**
     * Check if payment method is available
     */
    public function isPaymentMethodAvailable($method)
    {
        $availableMethods = [
            'aamarpay' => setting('aamarpay_enabled', true),
            'uddoktapay' => setting('uddoktapay_enabled', true),
            'wallate' => true,
            'cod' => setting('cod_enabled', true),
            'bank' => setting('bank_transfer_enabled', true)
        ];
        
        return $availableMethods[$method] ?? false;
    }
}