<?php if(!isset($classes)){$classes='col-lg-3 col-md-3 col-sm-4 col-4';}?>

<div class="product {{$classes}} pxc">
    <?php 
       $typeid=$product->slug;
    ?>

    <div class="modern-product-card {{ 
        (isset($product->category) && (
            str_contains(strtolower($product->category->name ?? ''), 'book') || 
            str_contains(strtolower($product->category->name ?? ''), 'বই') ||
            str_contains(strtolower($product->category->name ?? ''), 'kitab')
        )) || 
        str_contains(strtolower($product->title), 'book') || 
        str_contains(strtolower($product->title), 'বই') 
        ? 'book-product' : '' }}">
        <style>
            .modern-product-card {
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                overflow: hidden;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                height: auto;
                min-height: 340px;
                display: flex;
                flex-direction: column;
                position: relative;
                border: 1px solid rgba(0, 0, 0, 0.05);
            }

            .modern-product-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            }

            .product-image-container {
                position: relative;
                overflow: hidden;
                height: 200px;
                background: #f8fafc;
            }

            .product-image-container img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                transition: transform 0.3s ease;
                background: white;
            }

            /* For book products, use contain to show full cover */
            .modern-product-card.book-product .product-image-container img {
                object-fit: contain;
                padding: 8px;
            }

            /* For non-book products, use cover for better appearance */
            .modern-product-card:not(.book-product) .product-image-container img {
                object-fit: cover;
                padding: 0;
            }

            .modern-product-card:hover .product-image-container img {
                transform: scale(1.05);
            }

            .discount-badge {
                position: absolute;
                top: 12px;
                left: 12px;
                background: linear-gradient(135deg, #ff6b6b, #ee5a24);
                color: white;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                z-index: 2;
                box-shadow: 0 2px 8px rgba(238, 90, 36, 0.3);
            }

            .wishlist-btn {
                position: absolute;
                top: 12px;
                right: 12px;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                border: none;
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                z-index: 2;
            }

            .wishlist-btn:hover {
                background: white;
                transform: scale(1.1);
            }

            .wishlist-btn i {
                font-size: 16px;
                transition: color 0.3s ease;
            }

            .product-content {
                padding: 14px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .rating-container {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 6px;
            }

            .star-rating {
                display: flex;
                gap: 2px;
            }

            .star-rating i {
                font-size: 14px;
                color: #fbbf24;
            }

            .star-rating .far {
                color: #e5e7eb;
            }

            .product-title {
                font-size: 0.95rem;
                font-weight: 600;
                color: #1f2937;
                line-height: 1.4;
                margin: 6px 0 8px 0;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                min-height: 2.8rem;
            }

            .product-title:hover {
                color: var(--primary_color, #3b82f6);
            }

            .price-container {
                margin-bottom: 8px;
            }

            .current-price {
                font-size: 1.25rem;
                font-weight: 700;
                color: var(--primary_color, #3b82f6);
            }

            .original-price {
                font-size: 0.9rem;
                color: #9ca3af;
                text-decoration: line-through;
                margin-left: 8px;
            }

            .product-message {
                font-size: 0.8rem;
                color: #6b7280;
                margin-bottom: 6px;
                min-height: 1rem;
            }

            .action-buttons {
                display: flex;
                gap: 8px;
                margin-top: auto;
                min-height: 0;
            }
            
            .action-buttons:empty {
                display: none;
            }

            .btn-primary {
                flex: 1;
                background: var(--primary_color, #3b82f6);
                color: white;
                border: none;
                padding: 12px 16px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 0.875rem;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .btn-primary:hover {
                background: var(--primary_color_dark, #2563eb);
                transform: translateY(-1px);
            }

            .btn-secondary {
                background: #f3f4f6;
                color: #6b7280;
                border: none;
                padding: 12px 16px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 0.875rem;
                cursor: pointer;
                transition: all 0.3s ease;
                white-space: nowrap;
            }

            .btn-secondary:hover {
                background: #e5e7eb;
                color: #374151;
            }

            .btn-out-of-stock {
                background: #ef4444;
                color: white;
            }

            .btn-out-of-stock:hover {
                background: #dc2626;
            }

            .quick-view-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
                color: white;
                padding: 20px;
                transform: translateY(100%);
                transition: transform 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .modern-product-card:hover .quick-view-overlay {
                transform: translateY(0);
            }

            .quick-view-btn {
                color: white;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.9rem;
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(10px);
                transition: all 0.3s ease;
            }

            .quick-view-btn:hover {
                background: rgba(255, 255, 255, 0.3);
                color: white;
                text-decoration: none;
            }

            /* Book-specific styling for better cover display */
            .modern-product-card.book-product .product-image-container {
                height: 220px;
            }

            .modern-product-card.book-product {
                min-height: 360px;
            }

            @media (max-width: 768px) {
                .modern-product-card {
                    height: auto;
                    min-height: 300px;
                }
                
                .modern-product-card.book-product {
                    min-height: 320px;
                }
                
                .modern-product-card.book-product .product-image-container {
                    height: 180px;
                }
                
                .product-image-container {
                    height: 160px;
                }
                
                .product-image-container img {
                    object-fit: contain;
                    padding: 4px;
                }
                
                .modern-product-card:not(.book-product) .product-image-container img {
                    object-fit: contain;
                    padding: 4px;
                }
                
                .product-content {
                    padding: 10px;
                }
                
                .product-title {
                    font-size: 0.85rem;
                    margin: 4px 0 6px 0;
                    min-height: 2.2rem;
                    -webkit-line-clamp: 2;
                }
                
                .current-price {
                    font-size: 1.1rem;
                }
                
                .action-buttons {
                    flex-direction: column;
                    margin-top: 6px;
                }
                
                .discount-badge {
                    top: 8px;
                    left: 8px;
                    padding: 4px 8px;
                    font-size: 0.7rem;
                }
                
                .wishlist-btn {
                    top: 8px;
                    right: 8px;
                    width: 32px;
                    height: 32px;
                }
                
                .wishlist-btn i {
                    font-size: 14px;
                }
            }
        </style>

        <!-- Product Image -->
        <div class="product-image-container">
            <a href="{{route('product.details', $product->slug)}}">
                <img src="{{asset('uploads/product/'.$product->image)}}" alt="{{$product->title}}">
            </a>

            <!-- Discount Badge -->
            @if($product->discount_price > 0)
                @php
                    if($product->dis_type == '2') {
                        $discount_price = round((($product->regular_price - $product->discount_price) / ($product->regular_price)) * 100) . '%';
                    } else {
                        $currency_icon = (setting('CURRENCY_ICON')) ? setting('CURRENCY_ICON') : '৳';
                        $discount_price = $currency_icon . ($product->regular_price - $product->discount_price);
                    }
                @endphp
                <div class="discount-badge">{{$discount_price}} OFF</div>
            @endif

            <!-- Wishlist Button -->
            @php
                $hw = App\Models\wishlist::where('product_id', $product->id)->where('user_id', auth()->id())->first();
                $wishlist_color = $hw ? '#ef4444' : '#6b7280';
            @endphp
            <form action="{{route('wishlist.add')}}" method="post" id="submit_payment_form{{$typeid}}">
                @csrf
                <input type="hidden" name="product_id" value="{{$product->slug}}">
                <button type="submit" class="wishlist-btn" title="Add to Wishlist">
                    <i class="{{$hw ? 'fas' : 'far'}} fa-heart" style="color: {{$wishlist_color}}"></i>
                </button>
            </form>

            <!-- Quick View Overlay -->
            <div class="quick-view-overlay">
                <a href="{{route('product.details', $product->slug)}}" class="quick-view-btn">
                    <i class="fas fa-search"></i>
                    Quick View
                </a>
            </div>
        </div>

        <!-- Product Content -->
        <div class="product-content">
            <!-- Rating -->
            <div class="rating-container">
                @php
                    if ($product->reviews->count() > 0) {
                        $average_rating = $product->reviews->sum('rating') / $product->reviews->count();
                    } else {
                        $average_rating = 0;
                    }
                @endphp
                <div class="star-rating">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($average_rating >= $i)
                            <i class="fas fa-star"></i>
                        @elseif ($average_rating >= $i - 0.5)
                            <i class="fas fa-star-half-alt"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                </div>
            </div>

            <!-- Product Title -->
            <a href="{{route('product.details', $product->slug)}}" style="text-decoration: none;">
                <h5 class="product-title">{{implode(' ', array_slice(explode(' ', $product->title), 0, 10))}}...</h5>
            </a>

            <!-- Price -->
            <div class="price-container">
                @if($product->discount_price > 0 || $product->price)
                    <span class="current-price">{{ setting('CURRENCY_ICON') ?? '৳' }}{{$product->price ?? $product->discount_price}}</span>
                    <span class="original-price">{{ setting('CURRENCY_ICON') ?? '৳' }}{{$product->regular_price}}</span>
                @else
                    <span class="current-price">{{ setting('CURRENCY_ICON') ?? '৳' }}{{$product->regular_price}}</span>
                @endif
            </div>

            <!-- Product Message -->
            <div class="product-message">
                @if ($product->prdct_extra_msg)
                    <small>{{ $product->prdct_extra_msg }}</small>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                @if($product->quantity <= '0')
                    <a href="{{route('product.details', $product->slug)}}" class="btn-primary btn-out-of-stock">
                        <i class="fas fa-clock"></i>
                        Pre Order
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    // Wishlist form submit 
    $(document).on('submit', '#submit_payment_form{{$typeid}}', function(e) {
        e.preventDefault();
        
        let action = $(this).attr('action');
        var formData = $(this).serialize();
        
        $.ajax({
            type: 'POST',
            url: action,
            data: formData,
            dataType: "JSON",
            beforeSend: function() {
                loader(true);
            },
            success: function (response) {
                responseMessage(response.alert, response.message, response.alert.toLowerCase());
                
                // Update wishlist button appearance
                const button = $('#submit_payment_form{{$typeid}} .wishlist-btn i');
                if (response.action === 'added') {
                    button.removeClass('far').addClass('fas').css('color', '#ef4444');
                } else {
                    button.removeClass('fas').addClass('far').css('color', '#6b7280');
                }
            },
            complete: function() {
                loader(false);
            },
            error: function (xhr) {
                if (xhr.status == 422) {
                    if (typeof(xhr.responseJSON.errors) !== 'undefined') {
                        $.each(xhr.responseJSON.errors, function (key, error) { 
                            $('small.' + key + '').text(error);
                            $('#' + key + '').addClass('is-invalid');
                        });
                        responseMessage('Error', xhr.responseJSON.message, 'error');
                    }
                } else if (xhr.status == 401) {
                    alert('Please login to continue');
                    window.location = '/login';
                } else {
                    responseMessage(xhr.status, xhr.statusText, 'error');
                }
            }
        });
    });

    // Response message handler
    function responseMessage(heading, message, icon) {
        $.toast({
            heading: heading,
            text: message,
            icon: icon,
            position: 'top-right',
            stack: false
        });
    }

    // Loader handler
    function loader(status) {
        if (status == true) {
            $('#loading-image').removeClass('d-none').addClass('d-block');
        } else {
            $('#loading-image').addClass('d-none').removeClass('d-block');
        }
    }
</script>
@endpush