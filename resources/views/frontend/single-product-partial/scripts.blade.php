{{-- frontend/single-product-partial/scripts.blade.php --}}
<script src="{{ asset('/') }}assets/frontend/js/jquery.flexslider.js"></script>
<script src="{{ asset('/') }}assets/frontend/js/image-zoom.js"></script>
<script src="{{ asset('/') }}assets/frontend/js/toast.min.js"></script>
<script src="https://vjs.zencdn.net/8.3.0/video.min.js"></script>

<script>
// ======================================
// GLOBAL FUNCTIONS - Accessible Across Components
// ======================================

// Toggle description function - needs to be global
window.spToggleDescription = function() {
    const truncatedText = document.getElementById('sp-truncated-text');
    const fullText = document.getElementById('sp-full-text');
    const seeMoreBtn = document.getElementById('spSeeMoreBtn');
    
    if (fullText && truncatedText && seeMoreBtn) {
        if (fullText.style.display === 'none') {
            truncatedText.style.display = 'none';
            fullText.style.display = 'inline';
            seeMoreBtn.textContent = 'কম দেখুন';
        } else {
            truncatedText.style.display = 'inline';
            fullText.style.display = 'none';
            seeMoreBtn.textContent = 'আরও দেখুন';
        }
    }
}

// Global toast notification function
window.spShowToast = function(heading, message, icon) {
    if (typeof $.toast === 'function') {
        $.toast({
            heading: heading,
            text: message,
            icon: icon,
            position: 'top-right',
            stack: false,
            hideAfter: 3000
        });
    } else {
        // Fallback to custom notification
        let alertClass = icon === 'success' ? 'alert-success' : (icon === 'warning' ? 'alert-warning' : 'alert-danger');
        let notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
            '<strong>' + heading + '</strong> ' + message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>');
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.alert('close');
        }, 3000);
    }
}

// Global price calculation function
window.spUpdatePriceCalculation = function(quantity) {
    var basePrice = parseFloat($('#sp_dynamic_price').data('base-price')) || parseFloat($('#sp_dynamic_price').text());
    var totalPrice = basePrice * quantity;
    
    // Update displayed price
    $('#sp_dynamic_price').html(totalPrice);
    
    // Update form prices
    $('#spBuyNowJust').val(totalPrice);
    $('#spBuyNowDynamicPrice').val(basePrice);
    $('.sp_dynamic_price').val(basePrice);
    $('#sp_just').val(totalPrice);
    
    // Update savings calculation if there's a discount
    var originalPriceElement = $('.sp-original-price');
    if (originalPriceElement.length > 0) {
        var originalPriceText = originalPriceElement.text().replace('৳', '');
        var originalPrice = parseFloat(originalPriceText);
        if (!isNaN(originalPrice)) {
            // Get base original price
            var baseOriginalPrice = originalPriceElement.data('base-price') || originalPrice;
            var totalOriginalPrice = baseOriginalPrice * quantity;
            var totalSavings = totalOriginalPrice - totalPrice;
            
            // Update original price display
            originalPriceElement.text('৳' + totalOriginalPrice);
            
            // Update savings text
            $('.sp-savings-text').text('আপনি সাশ্রয় করবেন ৳' + totalSavings);
        }
    }
}

// Global form validation function
window.spValidateForm = function(form) {
    let hasErrors = false;
    
    // Validate required attributes
    form.find('input[type="radio"]:checked').each(function() {
        let name = $(this).attr('name');
        if (name && name !== 'color' && $('input#sp_' + name).val() === 'blank') {
            hasErrors = true;
            spShowToast('Error', 'Please select all required options', 'error');
            return false;
        }
    });
    
    return !hasErrors;
}

// Global image modal function
window.spOpenImageModal = function(imageSrc) {
    let modal = $('<div class="sp-image-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center;">' +
        '<div style="position: relative; max-width: 90%; max-height: 90%;">' +
        '<img src="' + imageSrc + '" style="max-width: 100%; max-height: 100%; object-fit: contain;">' +
        '<button type="button" style="position: absolute; top: -40px; right: 0; background: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 20px;">&times;</button>' +
        '</div>' +
        '</div>');
    
    $('body').append(modal);
    
    // Close modal on click
    modal.on('click', function(e) {
        if (e.target === this || e.target.tagName === 'BUTTON') {
            modal.remove();
        }
    });
}

