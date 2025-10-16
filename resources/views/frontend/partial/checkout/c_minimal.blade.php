@extends('layouts.frontend.app')
@push('meta')
<meta name='description' content="Checkout cart product"/>
<meta name='keywords' content="" />
@endpush
@section('title', 'Checkout - Complete Your Order')
@section('content')
@php
    $order=App\Models\Order::where('user_id',auth()->id())->select('address','shipping_charge','town','district','thana')->first();
    if(empty($order)){
        $town=''; $address=''; $district='';$thana='';
    }
@endphp

<style>
:root {
    --primary-color: #2563eb;
    --primary-dark: #1d4ed8;
    --success-color: #059669;
    --error-color: #dc2626;
    --border-color: #e5e7eb;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-900: #111827;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}

.checkout-container {
    min-height: 100vh;
    background: linear-gradient(135deg, var(--gray-50) 0%, #ffffff 100%);
    padding: 2rem 0;
}

.checkout-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.checkout-header {
    text-align: center;
    margin-bottom: 3rem;
}

.checkout-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 0.5rem;
}

.checkout-subtitle {
    color: var(--gray-500);
    font-size: 1.1rem;
}

.checkout-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 3rem;
    align-items: start;
}

@media (max-width: 1024px) {
    .checkout-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
}

.checkout-section {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--gray-100);
}

.step-number {
    width: 32px;
    height: 32px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 0.75rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.required::after {
    content: " *";
    color: var(--error-color);
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.2s ease;
    background: white;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1);
}

.form-input.is-invalid {
    border-color: var(--error-color);
}

.form-error {
    color: var(--error-color);
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.payment-methods {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.payment-option {
    position: relative;
}

.payment-radio {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.payment-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem 0.5rem;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    text-align: center;
    min-height: 100px;
}

.payment-label:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.payment-radio:checked + .payment-label {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    box-shadow: var(--shadow-lg);
}

.payment-icon {
    width: 40px;
    height: 30px;
    object-fit: contain;
    margin-bottom: 0.5rem;
    filter: brightness(0.8);
}

.payment-radio:checked + .payment-label .payment-icon {
    filter: brightness(1.2) contrast(1.2);
}

.payment-text {
    font-size: 0.85rem;
    font-weight: 600;
    line-height: 1.2;
}

.payment-details {
    margin-top: 1.5rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
}

.order-summary {
    position: sticky;
    top: 2rem;
}

.cart-items {
    max-height: 300px;
    overflow-y: auto;
    margin-bottom: 1.5rem;
}

.cart-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color);
}

.cart-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    object-fit: cover;
    margin-right: 0.75rem;
}

.item-details {
    flex: 1;
    margin-right: 0.75rem;
}

.item-name {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--gray-900);
    text-decoration: none;
    line-height: 1.3;
}

.item-price {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 0.9rem;
}

.coupon-section {
    margin-bottom: 1.5rem;
}

.coupon-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem;
    background: var(--gray-50);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    color: var(--gray-700);
}

.coupon-toggle:hover {
    background: var(--gray-100);
    text-decoration: none;
    color: var(--gray-700);
}

.coupon-content {
    margin-top: 1rem;
}

.coupon-input-group {
    display: flex;
    gap: 0.5rem;
}

.coupon-input {
    flex: 1;
}

.btn-apply {
    padding: 0.75rem 1.5rem;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-apply:hover {
    background: var(--primary-dark);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color);
}

.summary-row:last-child {
    border-bottom: none;
    padding-top: 1rem;
    margin-top: 0.5rem;
    border-top: 2px solid var(--gray-200);
}

.summary-label {
    color: var(--gray-600);
    font-size: 0.95rem;
}

.summary-value {
    font-weight: 600;
    color: var(--gray-900);
}

.summary-total {
    font-size: 1.25rem;
    color: var(--primary-color);
}

.btn-order {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, var(--success-color) 0%, #047857 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 1.5rem;
    box-shadow: var(--shadow-md);
}

