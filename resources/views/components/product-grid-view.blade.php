<?php if(!isset($classes)){$classes='modern-product-col';}?>

<div class="product {{$classes}} pxc">
    <div class="modern-product-card">
        <style>
            .modern-product-col {
                padding: 8px;
            }

            .modern-product-card {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
                background: #ffffff;
                border-radius: 8px;
                overflow: hidden;
                transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
                display: flex;
                flex-direction: column;
                position: relative;
                border: 1px solid #e5e7eb;
                height: 100%;
            }

            .modern-product-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }

            .product-image-container {
                position: relative;
                overflow: hidden;
            }

            .product-image-container img {
                width: 100%;
                display: block;
                aspect-ratio: 1 / 1;
                object-fit: cover;
                transition: transform 0.3s ease;
            }
            
            .modern-product-card:hover .product-image-container img {
                transform: scale(1.05);
            }

            .hot-selling-badge {
                position: absolute;
                bottom: 10px;
                left: 50%;
                transform: translateX(-50%);
                background-color: #ff9f43;
                color: white;
                padding: 5px 15px;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 600;
                z-index: 2;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                white-space: nowrap;
            }

            .product-content {
                padding: 12px;
                display: flex;
                flex-direction: column;
                flex-grow: 1;
            }

            .product-title-link {
                text-decoration: none;
                color: #111827;
            }

            .product-title {
                font-size: 1rem;
                font-weight: 500;
                line-height: 1.4;
                margin: 0 0 4px 0;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                min-height: 45px;
            }

            .product-title-link:hover .product-title {
                text-decoration: underline;
                color: #3b82f6;
            }

            .product-vendor {
                font-size: 0.8rem;
                color: #6b7280;
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .product-rating {
                display: flex;
                align-items: center;
                gap: 5px;
                margin-bottom: 8px;
            }

            .stars {
                color: #f59e0b;
            }

            .review-count {
                font-size: 0.8rem;
                color: #6b7280;
            }

            .price-container {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                margin-top: auto;
                margin-bottom: 12px;
            }
            
            .original-price {
                font-size: 0.9rem;
                color: #6b7280;
                text-decoration: line-through;
            }
            
            .current-price {
                font-size: 1.2rem;
                font-weight: 600;
                color: #111827;
            }
            
            .choose-options-btn {
                display: block;
                width: 100%;
                text-align: center;
                padding: 10px 5px; /* Reduced horizontal padding */
                border: 1px solid #374151;
                border-radius: 6px;
                text-decoration: none;
                color: #374151;
                font-weight: 500;
                background-color: #fff;
                transition: background-color 0.2s ease, border-color 0.2s ease;
                font-size: 0.9rem; /* Reduced font size */
                white-space: nowrap; /* Prevents text from wrapping */
            }

            .choose-options-btn:hover {
                background-color: #f9fafb;
                border-color: #111827;
            }

            @media (max-width: 768px) {
                .modern-product-col {
                    flex: 0 0 50%;
                    max-width: 50%;
                }
                .product-title {
                    font-size: 0.9rem;
                    min-height: 40px;
                }
            }
        </style>

        <a href="{{route('product.details', $product->slug)}}" class="product-image-container">
            <img src="{{asset('uploads/product/'.$product->image)}}" alt="{{$product->title}}">

            @if($product->discount_price > 0)
                <div class="hot-selling-badge">🔥 Hot Selling 🔥</div>
            @endif
        </a>

        <div class="product-content">
            <a href="{{route('product.details', $product->slug)}}" class="product-title-link">
                <h5 class="product-title">{{$product->title}}</h5>
            </a>
            
            <p class="product-vendor">TRENDIEZZS</p>

            @if($product->reviews->count() > 0)
                @php
                    // Calculate the average rating and round it
                    $rating = round($product->reviews->avg('rating'));
                @endphp
                <div class="product-rating">
                    <div class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $rating)
                                <span>&#9733;</span> @else
                                <span>&#9734;</span> @endif
                        @endfor
                    </div>
                    <span class="review-count">({{$product->reviews->count()}})</span>
                </div>
            @endif
            <div class="price-container">
                @if($product->discount_price > 0)
                    <span class="original-price">Tk {{number_format($product->regular_price, 2)}} BDT</span>
                    <span class="current-price">Tk {{number_format($product->discount_price, 2)}} BDT</span>
                @else
                    <span class="current-price">Tk {{number_format($product->regular_price, 2)}} BDT</span>
                @endif
            </div>

            <a href="{{route('product.details', $product->slug)}}" class="choose-options-btn">Choose options</a>
        </div>
    </div>
</div>