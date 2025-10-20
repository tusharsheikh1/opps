@extends('layouts.frontend.app')

@push('meta')
<meta name='description' content="Cart Products"/>
{{-- <meta name'keywords' content="@foreach(\App\Models\Tag::all() as $tag){{$tag->name.', '}}@endforeach" /> --}}
@endpush

@section('title', 'Cart Products')

@push('css')
    <style>
        .disable {
            color: currentColor;
            cursor: not-allowed;
            opacity: 0.5;
        }

        /* --- MODERN STYLES --- */

        /* --- Page Layout --- */
        .checkout-right {
            background-color: #f9fafb; /* Lighter, cleaner background */
            padding-top: 2rem;
            padding-bottom: 4rem;
        }
        .checkout-right .container {
            max-width: 1200px;
        }
        
        .h1.cart-title {
            font-size: 2rem; /* Larger title */
            font-weight: 600;
            margin-bottom: 2rem;
            color: #111827; /* Darker text */
        }
        
        .cart-layout {
            display: flex;
            flex-direction: column; /* Mobile-first */
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
            flex-direction: column; /* Mobile */
            background: #ffffff;
            border-radius: 12px; /* Softer corners */
            padding: 1.5rem; /* More whitespace */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04); /* Softer shadow */
            margin-bottom: 1.5rem;
            transition: box-shadow 0.2s ease-in-out;
        }
        .product-card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06); /* Subtle hover */
        }
        
        .product-info {
            display: flex;
            align-items: center;
            flex-grow: 1;
            margin-bottom: 1rem; /* Mobile */
        }
        .product-image {
            width: 72px; /* Slightly larger image */
            min-width: 72px;
            height: 72px;
            margin-right: 1.25rem;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px; /* Softer corners */
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
        }
        .product-unit-price {
            font-size: 0.95rem;
            color: #6b7280; /* Softer gray */
            margin-top: 0.25rem;
        }

        /* Actions on the right */
        .product-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%; /* Mobile */
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
            border-radius: 8px; /* Rounded container */
            overflow: hidden; /* Clip button corners */
        }
        .quantity-select .btn-qty {
            width: 32px;
            height: 32px;
            background-color: #f9fafb; /* Light bg */
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
            border-left: 1px solid #e5e7eb; /* Divider */
            border-right: 1px solid #e5e7eb; /* Divider */
            background-color: #ffffff; /* White input bg */
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
            color: #9ca3af; /* Lighter default color */
            font-size: 1.3rem;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%; /* Circular */
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto; /* Push to far right */
            transition: color 0.2s ease, background-color 0.2s ease;
        }
        .btn-remove:hover {
            color: #ef4444; /* Red on hover */
            background-color: #fef2f2; /* Light red bg */
        }

        /* --- Order Summary Card (Right Side) --- */
        .order-summary-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem 2rem; /* More padding */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 20px;
        }
        .order-summary-card h4 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #111827;
        }
        .summary-item, .summary-total {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-size: 1rem; /* Slightly larger base text */
        }
        .summary-item span:first-child {
            color: #4b5563;
        }
        .summary-item span:last-child {
            color: #111827;
            font-weight: 500;
        }
        .summary-divider {
            margin: 1.5rem 0;
            border-color: #f3f4f6; /* Lighter divider */
        }
        .summary-total {
            font-size: 1.15rem;
            font-weight: 600;
            color: #111827;
        }
        .btn-checkout {
            width: 100%;
            padding: 0.85rem; /* Taller button */
            font-size: 1rem;
            font-weight: 600;
            background-color: #0d4f6b;
            border-color: #0d4f6b;
            margin-top: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 8px; /* Softer corners */
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-checkout:hover:not(.disabled) {
            background-color: #093c52;
            border-color: #093c52;
            box-shadow: 0 4px 12px rgba(13, 79, 107, 0.15); /* Add a glow */
        }
        .btn-checkout.disabled {
            background-color: #9ca3af;
            border-color: #9ca3af;
            opacity: 0.7;
        }
        .btn-checkout .icofont {
            margin-right: 0.5rem;
        }

        /* Desktop View */
        @media screen and (min-width: 992px) {
            .cart-layout {
                flex-direction: row; /* Two columns */
                align-items: flex-start;
                gap: 2.5rem; /* More space between columns */
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
                flex-wrap: wrap; /* Allow wrapping on tiny screens */
                gap: 1rem;
            }
            .quantity-select {
                margin-right: auto; /* Push to left */
            }
            .product-line-total {
                order: 1; /* Move total before remove button */
                margin-left: 1rem;
            }
            .btn-remove {
                order: 2;
                margin-left: 0;
            }
        }

        /* --- NEW: EMPTY CART STYLES --- */
        .empty-cart-container {
            text-align: center;
            padding: 5rem 2rem;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            margin-top: 1rem;
        }
        .empty-cart-icon .icofont-bag-alt {
            font-size: 5rem; /* Large icon */
            color: #22c55e; /* Green color from image */
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
            background-color: #263238; /* Dark color from image */
            color: #ffffff;
            border: none;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px; /* Fully rounded */
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
@endphp

<div class="checkout-right">
    <div class="container">
        
        {{-- --- START: CONDITIONAL RENDER --- --}}
        @if ($count > 0)
            {{-- --- START: CART NOT EMPTY --- --}}

            <h1 class="cart-title">Shopping Cart</h1>

            <div class="cart-layout">

                <div class="shopping-cart-list">
                    <div class="product-all">
                        
                        {{-- This block renders the cart on page load for speed --}}
                        @forelse ($carts as $rowId => $cart)
                            <div class="product-card">
                                
                                <div class="product-info">
                                    <div class="product-image">
                                        <a href="{{ route('product.details', $cart->options->slug) }}">
                                            {{-- --- THIS IS THE CORRECTED LINE --- --}}
                                            <img src="{{ asset('uploads/product/' . $cart->options->image) }}" alt="Product Image">
                                        </a>
                                    </div> <div class="product-details">
                                        <a href="{{ route('product.details', $cart->options->slug) }}" class="product-title">{{ $cart->name }}</a>
                                        <span class="product-unit-price">{{ number_format($cart->price, 2, '.', ',') . ' ' . $currency }}</span>
                                    </div> </div> <div class="product-actions">
                                    <div class="quantity-select">
                                        <button class="btn-qty btn-qty-minus" data-id="{{ $rowId }}" {{ $cart->qty <= 1 ? 'disabled' : '' }}>-</button>
                                        <input type="text" class="value" value="{{ $cart->qty }}" readonly>
                                        <button class="btn-qty btn-qty-plus" data-id="{{ $rowId }}">+</button>
                                    </div> <span class="product-line-total">{{ number_format($cart->subtotal, 2, '.', ',') . ' ' . $currency }}</span>
                                    <button class="btn-remove" id="remove-product" data-id="{{ $rowId }}" title="Remove product">
                                        <i class="icofont icofont-trash"></i>
                                    </button>
                                </div> </div> @empty
                            {{-- This fallback should no longer be reached due to the @if($count > 0) check --}}
                            <div class="product-card" style="text-align: center; color: #6b7280; padding: 2rem;">
                                Your cart is empty. (Fallback)
                            </div>
                        @endforelse
                        {{-- --- END: SERVER-SIDE RENDER --- --}}

                    </div>
                </div>

                <div class="order-summary-column">
                    <div class="order-summary-card">
                        <h4>Order Summary</h4>
                        
                        {{-- Use the variables from the controller for the initial state --}}
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
            {{-- --- END: CART NOT EMPTY --- --}}

        @else
            {{-- --- START: CART IS EMPTY (NEW) --- --}}

            <div class="empty-cart-container">
                <div class="empty-cart-icon">
                    {{-- This icon matches the style from your image --}}
                    <i class="icofont-bag-alt"></i> 
                </div>
                <h2>Your Cart is Empty</h2>
                <p>Looks like you haven't added anything to your cart yet.</p>
                {{-- This button links to your main product page (from web.php) --}}
                <a href="{{ route('product') }}" class="btn btn-continue-shopping">Continue Shopping</a>
            </div>

            {{-- --- END: CART IS EMPTY (NEW) --- --}}
        @endif
        {{-- --- END: CONDITIONAL RENDER --- --}}

    </div>
</div>

@endsection

@push('js')
    <script>
        $(document).ready(function () {
            
            // --- Quantity Plus ---
            $(document).on('click', '.btn-qty-plus', function(e) {
                let id  = $(this).data('id');
                let btn = $(this);
                var divUpd = $(this).closest('.quantity-select').find('.value'),
                newVal = parseInt(divUpd.val(), 10) + 1;
                
                // Enable minus button
                $(this).closest('.quantity-select').find('.btn-qty-minus').prop('disabled', false);
                
                updateCartAjax(id, newVal, btn);
            });

            // --- Quantity Minus ---
            $(document).on('click', '.btn-qty-minus', function(e) {
                var divUpd = $(this).closest('.quantity-select').find('.value'),
                newVal = parseInt(divUpd.val(), 10) - 1;

                if (newVal >= 1) {
                    let id  = $(this).data('id');
                    let btn = $(this);
                    
                    // Disable minus button if qty hits 1
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
                        // Call getCart() to rebuild the list after removal
                        getCart(); 
                    },
                    complete: function() { 
                        // The card will be removed by getCart(), so no need to remove 'disable'
                    }
                });
            });

            // --- Reusable AJAX function for quantity update ---
            function updateCartAjax(id, qty, btn) {
                // Find related elements
                var card = $(btn).closest('.product-card');
                var qtySelect = $(btn).closest('.quantity-select');
                
                $.ajax({
                    type: 'GET',
                    url: '/update/cart/'+id+'/'+qty,
                    dataType: "JSON",
                    beforeSend: function() {
                        // Disable all buttons in this selector
                        qtySelect.find('.btn-qty').prop('disabled', true);
                        card.css('opacity', '0.7');
                    },
                    success: function (response) {
                        // Call getCart() to rebuild the list after update
                        // getCart() will re-enable buttons by re-rendering
                        getCart();
                    },
                    error: function() {
                        // On error, re-enable buttons and restore opacity
                        qtySelect.find('.btn-qty').prop('disabled', false);
                        // Re-check minus button status
                        var currentVal = parseInt(qtySelect.find('.value').val(), 10);
                        qtySelect.find('.btn-qty-minus').prop('disabled', currentVal <= 1);
                        card.css('opacity', '1');
                    },
                    complete: function() {
                        // On success, getCart() handles everything.
                        // On error, we've reset the state.
                    }
                });
            }
       
  
            // --- getCart() function ---
            // This function is now ONLY used to update the cart after an AJAX action
            function getCart() {
                $.ajax({
                    type: "GET",
                    url: "{!! route('get.cart') !!}",
                    dataType: "JSON",
                    success: function (response) {
                        
                        // --- UPDATED LOGIC ---
                        // If the cart is now empty, just reload the page
                        // to show the new server-rendered "Empty Cart" view.
                        if (response.count == 0) {
                            window.location.reload();
                            return; // Stop processing
                        }
                        // --- END UPDATED LOGIC ---


                        var total_qty = 0;
                        var total = 0;
                        let html = '';
                        var currency = ' {{ $currency }}'; // Add space for formatting
                        
                        // We no longer need an 'if(response.count > 0)' here
                        // because the logic above handles the '0' case.
                        
                        $.each(response.carts, function (key, val) {
                            total_qty += parseInt(val.qty);
                            total     += parseInt(val.subtotal);
                            var isQtyOne = parseInt(val.qty) <= 1 ? 'disabled' : '';

                            // --- Start Product Card ---
                            html += '<div class="product-card">';
                            
                            // 1. Product Info (Image + Title/Price)
                            html += '<div class="product-info">';
                            html += '<div class="product-image">';
                            html += '<a href="/product/'+val.options.slug+'">';
                            html += '<img src="/uploads/product/'+val.options.image+'" alt="Product Image">';
                            html += '</a>';
                            html += '</div>'; // end product-image
                            html += '<div class="product-details">';
                            html += '<a href="/product/'+val.options.slug+'" class="product-title">'+val.name+'</a>';
                            html += '<span class="product-unit-price">'+number_format(val.price, 2, '.', ',') + currency +'</span>';
                            html += '</div>'; // end product-details
                            html += '</div>'; // end product-info
                            
                            // 2. Product Actions (Qty, Total, Remove)
                            html += '<div class="product-actions">';
                            // Quantity
                            html += '<div class="quantity-select">';
                            html += '<button class="btn-qty btn-qty-minus" data-id="'+key+'" '+isQtyOne+'>-</button>';
                            html += '<input type="text" class="value" value="'+val.qty+'" readonly>';
                            html += '<button class="btn-qty btn-qty-plus" data-id="'+key+'">+</button>';
                            html += '</div>'; // end quantity-select
                            // Line Total
                            html += '<span class="product-line-total">'+number_format(val.subtotal, 2, '.', ',') + currency +'</span>';
                            // Removal
                            html += '<button class="btn-remove" id="remove-product" data-id="'+key+'" title="Remove product">';
                            html += '<i class="icofont icofont-trash"></i>';
                            html += '</button>';
                            html += '</div>'; // end product-actions

                            html += '</div>'; // end product-card
                        });
                        
                        // The 'else' block for an empty cart is no longer needed here.
                        
                        // Inject the built HTML
                        $('.product-all').html(html);
                        
                        // --- Update Order Summary ---
                        var formatted_total = number_format(total, 2, '.', ',');
                        var item_text = total_qty == 1 ? ' item' : ' items';
                        $('#summary-item-count').text(total_qty + item_text);
                        $('#summary-subtotal').text(formatted_total + currency);
                        $('#summary-total').text(formatted_total + currency);
                        
                        // --- Update Checkout Button State ---
                        // This logic is still fine, as it will never be 'disabled'
                        // if the 'response.count == 0' reload logic works.
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
                    }
                });
            }
            
            // --- NO LONGER NEEDED ON INITIAL LOAD ---
            // getCart();

            // --- number_format function (no change needed) ---
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