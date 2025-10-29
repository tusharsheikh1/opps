<?php if(!isset($classes)){$classes='modern-product-col';}?>

<div class="product {{$classes}} pxc">
    <div class="modern-product-card">
        <style>
            .modern-product-col {
                padding: 8px;
                flex: 0 0 auto;
            }

            .modern-product-card {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                background: #ffffff;
                border-radius: 20px;
                overflow: hidden;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                display: flex;
                flex-direction: column;
                position: relative;
                border: 1px solid rgba(0, 0, 0, 0.04);
                height: 100%;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            }

            .modern-product-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 24px 48px rgba(0, 0, 0, 0.12), 
                           0 8px 16px rgba(0, 0, 0, 0.08);
                border-color: rgba(99, 102, 241, 0.2);
            }

            /* Sale Badge - More Dynamic */
            .sale-badge {
                position: absolute;
                top: 12px;
                left: 12px;
                background: linear-gradient(135deg, #ff6b6b 0%, #ff4757 100%);
                color: white;
                padding: 8px 14px;
                border-radius: 30px;
                font-size: 0.7rem;
                font-weight: 700;
                letter-spacing: 0.8px;
                z-index: 10;
                box-shadow: 0 6px 20px rgba(255, 71, 87, 0.35);
                animation: gentlePulse 3s ease-in-out infinite;
                text-transform: uppercase;
            }

            @keyframes gentlePulse {
                0%, 100% {
                    transform: scale(1);
                    box-shadow: 0 6px 20px rgba(255, 71, 87, 0.35);
                }
                50% {
                    transform: scale(1.05);
                    box-shadow: 0 8px 24px rgba(255, 71, 87, 0.45);
                }
            }

            .product-image-container {
                position: relative;
                overflow: hidden;
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                aspect-ratio: 1 / 1;
            }

            .product-image-container img {
                width: 100%;
                height: 100%;
                display: block;
                object-fit: cover;
                transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .modern-product-card:hover .product-image-container img {
                transform: scale(1.08);
            }

            /* Quick View Overlay - Enhanced */
            .quick-view-overlay {
                position: absolute;
                inset: 0;
                background: linear-gradient(180deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.8) 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity 0.4s ease;
                pointer-events: none;
            }

            .modern-product-card:hover .quick-view-overlay {
                opacity: 1;
                pointer-events: all;
            }

            .quick-view-btn {
                background: white;
                color: #111827;
                padding: 14px 28px;
                border-radius: 14px;
                font-weight: 700;
                font-size: 0.875rem;
                border: none;
                cursor: pointer;
                transform: translateY(25px);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
                letter-spacing: 0.3px;
            }

            .modern-product-card:hover .quick-view-btn {
                transform: translateY(0);
            }

            .quick-view-btn:hover {
                background: #6366f1;
                color: white;
                transform: scale(1.05) translateY(-2px);
                box-shadow: 0 12px 32px rgba(99, 102, 241, 0.4);
            }

            .product-content {
                padding: 18px;
                display: flex;
                flex-direction: column;
                flex-grow: 1;
                background: white;
            }

            .product-vendor {
                font-size: 0.7rem;
                color: #6366f1;
                margin-bottom: 6px;
                text-transform: uppercase;
                letter-spacing: 1.2px;
                font-weight: 700;
                display: inline-block;
            }

            .product-title-link {
                text-decoration: none;
                color: #111827;
            }

            .product-title {
                font-size: 1rem;
                font-weight: 600;
                line-height: 1.4;
                margin: 0 0 10px 0;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                min-height: 44px;
                color: #1f2937;
                transition: color 0.3s ease;
            }

            .product-title-link:hover .product-title {
                color: #6366f1;
            }

            .product-rating {
                display: flex;
                align-items: center;
                gap: 6px;
                margin-bottom: 12px;
            }

            .stars {
                display: flex;
                gap: 1px;
            }

            .stars span {
                color: #fbbf24;
                font-size: 0.95rem;
            }

            .review-count {
                font-size: 0.75rem;
                color: #6b7280;
                background: #f9fafb;
                padding: 3px 8px;
                border-radius: 10px;
                font-weight: 500;
            }

            .stock-indicator {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 0.75rem;
                font-weight: 600;
                margin-bottom: 12px;
                padding: 6px 12px;
                border-radius: 12px;
                width: fit-content;
            }

            .stock-dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
            }

            .in-stock {
                color: #065f46;
                background: #d1fae5;
            }

            .in-stock .stock-dot {
                background: #10b981;
            }

            .low-stock {
                color: #92400e;
                background: #fef3c7;
            }

            .low-stock .stock-dot {
                background: #f59e0b;
            }

            .out-of-stock {
                color: #991b1b;
                background: #fee2e2;
            }

            .out-of-stock .stock-dot {
                background: #ef4444;
            }

            .price-container {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 16px;
                flex-wrap: wrap;
            }

            .current-price {
                font-size: 1.25rem;
                font-weight: 800;
                color: #111827;
                letter-spacing: -0.025em;
            }

            .original-price {
                font-size: 0.875rem;
                color: #9ca3af;
                text-decoration: line-through;
                font-weight: 500;
            }

            .discount-percentage {
                background: linear-gradient(135deg, #fef3c7 0%, #fbbf24 100%);
                color: #78350f;
                padding: 4px 8px;
                border-radius: 10px;
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                box-shadow: 0 2px 4px rgba(251, 191, 36, 0.2);
                height: 24px;
                display: flex;
                align-items: center;
            }

            .add-to-cart-btn {
                background: linear-gradient(135deg, #000000ff 0%, #111119ff 100%);
                color: white;
                border: none;
                padding: 14px 18px;
                border-radius: 16px;
                font-weight: 700;
                font-size: 0.875rem;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                box-shadow: 0 6px 20px rgba(99, 102, 241, 0.25);
                letter-spacing: 0.3px;
                margin-top: auto;
            }

            .add-to-cart-btn:hover {
                background: linear-gradient(135deg, #39f57eff 0%, #3730a3 100%);
                transform: translateY(-2px);
                box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
            }

            .add-to-cart-btn:active {
                transform: translateY(0);
                box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            }

            .add-to-cart-btn svg {
                transition: transform 0.3s ease;
            }

            .add-to-cart-btn:hover svg {
                transform: scale(1.1);
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .modern-product-col {
                    padding: 6px;
                }

                .modern-product-card {
                    border-radius: 16px;
                }

                .product-content {
                    padding: 16px;
                }

                .product-title {
                    font-size: 0.9rem;
                    min-height: 40px;
                }

                .current-price {
                    font-size: 1.1rem;
                }

                .original-price {
                    font-size: 0.75rem;
                }

                .discount-percentage {
                    font-size: 0.6rem;
                    padding: 2px 6px;
                    height: 20px;
                }

                .add-to-cart-btn {
                    padding: 10px 14px;
                    font-size: 0.8rem;
                    gap: 6px;
                    border-radius: 12px;
                }

                .add-to-cart-btn svg {
                    width: 16px;
                    height: 16px;
                }

                .sale-badge {
                    padding: 5px 8px;
                    font-size: 0.6rem;
                }

                .product-vendor {
                    font-size: 0.6rem;
                    margin-bottom: 4px;
                }

                .product-rating {
                    margin-bottom: 10px;
                    gap: 4px;
                }

                .stars {
                    gap: 0;
                }

                .stars span {
                    font-size: 0.8rem;
                }

                .review-count {
                    font-size: 0.65rem;
                    padding: 2px 6px;
                }

                .stock-indicator {
                    font-size: 0.7rem;
                    padding: 4px 10px;
                    margin-bottom: 10px;
                }

                .stock-dot {
                    width: 5px;
                    height: 5px;
                }

                .price-container {
                    gap: 6px;
                    margin-bottom: 12px;
                }
            }

            /* Extra Small Devices (360px and below) */
            @media (max-width: 360px) {
                .modern-product-col {
                    padding: 3px;
                }

                .product-title {
                    font-size: 0.8rem;
                    min-height: 36px;
                }

                .product-content {
                    padding: 10px;
                }

                .current-price {
                    font-size: 0.95rem;
                }

                .add-to-cart-btn {
                    padding: 9px 12px;
                    font-size: 0.75rem;
                }

                .add-to-cart-btn span {
                    display: none;
                }

                .add-to-cart-btn svg {
                    width: 18px;
                    height: 18px;
                }
            }

            /* Hover Effects Only on Non-Touch Devices */
            @media (hover: hover) and (pointer: fine) {
                .modern-product-card:hover {
                    transform: translateY(-6px);
                }
            }

            /* Touch Device Optimization */
            @media (hover: none) {
                .modern-product-card {
                    transition: transform 0.2s ease;
                }

                .modern-product-card:active {
                    transform: scale(0.98);
                }

                .quick-view-overlay {
                    display: none !important;
                }
            }

            /* Loading Animation */
            @keyframes shimmer {
                0% {
                    background-position: -1000px 0;
                }
                100% {
                    background-position: 1000px 0;
                }
            }

            .product-loading {
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 1000px 100%;
                animation: shimmer 2s infinite;
            }
        </style>

        @if($product->discount_price > 0)
            @php
                $discountPercent = round((($product->regular_price - $product->discount_price) / $product->regular_price) * 100);
            @endphp
            <span class="sale-badge">-{{$discountPercent}}% OFF</span>
        @endif

        <a href="{{route('product.details', $product->slug)}}" class="product-image-container">
            <img src="{{asset('uploads/product/'.$product->image)}}" alt="{{$product->title}}" loading="lazy">
            <div class="quick-view-overlay">
                <button class="quick-view-btn">Quick View</button>
            </div>
        </a>

        <div class="product-content">
            <p class="product-vendor">OPPS</p>
            
            <a href="{{route('product.details', $product->slug)}}" class="product-title-link">
                <h3 class="product-title">{{$product->title}}</h3>
            </a>
            
            @if($product->reviews->count() > 0)
                @php
                    $rating = round($product->reviews->avg('rating'));
                @endphp
                <div class="product-rating">
                    <div class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $rating)
                                <span>★</span>
                            @else
                                <span>☆</span>
                            @endif
                        @endfor
                    </div>
                    <span class="review-count">{{$product->reviews->count()}} reviews</span>
                </div>
            @endif

            @if(isset($product->stock))
                @if($product->stock > 10)
                    <div class="stock-indicator in-stock">
                        <span class="stock-dot"></span>
                        <span>In Stock</span>
                    </div>
                @elseif($product->stock > 0 && $product->stock <= 10)
                    <div class="stock-indicator low-stock">
                        <span class="stock-dot"></span>
                        <span>Only {{$product->stock}} left</span>
                    </div>
                @else
                    <div class="stock-indicator out-of-stock">
                        <span class="stock-dot"></span>
                        <span>Out of Stock</span>
                    </div>
                @endif
            @endif

            <div class="price-container">
                @if($product->discount_price > 0)
                    <span class="original-price">&#2547;{{number_format($product->regular_price, 0)}}</span>
                    <span class="current-price">&#2547;{{number_format($product->discount_price, 0)}}</span>
                    <span class="discount-percentage"> Save {{$discountPercent}}%</span>
                @else
                    <span class="current-price">&#2547;{{number_format($product->regular_price, 0)}}</span>
                @endif
            </div>

            <button class="add-to-cart-btn" id="productInfo" data-url="{{route('product.info', $product->slug)}}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                </svg>
                <span>Add to Cart</span>
            </button>
        </div>
    </div>
</div>