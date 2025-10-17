@extends('layouts.frontend.app')

@push('meta')
    <meta name='description' content="{{ $product->title }}" />
    <meta property="og:image" content="{{ asset('uploads/product/' . $product->image) }}" />
    <meta name='keywords' content="@foreach ($product->tags as $tag){{ $tag->name . ', ' }} @endforeach" />
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
        .product-title { font-size: 28px; font-weight: 700; color: var(--dark-color); margin-bottom: 15px; line-height: 1.3; }
        .product-meta { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color); }
        .meta-item { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #6b7280; }
        .meta-item i { color: var(--primary-color); }
        .meta-item a { color: var(--primary-color); text-decoration: none; }
        .meta-item a:hover { text-decoration: underline; }
        .price-section { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 20px; border-radius: 10px; margin-bottom: 25px; }
        .price-display { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
        .current-price { font-size: 36px; font-weight: 800; color: var(--danger-color); }
        .original-price { font-size: 20px; color: #9ca3af; text-decoration: line-through; }
        .discount-badge { background: var(--danger-color); color: white; padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .stock-status { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .stock-status.in-stock { background: #d1fae5; color: #065f46; }
        .stock-status.low-stock { background: #fef3c7; color: #92400e; }
        .stock-status.out-of-stock { background: #fee2e2; color: #991b1b; }
        .color-selection, .attribute-selection { margin-bottom: 25px; }
        .section-title { font-size: 16px; font-weight: 600; margin-bottom: 12px; color: var(--dark-color); }
        .color-options { display: flex; flex-wrap: wrap; gap: 12px; }
        .color-option { position: relative; cursor: pointer; border: 3px solid transparent; border-radius: 8px; overflow: hidden; transition: all 0.3s ease; }
        .color-option:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); }
        .color-option.active { border-color: var(--primary-color); box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
        .color-option.disabled { opacity: 0.5; cursor: not-allowed; pointer-events: none; }
        .color-option input { display: none; }
        .color-preview { width: 90px; height: 90px; object-fit: cover; display: block; }
        .color-swatch { width: 50px; height: 50px; border-radius: 6px; display: block; }
        .color-name { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0, 0, 0, 0.7); color: white; padding: 4px 8px; font-size: 11px; text-align: center; }
        .attribute-options { display: flex; flex-wrap: wrap; gap: 10px; }
        .attribute-option { position: relative; }
        .attribute-option input { display: none; }
        .attribute-option label { display: block; padding: 10px 20px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease; font-weight: 500; background: white; }
        .attribute-option input:checked + label { background: var(--primary-color); color: white; border-color: var(--primary-color); }
        .attribute-option input:disabled + label { opacity: 0.5; cursor: not-allowed; background: #f3f4f6; }
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
        @keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .notification.success { border-left: 4px solid var(--secondary-color); }
        .notification.error { border-left: 4px solid var(--danger-color); }
        @media (max-width: 768px) {
            .product-title { font-size: 22px; }
            .current-price { font-size: 28px; }
            .main-image-container { height: 350px; }
            .action-buttons { flex-direction: column; }
            .btn-primary, .btn-secondary { min-width: 100%; }
        }
    </style>
@endpush

@section('content')
<div class="product-page-wrapper">
    <div class="container">
        <div id="notification" class="notification"></div>

        <div class="product-main-card">
            <div class="row g-0">
                <!-- Image Gallery -->
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

                <!-- Product Information -->
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

                        <!-- Price -->
                        <div class="price-section">
                            <div class="price-display">
                                @php
                                    $basePrice = $product->discount_price > 0 ? $product->discount_price : $product->regular_price;
                                @endphp
                                
                                @if($product->discount_price > 0)
                                    <span class="current-price">৳<span id="dynamicPrice">{{ number_format($product->discount_price, 0) }}</span></span>
                                    <span class="original-price">৳{{ number_format($product->regular_price, 0) }}</span>
                                    @php
                                        $discount_percentage = round((($product->regular_price - $product->discount_price) / $product->regular_price) * 100);
                                    @endphp
                                    <span class="discount-badge">-{{ $discount_percentage }}%</span>
                                @else
                                    <span class="current-price">৳<span id="dynamicPrice">{{ number_format($product->regular_price, 0) }}</span></span>
                                @endif
                            </div>
                        </div>

                        <!-- Stock Status -->
                        @php
                            $totalStock = 0;
                            $isVariableProduct = $product->hasVariations();
                            
                            if($isVariableProduct) {
                                foreach($product->colors as $color) {
                                    $totalStock += $color->pivot->qnty;
                                }
                                foreach($product->attributes_values as $attr) {
                                    $totalStock += $attr->pivot->qnty;
                                }
                            } else {
                                $totalStock = $product->quantity;
                            }
                        @endphp

                        @if($totalStock > 10)
                            <div class="stock-status in-stock" id="stockStatus">
                                <i class="fas fa-check-circle"></i>
                                <span id="stockText">In Stock ({{ $totalStock }} available)</span>
                            </div>
                        @elseif($totalStock > 0)
                            <div class="stock-status low-stock" id="stockStatus">
                                <i class="fas fa-exclamation-circle"></i>
                                <span id="stockText">Low Stock (Only {{ $totalStock }} left)</span>
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
                            <input type="hidden" name="max_stock" id="maxStock" value="{{ $totalStock }}">
                            <input type="hidden" name="is_variable" id="isVariable" value="{{ $isVariableProduct ? '1' : '0' }}">

                            @foreach($attributes as $attribute)
                                <input type="hidden" name="attribute_{{ $attribute->id }}" id="attribute_{{ $attribute->id }}" value="">
                            @endforeach

                            <!-- Color Selection -->
                            @if($product->colors->count() > 0)
                                <div class="color-selection">
                                    <div class="section-title">
                                        <i class="fas fa-palette"></i> Select Color: <span id="selectedColorName"></span>
                                    </div>
                                    <div class="color-options">
                                        @foreach($product->colors as $index => $color)
                                            @php
                                                $colorStock = $color->pivot->qnty;
                                                $isOutOfStock = $colorStock <= 0;
                                            @endphp
                                            
                                            <div class="color-option {{ $index === 0 && !$isOutOfStock ? 'active' : '' }} {{ $isOutOfStock ? 'disabled' : '' }}" 
                                                 data-color-id="{{ $color->id }}"
                                                 data-color-slug="{{ $color->slug }}"
                                                 data-color-name="{{ $color->name }}"
                                                 data-color-price="{{ $color->pivot->price }}"
                                                 data-color-stock="{{ $colorStock }}"
                                                 data-color-images='@json($product->getColorImages($color->id)->pluck("name"))'>
                                                <input type="radio" name="color_radio" id="color_{{ $color->id }}" value="{{ $color->slug }}" {{ $index === 0 && !$isOutOfStock ? 'checked' : '' }} {{ $isOutOfStock ? 'disabled' : '' }}>
                                                
                                                @php
                                                    $firstColorImage = $product->getColorImages($color->id)->first();
                                                @endphp
                                                
                                                @if($firstColorImage)
                                                    <img src="{{ asset('uploads/product/' . $firstColorImage->name) }}" alt="{{ $color->name }}" class="color-preview">
                                                @else
                                                    <div class="color-swatch" style="background-color: {{ $color->code }}"></div>
                                                @endif
                                                
                                                <span class="color-name">{{ $color->name }} {{ $isOutOfStock ? '(Out)' : '(' . $colorStock . ')' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Attributes -->
                            @foreach($attributes as $attribute)
                                @php
                                    // Get attribute values for this specific attribute that belong to this product
                                    $productAttributes = $product->attributes_values->filter(function($attrValue) use ($attribute) {
                                        return $attrValue->attribute && $attrValue->attribute->id == $attribute->id;
                                    });
                                @endphp
                                
                                @if($productAttributes->count() > 0)
                                    <div class="attribute-selection">
                                        <div class="section-title">
                                            <i class="fas fa-list"></i> Select {{ $attribute->name }}: 
                                            <span id="selected{{ $attribute->slug }}Name"></span>
                                        </div>
                                        <div class="attribute-options">
                                            @foreach($productAttributes as $index => $attrValue)
                                                @php
                                                    $attrStock = $attrValue->pivot->qnty;
                                                    $isAttrOutOfStock = $attrStock <= 0;
                                                @endphp
                                                
                                                <div class="attribute-option">
                                                    <input type="radio" 
                                                           name="attribute_{{ $attribute->id }}_radio" 
                                                           id="attr_{{ $attrValue->id }}" 
                                                           value="{{ $attrValue->id }}" 
                                                           data-attr-name="{{ $attrValue->name }}" 
                                                           data-attr-slug="{{ $attribute->slug }}" 
                                                           data-attr-price="{{ $attrValue->pivot->price }}" 
                                                           data-attr-stock="{{ $attrStock }}" 
                                                           {{ $index === 0 && !$isAttrOutOfStock ? 'checked' : '' }} 
                                                           {{ $isAttrOutOfStock ? 'disabled' : '' }}>
                                                    <label for="attr_{{ $attrValue->id }}" class="{{ $isAttrOutOfStock ? 'disabled' : '' }}">
                                                        {{ $attrValue->name }}
                                                        <div class="attribute-stock-info">
                                                            {{ $isAttrOutOfStock ? 'Out of Stock' : 'Stock: ' . $attrStock }}
                                                        </div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <!-- Quantity -->
                            @if($totalStock > 0)
                                <div class="quantity-selector">
                                    <div class="section-title">Quantity:</div>
                                    <div class="quantity-controls">
                                        <button type="button" class="qty-btn" id="decreaseQty">−</button>
                                        <input type="number" class="qty-input" id="qtyInput" value="1" min="1" max="{{ $totalStock }}" readonly>
                                        <button type="button" class="qty-btn" id="increaseQty">+</button>
                                    </div>
                                    <small class="text-muted ms-2">Available: <span id="availableStock">{{ $totalStock }}</span></small>
                                </div>

                                <div class="action-buttons">
                                    <button type="button" class="btn-primary" id="addToCartBtn">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                    <button type="button" class="btn-secondary" id="buyNowBtn">
                                        <i class="fas fa-bolt"></i> Buy Now
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i> This product is currently out of stock
                                </div>
                            @endif
                        </form>

                        <!-- Secondary Actions -->
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

        <!-- Product Details Tabs -->
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
                            @if($product->colors->count() > 0)<tr><th>Available Colors</th><td>@foreach($product->colors as $color)<span class="badge" style="background-color: {{ $color->code }}; color: white;">{{ $color->name }}</span> @endforeach</td></tr>@endif
                            @if($isVariableProduct)<tr><th>Product Type</th><td>Variable Product</td></tr>@endif
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
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    const config = {
        productId: {{ $product->id }},
        basePrice: parseFloat($('#dynamicPrice').text().replace(/,/g, '')),
        isVariable: $('#isVariable').val() === '1',
        maxStock: parseInt($('#maxStock').val())
    };

    let selectedVariations = { color: null, attributes: {} };

    // Initialize
    $('.color-option:not(.disabled):first').trigger('click');
    $('[name^="attribute_"][type="radio"]:not(:disabled):checked').each(function() {
        const attrSlug = $(this).data('attr-slug');
        const attrName = $(this).data('attr-name');
        selectedVariations.attributes[attrSlug] = {
            id: $(this).val(),
            name: attrName,
            price: parseFloat($(this).data('attr-price')) || 0,
            stock: parseInt($(this).data('attr-stock')) || 0
        };
        $(`#selected${attrSlug}Name`).text(attrName);
    });
    updatePrice();

    // Thumbnail clicks
    $(document).on('click', '.thumbnail-item', function() {
        $('.thumbnail-item').removeClass('active');
        $(this).addClass('active');
        $('#mainImage').attr('src', $(this).data('image'));
    });

    // Color Selection
    $('.color-option').on('click', function() {
        if ($(this).hasClass('disabled')) return;
        $('.color-option').removeClass('active');
        $(this).addClass('active');

        const colorId = $(this).data('color-id');
        const colorSlug = $(this).data('color-slug');
        const colorName = $(this).data('color-name');
        const colorPrice = parseFloat($(this).data('color-price')) || 0;
        const colorStock = parseInt($(this).data('color-stock')) || 0;
        const colorImages = $(this).data('color-images');

        selectedVariations.color = { id: colorId, slug: colorSlug, name: colorName, price: colorPrice, stock: colorStock };
        $('#selectedColor').val(colorSlug);
        $('#selectedColorName').text(colorName);

        // Update images
        if (colorImages && colorImages.length > 0) {
            let thumbnailsHtml = '';
            colorImages.forEach((img, i) => {
                const imgUrl = '{{ asset("uploads/product") }}/' + img;
                thumbnailsHtml += `<div class="thumbnail-item ${i === 0 ? 'active' : ''}" data-image="${imgUrl}"><img src="${imgUrl}" alt="Color"></div>`;
            });
            $('#thumbnailsContainer').html(thumbnailsHtml);
            $('#mainImage').attr('src', '{{ asset("uploads/product") }}/' + colorImages[0]);
        }

        updatePrice();
        updateStockDisplay();
    });

    // Attribute Selection
    $('input[type="radio"][name^="attribute_"]').on('change', function() {
        if ($(this).is(':disabled')) return;
        const attrSlug = $(this).data('attr-slug');
        const attrName = $(this).data('attr-name');
        selectedVariations.attributes[attrSlug] = {
            id: $(this).val(),
            name: attrName,
            price: parseFloat($(this).data('attr-price')) || 0,
            stock: parseInt($(this).data('attr-stock')) || 0
        };
        const attributeId = $(this).attr('name').match(/\d+/)[0];
        $(`#attribute_${attributeId}`).val($(this).val());
        $(`#selected${attrSlug}Name`).text(attrName);
        updatePrice();
        updateStockDisplay();
    });

    function updatePrice() {
        let totalPrice = config.basePrice;
        const quantity = parseInt($('#qtyInput').val()) || 1;
        if (selectedVariations.color) totalPrice += selectedVariations.color.price;
        Object.values(selectedVariations.attributes).forEach(attr => { totalPrice += attr.price; });
        $('#dynamicPrice').text((totalPrice * quantity).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        $('#hiddenDynamicPrice').val(totalPrice);
    }

    function updateStockDisplay() {
        let availableStock = config.maxStock;
        if (config.isVariable) {
            if (selectedVariations.color) availableStock = selectedVariations.color.stock;
            const attrStocks = Object.values(selectedVariations.attributes).map(a => a.stock);
            if (attrStocks.length > 0) {
                const totalAttrStock = attrStocks.reduce((sum, stock) => sum + stock, 0);
                if (totalAttrStock > 0) availableStock = Math.min(availableStock, totalAttrStock);
            }
        }
        $('#availableStock').text(availableStock);
        $('#maxStock').val(availableStock);
        $('#qtyInput').attr('max', availableStock);
        
        const stockStatus = $('#stockStatus');
        const stockText = $('#stockText');
        stockStatus.removeClass('in-stock low-stock out-of-stock');
        if (availableStock > 10) {
            stockStatus.addClass('in-stock');
            stockText.html('<i class="fas fa-check-circle"></i> In Stock (' + availableStock + ' available)');
        } else if (availableStock > 0) {
            stockStatus.addClass('low-stock');
            stockText.html('<i class="fas fa-exclamation-circle"></i> Low Stock (Only ' + availableStock + ' left)');
        } else {
            stockStatus.addClass('out-of-stock');
            stockText.html('<i class="fas fa-times-circle"></i> Out of Stock');
            $('#addToCartBtn, #buyNowBtn').prop('disabled', true);
        }
    }

    // Quantity
    $('#increaseQty').on('click', function() {
        const input = $('#qtyInput');
        const max = parseInt(input.attr('max'));
        const current = parseInt(input.val());
        if (current < max) {
            input.val(current + 1);
            $('#quantity').val(current + 1);
            updatePrice();
        }
    });

    $('#decreaseQty').on('click', function() {
        const input = $('#qtyInput');
        const current = parseInt(input.val());
        if (current > 1) {
            input.val(current - 1);
            $('#quantity').val(current - 1);
            updatePrice();
        }
    });

    // Add to Cart
    $('#addToCartBtn').on('click', function() {
        if (config.isVariable && $('.color-option').length > 0 && !selectedVariations.color) {
            showNotification('Error', 'Please select a color', 'error');
            return;
        }

        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');

        $.ajax({
            url: '{{ route("add.cart") }}',
            method: 'POST',
            data: $('#productForm').serialize(),
            success: function(response) {
                showNotification('Success!', 'Product added to cart', 'success');
                btn.prop('disabled', false).html(originalText);
            },
            error: function(xhr) {
                showNotification('Error!', xhr.responseJSON?.message || 'Failed to add to cart', 'error');
                btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Buy Now
    $('#buyNowBtn').on('click', function() {
        if (config.isVariable && $('.color-option').length > 0 && !selectedVariations.color) {
            showNotification('Error', 'Please select a color', 'error');
            return;
        }
        const params = new URLSearchParams($('#productForm').serializeArray().reduce((obj, item) => ({...obj, [item.name]: item.value}), {}));
        window.location.href = '{{ route("buy.product") }}?' + params.toString();
    });

    // Tabs
    $('.tab-btn').on('click', function() {
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.tab-pane').removeClass('active');
        $('#' + $(this).data('tab')).addClass('active');
    });

    function showNotification(title, message, type) {
        const notification = $('#notification');
        notification.removeClass('success error').addClass(type).html(`<strong>${title}</strong><br>${message}`).addClass('show');
        setTimeout(() => notification.removeClass('show'), 3000);
    }
});
</script>
@endpush