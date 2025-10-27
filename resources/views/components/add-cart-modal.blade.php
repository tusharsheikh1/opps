<div class="modal fade" id="cart-modal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="cartModalLabel">Add To Cart</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
    
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div id="cart-img"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="product-info-section">
                            <h3 id="product-modal-title" class="product-modal-title"></h3>
                            
                            <div class="price-wrapper">
                                <div class="current-price">
                                    <span class="price-amount" id="del_price"></span>
                                    <span class="currency-code">{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</span>
                                </div>
                                <div class="original-price" id="nhide" style="display: none;">
                                    <span class="strike-price" id="item_price"></span>
                                    <span class="currency-code">{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</span>
                                </div>
                            </div>
                            
                            <div class="stock-badge" id="stock_badge">
                                <svg class="stock-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M13.5 5.5L6.5 12.5L2.5 8.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span id="stock_info">Loading...</span>
                            </div>
                            
                            <div class="section-divider"></div>
                            
                            <div class="selection-section" id="color_section" style="display:none;">
                                <div class="section-header">
                                    <span class="section-label">Color</span>
                                    <span class="selected-value" id="selected_color_name"></span>
                                </div>
                                <div class="options-container" id="colors"></div>
                            </div>
                            
                            <div class="selection-section" id="size_section" style="display:none;">
                                <div class="section-header">
                                    <span class="section-label">Size</span>
                                    <span class="selected-value" id="selected_size_name"></span>
                                </div>
                                <div class="options-container" id="sizes"></div>
                            </div>
                            
                            <div class="selection-section" id="attributes_all"></div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="modal-footer">
                <button type="button" class="btn-cancel" data-dismiss="modal">Cancel</button>
                <form action="{{route('add.cart')}}" method="post" id="addToCart">
                    @csrf
                    <fieldset>
                        <input required type="hidden" name="id" id="modal_product_id">
                        <input required type="hidden" name="qty" id="modal_qty_hidden" value="1">
                        <input type="hidden" value="" name="color" id="modal_color_id">
                        <input type="hidden" value="" name="size" id="modal_size_id">
                        <div id="attr_values"></div>
                        <button type="submit" class="btn-add-cart" id="modal_submit_btn" disabled>
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M6.5 16.5C6.91421 16.5 7.25 16.1642 7.25 15.75C7.25 15.3358 6.91421 15 6.5 15C6.08579 15 5.75 15.3358 5.75 15.75C5.75 16.1642 6.08579 16.5 6.5 16.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14.75 16.5C15.1642 16.5 15.5 16.1642 15.5 15.75C15.5 15.3358 15.1642 15 14.75 15C14.3358 15 14 15.3358 14 15.75C14 16.1642 14.3358 16.5 14.75 16.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M1.25 1.25H4.25L6.14 11.51C6.20671 11.8504 6.38426 12.1583 6.64326 12.3857C6.90226 12.6131 7.22767 12.7467 7.565 12.765H14.3C14.6373 12.7467 14.9627 12.6131 15.2217 12.3857C15.4807 12.1583 15.6583 11.8504 15.725 11.51L17 5H5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Add to Cart
                        </button>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* --- NEW: Product Title Style --- */
.product-modal-title {
    font-size: 24px;
    font-weight: 700;
    color: #1F2937;
    margin-top: 0; /* Remove default h3 top margin */
    margin-bottom: 16px; /* Spacing between title and price */
    line-height: 1.3;
}

/* --- Modal Header Hide --- */
#cart-modal .modal-header {
    background-color: white !important;
    color: black !important;
    border-bottom: none !important;
    padding: 0 !important;
    height: 0 !important;
    overflow: hidden !important; 
    border-top-left-radius: .3rem !important;
    border-top-right-radius: .3rem !important;
}

#cart-modal .modal-header * {
    display: none !important;
}

/* --- Product Info Section --- */
.product-info-section {
    padding: 0;
}

/* --- Price Wrapper --- */
.price-wrapper {
    margin-bottom: 16px;
}

.current-price {
    display: flex;
    align-items: baseline;
    gap: 6px;
    margin-bottom: 4px;
}

