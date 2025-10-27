@extends('layouts.frontend.app')

@push('meta')
<meta name='description' content="Products from the {{ $campaign->name ?? 'Active' }} Campaign"/>
<meta name='keywords' content="{{ $campaign->name ?? 'Campaign' }}, @foreach($products as $product){{$product->title.', '}}@endforeach" />
@endpush

@section('title', $campaign->name ?? 'Campaign Products')

@push('css')
    <link rel="stylesheet" href="{{asset('/')}}assets/frontend/css/jquery-ui1.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        :root {
            --primary-color-start: #667eea;
            --primary-color-end: #764ba2;
            --text-color-dark: #1f2937;
            --text-color-light: #6b7280;
            --background-color: #f8fafc;
            --surface-color: #ffffff;
            --border-color: #e5e7eb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-color);
        }
        
        * {
            box-sizing: border-box;
        }

        /* Campaign Hero Banner (Assumes $campaign object is available) */
        .collection-hero {
            position: relative;
            height: 40vh;
            min-height: 300px;
            /* Use campaign cover photo if available, otherwise a placeholder */
            background-image: url('{{ asset('uploads/collection/' . ($campaign->cover_photo ?? '')) }}');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin-bottom: 3rem;
        }

        .collection-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            padding: 0 1rem;
        }

        .collection-title {
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
            animation: fadeInDown 0.8s ease-out;
        }

        .collection-description {
            font-size: 1.125rem;
            margin-top: 0.5rem;
            color: rgba(255,255,255,0.9);
            text-shadow: 0 1px 3px rgba(0,0,0,0.5);
            animation: fadeInUp 0.8s ease-out 0.2s;
            animation-fill-mode: both;
        }
        
        .product-page {
            padding-bottom: 2rem;
            position: relative;
        }
        
        .main-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .sidebar {
            background: var(--surface-color);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border-color);
            height: fit-content;
            position: sticky;
            top: 2rem;
            transition: all 0.3s ease;
        }
        
        .products-container {
            min-height: 800px;
        }
        
        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--surface-color);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        
        /* === RESPONSIVE GRID UPDATES === */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        @media (min-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 1.5rem;
            }
        }

        @media (min-width: 1024px) {
            .products-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        /* === END RESPONSIVE GRID UPDATES === */

        .products-list {
            display: grid;
            gap: 1.5rem;
        }
        
        .product {
            background: var(--surface-color);
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
        }
        
        .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Loading Animation */
        .ajax-loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            width: 100%;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid var(--primary-color-start);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        /* View Toggle Buttons */
        .view-toggle {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            background-color: #f3f4f6;
            padding: 0.25rem;
            border-radius: 8px;
        }
        
        .view-btn {
            padding: 0.5rem;
            border: none;
            background: transparent;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-color-light);
        }

        .view-btn svg {
            width: 20px;
            height: 20px;
        }
        
        .view-btn:hover {
            color: var(--text-color-dark);
        }
        
        .view-btn.active {
            background: var(--surface-color);
            color: var(--primary-color-start);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-color-light);
            grid-column: 1 / -1;
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Animations */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* === NEW STYLES FOR MOBILE FILTER === */
        
        .btn-filter-toggle {
            display: none;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            color: var(--text-color-dark);
        }
        .btn-filter-toggle:hover {
            background-color: #f8f9fa;
        }
        .btn-filter-toggle svg {
            color: var(--text-color-light);
        }

        .sidebar-header-mobile {
            display: none;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .sidebar-header-mobile h5 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }
        .btn-filter-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            color: var(--text-color-light);
        }
        .btn-filter-close:hover {
            color: var(--text-color-dark);
        }

        .filter-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .filter-overlay.active {
            display: block;
            opacity: 1;
        }
        
        .filter-desktop-component {
            display: block;
        }
        /* === END NEW STYLES === */
        
        /* Responsive improvements */
        @media (max-width: 1024px) {
            .main-container {
                grid-template-columns: 250px 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .collection-title {
                font-size: 2rem;
            }
            .collection-description {
                font-size: 1rem;
            }
            
            .main-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 300px;
                max-width: 80%;
                height: 100%;
                z-index: 999;
                transform: translateX(-100%);
                transition: transform 0.3s ease-out;
                overflow-y: auto;
                border-radius: 0;
            }
            .sidebar.active {
                transform: translateX(0);
            }

            .sidebar-header-mobile {
                display: flex;
            }

            .btn-filter-toggle {
                display: flex;
            }
            
            .filter-desktop-component {
                display: none;
            }
        }

        /* === INLINED: product-grid-view.blade.php STYLES === */
        .modern-product-col {
            padding: 0;
        }

        .modern-product-card {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            display: flex;
            flex-direction: column;
            position: relative;
            border: 1px solid #e5e7eb;
            height: 100%;
        }

        .modern-product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .product-image-container {
            position: relative;
            overflow: hidden;
        }

        .product-image-container img {
            width: 100%;
            display: block;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .modern-product-card:hover .product-image-container img {
            transform: scale(1.05);
        }

        .product-content {
            padding: 12px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-title-link {
            text-decoration: none;
            color: #111827;
        }

        .product-title {
            font-size: 1rem;
            font-weight: 500;
            line-height: 1.4;
            margin: 0 0 4px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 45px;
        }

        .product-title-link:hover .product-title {
            text-decoration: underline;
            color: #3b82f6;
        }

        .product-vendor {
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 8px;
        }

        .stars {
            color: #f59e0b;
        }

        .review-count {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .price-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-top: auto;
            margin-bottom: 12px;
        }
        
        .original-price {
            font-size: 0.9rem;
            color: #6b7280;
            text-decoration: line-through;
        }
        
        .current-price {
            font-size: 1.2rem;
            font-weight: 600;
            color: #111827;
        }
        
        .add-to-cart-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 8px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            color: #fff;
            font-weight: 600;
            background-color: #000000;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .add-to-cart-btn:hover {
            background-color: #333333;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        /* === END: product-grid-view.blade.php STYLES === */


        /* === INLINED: product-list-view.blade.php STYLES === */
        .products-list .modern-product-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: auto;
            display: flex;
            flex-direction: row;
            position: relative;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .products-list .modern-product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .products-list .product-image-container {
            position: relative;
            overflow: hidden;
            height: 200px;
            width: 200px;
            flex-shrink: 0;
            background: #f8fafc;
        }

        .products-list .product-image-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
            background: white;
        }

        .products-list .modern-product-card.book-product .product-image-container img {
            object-fit: contain;
            padding: 8px;
        }

        .products-list .modern-product-card:not(.book-product) .product-image-container img {
            object-fit: cover;
            padding: 0;
        }

        .products-list .modern-product-card:hover .product-image-container img {
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

        .products-list .product-content {
            padding: 1.5rem;
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

        .products-list .product-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            line-height: 1.4;
            margin: 6px 0 8px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: auto;
        }

        .products-list .product-title:hover {
            color: var(--primary_color, #3b82f6);
        }

        .products-list .price-container {
            margin-bottom: 8px;
        }

        .products-list .current-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary_color, #3b82f6);
        }

        .products-list .original-price {
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

        .products-list .modern-product-card:hover .quick-view-overlay {
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

        .products-list .modern-product-card.book-product .product-image-container {
            height: 220px;
            width: 220px;
        }

        .products-list .modern-product-card.book-product {
            min-height: auto;
        }

        .products-list .add-to-cart-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px; 
            flex: 1;
            padding: 12px 16px;
            border: none;
            border-radius: 10px;
            text-decoration: none;
            color: #fff;
            font-weight: 600;
            background-color: #000000;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .products-list .add-to-cart-btn:hover {
            background-color: #333333;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .product-title {
                font-size: 0.9rem;
                min-height: 40px;
            }

            .products-list .modern-product-card {
                flex-direction: column;
                height: auto;
                min-height: 300px;
            }
            
            .products-list .modern-product-card.book-product {
                min-height: 320px;
            }
            
            .products-list .modern-product-card.book-product .product-image-container {
                height: 180px;
                width: 100%;
            }
            
            .products-list .product-image-container {
                height: 160px;
                width: 100%;
            }
            
            .products-list .product-image-container img {
                object-fit: contain;
                padding: 4px;
            }
            
            .products-list .modern-product-card:not(.book-product) .product-image-container img {
                object-fit: contain;
                padding: 4px;
            }
            
            .products-list .product-content {
                padding: 10px;
            }
            
            .products-list .product-title {
                font-size: 0.85rem;
                margin: 4px 0 6px 0;
                min-height: 2.2rem;
                -webkit-line-clamp: 2;
            }
            
            .products-list .current-price {
                font-size: 1.1rem;
            }
            
            .products-list .action-buttons {
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
        /* === END: product-list-view.blade.php STYLES === */
        
        /* Campaign Comment Section Style */
        .bef-footer .items {
            padding: 20px;
            margin-bottom: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1)
        }
    </style>
@endpush

@section('content')

{{-- ASSUMPTION: $campaign object with name and cover_photo is available --}}
<div class="collection-hero" style="background-image: url('{{ asset('uploads/campaign/' . ($campaign->cover_photo ?? '')) }}');">
    <div class="hero-content">
        <h1 class="collection-title">{{ $campaign->name ?? 'Campaign Products' }}</h1>
        <p class="collection-description">
            Explore our curated selection of products in the "{{ $campaign->name ?? 'Active Campaign' }}" collection. Find your next favorite item today!
        </p>
    </div>
</div>

<div class="product-page">
    <div class="filter-overlay" id="filter-overlay"></div>

    <div class="main-container">
        <aside class="sidebar" id="filter-sidebar">
            <div class="sidebar-header-mobile">
                <h5>Filters</h5>
                <button class="btn-filter-close" id="filter-close-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            {{-- Filter component adjusted for campaign and using $slug for value --}}
            <x-filter-search-component name="campaign" :value="$slug" />
        </aside>
        
        <main class="products-container">
            <div class="filter-header">
                <button class="btn-filter-toggle" id="filter-toggle-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0M3.75 18H7.5m3-6h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0M3.75 12H7.5" />
                    </svg>
                    <span>Filter</span>
                </button>

                <div class="view-toggle">
                    <button class="view-btn active" id="grid-view-btn" aria-label="Grid View">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 8.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6A2.25 2.25 0 0115.75 3.75h2.25A2.25 2.25 0 0120.25 6v2.25a2.25 2.25 0 01-2.25 2.25h-2.25A2.25 2.25 0 0113.5 8.25V6zM13.5 15.75A2.25 2.25 0 0115.75 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z" /></svg>
                    </button>
                    <button class="view-btn" id="list-view-btn" aria-label="List View">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    </button>
                </div>
            </div>
            
            <div id="product-area">
                <div class="products-grid" id="grid-view">
                    @forelse ($products as $product)
                        <div class="product modern-product-col pxc">
                            <div class="modern-product-card">

                                <a href="{{route('product.cam.details', $product->pid)}}" class="product-image-container">
                                    <img src="{{asset('uploads/product/'.$product->image)}}" alt="{{$product->title}}">
                                </a>

                                <div class="product-content">
                                    <a href="{{route('product.cam.details', $product->pid)}}" class="product-title-link">
                                        <h5 class="product-title">{{$product->title}}</h5>
                                    </a>
                                    
                                    <p class="product-vendor">OPPS</p>

                                    @if(isset($product->reviews) && $product->reviews->count() > 0)
                                        @php
                                            $rating = round($product->reviews->avg('rating'));
                                        @endphp
                                        <div class="product-rating">
                                            <div class="stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $rating)
                                                        <span>&#9733;</span>
                                                    @else
                                                        <span>&#9734;</span>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="review-count">({{$product->reviews->count()}})</span>
                                        </div>
                                    @endif
                                    
                                    @php
                                        $currency = setting('CURRENCY_CODE_MIN') ?? 'TK';
                                        // Use campaign price (cprice) as the primary discounted price
                                        $current_price = $product->cprice ?? $product->discount_price;
                                        $regular_price = $product->regular_price;
                                    @endphp
                                    <div class="price-container">
                                        {{-- Only show regular price if there is a campaign/discount price lower than it --}}
                                        @if($current_price && $current_price < $regular_price)
                                            <span class="original-price">{{$currency}} {{number_format($regular_price, 2)}} BDT</span>
                                            <span class="current-price">{{$currency}} {{number_format($current_price, 2)}} BDT</span>
                                        @else
                                            <span class="current-price">{{$currency}} {{number_format($regular_price, 2)}} BDT</span>
                                        @endif
                                    </div>

                                    {{-- Use the campaign-specific Add to Cart ID and Route --}}
                                    <button class="add-to-cart-btn" id="productInfo1" data-url="{{route('camp.product.info', $product->pid)}}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                        </svg>
                                        <span>Add to cart</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-state-icon">🛍️</div>
                            <x-product-empty-component />
                        </div>
                    @endforelse
                </div>
                
                <div class="products-list" id="list-view" style="display: none;">
                    @forelse ($products as $product)
                        <div class="product pxc">
                             @php
                                // Campaign price calculation
                                $currency_icon = setting('CURRENCY_ICON') ?? '৳';
                                $current_price = $product->cprice ?? $product->discount_price;
                                $regular_price = $product->regular_price;
                                $price_display = $current_price > 0 ? $current_price : $regular_price;

                                $has_discount = ($current_price > 0 && $current_price < $regular_price);
                                if ($has_discount) {
                                    $discount_percent = round((($regular_price - $current_price) / $regular_price) * 100);
                                }
                            @endphp
                            <div class="modern-product-card {{ 
                                (isset($product->category) && (
                                    str_contains(strtolower($product->category->name ?? ''), 'book') || 
                                    str_contains(strtolower($product->category->name ?? ''), 'bengali_book') ||
                                    str_contains(strtolower($product->category->name ?? ''), 'kitab')
                                )) || 
                                str_contains(strtolower($product->title), 'book') || 
                                str_contains(strtolower($product->title), 'bengali_book') 
                                ? 'book-product' : '' }}">

                                <div class="product-image-container">
                                    <a href="{{route('product.cam.details', $product->pid)}}">
                                        <img src="{{asset('uploads/product/'.$product->image)}}" alt="{{$product->title}}">
                                    </a>

                                    @if($has_discount)
                                        <div class="discount-badge">{{$discount_percent}}% OFF</div>
                                    @endif

                                    @php
                                        // Wishlist logic from collection file
                                        $hw = App\Models\wishlist::where('product_id', $product->id)->where('user_id', auth()->id())->first();
                                        $wishlist_color = $hw ? '#ef4444' : '#6b7280';
                                    @endphp
                                    <form action="{{route('wishlist.add')}}" method="post" class="wishlist-form">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{$product->slug}}">
                                        <button type="submit" class="wishlist-btn" title="Add to Wishlist">
                                            <i class="{{$hw ? 'fas' : 'far'}} fa-heart" style="color: {{$wishlist_color}}"></i>
                                        </button>
                                    </form>

                                    <div class="quick-view-overlay">
                                        <a href="{{route('product.cam.details', $product->pid)}}" class="quick-view-btn">
                                            <i class="fas fa-search"></i>
                                            Quick View
                                        </a>
                                    </div>
                                </div>

                                <div class="product-content">
                                    <div class="rating-container">
                                        @if(isset($product->reviews))
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
                                        @endif
                                    </div>

                                    <a href="{{route('product.cam.details', $product->pid)}}" style="text-decoration: none;">
                                        <h5 class="product-title">{{implode(' ', array_slice(explode(' ', $product->title), 0, 10))}}...</h5>
                                    </a>

                                    <div class="price-container">
                                        @if($has_discount)
                                            <span class="current-price">{{ $currency_icon }}{{number_format($current_price, 2)}}</span>
                                            <span class="original-price">{{ $currency_icon }}{{number_format($regular_price, 2)}}</span>
                                        @else
                                            <span class="current-price">{{ $currency_icon }}{{number_format($regular_price, 2)}}</span>
                                        @endif
                                    </div>

                                    <div class="product-message">
                                        @if ($product->prdct_extra_msg)
                                            <small>{{ $product->prdct_extra_msg }}</small>
                                        @endif
                                    </div>

                                    <div class="action-buttons">
                                        @if($product->quantity <= '0')
                                            <a href="{{route('product.cam.details', $product->pid)}}" class="btn-primary btn-out-of-stock">
                                                <i class="fas fa-clock"></i>
                                                Pre Order
                                            </a>
                                        @else
                                            {{-- Use the campaign-specific Add to Cart ID and Route --}}
                                            <button class="add-to-cart-btn" id="productInfo1" data-url="{{route('camp.product.info', $product->pid)}}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                                </svg>
                                                <span>Add to cart</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-state-icon">🛍️</div>
                            <x-product-empty-component />
                        </div>
                    @endforelse
                </div>
            </div>
            
            <div class="ajax-loading" style="display: none;">
                <div class="loading-spinner"></div>
            </div>
        </main>
    </div>
