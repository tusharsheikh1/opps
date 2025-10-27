@extends('layouts.frontend.app')

@push('meta')
    <meta name='description' content="Buy now product" />
    <meta name='keywords' content="@foreach ($product->tags as $tag){{ $tag->name . ', ' }} @endforeach" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('title', 'Checkout - Buy now product')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/checkout-styles.css') }}">
    <style>
    /* Enhanced Product Restriction Styling */
    .order-interval-warning.product-restriction {
        background: linear-gradient(135deg, #fef3e2 0%, #fed7aa 100%);
        border: 2px solid #f59e0b;
        border-left: 6px solid #d97706;
    }

    .order-interval-warning.product-restriction::before {
        background: linear-gradient(90deg, #f59e0b, #d97706, #f59e0b);
    }

    .order-interval-warning.product-restriction .warning-icon {
        color: #d97706;
        font-size: 30px;
    }

    .order-interval-warning.product-restriction .warning-title {
        color: #92400e;
    }

    .order-interval-warning.product-restriction .warning-text {
        color: #92400e;
    }

    /* Restriction Type Tags */
    .restriction-tag {
        display: inline-block;
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 6px;
        box-shadow: 0 2px 4px rgba(220, 38, 38, 0.3);
        letter-spacing: 0.3px;
        animation: pulse 2s infinite;
    }

    .restriction-tag:before {
        content: "🔒 ";
        font-size: 10px;
    }

    /* Device-specific styling */
    .order-interval-warning.product-restriction[data-reason="device"] {
        background: linear-gradient(135deg, #fce7f3 0%, #fbb6ce 100%);
        border-color: #ec4899;
        border-left-color: #be185d;
    }

    .order-interval-warning.product-restriction[data-reason="device"] .restriction-tag {
        background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);
    }

    .order-interval-warning.product-restriction[data-reason="device"] .restriction-tag:before {
        content: "📱 ";
    }

    /* IP-specific styling */
    .order-interval-warning.product-restriction[data-reason="ip"] {
        background: linear-gradient(135deg, #ede9fe 0%, #c4b5fd 100%);
        border-color: #8b5cf6;
        border-left-color: #7c3aed;
    }

    .order-interval-warning.product-restriction[data-reason="ip"] .restriction-tag {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }

    .order-interval-warning.product-restriction[data-reason="ip"] .restriction-tag:before {
        content: "🌐 ";
    }

    /* Enhanced warning responsiveness */
    @media (max-width: 768px) {
        .restriction-tag {
            display: block;
            margin: 4px 0;
            text-align: center;
            max-width: 150px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .order-interval-warning.product-restriction .warning-text {
            text-align: center;
            line-height: 1.8;
        }
    }

    /* Subtle animation for restriction detection */
    @keyframes restrictionDetected {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }

    .order-interval-warning.product-restriction.show {
        animation: slideInDown 0.5s ease-out, restrictionDetected 0.8s ease-in-out 0.5s;
    }
    </style>
@endpush

@section('content')
    @php
        $order = App\Models\Order::where('user_id', auth()->id())
            ->select('address', 'shipping_charge', 'town', 'district', 'thana')
            ->first();
    @endphp

    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Complete Your Order</h1> {{-- Translation: অর্ডার সম্পন্ন করুন --}}
            <p>Complete your order in just a few steps</p> {{-- Translation: মাত্র কয়েকটি ধাপে আপনার অর্ডার সম্পন্ন করুন --}}
        </div>

        <div class="order-interval-warning" id="order-interval-warning">
            <div class="warning-header">
                <div class="warning-icon">⚠️</div>
                <h4 class="warning-title">Order Restriction</h4> {{-- Translation: অর্ডার সীমাবদ্ধতা --}}
            </div>
            <p class="warning-text" id="warning-message">
                Hold on! You have already placed an order. You can order again after <span class="countdown-timer" id="countdown-timer">--</span>. This is to prevent fake orders. For any changes to your order, knock us on WhatsApp <a href="#" id="whatsapp-link" class="whatsapp-link" target="_blank">
                    <strong id="whatsapp-number">{{ setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX' }}</strong>
                </a>. {{-- Translation: অপেক্ষা করুন! আপনি ইতিমধ্যে একটা অর্ডার করেছেন। আপনি <span class="countdown-timer" id="countdown-timer">--</span> পর আবার অর্ডার করতে পারবেন। এটি ভুয়া অর্ডার প্রতিরোধের জন্য। অর্ডারের যেকোন পরিবর্তনের জন্য আমাদের WhatsApp <a href="#" id="whatsapp-link" class="whatsapp-link" target="_blank"><strong id="whatsapp-number">{{ setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX' }}</strong></a> এ নক করুন। --}}
            </p>
        </div>

        <form action="{{ route('order.buy.store_minimal') }}" method="POST" id="checkout-form">
            @csrf
            
            <div class="checkout-grid">
                <div class="checkout-forms">
                    {{-- Translation: অ্যালার্ট মেসেজ --}}
                    <div class="alert-container" style="display: none;">
                        <div class="alert" id="alert-message"></div>
                    </div>

                    {{-- Translation: বিলিং তথ্য --}}
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="section-number">1</div>
                            <h3 class="section-title">Customer Information</h3>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="first_name">
                                Full Name <span class="required">*</span> {{-- Translation: পূর্ণ নাম --}}
                            </label>
                            <input 
                                type="text" 
                                id="first_name" 
                                name="first_name" 
                                class="form-input @error('first_name') error @enderror"
                                value="{{ auth()->user()->name ?? '' }}" 
                                required
                            />
                            @error('first_name')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone">
                                Phone Number <span class="required">*</span> {{-- Translation: ফোন নম্বর --}}
                            </label>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone" 
                                class="form-input @error('phone') error @enderror"
                                value="{{ auth()->user()->phone ?? '' }}" 
                                required
                            />
                            @error('phone')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- MODIFIED: Removed style="display: none;" and added value/required attributes --}}
                        <div class="form-group" id="email_wrap">
                            <label class="form-label" for="email">
                                Email Address <span class="required">*</span> {{-- Translation: ইমেইল ঠিকানা --}}
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-input @error('email') error @enderror"
                                value="{{ auth()->user()->email ?? '' }}"
                                required
                            />
                            @error('email')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="address">Full Address</label> {{-- Translation: সম্পূর্ণ ঠিকানা --}}
                            <textarea 
                                id="address" 
                                name="address" 
                                class="form-textarea @error('address') error @enderror"
                                placeholder="Write your full address..." {{-- Translation: আপনার সম্পূর্ণ ঠিকানা লিখুন... --}}
                            ></textarea>
                            @error('address')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        @if ($product->sheba == 1)
                        <div class="form-group">
                            <label class="form-label" for="meet">Service Date</label> {{-- Translation: সেবা গ্রহণের তারিখ --}}
                            <input 
                                type="date" 
                                id="meet" 
                                name="meet" 
                                class="form-input"
                            />
                        </div>
                        @endif

                        <div class="form-group">
                            <label class="form-label">Shipping Zone</label>
                            <div class="shipping-options">
                                <div class="shipping-option">
                                    <input type="radio" name="shipping_range" value="1" id="dhaka_inside">
                                    <label for="dhaka_inside" class="shipping-label">
                                        <div class="shipping-icon">🏙️</div>
                                        <div class="shipping-info">
                                            <div class="shipping-title">Dhaka City</div> {{-- Translation: ঢাকার সিটি --}}
                                            <div class="shipping-charge">{{ setting('shipping_charge') }} {{ setting('CURRENCY_CODE_MIN') ?? 'টাকা' }}</div>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="shipping-option">
                                    <input type="radio" name="shipping_range" value="0" id="dhaka_outside" checked>
                                    <label for="dhaka_outside" class="shipping-label">
                                        <div class="shipping-icon">🏘️</div>
                                        <div class="shipping-info">
                                            <div class="shipping-title">Outside Dhaka City</div> {{-- Translation: ঢাকা সিটির বাহিরে --}}
                                            <div class="shipping-charge">{{ setting('shipping_charge_out_of_range') }} {{ setting('CURRENCY_CODE_MIN') ?? 'টাকা' }}</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        @if (!empty($request->pr))
                            <input type="hidden" name="pr" value="{{ $request->pr }}">
                        @endif
                        <input type="hidden" value="{{ $request->dynamic_price }}" name="dynamic_prices">
                    </div>

                    {{-- Translation: পেমেন্ট পদ্ধতি --}}
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="section-number">2</div>
                            <h3 class="section-title">Payment Methods</h3>
                        </div>

                        <div class="payment-methods">
                            @if (setting('g_cod') == 'true')
                            <div class="payment-option">
                                <input type="radio" name="payment_method" value="Cash on Delivery" id="cod" checked>
                                <label for="cod" class="payment-label">
                                    <img src="{{ asset('/') }}icon/delivery-man.png" alt="COD" class="payment-icon">
                                    <span class="payment-text">Cash on Delivery</span> {{-- Translation: ক্যাশ অন ডেলিভারি --}}
                                </label>
                            </div>
                            @endif

                            @if (setting('g_bkash') == 'true')
                            <div class="payment-option">
                                <input type="radio" name="payment_method" value="Bkash" id="Bkash">
                                <label for="Bkash" class="payment-label">
                                    <img src="{{ asset('/') }}icon/bkash.png" alt="bKash" class="payment-icon">
                                    <span class="payment-text">bKash</span> {{-- Translation: বিকাশ --}}
                                </label>
                            </div>
                            @endif

                            <div class="see-more-toggle" onclick="toggleMorePayments()">
                                <span class="see-more-text">See More Payment Options</span> {{-- Translation: আরো পেমেন্ট অপশন দেখুন --}}
                                <svg class="see-more-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>

                            <div class="additional-payments" id="additional-payments" style="display: none;">
                                @if (setting('g_aamar') == 'true')
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="aamarpay" id="aamarpay">
                                    <label for="aamarpay" class="payment-label">
                                        <img src="{{ asset('/') }}icon/aamarpay_logo.png" alt="Aamarpay" class="payment-icon">
                                        <span class="payment-text">aamarpay</span> {{-- Translation: আমারপে --}}
                                    </label>
                                </div>
                                @endif

                                @if (setting('g_uddok') == 'true')
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="uddoktapay" id="uddoktapay">
                                    <label for="uddoktapay" class="payment-label">
                                        <img src="{{ asset('/') }}icon/uddoktapay.png" alt="Uddoktapay" class="payment-icon">
                                        <span class="payment-text">Uddoktapay</span> {{-- Translation: উদ্দোক্তাপে --}}
                                    </label>
                                </div>
                                @endif

                                @if (setting('g_nagad') == 'true')
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="Nagad" id="Nagad">
                                    <label for="Nagad" class="payment-label">
                                        <img src="{{ asset('/') }}icon/nagad.png" alt="Nagad" class="payment-icon">
                                        <span class="payment-text">Nagad</span> {{-- Translation: নগদ --}}
                                    </label>
                                </div>
                                @endif

                                @if (setting('g_rocket') == 'true')
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="Rocket" id="Rocket">
                                    <label for="Rocket" class="payment-label">
                                        <img src="{{ asset('/') }}icon/rocket.png" alt="Rocket" class="payment-icon">
                                        <span class="payment-text">Rocket</span> {{-- Translation: রকেট --}}
                                    </label>
                                </div>
                                @endif

                                @if (setting('g_bank') == 'true')
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="Bank" id="Bank">
                                    <label for="Bank" class="payment-label">
                                        <img src="{{ asset('/') }}icon/bank.png" alt="Bank Transfer" class="payment-icon">
                                        <span class="payment-text">Bank Transfer</span> {{-- Translation: ব্যাংক ট্রান্সফার --}}
                                    </label>
                                </div>
                                @endif
                            </div>
                        </div>

                        @error('payment_method')
                            <span class="error-message">{{ $message }}</span>
                        @enderror

                        <div id="payment-instructions" class="payment-details">
                            <p>💵 Pay upon receiving the product.</p> {{-- Translation: পণ্য হাতে পেয়ে টাকা পরিশোধ করুন। --}}
                        </div>

                        <div id="payment-details"></div>
                    </div>
                </div>

                {{-- Translation: ডান কলাম: অর্ডার সামারি --}}
                <div class="order-summary">
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="section-number">3</div>
                            <h3 class="section-title">Order Summery</h3>
                        </div>

                        <?php
                        if ($request->qty >= 6 && $product->whole_price > 0) {
                            $sub_total = $product->whole_price * $request->qty;
                        } else {
                            $sub_total = $request->dynamic_price * $request->qty;
                        }
                        
                        // IMPROVED: Build attributes display for Buy Now Direct Checkout
                        $displayAttributes = [];
                        
                        // Get Color if selected
                        if (isset($request->color) && $request->color != 'blank' && $request->color != '') {
                            try {
                                $color = \App\Models\Color::where('slug', $request->color)->first();
                                if ($color) {
                                    $displayAttributes['Color'] = $color->name;
                                }
                            } catch (\Exception $e) {
                                // Silently fail
                            }
                        }
                        
                        // Get Size if selected (from color_size_product table)
                        if (isset($request->size) && $request->size != 'blank' && $request->size != '') {
                            try {
                                // Try to get size from Size model if exists
                                $size = \App\Models\Size::find($request->size);
                                if ($size) {
                                    $displayAttributes['Size'] = $size->name;
                                }
                            } catch (\Exception $e) {
                                // If Size model doesn't exist, try AttributeValue
                                try {
                                    $sizeAttr = \App\Models\AttributeValue::find($request->size);
                                    if ($sizeAttr) {
                                        $displayAttributes['Size'] = $sizeAttr->value;
                                    }
                                } catch (\Exception $e2) {
                                    // Silently fail
                                }
                            }
                        }
                        
                        // Get other attributes from request
                        $allAttributes = DB::table('attributes')->get();
                        foreach ($allAttributes as $attr) {
                            $slug = $attr->slug;
                            if ($slug !== 'size' && isset($request->$slug) && $request->$slug != 'blank' && $request->$slug != '') {
                                try {
                                    $attrValue = \App\Models\AttributeValue::find($request->$slug);
                                    if ($attrValue) {
                                        $displayAttributes[$attr->name] = $attrValue->value;
                                    }
                                } catch (\Exception $e) {
                                    // Silently fail
                                }
                            }
                        }
                        ?>

                        <div class="product-item">
                            <img src="{{ asset('uploads/product/' . $product->image) }}" alt="{{ $product->title }}" class="product-image">
                            <div class="product-info">
                                <h4 class="product-title">{{ $product->title }}</h4>
                                
                                {{-- IMPROVED: Display selected attributes --}}
                                @if (count($displayAttributes) > 0)
                                    <div class="product-attributes" style="font-size: 0.85rem; color: #6b7280; margin-top: 0.25rem; margin-bottom: 0.25rem;">
                                        @foreach ($displayAttributes as $attrName => $attrValue)
                                            <strong>{{ $attrName }}:</strong> {{ $attrValue }}@if (!$loop->last), @endif
                                        @endforeach
                                    </div>
                                @endif
                                
                                <div class="product-price">{{ number_format($sub_total, 2) }} {{ setting('CURRENCY_CODE_MIN') ?? 'টাকা' }}</div>
                            </div>
                        </div>

                        <input type="hidden" name="id" value="{{ $request->id }}">
                        <input type="hidden" name="qty" value="{{ $request->qty }}">
                        <?php
                        // Initialize variables
                        $sizeData = null;
                        $colorValue = $request->color ?? 'blank';
                        
                        // Priority 1: Check if it's a size-based product (from color_size_product table)
                        if (isset($request->size) && $request->size != 'blank' && $request->size != '') {
                            // Size-based product (Size Only OR Size+Color)
                            $sizeData = json_encode(['size_id' => $request->size]);
                        } 
                        // Priority 2: Check for attribute-based products
                        else {
                            $attr = [];
                            $attributes = DB::table('attributes')->get();
                            foreach ($attributes as $attribute) {
                                $attribute_prouct = DB::table('attribute_product')
                                    ->select('*')
                                    ->join('attribute_values', 'attribute_values.id', '=', 'attribute_product.attribute_value_id')
                                    ->addselect('attribute_values.name as vName')
                                    ->addselect('attribute_product.id as vid')
                                    ->join('attributes', 'attributes.id', '=', 'attribute_values.attributes_id')
                                    ->where('attribute_product.product_id', $product->id)
                                    ->where('attributes.id', $attribute->id)
                                    ->get();
                                if ($attribute_prouct->count() > 0) {
                                    $slug = $attribute->slug;
                                    if (isset($request->$slug)) {
                                        $attr[] = ['attribute_value_id' => $request->$slug];
                                    }
                                }
                            }
                            
                            // Format attribute data properly
                            if (!empty($attr)) {
                                $sizeData = json_encode($attr);
                            } else {
                                $sizeData = json_encode([]);
                            }
                        }
                        ?>
                        <input type="hidden" name="size" value="{{ $sizeData }}">
                        <input type="hidden" name="color" value="{{ $colorValue }}">


                        {{-- Translation: কুপন সেকশন --}}
                        <div class="coupon-section">
                            <div class="coupon-toggle" onclick="toggleCoupon()">
                                <span class="form-label">Have a coupon?</span> {{-- Translation: কুপন আছে? --}}
                                <span id="coupon-arrow">+</span>
                            </div>
                            <div id="coupon-form" style="display: none;">
                                <div class="coupon-input-group">
                                    <input type="text" id="coupon" class="form-input coupon-input" placeholder="Enter coupon code"> {{-- Translation: কুপন কোড লিখুন --}}
                                    <button type="button" class="btn-apply" onclick="applyCoupon()">Apply</button> {{-- Translation: প্রয়োগ করুন --}}
                                </div>
                            </div>
                        </div>

                        <div class="summary-row">
                            <span>Subtotal</span> {{-- Translation: উপমোট --}}
                            <span id="sub-total">{{ number_format($sub_total, 2) }} {{ setting('CURRENCY_CODE_MIN') ?? 'টাকা' }}</span>
                        </div>

                        <div class="summary-row">
                            <span>Delivery Charge</span> {{-- Translation: ডেলিভারি চার্জ --}}
                            <span id="ship-charge">
                                @if (isset($order->shipping_charge))
                                    {{ number_format($order->shipping_charge, 2) }}
                                @else
                                    0.00
                                @endif
                                {{ setting('CURRENCY_CODE_MIN') ?? 'টাকা' }}
                            </span>
                        </div>

                        <div class="summary-row" id="coupon-row" style="display: none;">
                            <span>Coupon <span id="coupon-name"></span></span>
                            <span style="color: #10b981;">-<span id="coupon-discount">0.00</span> {{ setting('CURRENCY_CODE_MIN') ?? 'টাকা' }}</span>
                        </div>

                        <div class="summary-row total">
                            <span>Total</span> {{-- Translation: মোট --}}
                            <span id="total">
                                @if (Session::has('coupon'))
                                    @php
                                        $discount = Session::get('coupon')['discount'];
                                        $total = number_format($sub_total - $discount, 2);
                                    @endphp
                                    {{ $total }}
                                @else
                                    {{ number_format($sub_total, 2) }}
                                @endif
                                {{ setting('CURRENCY_CODE_MIN') ?? 'টাকা' }}
                            </span>
                        </div>

                        <button type="submit" class="submit-btn" id="submit-btn">
                            <div class="btn-content">
                                <div class="btn-action">
                                    <span class="btn-icon">🛒</span>
                                    <span id="btn-text"> Place order</span> {{-- REMOVED TOTAL AMOUNT: Removed <span id="btn-total-value">...</span> --}}
                                    <span id="btn-spinner" class="spinner" style="display: none;"></span>
                                    <svg class="btn-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <path d="M5 12h14m-7-7l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('js')
<script>
// Enhanced Device-Based Order Tracking - Prevents phone number changes and product restrictions
// NEW: Also handles product-specific restrictions (same product within 10 days)
document.addEventListener('DOMContentLoaded', function() {
    // Initialize
    updateShippingCharge();
    updateButtonTotal();
    
    // Initialize device tracking for guest users
    if (!{{ auth()->check() ? 'true' : 'false' }}) {
        initializeDeviceTracking();
    }
    
    // Initialize WhatsApp link
    const initialNumber = '{{ setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX' }}';
    if (initialNumber !== '01XXXXXXXXX') {
        updateWhatsAppLink(initialNumber);
    }
    
    // Payment method change handler
    document.addEventListener('change', function(e) {
        if (e.target.name === 'payment_method') {
            updatePaymentInstructions(e.target.value);
            toggleEmailField(e.target.value);
        }
    });
    
    // Shipping range change handler  
    document.querySelectorAll('input[name="shipping_range"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            updateShippingCharge();
        });
    });
    
    // Form submission handler
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        // Check device restriction before allowing submission
        if (window.deviceRestrictionActive || window.productRestrictionActive) {
            e.preventDefault();
            if (window.productRestrictionActive) {
                showAlert('Sorry! You have recently ordered this product. You can order again after the specified time.', 'danger'); // Translation: দুঃখিত! আপনি এই পণ্যটি সম্প্রতি অর্ডার করেছেন। নির্ধারিত সময় পর আবার অর্ডার করতে পারবেন।
            } else {
                showAlert('Hold on! The order interval from this device has not yet expired.', 'danger'); // Translation: অপেক্ষা করুন! এই ডিভাইস থেকে অর্ডার ইন্টারভ্যাল এখনো শেষ হয়নি।
            }
            return false;
        }
        
        const submitBtn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');
        const btnArrow = document.querySelector('.btn-arrow');
        const btnIcon = document.querySelector('.btn-icon');
        
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        btnText.style.display = 'none';
        if (btnArrow) btnArrow.style.display = 'none';
        if (btnIcon) btnIcon.style.display = 'none';
        btnSpinner.style.display = 'inline-block';
    });
});

// Enhanced Device Tracking Functions
function initializeDeviceTracking() {
    // Generate comprehensive device fingerprint
    const fingerprint = generateDeviceFingerprint();
    
    // Add fingerprint to form
    addHiddenField('device_fingerprint', fingerprint);
    
    // Check device restriction immediately on page load (including product restriction)
    setTimeout(checkDeviceRestriction, 1000);
    
    // Track phone changes with immediate checking
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');
    
    let phoneCheckTimeout;
    let lastCheckedPhone = '';
    
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            const currentPhone = this.value.trim();
            
            // Clear previous timeout
            if (phoneCheckTimeout) {
                clearTimeout(phoneCheckTimeout);
            }
            
            // Check device restriction even if phone changes
            if (currentPhone.length >= 10) {
                phoneCheckTimeout = setTimeout(() => {
                    lastCheckedPhone = currentPhone;
                    checkDeviceRestriction();
                }, 600); // Shorter delay for device-based checking
            } else if (currentPhone.length < 10) {
                // Don't hide warning just because phone is short - device might still be restricted
                // hideDeviceRestrictionWarning();
            }
        });
        
        phoneInput.addEventListener('blur', function() {
            const currentPhone = this.value.trim();
            if (currentPhone.length >= 10) {
                lastCheckedPhone = currentPhone;
                checkDeviceRestriction();
            }
        });
    }
    
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            setTimeout(checkDeviceRestriction, 300);
        });
    }
}

function generateDeviceFingerprint() {
    // Enhanced fingerprint generation
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    ctx.textBaseline = 'top';
    ctx.font = '14px Arial';
    ctx.fillText('Device fingerprint', 2, 2);
    
    const data = {
        screen: screen.width + 'x' + screen.height + 'x' + screen.colorDepth,
        timezone: new Date().getTimezoneOffset(),
        language: navigator.language,
        languages: navigator.languages ? navigator.languages.join(',') : '',
        platform: navigator.platform,
        userAgent: navigator.userAgent.substring(0, 150),
        cookieEnabled: navigator.cookieEnabled,
        doNotTrack: navigator.doNotTrack,
        canvas: canvas.toDataURL(),
        webgl: getWebGLFingerprint(),
        touchSupport: getTouchSupport(),
        fonts: getFontList(),
        plugins: getPluginList(),
        sessionId: getSessionId()
    };
    
    return btoa(JSON.stringify(data)).substring(0, 40);
}

function getWebGLFingerprint() {
    try {
        const canvas = document.createElement('canvas');
        const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
        if (!gl) return 'no-webgl';
        
        const renderer = gl.getParameter(gl.RENDERER);
        const vendor = gl.getParameter(gl.VENDOR);
        return vendor + '~' + renderer;
    } catch (e) {
        return 'webgl-error';
    }
}

