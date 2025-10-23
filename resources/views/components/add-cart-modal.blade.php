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
                        <div id="cart-img">
                            </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row ml-1">
                            <div class="col-12 pl-0 mb-2" id="nhide">
                                <p><strong>Regular Price: </strong><span id="item_price"></span> {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</p>
                            </div>
                            <div class="col-12 pl-0 mb-2">
                                <p><strong id="nprice">Price: </strong><span id="del_price"></span> {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</p>
                            </div>
                            <div class="col-12 pl-0 mb-2">
                                <p><strong>Stock: </strong><span id="stock_info">Loading...</span></p>
                            </div>
                        </div>
                        
                        <div class="row ml-1 mb-3" id="color_section" style="display:none;">
                            <div class="col-12 pl-0 mb-2">
                                <p><strong>Select Color: <span id="selected_color_name" class="text-primary"></span></strong></p>
                            </div>
                            <div class="col-12 pl-0">
                                <div class="btn-group-toggle" data-toggle="buttons" id="colors">
                                    </div>
                            </div>
                        </div>
                        
                        <div class="row ml-1 mb-3" id="size_section" style="display:none;">
                            <div class="col-12 pl-0 mb-2">
                                <p><strong>Select Size: <span id="selected_size_name" class="text-primary"></span></strong></p>
                            </div>
                            <div class="col-12 pl-0">
                                <div id="sizes" class="d-flex flex-wrap gap-2">
                                    </div>
                            </div>
                        </div>
                        
                        <div class="row ml-1 mb-3" id="attributes_all">
                            </div>
                        
                        <div class="row ml-1 mb-3">
                            <div class="col-12 pl-0 mb-2">
                                <p><strong>Quantity:</strong></p>
                            </div>
                            <div class="col-12 pl-0">
                                <div class="quantity">
                                    <div class="quantity-select">
                                        <div class="entry value-minus">&nbsp;</div>
                                        <input type="text" class="entry value" value="1" min="1" max="1" id="modal_qty">
                                        <div class="entry value-plus active">&nbsp;</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <form action="{{route('add.cart')}}" method="post" id="addToCart">
                    @csrf
                    <fieldset>
                        <input required type="hidden" name="id" id="modal_product_id">
                        <input required type="hidden" name="qty" id="modal_qty_hidden" value="1">
                        <input type="hidden" value="" name="color" id="modal_color_id">
                        <input type="hidden" value="" name="size" id="modal_size_id">
                        <div id="attr_values"></div>
                        <button type="submit" class="btn btn-success" id="modal_submit_btn" disabled>
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* --- FIXES: Button and Header CSS --- */
/* Enforce black button background and white text for all states */
#modal_submit_btn,
#modal_submit_btn:hover,
#modal_submit_btn:focus,
#modal_submit_btn:active,
#modal_submit_btn:disabled {
    background-color: black !important;
    border-color: black !important;
    color: white !important;
}

/* Fix: Remove the blue header completely. Note: The HTML is left as is, this CSS hides it. */
#cart-modal .modal-header {
    background-color: white !important;
    color: black !important;
    border-bottom: none !important;
    /* Reduce height and padding, then hide children content */
    padding: 0 !important;
    height: 0 !important;
    overflow: hidden !important; 
    border-top-left-radius: .3rem !important; /* Ensure modal corner shape is correct */
    border-top-right-radius: .3rem !important;
}

#cart-modal .modal-header * {
    display: none !important; /* Force hide all children */
}
/* --- EXISTING STYLES --- */
    .color-option-btn {
        width: 60px;
        height: 60px;
        border: 2px solid #ddd;
        border-radius: 8px;
        margin: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .color-option-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .color-option-btn.active {
        border-color: #007bff;
        border-width: 3px;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.2);
    }
    
    .color-option-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .color-option-btn img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .size-option-btn, .attribute-option-btn {
        padding: 8px 16px;
        margin: 5px;
        border: 2px solid #ddd;
        border-radius: 6px;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .size-option-btn:hover, .attribute-option-btn:hover {
        border-color: #007bff;
        background: #f8f9fa;
    }
    
    .size-option-btn.active, .attribute-option-btn.active {
        border-color: #007bff;
        background: #007bff;
        color: white;
    }
    
    .size-option-btn.disabled, .attribute-option-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        text-decoration: line-through;
    }
    
    .quantity-select {
        display: inline-flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 6px;
        overflow: hidden;
    }
    
    .quantity-select .entry {
        padding: 8px 15px;
        border: none;
        text-align: center;
        font-size: 16px;
        font-weight: 500;
    }
    
    .quantity-select .value-minus,
    .quantity-select .value-plus {
        background: #f8f9fa;
        cursor: pointer;
        user-select: none;
        font-size: 18px;
        font-weight: bold;
        transition: background 0.3s ease;
        width: 40px;
    }
    
    .quantity-select .value-minus:hover,
    .quantity-select .value-plus:hover {
        background: #e9ecef;
    }
    
    .quantity-select .value {
        width: 60px;
        background: white;
    }
    
    .gap-2 {
        gap: 0.5rem;
    }
</style>

@push('js')
<script>
$(document).ready(function() {
    let productData = null;
    let selectedColor = null;
    let selectedSize = null;
    let selectedAttribute = null;
    let maxStock = 1;
    
    // When "Add to Cart" button is clicked on product card
    $(document).on('click', '#productInfo', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        
        // Reset modal
        resetModal();
        
        // Show loading state
        $('#cart-modal').modal('show');
        $('#cart-img').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
        
        // Fetch product info
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
        $('#modal_product_id').val('');
        $('#modal_qty_hidden').val(1);
        $('#modal_color_id').val('');
        $('#modal_size_id').val('');
        $('#modal_qty').val(1).attr('max', 1);
        $('#attr_values').empty();
        $('#modal_submit_btn').prop('disabled', true);
        $('#selected_color_name').text('');
        $('#selected_size_name').text('');
    }
    
    function loadProductToModal(data) {
        const product = data.product;
        
        // Set product ID
        $('#modal_product_id').val(product.id);
        
        // Set product image
        $('#cart-img').html(`<img src="${product.image}" alt="${product.title}" class="img-fluid rounded">`);
        
        // Set prices
        $('#item_price').text(product.regular_price);
        
        if (product.discount_price > 0) {
            $('#del_price').text(product.discount_price);
            $('#nprice').text('Discount Price:');
            $('#nhide').show();
        } else {
            $('#del_price').text(product.regular_price);
            $('#nprice').text('Price:');
            $('#nhide').hide();
        }
        
        // Set initial stock
        $('#stock_info').text(product.total_stock + ' available');
        maxStock = product.total_stock;
        $('#modal_qty').attr('max', maxStock);
        
        // Handle variations
        if (data.variations) {
            // Priority 1: Color-Size variations
            if (data.variations.color_size && Object.keys(data.variations.color_size).length > 0) {
                loadColorSizeVariations(data.variations.color_size, data.allSizes);
            }
            // Priority 2: Size-only variations
            else if (data.variations.size_only && data.variations.size_only.length > 0) {
                loadSizeOnlyVariations(data.variations.size_only);
            }
            // Priority 3: Attribute variations
            else if (data.variations.attributes && data.variations.attributes.length > 0) {
                loadAttributeVariations(data.variations.attributes);
            }
            // Simple product
            else {
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
        
        // Convert object to array for iteration
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
                        <div class="text-center small mt-1">${colorData.color_name}</div>
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
                        <div class="text-center small mt-1">${colorData.color_name}</div>
                    </button>
                `;
            }
        });
        
        $('#colors').html(colorsHtml);
        
        // Auto-select first available color
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
        
        // Auto-select first available size
        $('.size-option-btn:not(.disabled):first').trigger('click');
    }
    
    function loadAttributeVariations(attributesData) {
        $('#attributes_all').show();
        $('#color_section').hide();
        $('#size_section').hide();
        
        // Group attributes by attribute name
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
                <div class="col-12 pl-0 mb-3">
                    <p><strong>Select ${attrName}: <span id="selected_attr_name" class="text-primary"></span></strong></p>
                    <div class="d-flex flex-wrap gap-2">
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
            
            attrsHtml += `
                    </div>
                </div>
            `;
        });
        
        $('#attributes_all').html(attrsHtml);
        
        // Auto-select first available attribute
        $('.attribute-option-btn:not(.disabled):first').trigger('click');
    }
    
    // Color selection handler
    $(document).on('click', '.color-option-btn', function() {
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
        
        // Load sizes for this color
        loadSizesForColor(selectedColor.sizes);
        
        // Reset size selection
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
        
        // Auto-select first available size
        $('.size-option-btn:not(.disabled):first').trigger('click');
    }
    
    // Size selection handler (works for both color-size and size-only)
    $(document).on('click', '.size-option-btn', function() {
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
    
    // Attribute selection handler
    $(document).on('click', '.attribute-option-btn', function() {
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
        $('#stock_info').text(stock + ' available');
        
        maxStock = stock;
        $('#modal_qty').attr('max', maxStock);
        
        // Reset quantity if it exceeds new max
        const currentQty = parseInt($('#modal_qty').val());
        if (currentQty > maxStock) {
            $('#modal_qty').val(maxStock);
            $('#modal_qty_hidden').val(maxStock);
        }
    }
    
    function checkCanSubmit() {
        let canSubmit = false;
        
        // Check if we have color-size product
        if ($('#color_section').is(':visible') && $('#size_section').is(':visible')) {
            canSubmit = selectedColor !== null && selectedSize !== null && maxStock > 0;
        }
        // Check if we have size-only product
        else if ($('#size_section').is(':visible') && !$('#color_section').is(':visible')) {
            canSubmit = selectedSize !== null && maxStock > 0;
        }
        // Check if we have attribute product
        else if ($('#attributes_all').is(':visible')) {
            canSubmit = selectedAttribute !== null && maxStock > 0;
        }
        // Simple product
        else {
            canSubmit = maxStock > 0;
        }
        
        $('#modal_submit_btn').prop('disabled', !canSubmit);
    }
    
    function enableSubmitButton() {
        if (maxStock > 0) {
            $('#modal_submit_btn').prop('disabled', false);
        }
    }
    
    // --- FINAL FIX: UNBINDING AND REBINDING EVENTS ---
    
    // Safely remove any existing 'click' handlers on the document specifically targeting '.value-minus'
    $(document).off('click', '.value-minus').on('click', '.value-minus', function(e) {
        e.stopImmediatePropagation(); 
        const input = $('#modal_qty');
        let value = parseInt(input.val());
        if (value > 1) {
            // Update visible input and hidden input directly
            value = value - 1;
            input.val(value);
            $('#modal_qty_hidden').val(value);
        }
    });
    
    // Safely remove any existing 'click' handlers on the document specifically targeting '.value-plus'
    $(document).off('click', '.value-plus').on('click', '.value-plus', function(e) {
        e.stopImmediatePropagation(); 
        const input = $('#modal_qty');
        let value = parseInt(input.val());
        const max = parseInt(input.attr('max'));
        if (value < max) {
            // Update visible input and hidden input directly
            value = value + 1;
            input.val(value);
            $('#modal_qty_hidden').val(value);
        }
    });
    // --- END FINAL FIX ---
    
    // Centralized change handler for validation and hidden input update
    $('#modal_qty').on('change', function() {
        let value = parseInt($(this).val());
        const max = parseInt($(this).attr('max'));
        
        // Validation and Clamping
        if (isNaN(value) || value < 1) value = 1;
        if (value > max) value = max;
        
        $(this).val(value);
        // Single source of truth for the hidden input
        $('#modal_qty_hidden').val(value);
    });
    
    // Form submission
    $('#addToCart').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#modal_submit_btn');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                // Restore button state here
                submitBtn.html(originalText);
                checkCanSubmit(); 

                if (response.alert === 'Success') {
                    $.toast({
                        heading: 'Success',
                        text: response.message,
                        icon: 'success',
                        position: 'top-right',
                        stack: false,
                        // Removed afterHidden callback for reload
                    });
                    
                    // Update cart count visually
                    if (response.count) {
                        $('.cart-count-badge').text(response.count);
                    }
                    
                    // ⭐ NEW: Wait for 3 seconds before reloading the page to give time to see the success state
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000); 
                }
            },
            error: function(xhr) {
                $.toast({
                    heading: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to add to cart',
                    icon: 'error',
                    position: 'top-right',
                    stack: false
                });
            },
            complete: function() {
                // Ensure button is re-enabled/restored on failure or when no success action is taken
                // This is also a fallback for the success block's restoration if the flow is interrupted
                submitBtn.prop('disabled', false).html(originalText);
                checkCanSubmit();
            }
        });
    });
    
    function showError(message) {
        $('#cart-img').html(`<div class="alert alert-danger">${message}</div>`);
    }
    
    // Reset modal when closed
    $('#cart-modal').on('hidden.bs.modal', function() {
        resetModal();
    });
});
</script>
@endpush