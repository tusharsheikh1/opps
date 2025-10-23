@extends('layouts.frontend.app')

@push('meta')
<meta name='description' content="Order Successfully Placed - Thank You for Your Purchase"/>
<meta name='keywords' content="Order Success, Purchase Confirmation, E-commerce" />
@endpush

@section('title', 'Thank You - Order Confirmed!')

@section('content')

<style>
    :root {
        --color-success: #10b981; /* Emerald 500 */
        --color-success-light: #ecfdf5; /* Emerald 50 */
        --color-primary: #3b82f6; /* Blue 500 */
        --color-primary-hover: #2563eb; /* Blue 600 */
        --color-text-dark: #1f2937; /* Gray 800 */
        --color-text-medium: #4b5563; /* Gray 600 */
        --color-text-light: #6b7280; /* Gray 500 */
        --color-border: #e5e7eb; /* Gray 200 */
        --color-background: #f9fafb; /* Gray 50 */
    }

    .order-success-page {
        background-color: var(--color-background);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        padding: 40px 15px;
        min-height: 85vh; /* Adjusted min-height */
        display: flex; /* Added for centering */
        align-items: center; /* Added for centering */
        justify-content: center; /* Added for centering */
    }

    .success-card {
        max-width: 800px;
        width: 100%; /* Ensure it takes width on smaller screens */
        margin: 20px auto; /* Added top/bottom margin */
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07); /* Softer shadow */
        overflow: hidden;
    }

    /* 1. Header */
    .success-header {
        padding: 40px;
        text-align: center;
        border-bottom: 1px solid var(--color-border);
        background: linear-gradient(135deg, var(--color-success-light) 0%, #ffffff 100%); /* Subtle gradient */
    }

    .success-icon {
        width: 60px;
        height: 60px;
        background: var(--color-success); /* Solid success color */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: white; /* White icon */
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.4); /* Icon shadow */
    }

    .success-icon i {
        font-size: 28px;
    }

    .success-title {
        font-size: 1.75rem; /* Slightly smaller */
        font-weight: 700;
        color: var(--color-text-dark);
        margin: 0;
    }

    .success-subtitle {
        font-size: 1.05rem; /* Slightly smaller */
        color: var(--color-text-medium);
        margin-top: 8px;
        line-height: 1.6;
    }

    .success-email-notice {
        font-size: 0.9rem; /* Slightly smaller */
        color: var(--color-text-light);
        margin-top: 15px;
    }

    /* 2. Key Details */
    .order-key-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); /* Adjusted minmax */
        gap: 25px; /* Increased gap */
        padding: 30px 40px; /* Adjusted padding */
        background: #ffffff; /* White background */
        border-bottom: 1px solid var(--color-border);
    }

    .detail-box {
        text-align: left;
    }

    .detail-box-label {
        font-size: 0.8rem; /* Slightly smaller */
        font-weight: 600;
        color: var(--color-text-light);
        text-transform: uppercase;
        letter-spacing: 0.5px; /* Added letter spacing */
        margin-bottom: 6px;
    }

    .detail-box-value {
        font-size: 1rem; /* Slightly smaller */
        font-weight: 600;
        color: var(--color-text-dark);
        line-height: 1.4; /* Adjusted line height */
    }

    .detail-box-value.order-number {
        font-family: 'Courier New', monospace;
        font-size: 1.1rem; /* Adjusted size */
        color: var(--color-primary);
        font-weight: 700; /* Bolder */
    }

    /* 3. Order Items */
    .order-items-summary {
        padding: 30px 40px;
    }

    .summary-title {
        font-size: 1.2rem; /* Adjusted size */
        font-weight: 600;
        color: var(--color-text-dark);
        margin-bottom: 25px; /* Increased margin */
        padding-bottom: 10px;
        border-bottom: 1px solid var(--color-border);
    }

    .product-list { /* Added wrapper */
        display: flex;
        flex-direction: column;
        gap: 20px; /* Gap between items */
    }

    .product-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding-bottom: 20px; /* Use gap instead of padding/border */
        border-bottom: 1px solid var(--color-border); /* Keep border */
    }

    .product-item:last-child {
       border-bottom: none; /* Remove border for last item */
       padding-bottom: 0;
    }


    .product-image {
        width: 70px; /* Slightly larger */
        height: 70px;
        border-radius: 8px;
        background: var(--color-background); /* Lighter background */
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid var(--color-border); /* Subtle border */
    }

    .product-image-placeholder { /* Style for placeholder */
        width: 70px;
        height: 70px;
        border-radius: 8px;
        background: var(--color-background);
        border: 1px solid var(--color-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: var(--color-text-light);
        flex-shrink: 0;
    }


    .product-info {
        flex-grow: 1;
    }

    .product-name {
        font-size: 1rem;
        font-weight: 600;
        color: var(--color-text-dark);
        margin: 0;
        line-height: 1.4;
    }

    .product-meta {
        font-size: 0.85rem; /* Smaller meta */
        color: var(--color-text-light);
        margin-top: 5px; /* Increased margin */
    }

    .product-price {
        font-size: 1rem;
        font-weight: 600;
        color: var(--color-text-dark);
        text-align: right;
        flex-shrink: 0;
        white-space: nowrap; /* Prevent price wrapping */
    }

    /* 4. Details Grid (Billing & Shipping) */
    .details-grid {
        display: grid;
        grid-template-columns: 1fr; /* Default to single column */
        gap: 1px;
        border-top: 1px solid var(--color-border);
        background: var(--color-border);
    }

    /* Use media query for two columns on larger screens */
    @media (min-width: 768px) {
        .details-grid {
            grid-template-columns: 1fr 1fr;
        }
    }


    .grid-section {
        background: #ffffff;
        padding: 30px 40px;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--color-text-dark);
        margin-bottom: 20px; /* Increased margin */
    }

    .address-details p {
        font-size: 0.95rem;
        color: var(--color-text-medium);
        line-height: 1.7; /* Increased line height */
        margin: 0 0 5px 0; /* Spacing between address lines */
    }
     .address-details p:last-child {
         margin-bottom: 0;
     }

    /* Billing Summary */
    .billing-summary .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px; /* Increased margin */
        font-size: 0.95rem;
    }

    .billing-summary .detail-label {
        color: var(--color-text-medium);
    }

    .billing-summary .detail-value {
        font-weight: 500;
        color: var(--color-text-dark);
    }

    .billing-summary .detail-value.discount {
        color: #ef4444; /* Red 500 */
        font-weight: 600;
    }

    .billing-summary .total-divider {
        height: 1px;
        background: var(--color-border);
        margin: 20px 0; /* Increased margin */
    }

    .billing-summary .total-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1.15rem; /* Slightly smaller */
        font-weight: 700;
        color: var(--color-text-dark);
    }

    /* 5. Footer Actions */
    .success-footer {
        padding: 30px 40px;
        background: var(--color-background);
        border-top: 1px solid var(--color-border);
        display: flex;
        flex-wrap: wrap; /* Allow wrapping on small screens */
        justify-content: center;
        gap: 15px;
    }

    .action-btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 0.95rem; /* Slightly smaller */
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease-in-out; /* Faster transition */
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid transparent; /* Use 1px border */
        min-width: 180px; /* Slightly wider */
        justify-content: center;
        cursor: pointer; /* Add cursor */
    }

    .btn-primary {
        background: var(--color-primary);
        color: white;
        border-color: var(--color-primary);
    }

    .btn-primary:hover {
        background: var(--color-primary-hover);
        border-color: var(--color-primary-hover);
        transform: translateY(-2px); /* Slightly more lift */
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3); /* Add shadow on hover */
        color: white;
        text-decoration: none;
    }

    .btn-secondary {
        background: white;
        color: var(--color-text-medium);
        border-color: #cbd5e1; /* Gray 300 */
    }

    .btn-secondary:hover {
        background: #f8fafc; /* Gray 50 slightly darker */
        border-color: #94a3b8; /* Gray 400 */
        color: var(--color-text-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        text-decoration: none;
    }

    .action-btn i {
        font-size: 1em; /* Make icon size relative */
    }


    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .order-success-page {
            padding: 0; /* Remove padding for full width card */
             align-items: flex-start; /* Align card to top */
        }

        .success-card {
            margin: 0;
            border-radius: 0;
            box-shadow: none;
            min-height: 100vh; /* Ensure card takes full height */
        }

        .success-header,
        .order-key-details,
        .order-items-summary,
        .grid-section,
        .success-footer {
            padding-left: 20px;
            padding-right: 20px;
        }

         .success-header { padding-top: 40px; padding-bottom: 30px;}
         .order-key-details { grid-template-columns: 1fr 1fr; gap: 15px;} /* Keep 2 columns if possible */
         .detail-box-value { font-size: 0.95rem;}
         .detail-box-value.order-number { font-size: 1rem;}

        .product-image, .product-image-placeholder { width: 60px; height: 60px; } /* Adjust size */
        .product-name { font-size: 0.95rem; }
        .product-price { font-size: 0.95rem; }
        .product-list { gap: 15px;}
        .product-item { padding-bottom: 15px;}

        .details-grid { grid-template-columns: 1fr; gap: 0; border-top: none;} /* Remove gap and top border */
        .grid-section { border-top: 1px solid var(--color-border); } /* Add border between sections */
        .grid-section:first-child { border-top: none;}


        .success-footer {
            flex-direction: column;
            gap: 12px; /* Reduce gap */
            padding-top: 25px;
            padding-bottom: 25px;
        }

        .action-btn {
            width: 100%;
            min-width: auto; /* Remove min-width */
        }
    }