function getTouchSupport() {
    return 'ontouchstart' in window || navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0;
}

function getFontList() {
    // Simple font detection
    const fonts = ['Arial', 'Times New Roman', 'Courier New', 'Helvetica', 'Comic Sans MS'];
    return fonts.filter(font => {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        ctx.font = `12px ${font}`;
        const width = ctx.measureText('test').width;
        ctx.font = '12px monospace';
        const defaultWidth = ctx.measureText('test').width;
        return width !== defaultWidth;
    }).join(',');
}

function getPluginList() {
    if (navigator.plugins) {
        return Array.from(navigator.plugins).map(p => p.name).slice(0, 5).join(',');
    }
    return 'no-plugins';
}

function getSessionId() {
    // Try to get session ID from various sources
    let sessionId = sessionStorage.getItem('device_session_id');
    if (!sessionId) {
        sessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        try {
            sessionStorage.setItem('device_session_id', sessionId);
        } catch (e) {
            // Handle cases where sessionStorage is not available
        }
    }
    return sessionId;
}

function addHiddenField(name, value) {
    const form = document.getElementById('checkout-form');
    if (form) {
        let input = form.querySelector(`input[name="${name}"]`);
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            form.appendChild(input);
        }
        input.value = value;
    }
}

// Device restriction checking functionality
window.deviceRestrictionActive = false;
window.productRestrictionActive = false; // NEW: Track product restriction
window.countdownInterval = null;
window.currentCheckRequest = null;

