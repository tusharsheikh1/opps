@extends('layouts.frontend.app')

@push('meta')
    <meta name='description' content="{{ $product->title }}" />
    <meta property="og:image" content="{{ asset('uploads/product/' . $product->image) }}" />
    <meta name='keywords' content="@foreach ($product->tags as $tag){{ $tag->name . ', ' }} @endforeach" />
@endpush

@section('title', $product->title)

@push('css')
    <link href="https://fonts.cdnfonts.com/css/siyam-rupali" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/') }}assets/frontend/css/flexslider.css">
    <link rel="stylesheet" href="{{ asset('/') }}assets/frontend/css/image-zoom.css">
    <link href="https://vjs.zencdn.net/8.3.0/video-js.css" rel="stylesheet" />
    <style>
        /* Single Product Page Specific Styles */
        .single-product-container {
            --sp-primary: #2c3e50;
            --sp-secondary: #3498db;
            --sp-accent: #e74c3c;
            --sp-success: #27ae60;
            --sp-warning: #f39c12;
            --sp-pink: #e91e63;
            --sp-text-dark: #2c3e50;
            --sp-text-medium: #495057;
            --sp-text-light: #6c757d;
            --sp-text-muted: #adb5bd;
            --sp-bg-primary: #ffffff;
            --sp-bg-secondary: #f8f9fa;
            --sp-bg-light: #f1f3f5;
            --sp-border-light: #e9ecef;
            --sp-border-medium: #dee2e6;
            --sp-shadow-sm: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            --sp-shadow-md: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
            --sp-shadow-lg: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
            --sp-radius-sm: 6px;
            --sp-radius-md: 10px;
            --sp-radius-lg: 16px;
            --sp-transition-fast: 0.2s ease;
            --sp-transition-normal: 0.3s ease;
            --sp-transition-slow: 0.5s ease;
        }

        .single-product-container * {
            box-sizing: border-box;
        }

        .single-product-container {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: var(--sp-text-dark);
            background: var(--sp-bg-light);
            min-height: 100vh;
            padding: 20px 0;
        }

        /* Product Main Card */
        .sp-product-main-card {
            background: var(--sp-bg-primary);
            border-radius: var(--sp-radius-lg);
            box-shadow: var(--sp-shadow-md);
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid var(--sp-border-light);
        }

        /* Product Images Section */
        .sp-product-images {
            background: var(--sp-bg-primary);
            border-radius: var(--sp-radius-md);
            overflow: hidden;
            position: relative;
        }

        .single-product-container .flexslider {
            border: none !important;
            box-shadow: none !important;
            border-radius: var(--sp-radius-md);
            overflow: hidden;
            background: var(--sp-bg-primary);
            margin: 0 !important;
        }

        .single-product-container .flexslider .slides img {
            background: var(--sp-bg-primary);
            border-radius: var(--sp-radius-sm);
            transition: transform var(--sp-transition-normal);
            max-height: 400px;
            object-fit: contain;
            width: 100%;
        }

        .single-product-container .flexslider .slides img:hover {
            transform: scale(1.05);
        }

        .single-product-container .flex-direction-nav a {
            background: rgba(44, 62, 80, 0.8) !important;
            color: white !important;
            border-radius: 50% !important;
            width: 40px !important;
            height: 40px !important;
            margin: -20px 0 0 0 !important;
            transition: all var(--sp-transition-fast) !important;
            text-indent: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .single-product-container .flex-direction-nav a:hover {
            background: var(--sp-primary) !important;
            transform: scale(1.1) !important;
        }

        .single-product-container .flex-direction-nav a:before {
            font-family: "Font Awesome 5 Pro" !important;
            font-size: 16px !important;
        }

        .single-product-container .flex-direction-nav .flex-prev:before {
            content: "\f104" !important;
        }

        .single-product-container .flex-direction-nav .flex-next:before {
            content: "\f105" !important;
        }

        /* Product Info Section */
        .sp-product-info {
            background: transparent;
            padding: 20px;
        }

        .sp-product-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--sp-text-dark);
            margin: 0 0 20px 0;
            line-height: 1.3;
            letter-spacing: -0.5px;
        }

        .sp-product-meta {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 25px;
        }

        .sp-meta-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 16px;
            background: linear-gradient(135deg, var(--sp-bg-secondary) 0%, var(--sp-bg-primary) 100%);
            border-left: 4px solid var(--sp-secondary);
            border-radius: 0 var(--sp-radius-md) var(--sp-radius-md) 0;
            transition: all var(--sp-transition-fast);
            box-shadow: var(--sp-shadow-sm);
        }

        .sp-meta-item:hover {
            background: linear-gradient(135deg, #e3f2fd 0%, var(--sp-bg-secondary) 100%);
            border-left-color: var(--sp-primary);
            transform: translateX(4px);
            box-shadow: var(--sp-shadow-md);
        }

        .sp-meta-icon {
            font-size: 18px;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .sp-meta-text {
            flex: 1;
            min-width: 0;
        }

        .sp-meta-label {
            font-size: 14px;
            color: var(--sp-text-light);
            font-weight: 600;
            margin-right: 8px;
        }

        .sp-meta-link {
            color: var(--sp-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: color var(--sp-transition-fast);
        }

        .sp-meta-link:hover {
            color: var(--sp-primary);
            text-decoration: underline;
        }

        .sp-meta-value {
            color: var(--sp-text-dark);
            font-weight: 500;
            font-size: 15px;
        }

        .sp-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 4px;
        }

        .sp-category-tag {
            background: linear-gradient(135deg, var(--sp-success) 0%, #2ecc71 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.3px;
            transition: transform var(--sp-transition-fast);
            box-shadow: 0 2px 4px rgba(39, 174, 96, 0.3);
        }

        .sp-category-tag:hover {
            transform: scale(1.05);
        }

        /* Short Description */
        .sp-short-description {
            margin: 25px 0;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: var(--sp-radius-md);
            border-left: 4px solid var(--sp-secondary);
        }

        .sp-description-text {
            color: var(--sp-text-medium);
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 10px;
        }

        .sp-see-more-btn {
            background: none;
            border: none;
            color: var(--sp-secondary);
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
            padding: 0;
            text-decoration: underline;
            transition: color var(--sp-transition-fast);
        }

        .sp-see-more-btn:hover {
            color: var(--sp-accent);
        }

        /* Price Section */
        .sp-price-section {
            margin: 30px 0;
            padding: 25px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: var(--sp-radius-md);
            box-shadow: var(--sp-shadow-sm);
            border: 1px solid var(--sp-border-light);
        }

        .sp-price-display {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .sp-current-price {
            font-family: 'Inter', monospace;
            font-weight: 800;
            font-size: 2.8rem;
            color: var(--sp-accent);
            line-height: 1;
            text-shadow: 0 2px 4px rgba(231, 76, 60, 0.2);
        }

        .sp-original-price {
            font-size: 1.2rem;
            color: var(--sp-text-light);
            text-decoration: line-through;
            font-weight: 500;
        }

        .sp-discount-badge {
            background: linear-gradient(135deg, var(--sp-accent) 0%, #c0392b 100%);
            color: white;
            padding: 6px 12px;
            border-radius: var(--sp-radius-sm);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
        }

        .sp-savings-text {
            color: var(--sp-success);
            font-size: 14px;
            font-weight: 600;
            background: rgba(39, 174, 96, 0.1);
            padding: 4px 8px;
            border-radius: var(--sp-radius-sm);
        }

        /* Stock Status */
        .sp-stock-status {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
            padding: 12px 16px;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-radius: var(--sp-radius-md);
            border: 1px solid #c3e6cb;
            font-size: 14px;
        }

        .sp-stock-icon {
            width: 20px;
            height: 20px;
            background: var(--sp-success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            box-shadow: 0 2px 4px rgba(39, 174, 96, 0.3);
        }

        /* Quantity Section */
        .sp-quantity-section {
            margin: 25px 0;
        }

        .sp-quantity-label {
            font-size: 16px;
            color: var(--sp-text-dark);
            margin-bottom: 12px;
            font-weight: 600;
        }

        .sp-quantity-controls {
            display: flex;
            align-items: center;
            gap: 0;
            background: var(--sp-bg-primary);
            border: 2px solid var(--sp-border-medium);
            border-radius: var(--sp-radius-sm);
            width: fit-content;
            overflow: hidden;
        }

        .sp-quantity-btn {
            width: 45px;
            height: 45px;
            background: var(--sp-bg-secondary);
            border: none;
            border-right: 1px solid var(--sp-border-medium);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            font-size: 18px;
            color: var(--sp-text-dark);
            transition: all var(--sp-transition-fast);
        }

        .sp-quantity-btn:last-child {
            border-right: none;
            border-left: 1px solid var(--sp-border-medium);
        }

        .sp-quantity-btn:hover {
            background: var(--sp-primary);
            color: white;
            transform: scale(1.05);
        }

        .sp-quantity-input {
            width: 70px;
            height: 45px;
            border: none;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
            background: var(--sp-bg-primary);
            color: var(--sp-text-dark);
        }

        .sp-quantity-input:focus {
            outline: none;
        }

        /* Action Buttons */
        .sp-action-buttons {
            display: flex;
            gap: 15px;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .sp-btn-buy-now {
            background: linear-gradient(135deg, var(--sp-pink), #ad1457);
            color: white;
            border: none;
            padding: 16px 32px;
            font-size: 16px;
            font-weight: 600;
            border-radius: var(--sp-radius-md);
            cursor: pointer;
            transition: all var(--sp-transition-normal);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-width: 220px;
            box-shadow: 0 4px 15px rgba(233, 30, 99, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
        }

        .sp-btn-buy-now:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(233, 30, 99, 0.5);
            color: white;
            text-decoration: none;
        }

        .sp-btn-cart {
            background: var(--sp-bg-primary);
            color: var(--sp-primary);
            border: 2px solid var(--sp-primary);
            padding: 14px 28px;
            font-size: 15px;
            font-weight: 600;
            border-radius: var(--sp-radius-md);
            cursor: pointer;
            transition: all var(--sp-transition-normal);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 180px;
        }

        .sp-btn-cart:hover {
            background: var(--sp-primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--sp-shadow-md);
        }

        /* Color Selection */
        .sp-color-selection {
            margin: 25px 0;
            padding: 20px;
            background: var(--sp-bg-secondary);
            border-radius: var(--sp-radius-md);
        }

        .sp-color-selection h4 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--sp-text-dark);
        }

        .sp-btn-color-group .color {
            border: 3px solid transparent;
            border-radius: var(--sp-radius-md);
            transition: all var(--sp-transition-normal);
            overflow: hidden;
            margin: 8px;
            cursor: pointer;
        }

        .sp-btn-color-group .color:hover {
            transform: translateY(-4px);
            box-shadow: var(--sp-shadow-lg);
        }

        .sp-btn-color-group .color.active {
            border-color: var(--sp-primary);
            transform: translateY(-4px);
            box-shadow: var(--sp-shadow-lg);
        }

        .sp-btn-color-group .color img {
            border-radius: var(--sp-radius-sm);
            transition: transform var(--sp-transition-normal);
        }

        /* Attribute Selection */
        .sp-attribute-selection {
            margin: 25px 0;
            padding: 20px;
            background: var(--sp-bg-secondary);
            border-radius: var(--sp-radius-md);
        }

        .sp-attribute-selection h4 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--sp-text-dark);
        }

        .single-product-container .get_attri_price + label {
            background: var(--sp-bg-primary);
            border: 2px solid var(--sp-border-light);
            padding: 12px 24px;
            margin-right: 12px;
            display: inline-block;
            border-radius: var(--sp-radius-md);
            cursor: pointer;
            margin-bottom: 12px;
            transition: all var(--sp-transition-normal);
            font-weight: 500;
            color: var(--sp-text-dark);
        }

        .single-product-container .get_attri_price + label:hover {
            border-color: var(--sp-primary);
            transform: translateY(-2px);
            box-shadow: var(--sp-shadow-md);
        }

        .single-product-container input[type="radio"].get_attri_price {
            display: none;
        }

        .single-product-container input[type="radio"].get_attri_price:checked + label {
            background: var(--sp-primary);
            color: white;
            border-color: var(--sp-primary);
            transform: translateY(-2px);
            box-shadow: var(--sp-shadow-md);
        }

        /* Secondary Actions */
        .sp-secondary-actions {
            display: flex;
            gap: 15px;
            margin: 25px 0;
            flex-wrap: wrap;
        }

        .sp-wishlist-btn, .sp-share-btn {
            background: var(--sp-bg-primary);
            border: 2px solid var(--sp-border-light);
            color: var(--sp-text-dark);
            padding: 12px 20px;
            border-radius: var(--sp-radius-md);
            text-decoration: none;
            font-weight: 500;
            transition: all var(--sp-transition-normal);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sp-wishlist-btn:hover, .sp-share-btn:hover {
            border-color: var(--sp-accent);
            color: var(--sp-accent);
            transform: translateY(-2px);
            box-shadow: var(--sp-shadow-md);
            text-decoration: none;
        }

        /* Product Features */
        .sp-product-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 25px 0;
            padding: 25px;
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
            border-radius: var(--sp-radius-md);
            border: 1px solid #d4edda;
        }

        .sp-feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            color: var(--sp-text-dark);
            font-weight: 500;
        }

        .sp-feature-icon {
            color: var(--sp-success);
            font-size: 20px;
        }

        /* Related Products Sidebar */
        .sp-related-sidebar {
            background: var(--sp-bg-primary);
            border-radius: var(--sp-radius-lg);
            padding: 25px;
            box-shadow: var(--sp-shadow-md);
            height: fit-content;
            position: sticky;
            top: 20px;
            border: 1px solid var(--sp-border-light);
        }

        .sp-related-sidebar h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--sp-text-dark);
            margin-bottom: 20px;
            border-bottom: 3px solid var(--sp-primary);
            padding-bottom: 12px;
            position: relative;
        }

        .sp-related-sidebar h3::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--sp-accent);
        }

        /* Product Details Section */
        .sp-product-details-section {
            background: var(--sp-bg-primary);
            border-radius: var(--sp-radius-lg);
            box-shadow: var(--sp-shadow-md);
            overflow: hidden;
            margin-top: 30px;
            border: 1px solid var(--sp-border-light);
        }

        .sp-details-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
        }

        .sp-accordion-navigation {
            display: flex;
            background: var(--sp-bg-secondary);
            border-bottom: 1px solid var(--sp-border-light);
        }

        .sp-accordion-navigation button {
            flex: 1;
            background: none;
            border: none;
            padding: 18px 20px;
            font-weight: 600;
            color: var(--sp-text-light);
            cursor: pointer;
            transition: all var(--sp-transition-normal);
            border-bottom: 3px solid transparent;
            font-size: 14px;
        }

        .sp-accordion-navigation button:hover,
        .sp-accordion-navigation button:not(.collapsed) {
            color: var(--sp-primary);
            background: var(--sp-bg-primary);
            border-bottom-color: var(--sp-primary);
        }

        .sp-accordion-content {
            padding: 35px;
            line-height: 1.8;
        }

        /* Reviews Section */
        .sp-review-summary {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            padding: 30px;
            border-radius: var(--sp-radius-lg);
            margin-bottom: 30px;
            border: 1px solid #ffd6a5;
        }

        .sp-review-summary .heading {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--sp-text-dark);
            margin-bottom: 20px;
        }

        .sp-review-item {
            background: var(--sp-bg-primary);
            border-radius: var(--sp-radius-md);
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--sp-shadow-sm);
            border: 1px solid var(--sp-border-light);
            transition: all var(--sp-transition-normal);
        }

        .sp-review-item:hover {
            box-shadow: var(--sp-shadow-md);
            transform: translateY(-2px);
        }

        .sp-review-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .sp-review-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--sp-border-light);
        }

        .sp-reviewer-name {
            font-weight: 600;
            color: var(--sp-text-dark);
            font-size: 16px;
        }

        /* Comment Section */
        .sp-comment-item {
            background: var(--sp-bg-primary);
            border-radius: var(--sp-radius-md);
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--sp-shadow-sm);
            border: 1px solid var(--sp-border-light);
            transition: all var(--sp-transition-normal);
        }

        .sp-comment-item:hover {
            box-shadow: var(--sp-shadow-md);
            transform: translateY(-1px);
        }

        .sp-comment-form {
            background: var(--sp-bg-secondary);
            padding: 30px;
            border-radius: var(--sp-radius-md);
            margin-top: 30px;
            border: 1px solid var(--sp-border-light);
        }

        .sp-comment-form textarea {
            width: 100%;
            border: 2px solid var(--sp-border-light);
            border-radius: var(--sp-radius-md);
            padding: 15px;
            font-family: inherit;
            resize: vertical;
            min-height: 120px;
            transition: all var(--sp-transition-normal);
        }

        .sp-comment-form textarea:focus {
            outline: none;
            border-color: var(--sp-primary);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        /* Specification Table */
        .sp-spec-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border-radius: var(--sp-radius-md);
            overflow: hidden;
            box-shadow: var(--sp-shadow-sm);
        }

        .sp-spec-table th {
            background: var(--sp-bg-secondary);
            padding: 18px;
            text-align: left;
            font-weight: 600;
            color: var(--sp-text-dark);
            width: 200px;
            border-bottom: 1px solid var(--sp-border-light);
        }

        .sp-spec-table td {
            padding: 18px;
            border-bottom: 1px solid var(--sp-border-light);
            background: var(--sp-bg-primary);
        }

        .sp-spec-table tr:last-child td,
        .sp-spec-table tr:last-child th {
            border-bottom: none;
        }

        /* Similar Products Section */
        .sp-similar-products-section {
            background: var(--sp-bg-primary);
            border-radius: var(--sp-radius-lg);
            box-shadow: var(--sp-shadow-md);
            padding: 35px;
            margin-top: 30px;
            border: 1px solid var(--sp-border-light);
        }

        .sp-similar-products-section h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--sp-text-dark);
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }

        .sp-similar-products-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(135deg, var(--sp-primary) 0%, var(--sp-accent) 100%);
            border-radius: 2px;
        }

        /* Out of Stock */
        .sp-out-of-stock {
            background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);
            color: white;
            padding: 18px;
            text-align: center;
            border-radius: var(--sp-radius-md);
            font-weight: 600;
            font-size: 16px;
            box-shadow: var(--sp-shadow-md);
        }

        /* Share Dropdown */
        .sp-share-groups {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
        }

        .sp-share-groups span {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--sp-radius-sm);
            transition: transform var(--sp-transition-normal);
            cursor: pointer;
        }

        .sp-share-groups span:hover {
            transform: scale(1.15);
        }

        /* Author Section */
        .sp-author-section {
            display: flex;
            gap: 25px;
            align-items: flex-start;
        }

        .sp-author-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: var(--sp-shadow-md);
            border: 4px solid var(--sp-border-light);
        }

        .sp-author-info h4 {
            color: var(--sp-text-dark);
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        /* Video Player Styling */
        .single-product-container .video-js {
            border-radius: var(--sp-radius-md);
            overflow: hidden;
        }

        /* Loading States */
        .sp-btn-buy-now:disabled, .sp-btn-cart:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Response Message */
        .sp-response-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sp-product-main-card {
                margin: 10px;
                border-radius: var(--sp-radius-md);
            }
            
            .sp-product-title {
                font-size: 22px;
                margin-bottom: 15px;
            }
            
            .sp-current-price {
                font-size: 2.2rem;
            }

            .sp-price-display {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .sp-action-buttons {
                flex-direction: column;
                gap: 12px;
            }
            
            .sp-btn-buy-now, .sp-btn-cart {
                width: 100%;
                min-width: 100%;
                justify-content: center;
            }
            
            .sp-product-features {
                grid-template-columns: 1fr;
            }
            
            .sp-accordion-navigation {
                flex-direction: column;
            }
            
            .sp-accordion-navigation button {
                padding: 15px;
                border-bottom: 1px solid var(--sp-border-light);
            }
            
            .sp-related-sidebar {
                margin-top: 30px;
                position: static;
            }

            .sp-secondary-actions {
                flex-direction: column;
            }

            .sp-quantity-controls {
                width: 100%;
                justify-content: center;
            }

            .sp-author-section {
                flex-direction: column;
                text-align: center;
            }

            .sp-spec-table th {
                width: 150px;
                padding: 12px;
            }

            .sp-spec-table td {
                padding: 12px;
            }
        }

        @media (max-width: 480px) {
            .single-product-container {
                padding: 10px 0;
            }

            .sp-product-main-card {
                margin: 5px;
            }
            
            .sp-product-title {
                font-size: 20px;
            }

            .sp-current-price {
                font-size: 2rem;
            }

            .sp-original-price {
                font-size: 1rem;
            }

            .sp-discount-badge {
                font-size: 11px;
                padding: 4px 8px;
            }

            .sp-meta-item {
                padding: 10px 12px;
                gap: 8px;
            }

            .sp-categories {
                flex-direction: column;
                align-items: flex-start;
            }

            .sp-category-tag {
                font-size: 11px;
                padding: 3px 10px;
            }

            .sp-accordion-navigation {
                display: block;
            }

            .sp-accordion-navigation button {
                display: block;
                width: 100%;
                text-align: left;
            }

            .sp-author-avatar {
                width: 100px;
                height: 100px;
            }

            .sp-similar-products-section,
            .sp-product-details-section {
                margin: 20px 5px;
            }
        }

        /* Animation Enhancements */
        @keyframes spFadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .sp-product-main-card {
            animation: spFadeIn 0.6s ease-out;
        }

        .sp-meta-item {
            animation: spFadeIn 0.8s ease-out;
        }
/* ────────────── Sticky Buy-Now Wrapper (desktop inline) ────────────── */
.sticky-buy-fixed {
  display: block;        /* show on desktop */
  position: static;      /* flow normally in your layout */
  width: auto;           /* don’t force full-width on desktop */
  padding: 0;            /* no extra padding wrapper-wide */
  box-shadow: none;      /* remove the mobile shadow */
}

/* ────────────── Only turn it into a sticky bar on mobile ≤767px ────────────── */
@media (max-width: 767px) {
  .sticky-buy-fixed {
    display: block;
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: env(safe-area-inset-bottom,10px) 0;
    text-align: center;
    box-shadow: 0 -2px 6px rgba(0,0,0,0.1);
    z-index: 9999;
  }

  .sticky-buy-fixed .sp-btn-buy-now {
    display: block;
    width: calc(100% - 40px);
    max-width: 400px;
    margin: 0 auto;
    padding: 14px 0;
    font-size: 1.1rem;
    border-radius: 6px;
  }

  .product-page-wrapper {
    padding-bottom: 80px;
  }
}


        /* Toast Override for Single Product */
        .single-product-container .toast {
            position: fixed !important;
            top: 20px !important;
            right: 20px !important;
            z-index: 9999 !important;
        }
    </style>
@endpush

@section('content')
    <div class="single-product-container">
        <div class="container">
            <!-- Response Message Container -->
            <div id="sp-response-message" class="sp-response-message"></div>
            
            <div class="sp-product-main-card">
                <div class="row">
                    <!-- Product Images -->
                    <div class="col-md-4 single-right-left">
                        <div class="sp-product-images">
                            <div class="flexslider">
                                <div class="clearfix"></div>
                                <div class="flex-viewport" style="overflow: hidden; position: relative;">
                                    <ul class="slides my-gallery" style="width: 1000%; transition-duration: 0.6s; transform: translate3d(-1311px, 0px, 0px);">
                                        @if ($product->yvideo)
                                            <li data-thumb="{{ asset('uploads/product/video/' . $product->video_thumb) }}" style="width: 437px; float: left; display: block;" class="">
                                                <div class="thumb-image">
                                                    <iframe width="100%" style="height:300px;background:black;border-radius:8px;" src="{{ $product->yvideo }}"></iframe>
                                                </div>
                                            </li>
                                        @elseif($product->video)
                                            <li data-thumb="{{ asset('uploads/product/video/' . $product->video_thumb) }}" style="width: 437px; float: left; display: block;" class="">
                                                <div class="thumb-image">
                                                    <video class="video-js" controls data-setup="{}" controls width="100%" style="height:300px;background:black">
                                                        <source src="{{ asset('uploads/product/video/' . $product->video) }}" type="video/mp4">
                                                    </video>
                                                </div>
                                            </li>
                                        @endif

                                        @foreach ($product->images as $image)
                                            <li data-cl="{{ $image->color_attri }}" data-thumb="{{ asset('uploads/product/' . $image->name) }}" style="width: 437px; float: left; display: block;" class="">
                                                <div class="thumb-image">
                                                    <a class="" href="{{ asset('uploads/product/' . $image->name) }}" target="_blank">
                                                        <img src="{{ asset('uploads/product/' . $image->name) }}" class="my-gallery-image img-responsive" alt="" draggable="true">
                                                    </a>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <ul class="flex-direction-nav">
                                    <li class="flex-nav-prev"><a class="flex-prev" href="#">Previous</a></li>
                                    <li class="flex-nav-next"><a class="flex-next" href="#">Next</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Product Information -->
                    <div class="col-md-5 single-right-left simpleCart_shelfItem">
                        <div class="sp-product-info">
                            <!-- Product Title -->
                            <h1 class="sp-product-title">{{ $product->title }}</h1>
                            
                            <!-- Product Meta Information -->
                            <div class="sp-product-meta">
                                
                                @if($product->brand)
                                    <div class="sp-meta-item">
                                        <div class="sp-meta-text">
                                            <strong class="sp-meta-label">প্রকাশনি:</strong>
                                            <a href="{{ route('brand.product', ['slug' => $product->brand->slug]) }}" class="sp-meta-link">{{ $product->brand->name }}</a>
                                        </div>
                                    </div>
                                @endif
                                
                                @if ($product->book == 1 && $product->author)
                                    <div class="sp-meta-item">
                                        <div class="sp-meta-text">
                                            <strong class="sp-meta-label">লেখক:</strong>
                                            <a href="{{ route('author.product', ['slug' => $product->author_id]) }}" class="sp-meta-link">{{ $product->author->name }}</a>
                                        </div>
                                    </div>
                                @endif
                                
                                @if ($product->book == 1)
                                    <div class="sp-meta-item">
                                        <div class="sp-meta-text">
                                            <strong class="sp-meta-label">বিষয়:</strong>
                                            <div class="sp-categories">
                                                @foreach($product->categories as $category)
                                                    <span class="sp-category-tag">{{ $category->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                @if ($product->book == 1 && $product->pages)
                                    <div class="sp-meta-item">
                                        <div class="sp-meta-text">
                                            <strong class="sp-meta-label">পৃষ্ঠা সংখ্যা:</strong>
                                            <span class="sp-meta-value">{{ $product->pages }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Modern Short Description with See More -->
                            <div class="sp-short-description">
                                <div class="sp-description-text" id="spShortDesc">
                                    @php
                                        $shortText = strip_tags($product->short_description);
                                        $words = explode(' ', $shortText);
                                        $truncated = implode(' ', array_slice($words, 0, 30));
                                        $needsExpansion = count($words) > 30;
                                    @endphp
                                    <span id="sp-truncated-text">{{ $truncated }}{{ $needsExpansion ? '...' : '' }}</span>
                                    @if($needsExpansion)
                                        <span id="sp-full-text" style="display: none;">{!! $product->short_description !!}</span>
                                        <button class="sp-see-more-btn" id="spSeeMoreBtn" onclick="spToggleDescription()">আরও দেখুন</button>
                                    @else
                                        {!! $product->short_description !!}
                                    @endif
                                </div>
                            </div>

                            <!-- Price Section -->
                            <div class="sp-price-section">
                                <?php if (isset($campaigns_product)) {
                                    $product->discount_price = $campaigns_product->price;
                                } ?>
                                
                                <div class="sp-price-display">
                                    @if ($product->discount_price > 0)
                                        <div class="sp-current-price">৳<span id="sp_dynamic_price">{{ $product->discount_price }}</span></div>
                                        <div class="sp-original-price">৳{{ $product->regular_price }}</div>
                                        @php
                                            $per = $product->regular_price / 100;
                                            $amc = $product->regular_price - $product->discount_price;
                                            $discount_percentage = round($amc / $per);
                                        @endphp
                                        <div class="sp-discount-badge">{{ $discount_percentage }}% ছাড়</div>
                                    @else
                                        <div class="sp-current-price">৳<span id="sp_dynamic_price">{{ $product->regular_price }}</span></div>
                                    @endif
                                </div>
                                
                                @if ($product->discount_price > 0)
                                    <div class="sp-savings-text">আপনি সাশ্রয় করবেন ৳{{ $amc }}</div>
                                @endif
                            </div>

                            <!-- Stock Status -->
                            @if ($product->quantity > 0)
                                <div class="sp-stock-status">
                                    <div class="sp-stock-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <span><strong>স্টক আছে</strong> ({{ $product->quantity }} কপি বাকি)</span>
                                </div>
                            @else
                                <div class="sp-stock-status" style="background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%); color: white;">
                                    <div class="sp-stock-icon" style="background: #e74c3c;">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <span><strong>স্টক শেষ</strong></span>
                                </div>
                            @endif

                            <!-- Color Selection -->
                            @if ($product->colors->count() != 0)
                                <div class="sp-color-selection">
                                    <h4>রং নির্বাচন করুন:</h4>
                                    <div class="btn-group btn-group-toggle sp-btn-color-group d-block" data-toggle="buttons">
                                        @foreach ($colors_product as $color)
                                            <label class="btn color {{ $product->colors->count() == 1 ? 'active' : '' }}" style="background: {{ $color->code }}">
                                                @if ($product->images)
                                                    @foreach ($product->images as $image)
                                                        @if ($image->color_attri == $color->slug)
                                                            <img src="{{ asset('uploads/product/' . $image->name) }}" style="height: 70px;width: 70px;object-fit: contain;border: 5px solid white;background: white;" alt="" draggable="true">
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <input type="radio" name="color" autocomplete="off" value="{{ $color->slug }}" {{ $product->colors->count() == 1 ? 'checked' : '' }}>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Attribute Selection -->
                            @foreach ($attributes as $attribute)
                                <?php
                                $attribute_prouct = DB::table('attribute_product')
                                    ->select('*')
                                    ->join('attribute_values', 'attribute_values.id', '=', 'attribute_product.attribute_value_id')
                                    ->addselect('attribute_values.name as vName')
                                    ->addselect('attribute_values.id as vid')
                                    ->join('attributes', 'attributes.id', '=', 'attribute_values.attributes_id')
                                    ->where('attribute_product.product_id', $product->id)
                                    ->where('attributes.id', $attribute->id)
                                    ->get();
                                ?>
                                @if ($attribute_prouct->count() > 0)
                                    <div class="sp-attribute-selection">
                                        <h4>{{ $attribute->name }} নির্বাচন করুন:</h4>
                                        @foreach ($attribute_prouct as $attr)
                                            <div class="form-check" style="display:inline-block">
                                                <input id="sp_{{ $attr->vName }}" class="form-check-input get_attri_price" type="radio" name="{{ $attribute->slug }}" value="{{ $attr->vid }}" {{ $attribute_prouct->count() == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="sp_{{ $attr->vName }}">{{ $attr->vName }}</label>
                                            </div>
                                        @endforeach
                                        @push('js')
                                            <script>
                                                $(document).on('click', 'input[type="radio"][name="{{ $attribute->slug }}"]', function(e) {
                                                    $('input#sp_{{ $attribute->slug }}').val(this.value);
                                                    $('input#spBuyNow{{ ucfirst($attribute->slug) }}').val(this.value);
                                                })
                                            </script>
                                        @endpush
                                    </div>
                                @endif
                            @endforeach

                            <!-- Quantity Selection -->
                            <div class="sp-quantity-section">
                                <div class="sp-quantity-label">পরিমাণ:</div>
                                <div class="sp-quantity-controls">
                                    <button type="button" class="sp-quantity-btn sp-value-minus">−</button>
                                    <input type="text" class="sp-quantity-input sp-entry sp-value" value="1" readonly>
                                    <button type="button" class="sp-quantity-btn sp-value-plus">+</button>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="sp-action-buttons">
                                @if ($product->download_able == 1)
                                    <style>.sp-gcg { display: none; }</style>
                                @endif
                                
                                <!-- Add to Cart -->
                                @if ($product->quantity > 0)
                                    <div class="sp-gcg">
                                        <form action="{{ route('add.cart') }}" method="post" id="spAddToCart" style="display: inline;">
                                            @csrf
                                            <fieldset>
                                                <?php if(isset($campaigns_product)){?>
                                                <input type="hidden" name="camp" id="sp_camp" value="{{ $campaigns_product->id }}">
                                                <?php }?>
                                                <input type="hidden" name="id" id="sp_id" value="{{ $product->id }}">
                                                <input type="hidden" name="qty" id="sp_qty" value="1">
                                                <input type="hidden" name="color" id="sp_color" value="blank">
                                                @foreach ($attributes as $attribute)
                                                    <?php
                                                    $attribute_prouct = DB::table('attribute_product')
                                                        ->select('*')
                                                        ->join('attribute_values', 'attribute_values.id', '=', 'attribute_product.attribute_value_id')
                                                        ->addselect('attribute_values.name as vName')
                                                        ->addselect('attribute_values.id as vid')
                                                        ->join('attributes', 'attributes.id', '=', 'attribute_values.attributes_id')
                                                        ->where('attribute_product.product_id', $product->id)
                                                        ->where('attributes.id', $attribute->id)
                                                        ->get();
                                                    ?>
                                                    @if ($attribute_prouct->count() > 0)
                                                        @foreach ($attribute_prouct as $attr)
                                                            <?php $vid = $attr->vid; ?>
                                                        @endforeach
                                                        <input type="hidden" name="{{ $attribute->slug }}" id="sp_{{ $attribute->slug }}" value="{{ $attribute_prouct->count() == 1 ? $vid : 'blank' }}">
                                                    @endif
                                                @endforeach
                                                @if ($product->sheba != 1)
                                                    <button class="sp-btn-cart" type="submit" name="submit">
                                                        <i class="fal fa-shopping-cart"></i> কার্টে যোগ করুন
                                                    </button>
                                                @endif
                                            </fieldset>
                                        </form>
                                    </div>
                                @endif

                                <!-- Buy Now Button -->
                                <div class="sp-gcg buy-now-section">
                                    <form action="{{ route('buy.product') }}" method="GET" id="spBuyNowForm" style="display: inline;">
                                        <?php if(isset($campaigns_product)){?>
                                        <input type="hidden" name="camp" value="{{ $campaigns_product->id }}">
                                        <?php }?>
                                        <fieldset>
                                            <?php if (isset($campaigns_product)) {
                                                $product->discount_price = $campaigns_product->price;
                                            } ?>
                                            @if (!empty($product->discount_price))
                                                <input type="hidden" name="just" id="spBuyNowJust" value="{{ $product->discount_price }}">
                                                <input type="hidden" name="dynamic_price" id="spBuyNowDynamicPrice" value="{{ $product->discount_price }}">
                                            @else
                                                <input type="hidden" name="just" id="spBuyNowJust" value="{{ $product->regular_price }}">
                                                <input type="hidden" name="dynamic_price" id="spBuyNowDynamicPrice" value="{{ $product->regular_price }}">
                                            @endif
                                            <input type="hidden" name="id" value="{{ $product->id }}">
                                            <input type="hidden" name="qty" id="spBuyNowQty" value="1">
                                            <input type="hidden" name="color" id="spBuyNowColor" value="blank">
                                            @foreach ($attributes as $attribute)
                                                <?php
                                                $attribute_prouct = DB::table('attribute_product')
                                                    ->select('*')
                                                    ->join('attribute_values', 'attribute_values.id', '=', 'attribute_product.attribute_value_id')
                                                    ->addselect('attribute_values.name as vName')
                                                    ->addselect('attribute_values.id as vid')
                                                    ->join('attributes', 'attributes.id', '=', 'attribute_values.attributes_id')
                                                    ->where('attribute_product.product_id', $product->id)
                                                    ->where('attributes.id', $attribute->id)
                                                    ->get();
                                                ?>
                                                @if ($attribute_prouct->count() > 0)
                                                    @foreach ($attribute_prouct as $attr)
                                                        <?php $vid = $attr->vid; ?>
                                                    @endforeach
                                                    <input type="hidden" name="{{ $attribute->slug }}" id="spBuyNow{{ ucfirst($attribute->slug) }}" value="{{ $attribute_prouct->count() == 1 ? $vid : 'blank' }}">
                                                @endif
                                            @endforeach
                                            @if ($product->quantity <= 0)
  <div class="sp-out-of-stock">স্টক শেষ</div>
@else
  <div class="sticky-buy-fixed">
    <button class="sp-btn-buy-now" type="submit">
      <i class="fas fa-shopping-bag"></i> এখনি কিনুন
    </button>
  </div>
@endif

                                        </fieldset>
                                    </form>
                                </div>
                            </div>

                            <!-- Secondary Actions -->
                            <div class="sp-secondary-actions">
                                @php($typeid = $product->slug)
                                @if (App\Models\wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->first())
                                    @php($k = App\Models\wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->first())
                                    <a class="sp-wishlist-btn" href="{{ route('wishlist.remove', ['item' => $k->id]) }}">
                                        <i class="fal fa-heart-broken" aria-hidden="true"></i> ইচ্ছেতালিকা থেকে সরান
                                    </a>
                                @else
                                    <form style="display: inline;" action="{{ route('wishlist.add') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->slug }}">
                                        <button class="sp-wishlist-btn" type="submit">
                                            <i class="fal fa-heart" aria-hidden="true"></i> ইচ্ছেতালিকায় যোগ করুন
                                        </button>
                                    </form>
                                @endif

                                <!-- Share Dropdown -->
                                <div class="dropdown">
                                    <button class="sp-share-btn dropdown-toggle" type="button" id="spDropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icofont icofont-share"></i> শেয়ার করুন
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="spDropdownMenuButton">
                                        <div class="sp-share-groups">
                                            <div>Share:</div>
                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ Request::url() }}">
                                                <span style="background-color: rgb(24, 119, 242);">
                                                    <svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="20" height="20">
                                                        <path fill="#FFF" d="M17.78 27.5V17.008h3.522l.527-4.09h-4.05v-2.61c0-1.182.33-1.99 2.023-1.99h2.166V4.66c-.375-.05-1.66-.16-3.155-.16-3.123 0-5.26 1.905-5.26 5.405v3.016h-3.53v4.09h3.53V27.5h4.223z"></path>
                                                    </svg>
                                                </span>
                                            </a>
                                            <a href="https://twitter.com/intent/tweet?url={{ Request::url() }}&text=">
                                                <span style="background-color: rgb(29, 155, 240);">
                                                    <svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="20" height="20">
                                                        <path fill="#FFF" d="M28 8.557a9.913 9.913 0 01-2.828.775 4.93 4.93 0 002.166-2.725 9.738 9.738 0 01-3.13 1.194 4.92 4.92 0 00-3.593-1.55 4.924 4.924 0 00-4.794 6.049c-4.09-.21-7.72-2.17-10.15-5.15a4.942 4.942 0 00-.665 2.477c0 1.71.87 3.214 2.19 4.1a4.968 4.968 0 01-2.23-.616v.06c0 2.39 1.7 4.38 3.952 4.83-.414.115-.85.174-1.297.174-.318 0-.626-.03-.928-.086a4.935 4.935 0 004.6 3.42 9.893 9.893 0 01-6.114 2.107c-.398 0-.79-.023-1.175-.068a13.953 13.953 0 007.55 2.213c9.056 0 14.01-7.507 14.01-14.013 0-.213-.005-.426-.015-.637.96-.695 1.795-1.56 2.455-2.55z"></path>
                                                    </svg>
                                                </span>
                                            </a>
                                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ Request::url() }}">
                                                <span style="background-color: rgb(0, 123, 181);">
                                                    <svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="20" height="20">
                                                        <path d="M6.227 12.61h4.19v13.48h-4.19V12.61zm2.095-6.7a2.43 2.43 0 010 4.86c-1.344 0-2.428-1.09-2.428-2.43s1.084-2.43 2.428-2.43m4.72 6.7h4.02v1.84h.058c.56-1.058 1.927-2.176 3.965-2.176 4.238 0 5.02 2.792 5.02 6.42v7.395h-4.183v-6.56c0-1.564-.03-3.574-2.178-3.574-2.18 0-2.514 1.7-2.514 3.46v6.668h-4.187V12.61z" fill="#FFF"></path>
                                                    </svg>
                                                </span>
                                            </a>
                                            <a href="https://www.addtoany.com/add_to/whatsapp?linkurl={{ Request::url() }}&linknote=">
                                                <span style="background-color: rgb(18, 175, 10);">
                                                    <svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="20" height="20">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" fill="#FFF" d="M16.21 4.41C9.973 4.41 4.917 9.465 4.917 15.7c0 2.134.592 4.13 1.62 5.832L4.5 27.59l6.25-2.002a11.241 11.241 0 005.46 1.404c6.234 0 11.29-5.055 11.29-11.29 0-6.237-5.056-11.292-11.29-11.292zm0 20.69c-1.91 0-3.69-.57-5.173-1.553l-3.61 1.156 1.173-3.49a9.345 9.345 0 01-1.79-5.512c0-5.18 4.217-9.4 9.4-9.4 5.183 0 9.397 4.22 9.397 9.4 0 5.188-4.214 9.4-9.398 9.4zm5.293-6.832c-.284-.155-1.673-.906-1.934-1.012-.265-.106-.455-.16-.658.12s-.78.91-.954 1.096c-.176.186-.345.203-.628.048-.282-.154-1.2-.494-2.264-1.517-.83-.795-1.373-1.76-1.53-2.055-.158-.295 0-.445.15-.584.134-.124.3-.326.45-.488.15-.163.203-.28.306-.47.104-.19.06-.36-.005-.506-.066-.147-.59-1.587-.81-2.173-.218-.586-.46-.498-.63-.505-.168-.007-.358-.038-.55-.045-.19-.007-.51.054-.78.332-.277.274-1.05.943-1.1 2.362-.055 1.418.926 2.826 1.064 3.023.137.2 1.874 3.272 4.76 4.537 2.888 1.264 2.9.878 3.43.85.53-.027 1.734-.633 2-1.297.266-.664.287-1.24.22-1.363-.07-.123-.26-.203-.54-.357z"></path>
                                                    </svg>
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Features -->
                            <div class="sp-product-features">
                                <div class="sp-feature-item">
                                    <i class="fas fa-truck sp-feature-icon"></i>
                                    <span>৭ দিনের হ্যাপি রিটার্ন</span>
                                </div>
                                <div class="sp-feature-item">
                                    <i class="icofont icofont-cash-on-delivery sp-feature-icon"></i>
                                    <span>ক্যাশ অন ডেলিভারি</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Products Sidebar -->
                    <div class="col-md-3">
                        <div class="sp-related-sidebar">
                            <h3>সম্পর্কিত বই</h3>
                            <?php
                            foreach ($product->categories as $s) {
                                $ppis = $s->id;
                            }
                            
                            $productIds = DB::table('category_product')->where('category_id', $ppis)->get()->pluck('product_id');
                            $products = App\Models\Product::whereIn('id', $productIds)->where('status', true)->inRandomOrder()->take(5)->get();
                            ?>

                            @foreach ($products as $producter)
                                <x-single-related :product="$producter" classes="" />
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details Section -->
            <div class="sp-product-details-section">
                <div class="sp-details-header">
                    📚 Product Specification & Summary
                </div>
                
                <div class="sp-accordion-navigation">
                    <button class="collapsed" data-toggle="collapse" data-target="#spDescription" aria-expanded="true" aria-controls="spDescription">
                        Summary
                    </button>
                    @if ($product->book == 1 && $product->author)
                        <button class="collapsed" data-toggle="collapse" data-target="#spSpecification" aria-expanded="false" aria-controls="spSpecification">
                            Specification
                        </button>
                        <button class="collapsed" data-toggle="collapse" data-target="#spAuthor" aria-expanded="false" aria-controls="spAuthor">
                            Author
                        </button>
                    @endif
                    <button class="collapsed" data-toggle="collapse" data-target="#spReviews" aria-expanded="false" aria-controls="spReviews">
                        Reviews({{ $product->reviews->count() }})
                    </button>
                    <button class="collapsed" data-toggle="collapse" data-target="#spComments" aria-expanded="false" aria-controls="spComments">
                        Ask Question({{ $product->comments->where('parent_id', null)->count() }})
                    </button>
                </div>

                <div id="spAccordion" style="width: 100%;">
                    <div id="spDescription" class="collapse show" aria-labelledby="spHeadingTwo" data-parent="#spAccordion">
                        <div class="sp-accordion-content">
                            {!! $product->full_description !!}
                        </div>
                    </div>

                    @if ($product->book == 1 && $product->author)
                        <div id="spAuthor" class="collapse" aria-labelledby="spHeadingThrees" data-parent="#spAccordion">
                            <div class="sp-accordion-content">
                                <div class="sp-author-section">
                                    <img class="sp-author-avatar" src="{{ asset('/') }}/uploads/admin/{{ $product->author->img }}">
                                    <div class="sp-author-info">
                                        <h4>{{ $product->author->name }}</h4>
                                        {!! $product->author->bio !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="spSpecification" class="collapse" aria-labelledby="spHeadingThree" data-parent="#spAccordion">
                            <div class="sp-accordion-content">
                                <table class="sp-spec-table">
                                    <tr><th>Title</th><td>{{ $product->title }}</td></tr>
                                    <tr><th>Author</th><td><a href="{{ route('author.product', ['slug' => $product->author_id]) }}">{{ $product->author->name }}</a></td></tr>
                                    <tr><th>Publisher</th><td>
                                        @if($product->user && $product->user->shop_info)
                                            <a href="{{ route('vendor', $product->user->shop_info->slug) }}">{{ $product->user->shop_info->name }}</a>
                                        @else
                                            Not Available
                                        @endif
                                    </td></tr>
                                    <tr><th>ISBN</th><td>{{ $product->isbn }}</td></tr>
                                    <tr><th>Edition</th><td>{{ $product->edition }}</td></tr>
                                    <tr><th>Number of Pages</th><td>{{ $product->pages }}</td></tr>
                                    <tr><th>Country</th><td>{{ $product->country }}</td></tr>
                                    <tr><th>Language</th><td>{{ $product->language }}</td></tr>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div id="spReviews" class="collapse" aria-labelledby="spHeadingThree" data-parent="#spAccordion">
                        <div class="sp-accordion-content">
                            <div class="sp-review-summary">
                                <span class="heading">Product Reviews</span>
                                <p>{{ $product->reviews->count() }} reviews for this product.</p>
                            </div>

                            @forelse ($product->reviews as $review)
                                <div class="sp-review-item">
                                    <div class="sp-review-header">
                                        @if($review->user)
                                            <img class="sp-review-avatar" src="{{ $review->user->avatar == 'default.png' ? '/default/default.png' : '/uploads/admin/' . $review->user->avatar }}" alt="">
                                            <div>
                                                <div class="sp-reviewer-name">{{ $review->user->name }}</div>
                                            </div>
                                        @else
                                            <div>
                                                <div class="sp-reviewer-name">Anonymous User</div>
                                            </div>
                                        @endif
                                    </div>
                                    <p>{{ $review->body }}</p>
                                    @if($review->file || $review->file2 || $review->file3 || $review->file4 || $review->file5)
                                        <div class="d-flex crv" style="gap: 10px; margin-top: 15px;">
                                            @if ($review->file)
                                                <a href="{{ asset('/') }}uploads/review/{{ $review->file }}">
                                                    <img width="80px" height="80px" style="object-fit: cover; border-radius: 8px;" src="{{ asset('/') }}uploads/review/{{ $review->file }}">
                                                </a>
                                            @endif
                                            @if ($review->file2)
                                                <a href="{{ asset('/') }}uploads/review/{{ $review->file2 }}">
                                                    <img width="80px" height="80px" style="object-fit: cover; border-radius: 8px;" src="{{ asset('/') }}uploads/review/{{ $review->file2 }}">
                                                </a>
                                            @endif
                                            @if ($review->file3)
                                                <a href="{{ asset('/') }}uploads/review/{{ $review->file3 }}">
                                                    <img width="80px" height="80px" style="object-fit: cover; border-radius: 8px;" src="{{ asset('/') }}uploads/review/{{ $review->file3 }}">
                                                </a>
                                            @endif
                                            @if ($review->file4)
                                                <a href="{{ asset('/') }}uploads/review/{{ $review->file4 }}">
                                                    <img width="80px" height="80px" style="object-fit: cover; border-radius: 8px;" src="{{ asset('/') }}uploads/review/{{ $review->file4 }}">
                                                </a>
                                            @endif
                                            @if ($review->file5)
                                                <a href="{{ asset('/') }}uploads/review/{{ $review->file5 }}">
                                                    <img width="80px" height="80px" style="object-fit: cover; border-radius: 8px;" src="{{ asset('/') }}uploads/review/{{ $review->file5 }}">
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center" style="padding: 40px;">
                                    <i class="far fa-comments" style="font-size: 4rem; color: var(--sp-text-light); margin-bottom: 20px;"></i>
                                    <h3 style="color: var(--sp-text-light);">No Reviews Yet</h3>
                                    <p style="color: var(--sp-text-light);">Be the first to review this book!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div id="spComments" class="collapse" aria-labelledby="spHeadingThree" data-parent="#spAccordion">
                        <div class="sp-accordion-content">
                            @forelse ($product->comments->where('parent_id',null) as $comment)
                                <div class="sp-comment-item">
                                    <div class="sp-review-header">
                                        @if($comment->user)
                                            <img class="sp-review-avatar" src="{{ $comment->user->avatar == 'default.png' ? '/default/default.png' : '/uploads/admin/' . $comment->user->avatar }}" alt="">
                                            <div>
                                                <div class="sp-reviewer-name">{{ $comment->user->name }}</div>
                                                <small style="color: var(--sp-text-light);">{{ $comment->created_at->diffForHumans() }}</small>
                                            </div>
                                        @else
                                            <div>
                                                <div class="sp-reviewer-name">Anonymous User</div>
                                                <small style="color: var(--sp-text-light);">{{ $comment->created_at->diffForHumans() }}</small>
                                            </div>
                                        @endif
                                        @if (auth()->check() && auth()->user()->role_id == 1)
                                            <div style="margin-left: auto;">
                                                @auth
                                                    <a href="javascript:void(0)" id="sp-comment-reply" data-id="{{ $comment->id }}" data-slug="{{ $product->slug }}" style="color: var(--sp-primary); text-decoration: none;">
                                                        <i class="fa fa-reply"></i> Reply
                                                    </a>
                                                @endauth
                                            </div>
                                        @endif
                                    </div>
                                    <p>{{ $comment->body }}</p>
                                    
                                    @forelse ($comment->replies as $reply)
                                        <div class="sp-comment-item" style="margin-left: 40px; margin-top: 15px; background: var(--sp-bg-light);">
                                            <div class="sp-review-header">
                                                @if($reply->user)
                                                    <img class="sp-review-avatar" src="{{ $reply->user->avatar == 'default.png' ? '/default/default.png' : '/uploads/admin/' . $reply->user->avatar }}" alt="">
                                                    <div>
                                                        <div class="sp-reviewer-name">{{ $reply->user->name }}</div>
                                                        <small style="color: var(--sp-text-light);">{{ $reply->created_at->diffForHumans() }}</small>
                                                    </div>
                                                @else
                                                    <div>
                                                        <div class="sp-reviewer-name">Anonymous User</div>
                                                        <small style="color: var(--sp-text-light);">{{ $reply->created_at->diffForHumans() }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                            <p>{{ $reply->body }}</p>
                                        </div>
                                    @empty
                                    @endforelse
                                    
                                    <div class="sp-reply-box"></div>
                                </div>
                            @empty
                                <div class="text-center" style="padding: 40px;">
                                    <i class="far fa-question-circle" style="font-size: 4rem; color: var(--sp-text-light); margin-bottom: 20px;"></i>
                                    <h3 style="color: var(--sp-text-light);">No Questions Yet</h3>
                                    <p style="color: var(--sp-text-light);">Be the first to ask a question about this book!</p>
                                </div>
                            @endforelse

                            @auth
                                <form class="sp-comment-form" action="{{ route('comment', $product->slug) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="spComment" style="font-weight: 600; margin-bottom: 10px;"><strong>Ask Your Question</strong></label>
                                        <textarea class="form-control" name="comment" id="spComment" placeholder="What would you like to know about this book?" required></textarea>
                                        @error('comment')
                                            <small class="form-text text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <button type="submit" class="sp-btn-buy-now">
                                        <i class="fas fa-paper-plane"></i> Submit Question
                                    </button>
                                </form>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <!-- Similar Products Section -->
            <div class="sp-similar-products-section">
                <h3>📖 একই বিভাগের অন্যান্য বই</h3>
                <?php
                foreach ($product->categories as $s) {
                    $ppis = $s->id;
                }
                
                $productIds = DB::table('category_product')->where('category_id', $ppis)->get()->pluck('product_id');
                $products = App\Models\Product::whereIn('id', $productIds)->where('status', true)->inRandomOrder()->take(18)->get();
                ?>

                <div class="row">
                    @forelse ($products as $product_item)
                        <x-product-grid-view :product="$product_item" classes="" />
                    @empty
                        <x-product-empty-component />
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('/') }}assets/frontend/js/jquery.flexslider.js"></script>
    <script src="{{ asset('/') }}assets/frontend/js/image-zoom.js"></script>
    <script src="{{ asset('/') }}assets/frontend/js/toast.min.js"></script>
    <script src="https://vjs.zencdn.net/8.3.0/video.min.js"></script>

    <script>
        // Toggle description function for single product
        function spToggleDescription() {
            const truncatedText = document.getElementById('sp-truncated-text');
            const fullText = document.getElementById('sp-full-text');
            const seeMoreBtn = document.getElementById('spSeeMoreBtn');
            
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

            // Toast notification function
            function spShowToast(heading, message, icon) {
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

            // Smooth scroll to sections
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                
                let target = $($(this).attr('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 500);
                }
            });

            // Handle image gallery clicks
            $('.my-gallery-image').on('click', function(e) {
                e.preventDefault();
                
                // Get image source
                let imageSrc = $(this).attr('src');
                
                // Create modal or lightbox effect
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
            });

            // Handle escape key to close modals
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $('.sp-image-modal').remove();
                }
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
        });
    </script>
@endpush