// Global cart update function
window.spUpdateCartUI = function(response) {
    if (response.subtotal) {
        $('span#total-cart-amount').text(response.subtotal);
        $('.cart-count').text(response.count || 0);
    }
    
    // Update cart icon animation
    $('.action-item .fa-shopping-bag').addClass('bounce');
    setTimeout(() => {
        $('.action-item .fa-shopping-bag').removeClass('bounce');
    }, 600);
}

// Global loading state management
window.spSetLoadingState = function(element, loading) {
    if (loading) {
        element.prop('disabled', true);
        element.data('original-text', element.html());
        element.html('<span class="sp-loading"></span> Loading...');
    } else {
        element.prop('disabled', false);
        element.html(element.data('original-text'));
    }
}

// Global error handler
window.spHandleError = function(xhr, defaultMessage) {
    let message = defaultMessage || 'Something went wrong. Please try again.';
    
    if (xhr.responseJSON && xhr.responseJSON.message) {
        message = xhr.responseJSON.message;
    } else if (xhr.responseText) {
        try {
            let response = JSON.parse(xhr.responseText);
            if (response.message) {
                message = response.message;
            }
        } catch (e) {
            // Use default message
        }
    }
    
    spShowToast('Error', message, 'error');
}

console.log('🚀 Single Product Global Functions Initialized');
</script>

@auth
<script>
    $(document).ready(function() {
        $('.dropdown-toggle').dropdown();
        $(document).on('click', '#sp-comment-reply', function(e) {
            e.preventDefault();

            var replyBox = document.getElementsByClassName("sp-reply-box");
            for (var i = 0; i < replyBox.length; i++) {
                $(replyBox[i]).empty();
            }
            let id = $(this).data('id');
            let slug = $(this).data('slug');
            let children = $(this).parent().parent().parent().parent().find('.sp-reply-box');
            let url = '/product/comment/reply/' + slug + '/' + id
            let csrf = $('meta[name="csrf-token"]').attr('content')
            let html = '<div class="sp-comment-form" style="margin-top: 20px;">';
            html += '<form action="' + url + '" method="post">';
            html += '<input type="hidden" name="_token" value="' + csrf + '">';
            html += '<div class="form-group">';
            html += '<label for="reply">Reply</label>';
            html += '<textarea required name="reply" id="reply" class="form-control" placeholder="Write your reply here..."></textarea>';
            html += '<small class="form-text text-danger"></small>';
            html += '</div>';
            html += '<div class="form-group">';
            html += '<button type="submit" class="sp-btn-buy-now">Submit Reply</button>';
            html += '</div>';
            html += '</form>';
            html += '</div>';

            $(children).html(html);
        })
    });
</script>
@endauth