function checkDeviceRestriction() {
    // Only check for guest users
    if ({{ auth()->check() ? 'true' : 'false' }}) {
        return;
    }
    
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');
    
    if (!phoneInput || !phoneInput.value.trim()) {
        // Still check device restriction even without phone number
        // because device might be restricted from previous order
    }
    
    const phone = phoneInput ? phoneInput.value.trim() : '';
    const email = emailInput ? emailInput.value.trim() : '';
    
    // Get product ID from the form (for product-specific restriction check)
    const productIdInput = document.querySelector('input[name="id"]');
    const productId = productIdInput ? productIdInput.value : null;
    
    // Cancel previous request if still pending
    if (window.currentCheckRequest) {
        window.currentCheckRequest.abort();
    }
    
    // Create new AbortController for this request
    const controller = new AbortController();
    window.currentCheckRequest = controller;
    
    // Prepare request data
    const requestData = {
        phone: phone || 'temp_phone_check',
        email: email || 'noreply@lems.shop'
    };
    
    // Add product ID if available for product restriction check
    if (productId) {
        requestData.product_id = productId;
    }
    
    // Make AJAX request to check device restriction
    fetch('/check-order-interval', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(requestData),
        signal: controller.signal
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.restricted) {
            if (data.restriction_type === 'product') {
                // NEW: Handle product-specific restriction
                showProductRestrictionWarning(data);
            } else {
                // Handle device-based restriction
                showDeviceRestrictionWarning(data.remaining_seconds, data.whatsapp_number, data.phone_changed, data.last_phone, data.current_phone);
            }
        } else {
            hideDeviceRestrictionWarning();
            hideProductRestrictionWarning(); // NEW: Also hide product warning
        }
    })
    .catch(error => {
        if (error.name === 'AbortError') {
            console.log('Device restriction check was cancelled');
        } else {
            console.log('Device restriction check failed:', error);
            // On error, don't block the user - fail gracefully
            hideDeviceRestrictionWarning();
            hideProductRestrictionWarning(); // NEW: Also hide product warning
        }
    })
    .finally(() => {
        window.currentCheckRequest = null;
    });
}

