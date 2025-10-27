@extends('layouts.frontend.app')

@push('meta')
    <meta name='description' content="Complete your order" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('title', 'Checkout - Complete Order')

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

    /* Additional styles for unified checkout */
    .checkout-mode-indicator {
        background: linear-gradient(135deg, #e0f2fe 0%, #b3e5fc 100%);
        border: 1px solid #0288d1;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: 600;
        color: #01579b;
    }

    .product-summary-item {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 12px;
        background: #f9fafb;
    }

    .product-summary-item img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 15px;
    }

    .product-summary-info {
        flex: 1;
    }

    .product-summary-title {
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
    }

    .product-summary-attributes {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .product-summary-price {
        font-weight: 600;
        color: #059669;
    }
    </style>
@endpush

@section('content')
    @php
        // Determine checkout mode: 'buy_now' or 'cart'
        $isBuyNow = isset($product) && $product;
        $isCart = !$isBuyNow;
        
        // Get previous user order data for address prefill
        $order = Auth::check() ? App\Models\Order::where('user_id', auth()->id())
            ->select('address', 'shipping_charge', 'town', 'district', 'thana')
            ->first() : null;
    @endphp

    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Complete Your Order</h1>
            <p>Complete your order in just a few steps</p>
        </div>

        @if($isBuyNow)
            <div class="checkout-mode-indicator">
                <i class="fas fa-shopping-bag"></i> Buy Now Checkout - Single Product
            </div>
        @else
            <div class="checkout-mode-indicator">
                <i class="fas fa-shopping-cart"></i> Cart Checkout - Multiple Products
            </div>
        @endif

        <div class="order-interval-warning" id="order-interval-warning">
            <div class="warning-header">
                <div class="warning-icon">⚠️</div>
                <h4 class="warning-title">Order Restriction</h4>
            </div>
            <p class="warning-text" id="warning-message">
                Hold on! You have already placed an order. You can order again after <span class="countdown-timer" id="countdown-timer">--</span>. This is to prevent fake orders. For any changes to your order, knock us on WhatsApp <a href="#" id="whatsapp-link" class="whatsapp-link" target="_blank">
                    <strong id="whatsapp-number">{{ setting('whatsapp') ?? setting('phone') ?? '01XXXXXXXXX' }}</strong>
                </a>.
            </p>
        </div>

        @if($isBuyNow)
            <form action="{{ route('order.buy.store_minimal') }}" method="POST" id="checkout-form">
        @else
            <form action="{{ route('order.store_minimal') }}" method="POST" id="checkout-form">
        @endif
            @csrf
            
            {{-- Hidden fields for different checkout modes --}}
            @if($isBuyNow)
                <input type="hidden" name="id" value="{{ $request->id }}">
                <input type="hidden" name="qty" value="{{ $request->qty ?? 1 }}">
                <input type="hidden" name="color" value="{{ $request->color ?? 'blank' }}">
                <input type="hidden" name="size" value="{{ $request->size ?? '' }}">
                <input type="hidden" name="dynamic_price" value="{{ $request->dynamic_price ?? 0 }}">
                @if($request->camp)
                    <input type="hidden" name="camp" value="{{ $request->camp }}">
                @endif
                {{-- Add all attribute fields --}}
                @foreach($request->all() as $key => $value)
                    @if(str_contains($key, 'attribute_') || is_numeric($key))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
            @else
                @php
                    $cartItems = \Gloudemans\Shoppingcart\Facades\Cart::content();
                    $sub_total = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', '');
                    $seller_count = $cartItems->groupBy('weight')->count();
                @endphp
                <input type="hidden" name="seller_count" value="{{ $seller_count }}">
                <input type="hidden" name="stotal" value="{{ $sub_total }}">
            @endif
            
            <div class="checkout-grid">
                <div class="checkout-forms">
                    {{-- Alert container --}}
                    <div class="alert-container" style="display: none;">
                        <div class="alert" id="alert-message"></div>
                    </div>

                    {{-- Customer Information --}}
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
                                value="{{ Auth::check() ? auth()->user()->name : old('first_name') }}" 
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
                                value="{{ Auth::check() ? auth()->user()->phone : old('phone') }}" 
                                required
                            />
                            @error('phone')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">
                                Email Address
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-input @error('email') error @enderror"
                                value="{{ Auth::check() ? auth()->user()->email : old('email') }}"
                            />
                            @error('email')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Shipping Information --}}
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="section-number">2</div>
                            <h3 class="section-title">Shipping Information</h3>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="address">
                                Address <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="address" 
                                name="address" 
                                class="form-input @error('address') error @enderror"
                                value="{{ $order->address ?? old('address') }}" 
                                required
                            />
                            @error('address')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="city">
                                    City <span class="required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="city" 
                                    name="city" 
                                    class="form-input @error('city') error @enderror"
                                    value="{{ $order->town ?? old('city') }}" 
                                    required
                                />
                                @error('city')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="district">
                                    District <span class="required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="district" 
                                    name="district" 
                                    class="form-input @error('district') error @enderror"
                                    value="{{ $order->district ?? old('district') }}" 
                                    required
                                />
                                @error('district')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="thana">
                                    Thana/Area
                                </label>
                                <input 
                                    type="text" 
                                    id="thana" 
                                    name="thana" 
                                    class="form-input @error('thana') error @enderror"
                                    value="{{ $order->thana ?? old('thana') }}"
                                />
                                @error('thana')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="postcode">
                                    Post Code
                                </label>
                                <input 
                                    type="text" 
                                    id="postcode" 
                                    name="postcode" 
                                    class="form-input @error('postcode') error @enderror"
                                    value="{{ old('postcode') }}"
                                />
                                @error('postcode')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        @if($isBuyNow && $product && !$product->download_able)
                            {{-- Shipping range for buy now --}}
                            <div class="form-group">
                                <label class="form-label">Shipping Area <span class="required">*</span></label>
                                <div class="radio-group">
                                    <div class="radio-item">
                                        <input type="radio" id="inside_dhaka" name="shipping_range" value="1" required>
                                        <label for="inside_dhaka">Inside Dhaka ({{ setting('shipping_charge') ?? '60' }} Taka)</label>
                                    </div>
                                    <div class="radio-item">
                                        <input type="radio" id="outside_dhaka" name="shipping_range" value="2" required>
                                        <label for="outside_dhaka">Outside Dhaka ({{ setting('shipping_charge_out_of_range') ?? '120' }} Taka)</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Payment Method --}}
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="section-number">3</div>
                            <h3 class="section-title">Payment Method</h3>
                        </div>

                        <div class="payment-methods">
                            <div class="payment-option">
                                <input type="radio" name="payment_method" value="COD" id="COD" checked>
                                <label for="COD" class="payment-label">
                                    <img src="{{ asset('/') }}icon/cod.png" alt="Cash on Delivery" class="payment-icon">
                                    <span class="payment-text">Cash on Delivery</span>
                                </label>
                            </div>

                            @if(setting('bkash_api') == 'true')
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="bKash" id="bKash">
                                    <label for="bKash" class="payment-label">
                                        <img src="{{ asset('/') }}icon/bkash.png" alt="bKash" class="payment-icon">
                                        <span class="payment-text">bKash</span>
                                    </label>
                                </div>
                            @endif

                            @if(setting('nagad_api') == 'true')
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="Nagad" id="Nagad">
                                    <label for="Nagad" class="payment-label">
                                        <img src="{{ asset('/') }}icon/nagad.png" alt="Nagad" class="payment-icon">
                                        <span class="payment-text">Nagad</span>
                                    </label>
                                </div>
                            @endif

                            @if(setting('rocket_api') == 'true')
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="Rocket" id="Rocket">
                                    <label for="Rocket" class="payment-label">
                                        <img src="{{ asset('/') }}icon/rocket.png" alt="Rocket" class="payment-icon">
                                        <span class="payment-text">Rocket</span>
                                    </label>
                                </div>
                            @endif

                            @if(setting('upay_api') == 'true')
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="Upay" id="Upay">
                                    <label for="Upay" class="payment-label">
                                        <img src="{{ asset('/') }}icon/upay.png" alt="Upay" class="payment-icon">
                                        <span class="payment-text">Upay</span>
                                    </label>
                                </div>
                            @endif

                            @if(setting('bank_api') == 'true')
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="Bank" id="Bank">
                                    <label for="Bank" class="payment-label">
                                        <img src="{{ asset('/') }}icon/bank.png" alt="Bank Transfer" class="payment-icon">
                                        <span class="payment-text">Bank Transfer</span>
                                    </label>
                                </div>
                            @endif
                        </div>

                        @error('payment_method')
                            <span class="error-message">{{ $message }}</span>
                        @enderror

                        <div id="payment-instructions" class="payment-details">
                            <p>💵 Pay upon receiving the product.</p>
                        </div>
                    </div>
                </div>

                <div class="order-summary">
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="section-number">4</div>
                            <h3 class="section-title">Order Summary</h3>
                        </div>

                        {{-- Product Summary based on checkout mode --}}
                        @if($isBuyNow)
                            {{-- Buy Now: Single Product --}}
                            @php
                                $req_qty = $request->qty ?? 1;
                                $req_dynamic_price = $request->dynamic_price ?? 0;
                                
                                // Calculate pricing
                                $base_price = !empty($product->discount_price) ? $product->discount_price : $product->regular_price;
                                $total_price = $base_price + $req_dynamic_price;
                                $sub_total = $total_price * $req_qty;
                                
                                // Build attributes display
                                $buyNowAttributes = [];
                                if (!empty($request->color) && $request->color != 'blank') {
                                    $color = App\Models\Color::where('slug', $request->color)->first();
                                    $buyNowAttributes['Color'] = $color ? $color->name : $request->color;
                                }
                                if (!empty($request->size) && $request->size != 'blank') {
                                    $buyNowAttributes['Size'] = $request->size;
                                }
                                // Add other attributes
                                foreach($request->all() as $key => $value) {
                                    if(str_contains($key, 'attribute_') && !empty($value) && $value != 'blank') {
                                        $attrName = str_replace('attribute_', '', $key);
                                        $buyNowAttributes[ucfirst($attrName)] = $value;
                                    }
                                }
                            @endphp

                            <div class="product-summary-item">
                                <img src="{{ asset('uploads/product/' . $product->image) }}" alt="{{ $product->title }}">
                                <div class="product-summary-info">
                                    <div class="product-summary-title">{{ $product->title }} (x{{ $req_qty }})</div>
                                    
                                    @if (count($buyNowAttributes) > 0)
                                        <div class="product-summary-attributes">
                                            @foreach ($buyNowAttributes as $attrName => $attrValue)
                                                <strong>{{ $attrName }}:</strong> {{ $attrValue }}@if (!$loop->last), @endif
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <div class="product-summary-price">{{ number_format($sub_total, 2) }} {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}</div>
                                </div>
                            </div>
                        @else
                            {{-- Cart: Multiple Products --}}
                            @foreach ($cartItems as $item)
                                @php
                                    $itemAttributes = [];
                                    $options = $item->options;
                                    
                                    if (!empty($options['color']) && $options['color'] != 'blank') {
                                        $color = App\Models\Color::where('slug', $options['color'])->first();
                                        $itemAttributes['Color'] = $color ? $color->name : $options['color'];
                                    }
                                    
                                    if (!empty($options['attributes'])) {
                                        foreach ($options['attributes'] as $attrSlug => $attrValueId) {
                                            $attributeValue = App\Models\AttributeValue::find($attrValueId);
                                            if ($attributeValue) {
                                                $attribute = $attributeValue->attribute;
                                                $itemAttributes[$attribute->name] = $attributeValue->name;
                                            }
                                        }
                                    }
                                @endphp

                                <div class="product-summary-item">
                                    <img src="{{ asset('uploads/product/' . $options['image']) }}" alt="{{ $item->name }}">
                                    <div class="product-summary-info">
                                        <div class="product-summary-title">{{ $item->name }} (x{{ $item->qty }})</div>
                                        
                                        @if (count($itemAttributes) > 0)
                                            <div class="product-summary-attributes">
                                                @foreach ($itemAttributes as $attrName => $attrValue)
                                                    <strong>{{ $attrName }}:</strong> {{ $attrValue }}@if (!$loop->last), @endif
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <div class="product-summary-price">{{ number_format($item->subtotal, 2) }} {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        {{-- Order Totals --}}
                        <div class="order-totals">
                            <div class="total-row">
                                <span class="total-label">Subtotal:</span>
                                <span class="total-value" id="sub-total">
                                    @if($isBuyNow)
                                        {{ number_format($sub_total, 2) }}
                                    @else
                                        {{ number_format($sub_total, 2) }}
                                    @endif
                                    {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}
                                </span>
                            </div>

                            <div class="total-row">
                                <span class="total-label">Shipping:</span>
                                <span class="total-value" id="ship-charge">
                                    @if($isBuyNow && $product && !$product->download_able)
                                        0.00 {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}
                                    @else
                                        {{ setting('shipping_charge') ?? '60' }}.00 {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}
                                    @endif
                                </span>
                            </div>

                            <div class="total-row coupon-row" id="coupon-row" style="display: none;">
                                <span class="total-label">Coupon Discount <span id="coupon-name"></span>:</span>
                                <span class="total-value" id="coupon-discount">0.00</span>
                            </div>

                            <div class="total-row total-final">
                                <span class="total-label">Total:</span>
                                <span class="total-value" id="total">
                                    @if($isBuyNow)
                                        {{ number_format($sub_total, 2) }}
                                    @else
                                        {{ number_format($sub_total + (setting('shipping_charge') ?? 60), 2) }}
                                    @endif
                                    {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}
                                </span>
                            </div>
                        </div>

                        {{-- Coupon Section --}}
                        <div class="coupon-section">
                            <div class="coupon-toggle" onclick="toggleCoupon()">
                                <span>Have a coupon code?</span>
                                <span class="coupon-arrow" id="coupon-arrow">+</span>
                            </div>
                            <div class="coupon-form" id="coupon-form" style="display: none;">
                                <div class="coupon-input-group">
                                    <input type="text" id="coupon" placeholder="Enter coupon code" class="coupon-input">
                                    <button type="button" onclick="applyCoupon()" class="btn-apply">Apply</button>
                                </div>
                            </div>
                        </div>

                        {{-- Place Order Button --}}
                        <button type="submit" class="btn-place-order" id="place-order-btn">
                            <i class="fas fa-lock"></i>
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('js')
<script>
// Order interval checking and form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkout-form');
    const warning = document.getElementById('order-interval-warning');
    const submitBtn = document.getElementById('place-order-btn');
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');
    
    let isCheckingInterval = false;
    let orderRestricted = false;
    
    // Hide warning initially
    warning.style.display = 'none';
    
    // Check order interval when phone changes
    phoneInput.addEventListener('input', debounce(checkOrderInterval, 500));
    
    @if($isBuyNow)
        // For buy now, also check shipping range changes
        document.addEventListener('change', function(e) {
            if (e.target.name === 'shipping_range') {
                updateShippingCharge();
            }
        });
    @endif

    // Form submission
    form.addEventListener('submit', function(e) {
        if (orderRestricted) {
            e.preventDefault();
            showAlert('Please wait until you can place your next order.', 'danger');
            return false;
        }
        
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    });

    function checkOrderInterval() {
        const phone = phoneInput.value.trim();
        const email = emailInput.value.trim() || '';
        
        if (!phone || phone.length < 10) {
            hideWarning();
            return;
        }
        
        if (isCheckingInterval) return;
        isCheckingInterval = true;
        
        const requestData = {
            phone: phone,
            email: email,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
        
        @if($isBuyNow)
            requestData.product_id = {{ $product->id }};
        @endif
        
        fetch('{{ route("check.order.interval") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.restricted) {
                showWarning(data);
                orderRestricted = true;
                submitBtn.disabled = true;
            } else {
                hideWarning();
                orderRestricted = false;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error checking order interval:', error);
            hideWarning();
            orderRestricted = false;
            submitBtn.disabled = false;
        })
        .finally(() => {
            isCheckingInterval = false;
        });
    }

    function showWarning(data) {
        const warningElement = document.getElementById('order-interval-warning');
        const messageElement = document.getElementById('warning-message');
        const whatsappLink = document.getElementById('whatsapp-link');
        const whatsappNumber = document.getElementById('whatsapp-number');
        const countdownTimer = document.getElementById('countdown-timer');
        
        // Update warning styling based on restriction type
        warningElement.className = 'order-interval-warning product-restriction show';
        if (data.restriction_reason) {
            warningElement.setAttribute('data-reason', data.restriction_reason);
        }
        
        // Update message
        messageElement.innerHTML = data.message || 'Order restricted. Please try again later.';
        
        // Update WhatsApp link
        if (data.whatsapp_number) {
            whatsappNumber.textContent = data.whatsapp_number;
            whatsappLink.href = `https://wa.me/${data.whatsapp_number.replace(/[^0-9]/g, '')}`;
        }
        
        // Show countdown if available
        if (data.remaining_seconds) {
            startCountdown(data.remaining_seconds, countdownTimer);
        } else if (data.remaining_days) {
            countdownTimer.textContent = `${data.remaining_days} days`;
        }
        
        warningElement.style.display = 'block';
    }

    function hideWarning() {
        const warningElement = document.getElementById('order-interval-warning');
        warningElement.style.display = 'none';
        warningElement.classList.remove('show');
    }

    function startCountdown(seconds, element) {
        function updateCountdown() {
            if (seconds <= 0) {
                element.textContent = 'Now available';
                orderRestricted = false;
                submitBtn.disabled = false;
                hideWarning();
                return;
            }
            
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            let timeString = '';
            if (hours > 0) timeString += `${hours}h `;
            if (minutes > 0) timeString += `${minutes}m `;
            timeString += `${secs}s`;
            
            element.textContent = timeString;
            seconds--;
        }
        
        updateCountdown();
        const interval = setInterval(updateCountdown, 1000);
        
        // Clear interval when countdown reaches 0
        setTimeout(() => clearInterval(interval), seconds * 1000);
    }

    function validateForm() {
        const requiredFields = ['first_name', 'phone', 'address', 'city', 'district'];
        let isValid = true;
        
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!field.value.trim()) {
                field.classList.add('error');
                isValid = false;
            } else {
                field.classList.remove('error');
            }
        });
        
        // Validate phone number
        const phone = phoneInput.value.trim();
        if (phone && !validatePhoneNumber(phone)) {
            phoneInput.classList.add('error');
            showAlert('Please enter a valid phone number.', 'danger');
            isValid = false;
        }
        
        // Validate payment method
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            showAlert('Please select a payment method.', 'danger');
            isValid = false;
        }
        
        @if($isBuyNow && $product && !$product->download_able)
            // Validate shipping range for buy now
            const shippingRange = document.querySelector('input[name="shipping_range"]:checked');
            if (!shippingRange) {
                showAlert('Please select a shipping area.', 'danger');
                isValid = false;
            }
        @endif
        
        return isValid;
    }

    @if($isBuyNow && $product && !$product->download_able)
        function updateShippingCharge() {
            const shippingRange = document.querySelector('input[name="shipping_range"]:checked');
            if (!shippingRange) return;
            
            let shippingCharge = 0;
            if (shippingRange.value == 1) {
                shippingCharge = parseInt("{{ setting('shipping_charge') ?? 60 }}");
            } else {
                shippingCharge = parseInt("{{ setting('shipping_charge_out_of_range') ?? 120 }}");
            }
            
            document.getElementById('ship-charge').textContent = 
                formatNumber(shippingCharge, 2) + ' {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}';
            
            updateTotal();
        }
    @endif

    function updateTotal() {
        const subtotal = parseFloat(document.getElementById('sub-total').textContent.replace(/[^0-9.-]+/g, ''));
        const shipping = parseFloat(document.getElementById('ship-charge').textContent.replace(/[^0-9.-]+/g, ''));
        const coupon = parseFloat(document.getElementById('coupon-discount').textContent.replace(/[^0-9.-]+/g, '')) || 0;
        
        const total = subtotal + shipping - coupon;
        const formattedTotal = `${formatNumber(total, 2)} {{ setting('CURRENCY_CODE_MIN') ?? 'Taka' }}`;
        
        document.getElementById('total').innerHTML = formattedTotal;
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function validatePhoneNumber(phone) {
        const cleanPhone = phone.replace(/[^0-9]/g, '');
        return cleanPhone.length >= 11 && (cleanPhone.startsWith('01') || cleanPhone.startsWith('8801'));
    }

    function formatNumber(number, decimals) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(number);
    }
});

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
    
    if (!code) {
        showAlert('Please enter a coupon code', 'danger');
        return;
    }
    
    const applyBtn = document.querySelector('.btn-apply');
    const originalText = applyBtn.textContent;
    applyBtn.disabled = true;
    applyBtn.innerHTML = '<span class="spinner"></span>';
    
    @if($isBuyNow)
        const id = "{{ $request->id }}";
        const qty = "{{ $request->qty ?? 1 }}";
        const dynamicPrice = "{{ $request->dynamic_price ?? 0 }}";
        const url = `/apply/coupon/buy-now/${code}/${id}/${qty}/${dynamicPrice}`;
    @else
        const subtotal = document.getElementById('sub-total').textContent.replace(/[^0-9.-]+/g, '');
        const url = `/apply/coupon/${code}/${subtotal}`;
    @endif
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.alert === 'success') {
                showAlert(data.message, 'success');
                document.getElementById('coupon-discount').textContent = formatNumber(data.discount, 2);
                document.getElementById('coupon-name').textContent = `(${code})`;
                document.getElementById('coupon-row').style.display = 'flex';
                document.getElementById('coupon').value = '';
                updateTotal();
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
    
    setTimeout(() => {
        alertContainer.style.display = 'none';
    }, 5000);
    
    alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function formatNumber(number, decimals) {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
}
</script>
@endpush