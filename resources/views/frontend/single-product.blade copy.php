@extends('layouts.frontend.app')

@push('meta')
    <meta name='description' content="{{ $product->title }}" />
    <meta property="og:image" content="{{ asset('uploads/product/' . $product->image) }}" />
    {{-- Using pluck and implode is safer for keywords output --}}
    <meta name='keywords' content="{{ $product->tags->pluck('name')->implode(', ') }}" />
@endpush

@section('title', $product->title)

@push('css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #10b981;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f3f4f6;
            --border-color: #e5e7eb;
        }

        body { font-family: 'Inter', sans-serif; color: var(--dark-color); background: #fafafa; }
        .product-page-wrapper { padding: 30px 0; }
        .product-main-card { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); overflow: hidden; margin-bottom: 30px; }
        .product-gallery { position: relative; background: white; padding: 20px; }
        .main-image-container { position: relative; width: 100%; height: 500px; border-radius: 8px; overflow: hidden; background: #f9fafb; display: flex; align-items: center; justify-content: center; }
        .main-image { max-width: 100%; max-height: 100%; object-fit: contain; transition: transform 0.3s ease; }
        .main-image:hover { transform: scale(1.05); }
        .image-thumbnails { display: flex; gap: 10px; margin-top: 15px; overflow-x: auto; padding: 10px 0; }
        .thumbnail-item { flex: 0 0 80px; height: 80px; border-radius: 8px; overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease; }
        .thumbnail-item:hover, .thumbnail-item.active { border-color: var(--primary-color); transform: translateY(-2px); }
        .thumbnail-item img { width: 100%; height: 100%; object-fit: cover; }
        .product-info { padding: 30px; }
        .product-title { 
            font-size: 32px; /* UPDATED from 28px to 32px */
            font-weight: 700; 
            color: var(--dark-color); 
            margin-bottom: 15px; 
            line-height: 1.3; 
        }
        .product-meta { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color); }
        .meta-item { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #6b7280; }
        .meta-item i { color: var(--primary-color); }
        .meta-item a { color: var(--primary-color); text-decoration: none; }
        .meta-item a:hover { text-decoration: underline; }
        .price-section { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 20px; border-radius: 10px; margin-bottom: 25px; }
        .price-display { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
        .current-price { font-size: 36px; font-weight: 800; color: var(--danger-color); }
        .original-price { font-size: 20px; color: #f9a200ff; text-decoration: line-through; }
        .discount-badge { background: var(--danger-color); color: white; padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .stock-status { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .stock-status.in-stock { background: #d1fae5; color: #065f46; }
        .stock-status.low-stock { background: #fef3c7; color: #92400e; }
        .stock-status.out-of-stock { background: #fee2e2; color: #991b1b; }
        .color-selection, .size-selection, .attribute-selection { margin-bottom: 25px; }
        .section-title { font-size: 16px; font-weight: 600; margin-bottom: 12px; color: var(--dark-color); }
        .color-options { display: flex; flex-wrap: wrap; gap: 12px; }
        .color-option { position: relative; cursor: pointer; border: 3px solid transparent; border-radius: 8px; overflow: hidden; transition: all 0.3s ease; }
        .color-option:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); }
        .color-option.active { border-color: var(--primary-color); box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
        .color-option.disabled { opacity: 0.5; cursor: not-allowed; pointer-events: none; }
        .color-option input { display: none; }
        .color-preview { width: 90px; height: 90px; object-fit: cover; display: block; }
        .color-swatch { width: 90px; height: 90px; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
        .color-name { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0, 0, 0, 0.7); color: white; padding: 4px 8px; font-size: 11px; text-align: center; }
        .attribute-options { display: flex; flex-wrap: wrap; gap: 10px; }
        .attribute-option { position: relative; }
        .attribute-option input { display: none; }
        .attribute-option label { display: block; padding: 10px 20px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease; font-weight: 500; background: white; }
        .attribute-option input:checked + label { background: var(--primary-color); color: white; border-color: var(--primary-color); }
        .attribute-option input:disabled + label {
            opacity: 0.7;
            cursor: not-allowed;
            background: #f9fafb;
            color: #9ca3af;
            text-decoration: line-through;
        }
        .attribute-option label:hover:not(.disabled) { border-color: var(--primary-color); transform: translateY(-2px); }
        .attribute-stock-info { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .quantity-selector { margin-bottom: 25px; }
        .quantity-controls { display: inline-flex; align-items: center; border: 2px solid var(--border-color); border-radius: 8px; overflow: hidden; }
        .qty-btn { background: white; border: none; width: 40px; height: 40px; cursor: pointer; font-size: 18px; font-weight: bold; color: var(--dark-color); transition: background 0.3s; }
        .qty-btn:hover { background: var(--light-color); }
        .qty-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .qty-input { width: 60px; height: 40px; border: none; text-align: center; font-weight: 600; font-size: 16px; }
        .action-buttons { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
        .btn-primary, .btn-secondary { flex: 1; min-width: 200px; padding: 14px 24px; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-primary { background: var(--primary-color); color: white; border: none; }
        .btn-primary:hover:not(:disabled) { background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
        .btn-secondary { background: white; color: var(--primary-color); border: 2px solid var(--primary-color); }
        .btn-secondary:hover:not(:disabled) { background: var(--primary-color); color: white; transform: translateY(-2px); }
        .btn-secondary:disabled { opacity: 0.6; cursor: not-allowed; }
        .secondary-actions { display: flex; gap: 10px; margin-bottom: 20px; }
        .icon-btn { padding: 10px 16px; background: white; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px; text-decoration: none; color: var(--dark-color); }
        .icon-btn:hover { border-color: var(--primary-color); color: var(--primary-color); transform: translateY(-2px); }
        .product-details-section { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); margin-top: 30px; overflow: hidden; }
        .tabs-navigation { display: flex; border-bottom: 2px solid var(--border-color); overflow-x: auto; }
        .tab-btn { padding: 18px 24px; background: none; border: none; border-bottom: 3px solid transparent; cursor: pointer; font-weight: 600; color: #6b7280; transition: all 0.3s ease; white-space: nowrap; }
        .tab-btn:hover, .tab-btn.active { color: var(--primary-color); border-bottom-color: var(--primary-color); background: #f9fafb; }
        .tab-content { padding: 30px; }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }
        .review-item { padding: 20px; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 15px; }
        .review-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .review-avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
        .reviewer-name { font-weight: 600; margin-bottom: 4px; }
        .review-date { font-size: 13px; color: #6b7280; }
        .notification { position: fixed; top: 20px; right: 20px; background: white; padding: 16px 20px; border-radius: 8px; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); z-index: 9999; min-width: 300px; display: none; }
        .notification.show { display: block; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateX(400px); opacity: 0); } to { transform: translateX(0); opacity: 1); } }
        .notification.success { border-left: 4px solid var(--secondary-color); }
        .notification.error { border-left: 4px solid var(--danger-color); }
        @media (max-width: 768px) {
            .product-title { font-size: 26px; } /* UPDATED from 22px to 26px for mobile responsiveness */
            .current-price { font-size: 28px; }
            .main-image-container { height: 350px; }
            .action-buttons { flex-direction: column; }
            .btn-primary, .btn-secondary { min-width: 100%; }
        }
        /* Related Products Section */
        .related-products-section { 
            margin-top: 60px; 
            padding: 40px 0 20px 0;
            background: #fafafa;
            border-top: 1px solid var(--border-color);
        }
        .section-header { 
            text-align: center; 
            margin-bottom: 40px; 
        }
        .section-title { 
            font-size: 16px; 
            font-weight: 700; 
            color: var(--dark-color); 
            margin-bottom: 10px; 
        }
        .section-subtitle { 
            font-size: 12px; 
            color: #6b7280; 
        }
        @media (max-width: 768px) {
            .section-title { font-size: 24px; }
            .related-products-section { margin-top: 40px; padding: 30px 0 15px 0; }
        }
    </style>
@endpush

@section('content')
<div class="product-page-wrapper">
    <div class="container">
        <div id="notification" class="notification"></div>

        <div class="product-main-card">
            <div class="row g-0">
                <div class="col-md-5">
                    <div class="product-gallery">
                        <div class="main-image-container">
                            @if($product->image)
                                <img src="{{ asset('uploads/product/' . $product->image) }}" alt="{{ $product->title }}" class="main-image" id="mainImage">
                            @else
                                <div style="text-align: center; color: #9ca3af;">
                                    <i class="fas fa-image" style="font-size: 64px;"></i>
                                    <p>No Image Available</p>
                                </div>
                            @endif
                        </div>

                        <div class="image-thumbnails" id="thumbnailsContainer">
                            @if($product->image)
                                <div class="thumbnail-item active" data-image="{{ asset('uploads/product/' . $product->image) }}">
                                    <img src="{{ asset('uploads/product/' . $product->image) }}" alt="Main">
                                </div>
                            @endif

                            @foreach($product->getGeneralImages() as $image)
                                <div class="thumbnail-item" data-image="{{ asset('uploads/product/' . $image->name) }}">
                                    <img src="{{ asset('uploads/product/' . $image->name) }}" alt="Gallery">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="product-info">
                        <h1 class="product-title">{{ $product->title }}</h1>

                        <div class="product-meta">
                            @if($product->brand)
                                <div class="meta-item">
                                    <i class="fas fa-tag"></i>
                                    <span>Brand: <a href="{{ route('brand.product', $product->brand->slug) }}">{{ $product->brand->name }}</a></span>
                                </div>
                            @endif
                            @if($product->sku)
                                <div class="meta-item">
                                    <i class="fas fa-barcode"></i>
                                    <span>SKU: {{ $product->sku }}</span>
                                </div>
                            @endif
                            <div class="meta-item">
                                <i class="fas fa-eye"></i>
                                <span>{{ $product->reach ?? 0 }} views</span>
                            </div>
                        </div>

                        <div class="price-section">
                            <div class="price-display">
                                @php
                                    $basePrice = $product->discount_price > 0 ? $product->discount_price : $product->regular_price;
                                @endphp
                                
                                @if($product->discount_price > 0)
                                    {{-- FIXED: Corrupted currency symbol 'à§³' replaced with Taka symbol '৳' (or its HTML entity) --}}
                                    <span class="current-price">৳<span id="dynamicPrice">{{ number_format($product->discount_price, 0) }}</span></span>
                                    <span class="original-price">৳{{ number_format($product->regular_price, 0) }}</span>
                                    @php
                                        $discount_percentage = round((($product->regular_price - $product->discount_price) / $product->regular_price) * 100);
                                    @endphp
                                    <span class="discount-badge">-{{ $discount_percentage }}%</span>
                                @else
                                    {{-- FIXED: Corrupted currency symbol 'à§³' replaced with Taka symbol '৳' (or its HTML entity) --}}
                                    <span class="current-price">৳<span id="dynamicPrice">{{ number_format($product->regular_price, 0) }}</span></span>
                                @endif
                            </div>
                        </div>

                        @php $totalStock = $product->getTotalStockAttribute(); @endphp

                        @if($totalStock > 10)
                            <div class="stock-status in-stock" id="stockStatus">
                                <i class="fas fa-check-circle"></i>
                                <span id="stockText">In Stock</span>
                            </div>
                        @elseif($totalStock > 0)
                            <div class="stock-status low-stock" id="stockStatus">
                                <i class="fas fa-exclamation-circle"></i>
                                <span id="stockText">Low Stock</span>
                            </div>
                        @else
                            <div class="stock-status out-of-stock" id="stockStatus">
                                <i class="fas fa-times-circle"></i>
                                <span id="stockText">Out of Stock</span>
                            </div>
                        @endif

                        @if($product->short_description)
                            <div class="mb-4">
                                {!! Str::limit($product->short_description, 200) !!}
                            </div>
                        @endif

                        <form id="productForm">
                            @csrf
                            <input type="hidden" name="id" value="{{ $product->id }}">
                            <input type="hidden" name="qty" id="quantity" value="1">
                            <input type="hidden" name="color" id="selectedColor" value="">
                            <input type="hidden" name="size" id="selectedSize" value="">
                            <input type="hidden" name="dynamic_price" id="hiddenDynamicPrice" value="{{ $basePrice }}">

                            {{-- 1. Color and Size Selection --}}
                            @if(!empty($variations['color_size']))
                                <div class="color-selection">
                                    <div class="section-title">
                                        <i class="fas fa-palette"></i> Select Color: <span id="selectedColorName" class="font-weight-bold"></span>
                                    </div>
                                    <div class="color-options">
                                        @foreach(collect($variations['color_size'])->values() as $colorData)
                                            @php
                                                $isOutOfStock = $colorData['total_stock'] <= 0;
                                                $firstImage = $colorData['images']->first();
                                            @endphp
                                            <div class="color-option {{ $isOutOfStock ? 'disabled' : '' }}" 
                                                data-color-id="{{ $colorData['color_id'] }}"
                                                data-color-name="{{ $colorData['color_name'] }}"
                                                data-color-images="{{ json_encode($colorData['images']->pluck('name')) }}">
                                                <input type="radio" name="color_radio" id="color_{{ $colorData['color_id'] }}" value="{{ $colorData['color_id'] }}" {{ $isOutOfStock ? 'disabled' : '' }}>
                                                
                                                @if($firstImage)
                                                    <img src="{{ asset('uploads/product/' . $firstImage->name) }}" alt="{{ $colorData['color_name'] }}" class="color-preview">
                                                @else
                                                    <div class="color-swatch" style="background-color: {{ $colorData['color_code'] }}"></div>
                                                @endif
                                                <span class="color-name">{{ $colorData['color_name'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- The size selection is hidden initially and populated by JS after color selection --}}
                                <div class="size-selection" style="display:none;">
                                    <div class="section-title">
                                        <i class="fas fa-ruler-horizontal"></i> Select Size: <span id="selectedSizeName" class="font-weight-bold"></span>
                                    </div>
                                    <div class="attribute-options" id="sizeOptionsContainer">
                                        {{-- Size options populated by JS --}}
                                    </div>
                                </div>

                            {{-- 2. Size-Only Selection --}}
                            @elseif(!empty($variations['size_only']))
                                <div class="size-selection">
                                    <div class="section-title">
                                        <i class="fas fa-ruler-horizontal"></i> Select Size: <span id="selectedSizeName" class="font-weight-bold"></span>
                                    </div>
                                    <div class="attribute-options" id="sizeOnlyOptionsContainer">
                                        @foreach($variations['size_only'] as $sizeValue)
                                            @php $isSizeOutOfStock = $sizeValue['stock'] <= 0; @endphp
                                            <div class="attribute-option">
                                                <input type="radio" 
                                                    name="size_radio" {{-- Reusing 'size_radio' name for unified JS handling --}}
                                                    id="size_only_{{ $sizeValue['id'] }}" 
                                                    value="{{ $sizeValue['id'] }}" 
                                                    data-size-name="{{ $sizeValue['name'] }}"
                                                    data-stock="{{ $sizeValue['stock'] }}" 
                                                    data-price="{{ $sizeValue['price'] }}" 
                                                    {{ $isSizeOutOfStock ? 'disabled' : '' }}>
                                                <label for="size_only_{{ $sizeValue['id'] }}" class="{{ $isSizeOutOfStock ? 'disabled' : '' }}">
                                                    {{ $sizeValue['name'] }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            {{-- 3. Generic Attribute Selection --}}
                            @elseif(!empty($variations['attributes']))
                                @php
                                    $groupedAttributes = collect($variations['attributes'])->groupBy('attribute_name');
                                @endphp
                                @foreach($groupedAttributes as $attributeName => $attributeValues)
                                    <div class="attribute-selection">
                                        <div class="section-title">
                                            <i class="fas fa-list"></i> Select {{ $attributeName }}: <span id="selectedAttrName" class="font-weight-bold"></span>
                                        </div>
                                        <div class="attribute-options">
                                            @foreach($attributeValues as $attrValue)
                                                @php $isAttrOutOfStock = $attrValue['stock'] <= 0; @endphp
                                                <div class="attribute-option">
                                                    <input type="radio" 
                                                        name="attribute_radio" 
                                                        id="attr_{{ $attrValue['id'] }}" 
                                                        value="{{ $attrValue['id'] }}" 
                                                        data-attr-name="{{ $attrValue['name'] }}"
                                                        data-attr-price="{{ $attrValue['price'] }}" 
                                                        data-attr-stock="{{ $attrValue['stock'] }}" 
                                                        {{ $isAttrOutOfStock ? 'disabled' : '' }}>
                                                    <label for="attr_{{ $attrValue['id'] }}" class="{{ $isAttrOutOfStock ? 'disabled' : '' }}">
                                                        {{ $attrValue['name'] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if($totalStock > 0)
                                <div class="quantity-selector">
                                    <div class="section-title">Quantity:</div>
                                    <div class="quantity-controls">
                                        {{-- FIXED: Corrupted minus sign 'âˆ’' replaced with '&minus;' --}}
                                        <button type="button" class="qty-btn" id="decreaseQty">&minus;</button>
                                        <input type="number" class="qty-input" id="qtyInput" value="1" min="1" readonly>
                                        <button type="button" class="qty-btn" id="increaseQty">+</button>
                                    </div>
                                    <small class="text-muted ms-2">Available: <span id="availableStock">{{ $totalStock }}</span></small>
                                </div>

                                <div class="action-buttons">
                                    <button type="button" class="btn-primary" id="addToCartBtn" disabled>
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                    <button type="button" class="btn-secondary" id="buyNowBtn" disabled>
                                        <i class="fas fa-bolt"></i> Buy Now
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-danger mt-4">
                                    <i class="fas fa-exclamation-triangle"></i> This product is currently out of stock
                                </div>
                            @endif
                        </form>

                        <div class="secondary-actions">
                            @auth
                                @if(App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists())
                                    @php $wishlist = App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->first(); @endphp
                                    <a href="{{ route('wishlist.remove', $wishlist->id) }}" class="icon-btn">
                                        <i class="fas fa-heart-broken"></i> Remove from Wishlist
                                    </a>
                                @else
                                    <form action="{{ route('wishlist.add') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->slug }}">
                                        <button type="submit" class="icon-btn">
                                            <i class="far fa-heart"></i> Add to Wishlist
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>

                        @if($product->shipping_charge == 0)
                            <div class="mt-4">
                                <div class="alert alert-info">
                                    <i class="fas fa-truck"></i> Free shipping available
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="product-details-section">
            <div class="tabs-navigation">
                <button class="tab-btn active" data-tab="description">Description</button>
                <button class="tab-btn" data-tab="specifications">Specifications</button>
                <button class="tab-btn" data-tab="reviews">Reviews ({{ $product->reviews->count() }})</button>
                <button class="tab-btn" data-tab="questions">Questions ({{ $product->comments->whereNull('parent_id')->count() }})</button>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="description">
                    {!! $product->full_description !!}
                </div>

                <div class="tab-pane" id="specifications">
                    <table class="table">
                        <tbody>
                            <tr><th width="200">Product Name</th><td>{{ $product->title }}</td></tr>
                            @if($product->brand)<tr><th>Brand</th><td>{{ $product->brand->name }}</td></tr>@endif
                            @if($product->sku)<tr><th>SKU</th><td>{{ $product->sku }}</td></tr>@endif
                            <tr><th>Categories</th><td>@foreach($product->categories as $category)<span class="badge bg-secondary">{{ $category->name }}</span> @endforeach</td></tr>
                            @if(!empty($variations['color_size']))<tr><th>Available Colors</th><td>@foreach(collect($variations['color_size'])->values() as $c)<span class="badge" style="background-color: {{ $c['color_code'] }}; color: white; margin-right: 5px;">{{ $c['color_name'] }}</span> @endforeach</td></tr>@endif
                            @if($product->hasVariations())<tr><th>Product Type</th><td>Variable Product</td></tr>@endif
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane" id="reviews">
                    @forelse($product->reviews as $review)
                        <div class="review-item">
                            <div class="review-header">
                                <img src="{{ $review->user && $review->user->avatar != 'default.png' ? asset('uploads/admin/' . $review->user->avatar) : asset('default/default.png') }}" alt="User" class="review-avatar">
                                <div>
                                    <div class="reviewer-name">{{ $review->user ? $review->user->name : 'Anonymous' }}</div>
                                    <div class="review-date">{{ $review->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="mb-2">
                                @for($i = 1; $i <= 5; $i++)<i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>@endfor
                            </div>
                            <p>{{ $review->body }}</p>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="far fa-comment-dots" style="font-size: 48px; color: #d1d5db;"></i>
                            <p class="mt-3 text-muted">No reviews yet.</p>
                        </div>
                    @endforelse
                </div>

                <div class="tab-pane" id="questions">
                    @forelse($product->comments->whereNull('parent_id') as $comment)
                        <div class="review-item">
                            <div class="review-header">
                                <img src="{{ $comment->user && $comment->user->avatar != 'default.png' ? asset('uploads/admin/' . $comment->user->avatar) : asset('default/default.png') }}" alt="User" class="review-avatar">
                                <div>
                                    <div class="reviewer-name">{{ $comment->user ? $comment->user->name : 'Anonymous' }}</div>
                                    <div class="review-date">{{ $comment->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <p>{{ $comment->body }}</p>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="far fa-question-circle" style="font-size: 48px; color: #d1d5db;"></i>
                            <p class="mt-3 text-muted">No questions yet.</p>
                        </div>
                    @endforelse

                    @auth
                        <div class="mt-4">
                            <h5>Ask a Question</h5>
                            <form action="{{ route('comment', $product->slug) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <textarea name="comment" class="form-control" rows="4" placeholder="Type your question here..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Question</button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        {{-- You May Also Like Section --}}
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="related-products-section">
            <div class="section-header">
                <h2 class="section-title">You May Also Like</h2>
                <p class="section-subtitle">Similar products from the same category</p>
            </div>
            <div class="row" style="margin: 0 -8px;">
                @foreach($relatedProducts as $product)
                    @include('components.product-grid-view', ['product' => $product, 'classes' => 'col-lg-3 col-md-4 col-6'])
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // --- CONFIGURATION & STATE ---
    const config = {
        basePrice: parseFloat('{{ $product->discount_price > 0 ? $product->discount_price : $product->regular_price }}'),
        variations: @json($variations ?? ['color_size' => [], 'size_only' => [], 'attributes' => []]),
        allSizes: @json($allSizes ?? []),
        hasColorSize: {{ !empty($variations['color_size']) ? 'true' : 'false' }},
        hasAttributes: {{ !empty($variations['attributes']) && empty($variations['color_size']) ? 'true' : 'false' }},
        hasSizeOnly: {{ !empty($variations['size_only']) && empty($variations['color_size']) && empty($variations['attributes']) ? 'true' : 'false' }},
        totalInitialStock: {{ $totalStock }}
    };

    let selection = {
        color: null,
        size: null,
        attribute: null
    };

    // --- INITIALIZATION ---
    function initialize() {
        if (config.hasColorSize) {
            $('.color-option:not(.disabled):first').trigger('click');
        } else if (config.hasSizeOnly) {
            $('#sizeOnlyOptionsContainer input[type="radio"]:not(:disabled):first').prop('checked', true).trigger('change');
        } else if (config.hasAttributes) {
            $('.attribute-option input[type="radio"]:not(:disabled):first').prop('checked', true).trigger('change');
        } else {
            updateUI();
        }
    }

    // --- EVENT HANDLERS ---
    $(document).on('click', '.thumbnail-item', function() {
        $('.thumbnail-item').removeClass('active');
        $(this).addClass('active');
        $('#mainImage').attr('src', $(this).data('image'));
    });

    $('.color-option').on('click', function() {
        if ($(this).hasClass('disabled')) return;
        
        const colorId = $(this).data('color-id');
        let colorImages;
        try {
            colorImages = JSON.parse($(this).attr('data-color-images'));
        } catch (e) {
            console.error("Failed to parse color images JSON:", e);
            colorImages = [];
        }

        if (selection.color?.id === colorId) {
            selection.size = null; 
            $('#selectedSize').val('');
            $('#selectedSizeName').text('');
            populateSizeOptions(colorId);
            updateUI(); 
            return; 
        }

        selection.color = {
            id: colorId,
            name: $(this).data('color-name'),
            images: colorImages
        };
        selection.size = null;

        $('.color-option').removeClass('active');
        $(this).addClass('active');
        $('#selectedColor').val(selection.color.id);  // Store color ID, not slug
        $('#selectedSize').val('');
        $('#selectedColorName').text(selection.color.name);
        $('#selectedSizeName').text('');

        updateImageGallery(selection.color.images);
        populateSizeOptions(selection.color.id);
        updateUI();
    });

    $(document).on('change', 'input[name="size_radio"]', function() {
        if ($(this).is(':disabled')) return;

        selection.size = {
            id: $(this).val(),
            name: $(this).data('size-name'),
            stock: parseInt($(this).data('stock')),
            price: parseFloat($(this).data('price'))
        };
        
        $('#selectedSize').val(selection.size.id);
        $('#selectedSizeName').text(selection.size.name);
        
        if (config.hasSizeOnly) {
            selection.color = null;
            selection.attribute = null;
        }
        
        updateUI();
    });
    
    $('input[name="attribute_radio"]').on('change', function() {
        if ($(this).is(':disabled')) return;

        selection.attribute = {
            id: $(this).val(),
            name: $(this).data('attr-name'),
            stock: parseInt($(this).data('attr-stock')),
            price: parseFloat($(this).data('attr-price'))
        };

        $('#selectedAttrName').text(selection.attribute.name);
        $('#selectedSize').val(selection.attribute.id);
        updateUI();
    });

    // Quantity Handlers
    $('#increaseQty').on('click', function() {
        const input = $('#qtyInput');
        const max = parseInt(input.attr('max'));
        const current = parseInt(input.val());
        if (current < max) {
            input.val(current + 1).trigger('change');
        }
    });

    $('#decreaseQty').on('click', function() {
        const input = $('#qtyInput');
        const current = parseInt(input.val());
        if (current > 1) {
            input.val(current - 1).trigger('change');
        }
    });
    
    $('#qtyInput').on('change', function() {
        $('#quantity').val($(this).val());
    });
    
    // Action Buttons
    $('#addToCartBtn, #buyNowBtn').on('click', function() {
        let isValid = false;
        
        if (config.hasColorSize) {
            if (selection.color && selection.size) isValid = true;
            else showNotification('Error', 'Please select a color and size.', 'error');
        } else if (config.hasSizeOnly) {
            if (selection.size) isValid = true;
            else showNotification('Error', 'Please select a size.', 'error');
        } else if (config.hasAttributes) {
            if (selection.attribute) isValid = true;
            else showNotification('Error', 'Please select an option.', 'error');
        } else {
            isValid = true;
        }

        if(isValid) {
            const isBuyNow = $(this).is('#buyNowBtn');
            handleAction(isBuyNow);
        }
    });

    // --- UI & LOGIC FUNCTIONS ---
    function populateSizeOptions(colorId) {
        const productVariationSizes = config.variations.color_size[colorId]?.sizes || [];
        const sizeMap = new Map(productVariationSizes.map(s => [s.size_id, s]));

        const container = $('#sizeOptionsContainer');
        container.empty();
        
        if (config.allSizes && config.allSizes.length > 0) {
            let html = '';
            config.allSizes.forEach(size => {
                const variationData = sizeMap.get(size.id);
                const stock = variationData ? variationData.stock : 0;
                const price = variationData ? variationData.price : 0;
                const isOutOfStock = stock <= 0;

                html += `
                    <div class="attribute-option">
                        <input type="radio" name="size_radio" id="size_${size.id}" value="${size.id}" 
                            data-size-name="${size.name}" data-stock="${stock}" data-price="${price}" 
                            ${isOutOfStock ? 'disabled' : ''}>
                        <label for="size_${size.id}" class="${isOutOfStock ? 'disabled' : ''}">${size.name}</label>
                    </div>`;
            });
            container.html(html);
            $('.size-selection').slideDown();

            const previouslySelected = container.find(`input[value="${selection.size?.id}"]`);
            const firstAvailable = container.find('input[type="radio"]:not(:disabled):first');
            
            if (previouslySelected.length && !previouslySelected.is(':disabled')) {
                previouslySelected.prop('checked', true).trigger('change');
            } else if (firstAvailable.length) {
                firstAvailable.prop('checked', true).trigger('change');
            } else {
                selection.size = null; 
                $('#selectedSize').val('');
                $('#selectedSizeName').text('');
                updateUI();
            }
        } else {
            $('.size-selection').slideUp();
        }
    }
    
    function updateImageGallery(images) {
        let thumbnailsHtml = '';
        const defaultImage = {
            name: '{{ $product->image }}',
            url: '{{ asset("uploads/product/" . $product->image) }}'
        };
        
        thumbnailsHtml += `<div class="thumbnail-item active" data-image="${defaultImage.url}"><img src="${defaultImage.url}" alt="Main"></div>`;
        $('#mainImage').attr('src', defaultImage.url);

        if (images && images.length > 0) {
            images.forEach((img, i) => {
                const imgUrl = '{{ asset("uploads/product") }}/' + img;
                thumbnailsHtml += `<div class="thumbnail-item" data-image="${imgUrl}"><img src="${imgUrl}" alt="Color Variant"></div>`;
            });
            $('#mainImage').attr('src', '{{ asset("uploads/product") }}/' + images[0]);
        } 
        
        @foreach($product->getGeneralImages() as $image)
            const generalImgUrl = '{{ asset("uploads/product/" . $image->name) }}';
            thumbnailsHtml += `<div class="thumbnail-item" data-image="${generalImgUrl}"><img src="${generalImgUrl}" alt="Gallery"></div>`;
        @endforeach
        
        $('#thumbnailsContainer').html(thumbnailsHtml);
        $('#thumbnailsContainer .thumbnail-item:first').addClass('active');
    }

    function updateUI() {
        let price = config.basePrice;
        let stock = config.totalInitialStock;
        let selectionComplete = false;
        
        if (config.hasColorSize) {
            if (selection.color && selection.size) {
                price = config.basePrice + (selection.size.price || 0);
                stock = selection.size.stock;
                selectionComplete = true;
            } else if (selection.color) {
                stock = config.variations.color_size[selection.color.id]?.total_stock || 0;
            }
        } else if (config.hasSizeOnly) {
            if (selection.size) {
                price = config.basePrice + (selection.size.price || 0);
                stock = selection.size.stock;
                selectionComplete = true;
            }
        } else if (config.hasAttributes) {
            if (selection.attribute) {
                price = config.basePrice + (selection.attribute.price || 0);
                stock = selection.attribute.stock;
                selectionComplete = true;
            }
        } else {
            selectionComplete = true;
        }

        const formattedPrice = price.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        $('#dynamicPrice').text(formattedPrice);
        $('#hiddenDynamicPrice').val(price);

        $('#availableStock').text(stock);
        
        const newMax = stock > 0 ? stock : 1;
        $('#qtyInput').attr('max', newMax);
        
        const currentQty = parseInt($('#qtyInput').val());
        if (currentQty > newMax) {
            $('#qtyInput').val(newMax).trigger('change');
        } else if (currentQty < 1 && newMax >= 1) {
             $('#qtyInput').val(1).trigger('change');
        }

        const stockStatus = $('#stockStatus');
        const stockText = $('#stockText');
        stockStatus.removeClass('in-stock low-stock out-of-stock');
        
        let iconHtml;
        let text;

        if (stock > 10) {
            stockStatus.addClass('in-stock');
            iconHtml = '<i class="fas fa-check-circle"></i> ';
            text = `In Stock ${selectionComplete && (config.hasColorSize || config.hasSizeOnly || config.hasAttributes) ? `(${stock} available)` : ''}`;
        } else if (stock > 0) {
            stockStatus.addClass('low-stock');
            iconHtml = '<i class="fas fa-exclamation-circle"></i> ';
            text = `Low Stock ${selectionComplete && (config.hasColorSize || config.hasSizeOnly || config.hasAttributes) ? `(Only ${stock} left)` : ''}`;
        } else {
            stockStatus.addClass('out-of-stock');
            iconHtml = '<i class="fas fa-times-circle"></i> ';
            text = 'Out of Stock';
        }
        
        // FIXED: The i tag was being added twice, removed the original i tag from HTML and only using JS-generated icon
        stockText.html(iconHtml + text); 

        const canAddToCart = selectionComplete && stock > 0;
        $('#addToCartBtn, #buyNowBtn').prop('disabled', !canAddToCart);
    }

    function handleAction(isBuyNow) {
        const btn = isBuyNow ? $('#buyNowBtn') : $('#addToCartBtn');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        if (isBuyNow) {
            const params = new URLSearchParams($('#productForm').serialize()).toString();
            window.location.href = '{{ route("buy.product") }}?' + params;
        } else {
            $.ajax({
                url: '{{ route("add.cart") }}',
                method: 'POST',
                data: $('#productForm').serialize(),
                success: function(response) {
                    if (response.alert === 'Success') {
                        showNotification('Success!', response.message, 'success');
                        
                        if (response && typeof response.count !== 'undefined') {
                            $('.cart-count-badge').text(response.count);
                        }
                    } else {
                        showNotification(response.alert || 'Warning', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    let message = 'Failed to add to cart';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        message = response.message || message;
                    } catch (e) {
                        // Use default message
                    }
                    showNotification('Error!', message, 'error');
                }
            }).always(function() {
                btn.prop('disabled', false).html(originalText);
                updateUI();
            });
        }
    }
    
    $('.tab-btn').on('click', function() {
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.tab-pane').removeClass('active');
        $('#' + $(this).data('tab')).addClass('active');
    });

    function showNotification(title, message, type) {
        const notification = $('#notification');
        notification.removeClass('success error').addClass(type).html(`<strong>${title}</strong><br>${message}`).addClass('show');
        setTimeout(() => notification.removeClass('show'), 4000);
    }


    // --- RUN ---
    // --- RUN ---
    initialize();
});
</script>
@endpush