.btn-order:hover {
    background: linear-gradient(135deg, #047857 0%, #065f46 100%);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.alert-message {
    margin-bottom: 2rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    border: 1px solid;
}

.alert-success {
    background: #d1fae5;
    border-color: #a7f3d0;
    color: #065f46;
}

.alert-danger {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #991b1b;
}

.shipping-options {
    display: grid;
    gap: 0.75rem;
    margin-top: 0.5rem;
}

.shipping-option {
    padding: 0.75rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.shipping-option:hover {
    border-color: var(--primary-color);
}

.shipping-option.selected {
    border-color: var(--primary-color);
    background: rgb(37 99 235 / 0.05);
}

@media (max-width: 768px) {
    .checkout-title {
        font-size: 2rem;
    }
    
    .checkout-section {
        padding: 1.5rem;
    }
    
    .payment-methods {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .coupon-input-group {
        flex-direction: column;
    }
}
</style>

<div class="checkout-container">
    <div class="checkout-wrapper">
        <div class="checkout-header">
            <h1 class="checkout-title">Checkout</h1>
            <p class="checkout-subtitle">Complete your order in just a few simple steps</p>
        </div>

        <form action="{{route('order.store_minimal')}}" method="POST">
            @csrf
            <input type="hidden" name="checkout_user_type" value="guest">
            
            <div class="alert-message">
                <div class="alert"></div>
            </div>

            <div class="checkout-grid">
                <!-- Billing Information -->
                <div class="checkout-main">
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="step-number">1</div>
                            <h2 class="section-title">Billing Information</h2>
                        </div>

                        <div class="form-group">
                            <label for="first_name" class="form-label required">Full Name</label>
                            <input 
                                required 
                                @if (auth()->user()) value="{{auth()->user()->name}}" @endif 
                                name="first_name" 
                                id="first_name"
                                class="form-input @error('first_name') is-invalid @enderror" 
                                type="text" 
                                placeholder="Enter your full name"
                            />
                            @error('first_name')<div class="form-error">{{$message}}</div>@enderror
                        </div>

                        <input name="country" id="country" value="{{ setting('COUNTRY_SERVE') ?? 'Bangladesh' }}" type="hidden" />

                        <div class="form-group">
                            <label for="phone" class="form-label required">Phone Number</label>
                            <input 
                                @if (auth()->user()) value="{{auth()->user()->phone}}" @endif 
                                required 
                                name="phone" 
                                id="phone"
                                class="form-input @error('phone') is-invalid @enderror" 
                                type="tel" 
                                placeholder="Enter your phone number"
                            />
                            @error('phone')<div class="form-error">{{$message}}</div>@enderror
                        </div>

                        <div class="form-group d-none" id="email_wrap">
                            <label for="email" class="form-label required">Email Address</label>
                            <input 
                                name="email" 
                                id="email" 
                                class="form-input @error('email') is-invalid @enderror" 
                                type="email" 
                                placeholder="Enter your email address"
                            />
                            @error('email')<div class="form-error">{{$message}}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="address" class="form-label">Delivery Address</label>
                            <textarea 
                                name="address" 
                                id="address" 
                                rows="3"
                                class="form-input"
                                placeholder="Enter your complete address"
                            ></textarea>
                            @error('address')<div class="form-error">{{$message}}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="shipping_range" class="form-label">Shipping Area</label>
                            <select name="shipping_range" id="shipping_range" class="form-input">
                                <option value="1">Inside {{ setting('shipping_range_inside') }} ({{ setting('shipping_charge') }} {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }})</option>
                                <option value="0">Outside ({{ setting('shipping_charge_out_of_range') }} {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }})</option>
                            </select>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="step-number">2</div>
                            <h2 class="section-title">Payment Method</h2>
                        </div>

                        <div class="payment-methods">
                            @if(setting('g_cod')=='true')
                            <div class="payment-option">
                                <input type="radio" name="payment_method" class="payment-radio" value="Cash on Delivery" id="cod" checked>
                                <label for="cod" class="payment-label">
                                    <img src="{{asset('/')}}icon/delivery-man.png" alt="COD" class="payment-icon">
                                    <span class="payment-text">Cash on<br>Delivery</span>
                                </label>
                            </div>
                            @endif

                            @if(setting('g_aamar')=='true')
                            <div class="payment-option">
                                <input type="radio" name="payment_method" class="payment-radio" value="aamarpay" id="aamarpay">
                                <label for="aamarpay" class="payment-label">
                                    <img src="{{asset('/')}}icon/aamarpay_logo.png" alt="Aamarpay" class="payment-icon">
                                    <span class="payment-text">Aamarpay</span>
                                </label>
                            </div>
                            @endif

                            @if(setting('g_uddok')=='true')
                            <div class="payment-option">
                                <input type="radio" name="payment_method" class="payment-radio" value="uddoktapay" id="uddoktapay">
                                <label for="uddoktapay" class="payment-label">
                                    <img src="{{asset('/')}}icon/uddoktapay.png" alt="Uddoktapay" class="payment-icon">
                                    <span class="payment-text">Uddoktapay</span>
                                </label>
                            </div>
                            @endif

                            @auth
                            @if(setting('g_wallate')=='true')
                            <div class="payment-option">
                                <input type="radio" name="payment_method" class="payment-radio" value="wallate" id="wallate">
                                <label for="wallate" class="payment-label">
                                    <img src="{{asset('/')}}icon/wallet.png" alt="Wallet" class="payment-icon">
                                    <span class="payment-text">Wallet<br>{{auth()->user()->wallate}} {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</span>
                                </label>
                            </div>
                            @endif
                            @endauth

                            @if(setting('g_bkash')=='true')
                            <div class="payment-option">
                                <input type="radio" name="payment_method" class="payment-radio" value="Bkash" id="Bkash">
                                <label for="Bkash" class="payment-label">
                                    <img src="{{asset('/')}}icon/bkash.png" alt="Bkash" class="payment-icon">
                                    <span class="payment-text">Bkash</span>
                                </label>
                            </div>
                            @endif

                            @if(setting('g_nagad')=='true')
                            <div class="payment-option">
                                <input type="radio" name="payment_method" class="payment-radio" value="Nagad" id="Nagad">
                                <label for="Nagad" class="payment-label">
                                    <img src="{{asset('/')}}icon/nagad.png" alt="Nagad" class="payment-icon">
                                    <span class="payment-text">Nagad</span>
                                </label>
                            </div>
                            @endif

                            @if(setting('g_rocket')=='true')
                            <div class="payment-option">
                                <input type="radio" name="payment_method" class="payment-radio" value="Rocket" id="Rocket">
                                <label for="Rocket" class="payment-label">
                                    <img src="{{asset('/')}}icon/rocket.png" alt="Rocket" class="payment-icon">
                                    <span class="payment-text">Rocket</span>
                                </label>
                            </div>
                            @endif

                            @if(setting('g_bank')=='true')
                            <div class="payment-option">
                                <input type="radio" name="payment_method" class="payment-radio" value="Bank" id="Bank">
                                <label for="Bank" class="payment-label">
                                    <img src="{{asset('/')}}icon/bank.png" alt="Bank" class="payment-icon">
                                    <span class="payment-text">Bank Transfer</span>
                                </label>
                            </div>
                            @endif
                        </div>

                        @error('payment_method')<div class="form-error">{{$message}}</div>@enderror

                        <div class="payment-details" id="payment-details">
                            Pay when you receive your order at your doorstep.
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="step-number">3</div>
                            <h2 class="section-title">Order Summary</h2>
                        </div>

                        <?php 
                            $stotal=0;
                            $ids=[];
                            $cartCollection= Cart::content();
                            $data= $cartCollection->sortBy('weight');
                        ?>

                        <div class="cart-items">
                            @foreach ($data as $item)
                                <div class="cart-item">
                                    <img src="{{asset('uploads/product/'.$item->options->image)}}" alt="{{$item->name}}" class="item-image">
                                    <div class="item-details">
                                        <a href="{{route('product.details', $item->options->slug)}}" class="item-name">{{$item->name}}</a>
                                    </div>
                                    <?php
                                        $whole=\App\Models\Product::find($item->id);
                                        if (!in_array("$whole->user_id", $ids)) {
                                            $ids[]=$whole->user_id;
                                        }
                                        if($item->qty>=6 && $whole->whole_price >0){
                                            $istotal=$item->qty*$whole->whole_price;
                                            $stotal+=$item->qty*$whole->whole_price;
                                        }else{
                                            $istotal=$item->subtotal;
                                            $stotal+=$item->subtotal;
                                        }
                                    ?>
                                    <div class="item-price">{{$istotal}}.00 {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</div>
                                </div>
                            @endforeach
                        </div>

                        <?php $seller_count= sizeof($ids); ?>
                        <input type="hidden" name="stotal" value="{{$stotal}}">
                        <input type="hidden" name="seller_count" id="seller_count" value="{{$seller_count}}">

                        <!-- Coupon Section -->
                        <div class="coupon-section">
                            <a class="coupon-toggle collapsed" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                                <span>Have a coupon code?</span>
                                <span>+</span>
                            </a>
                            <div class="collapse" id="collapseExample">
                                <div class="coupon-content">
                                    <div class="coupon-input-group">
                                        <input type="text" id="coupon" class="form-input coupon-input" placeholder="Enter coupon code" />
                                        <button type="button" class="btn-apply" id="apply-coupon">Apply</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" value="0" id="partial_paid" name="partial_paid" />

                        <!-- Summary -->
                        <div class="summary-row">
                            <span class="summary-label">Subtotal</span>
                            <span class="summary-value"><span id="sub-total">{{$stotal}}</span> {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">
                                Shipping @if ($stotal > setting('shipping_free_above'))(Free)@endif
                            </span>
                            <span class="summary-value">
                                @if ($stotal > setting('shipping_free_above'))
                                    0.00
                                @else
                                    <span id="ship-charge">
                                        @if(isset($order->shipping_charge))
                                            {{$order->single_charge*$seller_count}}
                                        @else 
                                            0.00 
                                        @endif
                                    </span>
                                @endif
                                {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}
                            </span>
                        </div>

                        <div class="summary-row coupon" style="display: none;">
                            <span class="summary-label">Coupon <span class="coupon-name"></span></span>
                            <span class="summary-value">-<span id="coupon-discount">{{Session::has('coupon') ? number_format(Session::get('coupon')['discount'], 2, '.', ',') : '0.00'}}</span> {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Total</span>
                            <span class="summary-value summary-total">
                                @if (Session::has('coupon'))
                                    @php
                                        $sub_total = $stotal;
                                        $discount = Session::get('coupon')['discount'];
                                        $rep_sub = str_replace(',', '', $sub_total);
                                        $total = number_format($rep_sub - $discount, 2, '.', ',');
                                    @endphp
                                @endif
                                
                                @if ($stotal > setting('shipping_free_above'))
                                    {{ $stotal }}
                                @else
                                    <span id="total">{{$total ?? $stotal}}</span>
                                @endif
                                {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}
                            </span>
                        </div>

                        <button type="submit" class="btn-order">Complete Order</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script src="{{asset('/')}}assets/frontend/js/city.js"></script>
<script>
$(document).ready(function () {
    // Coupon Application
    $(document).on('click', '#apply-coupon', function (e) {
        e.preventDefault();
        $('#coupon').removeClass('is-invalid');
        let stotal = "{!! $stotal !!}";
        let code = $('input#coupon').val();
        let seller_count = $('#seller_count').val();
        let shipping_charge = 0;

        if ($("select[name='shipping_range']").val() == 1) {
            let charge = "{!! setting('shipping_charge') !!}";
            shipping_charge += parseInt(charge);
        } else {
            let charge = "{!! setting('shipping_charge_out_of_range') !!}";
            shipping_charge += parseInt(charge);
        }
        shipping_charge = parseInt(shipping_charge) * parseInt(seller_count);
    
        if (code != '') {
            $.ajax({
                type: 'GET',
                url: '/apply/coupon/' + code + '/' + stotal,
                dataType: "JSON",
                success: function (response) {
                    $('.alert-message').removeClass('d-none');
                    if (response.alert == 'success') {
                        $('.alert-message .alert').removeClass('alert-danger').addClass('alert-success').text(response.message);
                        $('span#ship-charge').text(number_format(shipping_charge, 2, '.', ','));
                        $('span#coupon-discount').text(number_format(response.discount, 2, '.', ','));
                        let total = response.total + shipping_charge;
                        $('span#total').text(number_format(total, 2, '.', ','));
                        $('span.coupon-name').text('(' + code + ')');
                        $('.coupon').show();
                        $('#coupon').val('');
                    } else {
                        $('.alert-message .alert').removeClass('alert-success').addClass('alert-danger').text(response.message);
                    }
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });
        } else {
            $('#coupon').addClass('is-invalid');
        }
        setTimeout(() => {
            $('.alert-message .alert').removeClass('alert-danger alert-success').text('');
        }, 10000);
    });

    // Payment Method Selection
    $('.payment-radio').change(function() {
        let method = $(this).val();
        let html = '';
        
        var bkash = "{!! setting('bkash') !!}";
        var nogod = "{!! setting('nagad') !!}";
        var rocket = "{!! setting('rocket') !!}";
        var bank = "{!! setting('bank_name') !!}";
        var branch = "{!! setting('branch_name') !!}";
        var holder = "{!! setting('holder_name') !!}";
        var account = "{!! setting('bank_account') !!}";
        var routing = "{!! setting('routing') !!}";

        if (method == 'Bkash') {
            off_email();
            html = `
                <div style="margin-bottom: 1rem;">
                    <strong>Bkash Payment Instructions:</strong><br>
                    Send money to: <strong>${bkash}</strong><br>
                    Then enter your details below:
                </div>
                <div class="form-group">
                    <label class="form-label">Your Mobile Number</label>
                    <input required type="text" name="mobile_number" class="form-input" placeholder="Enter your mobile number"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Transaction ID</label>
                    <input required type="text" name="transaction_id" class="form-input" placeholder="Enter transaction ID"/>
                </div>
            `;
        } else if (method == 'Nagad') {
            off_email();
            html = `
                <div style="margin-bottom: 1rem;">
                    <strong>Nagad Payment Instructions:</strong><br>
                    Send money to: <strong>${nogod}</strong><br>
                    Then enter your details below:
                </div>
                <div class="form-group">
                    <label class="form-label">Your Mobile Number</label>
                    <input required type="text" name="mobile_number" class="form-input" placeholder="Enter your mobile number"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Transaction ID</label>
                    <input required type="text" name="transaction_id" class="form-input" placeholder="Enter transaction ID"/>
                </div>
            `;
        } else if (method == 'Rocket') {
            off_email();
            html = `
                <div style="margin-bottom: 1rem;">
                    <strong>Rocket Payment Instructions:</strong><br>
                    Send money to: <strong>${rocket}</strong><br>
                    Then enter your details below:
                </div>
                <div class="form-group">
                    <label class="form-label">Your Mobile Number</label>
                    <input required type="text" name="mobile_number" class="form-input" placeholder="Enter your mobile number"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Transaction ID</label>
                    <input required type="text" name="transaction_id" class="form-input" placeholder="Enter transaction ID"/>
                </div>
            `;
        } else if (method == 'Bank') {
            off_email();
            html = `
                <div style="margin-bottom: 1rem;">
                    <strong>Bank Transfer Details:</strong><br>
                    Bank: <strong>${bank}</strong><br>
                    Branch: <strong>${branch}</strong><br>
                    Account Holder: <strong>${holder}</strong><br>
                    Account Number: <strong>${account}</strong><br>
                    Routing: <strong>${routing}</strong>
                </div>
                <div class="form-group">
                    <label class="form-label">Your Bank Name</label>
                    <input required type="text" name="bank_name" class="form-input" placeholder="Enter bank name"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Your Account Number</label>
                    <input required type="text" name="account_number" class="form-input" placeholder="Enter account number"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Account Holder Name</label>
                    <input required type="text" name="holder_name" class="form-input" placeholder="Enter holder name"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Branch Name</label>
                    <input required type="text" name="branch" class="form-input" placeholder="Enter branch name"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Routing Number</label>
                    <input required type="text" name="routing" class="form-input" placeholder="Enter routing number"/>
                </div>
            `;
        } else if (method == 'Cash on Delivery') {
            off_email();
            html = 'Pay when you receive your order at your doorstep.';
        } else if (method == 'uddoktapay') {
            $('#email_wrap').removeClass('d-none');
            $('#email').prop('required', true);
            html = 'You will be redirected to complete your payment online.';
        } else if (method == 'aamarpay') {
            off_email();
            html = 'You will be redirected to complete your payment online.';
        } else if (method == 'wallate') {
            off_email();
            html = 'Payment will be deducted from your wallet balance.';
        } else {
            off_email();
            html = 'Complete your payment using the selected method.';
        }
        
        $('#payment-details').html(html);
    });

    function off_email() {
        $('#email_wrap').addClass('d-none');
        $('#email').removeAttr('required');
    }

    // Shipping charge calculation
    $(document).on('change', '#shipping_range', function (e) {
        calculateTotal();
    });

    function calculateTotal() {
        let shipping_charge = 0;
        let seller_count = $('#seller_count').val();
        
        if ($("select[name='shipping_range']").val() == 1) {
            let charge = "{!! setting('shipping_charge') !!}";
            shipping_charge += parseInt(charge);
        } else {
            let charge = "{!! setting('shipping_charge_out_of_range') !!}";
            shipping_charge += parseInt(charge);
        }

        shipping_charge = parseInt(shipping_charge) * parseInt(seller_count);

        let subtotal = $('span#sub-total').text();
        let coupon = $('span#coupon-discount').text();

        let rep_subtotal = subtotal.replace(',', '');
        let rep_coupon = coupon.replace(',', '');

        let total = (parseInt(rep_subtotal) + shipping_charge) - parseInt(rep_coupon);
        $('span#ship-charge').text(number_format(shipping_charge, 2, '.', ','));
        $('span#total').text(number_format(total, 2, '.', ','));
    }
    
    calculateTotal();

    function number_format(number, decimals, dec_point, thousands_sep) {
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return Math.round(n * k) / k;
            },
            s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }
});
</script>
@endpush