// NEW: Show product restriction warning with enhanced device/IP detection
function showProductRestrictionWarning(data) {
    window.productRestrictionActive = true;
    const warningDiv = document.getElementById('order-interval-warning');
    const submitBtn = document.getElementById('submit-btn');
    const warningMessage = document.getElementById('warning-message');
    
    // Create enhanced product-specific warning message based on restriction reason
    let customMessage = "Sorry! "; // Translation: দুঃখিত!
    
    switch (data.restriction_reason) {
        case 'device':
            if (data.last_order_phone && data.last_order_phone !== (document.getElementById('phone')?.value || '')) {
                customMessage += `This product was ordered from this device with the number <strong>${data.last_order_phone}</strong> on <strong>${data.last_order_date}</strong>. You cannot order the same product even by changing the phone number.`; // Translation: এই ডিভাইস থেকে <strong>${data.last_order_phone}</strong> নম্বর দিয়ে এই পণ্যটি <strong>${data.last_order_date}</strong> তারিখে অর্ডার করা হয়েছে। ফোন নম্বর পরিবর্তন করেও একই পণ্য অর্ডার করা যাবে না।
            } else {
                customMessage += `You ordered this product on <strong>${data.last_order_date}</strong>.`; // Translation: আপনি এই পণ্যটি <strong>${data.last_order_date}</strong> তারিখে অর্ডার করেছেন।
            }
            customMessage += ` <span class="restriction-tag">Device Detected</span>`; // Translation: ডিভাইস শনাক্ত
            break;
        case 'ip':
            customMessage += `This product was ordered from this network on <strong>${data.last_order_date}</strong>. <span class="restriction-tag">IP Address Detected</span>`; // Translation: এই নেটওয়ার্ক থেকে এই পণ্যটি <strong>${data.last_order_date}</strong> তারিখে অর্ডার করা হয়েছে। <span class="restriction-tag">IP ঠিকানা শনাক্ত</span>
            break;
        default: // phone
            customMessage += `You ordered this product on <strong>${data.last_order_date}</strong>.`; // Translation: আপনি এই পণ্যটি <strong>${data.last_order_date}</strong> তারিখে অর্ডার করেছেন।
    }
    
    customMessage += ` You can order the same product again after <strong>${data.remaining_days} days</strong>. For a new order, contact us on WhatsApp <a href="#" id="whatsapp-link" class="whatsapp-link" target="_blank"><strong id="whatsapp-number">${data.whatsapp_number || '01XXXXXXXXX'}</strong></a>.`; // Translation: একই পণ্য আরো <strong>${data.remaining_days} দিন</strong> পর অর্ডার করতে পারবেন। নতুন অর্ডারের জন্য আমাদের WhatsApp <a href="#" id="whatsapp-link" class="whatsapp-link" target="_blank"><strong id="whatsapp-number">${data.whatsapp_number || '01XXXXXXXXX'}</strong></a> এ যোগাযোগ করুন।
    
    if (warningMessage) {
        warningMessage.innerHTML = customMessage;
    }
    
    // Update WhatsApp link
    if (data.whatsapp_number) {
        updateWhatsAppLink(data.whatsapp_number);
    }
    
    warningDiv.style.display = 'block';
    warningDiv.classList.add('show');
    submitBtn.disabled = true;
    
    // Add restriction-specific styling
    warningDiv.classList.add('product-restriction');
    warningDiv.setAttribute('data-reason', data.restriction_reason);
    
    // Scroll to warning if not visible
    setTimeout(() => {
        if (isElementInViewport(warningDiv) === false) {
            warningDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, 300);
    
    // Log the restriction for debugging
    console.log('Product restriction applied:', {
        reason: data.restriction_reason,
        days: data.remaining_days,
        device_matched: data.device_matched,
        ip_matched: data.ip_matched
    });
    
    // No countdown for product restriction - it's a fixed number of days
}

// NEW: Hide product restriction warning
function hideProductRestrictionWarning() {
    window.productRestrictionActive = false;
    // The warning div is shared with device restriction, so only hide if device restriction is also not active
    if (!window.deviceRestrictionActive) {
        const warningDiv = document.getElementById('order-interval-warning');
        const submitBtn = document.getElementById('submit-btn');
        
        warningDiv.style.display = 'none';
        warningDiv.classList.remove('show');
        warningDiv.classList.remove('product-restriction'); // Remove product restriction styling
        warningDiv.removeAttribute('data-reason'); // Remove data attribute
        submitBtn.disabled = false;
    }
}

function showDeviceRestrictionWarning(remainingSeconds, whatsappNumber = null, phoneChanged = false, lastPhone = null, currentPhone = null) {
    window.deviceRestrictionActive = true;
    const warningDiv = document.getElementById('order-interval-warning');
    const submitBtn = document.getElementById('submit-btn');
    const whatsappElement = document.getElementById('whatsapp-number');
    const whatsappLink = document.getElementById('whatsapp-link');
    const warningMessage = document.getElementById('warning-message');
    
    // Update warning message based on phone change
    if (phoneChanged && lastPhone && currentPhone) {
        const customMessage = `Hold on! An order was recently placed from this device using the number ${lastPhone}. You cannot place an order by changing the phone number. You can order again after <span class="countdown-timer" id="countdown-timer">--</span>. For any changes to your order, knock us on WhatsApp <a href="#" id="whatsapp-link" class="whatsapp-link" target="_blank"><strong id="whatsapp-number">${whatsappNumber || '01XXXXXXXXX'}</strong></a>.`; // Translation: অপেক্ষা করুন! এই ডিভাইস থেকে সম্প্রতি ${lastPhone} নম্বর দিয়ে অর্ডার করা হয়েছে। ফোন নম্বর পরিবর্তন করে অর্ডার করা যাবে না। আপনি <span class="countdown-timer" id="countdown-timer">--</span> পর আবার অর্ডার করতে পারবেন। অর্ডারের যেকোন পরিবর্তনের জন্য আমাদের WhatsApp <a href="#" id="whatsapp-link" class="whatsapp-link" target="_blank"><strong id="whatsapp-number">${whatsappNumber || '01XXXXXXXXX'}</strong></a> এ নক করুন।
        
        if (warningMessage) {
            warningMessage.innerHTML = customMessage;
        }
    } else {
        const defaultMessage = `Hold on! You have already placed an order from this device. You can order again after <span class="countdown-timer" id="countdown-timer">--</span>. This is to prevent fake orders. For any changes to your order, knock us on WhatsApp <a href="#" id="whatsapp-link" class="whatsapp-link" target="_blank"><strong id="whatsapp-number">${whatsappNumber || '01XXXXXXXXX'}</strong></a>.`; // Translation: অপেক্ষা করুন! এই ডিভাইস থেকে ইতিমধ্যে একটা অর্ডার করা হয়েছে। আপনি <span class="countdown-timer" id="countdown-timer">--</span> পর আবার অর্ডার করতে পারবেন। এটি ভুয়া অর্ডার প্রতিরোধের জন্য। অর্ডারের যেকোন পরিবর্তনের জন্য আমাদের WhatsApp <a href="#" id="whatsapp-link" class="whatsapp-link" target="_blank"><strong id="whatsapp-number">${whatsappNumber || '01XXXXXXXXX'}</strong></a> এ নক করুন।
        
        if (warningMessage) {
            warningMessage.innerHTML = defaultMessage;
        }
    }
    
    // Update WhatsApp number if provided
    if (whatsappNumber && whatsappElement) {
        whatsappElement.textContent = whatsappNumber;
        
        // Update WhatsApp link
        if (whatsappLink) {
            updateWhatsAppLink(whatsappNumber);
        }
    }
    
    warningDiv.style.display = 'block';
    warningDiv.classList.add('show');
    submitBtn.disabled = true;
    
    // Scroll to warning if not visible
    setTimeout(() => {
        if (isElementInViewport(warningDiv) === false) {
            warningDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, 300);
    
    startCountdown(remainingSeconds);
}

function hideDeviceRestrictionWarning() {
    window.deviceRestrictionActive = false;
    
    // Only hide the warning if product restriction is also not active
    if (!window.productRestrictionActive) {
        const warningDiv = document.getElementById('order-interval-warning');
        const submitBtn = document.getElementById('submit-btn');
        
        warningDiv.style.display = 'none';
        warningDiv.classList.remove('show');
        submitBtn.disabled = false;
    } else {
        // If product restriction is still active, just update the button state
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
    }
    
    if (window.countdownInterval) {
        clearInterval(window.countdownInterval);
        window.countdownInterval = null;
    }
}

function startCountdown(seconds) {
    const timerElement = document.getElementById('countdown-timer');
    if (!timerElement) return;
    
    let remaining = seconds;
    
    function updateTimer() {
        const minutes = Math.floor(remaining / 60);
        const secs = remaining % 60;
        const formatted = `${minutes}:${secs.toString().padStart(2, '0')}`;
        timerElement.textContent = formatted;
        
        if (remaining <= 0) {
            clearInterval(window.countdownInterval);
            window.countdownInterval = null;
            hideDeviceRestrictionWarning();
            return;
        }
        
        remaining--;
    }
    
    // Clear any existing countdown
    if (window.countdownInterval) {
        clearInterval(window.countdownInterval);
    }
    
    updateTimer();
    window.countdownInterval = setInterval(updateTimer, 1000);
}

// Utility function to check if element is in viewport
function isElementInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// WhatsApp link functionality
function updateWhatsAppLink(phoneNumber) {
    const whatsappLink = document.getElementById('whatsapp-link');
    const whatsappNumberElement = document.getElementById('whatsapp-number');
    
    if (whatsappLink && phoneNumber && phoneNumber !== '01XXXXXXXXX') {
        // Clean phone number
        const cleanNumber = phoneNumber.replace(/[\s\-\+]/g, '');
        
        // Format for WhatsApp
        let formattedNumber = cleanNumber;
        if (cleanNumber.startsWith('01')) {
            formattedNumber = '880' + cleanNumber.substring(1);
        }
        
        const message = encodeURIComponent('Assalamu Alaikum. I want to know about my order.'); // Translation: আসসালামু আলাইকুম। আমি আমার অর্ডার সম্পর্কে জানতে চাই।
        const whatsappUrl = `https://wa.me/${formattedNumber}?text=${message}`;
        
        whatsappLink.href = whatsappUrl;
        
        if (whatsappNumberElement) {
            whatsappNumberElement.textContent = phoneNumber;
        }
    }
}

// Payment and form handling functions
function updatePaymentInstructions(method) {
    const instructionsDiv = document.getElementById('payment-instructions');
    const detailsDiv = document.getElementById('payment-details');
    
    let instructions = '';
    let details = '';
    
    const bkash = "{{ setting('bkash') }}";
    const nagad = "{{ setting('nagad') }}";
    const rocket = "{{ setting('rocket') }}";
    const bank = "{!! setting('bank_name') !!}";
    const branch = "{!! setting('branch_name') !!}";
    const holder = "{!! setting('holder_name') !!}";
    const account = "{!! setting('bank_account') !!}";
    
    switch(method) {
        case 'Cash on Delivery':
            instructions = '💵 Pay upon receiving the product.'; // Translation: পণ্য হাতে পেয়ে টাকা পরিশোধ করুন।
            break;
        case 'Bkash':
            instructions = `📱 Send money to <strong>${bkash}</strong> and provide transaction details below.`; // Translation: 📱 <strong>${bkash}</strong> নম্বরে টাকা পাঠান এবং নিচে ট্রানজেকশনের বিবরণ দিন।
            details = getMobilePaymentForm();
            break;
        case 'Nagad':
            instructions = `📱 Send money to <strong>${nagad}</strong> and provide transaction details below.`; // Translation: 📱 <strong>${nagad}</strong> নম্বরে টাকা পাঠান এবং নিচে ট্রানজেকশনের বিবরণ দিন।
            details = getMobilePaymentForm();
            break;
        case 'Rocket':
            instructions = `📱 Send money to <strong>${rocket}</strong> and provide transaction details below.`; // Translation: 📱 <strong>${rocket}</strong> নম্বরে টাকা পাঠান এবং নিচে ট্রানজেকশনের বিবরণ দিন।
            details = getMobilePaymentForm();
            break;
        case 'Bank':
            instructions = `🏦 Send money to the bank account below and provide transaction details.<br>
                           <strong>Bank:</strong> ${bank}<br>
                           <strong>Branch:</strong> ${branch}<br>
                           <strong>Account Holder:</strong> ${holder}<br>
                           <strong>Account Number:</strong> ${account}`; // Translation: 🏦 নিচের ব্যাংক অ্যাকাউন্টে টাকা পাঠান এবং ট্রানজেকশনের বিবরণ দিন।
            details = getBankTransferForm();
            break;
        case 'aamarpay':
        case 'uddoktapay':
            instructions = '🌐 You will be redirected to complete the secure online payment.'; // Translation: নিরাপদে অনলাইন পেমেন্ট সম্পন্ন করতে আপনাকে রিডিরেক্ট করা হবে।
            break;
        default:
            instructions = '💵 Pay upon receiving the product.'; // Translation: পণ্য হাতে পেয়ে টাকা পরিশোধ করুন।
    }
    
    instructionsDiv.innerHTML = `<p>${instructions}</p>`;
    detailsDiv.innerHTML = details;
}

function getMobilePaymentForm() {
    return `
        <div class="form-group">
            <label class="form-label" for="mobile_number">Your Mobile Number</label> {{-- Translation: আপনার মোবাইল নম্বর --}}
            <input type="text" name="mobile_number" id="mobile_number" class="form-input" placeholder="e.g.: 01712345678" required> {{-- Translation: যেমন: ০১৭১২৩৪৫৬৭৮ --}}
        </div>
        <div class="form-group">
            <label class="form-label" for="transaction_id">Transaction ID</label> {{-- Translation: ট্রানজেকশন আইডি --}}
            <input type="text" name="transaction_id" id="transaction_id" class="form-input" placeholder="Enter Transaction ID" required> {{-- Translation: ট্রানজেকশন আইডি লিখুন --}}
        </div>
    `;
}

function getBankTransferForm() {
    return `
        <div class="form-group">
            <label class="form-label" for="bank_name">Bank Name</label> {{-- Translation: ব্যাংকের নাম --}}
            <input type="text" name="bank_name" id="bank_name" class="form-input" placeholder="Enter Bank Name" required> {{-- Translation: ব্যাংকের নাম লিখুন --}}
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="account_number">Account Number</label> {{-- Translation: অ্যাকাউন্ট নম্বর --}}
                <input type="text" name="account_number" id="account_number" class="form-input" placeholder="Account Number" required> {{-- Translation: অ্যাকাউন্ট নম্বর --}}
            </div>
            <div class="form-group">
                <label class="form-label" for="routing">Routing Number</label> {{-- Translation: রাউটিং নম্বর --}}
                <input type="text" name="routing" id="routing" class="form-input" placeholder="Routing Number" required> {{-- Translation: রাউটিং নম্বর --}}
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="holder_name">Account Holder</label> {{-- Translation: অ্যাকাউন্ট হোল্ডার --}}
                <input type="text" name="holder_name" id="holder_name" class="form-input" placeholder="Account Holder Name" required> {{-- Translation: অ্যাকাউন্ট হোল্ডারের নাম --}}
            </div>
            <div class="form-group">
                <label class="form-label" for="branch">Branch Name</label> {{-- Translation: শাখার নাম --}}
                <input type="text" name="branch" id="branch" class="form-input" placeholder="Branch Name" required> {{-- Translation: শাখার নাম --}}
            </div>
        </div>
    `;
}

function toggleEmailField(method) {
    const emailWrap = document.getElementById('email_wrap');
    const emailInput = document.getElementById('email');
    
    // The previous logic for Uddoktapay is now redundant as the field is always visible and required by the HTML change.
    // However, keeping the toggle logic in case of future conditional needs.
    // Since the user requested the email to be fillable by default, we primarily rely on the HTML/Blade change.
    if (method === 'uddoktapay') {
        emailInput.required = true;
    } else {
        // Since the field is now required in the Blade file, we only need to ensure it's still treated as such, 
        // unless a payment method specifically allows it to be optional (which is not the case here).
        // Removing the 'required = false' line from the else block to maintain the default required state.
        emailInput.required = true;
    }
}

function updateShippingCharge() {
    const shippingRange = document.querySelector('input[name="shipping_range"]:checked').value;
    const downloadAble = "{!! $product->download_able !!}";
    let shippingCharge = 0;
    
    if (downloadAble != 1) {
        if (shippingRange == 1) {
            shippingCharge = parseInt("{!! setting('shipping_charge') !!}");
        } else {
            shippingCharge = parseInt("{!! setting('shipping_charge_out_of_range') !!}");
        }
    }
    
    document.getElementById('ship-charge').textContent = 
        formatNumber(shippingCharge, 2) + ' {{ setting('CURRENCY_CODE_MIN') ?? 'টাকা' }}';
    
    updateTotal();
    updateButtonTotal();
}

function updateTotal() {
    const subtotal = parseFloat(document.getElementById('sub-total').textContent.replace(/[^0-9.-]+/g, ''));
    const shipping = parseFloat(document.getElementById('ship-charge').textContent.replace(/[^0-9.-]+/g, ''));
    const coupon = parseFloat(document.getElementById('coupon-discount').textContent.replace(/[^0-9.-]+/g, '')) || 0;
    
    const total = subtotal + shipping - coupon;
    const formattedTotal = `${formatNumber(total, 2)} {{ setting('CURRENCY_CODE_MIN') ?? 'টাকা' }}`;
    
    // Update summary total
    document.getElementById('total').innerHTML = formattedTotal;
}

function updateButtonTotal() {
    // NOTE: This function's logic is kept for potential future use or if the total is needed elsewhere in the button logic, 
    // but the displayed output in the HTML has been removed as requested.
    const subtotal = parseFloat(document.getElementById('sub-total').textContent.replace(/[^0-9.-]+/g, ''));
    const shipping = parseFloat(document.getElementById('ship-charge').textContent.replace(/[^0-9.-]+/g, ''));
    const coupon = parseFloat(document.getElementById('coupon-discount').textContent.replace(/[^0-9.-]+/g, '')) || 0;
    
    const total = subtotal + shipping - coupon;
    const formattedTotal = `${formatNumber(total, 2)} {{ setting('CURRENCY_CODE_MIN') ?? 'টাকা' }}`;
    
    // Update button total (This element is now hidden/removed in the HTML structure)
    const btnTotalValue = document.getElementById('btn-total-value');
    if(btnTotalValue) {
        btnTotalValue.innerHTML = formattedTotal;
    }
}

function toggleCoupon() {
    const couponForm = document.getElementById('coupon-form');
    const arrow = document.getElementById('coupon-arrow');
    
    if (couponForm.style.display === 'none') {
        couponForm.style.display = 'block';
        arrow.textContent = '−';
    } else {
        couponForm.style.display = 'none';
        arrow.textContent = '+';
    }
}

function applyCoupon() {
    const code = document.getElementById('coupon').value.trim();
    const id = "{!! $request->id !!}";
    const qty = "{!! $request->qty !!}";
    const dynamicPrice = "{!! $request->dynamic_price !!}";
    
    if (!code) {
        showAlert('Please enter a coupon code', 'danger'); // Translation: দয়া করে একটি কুপন কোড লিখুন
        return;
    }
    
    // Show loading state
    const applyBtn = document.querySelector('.btn-apply');
    const originalText = applyBtn.textContent;
    applyBtn.disabled = true;
    applyBtn.innerHTML = '<span class="spinner"></span>';
    
    fetch(`/apply/coupon/buy-now/${code}/${id}/${qty}/${dynamicPrice}`)
        .then(response => response.json())
        .then(data => {
            if (data.alert === 'success') {
                showAlert(data.message, 'success');
                document.getElementById('coupon-discount').textContent = formatNumber(data.discount, 2);
                document.getElementById('coupon-name').textContent = `(${code})`;
                document.getElementById('coupon-row').style.display = 'flex';
                document.getElementById('coupon').value = '';
                updateTotal();
                updateButtonTotal();
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            showAlert('An error occurred. Please try again.', 'danger'); // Translation: একটি ত্রুটি ঘটেছে। অনুগ্রহ করে আবার চেষ্টা করুন।
        })
        .finally(() => {
            applyBtn.disabled = false;
            applyBtn.textContent = originalText;
        });
}

function showAlert(message, type) {
    const alertContainer = document.querySelector('.alert-container');
    const alertMessage = document.getElementById('alert-message');
    
    alertMessage.className = `alert alert-${type}`;
    alertMessage.textContent = message;
    alertContainer.style.display = 'block';
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        alertContainer.style.display = 'none';
    }, 5000);
    
    // Scroll to alert
    alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function formatNumber(number, decimals) {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
}

function toggleMorePayments() {
    const additionalPayments = document.getElementById('additional-payments');
    const toggle = document.querySelector('.see-more-toggle');
    const toggleText = document.querySelector('.see-more-text');
    
    if (additionalPayments.style.display === 'none') {
        additionalPayments.style.display = 'grid';
        setTimeout(() => {
            additionalPayments.classList.add('show');
        }, 10);
        toggle.classList.add('expanded');
        toggleText.textContent = 'See Fewer Options'; // Translation: কম অপশন দেখুন
    } else {
        additionalPayments.classList.remove('show');
        toggle.classList.remove('expanded');
        toggleText.textContent = 'See More Payment Options'; // Translation: আরো পেমেন্ট অপশন দেখুন
        setTimeout(() => {
            additionalPayments.style.display = 'none';
        }, 300);
    }
}

// Cleanup function to prevent memory leaks
window.addEventListener('beforeunload', function() {
    if (window.countdownInterval) {
        clearInterval(window.countdownInterval);
    }
    if (window.currentCheckRequest) {
        window.currentCheckRequest.abort();
    }
});

// Additional helper function for better UX
function validatePhoneNumber(phone) {
    // Basic phone validation for Bangladesh numbers
    const cleanPhone = phone.replace(/[^0-9]/g, '');
    return cleanPhone.length >= 11 && (cleanPhone.startsWith('01') || cleanPhone.startsWith('8801'));
}

// Enhanced error handling for network issues
function handleNetworkError(error) {
    console.error('Network error:', error);
    
    // Show user-friendly message for network issues
    if (!navigator.onLine) {
        showAlert('Check your internet connection and try again.', 'danger'); // Translation: ইন্টারনেট সংযোগ পরীক্ষা করুন এবং আবার চেষ্টা করুন।
    } else {
        showAlert('Server connection issue. Please try again shortly.', 'danger'); // Translation: সার্ভার সংযোগে সমস্যা। অনুগ্রহ করে কিছুক্ষণ পর আবার চেষ্টা করুন।
    }
}
</script>
@endpush