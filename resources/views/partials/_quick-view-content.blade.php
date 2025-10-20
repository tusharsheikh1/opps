@php
    $productId = $product->id;
    $totalStock = $product->getTotalStockAttribute();
    $basePrice = $product->discount_price > 0 ? $product->discount_price : $product->regular_price;
@endphp

{{-- 
This wrapper, with its unique ID, is crucial for scoping the CSS and JavaScript 
to *this specific modal* and preventing conflicts.
--}}
<div id="quickViewContent_{{ $productId }}">

<style>
    /* All styles are scoped to the unique wrapper ID to avoid conflicts.
    These are taken from your original single-product.blade.php
    */
    #quickViewContent_{{ $productId }} {
        font-family: 'Inter', sans-serif;
        color: var(--dark-color);
    }
    #quickViewContent_{{ $productId }} :root {
        --primary-color: #2563eb;
        --secondary-color: #10b981;
        --danger-color: #ef4444;
        --dark-color: #1f2937;
        --light-color: #f3f4f6;
        --border-color: #e5e7eb;
    }
    #quickViewContent_{{ $productId }} .product-gallery { position: relative; background: white; padding: 20px; }
    #quickViewContent_{{ $productId }} .main-image-container { position: relative; width: 100%; height: 450px; border-radius: 8px; overflow: hidden; background: #f9fafb; display: flex; align-items: center; justify-content: center; }
    #quickViewContent_{{ $productId }} .main-image { max-width: 100%; max-height: 100%; object-fit: contain; transition: transform 0.3s ease; }
    #quickViewContent_{{ $productId }} .main-image:hover { transform: scale(1.05); }
    #quickViewContent_{{ $productId }} .image-thumbnails { display: flex; gap: 10px; margin-top: 15px; overflow-x: auto; padding: 10px 0; }
    #quickViewContent_{{ $productId }} .thumbnail-item { flex: 0 0 70px; height: 70px; border-radius: 8px; overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease; }
    #quickViewContent_{{ $productId }} .thumbnail-item:hover, #quickViewContent_{{ $productId }} .thumbnail-item.active { border-color: var(--primary-color); transform: translateY(-2px); }
    #quickViewContent_{{ $productId }} .thumbnail-item img { width: 100%; height: 100%; object-fit: cover; }
    #quickViewContent_{{ $productId }} .product-info { padding: 20px; max-height: 75vh; overflow-y: auto; }
    #quickViewContent_{{ $productId }} .product-title { font-size: 24px; font-weight: 700; color: var(--dark-color); margin-bottom: 15px; line-height: 1.3; }
    #quickViewContent_{{ $productId }} .product-meta { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color); }
    #quickViewContent_{{ $productId }} .meta-item { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #6b7280; }
    #quickViewContent_{{ $productId }} .meta-item i { color: var(--primary-color); }
    #quickViewContent_{{ $productId }} .meta-item a { color: var(--primary-color); text-decoration: none; }
    #quickViewContent_{{ $productId }} .meta-item a:hover { text-decoration: underline; }
    #quickViewContent_{{ $productId }} .price-section { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 20px; border-radius: 10px; margin-bottom: 25px; }
    #quickViewContent_{{ $productId }} .price-display { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
    #quickViewContent_{{ $productId }} .current-price { font-size: 32px; font-weight: 800; color: var(--danger-color); }
    #quickViewContent_{{ $productId }} .original-price { font-size: 18px; color: #9ca3af; text-decoration: line-through; }
    #quickViewContent_{{ $productId }} .discount-badge { background: var(--danger-color); color: white; padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
    #quickViewContent_{{ $productId }} .stock-status { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
    #quickViewContent_{{ $productId }} .stock-status.in-stock { background: #d1fae5; color: #065f46; }
    #quickViewContent_{{ $productId }} .stock-status.low-stock { background: #fef3c7; color: #92400e; }
    #quickViewContent_{{ $productId }} .stock-status.out-of-stock { background: #fee2e2; color: #991b1b; }
    #quickViewContent_{{ $productId }} .color-selection, #quickViewContent_{{ $productId }} .size-selection, #quickViewContent_{{ $productId }} .attribute-selection { margin-bottom: 25px; }
    #quickViewContent_{{ $productId }} .section-title { font-size: 16px; font-weight: 600; margin-bottom: 12px; color: var(--dark-color); }
    #quickViewContent_{{ $productId }} .color-options { display: flex; flex-wrap: wrap; gap: 12px; }
    #quickViewContent_{{ $productId }} .color-option { position: relative; cursor: pointer; border: 3px solid transparent; border-radius: 8px; overflow: hidden; transition: all 0.3s ease; }
    #quickViewContent_{{ $productId }} .color-option:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); }
    #quickViewContent_{{ $productId }} .color-option.active { border-color: var(--primary-color); box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
    #quickViewContent_{{ $productId }} .color-option.disabled { opacity: 0.5; cursor: not-allowed; pointer-events: none; }
    #quickViewContent_{{ $productId }} .color-option input { display: none; }
    #quickViewContent_{{ $productId }} .color-preview { width: 70px; height: 70px; object-fit: cover; display: block; }
    #quickViewContent_{{ $productId }} .color-swatch { width: 70px; height: 70px; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
    #quickViewContent_{{ $productId }} .color-name { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0, 0, 0, 0.7); color: white; padding: 4px 8px; font-size: 11px; text-align: center; }
    #quickViewContent_{{ $productId }} .attribute-options { display: flex; flex-wrap: wrap; gap: 10px; }
    #quickViewContent_{{ $productId }} .attribute-option { position: relative; }
    #quickViewContent_{{ $productId }} .attribute-option input { display: none; }
    #quickViewContent_{{ $productId }} .attribute-option label { display: block; padding: 10px 20px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease; font-weight: 500; background: white; }
    #quickViewContent_{{ $productId }} .attribute-option input:checked + label { background: var(--primary-color); color: white; border-color: var(--primary-color); }
    #quickViewContent_{{ $productId }} .attribute-option input:disabled + label { opacity: 0.7; cursor: not-allowed; background: #f9fafb; color: #9ca3af; text-decoration: line-through; }
    #quickViewContent_{{ $productId }} .attribute-option label:hover:not(.disabled) { border-color: var(--primary-color); transform: translateY(-2px); }
    #quickViewContent_{{ $productId }} .quantity-selector { margin-bottom: 25px; }
    #quickViewContent_{{ $productId }} .quantity-controls { display: inline-flex; align-items: center; border: 2px solid var(--border-color); border-radius: 8px; overflow: hidden; }
    #quickViewContent_{{ $productId }} .qty-btn { background: white; border: none; width: 40px; height: 40px; cursor: pointer; font-size: 18px; font-weight: bold; color: var(--dark-color); transition: background 0.3s; }
    #quickViewContent_{{ $productId }} .qty-btn:hover { background: var(--light-color); }
    #quickViewContent_{{ $productId }} .qty-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    #quickViewContent_{{ $productId }} .qty-input { width: 60px; height: 40px; border: none; text-align: center; font-weight: 600; font-size: 16px; }
    #quickViewContent_{{ $productId }} .action-buttons { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
    #quickViewContent_{{ $productId }} .btn-primary, #quickViewContent_{{ $productId }} .btn-secondary { flex: 1; min-width: 150px; padding: 14px 24px; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 8px; }
    #quickViewContent_{{ $productId }} .btn-primary { background: var(--primary-color); color: white; border: none; }
    #quickViewContent_{{ $productId }} .btn-primary:hover:not(:disabled) { background: #1d4ed8; transform: translateY(-2px); }
    #quickViewContent_{{ $productId }} .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
    #quickViewContent_{{ $productId }} .btn-secondary { background: white; color: var(--primary-color); border: 2px solid var(--primary-color); }
    #quickViewContent_{{ $productId }} .btn-secondary:hover:not(:disabled) { background: var(--primary-color); color: white; transform: translateY(-2px); }
    #quickViewContent_{{ $productId }} .btn-secondary:disabled { opacity: 0.6; cursor: not-allowed; }
    #quickViewContent_{{ $productId }} .notification { position: fixed; top: 20px; right: 20px; background: white; padding: 16px 20px; border-radius: 8px; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); z-index: 9999; min-width: 300px; display: none; }
    #quickViewContent_{{ $productId }} .notification.show { display: block; animation: qvSlideIn 0.3s ease; }
    @keyframes qvSlideIn { from { transform: translateX(400px); opacity: 0); } to { transform: translateX(0); opacity: 1); } }
    #quickViewContent_{{ $productId }} .notification.success { border-left: 4px solid var(--secondary-color); }
    #quickViewContent_{{ $productId }} .notification.error { border-left: 4px solid var(--danger-color); }
    @media (max-width: 768px) {
        #quickViewContent_{{ $productId }} .product-title { font-size: 20px; }
        #quickViewContent_{{ $productId }} .current-price { font-size: 28px; }
        #quickViewContent_{{ $productId }} .main-image-container { height: 350px; }
        #quickViewContent_{{ $productId }} .action-buttons { flex-direction: column; }
        #quickViewContent_{{ $productId }} .btn-primary, #quickViewContent_{{ $productId }} .btn-secondary { min-width: 100%; }
    }