</style>

<div class="order-success-page">
    <div class="success-card">

        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1 class="success-title">Thank You For Your Order!</h1>
            <p class="success-subtitle">Your order has been placed successfully and is being processed.</p>
            @if(!empty($data['email']) && $data['email'] !== 'noreply@lems.shop')
                <p class="success-email-notice">
                    A confirmation email with details has been sent to <strong>{{ $data['email'] }}</strong>.
                </p>
            @endif
        </div>

        <div class="order-key-details">
            <div class="detail-box">
                <div class="detail-box-label">Order Number</div>
                <div class="detail-box-value order-number">{{ $data['invoice'] ?? 'N/A' }}</div>
            </div>
            <div class="detail-box">
                <div class="detail-box-label">Order Date</div>
                <div class="detail-box-value">
                    {{ $data['date'] ? \Carbon\Carbon::parse($data['date'])->format('F d, Y') : now()->format('F d, Y') }}
                </div>
            </div>
            <div class="detail-box">
                <div class="detail-box-label">Payment Method</div>
                <div class="detail-box-value">{{ $data['payment_method'] ?? 'N/A' }}</div>
            </div>
            <div class="detail-box">
                <div class="detail-box-label">Total Amount</div>
                <div class="detail-box-value">
                    {{-- Use setting('CURRENCY_ICON') consistently --}}
                    {{ setting('CURRENCY_ICON') ?? '৳' }}{{ number_format($data['total'] ?? 0, 2) }}
                </div>
            </div>
        </div>

        {{-- Check if orderDetails exists and is not empty --}}
        @if(isset($data['orderDetails']) && $data['orderDetails']->isNotEmpty())
        <div class="order-items-summary">
            <h2 class="summary-title">Order Summary ({{ $data['orderDetails']->count() }} {{ $data['orderDetails']->count() > 1 ? 'items' : 'item' }})</h2>
            <div class="product-list">
                @foreach($data['orderDetails'] as $item)
                    <div class="product-item">
                        {{-- Logic to generate image URL --}}
                        @php
                            $imageUrl = null;
                            // Ensure the 'product' relationship is loaded and thumbnail_image exists
                            if (isset($item->product) && !empty($item->product->thumbnail_image)) {
                                // Check if the path already includes 'uploads/product/'
                                if (str_contains($item->product->thumbnail_image, 'uploads/product/')) {
                                    $imageUrl = asset($item->product->thumbnail_image);
                                } else {
                                    // Assume it's just the filename and prepend the path
                                    $imageUrl = asset('uploads/product/' . $item->product->thumbnail_image);
                                }
                            }
                        @endphp

                        {{-- Display image or placeholder --}}
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $item->title ?? 'Product Image' }}" class="product-image">
                        @else
                            {{-- Fallback placeholder --}}
                            <div class="product-image-placeholder">
                                <i class="fas fa-image"></i> {{-- Changed icon --}}
                            </div>
                        @endif

                        <div class="product-info">
                            <h3 class="product-name">{{ $item->title ?? 'Product Name' }}</h3>
                            <div class="product-meta">
                                Qty: {{ $item->qty ?? 1 }}
                                {{-- Optionally display color if available in $item --}}
                                @if(!empty($item->color) && $item->color != 'blank')
                                 | Color: {{ Str::ucfirst($item->color) }}
                                @endif
                                {{-- Decode and display size/attributes if stored in $item->size --}}
                                @php
                                    try {
                                        // Ensure $item->size is treated as string before decoding
                                        $attributes = json_decode((string)($item->size ?? '{}'), true, 512, JSON_THROW_ON_ERROR);
                                    } catch (\JsonException $e) {
                                        $attributes = [];
                                        \Log::error("JSON Decode Error in order_success for item size: " . $e->getMessage(), ['item_size' => $item->size ?? null]);
                                    }
                                @endphp
                                {{-- Check if $attributes is a valid array --}}
                                @if(!empty($attributes) && is_array($attributes))
                                    @foreach($attributes as $key => $valueId)
                                         {{-- You might need to fetch the attribute value name based on $valueId --}}
                                         {{-- Basic display: | Key: ValueID --}}
                                         | {{ Str::ucfirst(str_replace('_', ' ', $key)) }}: {{ $valueId }} {{-- Displaying Value ID for now --}}
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="product-price">
                             {{-- Use setting('CURRENCY_ICON') consistently --}}
                            {{ setting('CURRENCY_ICON') ?? '৳' }}{{ number_format(($item->price ?? 0) * ($item->qty ?? 1), 2) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @else
         <div style="padding: 20px 40px; text-align: center; color: var(--color-text-light);">No items found in this order.</div>
        @endif

        <div class="details-grid">
            <div class="grid-section shipping-details">
                <h3 class="section-title">Shipping Address</h3>
                <div class="address-details">
                    <p><strong>{{ $data['name'] ?? 'N/A' }}</strong></p>
                    <p><i class="fas fa-phone-alt fa-fw" style="color: var(--color-text-light); margin-right: 5px;"></i>{{ $data['phone'] ?? 'N/A' }}</p>
                    <p><i class="fas fa-map-marker-alt fa-fw" style="color: var(--color-text-light); margin-right: 5px;"></i>{{ $data['address'] ?? 'No address provided' }}</p>
                    {{-- Optionally add City, District etc. if available --}}
                    {{-- <p>{{ $data['town'] ?? '' }}{{ isset($data['district']) ? ', ' . $data['district'] : '' }}</p> --}}
                </div>
            </div>

            <div class="grid-section billing-summary">
                <h3 class="section-title">Billing Summary</h3>

                <div class="detail-item">
                    <span class="detail-label">Subtotal:</span>
                    <span class="detail-value">{{ setting('CURRENCY_ICON') ?? '৳' }}{{ number_format($data['subtotal'] ?? 0, 2) }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Shipping:</span>
                    <span class="detail-value">{{ setting('CURRENCY_ICON') ?? '৳' }}{{ number_format($data['shipping_charge'] ?? 0, 2) }}</span>
                </div>

                @if(isset($data['discount']) && $data['discount'] > 0)
                <div class="detail-item">
                    <span class="detail-label">Discount{{ isset($data['coupon_code']) && !empty($data['coupon_code']) ? ' (' . $data['coupon_code'] . ')' : '' }}:</span>
                    <span class="detail-value discount">-{{ setting('CURRENCY_ICON') ?? '৳' }}{{ number_format($data['discount'], 2) }}</span>
                </div>
                @endif

                <div class="total-divider"></div>

                <div class="total-item">
                    <span class="total-label">Total</span>
                    <span class="total-value">{{ setting('CURRENCY_ICON') ?? '৳' }}{{ number_format($data['total'] ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="success-footer">
            <a href="{{ route('order') }}" class="action-btn btn-primary">
                <i class="fas fa-receipt"></i> {{-- Changed icon --}}
                View My Orders
            </a>
            <a href="{{ route('home') }}" class="action-btn btn-secondary">
                <i class="fas fa-store"></i> {{-- Changed icon --}}
                Continue Shopping
            </a>
        </div>

    </div>
</div>

@endsection

@push('js')
{{-- No JS needed for this static page --}}
@endpush