<script>
    $(document).ready(function() {
        // Initialize Flexslider
        $('.flexslider').flexslider({
            animation: "slide",
            controlNav: "thumbnails",
            slideshow: false
        });

        // Store initial base price
        var initialBasePrice = parseFloat($('#sp_dynamic_price').text());
        $('#sp_dynamic_price').data('base-price', initialBasePrice);
        
        // Store initial original price if exists
        var originalPriceElement = $('.sp-original-price');
        if (originalPriceElement.length > 0) {
            var initialOriginalPrice = parseFloat(originalPriceElement.text().replace('৳', ''));
            originalPriceElement.data('base-price', initialOriginalPrice);
        }

        // Function to get current base price
        function getCurrentBasePrice() {
            return parseFloat($('#sp_dynamic_price').data('base-price')) || parseFloat($('#sp_dynamic_price').text());
        }

        // Function to set base price
        function setBasePrice(price) {
            $('#sp_dynamic_price').data('base-price', price);
        }

        // Function to update price calculations
        function updatePriceCalculation(quantity) {
            var basePrice = getCurrentBasePrice();
            var totalPrice = basePrice * quantity;
            
            // Update displayed price
            $('#sp_dynamic_price').html(totalPrice);
            
            // Update form prices
            $('#spBuyNowJust').val(totalPrice);
            $('#spBuyNowDynamicPrice').val(basePrice);
            $('.sp_dynamic_price').val(basePrice);
            $('#sp_just').val(totalPrice);
            
            // Update savings calculation if there's a discount
            var originalPriceElement = $('.sp-original-price');
            if (originalPriceElement.length > 0) {
                var originalPriceText = originalPriceElement.text().replace('৳', '');
                var originalPrice = parseFloat(originalPriceText);
                if (!isNaN(originalPrice)) {
                    // Get base original price
                    var baseOriginalPrice = originalPriceElement.data('base-price') || originalPrice;
                    var totalOriginalPrice = baseOriginalPrice * quantity;
                    var totalSavings = totalOriginalPrice - totalPrice;
                    
                    // Update original price display
                    originalPriceElement.text('৳' + totalOriginalPrice);
                    
                    // Update savings text
                    $('.sp-savings-text').text('আপনি সাশ্রয় করবেন ৳' + totalSavings);
                }
            }
        }

        // Quantity controls
        $('.sp-value-plus').on('click', function() {
            var divUpd = $(this).parent().find('.sp-value'),
                newVal = parseInt(divUpd.val(), 10) + 1;
            divUpd.val(newVal);
            
            // Update all quantity fields
            $('input#sp_qty').val(newVal);
            $('input#spBuyNowQty').val(newVal);
            
            // Update price calculation
            updatePriceCalculation(newVal);
        });

        $('.sp-value-minus').on('click', function() {
            var divUpd = $(this).parent().find('.sp-value'),
                newVal = parseInt(divUpd.val(), 10) - 1;
            if (newVal >= 1) {
                divUpd.val(newVal);
                
                // Update all quantity fields
                $('input#sp_qty').val(newVal);
                $('input#spBuyNowQty').val(newVal);
                
                // Update price calculation
                updatePriceCalculation(newVal);
            }
        });

        // Color selection
        $(document).on('click', '.color', function(e) {
            $('.color').removeClass('active');
            $(this).addClass('active');
            let value = $(this).children('input[name="color"]').val();
            
            // Update all color fields
            $('input#sp_color').val(value);
            $('input#spBuyNowColor').val(value);
            
            var qnt = parseInt($('input.sp-entry.sp-value').val());
            let formData = $('#spAddToCart').serialize();

            $.ajax({
                type: 'POST',
                url: '/get/color/price',
                data: formData,
                dataType: "JSON",
                success: function(response) {
                    // Update base price
                    var newBasePrice = parseFloat(response);
                    setBasePrice(newBasePrice);
                    
                    // Recalculate total price
                    updatePriceCalculation(qnt);
                }
            });
            
            // Update image slider
            const targetLi = document.querySelector(`[data-cl="${value}"]`);
            if(targetLi) targetLi.click();
        });

        // Attribute selection
        $(document).on('click', '.get_attri_price', function(e) {
            let formData = $('#spAddToCart').serialize();
            var qnt = parseInt($('input.sp-entry.sp-value').val());
            
            // Update attribute values for both forms
            let attrName = $(this).attr('name');
            let attrValue = $(this).val();
            $('input#sp_' + attrName).val(attrValue);
            $('input#spBuyNow' + attrName.charAt(0).toUpperCase() + attrName.slice(1)).val(attrValue);
            
            $.ajax({
                type: 'POST',
                url: '/get/attr/price',
                data: formData,
                dataType: "JSON",
                success: function(response) {
                    // Update base price
                    var newBasePrice = parseFloat(response);
                    setBasePrice(newBasePrice);
                    
                    // Recalculate total price
                    updatePriceCalculation(qnt);
                }
            });
        });

        // Enhanced add to cart with better error handling
        $(document).on('submit', '#spAddToCart', function(e) {
            e.preventDefault();

            // Disable button to prevent double submission
            let submitBtn = $(this).find('button[type="submit"]');
            let originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> যোগ করা হচ্ছে...');

            let url = $(this).attr('action');
            let type = $(this).attr('method');
            let formData = $(this).serialize();

            $.ajax({
                type: type,
                url: url,
                data: formData,
                dataType: 'JSON',
                success: function(response) {
                    let message = '<div class="col-12 mt-2">';
                    if (response.alert == 'Warning') {
                        message += '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                    } else {
                        message += '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                    }
                    message += '<strong>' + response.alert + ' </strong> ' + response.message;
                    message += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                    message += '<span aria-hidden="true">&times;</span>';
                    message += '</button>';
                    message += '</div>';
                    message += '</div>';

                    if (response.subtotal) {
                        $('span#total-cart-amount').text(response.subtotal);
                    }
                    $('#sp-response-message').html(message);
                    
                    // Auto-hide message
                    setTimeout(() => {
                        $('#sp-response-message').empty();
                    }, 4000);
                },
                error: function(xhr) {
                    let message = '<div class="col-12 mt-2">';
                    message += '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                    message += '<strong>Error! </strong>';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message += xhr.responseJSON.message;
                    } else {
                        message += 'Something went wrong. Please try again.';
                    }
                    
                    message += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                    message += '<span aria-hidden="true">&times;</span>';
                    message += '</button>';
                    message += '</div>';
                    message += '</div>';

                    $('#sp-response-message').html(message);
                    setTimeout(() => {
                        $('#sp-response-message').empty();
                    }, 6000);
                },
                complete: function() {
                    // Re-enable button
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Form validation before submission
        $('form').on('submit', function(e) {
            let form = $(this);
            let hasErrors = false;
            
            // Validate required attributes
            form.find('input[type="radio"]:checked').each(function() {
                let name = $(this).attr('name');
                if (name && name !== 'color' && $('input#sp_' + name).val() === 'blank') {
                    hasErrors = true;
                    alert('Please select all required options');
                    return false;
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                return false;
            }
        });

        // Global escape key handler
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.sp-image-modal').remove();
                $('.dropdown-menu').removeClass('show');
            }
        });
        
        // Global click outside handler
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').removeClass('show');
            }
        });
        
        // Global smooth scroll
        $(document).on('click', 'a[href^="#"]', function(e) {
            e.preventDefault();
            let target = $($(this).attr('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        const mediaQuery = window.matchMedia('(min-width: 768px)')
        if (mediaQuery.matches) {
            $('.my-gallery-image').each(function() {
                $(this).imageZoom({
                    zoom: 200
                });
            });
        }
    });
</script>

<!-- Single Product Specific Scripts -->
<script>
    $(document).ready(function() {
        // Initialize video players if any
        if (typeof videojs !== 'undefined') {
            $('.video-js').each(function() {
                videojs(this);
            });
        }

        // Handle accordion navigation
        $('.sp-accordion-navigation button').on('click', function() {
            $('.sp-accordion-navigation button').addClass('collapsed');
            $(this).removeClass('collapsed');
        });

        // Handle wishlist form submission
        $('form[action*="wishlist"]').on('submit', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let url = form.attr('action');
            let formData = form.serialize();
            
            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                success: function(response) {
                    // Reload page to update wishlist status
                    window.location.reload();
                },
                error: function(xhr) {
                    spShowToast('Error', 'Failed to update wishlist', 'error');
                }
            });
        });

        // Handle share links
        $('.sp-share-groups a').on('click', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            
            // Open share window
            window.open(url, 'share', 'width=600,height=400,scrollbars=yes,resizable=yes');
        });

        // Handle image gallery clicks
        $('.my-gallery-image').on('click', function(e) {
            e.preventDefault();
            
            // Get image source
            let imageSrc = $(this).attr('src');
            
            // Use global image modal function
            spOpenImageModal(imageSrc);
        });

        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            let imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        let img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(function(img) {
                imageObserver.observe(img);
            });
        }

        // Handle responsive navigation for small screens
        if (window.innerWidth < 768) {
            $('.sp-accordion-navigation').addClass('mobile-nav');
            
            // Convert to dropdown on mobile
            let mobileSelect = $('<select class="form-control mb-3" id="sp-mobile-nav"></select>');
            $('.sp-accordion-navigation button').each(function() {
                let target = $(this).data('target');
                let text = $(this).text().trim();
                mobileSelect.append('<option value="' + target + '">' + text + '</option>');
            });
            
            $('.sp-accordion-navigation').before(mobileSelect);
            
            mobileSelect.on('change', function() {
                let target = $(this).val();
                $('.collapse').removeClass('show');
                $(target).addClass('show');
            });
        }

        // Performance optimization: Debounce resize events
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Handle responsive changes
                if (window.innerWidth < 768) {
                    $('.sp-related-sidebar').removeClass('sticky-top');
                } else {
                    $('.sp-related-sidebar').addClass('sticky-top');
                }
            }, 250);
        });

        // Enhanced touch support for mobile
        if ('ontouchstart' in window) {
            $('.sp-btn-buy-now, .sp-btn-cart, .sp-quantity-btn').on('touchstart', function() {
                $(this).addClass('active');
            }).on('touchend', function() {
                $(this).removeClass('active');
            });
        }

        // Add to cart animation
        $(document).on('click', '[data-add-to-cart]', function(e) {
            // Create floating cart icon animation
            const cartIcon = document.createElement('i');
            cartIcon.className = 'fas fa-shopping-bag';
            cartIcon.style.cssText = `
                position: fixed;
                top: ${e.clientY}px;
                left: ${e.clientX}px;
                color: var(--sp-primary);
                font-size: 20px;
                pointer-events: none;
                z-index: 9999;
                animation: flyToCart 1s ease-out forwards;
            `;
            
            document.body.appendChild(cartIcon);
            
            setTimeout(() => {
                cartIcon.remove();
            }, 1000);
        });

        // Add fly to cart animation CSS
        if (!document.getElementById('flyToCartStyle')) {
            const style = document.createElement('style');
            style.id = 'flyToCartStyle';
            style.textContent = `
                @keyframes flyToCart {
                    0% {
                        transform: scale(1) rotate(0deg);
                        opacity: 1;
                    }
                    50% {
                        transform: scale(0.8) rotate(180deg);
                        opacity: 0.8;
                    }
                    100% {
                        transform: scale(0.3) translateX(50vw) translateY(-50vh) rotate(360deg);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        // Enhanced scroll to top functionality
        const scrollToTop = document.createElement('button');
        scrollToTop.innerHTML = '<i class="fas fa-chevron-up"></i>';
        scrollToTop.className = 'scroll-to-top';
        scrollToTop.style.cssText = `
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 50px;
            height: 50px;
            background: var(--sp-gradient-primary, linear-gradient(135deg, #ec4899 0%, #a855f7 100%));
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: var(--sp-shadow-primary, 0 4px 15px rgba(236, 72, 153, 0.2));
        `;
        
        document.body.appendChild(scrollToTop);

        $(window).on('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollToTop.style.opacity = '1';
                scrollToTop.style.visibility = 'visible';
            } else {
                scrollToTop.style.opacity = '0';
                scrollToTop.style.visibility = 'hidden';
            }
        });

        scrollToTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        scrollToTop.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.1)';
            this.style.boxShadow = 'var(--sp-shadow-hover, 0 8px 25px rgba(236, 72, 153, 0.3))';
        });

        scrollToTop.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = 'var(--sp-shadow-primary, 0 4px 15px rgba(236, 72, 153, 0.2))';
        });

        // Performance optimization: Debounce scroll events
        let scrollTimeout;
        function debounceScroll(func, wait) {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(func, wait);
        }

        // Add subtle parallax effect to background elements
        $(window).on('scroll', function() {
            debounceScroll(() => {
                const scrolled = window.pageYOffset;
                const parallaxElements = document.querySelectorAll('.content-wrapper:before');
                parallaxElements.forEach(el => {
                    el.style.transform = `translateY(${scrolled * 0.1}px)`;
                });
            }, 10);
        });

        // Error handling for missing elements
        window.addEventListener('error', function(e) {
            console.warn('Non-critical error:', e.message);
        });

        // Preload critical resources
        function preloadCriticalResources() {
            const criticalImages = [
                '/assets/frontend/font/Poppins/Poppins-Regular.ttf',
                // Add other critical resources here
            ];

            criticalImages.forEach(src => {
                const link = document.createElement('link');
                link.rel = 'preload';
                link.href = src;
                link.as = src.includes('.ttf') ? 'font' : 'image';
                if (link.as === 'font') link.crossOrigin = 'anonymous';
                document.head.appendChild(link);
            });
        }

        preloadCriticalResources();

        console.log('🌸 Single Product page initialized successfully! 🌸');
    });
</script>