</style>

<div class="container-fluid">
    <div id="notification_{{ $productId }}" class="notification"></div>

    <div class="row">
        <div class="col-md-5">
            <div class="product-gallery">
                <div class="main-image-container">
                    @if($product->image)
                        <img src="{{ asset('uploads/product/' . $product->image) }}" alt="{{ $product->title }}" class="main-image" id="mainImage_{{ $productId }}">
                    @else
                        <div style="text-align: center; color: #9ca3af;">
                            <i class="fas fa-image" style="font-size: 64px;"></i>
                            <p>No Image Available</p>
                        </div>
                    @endif
                </div>

                <div class="image-thumbnails" id="thumbnailsContainer_{{ $productId }}">
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
                </div>

                <div class="price-section">
                    <div class="price-display">
                        @if($product->discount_price > 0)
                            <span class="current-price">৳<span id="dynamicPrice_{{ $productId }}">{{ number_format($product->discount_price, 0) }}</span></span>
                            <span class="original-price">৳{{ number_format($product->regular_price, 0) }}</span>
                            @php
                                $discount_percentage = round((($product->regular_price - $product->discount_price) / $product->regular_price) * 100);
                            @endphp
                            <span class="discount-badge">-{{ $discount_percentage }}%</span>
                        @else
                            <span class="current-price">৳<span id="dynamicPrice_{{ $productId }}">{{ number_format($product->regular_price, 0) }}</span></span>
                        @endif
                    </div>
                </div>

                @if($totalStock > 10)
                    <div class="stock-status in-stock" id="stockStatus_{{ $productId }}">
                        <i class="fas fa-check-circle"></i>
                        <span id="stockText_{{ $productId }}">In Stock</span>
                    </div>
                @elseif($totalStock > 0)
                    <div class="stock-status low-stock" id="stockStatus_{{ $productId }}">
                        <i class="fas fa-exclamation-circle"></i>
                        <span id="stockText_{{ $productId }}">Low Stock</span>
                    </div>
                @else
                    <div class="stock-status out-of-stock" id="stockStatus_{{ $productId }}">
                        <i class="fas fa-times-circle"></i>
                        <span id="stockText_{{ $productId }}">Out of Stock</span>
                    </div>
                @endif

                @if($product->short_description)
                    <div class="mb-4">
                        {!! Str::limit($product->short_description, 200) !!}
                    </div>
                @endif

                <form id="productForm_{{ $productId }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="qty" id="quantity_{{ $productId }}" value="1">
                    <input type="hidden" name="color" id="selectedColor_{{ $productId }}" value="">
                    <input type="hidden" name="size" id="selectedSize_{{ $productId }}" value="">
                    <input type="hidden" name="dynamic_price" id="hiddenDynamicPrice_{{ $productId }}" value="{{ $basePrice }}">

                    {{-- 1. Color and Size Selection --}}
                    @if(!empty($variations['color_size']))
                        <div class="color-selection">
                            <div class="section-title">
                                <i class="fas fa-palette"></i> Select Color: <span id="selectedColorName_{{ $productId }}" class="font-weight-bold"></span>
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
                                        <input type="radio" name="color_radio_{{ $productId }}" id="color_{{ $productId }}_{{ $colorData['color_id'] }}" value="{{ $colorData['color_id'] }}" {{ $isOutOfStock ? 'disabled' : '' }}>
                                        
                                        @if($firstImage)
                                            <img src="{{ asset('uploads/product/' . $firstImage->name) }}" alt="{{ $colorData['color_name'] }}" class="color-preview">
                                        @else
                                            <div class="color-swatch" style="background-color: {{ $colorData['color_code'] ?? '#ffffff' }}"></div>
                                        @endif
                                        <span class="color-name">{{ $colorData['color_name'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="size-selection" style="display:none;" id="sizeSelectionContainer_{{ $productId }}">
                            <div class="section-title">
                                <i class="fas fa-ruler-horizontal"></i> Select Size: <span id="selectedSizeName_{{ $productId }}" class="font-weight-bold"></span>
                            </div>
                            <div class="attribute-options" id="sizeOptionsContainer_{{ $productId }}">
                                {{-- Size options populated by JS --}}
                            </div>
                        </div>

                    {{-- 2. Size-Only Selection --}}
                    @elseif(!empty($variations['size_only']))
                        <div class="size-selection">
                            <div class="section-title">
                                <i class="fas fa-ruler-horizontal"></i> Select Size: <span id="selectedSizeName_{{ $productId }}" class="font-weight-bold"></span>
                            </div>
                            <div class="attribute-options" id="sizeOnlyOptionsContainer_{{ $productId }}">
                                @foreach($variations['size_only'] as $sizeValue)
                                    @php $isSizeOutOfStock = $sizeValue['stock'] <= 0; @endphp
                                    <div class="attribute-option">
                                        <input type="radio" 
                                            name="size_radio_{{ $productId }}"
                                            id="size_only_{{ $productId }}_{{ $sizeValue['id'] }}" 
                                            value="{{ $sizeValue['id'] }}" 
                                            data-size-name="{{ $sizeValue['name'] }}"
                                            data-stock="{{ $sizeValue['stock'] }}" 
                                            data-price="{{ $sizeValue['price'] }}" 
                                            {{ $isSizeOutOfStock ? 'disabled' : '' }}>
                                        <label for="size_only_{{ $productId }}_{{ $sizeValue['id'] }}" class="{{ $isSizeOutOfStock ? 'disabled' : '' }}">
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
                                    <i class="fas fa-list"></i> Select {{ $attributeName }}: <span id="selectedAttrName_{{ $productId }}" class="font-weight-bold"></span>
                                </div>
                                <div class="attribute-options">
                                    @foreach($attributeValues as $attrValue)
                                        @php $isAttrOutOfStock = $attrValue['stock'] <= 0; @endphp
                                        <div class="attribute-option">
                                            <input type="radio" 
                                                name="attribute_radio_{{ $productId }}" 
                                                id="attr_{{ $productId }}_{{ $attrValue['id'] }}" 
                                                value="{{ $attrValue['id'] }}" 
                                                data-attr-name="{{ $attrValue['name'] }}"
                                                data-attr-price="{{ $attrValue['price'] }}" 
                                                data-attr-stock="{{ $attrValue['stock'] }}" 
                                                {{ $isAttrOutOfStock ? 'disabled' : '' }}>
                                            <label for="attr_{{ $productId }}_{{ $attrValue['id'] }}" class="{{ $isAttrOutOfStock ? 'disabled' : '' }}">
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
                                <button type="button" class="qty-btn" id="decreaseQty_{{ $productId }}">−</button>
                                <input type="number" class="qty-input" id="qtyInput_{{ $productId }}" value="1" min="1" readonly>
                                <button type="button" class="qty-btn" id="increaseQty_{{ $productId }}">+</button>
                            </div>
                            <small class="text-muted ms-2">Available: <span id="availableStock_{{ $productId }}">{{ $totalStock }}</span></small>
                        </div>

                        <div class="action-buttons">
                            <button type="button" class="btn-primary" id="addToCartBtn_{{ $productId }}" disabled>
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                            <button type="button" class="btn-secondary" id="buyNowBtn_{{ $productId }}" disabled>
                                <i class="fas fa-bolt"></i> Buy Now
                            </button>
                        </div>
                    @else
                        <div class="alert alert-danger mt-4">
                            <i class="fas fa-exclamation-triangle"></i> This product is currently out of stock
                        </div>
                    @endif
                </form>

                <div class="mt-3">
                    <a href="{{ route('product.details', $product->slug) }}" class="btn-link">View Full Product Details →</a>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
{{-- 
This is the refactored JavaScript from your single-product.blade.php file.
It is now self-contained and scoped to the unique modal ID.
--}}
(function() {
    // --- SCOPED CONFIGURATION & STATE ---
    const productId = '{{ $productId }}';
    const wrapper = $(`#quickViewContent_${productId}`);
    if (!wrapper.length) {
        console.error(`Quick view wrapper "#quickViewContent_${productId}" not found.`);
        return;
    }

    const config = {
        basePrice: parseFloat('{{ $basePrice }}'),
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

    // --- CACHED JQUERY SELECTORS ---
    // All selectors are scoped to the unique modal wrapper
    const selectors = {
        mainImage: wrapper.find(`#mainImage_${productId}`),
        thumbnailsContainer: wrapper.find(`#thumbnailsContainer_${productId}`),
        dynamicPrice: wrapper.find(`#dynamicPrice_${productId}`),
        hiddenDynamicPrice: wrapper.find(`#hiddenDynamicPrice_${productId}`),
        stockStatus: wrapper.find(`#stockStatus_${productId}`),
        stockText: wrapper.find(`#stockText_${productId}`),
        selectedColor: wrapper.find(`#selectedColor_${productId}`),
        selectedSize: wrapper.find(`#selectedSize_${productId}`),
        selectedColorName: wrapper.find(`#selectedColorName_${productId}`),
        selectedSizeName: wrapper.find(`#selectedSizeName_${productId}`),
        selectedAttrName: wrapper.find(`#selectedAttrName_${productId}`),
        sizeSelectionContainer: wrapper.find(`#sizeSelectionContainer_${productId}`),
        sizeOptionsContainer: wrapper.find(`#sizeOptionsContainer_${productId}`),
        sizeOnlyOptionsContainer: wrapper.find(`#sizeOnlyOptionsContainer_${productId}`),
        qtyInput: wrapper.find(`#qtyInput_${productId}`),
        quantity: wrapper.find(`#quantity_${productId}`),
        availableStock: wrapper.find(`#availableStock_${productId}`),
        addToCartBtn: wrapper.find(`#addToCartBtn_${productId}`),
        buyNowBtn: wrapper.find(`#buyNowBtn_${productId}`),
        notification: wrapper.find(`#notification_${productId}`),
        productForm: wrapper.find(`#productForm_${productId}`)
    };

    // --- EVENT HANDLERS (Delegated from wrapper) ---
    wrapper.on('click', '.thumbnail-item', function() {
        wrapper.find('.thumbnail-item').removeClass('active');
        $(this).addClass('active');
        selectors.mainImage.attr('src', $(this).data('image'));
    });

    wrapper.on('click', '.color-option', function() {
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
            selectors.selectedSize.val('');
            selectors.selectedSizeName.text('');
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

        wrapper.find('.color-option').removeClass('active');
        $(this).addClass('active');
        selectors.selectedColor.val(selection.color.id);
        selectors.selectedSize.val('');
        selectors.selectedColorName.text(selection.color.name);
        selectors.selectedSizeName.text('');

        updateImageGallery(selection.color.images);
        populateSizeOptions(selection.color.id);
        updateUI();
    });

    wrapper.on('change', `input[name="size_radio_${productId}"]`, function() {
        if ($(this).is(':disabled')) return;

        selection.size = {
            id: $(this).val(),
            name: $(this).data('size-name'),
            stock: parseInt($(this).data('stock')),
            price: parseFloat($(this).data('price'))
        };
        
        selectors.selectedSize.val(selection.size.id);
        selectors.selectedSizeName.text(selection.size.name);
        
        if (config.hasSizeOnly) {
            selection.color = null;
            selection.attribute = null;
        }
        
        updateUI();
    });
    
    wrapper.on('change', `input[name="attribute_radio_${productId}"]`, function() {
        if ($(this).is(':disabled')) return;

        selection.attribute = {
            id: $(this).val(),
            name: $(this).data('attr-name'),
            stock: parseInt($(this).data('attr-stock')),
            price: parseFloat($(this).data('attr-price'))
        };

        selectors.selectedAttrName.text(selection.attribute.name);
        selectors.selectedSize.val(selection.attribute.id); // Storing attribute ID in size field
        updateUI();
    });

    wrapper.on('click', `#increaseQty_${productId}`, function() {
        const input = selectors.qtyInput;
        const max = parseInt(input.attr('max'));
        const current = parseInt(input.val());
        if (current < max) {
            input.val(current + 1).trigger('change');
        }
    });

    wrapper.on('click', `#decreaseQty_${productId}`, function() {
        const input = selectors.qtyInput;
        const current = parseInt(input.val());
        if (current > 1) {
            input.val(current - 1).trigger('change');
        }
    });
    
    wrapper.on('change', `#qtyInput_${productId}`, function() {
        selectors.quantity.val($(this).val());
    });
    
    wrapper.on('click', `#addToCartBtn_${productId}, #buyNowBtn_${productId}`, function() {
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
            isValid = true; // Simple product
        }

        if(isValid) {
            const isBuyNow = $(this).is(`#buyNowBtn_${productId}`);
            handleAction(isBuyNow, $(this));
        }
    });

    // --- UI & LOGIC FUNCTIONS ---
    function populateSizeOptions(colorId) {
        const productVariationSizes = config.variations.color_size[colorId]?.sizes || [];
        const sizeMap = new Map(productVariationSizes.map(s => [s.size_id, s]));
        const container = selectors.sizeOptionsContainer;
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
                        <input type="radio" name="size_radio_${productId}" id="size_${productId}_${size.id}" value="${size.id}" 
                            data-size-name="${size.name}" data-stock="${stock}" data-price="${price}" 
                            ${isOutOfStock ? 'disabled' : ''}>
                        <label for="size_${productId}_${size.id}" class="${isOutOfStock ? 'disabled' : ''}">${size.name}</label>
                    </div>`;
            });
            container.html(html);
            selectors.sizeSelectionContainer.slideDown();

            const previouslySelected = container.find(`input[value="${selection.size?.id}"]`);
            const firstAvailable = container.find('input[type="radio"]:not(:disabled):first');
            
            if (previouslySelected.length && !previouslySelected.is(':disabled')) {
                previouslySelected.prop('checked', true).trigger('change');
            } else if (firstAvailable.length) {
                firstAvailable.prop('checked', true).trigger('change');
            } else {
                selection.size = null; 
                selectors.selectedSize.val('');
                selectors.selectedSizeName.text('');
                updateUI();
            }
        } else {
            selectors.sizeSelectionContainer.slideUp();
        }
    }
    
    function updateImageGallery(images) {
        let thumbnailsHtml = '';
        const defaultImage = {
            name: '{{ $product->image }}',
            url: '{{ asset("uploads/product/" . $product->image) }}'
        };
        
        thumbnailsHtml += `<div class="thumbnail-item active" data-image="${defaultImage.url}"><img src="${defaultImage.url}" alt="Main"></div>`;
        selectors.mainImage.attr('src', defaultImage.url);

        if (images && images.length > 0) {
            images.forEach((img, i) => {
                const imgUrl = '{{ asset("uploads/product") }}/' + img;
                thumbnailsHtml += `<div class="thumbnail-item" data-image="${imgUrl}"><img src="${imgUrl}" alt="Color Variant"></div>`;
            });
            // Set main image to first variant image
            selectors.mainImage.attr('src', '{{ asset("uploads/product") }}/' + images[0]);
            // Adjust active thumbnail
            thumbnailsHtml = thumbnailsHtml.replace('thumbnail-item active', 'thumbnail-item');
            thumbnailsHtml = thumbnailsHtml.replace('thumbnail-item', 'thumbnail-item active');
        } 
        
        @foreach($product->getGeneralImages() as $image)
            @if($product->image != $image->name) // Avoid duplicate
                const generalImgUrl = '{{ asset("uploads/product/" . $image->name) }}';
                thumbnailsHtml += `<div class="thumbnail-item" data-image="${generalImgUrl}"><img src="${generalImgUrl}" alt="Gallery"></div>`;
            @endif
        @endforeach
        
        selectors.thumbnailsContainer.html(thumbnailsHtml);
        
        // Ensure first item is active if no variant image was set
        if (selectors.thumbnailsContainer.find('.active').length === 0) {
             selectors.thumbnailsContainer.find('.thumbnail-item:first').addClass('active');
        }
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
        selectors.dynamicPrice.text(formattedPrice);
        selectors.hiddenDynamicPrice.val(price);
        selectors.availableStock.text(stock);
        
        const newMax = stock > 0 ? stock : 1;
        selectors.qtyInput.attr('max', newMax);
        
        const currentQty = parseInt(selectors.qtyInput.val());
        if (currentQty > newMax) {
            selectors.qtyInput.val(newMax).trigger('change');
        } else if (currentQty < 1 && newMax >= 1) {
             selectors.qtyInput.val(1).trigger('change');
        }

        const stockStatus = selectors.stockStatus;
        const stockText = selectors.stockText;
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
        
        stockText.html(iconHtml + text);

        const canAddToCart = selectionComplete && stock > 0;
        selectors.addToCartBtn.prop('disabled', !canAddToCart);
        selectors.buyNowBtn.prop('disabled', !canAddToCart);
    }

    function handleAction(isBuyNow, btn) {
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        const formData = selectors.productForm.serialize();

        if (isBuyNow) {
            const params = new URLSearchParams(formData).toString();
            window.location.href = '{{ route("buy.product") }}?' + params;
        } else {
            $.ajax({
                url: '{{ route("add.cart") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    showNotification('Success!', 'Product added to cart', 'success');
                    if (response && typeof response.count !== 'undefined') {
                        // Update the main cart count outside the modal
                        $('.cart-count-badge').text(response.count); 
                    }
                    // Close the modal on success
                    $('#quickViewCloseBtn').trigger('click');
                },
                error: function(xhr) {
                    showNotification('Error!', xhr.responseJSON?.message || 'Failed to add to cart', 'error');
                }
            }).always(function() {
                btn.prop('disabled', false).html(originalText);
                updateUI();
            });
        }
    }
    
    function showNotification(title, message, type) {
        const notification = selectors.notification;
        notification.removeClass('success error').addClass(type).html(`<strong>${title}</strong><br>${message}`).addClass('show');
        setTimeout(() => notification.removeClass('show'), 3000);
    }

    // --- RUN INITIALIZATION ---
    function initialize() {
        if (config.hasColorSize) {
            wrapper.find('.color-option:not(.disabled):first').trigger('click');
        } else if (config.hasSizeOnly) {
            selectors.sizeOnlyOptionsContainer.find('input[type="radio"]:not(:disabled):first').prop('checked', true).trigger('change');
        } else if (config.hasAttributes) {
            wrapper.find('.attribute-option input[type="radio"]:not(:disabled):first').prop('checked', true).trigger('change');
        } else {
            updateUI();
        }
    }

    initialize();
})();
</script>