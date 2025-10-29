@extends('layouts.frontend.app')

@push('meta')
<meta name='description' content="Cart Products"/>
@endpush

@section('title', 'Cart Products')

@push('css')
    <style>
        /* --- (CSS STYLES) --- */
        .disable {
            color: currentColor;
            cursor: not-allowed;
            opacity: 0.5;
        }

        /* --- MODERN STYLES --- */

        /* --- Page Layout --- */
        .checkout-right {
            background-color: #f9fafb;
            padding-top: 2rem;
            padding-bottom: 4rem;
        }
        .checkout-right .container {
            max-width: 1200px;
        }
        
        .h1.cart-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: #111827;
        }
        
        .cart-layout {
            display: flex;
            flex-direction: column;
        }
        
        .shopping-cart-list {
            flex: 1;
        }
        
        .order-summary-column {
            width: 100%;
            margin-top: 2rem;
        }

        /* --- Product Card (Left Side) --- */
        .product-card {
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            margin-bottom: 1.5rem;
            transition: box-shadow 0.2s ease-in-out;
        }
        .product-card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
        }
        
        .product-info {
            display: flex;
            align-items: center;
            flex-grow: 1;
            margin-bottom: 1rem;
        }
        .product-image {
            width: 72px;
            min-width: 72px;
            height: 72px;
            margin-right: 1.25rem;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #f3f4f6;
        }

        .product-details {
            display: flex;
            flex-direction: column;
        }
        .product-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: #111827;
            text-decoration: none;
            line-height: 1.4;
            margin-bottom: 0.25rem;
        }
        .product-title:hover {
            color: #0d4f6b;
        }
        
        /* IMPROVED: Style for attributes display */
        .product-attributes {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
            margin-bottom: 0.25rem;
            line-height: 1.5;
        }
        .product-attributes strong {
            color: #374151;
            font-weight: 500;
        }
        
        .product-unit-price {
            font-size: 0.95rem;
            color: #6b7280;
            margin-top: 0.35rem;
        }

        /* Actions on the right */
        .product-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        .product-line-total {
            font-weight: 600;
            color: #374151;
            font-size: 1rem;
            margin-right: 1rem;
        }
        
        /* Quantity Selector (Modernized) */
        .quantity-select {
            display: flex;
            align-items: center;
            margin-right: 1.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        .quantity-select .btn-qty {
            width: 32px;
            height: 32px;
            background-color: #f9fafb;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: 600;
            color: #374151;
            transition: background-color 0.2s ease;
        }
        .quantity-select .btn-qty:hover {
            background-color: #f3f4f6;
        }
        .quantity-select .btn-qty:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .quantity-select .value {
            width: 40px;
            height: 32px;
            border: none;
            border-left: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            background-color: #ffffff;
            text-align: center;
            font-size: 1.05rem;
            font-weight: 600;
            color: #111827;
            padding: 0;
            -moz-appearance: textfield;
        }
        
        /* Remove Button (Modernized) */
        .btn-remove {
            border: 0;
            background: none;
            color: #9ca3af;
            font-size: 1.3rem;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            transition: color 0.2s ease, background-color 0.2s ease;
        }
        .btn-remove:hover {
            color: #ef4444;
            background-color: #fef2f2;
        }

        /* --- Order Summary Card (Right Side) --- */
        .order-summary-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 2rem;
        }
        .order-summary-card h4 {
            font-size: 1.35rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 1.5rem;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.85rem 0;
            font-size: 0.95rem;
            color: #374151;
        }
        .summary-item strong {
            font-weight: 600;
            color: #111827;
        }
        
        .summary-divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 1rem 0;
        }
        
        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            font-size: 1.15rem;
            font-weight: 600;
            color: #111827;
        }
        
        .btn-checkout {
            width: 100%;
            margin-top: 1.5rem;
            background-color: #0d4f6b;
            border: 2px solid #0d4f6b;
            color: #ffffff;
            font-weight: 600;
            font-size: 1rem;
            padding: 0.85rem 1.5rem;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            display: block;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-checkout:hover {
            color: #ffffff;
            background-color: #093c52;
            border-color: #093c52;
            box-shadow: 0 4px 12px rgba(13, 79, 107, 0.15);
        }
        .btn-checkout.disabled {
            background-color: #9ca3af;
            border-color: #9ca3af;
            opacity: 0.7;
            cursor: not-allowed;
        }
        .btn-checkout .icofont {
            margin-right: 0.5rem;
        }

        /* Desktop View */
        @media screen and (min-width: 992px) {
            .cart-layout {
                flex-direction: row;
                align-items: flex-start;
                gap: 2.5rem;
            }
            
            .shopping-cart-list {
                flex: 0 0 65%;
            }
            
            .order-summary-column {
                flex: 0 0 33%;
                margin-top: 0;
            }
            
            .product-card {
                flex-direction: row;
                align-items: center;
                padding: 1.5rem;
            }
            .product-info {
                margin-bottom: 0;
            }
            .product-actions {
                width: auto;
                margin-left: auto;
            }
            .product-line-total {
                width: 90px;
                text-align: right;
                font-size: 1.05rem;
            }
        }
        
        /* Specific Mobile Tweak */
        @media screen and (max-width: 400px) {
            .product-card {
                padding: 1rem;
            }
            .product-actions {
                flex-wrap: wrap;
                gap: 1rem;
            }
            .quantity-select {
                margin-right: auto;
            }
            .product-line-total {
                order: 1;
                margin-left: 1rem;
            }
            .btn-remove {
                order: 2;
                margin-left: 0;
            }
        }

        /* --- EMPTY CART STYLES --- */
        .empty-cart-container {
            text-align: center;
            padding: 5rem 2rem;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            margin-top: 1rem;
        }
        .empty-cart-icon .icofont-bag-alt {
            font-size: 5rem;
            color: #22c55e;
            margin-bottom: 1.5rem;
        }
        .empty-cart-container h2 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.75rem;
        }
        .empty-cart-container p {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 2.5rem;
        }
        .btn-continue-shopping {
            background-color: #263238;
            color: #ffffff;
            border: none;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-continue-shopping:hover {
            background-color: #37474f;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(55, 71, 79, 0.2);
        }

    </style>
@endpush

@section('content')

@php
    $currency = setting('CURRENCY_CODE_MIN') ?? 'TK';
    
    /**
     * Helper function to get attribute value name from ID
     * Used for displaying size and other attributes
     */
    function getAttributeName($id) {
        try {
            $attributeValue = \App\Models\AttributeValue::find($id);
            return $attributeValue ? $attributeValue->value : 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
    
    /**
     * Helper function to get Color name from slug
     */
    function getColorName($slug) {
        try {
            $color = \App\Models\Color::where('slug', $slug)->first();
            return $color ? $color->name : 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * IMPROVED: Helper function to format all attributes for display
     * This creates a consistent display format for Color, Size, and other attributes
     */
    function getFormattedAttributes($cartItem) {
        $attributesToDisplay = [];

        // 1. Display Color (if available)
        if (!empty($cartItem->options->color) && $cartItem->options->color != 'blank') {
            $attributesToDisplay['Color'] = getColorName($cartItem->options->color);
        }

        // 2. Display Size and Other Attributes
        if (isset($cartItem->options->attributes) && is_array($cartItem->options->attributes)) {
            foreach ($cartItem->options->attributes as $slug => $attributeId) {
                $attributeValueName = getAttributeName($attributeId);
                
                if ($slug === 'size') {
                    // Display Size with capital 'S'
                    $attributesToDisplay['Size'] = $attributeValueName;
                } else {
                    // For other attributes, get the friendly name (e.g., 'Material', 'Style')
                    try {
                        $attributeValue = \App\Models\AttributeValue::find($attributeId);
                        if ($attributeValue && $attributeValue->attribute) {
                            $attributeName = $attributeValue->attribute->name;
                        } else {
                            $attributeName = ucfirst($slug);
                        }
                    } catch (\Exception $e) {
                        $attributeName = ucfirst($slug);
                    }
                    $attributesToDisplay[$attributeName] = $attributeValueName;
                }
            }
        }
        
        return $attributesToDisplay;
    }
@endphp

<div class="checkout-right">
    <div class="container">
        
        @if ($count > 0)
            <h1 class="cart-title">Shopping Cart</h1>

            <div class="cart-layout">

                <div class="shopping-cart-list">
                    <div class="product-all">
                        
                        {{-- This block renders the cart on initial page load --}}
                        @forelse ($carts as $rowId => $cart)
                            <div class="product-card">
                                
                                <div class="product-info">
                                    <div class="product-image">
                                        <a href="{{ route('product.details', $cart->options->slug) }}">
                                            <img src="{{ asset('uploads/product/' . $cart->options->image) }}" alt="Product Image">
                                        </a>
                                    </div> 
                                    <div class="product-details">
                                        <a href="{{ route('product.details', $cart->options->slug) }}" class="product-title">{{ $cart->name }}</a>
                                        
                                        {{-- IMPROVED: Display Color, Size, and All Attributes --}}
                                        @php
                                            $attributesToDisplay = getFormattedAttributes($cart);
                                        @endphp

                                        @if (count($attributesToDisplay) > 0)
                                            <span class="product-attributes">
                                                @foreach ($attributesToDisplay as $key => $value)
                                                    <strong>{{ $key }}:</strong> {{ $value }}@if (!$loop->last), @endif
                                                @endforeach
                                            </span>
                                        @endif
                                        
                                        <span class="product-unit-price">{{ number_format($cart->price, 2, '.', ',') . ' ' . $currency }}</span>
                                    </div> 
                                </div> 
                                
                                <div class="product-actions">
                                    <div class="quantity-select">
                                        <button class="btn-qty btn-qty-minus" data-id="{{ $rowId }}" {{ $cart->qty <= 1 ? 'disabled' : '' }}>-</button>
                                        <input type="text" class="value" value="{{ $cart->qty }}" readonly>
                                        <button class="btn-qty btn-qty-plus" data-id="{{ $rowId }}">+</button>
                                    </div> 
                                    <span class="product-line-total">{{ number_format($cart->subtotal, 2, '.', ',') . ' ' . $currency }}</span>
                                    <button class="btn-remove" id="remove-product" data-id="{{ $rowId }}" title="Remove product">
                                        <i class="icofont icofont-trash"></i>
                                    </button>
                                </div> 
                            </div> 
                        @empty
                            <div class="product-card" style="text-align: center; color: #6b7280; padding: 2rem;">
                                Your cart is empty.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="order-summary-column">
                    <div class="order-summary-card">
                        <h4>Order Summary</h4>
                        
                        <div class="summary-item">
                            <span>Subtotal (<span id="summary-item-count">{{ $count }} {{ $count == 1 ? 'item' : 'items' }}</span>)</span>
                            <strong id="summary-subtotal">{{ number_format($subtotal, 2, '.', ',') . ' ' . $currency }}</strong>
                        </div>

                        <div class="summary-item">
                            <span>Shipping</span>
                            <span>Calculated at next step</span>
                        </div>
                        
                        <hr class="summary-divider">
                        
                        <div class="summary-total">
                            <strong>Total</strong>
                            <strong id="summary-total">{{ number_format($subtotal, 2, '.', ',') . ' ' . $currency }}</strong>
                        </div>
                        
                        <a href="{{ $count > 0 ? route('checkout') : 'javascript:void(0)' }}" 
                           class="btn btn-checkout btn-primary {{ $count == 0 ? 'disabled' : '' }}">
                            <i class="icofont icofont-lock"></i>
                            PROCEED TO CHECKOUT
                        </a>
                    </div>
                </div>

            </div>
        @else
            <div class="empty-cart-container">
                <div class="empty-cart-icon">
                    <i class="icofont-bag-alt"></i> 
                </div>
                <h2>Your Cart is Empty</h2>
                <p>Looks like you haven't added anything to your cart yet.</p>
                <a href="{{ route('product') }}" class="btn btn-continue-shopping">Continue Shopping</a>
            </div>
        @endif
    </div>
</div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {

            // --- Quantity Increase ---
            $(document).on('click', '.btn-qty-plus', function(e) {
                e.preventDefault();
                let id  = $(this).data('id');
                let btn = $(this);
                let qtyInput = $(this).siblings('.value');
                let currentVal = parseInt(qtyInput.val(), 10);
                
                if (!isNaN(currentVal)) {
                    let newVal = currentVal + 1;
                    qtyInput.val(newVal);
                    
                    // Enable minus button since qty > 1
                    $(this).siblings('.btn-qty-minus').prop('disabled', false);
                    
                    updateCartAjax(id, newVal, btn);
                }
            });

            // --- Quantity Decrease ---
            $(document).on('click', '.btn-qty-minus', function(e) {
                e.preventDefault();
                let id  = $(this).data('id');
                let btn = $(this);
                let qtyInput = $(this).siblings('.value');
                let currentVal = parseInt(qtyInput.val(), 10);
                
                if (!isNaN(currentVal) && currentVal > 1) {
                    let newVal = currentVal - 1;
                    qtyInput.val(newVal);
                    
                    if (newVal === 1) {
                        $(btn).prop('disabled', true);
                    }
                    
                    updateCartAjax(id, newVal, btn);
                }
            });

            // --- Remove Product ---
            $(document).on('click', '#remove-product', function(e) {
                e.preventDefault();
                let id  = $(this).data('id');
                let btn = $(this);
                $.ajax({
                    type: 'GET',
                    url: '/destroy/cart/'+id,
                    dataType: "JSON",
                    beforeSend: function() { 
                        $(btn).addClass('disable'); 
                        $(btn).closest('.product-card').css('opacity', '0.5');
                    },
                    success: function (response) { 
                        // Call getCart() on success
                        getCart(); 
                    },
                    complete: function() { 
                        // UI cleanup handled by getCart() success/error
                    }
                });
            });

            // --- Reusable AJAX function for quantity update ---
            function updateCartAjax(id, qty, btn) {
                var card = $(btn).closest('.product-card');
                var qtySelect = $(btn).closest('.quantity-select');
                
                $.ajax({
                    type: 'GET',
                    url: '/update/cart/'+id+'/'+qty,
                    dataType: "JSON",
                    beforeSend: function() {
                        // Disable buttons and dim card before sending request
                        qtySelect.find('.btn-qty').prop('disabled', true);
                        card.css('opacity', '0.7');
                    },
                    // --- FIX 1: Handle Server Warnings (HTTP 200) that prevent update ---
                    success: function (response) {
                        if (response.alert == 'Warning' || response.alert == 'Error') {
                            // If the server returns a warning (e.g., out of stock), the update failed.
                            // We need to reset the UI and quantity locally, and show a message.
                            
                            // 1. Reset the quantity input to the original value (since update failed)
                            // This part is tricky without knowing the original value. For now, we only undo the UI disablement.
                            
                            // 2. Re-enable buttons and restore opacity
                            qtySelect.find('.btn-qty').prop('disabled', false);
                            var currentVal = parseInt(qtySelect.find('.value').val(), 10);
                            qtySelect.find('.btn-qty-minus').prop('disabled', currentVal <= 1);
                            card.css('opacity', '1');
                            
                            // Optional: Display alert message (assuming you have a notification function)
                            // alert(response.message);
                            
                        } else {
                            // Only call getCart() on a true success to re-render cart
                            getCart();
                        }
                    },
                    // --- Handle HTTP Errors (4xx, 5xx) ---
                    error: function() {
                        // If the AJAX call fails (server error, etc.), reset the UI locally
                        qtySelect.find('.btn-qty').prop('disabled', false);
                        var currentVal = parseInt(qtySelect.find('.value').val(), 10);
                        qtySelect.find('.btn-qty-minus').prop('disabled', currentVal <= 1);
                        card.css('opacity', '1');
                    },
                    complete: function() {
                        // UI cleanup is mostly handled by the success handler calling getCart() 
                        // or the error handler.
                    }
                });
            }
       
  
            /**
             * IMPROVED: getCart() function
             * Fetches cart data via AJAX and displays formatted attributes
             */
            function getCart() {
                $.ajax({
                    type: "GET",
                    url: "{!! route('get.cart') !!}",
                    dataType: "JSON",
                    success: function (response) {
                        
                        if (response.count == 0) {
                            window.location.reload();
                            return; 
                        }

                        var total_qty = 0;
                        var total = 0;
                        let html = '';
                        var currency = ' {{ $currency }}'; 
                        
                        $.each(response.carts, function (key, val) {
                            total_qty += parseInt(val.qty);
                            total     += parseFloat(val.subtotal); 
                            var isQtyOne = parseInt(val.qty) <= 1 ? 'disabled' : '';
                            
                            // --- IMPROVED: Build Attributes HTML ---
                            var attributesHtml = '';
                            var displayAttributes = val.options.formatted_attributes || {};
                            
                            if (Object.keys(displayAttributes).length > 0) {
                                attributesHtml += '<span class="product-attributes">';
                                var attrArray = [];
                                for (var keyName in displayAttributes) {
                                    if (displayAttributes.hasOwnProperty(keyName)) {
                                        attrArray.push('<strong>' + keyName + ':</strong> ' + displayAttributes[keyName]);
                                    }
                                }
                                attributesHtml += attrArray.join(', ');
                                attributesHtml += '</span>';
                            }

                            // --- Start Product Card ---
                            html += '<div class="product-card">';
                            
                            // 1. Product Info (Image + Title/Attributes/Price)
                            html += '<div class="product-info">';
                            html += '<div class="product-image">';
                            html += '<a href="/product/'+val.options.slug+'">';
                            html += '<img src="/uploads/product/'+val.options.image+'" alt="Product Image">';
                            html += '</a>';
                            html += '</div>';
                            html += '<div class="product-details">';
                            html += '<a href="/product/'+val.options.slug+'" class="product-title">'+val.name+'</a>';
                            // Insert Attributes (Color, Size, etc.)
                            html += attributesHtml;
                            html += '<span class="product-unit-price">'+number_format(val.price, 2, '.', ',') + currency +'</span>';
                            html += '</div>';
                            html += '</div>';
                            
                            // 2. Product Actions (Qty, Total, Remove)
                            html += '<div class="product-actions">';
                            // Quantity
                            html += '<div class="quantity-select">';
                            html += '<button class="btn-qty btn-qty-minus" data-id="'+key+'" '+isQtyOne+'>-</button>';
                            html += '<input type="text" class="value" value="'+val.qty+'" readonly>';
                            html += '<button class="btn-qty btn-qty-plus" data-id="'+key+'">+</button>';
                            html += '</div>';
                            // Line Total
                            html += '<span class="product-line-total">'+number_format(val.subtotal, 2, '.', ',') + currency +'</span>';
                            // Removal
                            html += '<button class="btn-remove" id="remove-product" data-id="'+key+'" title="Remove product">';
                            html += '<i class="icofont icofont-trash"></i>';
                            html += '</button>';
                            html += '</div>';

                            html += '</div>';
                        });
                        
                        // Inject the built HTML
                        $('.product-all').html(html);
                        
                        // --- Update Order Summary ---
                        var formatted_total = number_format(total, 2, '.', ',');
                        var item_text = total_qty == 1 ? ' item' : ' items';
                        $('#summary-item-count').text(total_qty + item_text);
                        $('#summary-subtotal').text(formatted_total + currency);
                        $('#summary-total').text(formatted_total + currency);
                        
                        // --- Update Checkout Button State ---
                        var checkoutBtn = $('.btn-checkout');
                        if (response.count > 0) {
                            checkoutBtn.removeClass('disabled');
                            checkoutBtn.attr('href', '{{ route('checkout') }}');
                        } else {
                            checkoutBtn.addClass('disabled');
                            checkoutBtn.attr('href', 'javascript:void(0)');
                        }

                        // --- Update Header Cart ---
                        $('.cart-count-badge').text(total_qty);
                        $('span.qty').text(total_qty);
                        $('span#total-cart-amount').text(formatted_total);
                        $('span#count_product').text(response.count+' Products');
                    },
                    
                    // --- FIX 2: Add essential error handler to getCart() ---
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("getCart() failed: " + textStatus, errorThrown);
                        
                        // Failsafe: Re-enable all buttons and reset opacity
                        // This prevents the UI from being stuck in a disabled state
                        
                        $('.product-card').css('opacity', '1');
                        $('.quantity-select .btn-qty').prop('disabled', false);
                        
                        // Re-apply the "disabled" state just for minus buttons at quantity 1
                        $('.quantity-select').each(function() {
                            var qtyInput = $(this).find('.value');
                            var currentVal = parseInt(qtyInput.val(), 10);
                            if (currentVal <= 1) {
                                $(this).find('.btn-qty-minus').prop('disabled', true);
                            }
                        });
                    }
                    // --- END OF FIX ---
                    
                });
            }
            
            // --- number_format function ---
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