</div>

{{-- Campaign Comment Section (Retained from original cam_product.blade.php) --}}
<div class="bef-footer">
    <div class="container" style="padding: 0">
        <div class="items">
            <div class="search-box">
                <h5 style="text-align: center;margin-bottom: 20px;">Leave Your Commment For This Campaing</h5>
               <div class="row">
                   <div class="col-md-2"></div>
                    <form class="col-md-8" action="{{route('campaing.comment')}}" method="Post">
                           @csrf
                        <div class="input-group">
                            <input class="sear" type="text" name="comment" placeholder="Type Your Comments">
                            <button class="input-group-addon components-bg" type="submit">Send </button>
                        </div>
                       <input class="sear" type="hidden" name="campaign_id" value="{{$id}}">
                    </form>
                    <div class="col-md-2"></div>
               </div>
            </div>
        </div>
    </div>
</div>
  
<x-add-cart-modal />
@include('components.cart-modal-attri')
@endsection

@push('js')
    <script src="{{asset('/')}}assets/frontend/js/jquery-ui.js"></script>

    <script>
        $(document).ready(function () {
            // Add staggered animation to initial products
            $('.product').each(function(index) {
                $(this).css('animation-delay', (index * 0.05) + 's');
            });

            // View Toggle Logic
            $('#grid-view-btn').on('click', function() {
                $(this).addClass('active');
                $('#list-view-btn').removeClass('active');
                $('#grid-view').show();
                $('#list-view').hide();
            });

            $('#list-view-btn').on('click', function() {
                $(this).addClass('active');
                $('#grid-view-btn').removeClass('active');
                $('#list-view').show();
                $('#grid-view').hide();
            });

            // Enhanced quantity controls
            $(document).on('click', '.value-plus, .value-minus', function () {
                var isPlus = $(this).hasClass('value-plus');
                var divUpd = $(this).parent().find('.value');
                var currentVal = parseInt(divUpd.val(), 10);
                var newVal = isPlus ? currentVal + 1 : (currentVal > 1 ? currentVal - 1 : 1);
                
                divUpd.val(newVal);
                $('input#qty').val(newVal);
            });

            // Enhanced AJAX cart functionality
            $(document).on('submit', '#addToCart', function(e) {
                e.preventDefault();

                let url      = $(this).attr('action');
                let type     = $(this).attr('method');
                let btn      = $(this);
                let formData = $(this).serialize();

                $.ajax({
                    type: type,
                    url: url,
                    data: formData,
                    dataType: 'JSON',
                    beforeSend: function() {
                        $(btn).attr('disabled', true);
                    },
                    success: function (response) {
                        if(response.alert!='Congratulations'){
             
                            $.toast({
                                heading: 'Warning',
                                text: response.message,
                                icon: 'warning',
                                position: 'top-right',
                                stack: false
                            });
                        }else{
                        $('span#total-cart-amount').text(response.subtotal);

                        $.toast({
                            heading: 'Congratulations',
                            text: response.message,
                            icon: 'success',
                            position: 'top-right',
                            stack: false
                        });

                        $('#cart-modal').modal('hide');}
                        
                    },
                    complete: function() {
                        $(btn).attr('disabled', false);
                    },
                    error: function(xhr) {
                        $.toast({
                            heading: xhr.status,
                            text: xhr.responseJSON.message,
                            icon: 'error',
                            position: 'top-right',
                            stack: false
                        });
                    }
                });
            });

            // Enhanced sorting
            $(document).on('change', 'select#sort', function() {
                $('input[name="sort"]').val($(this).val());
                $('form#form').submit();
            });

            // Price slider
            if ($("#slider-range").length) {
                $("#slider-range").slider({
                    range: true,
                    min: 0,
                    max: 9000,
                    values: [50, 6000],
                    slide: function (event, ui) {
                        $("#amount").val("{!! setting('CURRENCY_CODE_MIN') ?? 'TK' !!}" + ui.values[0] + " - {!! setting('CURRENCY_CODE_MIN') ?? 'TK' !!}" + ui.values[1]);
                    }
                });
                $("#amount").val("{!! setting('CURRENCY_CODE_MIN') ?? 'TK' !!}" + $("#slider-range").slider("values", 0) + " - {!! setting('CURRENCY_CODE_MIN') ?? 'TK' !!}" + $("#slider-range").slider("values", 1));
            }

            // === NEW MOBILE FILTER JS ===
            var $sidebar = $('#filter-sidebar');
            var $overlay = $('#filter-overlay');

            // Open Sidebar
            $('#filter-toggle-btn').on('click', function() {
                $sidebar.addClass('active');
                $overlay.addClass('active');
            });

            // Close Sidebar
            function closeFilter() {
                $sidebar.removeClass('active');
                $overlay.removeClass('active');
            }

            $('#filter-close-btn').on('click', closeFilter);
            $('#filter-overlay').on('click', closeFilter);
            // === END NEW MOBILE FILTER JS ===
        });
    </script>
    
    <script>
        var site_url = "{{ url('/') }}";   
        var page = 1;
        var isLoading = false;
        var noMoreProducts = false;

        function load_more(page) {
            if (isLoading || noMoreProducts) return;

            var slug = '{!! $slug !!}'; // Use the campaign slug
            var _totalCurrentResult = $("#grid-view .product").length;
            
            $.ajax({
                // Use the campaign-specific route
                url: `${site_url}/Campaign/product/${slug}?page=${page}`,
                type: "get",
                datatype: "html",
                data: { skip: _totalCurrentResult },
                beforeSend: function() {
                    isLoading = true;
                    $('.ajax-loading').show();
                },
                success: function(response) {
                    var result = $.parseJSON(response);
                    $('.ajax-loading').hide();
                    
                    if(result[0].length > 10) {
                        var $newGridItems = $(result[0]);
                        var $newListItems = $(result[1]);
                        
                        $newGridItems.css('opacity', '0');
                        $newListItems.css('opacity', '0');
                        
                        $("#grid-view").append($newGridItems);
                        $("#list-view").append($newListItems);
                        
                        $newGridItems.each(function(index) {
                            $(this).css('animation-delay', (index * 0.05) + 's').animate({opacity: 1}, 300);
                        });
                        $newListItems.each(function(index) {
                            $(this).css('animation-delay', (index * 0.05) + 's').animate({opacity: 1}, 300);
                        });

                        isLoading = false;
                    } else {
                        noMoreProducts = true;
                    }
                },
                error: function(jqXHR, ajaxOptions, thrownError) {
                    console.log('Server error for loading more products');
                    $('.ajax-loading').hide();
                    isLoading = false;
                }
            });
        }

        $(window).scroll(function() {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 400) {
                page++;
                load_more(page);
            }
        });
    </script>

    <script>
        // Wishlist form submit (Delegated from collection-product.blade.php)
        $(document).on('submit', '.wishlist-form', function(e) {
            e.preventDefault();
            
            let action = $(this).attr('action');
            var formData = $(this).serialize();
            var form = $(this); // Get the specific form that was submitted
            
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
                    
                    const button = form.find('.wishlist-btn i');
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

        function responseMessage(heading, message, icon) {
            $.toast({
                heading: heading,
                text: message,
                icon: icon,
                position: 'top-right',
                stack: false
            });
        }

        function loader(status) {
            if (status == true) {
                $('#loading-image').removeClass('d-none').addClass('d-block');
            } else {
                $('#loading-image').addClass('d-none').removeClass('d-block');
            }
        }
    </script>
@endpush