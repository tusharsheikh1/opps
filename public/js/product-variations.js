/**
 * Product Variations Handler
 * Handles all product variation logic for add to cart modals
 */

// Global product cart handler
window.ProductCartHandler = {
    productData: null,
    selectedColor: null,
    selectedSize: null,
    selectedAttribute: null,
    maxStock: 1,
    
    init: function() {
        this.bindEvents();
    },
    
    bindEvents: function() {
        const self = this;
        
        // Product info button click
        $(document).on('click', '#productInfo, .productInfo', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            self.openModal(url);
        });
        
        // Color selection
        $(document).on('click', '.color-option-btn', function() {
            if (!$(this).hasClass('disabled')) {
                self.selectColor($(this));
            }
        });
        
        // Size selection
        $(document).on('click', '.size-option-btn', function() {
            if (!$(this).hasClass('disabled')) {
                self.selectSize($(this));
            }
        });
        
        // Attribute selection
        $(document).on('click', '.attribute-option-btn', function() {
            if (!$(this).hasClass('disabled')) {
                self.selectAttribute($(this));
            }
        });
        
        // Quantity controls
        $(document).on('click', '.value-minus', function() {
            self.decreaseQuantity();
        });
        
        $(document).on('click', '.value-plus', function() {
            self.increaseQuantity();
        });
        
        $('#modal_qty').on('change', function() {
            self.validateQuantity();
        });
        
        // Form submission
        $('#addToCart').on('submit', function(e) {
            e.preventDefault();
            self.submitForm($(this));
        });
        
        // Modal close event
        $('#cart-modal').on('hidden.bs.modal', function() {
            self.resetModal();
        });
    },
    
    openModal: function(url) {
        this.resetModal();
        $('#cart-modal').modal('show');
        $('#cart-img').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i></div>');
        
        const self = this;
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    self.productData = response;
                    self.loadProductToModal(response);
                } else {
                    self.showError('Failed to load product information');
                }
            },
            error: function(xhr) {
                self.showError('Error: ' + (xhr.responseJSON?.message || 'Unable to load product'));
            }
        });
    },
    
    resetModal: function() {
        this.selectedColor = null;
        this.selectedSize = null;
        this.selectedAttribute = null;
        this.maxStock = 1;
        
        $('#modal_product_id').val('');
        $('#modal_qty_hidden').val(1);
        $('#modal_color_id').val('');
        $('#modal_size_id').val('');
        $('#modal_qty').val(1).attr('max', 1);
        $('#attr_values').empty();
        $('#modal_submit_btn').prop('disabled', true);
        $('#selected_color_name, #selected_size_name').text('');
        
        // Hide all sections
        $('#color_section, #size_section, #attributes_all').hide();
        $('#colors, #sizes, #attributes_all').empty();
    },
    
    loadProductToModal: function(data) {
        const product = data.product;
        
        $('#modal_product_id').val(product.id);
        $('#cart-img').html(`<img src="${product.image}" alt="${product.title}" class="img-fluid rounded shadow-sm">`);
        
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
        
        $('#stock_info').text(product.total_stock + ' available');
        this.maxStock = product.total_stock;
        $('#modal_qty').attr('max', this.maxStock);
        
        // Handle variations based on priority
        if (data.variations) {
            if (data.variations.color_size && Object.keys(data.variations.color_size).length > 0) {
                this.loadColorSizeVariations(data.variations.color_size);
            } else if (data.variations.size_only && data.variations.size_only.length > 0) {
                this.loadSizeOnlyVariations(data.variations.size_only);
            } else if (data.variations.attributes && data.variations.attributes.length > 0) {
                this.loadAttributeVariations(data.variations.attributes);
            } else {
                this.enableSubmitButton();
            }
        } else {
            this.enableSubmitButton();
        }
    },
    
    loadColorSizeVariations: function(colorSizeData) {
        $('#color_section, #size_section').show();
        
        let colorsHtml = '';
        const colors = Object.values(colorSizeData);
        
        colors.forEach(colorData => {
            const isOutOfStock = colorData.total_stock <= 0;
            const firstImage = colorData.images && colorData.images.length > 0 ? colorData.images[0] : null;
            
            if (firstImage) {
                const imageUrl = firstImage.url || `/uploads/product/${firstImage.name}`;
                colorsHtml += `
                    <button type="button" class="color-option-btn ${isOutOfStock ? 'disabled' : ''}" 
                        data-color-id="${colorData.color_id}" 
                        data-color-name="${colorData.color_name}"
                        data-sizes='${JSON.stringify(colorData.sizes)}'
                        ${isOutOfStock ? 'disabled' : ''}>
                        <img src="${imageUrl}" alt="${colorData.color_name}">
                    </button>
                `;
            } else {
                colorsHtml += `
                    <button type="button" class="color-option-btn ${isOutOfStock ? 'disabled' : ''}" 
                        data-color-id="${colorData.color_id}" 
                        data-color-name="${colorData.color_name}"
                        data-sizes='${JSON.stringify(colorData.sizes)}'
                        ${isOutOfStock ? 'disabled' : ''}>
                        <div style="width:100%;height:100%;background-color:${colorData.color_code}"></div>
                    </button>
                `;
            }
        });
        
        $('#colors').html(colorsHtml);
        $('.color-option-btn:not(.disabled):first').trigger('click');
    },
    
    loadSizeOnlyVariations: function(sizeOnlyData) {
        $('#size_section').show();
        $('#color_section').hide();
        
        let sizesHtml = '';
        sizeOnlyData.forEach(size => {
            const isOutOfStock = size.stock <= 0;
            sizesHtml += `
                <button type="button" class="size-option-btn ${isOutOfStock ? 'disabled' : ''}" 
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
    },
    
    loadAttributeVariations: function(attributesData) {
        $('#attributes_all').show();
        $('#color_section, #size_section').hide();
        
        const grouped = {};
        attributesData.forEach(attr => {
            if (!grouped[attr.attribute_name]) {
                grouped[attr.attribute_name] = [];
            }
            grouped[attr.attribute_name].push(attr);
        });
        
        let attrsHtml = '';
        Object.keys(grouped).forEach(attrName => {
            attrsHtml += `
                <div class="col-12 pl-0 mb-3">
                    <p><strong>Select ${attrName}:</strong></p>
                    <div class="d-flex flex-wrap">
            `;
            
            grouped[attrName].forEach(attr => {
                const isOutOfStock = attr.stock <= 0;
                attrsHtml += `
                    <button type="button" class="attribute-option-btn ${isOutOfStock ? 'disabled' : ''}" 
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
    },
    
    selectColor: function($btn) {
        $('.color-option-btn').removeClass('active');
        $btn.addClass('active');
        
        this.selectedColor = {
            id: $btn.data('color-id'),
            name: $btn.data('color-name'),
            sizes: $btn.data('sizes')
        };
        
        $('#modal_color_id').val(this.selectedColor.id);
        $('#selected_color_name').text(this.selectedColor.name);
        
        this.loadSizesForColor(this.selectedColor.sizes);
        this.selectedSize = null;
        $('#modal_size_id').val('');
        $('#selected_size_name').text('');
        this.checkCanSubmit();
    },
    
    loadSizesForColor: function(sizes) {
        let sizesHtml = '';
        sizes.forEach(size => {
            const isOutOfStock = size.stock <= 0;
            sizesHtml += `
                <button type="button" class="size-option-btn ${isOutOfStock ? 'disabled' : ''}" 
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
    },
    
    selectSize: function($btn) {
        $('.size-option-btn').removeClass('active');
        $btn.addClass('active');
        
        this.selectedSize = {
            id: $btn.data('size-id'),
            name: $btn.data('size-name'),
            stock: parseInt($btn.data('stock')),
            price: parseFloat($btn.data('price') || 0)
        };
        
        $('#modal_size_id').val(this.selectedSize.id);
        $('#selected_size_name').text(this.selectedSize.name);
        
        this.updatePriceAndStock(this.selectedSize.stock, this.selectedSize.price);
        this.checkCanSubmit();
    },
    
    selectAttribute: function($btn) {
        $('.attribute-option-btn').removeClass('active');
        $btn.addClass('active');
        
        this.selectedAttribute = {
            id: $btn.data('attr-id'),
            name: $btn.data('attr-name'),
            stock: parseInt($btn.data('stock')),
            price: parseFloat($btn.data('price') || 0)
        };
        
        $('#modal_size_id').val(this.selectedAttribute.id);
        this.updatePriceAndStock(this.selectedAttribute.stock, this.selectedAttribute.price);
        this.checkCanSubmit();
    },
    
    updatePriceAndStock: function(stock, additionalPrice) {
        const basePrice = this.productData.product.discount_price > 0 
            ? this.productData.product.discount_price 
            : this.productData.product.regular_price;
        
        const finalPrice = basePrice + (additionalPrice || 0);
        $('#del_price').text(finalPrice);
        $('#stock_info').text(stock + ' available');
        
        this.maxStock = stock;
        $('#modal_qty').attr('max', this.maxStock);
        
        const currentQty = parseInt($('#modal_qty').val());
        if (currentQty > this.maxStock) {
            $('#modal_qty').val(this.maxStock);
            $('#modal_qty_hidden').val(this.maxStock);
        }
    },
    
    checkCanSubmit: function() {
        let canSubmit = false;
        
        if ($('#color_section').is(':visible') && $('#size_section').is(':visible')) {
            canSubmit = this.selectedColor !== null && this.selectedSize !== null && this.maxStock > 0;
        } else if ($('#size_section').is(':visible') && !$('#color_section').is(':visible')) {
            canSubmit = this.selectedSize !== null && this.maxStock > 0;
        } else if ($('#attributes_all').is(':visible')) {
            canSubmit = this.selectedAttribute !== null && this.maxStock > 0;
        } else {
            canSubmit = this.maxStock > 0;
        }
        
        $('#modal_submit_btn').prop('disabled', !canSubmit);
    },
    
    enableSubmitButton: function() {
        if (this.maxStock > 0) {
            $('#modal_submit_btn').prop('disabled', false);
        }
    },
    
    decreaseQuantity: function() {
        const input = $('#modal_qty');
        let value = parseInt(input.val());
        if (value > 1) {
            value--;
            input.val(value);
            $('#modal_qty_hidden').val(value);
        }
    },
    
    increaseQuantity: function() {
        const input = $('#modal_qty');
        let value = parseInt(input.val());
        const max = parseInt(input.attr('max'));
        if (value < max) {
            value++;
            input.val(value);
            $('#modal_qty_hidden').val(value);
        }
    },
    
    validateQuantity: function() {
        const input = $('#modal_qty');
        let value = parseInt(input.val());
        const max = parseInt(input.attr('max'));
        
        if (value < 1) value = 1;
        if (value > max) value = max;
        
        input.val(value);
        $('#modal_qty_hidden').val(value);
    },
    
    submitForm: function($form) {
        const submitBtn = $('#modal_submit_btn');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');
        
        const self = this;
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.alert === 'Success') {
                    self.showToast('Success', response.message, 'success');
                    
                    if (response.count) {
                        $('.cart-count-badge').text(response.count);
                    }
                    
                    $('#cart-modal').modal('hide');
                } else {
                    self.showToast('Warning', response.message, 'warning');
                }
            },
            error: function(xhr) {
                self.showToast('Error', xhr.responseJSON?.message || 'Failed to add to cart', 'error');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
                self.checkCanSubmit();
            }
        });
    },
    
    showError: function(message) {
        $('#cart-img').html(`<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${message}</div>`);
    },
    
    showToast: function(heading, text, icon) {
        $.toast({
            heading: heading,
            text: text,
            icon: icon,
            position: 'top-right',
            stack: false,
            hideAfter: 3000
        });
    }
};

// Initialize when document is ready
$(document).ready(function() {
    window.ProductCartHandler.init();
});