{{-- frontend/single-product-partial/styles.blade.php --}}
<link href="https://fonts.cdnfonts.com/css/siyam-rupali" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('/') }}assets/frontend/css/flexslider.css">
<link rel="stylesheet" href="{{ asset('/') }}assets/frontend/css/image-zoom.css">
<link href="https://vjs.zencdn.net/8.3.0/video-js.css" rel="stylesheet" />

<style>
    /* Enhanced Single Product Page Styles */
    .single-product-container {
        /* Color Palette */
        --sp-primary: #2c3e50;
        --sp-secondary: #3498db;
        --sp-accent: #e74c3c;
        --sp-success: #27ae60;
        --sp-warning: #f39c12;
        --sp-info: #17a2b8;
        --sp-pink: #e91e63;
        --sp-purple: #9b59b6;
        --sp-orange: #fd7e14;
        
        /* Text Colors */
        --sp-text-dark: #2c3e50;
        --sp-text-medium: #495057;
        --sp-text-light: #6c757d;
        --sp-text-muted: #adb5bd;
        --sp-text-white: #ffffff;
        
        /* Background Colors */
        --sp-bg-primary: #ffffff;
        --sp-bg-secondary: #f8f9fa;
        --sp-bg-light: #f1f3f5;
        --sp-bg-dark: #343a40;
        --sp-bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        
        /* Border Colors */
        --sp-border-light: #e9ecef;
        --sp-border-medium: #dee2e6;
        --sp-border-dark: #adb5bd;
        
        /* Shadows */
        --sp-shadow-xs: 0 1px 2px rgba(0,0,0,0.05);
        --sp-shadow-sm: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        --sp-shadow-md: 0 4px 6px rgba(0,0,0,0.07), 0 2px 4px rgba(0,0,0,0.06);
        --sp-shadow-lg: 0 10px 15px rgba(0,0,0,0.1), 0 4px 6px rgba(0,0,0,0.05);
        --sp-shadow-xl: 0 20px 25px rgba(0,0,0,0.1), 0 10px 10px rgba(0,0,0,0.04);
        --sp-shadow-inner: inset 0 2px 4px rgba(0,0,0,0.06);
        
        /* Border Radius */
        --sp-radius-xs: 4px;
        --sp-radius-sm: 6px;
        --sp-radius-md: 10px;
        --sp-radius-lg: 16px;
        --sp-radius-xl: 24px;
        --sp-radius-full: 9999px;
        
        /* Transitions */
        --sp-transition-fastest: 0.1s ease;
        --sp-transition-fast: 0.2s ease;
        --sp-transition-normal: 0.3s ease;
        --sp-transition-slow: 0.5s ease;
        --sp-transition-slowest: 0.8s ease;
        
        /* Spacing */
        --sp-space-xs: 4px;
        --sp-space-sm: 8px;
        --sp-space-md: 16px;
        --sp-space-lg: 24px;
        --sp-space-xl: 32px;
        --sp-space-2xl: 48px;
        
        /* Typography */
        --sp-font-size-xs: 0.75rem;
        --sp-font-size-sm: 0.875rem;
        --sp-font-size-base: 1rem;
        --sp-font-size-lg: 1.125rem;
        --sp-font-size-xl: 1.25rem;
        --sp-font-size-2xl: 1.5rem;
        --sp-font-size-3xl: 2rem;
        
        /* Z-Index Scale */
        --sp-z-dropdown: 1000;
        --sp-z-sticky: 1020;
        --sp-z-fixed: 1030;
        --sp-z-modal-backdrop: 1040;
        --sp-z-modal: 1050;
        --sp-z-popover: 1060;
        --sp-z-tooltip: 1070;
        --sp-z-toast: 1080;
    }

    /* Reset and Base Styles */
    .single-product-container * {
        box-sizing: border-box;
    }

    .single-product-container {
        font-family: 'Siyam Rupali', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        line-height: 1.6;
        color: var(--sp-text-dark);
        background: var(--sp-bg-light);
        min-height: 100vh;
        padding: var(--sp-space-lg) 0;
        position: relative;
    }

    /* Background Pattern */
    .single-product-container::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: 
            radial-gradient(circle at 25px 25px, rgba(52, 152, 219, 0.1) 2px, transparent 0),
            radial-gradient(circle at 75px 75px, rgba(231, 76, 60, 0.1) 1px, transparent 0);
        background-size: 100px 100px;
        z-index: -1;
        opacity: 0.5;
    }

    /* Product Main Card */
    .sp-product-main-card {
        background: var(--sp-bg-primary);
        border-radius: var(--sp-radius-lg);
        box-shadow: var(--sp-shadow-lg);
        overflow: hidden;
        margin-bottom: var(--sp-space-xl);
        border: 1px solid var(--sp-border-light);
        position: relative;
    }

    .sp-product-main-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--sp-bg-gradient);
        z-index: 1;
    }

    /* Enhanced Loading States */
    .sp-loading-skeleton {
        background: linear-gradient(90deg, 
            var(--sp-bg-secondary) 25%, 
            var(--sp-bg-light) 50%, 
            var(--sp-bg-secondary) 75%);
        background-size: 200% 100%;
        animation: shimmer 2s infinite;
        border-radius: var(--sp-radius-sm);
    }

    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    /* Enhanced Buttons */
    .sp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: var(--sp-space-sm);
        padding: 12px 24px;
        font-weight: 600;
        text-decoration: none;
        border-radius: var(--sp-radius-md);
        transition: all var(--sp-transition-normal);
        cursor: pointer;
        border: 2px solid transparent;
        font-size: var(--sp-font-size-base);
        position: relative;
        overflow: hidden;
        min-height: 48px;
    }

    .sp-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left var(--sp-transition-slow);
    }

    .sp-btn:hover::before {
        left: 100%;
    }

    .sp-btn-primary {
        background: linear-gradient(135deg, var(--sp-primary), #34495e);
        color: var(--sp-text-white);
        box-shadow: var(--sp-shadow-md);
    }

    .sp-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--sp-shadow-lg);
        background: linear-gradient(135deg, #34495e, var(--sp-primary));
    }

    .sp-btn-secondary {
        background: linear-gradient(135deg, var(--sp-secondary), #2980b9);
        color: var(--sp-text-white);
        box-shadow: var(--sp-shadow-md);
    }

    .sp-btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: var(--sp-shadow-lg);
        background: linear-gradient(135deg, #2980b9, var(--sp-secondary));
    }

    .sp-btn-buy-now {
        background: linear-gradient(135deg, var(--sp-pink), #c2185b);
        color: var(--sp-text-white);
        font-size: var(--sp-font-size-lg);
        padding: 16px 32px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        box-shadow: 0 6px 20px rgba(233, 30, 99, 0.4);
        position: relative;
        overflow: hidden;
    }

    .sp-btn-buy-now:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 10px 30px rgba(233, 30, 99, 0.6);
        background: linear-gradient(135deg, #c2185b, var(--sp-pink));
    }

    .sp-btn-buy-now:active {
        transform: translateY(-1px) scale(1.01);
    }

    .sp-btn-cart {
        background: linear-gradient(135deg, var(--sp-success), #229954);
        color: var(--sp-text-white);
        border: 2px solid var(--sp-success);
    }

    .sp-btn-cart:hover {
        background: linear-gradient(135deg, #229954, var(--sp-success));
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(39, 174, 96, 0.4);
    }

    .sp-btn-outline {
        background: transparent;
        border: 2px solid var(--sp-secondary);
        color: var(--sp-secondary);
    }

    .sp-btn-outline:hover {
        background: var(--sp-secondary);
        color: var(--sp-text-white);
        transform: translateY(-2px);
    }

    /* Enhanced Form Controls */
    .sp-form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--sp-border-light);
        border-radius: var(--sp-radius-md);
        font-size: var(--sp-font-size-base);
        transition: all var(--sp-transition-normal);
        background: var(--sp-bg-primary);
        color: var(--sp-text-dark);
    }

    .sp-form-control:focus {
        outline: none;
        border-color: var(--sp-primary);
        box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        background: var(--sp-bg-primary);
    }

    .sp-form-control:invalid {
        border-color: var(--sp-accent);
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    /* Quantity Controls */
    .sp-quantity-wrapper {
        display: flex;
        align-items: center;
        border: 2px solid var(--sp-border-light);
        border-radius: var(--sp-radius-md);
        overflow: hidden;
        background: var(--sp-bg-primary);
        max-width: 140px;
    }

    .sp-quantity-btn {
        background: var(--sp-bg-secondary);
        border: none;
        padding: 12px 16px;
        cursor: pointer;
        transition: all var(--sp-transition-fast);
        color: var(--sp-text-dark);
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sp-quantity-btn:hover {
        background: var(--sp-primary);
        color: var(--sp-text-white);
    }

    .sp-quantity-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .sp-quantity-input {
        border: none;
        text-align: center;
        width: 60px;
        padding: 12px 8px;
        font-weight: 600;
        background: transparent;
        color: var(--sp-text-dark);
    }

    .sp-quantity-input:focus {
        outline: none;
        background: rgba(52, 152, 219, 0.1);
    }

    /* Price Display */
    .sp-price-wrapper {
        display: flex;
        align-items: center;
        gap: var(--sp-space-md);
        margin: var(--sp-space-lg) 0;
    }

    .sp-current-price {
        font-size: var(--sp-font-size-3xl);
        font-weight: 800;
        color: var(--sp-accent);
        background: linear-gradient(135deg, var(--sp-accent), #c0392b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .sp-original-price {
        font-size: var(--sp-font-size-xl);
        color: var(--sp-text-muted);
        text-decoration: line-through;
        font-weight: 500;
    }

    .sp-discount-badge {
        background: linear-gradient(135deg, var(--sp-warning), #e67e22);
        color: var(--sp-text-white);
        padding: 6px 12px;
        border-radius: var(--sp-radius-full);
        font-size: var(--sp-font-size-sm);
        font-weight: 600;
        letter-spacing: 0.3px;
        box-shadow: var(--sp-shadow-md);
    }

    /* Response Messages */
    .sp-response-message {
        position: fixed;
        top: var(--sp-space-lg);
        right: var(--sp-space-lg);
        z-index: var(--sp-z-toast);
        max-width: 400px;
        min-width: 300px;
    }

    .sp-alert {
        padding: 16px 20px;
        border-radius: var(--sp-radius-md);
        border: 2px solid;
        font-weight: 500;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
        animation: slideInRight 0.5s ease-out;
    }

    .sp-alert::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: currentColor;
    }

    .sp-alert-success {
        background: rgba(212, 237, 218, 0.95);
        border-color: var(--sp-success);
        color: #155724;
    }

    .sp-alert-danger {
        background: rgba(248, 215, 218, 0.95);
        border-color: var(--sp-accent);
        color: #721c24;
    }

    .sp-alert-warning {
        background: rgba(255, 243, 205, 0.95);
        border-color: var(--sp-warning);
        color: #856404;
    }

    .sp-alert-info {
        background: rgba(209, 236, 241, 0.95);
        border-color: var(--sp-info);
        color: #0c5460;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Sticky Buy Button (Mobile) */
    .sticky-buy-fixed {
        display: block;
        position: static;
        width: auto;
        padding: 0;
        box-shadow: none;
        background: transparent;
    }

    @media (max-width: 767px) {
        .sticky-buy-fixed {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: var(--sp-space-md);
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.95) 0%, 
                rgba(248, 249, 250, 0.95) 100%);
            backdrop-filter: blur(15px);
            border-top: 2px solid var(--sp-border-light);
            box-shadow: var(--sp-shadow-xl);
            z-index: var(--sp-z-sticky);
            animation: slideInUp 0.5s ease-out;
        }

        .sticky-buy-fixed .sp-btn-buy-now {
            width: 100%;
            max-width: none;
            margin: 0;
            font-size: var(--sp-font-size-lg);
            padding: 16px;
        }

        .single-product-container {
            padding-bottom: 120px;
        }
    }

    @keyframes slideInUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Image Gallery Enhancements */
    .sp-product-images {
        position: relative;
        overflow: hidden;
        border-radius: var(--sp-radius-lg);
    }

    .sp-main-image {
        transition: transform var(--sp-transition-slow);
        cursor: zoom-in;
    }

    .sp-main-image:hover {
        transform: scale(1.05);
    }

    .sp-thumbnail-nav {
        display: flex;
        gap: var(--sp-space-sm);
        margin-top: var(--sp-space-md);
        overflow-x: auto;
        padding: var(--sp-space-sm);
    }

    .sp-thumbnail {
        flex-shrink: 0;
        width: 80px;
        height: 80px;
        border-radius: var(--sp-radius-sm);
        border: 3px solid transparent;
        cursor: pointer;
        transition: all var(--sp-transition-normal);
        overflow: hidden;
    }

    .sp-thumbnail:hover,
    .sp-thumbnail.active {
        border-color: var(--sp-primary);
        transform: scale(1.1);
        box-shadow: var(--sp-shadow-md);
    }

    .sp-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Enhanced Flexslider Styles */
    .flexslider {
        border: none;
        border-radius: var(--sp-radius-lg);
        box-shadow: var(--sp-shadow-lg);
    }

    .flex-control-nav {
        bottom: -60px !important;
    }

    .flex-control-nav li {
        margin: 0 var(--sp-space-xs);
    }

    .flex-control-nav li a {
        width: 70px !important;
        height: 70px !important;
        border-radius: var(--sp-radius-md) !important;
        border: 3px solid var(--sp-border-light) !important;
        transition: all var(--sp-transition-normal) !important;
        box-shadow: var(--sp-shadow-sm) !important;
    }

    .flex-control-nav li a.flex-active,
    .flex-control-nav li a:hover {
        border-color: var(--sp-primary) !important;
        transform: scale(1.1) !important;
        box-shadow: var(--sp-shadow-md) !important;
    }

    /* Color Selection */
    .sp-color-selection {
        display: flex;
        gap: var(--sp-space-sm);
        margin: var(--sp-space-md) 0;
    }

    .sp-color-option {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 3px solid var(--sp-border-light);
        cursor: pointer;
        transition: all var(--sp-transition-normal);
        position: relative;
    }

    .sp-color-option::after {
        content: '';
        position: absolute;
        top: -6px;
        left: -6px;
        right: -6px;
        bottom: -6px;
        border: 2px solid transparent;
        border-radius: 50%;
        transition: all var(--sp-transition-normal);
    }

    .sp-color-option:hover::after,
    .sp-color-option.active::after {
        border-color: var(--sp-primary);
    }

    .sp-color-option.active {
        transform: scale(1.1);
    }

    /* Size Selection */
    .sp-size-selection {
        display: flex;
        gap: var(--sp-space-sm);
        margin: var(--sp-space-md) 0;
    }

    .sp-size-option {
        padding: 8px 16px;
        border: 2px solid var(--sp-border-light);
        border-radius: var(--sp-radius-md);
        cursor: pointer;
        transition: all var(--sp-transition-normal);
        font-weight: 500;
        background: var(--sp-bg-primary);
    }

    .sp-size-option:hover,
    .sp-size-option.active {
        border-color: var(--sp-primary);
        background: rgba(52, 152, 219, 0.1);
        color: var(--sp-primary);
    }

    /* Reviews Section */
    .sp-reviews-section {
        background: var(--sp-bg-primary);
        border-radius: var(--sp-radius-lg);
        padding: var(--sp-space-xl);
        margin-top: var(--sp-space-xl);
        box-shadow: var(--sp-shadow-md);
    }

    .sp-review-item {
        border-bottom: 1px solid var(--sp-border-light);
        padding: var(--sp-space-lg) 0;
        transition: all var(--sp-transition-normal);
    }

    .sp-review-item:hover {
        background: rgba(52, 152, 219, 0.05);
        border-radius: var(--sp-radius-md);
        padding: var(--sp-space-lg);
        margin: 0 calc(-1 * var(--sp-space-lg));
    }

    .sp-review-header {
        display: flex;
        align-items: center;
        gap: var(--sp-space-md);
        margin-bottom: var(--sp-space-sm);
    }

    .sp-reviewer-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--sp-bg-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--sp-text-white);
        font-weight: 600;
        font-size: var(--sp-font-size-lg);
    }

    .sp-rating-stars {
        display: flex;
        gap: 2px;
        color: var(--sp-warning);
    }

    /* Similar Products */
    .sp-similar-products {
        margin-top: var(--sp-space-2xl);
    }

    .sp-product-card {
        background: var(--sp-bg-primary);
        border-radius: var(--sp-radius-lg);
        overflow: hidden;
        box-shadow: var(--sp-shadow-md);
        transition: all var(--sp-transition-normal);
        border: 1px solid var(--sp-border-light);
    }

    .sp-product-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--sp-shadow-xl);
    }

    .sp-product-card-image {
        position: relative;
        overflow: hidden;
        aspect-ratio: 1;
    }

    .sp-product-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform var(--sp-transition-slow);
    }

    .sp-product-card:hover .sp-product-card-image img {
        transform: scale(1.1);
    }

    /* Loading States */
    .sp-loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: var(--sp-z-modal);
        backdrop-filter: blur(5px);
    }

    .sp-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid var(--sp-border-light);
        border-top: 4px solid var(--sp-primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Accessibility Improvements */
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    /* Focus States */
    .sp-btn:focus,
    .sp-form-control:focus,
    .sp-quantity-btn:focus {
        outline: 3px solid rgba(52, 152, 219, 0.5);
        outline-offset: 2px;
    }

    /* High Contrast Mode */
    @media (prefers-contrast: high) {
        .single-product-container {
            --sp-border-light: #000000;
            --sp-text-muted: #333333;
        }
        
        .sp-btn {
            border-width: 3px;
        }
    }

    /* Reduced Motion */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        .single-product-container {
            --sp-bg-primary: #1f2937;
            --sp-bg-secondary: #374151;
            --sp-bg-light: #111827;
            --sp-text-dark: #f9fafb;
            --sp-text-medium: #d1d5db;
            --sp-text-light: #9ca3af;
            --sp-border-light: #4b5563;
            --sp-border-medium: #6b7280;
        }
    }

    /* Print Styles */
    @media print {
        .sp-response-message,
        .sticky-buy-fixed,
        .sp-btn,
        .sp-quantity-wrapper {
            display: none !important;
        }
        
        .single-product-container {
            background: white !important;
            box-shadow: none !important;
        }
        
        .sp-product-main-card {
            box-shadow: none !important;
            border: 2px solid #000;
        }
    }

    /* Ultra-wide Screen Support */
    @media (min-width: 1920px) {
        .single-product-container {
            max-width: 1400px;
            margin: 0 auto;
        }
    }

    /* Mobile Optimizations */
    @media (max-width: 480px) {
        .single-product-container {
            padding: var(--sp-space-md) 0;
        }
        
        .sp-product-main-card {
            margin: 0 var(--sp-space-sm);
            border-radius: var(--sp-radius-md);
        }
        
        .sp-current-price {
            font-size: var(--sp-font-size-2xl);
        }
        
        .sp-btn {
            padding: 12px 16px;
            font-size: var(--sp-font-size-sm);
        }
    }
</style>