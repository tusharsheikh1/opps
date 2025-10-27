@extends('layouts.frontend.app')

@push('meta')
    <meta name='description' content="Buy now product" />
    {{-- Check if $product is set before trying to access its tags --}}
    @isset($product) 
    <meta name='keywords' content="@foreach ($product->tags as $tag){{ $tag->name . ', ' }} @endforeach" />
    @endisset
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
            <h1>Complete Your Order</h1>
            <p>Complete your order in just a few steps</p>
        </div>

        <div class="order-interval-warning" id="order-interval-warning">
            <div class="warning-header">
                <div class="warning-icon">⚠️</div>
                <h4 class="warning-title">Order Restriction</h4>
            </div>
            <p class="warning-text" id="warning-message">
                Wait! You have already placed an order. You can order again after <span class="countdown-timer" id="countdown-timer">--</span>. This is to prevent fake orders. For any changes to the order, please contact us on WhatsApp <a href="#" id="whatsapp-link" class="whatsapp-link" target="_blank">
                    <strong id="whatsapp-number">{{ setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX' }}</strong>
                </a>.
            </p>
        </div>

        {{-- 
          FIX: The form action is now dynamic.
          - If $product is set (Buy Now flow), it submits to 'order.buy.store_minimal'.
          - If $product is NOT set (Cart flow), it submits to 'order.store_minimal'.
        --}}
        <form action="{{ isset($product) ? route('order.buy.store_minimal') : route('order.store_minimal') }}" method="POST" id="checkout-form">
            @csrf
            
            <div class="checkout-grid">
                <div class="checkout-forms">
                    <div class="alert-container" style="display: none;">
                        <div class="alert" id="alert-message"></div>
                    </div>

                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="section-number">1</div>
                            <h3 class="section-title">Customer Information</h3>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="first_name">
                                Full Name <span class="required">*</span>
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
                                Phone Number <span class="required">*</span>
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

                        {{-- START: Updated Email Field (Always visible) --}}
                        <div class="form-group" id="email_wrap"> 
                            <label class="form-label" for="email">
                                Email Address
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-input @error('email') error @enderror"
                            />
                            @error('email')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- END: Updated Email Field --}}

                        <div class="form-group">
                            <label class="form-label" for="address">Full Address</label>
                            <textarea 
                                id="address" 
                                name="address" 
                                class="form-textarea @error('address') error @enderror"
                                placeholder="Write your full address..."
                            ></textarea>
                            @error('address')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Check if $product is set before accessing its properties --}}
                        @if (isset($product) && $product->sheba == 1)
                        <div class="form-group">
                            <label class="form-label" for="meet">Service Date</label>
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
                                            <div class="shipping-title">Inside Dhaka City</div>
                                            <div class="shipping-charge">{{ setting('shipping_charge') }} {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}</div>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="shipping-option">
                                    <input type="radio" name="shipping_range" value="0" id="dhaka_outside" checked>
                                    <label for="dhaka_outside" class="shipping-label">
                                        <div class="shipping-icon">🏘️</div>
                                        <div class="shipping-info">
                                            <div class="shipping-title">Outside Dhaka City</div>
                                            <div class="shipping-charge">{{ setting('shipping_charge_out_of_range') }} {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Check if $request is set before accessing it --}}
                        @if (isset($request) && !empty($request->pr))
                            <input type="hidden" name="pr" value="{{ $request->pr }}">
                        @endif
                        {{-- Add default value for dynamic_price --}}
                        <input type="hidden" value="{{ $request->dynamic_price ?? 0 }}" name="dynamic_prices">
                    </div>

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
                                    <span class="payment-text">Cash on Delivery</span>
                                </label>
                            </div>
                            @endif

                            @if (setting('g_bkash') == 'true')
                            <div class="payment-option">
                                <input type="radio" name="payment_method" value="Bkash" id="Bkash">
                                <label for="Bkash" class="payment-label">
                                    <img src="{{ asset('/') }}icon/bkash.png" alt="bKash" class="payment-icon">
                                    <span class="payment-text">bKash</span>
                                </label>
                            </div>
                            @endif

                            <div class="see-more-toggle" onclick="toggleMorePayments()">
                                <span class="see-more-text">See more payment options</span>
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
                                        <span class="payment-text">Aamarpay</span>
                                    </label>
                                </div>
                                @endif

                                @if (setting('g_uddok') == 'true')
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="uddoktapay" id="uddoktapay">
                                    <label for="uddoktapay" class="payment-label">
                                        <img src="{{ asset('/') }}icon/uddoktapay.png" alt="Uddoktapay" class="payment-icon">
                                        <span class="payment-text">Uddoktapay</span>
                                    </label>
                                </div>
                                @endif

                                @if (setting('g_nagad') == 'true')
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="Nagad" id="Nagad">
                                    <label for="Nagad" class="payment-label">
                                        <img src="{{ asset('/') }}icon/nagad.png" alt="Nagad" class="payment-icon">
                                        <span class="payment-text">Nagad</span>
                                    </label>
                                </div>
                                @endif

                                @if (setting('g_rocket') == 'true')
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="Rocket" id="Rocket">
                                    <label for="Rocket" class="payment-label">
                                        <img src="{{ asset('/') }}icon/rocket.png" alt="Rocket" class="payment-icon">
                                        <span class="payment-text">Rocket</span>
                                    </label>
                                </div>
                                @endif

                                @if (setting('g_bank') == 'true')
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="Bank" id="Bank">
                                    <label for="Bank" class="payment-label">
                                        <img src="{{ asset('/') }}icon/bank.png" alt="Bank Transfer" class="payment-icon">
                                        <span class="payment-text">Bank Transfer</span>
                                    </label>
                                </div>
                                @endif
                            </div>
                        </div>

                        @error('payment_method')
                            <span class="error-message">{{ $message }}</span>
                        @enderror

                        <div id="payment-instructions" class="payment-details">
                            <p>💵 Pay upon receiving the product.</p>
                        </div>

                        <div id="payment-details"></div>
                    </div>
                </div>

                <div class="order-summary">
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="section-number">3</div>
                            <h3 class="section-title">Order Summery</h3>
                        </div>

                        {{-- 
                          FIX: This block now dynamically shows EITHER the single product (Buy Now)
                          OR all cart items (Cart Checkout).
                        --}}
                        @isset($product)
                            {{-- START: BUY NOW (SINGLE PRODUCT) LOGIC --}}
                            <?php
                            // Use $request?? for safe access with default values
                            $req_qty = $request->qty ?? 1;
                            $req_dynamic_price = $request->dynamic_price ?? $product->price; 

                            if ($req_qty >= 6 && $product->whole_price > 0) {
                                $sub_total = $product->whole_price * $req_qty;
                            } else {
                                $sub_total = $req_dynamic_price * $req_qty;
                            }
                            
                            // IMPROVED: Build attributes display for Buy Now
                            $buyNowAttributes = [];
                            
                            // Get color if selected
                            if (isset($request->color) && $request->color != 'blank') {
                                try {
                                    $color = \App\Models\Color::where('slug', $request->color)->first();
                                    if ($color) {
                                        $buyNowAttributes['Color'] = $color->name;
                                    }
                                } catch (\Exception $e) {
                                    // Silently fail
                                }
                            }
                            
                            // Get size and other attributes
                            if (isset($request->size) && $request->size != 'blank') {
                                try {
                                    $sizeAttr = \App\Models\AttributeValue::find($request->size);
                                    if ($sizeAttr) {
                                        $buyNowAttributes['Size'] = $sizeAttr->value;
                                    }
                                } catch (\Exception $e) {
                                    // Silently fail
                                }
                            }
                            
                            // Get other attributes from request
                            $allAttributes = DB::table('attributes')->get();
                            foreach ($allAttributes as $attr) {
                                $slug = $attr->slug;
                                if ($slug !== 'size' && isset($request->$slug) && $request->$slug != 'blank') {
                                    try {
                                        $attrValue = \App\Models\AttributeValue::find($request->$slug);
                                        if ($attrValue) {
                                            $buyNowAttributes[$attr->name] = $attrValue->value;
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
                                    <h4 class="product-title">{{ $product->title }} (x{{ $req_qty }})</h4>
                                    
                                    {{-- IMPROVED: Display attributes for Buy Now --}}
                                    @if (count($buyNowAttributes) > 0)
                                        <div class="product-attributes" style="font-size: 0.85rem; color: #6b7280; margin-top: 0.25rem;">
                                            @foreach ($buyNowAttributes as $attrName => $attrValue)
                                                <strong>{{ $attrName }}:</strong> {{ $attrValue }}@if (!$loop->last), @endif
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <div class="product-price">{{ number_format($sub_total, 2) }} {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}</div>
                                </div>
                            </div>
                            {{-- END: BUY NOW (SINGLE PRODUCT) LOGIC --}}
                        @else
                            {{-- START: CART (MULTIPLE PRODUCTS) LOGIC --}}
                            @php
                                $cartItems = \Gloudemans\Shoppingcart\Facades\Cart::content();
                                // Get subtotal as a raw number, matching the cart controller
                                $sub_total = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', '');
                                // Get unique seller count, which is needed for orderStore_minimal
                                $seller_count = $cartItems->groupBy('weight')->count(); 
                            @endphp
                            
                            {{-- Hidden field for seller count, required by orderStore_minimal --}}
                            <input type="hidden" name="seller_count" value="{{ $seller_count }}">
                            {{-- Hidden field for subtotal, required by orderStore_minimal --}}
                            <input type="hidden" name="stotal" value="{{ $sub_total }}">

                            @foreach ($cartItems as $item)
                                @php
                                    // IMPROVED: Format attributes for each cart item
                                    $itemAttributes = [];
                                    
                                    // Convert options to array for easier access
                                    $options = $item->options;
                                    if (is_object($options)) {
                                        $options = $options->toArray();
                                    }
                                    
                                    // Get Color
                                    if (!empty($options['color']) && $options['color'] != 'blank') {
                                        try {
                                            $color = \App\Models\Color::where('slug', $options['color'])->first();
                                            if ($color) {
                                                $itemAttributes['Color'] = $color->name;
                                            }
                                        } catch (\Exception $e) {
                                            // Silently fail
                                        }
                                    }
                                    
                                    // Get Size and other attributes
                                    if (isset($options['attributes'])) {
                                        $attributes = $options['attributes'];
                                        // Convert to array if it's an object
                                        if (is_object($attributes)) {
                                            $attributes = (array) $attributes;
                                        }
                                        
                                        if (is_array($attributes) && !empty($attributes)) {
                                            foreach ($attributes as $slug => $attrId) {
                                                try {
                                                    $attrValue = \App\Models\AttributeValue::find($attrId);
                                                    if ($attrValue) {
                                                        if ($slug === 'size') {
                                                            $itemAttributes['Size'] = $attrValue->value;
                                                        } else {
                                                            $attrName = $attrValue->attribute->name ?? ucfirst($slug);
                                                            $itemAttributes[$attrName] = $attrValue->value;
                                                        }
                                                    }
                                                } catch (\Exception $e) {
                                                    // Silently fail
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                
                                <div class="product-item">
                                    <img src="{{ $item->options->image ? asset('uploads/product/' . $item->options->image) : asset('path/to/default-image.png') }}" alt="{{ $item->name }}" class="product-image">
                                    <div class="product-info">
                                        <h4 class="product-title">{{ $item->name }} (x{{ $item->qty }})</h4>
                                        
                                        {{-- IMPROVED: Display attributes for Cart items --}}
                                        @if (count($itemAttributes) > 0)
                                            <div class="product-attributes" style="font-size: 0.85rem; color: #6b7280; margin-top: 0.25rem;">
                                                @foreach ($itemAttributes as $attrName => $attrValue)
                                                    <strong>{{ $attrName }}:</strong> {{ $attrValue }}@if (!$loop->last), @endif
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <div class="product-price">{{ number_format($item->price * $item->qty, 2) }} {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}</div>
                                    </div>
                                </div>
                            @endforeach
                            {{-- END: CART (MULTIPLE PRODUCTS) LOGIC --}}
                        @endisset


                        {{-- Add default values for $request properties --}}
                        <input type="hidden" name="id" value="{{ $request->id ?? null }}">
                        <input type="hidden" name="qty" value="{{ $request->qty ?? 1 }}">
                        <?php
                        // FIX: Ensure $request and $product are available before trying to access properties
                        $attr = [];
                        if (isset($product) && isset($request)) { 
                            $attributes = DB::table('attributes')->get();
                            foreach ($attributes as $attribute) {
                                // Check for attribute value in request
                                $slug = $attribute->slug;
                                if (isset($request->$slug)) {
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
                                        $attr[$slug] = $request->$slug;
                                    }
                                }
                            }
                        }
                        ?>
                        <input type="hidden" name="size" value="{{ $attr != '' ? json_encode($attr) : 'blank' }}">
                        <input type="hidden" name="color" value="{{ $request->color ?? 'blank' }}">

                        <div class="coupon-section">
                            <div class="coupon-toggle" onclick="toggleCoupon()">
                                <span class="form-label">Have a coupon?</span>
                                <span id="coupon-arrow">+</span>
                            </div>
                            <div id="coupon-form" style="display: none;">
                                <div class="coupon-input-group">
                                    <input type="text" id="coupon" class="form-input coupon-input" placeholder="Enter coupon code">
                                    <button type="button" class="btn-apply" onclick="applyCoupon()">Apply</button>
                                </div>
                            </div>
                        </div>

                        <div class="summary-row">
                            <span>Subtotal</span>
                            {{-- This $sub_total variable is now set correctly for both cart and buy-now --}}
                            <span id="sub-total">{{ number_format($sub_total, 2) }} {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}</span>
                        </div>

                        <div class="summary-row">
                            <span>Delivery Charge</span>
                            <span id="ship-charge">
                                @if (isset($order->shipping_charge))
                                    {{ number_format($order->shipping_charge, 2) }}
                                @else
                                    0.00
                                @endif
                                {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}
                            </span>
                        </div>

                        <div class="summary-row" id="coupon-row" style="display: none;">
                            <span>Coupon <span id="coupon-name"></span></span>
                            <span style="color: #10b981;">-<span id="coupon-discount">0.00</span> {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}</span>
                        </div>

                        <div class="summary-row total">
                            <span>Total</span>
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
                                {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}
                            </span>
                        </div>

                        <button type="submit" class="submit-btn" id="submit-btn">
                            <div class="btn-content">
                                <div class="btn-action">
                                    <span class="btn-icon">🛒</span>
                                    {{-- UPDATED: Removed total amount and currency from button text --}}
                                    <span id="btn-text">Place Order</span>
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
    // Removed: updateButtonTotal(); 
    
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
                showAlert('Sorry! You have recently ordered this product. You can order again after the specified time.', 'danger');
            } else {
                showAlert('Wait! The order interval for this device is not over yet.', 'danger');
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
        email: email // FIX: Removed default email, now uses user input or empty string
    };
    
    // Add product ID if available for product restriction check
    // Only add if it's not null/empty (i.e., in Buy Now mode)
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
    let customMessage = "Sorry! ";
    
    switch (data.restriction_reason) {
        case 'device':
            if (data.last_order_phone && data.last_order_phone !== (document.getElementById('phone')?.value || '')) {
                customMessage += `This product was ordered from this device with the number **${data.last_order_phone}** on **${data.last_order_date}**. You cannot order the same product by changing the phone number.`;
            } else {
                customMessage += `You ordered this product on **${data.last_order_date}**.`;
            }
            customMessage += ` <span class="restriction-tag">Device Detected</span>`;
            break;
        case 'ip':
            customMessage += `This product was ordered from this network on **${data.last_order_date}**. <span class="restriction-tag">IP Address Detected</span>`;
            break;
        default: // phone
            customMessage += `You ordered this product on **${data.last_order_date}**.`;
    }
    
    customMessage += ` You can order the same product again after **${data.remaining_days} days**. For new orders, please contact us on WhatsApp <a href="#" id="whatsapp-link" class="whatsapp-link" target="_blank"><strong id="whatsapp-number">${data.whatsapp_number || '01XXXXXXXXX'}</strong></a>.`;
    
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
        const customMessage = `Wait! An order was recently placed from this device with the number ${lastPhone}. You cannot place an order by changing the phone number. You can order again after <span class="countdown-timer" id="countdown-timer">--</span>. For any changes to the order, please contact us on WhatsApp <a href="#" id="whatsapp-link" class="whatsapp-link" target="_blank"><strong id="whatsapp-number">${whatsappNumber || '01XXXXXXXXX'}</strong></a>.`;
        
        if (warningMessage) {
            warningMessage.innerHTML = customMessage;
        }
    } else {
        const defaultMessage = `Wait! You have already placed an order from this device. You can order again after <span class="countdown-timer" id="countdown-timer">--</span>. This is to prevent fake orders. For any changes to the order, please contact us on WhatsApp <a href="#" id="whatsapp-link" class="whatsapp-link" target="_blank"><strong id="whatsapp-number">${whatsappNumber || '01XXXXXXXXX'}</strong></a>.`;
        
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
        
        const message = encodeURIComponent('Assalamu Alaikum. I want to inquire about my order.');
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
            instructions = '💵 Pay upon receiving the product.';
            break;
        case 'Bkash':
            instructions = `📱 Send money to **${bkash}** and provide transaction details below.`;
            details = getMobilePaymentForm();
            break;
        case 'Nagad':
            instructions = `📱 Send money to **${nagad}** and provide transaction details below.`;
            details = getMobilePaymentForm();
            break;
        case 'Rocket':
            instructions = `📱 Send money to **${rocket}** and provide transaction details below.`;
            details = getMobilePaymentForm();
            break;
        case 'Bank':
            instructions = `🏦 Send money to the bank account below and provide transaction details.<br>
                           **Bank:** ${bank}<br>
                           **Branch:** ${branch}<br>
                           **Account Holder:** ${holder}<br>
                           **Account Number:** ${account}`;
            details = getBankTransferForm();
            break;
        case 'aamarpay':
        case 'uddoktapay':
            instructions = '🌐 You will be redirected to complete the online payment securely.';
            break;
        default:
            instructions = '💵 Pay upon receiving the product.';
    }
    
    instructionsDiv.innerHTML = `<p>${instructions}</p>`;
    detailsDiv.innerHTML = details;
}

function getMobilePaymentForm() {
    return `
        <div class="form-group">
            <label class="form-label" for="mobile_number">Your Mobile Number</label>
            <input type="text" name="mobile_number" id="mobile_number" class="form-input" placeholder="e.g. 01712345678" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="transaction_id">Transaction ID</label>
            <input type="text" name="transaction_id" id="transaction_id" class="form-input" placeholder="Enter Transaction ID" required>
        </div>
    `;
}

function getBankTransferForm() {
    return `
        <div class="form-group">
            <label class="form-label" for="bank_name">Bank Name</label>
            <input type="text" name="bank_name" id="bank_name" class="form-input" placeholder="Enter Bank Name" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="account_number">Account Number</label>
                <input type="text" name="account_number" id="account_number" class="form-input" placeholder="Account Number" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="routing">Routing Number</label>
                <input type="text" name="routing" id="routing" class="form-input" placeholder="Routing Number" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="holder_name">Account Holder</label>
                <input type="text" name="holder_name" id="holder_name" class="form-input" placeholder="Account Holder's Name" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="branch">Branch Name</label>
                <input type="text" name="branch" id="branch" class="form-input" placeholder="Branch Name" required>
            </div>
        </div>
    `;
}

function toggleEmailField(method) {
    const emailInput = document.getElementById('email');
    const emailLabel = document.querySelector('#email_wrap label');
    
    // Only manage the required state for specific payment methods
    if (method === 'uddoktapay') {
        emailInput.required = true;
        // Add required mark to label
        if (emailLabel && !emailLabel.querySelector('.required')) {
            emailLabel.innerHTML = emailLabel.textContent.trim() + ' <span class="required">*</span>';
        }
    } else {
        emailInput.required = false;
        // Remove required mark from label
        const requiredMark = emailLabel?.querySelector('.required');
        if (requiredMark) {
             requiredMark.remove();
        }
    }
}

function updateShippingCharge() {
    const shippingRange = document.querySelector('input[name="shipping_range"]:checked').value;
    // Add a check here in case $product is not set
    const downloadAble = "{!! isset($product) ? $product->download_able : 0 !!}"; 
    let shippingCharge = 0;
    
    if (downloadAble != 1) {
        if (shippingRange == 1) {
            shippingCharge = parseInt("{!! setting('shipping_charge') !!}");
        } else {
            shippingCharge = parseInt("{!! setting('shipping_charge_out_of_range') !!}");
        }
    }
    
    @unless(isset($product))
        const sellerCount = parseInt(document.querySelector('input[name="seller_count"]').value) || 1;
    @endunless

    document.getElementById('ship-charge').textContent = 
        formatNumber(shippingCharge, 2) + ' {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}';
    
    updateTotal();
    // Removed: updateButtonTotal(); 
}

function updateTotal() {
    const subtotal = parseFloat(document.getElementById('sub-total').textContent.replace(/[^0-9.-]+/g, ''));
    const shipping = parseFloat(document.getElementById('ship-charge').textContent.replace(/[^0-9.-]+/g, ''));
    const coupon = parseFloat(document.getElementById('coupon-discount').textContent.replace(/[^0-9.-]+/g, '')) || 0;
    
    const total = subtotal + shipping - coupon;
    const formattedTotal = `${formatNumber(total, 2)} {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}`;
    
    // Update summary total
    document.getElementById('total').innerHTML = formattedTotal;
}

// Removed: function updateButtonTotal() {}

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
    
    // Check if we are in "Buy Now" mode or "Cart" mode
    const isBuyNow = {{ isset($product) ? 'true' : 'false' }};
    
    let url = '';
    
    if (isBuyNow) {
        // FIX: Safely retrieve all necessary data from hidden fields in the form
        const id = document.querySelector('input[name="id"]').value || 'null';
        const qty = document.querySelector('input[name="qty"]').value || '1';
        const dynamicPrice = document.querySelector('input[name="dynamic_prices"]').value || '0';
        // Note: The /apply/coupon/buy-now/{code}/{id}/{qty}/{price} route needs to be available
        url = `/apply/coupon/buy-now/${code}/${id}/${qty}/${dynamicPrice}`;
    } else {
        // Cart mode
        const subtotal = parseFloat(document.getElementById('sub-total').textContent.replace(/[^0-9.-]+/g, ''));
        // Note: The /apply/coupon/{code}/{subtotal} route needs to be available
        url = `/apply/coupon/${code}/${subtotal}`;
    }

    if (!code) {
        showAlert('Please enter a coupon code', 'danger');
        return;
    }
    
    // Show loading state
    const applyBtn = document.querySelector('.btn-apply');
    const originalText = applyBtn.textContent;
    applyBtn.disabled = true;
    applyBtn.innerHTML = '<span class="spinner"></span>';
    
    fetch(url) // Use the dynamic URL
        .then(response => response.json())
        .then(data => {
            if (data.alert === 'success') {
                showAlert(data.message, 'success');
                document.getElementById('coupon-discount').textContent = formatNumber(data.discount, 2);
                document.getElementById('coupon-name').textContent = `(${code})`;
                document.getElementById('coupon-row').style.display = 'flex';
                document.getElementById('coupon').value = '';
                updateTotal();
                // Removed: updateButtonTotal(); 
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            showAlert('An error occurred. Please try again.', 'danger');
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
        toggleText.textContent = 'See fewer options';
    } else {
        additionalPayments.classList.remove('show');
        toggle.classList.remove('expanded');
        toggleText.textContent = 'See more payment options';
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
        showAlert('Check your internet connection and try again.', 'danger');
    } else {
        showAlert('Server connection issue. Please try again in a moment.', 'danger');
    }
}
</script>
@endpush