.price-amount {
    font-size: 32px;
    font-weight: 700;
    color: #000000;
    line-height: 1;
}

.currency-code {
    font-size: 18px;
    font-weight: 600;
    color: #666666;
}

.original-price {
    display: flex;
    align-items: baseline;
    gap: 6px;
}

.strike-price {
    font-size: 18px;
    font-weight: 500;
    color: #999999;
    text-decoration: line-through;
    line-height: 1;
}

/* --- Stock Badge --- */
.stock-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background-color: #F0F9F4;
    border-radius: 20px;
    margin-bottom: 24px;
}

.stock-icon {
    color: #10B981;
    flex-shrink: 0;
}

.stock-badge span {
    font-size: 14px;
    font-weight: 600;
    color: #059669;
}

/* --- Section Divider --- */
.section-divider {
    height: 1px;
    background-color: #E5E7EB;
    margin: 20px 0;
}

/* --- Selection Section --- */
.selection-section {
    margin-bottom: 24px;
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.section-label {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.selected-value {
    font-size: 14px;
    font-weight: 600;
    color: #000000;
}

.options-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

/* --- Color Options --- */
.color-option-btn {
    width: 56px;
    height: 56px;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
    padding: 0;
    background: white;
}

.color-option-btn:hover:not(.disabled) {
    border-color: #000000;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.color-option-btn.active {
    border-color: #000000;
    border-width: 2.5px;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
}

.color-option-btn.disabled {
    opacity: 0.4;
    cursor: not-allowed;
    position: relative;
}

.color-option-btn.disabled::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 10%;
    right: 10%;
    height: 2px;
    background: #EF4444;
    transform: translateY(-50%) rotate(-45deg);
}

.color-option-btn img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* --- Size & Attribute Options --- */
.size-option-btn, 
.attribute-option-btn {
    padding: 10px 20px;
    border: 1.5px solid #E5E7EB;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
}

.size-option-btn:hover:not(.disabled), 
.attribute-option-btn:hover:not(.disabled) {
    border-color: #000000;
    background: #F9FAFB;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.size-option-btn.active, 
.attribute-option-btn.active {
    border-color: #000000;
    background: #000000;
    color: white;
    font-weight: 600;
}

.size-option-btn.disabled, 
.attribute-option-btn.disabled {
    opacity: 0.4;
    cursor: not-allowed;
    text-decoration: line-through;
}

/* --- Modal Footer --- */
#cart-modal .modal-footer {
    border-top: 1px solid #E5E7EB;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.btn-cancel {
    padding: 12px 24px;
    border: 1.5px solid #E5E7EB;
    border-radius: 8px;
    background: white;
    color: #6B7280;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-cancel:hover {
    border-color: #D1D5DB;
    background: #F9FAFB;
    color: #374151;
}

.btn-add-cart {
    padding: 12px 32px;
    border: none;
    border-radius: 8px;
    background: #000000;
    color: white;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-add-cart:hover:not(:disabled) {
    background: #1F2937;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

.btn-add-cart:disabled {
    background: #D1D5DB;
    color: #9CA3AF;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.btn-add-cart svg {
    width: 18px;
    height: 18px;
}

/* --- Modal Body Spacing --- */
#cart-modal .modal-body {
    padding: 24px;
}

#cart-modal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

/* --- Image Container --- */
#cart-img {
    border-radius: 12px;
    overflow: hidden;
    background: #F9FAFB;
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#cart-img img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

/* --- Responsive Design --- */
@media (max-width: 768px) {
    .product-modal-title {
        font-size: 20px;
        margin-bottom: 20px;
    }

    .price-amount {
        font-size: 28px;
    }
    
    .currency-code {
        font-size: 16px;
    }
    
    .strike-price {
        font-size: 16px;
    }
    
    #cart-modal .modal-footer {
        flex-direction: column;
    }
    
    .btn-cancel,
    .btn-add-cart {
        width: 100%;
        justify-content: center;
    }
    
    .color-option-btn {
        width: 48px;
        height: 48px;
    }
}

/* --- Loading State --- */
.btn-add-cart .fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>

@push('js')
<script>
(function() {
    'use strict';
    
    // Prevent multiple initializations
    if (window.cartModalInitialized) {
        console.log('Cart modal already initialized, skipping...');
        return;
    }
    window.cartModalInitialized = true;

    let productData = null;
    let selectedColor = null;
    let selectedSize = null;
    let selectedAttribute = null;
    let maxStock = 1;
    let isSubmitting = false;
    
    // ===== PRODUCT INFO BUTTON CLICK =====
    $(document).off('click.productinfo').on('click.productinfo', '#productInfo', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        
        resetModal();
        $('#cart-modal').modal('show');
        $('#cart-img').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    productData = response;
                    loadProductToModal(response);
                } else {
                    showError('Failed to load product information');
                }
            },
            error: function(xhr) {
                showError('Error loading product: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });
    
    function resetModal() {
        selectedColor = null;
        selectedSize = null;
        selectedAttribute = null;
        isSubmitting = false;
        
        $('#modal_product_id').val('');
        $('#modal_qty_hidden').val(1);
        $('#modal_color_id').val('');
        $('#modal_size_id').val('');
        $('#attr_values').empty();
        $('#modal_submit_btn').prop('disabled', true);
        $('#selected_color_name').text('');
        $('#selected_size_name').text('');
        // Reset product title
        $('#product-modal-title').text('');
        
        console.log('Modal reset');
    }
    
    function loadProductToModal(data) {
        const product = data.product;
        
        $('#modal_product_id').val(product.id);
        // Populate product title
        $('#product-modal-title').text(product.title);
        $('#cart-img').html(`<img src="${product.image}" alt="${product.title}" class="img-fluid">`);
        $('#item_price').text(product.regular_price);
        
        if (product.discount_price > 0) {
            $('#del_price').text(product.discount_price);
            $('#nhide').show();
        } else {
            $('#del_price').text(product.regular_price);
            $('#nhide').hide();
        }
        
        $('#stock_info').text(product.total_stock + ' in stock');
        maxStock = product.total_stock;
        
        if (data.variations) {
            if (data.variations.color_size && Object.keys(data.variations.color_size).length > 0) {
                loadColorSizeVariations(data.variations.color_size, data.allSizes);
            } else if (data.variations.size_only && data.variations.size_only.length > 0) {
                loadSizeOnlyVariations(data.variations.size_only);
            } else if (data.variations.attributes && data.variations.attributes.length > 0) {
                loadAttributeVariations(data.variations.attributes);
            } else {
                enableSubmitButton();
            }
        } else {
            enableSubmitButton();
        }
    }
    
    function loadColorSizeVariations(colorSizeData, allSizes) {
        $('#color_section').show();
        $('#size_section').show();
        
        let colorsHtml = '';
        const colors = Object.values(colorSizeData);
        
        colors.forEach(function(colorData) {
            const isOutOfStock = colorData.total_stock <= 0;
            const disabledClass = isOutOfStock ? 'disabled' : '';
            const firstImage = colorData.images && colorData.images.length > 0 ? colorData.images[0] : null;
            
            if (firstImage) {
                const imageUrl = firstImage.url || '{{ asset("uploads/product") }}/' + firstImage.name;
                colorsHtml += `
                    <button type="button" class="color-option-btn ${disabledClass}" 
                        data-color-id="${colorData.color_id}" 
                        data-color-name="${colorData.color_name}"
                        data-color-code="${colorData.color_code}"
                        data-sizes='${JSON.stringify(colorData.sizes)}'
                        ${isOutOfStock ? 'disabled' : ''}>
                        <img src="${imageUrl}" alt="${colorData.color_name}">
                    </button>
                `;
            } else {
                colorsHtml += `
                    <button type="button" class="color-option-btn ${disabledClass}" 
                        data-color-id="${colorData.color_id}" 
                        data-color-name="${colorData.color_name}"
                        data-color-code="${colorData.color_code}"
                        data-sizes='${JSON.stringify(colorData.sizes)}'
                        ${isOutOfStock ? 'disabled' : ''}>
                        <div style="width:100%;height:100%;background-color:${colorData.color_code}"></div>
                    </button>
                `;
            }
        });
        
        $('#colors').html(colorsHtml);
        $('.color-option-btn:not(.disabled):first').trigger('click');
    }
    
    function loadSizeOnlyVariations(sizeOnlyData) {
        $('#size_section').show();
        $('#color_section').hide();
        
        let sizesHtml = '';
        
        sizeOnlyData.forEach(function(size) {
            const isOutOfStock = size.stock <= 0;
            const disabledClass = isOutOfStock ? 'disabled' : '';
            
            sizesHtml += `
                <button type="button" class="size-option-btn ${disabledClass}" 
                    data-size-id="${size.id}" 
                    data-size-name="${size.name}"
                    data-stock="${size.stock}"
                    data-price="${size.price}"
                    ${isOutOfStock ? 'disabled' : ''}>
                    ${size.name}
                </button>
            `;
        });
        
        $('#sizes').html(sizesHtml);
        $('.size-option-btn:not(.disabled):first').trigger('click');
    }
    
    function loadAttributeVariations(attributesData) {
        $('#attributes_all').show();
        $('#color_section').hide();
        $('#size_section').hide();
        
        const grouped = {};
        attributesData.forEach(function(attr) {
            if (!grouped[attr.attribute_name]) {
                grouped[attr.attribute_name] = [];
            }
            grouped[attr.attribute_name].push(attr);
        });
        
        let attrsHtml = '';
        
        Object.keys(grouped).forEach(function(attrName) {
            attrsHtml += `
                <div class="selection-section">
                    <div class="section-header">
                        <span class="section-label">${attrName}</span>
                        <span class="selected-value" id="selected_attr_name"></span>
                    </div>
                    <div class="options-container">
            `;
            
            grouped[attrName].forEach(function(attr) {
                const isOutOfStock = attr.stock <= 0;
                const disabledClass = isOutOfStock ? 'disabled' : '';
                
                attrsHtml += `
                    <button type="button" class="attribute-option-btn ${disabledClass}" 
                        data-attr-id="${attr.id}" 
                        data-attr-name="${attr.name}"
                        data-stock="${attr.stock}"
                        data-price="${attr.price}"
                        ${isOutOfStock ? 'disabled' : ''}>
                        ${attr.name}
                    </button>
                `;
            });
            
            attrsHtml += `</div></div>`;
        });
        
        $('#attributes_all').html(attrsHtml);
        $('.attribute-option-btn:not(.disabled):first').trigger('click');
    }
    
    // Color selection
    $(document).off('click.colorselect').on('click.colorselect', '.color-option-btn', function() {
        if ($(this).hasClass('disabled')) return;
        
        $('.color-option-btn').removeClass('active');
        $(this).addClass('active');
        
        selectedColor = {
            id: $(this).data('color-id'),
            name: $(this).data('color-name'),
            sizes: $(this).data('sizes')
        };
        
        $('#modal_color_id').val(selectedColor.id);
        $('#selected_color_name').text(selectedColor.name);
        
        loadSizesForColor(selectedColor.sizes);
        
        selectedSize = null;
        $('#modal_size_id').val('');
        $('#selected_size_name').text('');
        
        checkCanSubmit();
    });
    
    function loadSizesForColor(sizes) {
        let sizesHtml = '';
        
        sizes.forEach(function(size) {
            const isOutOfStock = size.stock <= 0;
            const disabledClass = isOutOfStock ? 'disabled' : '';
            
            sizesHtml += `
                <button type="button" class="size-option-btn ${disabledClass}" 
                    data-size-id="${size.size_id}" 
                    data-size-name="${size.size_name}"
                    data-stock="${size.stock}"
                    data-price="${size.price}"
                    ${isOutOfStock ? 'disabled' : ''}>
                    ${size.size_name}
                </button>
            `;
        });
        
        $('#sizes').html(sizesHtml);
        $('.size-option-btn:not(.disabled):first').trigger('click');
    }
    
    // Size selection
    $(document).off('click.sizeselect').on('click.sizeselect', '.size-option-btn', function() {
        if ($(this).hasClass('disabled')) return;
        
        $('.size-option-btn').removeClass('active');
        $(this).addClass('active');
        
        selectedSize = {
            id: $(this).data('size-id'),
            name: $(this).data('size-name'),
            stock: parseInt($(this).data('stock')),
            price: parseFloat($(this).data('price'))
        };
        
        $('#modal_size_id').val(selectedSize.id);
        $('#selected_size_name').text(selectedSize.name);
        
        updatePriceAndStock(selectedSize.stock, selectedSize.price);
        checkCanSubmit();
    });
    
    // Attribute selection
    $(document).off('click.attrselect').on('click.attrselect', '.attribute-option-btn', function() {
        if ($(this).hasClass('disabled')) return;
        
        $('.attribute-option-btn').removeClass('active');
        $(this).addClass('active');
        
        selectedAttribute = {
            id: $(this).data('attr-id'),
            name: $(this).data('attr-name'),
            stock: parseInt($(this).data('stock')),
            price: parseFloat($(this).data('price'))
        };
        
        $('#modal_size_id').val(selectedAttribute.id);
        $('#selected_attr_name').text(selectedAttribute.name);
        
        updatePriceAndStock(selectedAttribute.stock, selectedAttribute.price);
        checkCanSubmit();
    });
    
    function updatePriceAndStock(stock, additionalPrice) {
        const basePrice = productData.product.discount_price > 0 
            ? productData.product.discount_price 
            : productData.product.regular_price;
        
        const finalPrice = basePrice + (additionalPrice || 0);
        
        $('#del_price').text(finalPrice);
        $('#stock_info').text(stock + ' in stock');
        
        maxStock = stock;
    }
    
    function checkCanSubmit() {
        let canSubmit = false;
        
        if ($('#color_section').is(':visible') && $('#size_section').is(':visible')) {
            canSubmit = selectedColor !== null && selectedSize !== null && maxStock > 0;
        } else if ($('#size_section').is(':visible') && !$('#color_section').is(':visible')) {
            canSubmit = selectedSize !== null && maxStock > 0;
        } else if ($('#attributes_all').is(':visible')) {
            canSubmit = selectedAttribute !== null && maxStock > 0;
        } else {
            canSubmit = maxStock > 0;
        }
        
        $('#modal_submit_btn').prop('disabled', !canSubmit);
    }
    
    function enableSubmitButton() {
        if (maxStock > 0) {
            $('#modal_submit_btn').prop('disabled', false);
        }
    }
    
    // Form submission
    $('#addToCart').off('submit.cartform').on('submit.cartform', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        
        const submitBtn = $('#modal_submit_btn');
        const originalText = submitBtn.html();
        
        if (isSubmitting || submitBtn.prop('disabled')) {
            console.log('Submission blocked - already in progress');
            return false;
        }
        
        isSubmitting = true;
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');
        
        console.log('=== SUBMITTING TO CART ===');
        console.log('Quantity: 1 (default)');
        console.log('Form Data:', $(this).serialize());
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                console.log('Server Response:', response);
                
                if (response.alert === 'Success') {
                    $.toast({
                        heading: 'Success',
                        text: response.message,
                        icon: 'success',
                        position: 'top-right',
                        stack: false,
                    });
                    
                    if (response.count) {
                        $('.cart-count-badge').text(response.count);
                    }
                    
                    setTimeout(function() {
                        $('#cart-modal').modal('hide');
                        window.location.reload();
                    }, 1500);
                } else {
                    submitBtn.html(originalText).prop('disabled', false);
                    isSubmitting = false;
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr);
                $.toast({
                    heading: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to add to cart',
                    icon: 'error',
                    position: 'top-right',
                    stack: false
                });
                submitBtn.html(originalText).prop('disabled', false);
                isSubmitting = false;
            }
        });
        
        return false;
    });
    
    function showError(message) {
        $('#cart-img').html(`<div class="alert alert-danger">${message}</div>`);
    }
    
    $('#cart-modal').on('hidden.bs.modal', function() {
        resetModal();
    });
    
    console.log('Cart modal initialized successfully');
})();
</script>
@endpush