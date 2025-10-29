{{-- 
    Similar Products Component
    
    Usage: @include('components.similar-products', ['products' => $relatedProducts])
    
    Props:
    - $products: Collection of products to display
    - $title (optional): Custom section title, defaults to "Similar Products You May Like"
--}}

@if(isset($products) && $products->count() > 0)
<div class="similar-products-wrapper">
    <style>
        /* Similar Products Section Styles */
        .similar-products-wrapper {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-top: 40px;
            padding: 40px 30px;
            position: relative;
            overflow: hidden;
        }

        .similar-products-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
        }

        .similar-products-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f3f4f6;
        }

        .similar-products-title {
            font-size: 28px;
            font-weight: 800;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
            letter-spacing: -0.025em;
        }

        .similar-products-title i {
            color: #6366f1;
            font-size: 24px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        .products-count-badge {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

.similar-products-grid {
            display: grid;
            /* Changed to explicitly set 4 equal-width columns on desktop */
            grid-template-columns: repeat(4, 1fr); 
            gap: 24px;
        }

        .similar-product-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .similar-product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 16px;
            padding: 2px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .similar-product-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15), 0 10px 20px -5px rgba(0, 0, 0, 0.1);
        }

        .similar-product-card:hover::before {
            opacity: 1;
        }

        .similar-product-image-wrapper {
            position: relative;
            width: 100%;
            height: 280px;
            overflow: hidden;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .similar-product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .similar-product-card:hover .similar-product-image {
            transform: scale(1.12) rotate(2deg);
        }

        .product-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ff4757 100%);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(255, 71, 87, 0.4);
            text-transform: uppercase;
        }

        .quick-view-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.8) 100%);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .similar-product-card:hover .quick-view-overlay {
            opacity: 1;
        }

        .quick-view-text {
            color: white;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transform: translateY(20px);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .similar-product-card:hover .quick-view-text {
            transform: translateY(0);
        }

        .similar-product-info {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-vendor {
            font-size: 11px;
            color: #6366f1;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .similar-product-title-link {
            text-decoration: none;
            color: inherit;
        }

        .similar-product-title {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 12px 0;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 48px;
            transition: color 0.3s ease;
        }

        .similar-product-title-link:hover .similar-product-title {
            color: #6366f1;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
        }

        .rating-stars {
            display: flex;
            gap: 2px;
        }

        .rating-stars i {
            font-size: 14px;
        }

        .rating-stars i.filled {
            color: #fbbf24;
        }

        .rating-stars i.empty {
            color: #d1d5db;
        }

        .rating-count {
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
        }

        .similar-product-price-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .similar-product-price {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.025em;
        }

        .similar-product-original-price {
            font-size: 16px;
            color: #9ca3af;
            text-decoration: line-through;
            font-weight: 500;
        }

        .discount-tag {
            background: linear-gradient(135deg, #fef3c7 0%, #fbbf24 100%);
            color: #78350f;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stock-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 12px;
            margin-bottom: 16px;
            width: fit-content;
        }

        .stock-indicator.in-stock {
            background: #d1fae5;
            color: #065f46;
        }

        .stock-indicator.low-stock {
            background: #fef3c7;
            color: #92400e;
        }

        .stock-indicator.out-of-stock {
            background: #fee2e2;
            color: #991b1b;
        }

        .stock-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .stock-indicator.in-stock .stock-dot {
            background: #10b981;
        }

        .stock-indicator.low-stock .stock-dot {
            background: #f59e0b;
        }

        .stock-indicator.out-of-stock .stock-dot {
            background: #ef4444;
        }

        .similar-product-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(17, 24, 39, 0.2);
            letter-spacing: 0.3px;
            margin-top: auto;
        }

        .similar-product-btn:hover {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
            color: white;
            text-decoration: none;
        }

        .similar-product-btn i {
            transition: transform 0.3s ease;
        }

        .similar-product-btn:hover i {
            transform: translateX(4px);
        }

        .no-products-message {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .no-products-message i {
            font-size: 64px;
            color: #d1d5db;
            margin-bottom: 20px;
        }

        .no-products-message p {
            font-size: 18px;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .similar-products-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .similar-products-wrapper {
                padding: 30px 20px;
                margin-top: 30px;
                border-radius: 12px;
            }

            .similar-products-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                margin-bottom: 24px;
            }

            .similar-products-title {
                font-size: 22px;
            }

            .similar-products-title i {
                font-size: 20px;
            }

            .similar-products-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 16px;
            }

            .similar-product-image-wrapper {
                height: 200px;
            }

            .similar-product-info {
                padding: 16px;
            }

            .similar-product-title {
                font-size: 14px;
                min-height: 42px;
            }

            .similar-product-price {
                font-size: 18px;
            }

            .similar-product-original-price {
                font-size: 14px;
            }

            .similar-product-btn {
                padding: 10px 16px;
                font-size: 13px;
            }

            .product-badge {
                padding: 4px 10px;
                font-size: 10px;
            }

            .discount-tag {
                font-size: 10px;
                padding: 3px 8px;
            }
        }

        @media (max-width: 480px) {
            .similar-products-wrapper {
                padding: 24px 16px;
            }

            .similar-products-title {
                font-size: 20px;
            }

            .similar-products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .similar-product-image-wrapper {
                height: 160px;
            }

            .similar-product-info {
                padding: 12px;
            }

            .product-vendor {
                font-size: 10px;
                margin-bottom: 6px;
            }

            .similar-product-title {
                font-size: 13px;
                min-height: 38px;
                margin-bottom: 8px;
            }

            .product-rating {
                margin-bottom: 8px;
            }

            .rating-stars i {
                font-size: 12px;
            }

            .rating-count {
                font-size: 11px;
            }

            .similar-product-price {
                font-size: 16px;
            }

            .similar-product-original-price {
                font-size: 12px;
            }

            .similar-product-price-wrapper {
                gap: 6px;
                margin-bottom: 12px;
            }

            .stock-indicator {
                font-size: 10px;
                padding: 4px 8px;
                margin-bottom: 12px;
            }

            .stock-dot {
                width: 5px;
                height: 5px;
            }

            .similar-product-btn {
                padding: 8px 12px;
                font-size: 12px;
                gap: 6px;
            }

            .similar-product-btn i {
                font-size: 12px;
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

        /* Touch Device Optimization */
        @media (hover: none) {
            .similar-product-card {
                transition: transform 0.2s ease;
            }

            .similar-product-card:active {
                transform: scale(0.98);
            }

            .quick-view-overlay {
                display: none;
            }
        }
    </style>

    <div class="similar-products-header">
        <h2 class="similar-products-title">
            <i class="fas fa-layer-group"></i>
            {{ $title ?? 'Similar Products You May Like' }}
        </h2>
        <span class="products-count-badge">{{ $products->count() }} Products</span>
    </div>

    <div class="similar-products-grid">
        @foreach($products as $item)
            <div class="similar-product-card">
                {{-- Discount Badge --}}
                @if($item->discount_price > 0)
                    @php
                        $discountPercent = round((($item->regular_price - $item->discount_price) / $item->regular_price) * 100);
                    @endphp
                    <span class="product-badge">-{{ $discountPercent }}% OFF</span>
                @endif

                {{-- Product Image --}}
                <a href="{{ route('product.details', $item->slug) }}" class="similar-product-image-wrapper">
                    <img src="{{ asset('uploads/product/' . $item->image) }}" 
                         alt="{{ $item->title }}" 
                         class="similar-product-image"
                         loading="lazy">
                    
                    <div class="quick-view-overlay">
                        <span class="quick-view-text">
                            <i class="fas fa-eye"></i>
                            Quick View
                        </span>
                    </div>
                </a>

                {{-- Product Info --}}
                <div class="similar-product-info">
                    <p class="product-vendor">OPPS</p>
                    
                    <a href="{{ route('product.details', $item->slug) }}" class="similar-product-title-link">
                        <h3 class="similar-product-title">{{ $item->title }}</h3>
                    </a>

                    {{-- Rating --}}
                    @if($item->reviews && $item->reviews->count() > 0)
                        @php
                            $avgRating = round($item->reviews->avg('rating'));
                        @endphp
                        <div class="product-rating">
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $avgRating ? 'filled' : 'empty' }}"></i>
                                @endfor
                            </div>
                            <span class="rating-count">({{ $item->reviews->count() }})</span>
                        </div>
                    @endif

                    {{-- Stock Indicator --}}
                    @if(isset($item->stock))
                        @if($item->stock > 10)
                            <div class="stock-indicator in-stock">
                                <span class="stock-dot"></span>
                                <span>In Stock</span>
                            </div>
                        @elseif($item->stock > 0 && $item->stock <= 10)
                            <div class="stock-indicator low-stock">
                                <span class="stock-dot"></span>
                                <span>Only {{ $item->stock }} left</span>
                            </div>
                        @else
                            <div class="stock-indicator out-of-stock">
                                <span class="stock-dot"></span>
                                <span>Out of Stock</span>
                            </div>
                        @endif
                    @endif

                    {{-- Price --}}
                    <div class="similar-product-price-wrapper">
                        @if($item->discount_price > 0)
                            <span class="similar-product-price">৳{{ number_format($item->discount_price, 0) }}</span>
                            <span class="similar-product-original-price">৳{{ number_format($item->regular_price, 0) }}</span>
                            <span class="discount-tag">Save {{ $discountPercent }}%</span>
                        @else
                            <span class="similar-product-price">৳{{ number_format($item->regular_price, 0) }}</span>
                        @endif
                    </div>

                    {{-- View Details Button --}}
                    <a href="{{ route('product.details', $item->slug) }}" class="similar-product-btn">
                        <i class="fas fa-shopping-bag"></i>
                        View Details
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif