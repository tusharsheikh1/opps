<style>
@font-face{
    font-family: 'Muli';
    src: url('{{asset("/")}}assets/frontend/font/Muli/Muli-VariableFont_wght.ttf');
    font-display: swap;
}

/* Global Header Styles */
header {
    font-family: 'Muli', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    position: sticky;
    top: 0;
    z-index: 1000;
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    margin: 0;
    padding: 0;
}

header.scrolled {
    box-shadow: 0 2px 20px rgba(0,0,0,0.1);
    backdrop-filter: blur(20px);
    background: rgba(255,255,255,0.98);
}

header .main-menu {
    z-index: 999999;
}



/* Main Header */
.top-header {
    background: #ffffff;
    padding: 16px 0;
    border-bottom: 1px solid #f3f4f6;
    margin: 0;
}



.header-content {
    display: flex;
    align-items: center;
    gap: 32px;
    position: relative;
}

/* Logo */
.logo-area {
    flex-shrink: 0;
}

.logo-area img {
    max-height: 40px;
    width: auto;
}

/* Search Box */
.search-container {
    flex: 1;
    max-width: 600px;
    position: relative;
}

.search-box {
    position: relative;
    width: 100%;
}

.search-input-group {
    display: flex;
    background: #f9fafb;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.search-input-group:focus-within {
    border-color: #f97316;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
    background: #ffffff;
}

.search-input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 12px 16px;
    font-size: 14px;
    outline: none;
    color: #374151;
    font-weight: 400;
}

.search-input::placeholder {
    color: #9ca3af;
    font-weight: 400;
}

.search-btn {
    background: #374151;
    border: none;
    padding: 12px 20px;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-btn:hover {
    background: #1f2937;
}

.search-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}

/* Header Actions */
.header-actions {
    display: flex;
    align-items: center;
    gap: 24px;
    flex-shrink: 0;
}

.action-item {
    position: relative;
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #6b7280;
    font-weight: 500;
    transition: all 0.2s ease;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
}

.action-item:hover {
    color: #f97316;
    background: #fef3c7;
    text-decoration: none;
}

.action-item i {
    font-size: 20px;
    margin-right: 8px;
}

.badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 16px;
    text-align: center;
    line-height: 1.2;
    box-shadow: 0 1px 3px rgba(239, 68, 68, 0.5);
}

/* Account/Login Buttons */
.auth-buttons {
    display: flex;
    align-items: center;
    gap: 12px;
}

.auth-btn {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.signin-btn {
    color: #6b7280;
    background: transparent;
    border-color: #d1d5db;
}

.signin-btn:hover {
    color: #374151;
    background: #f9fafb;
    text-decoration: none;
}

.signup-btn {
    color: white;
    background: #f97316;
    border-color: #f97316;
}

.signup-btn:hover {
    background: #ea580c;
    border-color: #ea580c;
    color: white;
    text-decoration: none;
}

.seller-btn {
    background: #3b82f6;
    color: white !important;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 13px;
    transition: all 0.2s ease;
    text-decoration: none;
}

.seller-btn:hover {
    background: #2563eb;
    color: white !important;
    text-decoration: none;
}

/* Logout Button */
.logout-btn {
    background: #ef4444;
    color: white !important;
    padding: 8px 16px;
    border-radius: 6px;
    transition: all 0.2s ease;
    font-weight: 500;
    font-size: 14px;
    text-decoration: none;
}

.logout-btn:hover {
    background: #dc2626;
    color: white !important;
    text-decoration: none;
}

/* Ensure content doesn't get hidden behind sticky header */
body {
    padding-top: 0;
    margin: 0;
}

/* Container adjustments */
.container {
    margin: 0 auto;
    padding-left: 15px;
    padding-right: 15px;
}

/* Mobile Menu Toggle */
.mobile-menu-toggle {
    display: none;
    flex-direction: column;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: all 0.2s ease;
    background: transparent;
    border: none;
    z-index: 1001;
}

.mobile-menu-toggle:hover {
    background: #f3f4f6;
}

.mobile-menu-toggle span {
    width: 20px;
    height: 2px;
    background: #374151;
    margin: 2px 0;
    transition: all 0.3s ease;
    border-radius: 1px;
}

.mobile-menu-toggle.active span:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.mobile-menu-toggle.active span:nth-child(2) {
    opacity: 0;
}

.mobile-menu-toggle.active span:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -6px);
}

/* Mobile Menu Overlay */
.mobile-menu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 9998;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.mobile-menu-overlay.active {
    opacity: 1;
    visibility: visible;
}

/* Mobile Menu Panel */
.mobile-menu-panel {
    position: fixed;
    top: 0;
    left: -100%;
    width: 280px;
    height: 100vh;
    background: #ffffff;
    z-index: 9999;
    transition: all 0.3s ease;
    box-shadow: 4px 0 20px rgba(0,0,0,0.1);
    overflow-y: auto;
    padding-top: 0; /* Account for sticky header */
}

.mobile-menu-panel.active {
    left: 0;
}

.mobile-menu-header {
    padding: 20px;
    border-bottom: 1px solid #f3f4f6;
    background: #f9fafb;
}

.mobile-menu-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #6b7280;
    cursor: pointer;
    float: right;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.mobile-menu-close:hover {
    background: #f3f4f6;
    color: #374151;
}

.mobile-menu-content {
    padding: 0;
}

.mobile-menu-section {
    border-bottom: 1px solid #f3f4f6;
}

.mobile-menu-section:last-child {
    border-bottom: none;
}

.mobile-menu-item {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    color: #374151;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    border-bottom: 1px solid rgba(243, 244, 246, 0.5);
}

.mobile-menu-item:hover {
    background: #f9fafb;
    color: #f97316;
    text-decoration: none;
}

.mobile-menu-item i {
    font-size: 18px;
    margin-right: 12px;
    width: 20px;
    text-align: center;
}

.mobile-menu-item .badge {
    position: static;
    margin-left: auto;
}



/* Enhanced Focus States */
*:focus {
    outline: 2px solid #f97316;
    outline-offset: 2px;
}

.search-input:focus {
    outline: none;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .header-content {
        gap: 24px;
    }
    
    .search-container {
        max-width: 400px;
    }
    
    .header-actions {
        gap: 16px;
    }
}

@media (max-width: 768px) {
    .top-header {
        padding: 8px 0;
    }
    
    .header-content {
        gap: 8px;
        flex-wrap: wrap;
    }
    
    /* Mobile: Hide desktop search, show mobile toggle */
    .search-container {
        display: none;
    }
    
    .mobile-menu-toggle {
        display: flex;
        order: 1;
    }
    
    .logo-area {
        order: 2;
        flex: 1;
        text-align: center;
    }
    
    .auth-buttons {
        display: none !important; /* Hide auth buttons on mobile */
    }
    
    .header-actions {
        order: 3;
        gap: 8px;
    }
    
    /* Enhanced mobile action items */
    .action-item {
        padding: 10px;
        border-radius: 8px;
        min-width: 44px;
        justify-content: center;
        position: relative;
    }
    
    .action-item i {
        font-size: 20px;
        margin-right: 0;
    }
    
    .action-item span {
        display: none;
    }
    
    /* Mobile badge positioning */
    .action-item .badge {
        top: -2px;
        right: -2px;
        font-size: 9px;
        padding: 1px 5px;
        min-width: 14px;
    }
    
    .logout-btn {
        padding: 8px 12px;
        font-size: 12px;
        min-width: auto;
    }
    
    .logo-area img {
        max-height: 36px;
    }
    
    /* Mobile Search Bar - Full Width Below Header */
    .mobile-search-bar {
        display: block;
        width: 100%;
        order: 4;
        flex-basis: 100%;
        margin-top: 8px;
    }
    
    .mobile-search-bar .search-input-group {
        border-radius: 8px;
        margin: 0;
    }
    
    .mobile-search-bar .search-input {
        padding: 12px 16px;
        font-size: 16px;
    }
    
    .mobile-search-bar .search-btn {
        padding: 12px 20px;
    }

    /* Mobile sticky header adjustments */
    header {
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    /* Auth buttons for mobile */
    .auth-buttons {
        gap: 8px;
    }
    
    .auth-btn {
        padding: 6px 12px;
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .header-actions {
        gap: 4px;
    }
    
    .action-item {
        padding: 8px;
        min-width: 40px;
    }
    
    .logo-area img {
        max-height: 32px;
    }
    
    .mobile-search-bar .search-input {
        padding: 12px 16px;
        font-size: 16px;
    }
    
    .mobile-search-bar .search-btn {
        padding: 12px 16px;
    }
}

/* Desktop Search Container */
@media (min-width: 769px) {
    .mobile-search-bar {
        display: none;
    }
}

/* Product-specific styles */
@if(!Request::is('/'))
.products .product .thumbnail {
    height: 190px !important;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.products .product .thumbnail:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

#list-view .product .thumbnail img {
    width: 200px;
    border-radius: 6px;
}

@media(max-width:767px) {
    #list-view .product .thumbnail img {
        width: inherit;
    }
    
    #list-view .product h4 {
        font-size: 17px;
        font-weight: 500;
        margin-top: 10px;
        line-height: 1.4;
        color: #374151;
    }
    
    #list-view .product .details {
        margin-left: 15px;
    }
    
    #list-view .product .details .dis-label {
        display: none;
    }
}
@endif

/* Additional Modern Touches */
.action-item:active {
    transform: translateY(0);
}

/* Animation Classes */
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

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.3s ease;
}

.slide-down {
    animation: slideDown 0.2s ease;
}
</style>

<header class="not-home">

    <!-- Main Header -->
    <div class="top-header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <div class="logo-area">
                    <a href="{{route('home')}}">
                        <img src="{{asset('uploads/setting/'.setting('logo'))}}" alt="Application Logo">
                    </a>
                </div>

                <!-- Desktop Search Box -->
                <div class="search-container">
                    <div class="search-box">
                        <form action="{{route('product.search')}}" method="GET">
                            <div class="search-input-group">
                                <input 
                                    type="search" 
                                    name="keyword" 
                                    class="search-input" 
                                    placeholder="Search For Products, Brands And More..."
                                    id="searchbox"
                                >
                                <button type="submit" class="search-btn" name="go">
                                    <i class="fal fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Header Actions on Right -->
                <div class="header-actions">
                    <!-- Wishlist -->
                    <a href="{{route('wishlist')}}" class="action-item" title="Wishlist">
                        <i class="fal fa-heart"></i>
                        <span class="d-none d-lg-inline">Wishlist</span>
                        @if(auth()->check())
                        <span class="badge">{{App\Models\wishlist::where('user_id',auth()->id())->count()}}</span>
                        @endif
                    </a>

                    <!-- Cart -->
                    <a href="{{route('cart')}}" class="action-item" title="Shopping Cart">
                        <i class="fal fa-shopping-bag"></i>
                        <span class="d-none d-lg-inline">Cart</span>
                        <span class="badge">{{Cart::count()}}</span>
                    </a>

                    <!-- Auth Buttons for Guest Users (Right Corner) -->
                    @guest
                    <div class="auth-buttons d-none d-lg-flex">
                        <a href="{{route('login')}}" class="auth-btn signin-btn">
                            <i class="fal fa-sign-in-alt"></i> Sign In
                        </a>
                        <a href="{{route('register')}}" class="auth-btn signup-btn">
                            <i class="fal fa-user-plus"></i> Sign Up
                        </a>
                    </div>
                    @else
                    <!-- Authenticated User Options (Right Corner) -->
                    <div class="auth-buttons d-none d-lg-flex">
                        @if(auth()->user()->role_id != 1)
                        <a href="{{route('dashboard')}}" class="auth-btn signin-btn">
                            <i class="fal fa-user-circle"></i> My Account
                        </a>
                        @endif
                        @if(auth()->user()->role_id == 2)
                        <a href="{{routeHelper('dashboard')}}" class="auth-btn signin-btn">
                            <i class="fal fa-tachometer-alt"></i> Dashboard
                        </a>
                        @endif
                    </div>

                    <!-- Logout for Authenticated Users -->
                    <a href="{{route('logout')}}" 
                       class="logout-btn"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fal fa-sign-out-alt"></i>
                        <span class="d-none d-sm-inline">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    @endauth
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Toggle mobile menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <!-- Mobile Search Bar -->
                <div class="mobile-search-bar">
                    <form action="{{route('product.search')}}" method="GET">
                        <div class="search-input-group">
                            <input 
                                type="search" 
                                name="keyword" 
                                class="search-input" 
                                placeholder="Search For Products, Brands And More..."
                                id="mobile-searchbox"
                            >
                            <button type="submit" class="search-btn" name="go">
                                <i class="fal fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobile-menu-overlay"></div>

    <!-- Mobile Menu Panel -->
    <div class="mobile-menu-panel" id="mobile-menu-panel">
        <div class="mobile-menu-header">
            <h3 style="margin: 0; color: #374151; font-size: 18px; font-weight: 600;">Menu</h3>
            <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Close menu">
                <i class="fal fa-times"></i>
            </button>
            <div style="clear: both;"></div>
        </div>
        
        <div class="mobile-menu-content">
            <!-- Account Section -->
            @guest
            <div class="mobile-menu-section">
                <a href="{{route('login')}}" class="mobile-menu-item">
                    <i class="fal fa-sign-in-alt"></i>
                    Sign In
                </a>
                <a href="{{route('register')}}" class="mobile-menu-item">
                    <i class="fal fa-user-plus"></i>
                    Sign Up
                </a>
            </div>
            @else
            <div class="mobile-menu-section">
                @if(auth()->user()->role_id != 1)
                <a href="{{route('dashboard')}}" class="mobile-menu-item">
                    <i class="fal fa-user-circle"></i>
                    My Account
                </a>
                @endif
                
                @if(auth()->user()->role_id == 2)
                <a href="{{routeHelper('dashboard')}}" class="mobile-menu-item">
                    <i class="fal fa-tachometer-alt"></i>
                    Dashboard
                </a>
                @endif
            </div>
            @endguest

            <!-- Shopping Section -->
            <div class="mobile-menu-section">
                <a href="{{route('wishlist')}}" class="mobile-menu-item">
                    <i class="fal fa-heart"></i>
                    Wishlist
                    @if(auth()->check())
                    <span class="badge">{{App\Models\wishlist::where('user_id',auth()->id())->count()}}</span>
                    @endif
                </a>
                <a href="{{route('cart')}}" class="mobile-menu-item">
                    <i class="fal fa-shopping-bag"></i>
                    Shopping Cart
                    <span class="badge">{{Cart::count()}}</span>
                </a>
            </div>

            <!-- Business Section -->
            @if(!auth()->user())
            <div class="mobile-menu-section">
                <a href="{{route('vendorJoin')}}" class="mobile-menu-item">
                    <i class="fal fa-store"></i>
                    Become a Seller
                </a>
            </div>
            @endif

            <!-- Logout Section -->
            @auth
            <div class="mobile-menu-section">
                <a href="{{route('logout')}}" 
                   class="mobile-menu-item"
                   style="color: #ef4444;"
                   onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                    <i class="fal fa-sign-out-alt"></i>
                    Logout
                </a>
                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
            @endauth
        </div>
    </div>

    <div class="menu-overly"></div>

    {{-- Main Menu --}}
    @if (!empty(setting('MAIN_MENU_STYLE')))
    @include('layouts.frontend.partials.partial-part.header_main_menu_' . setting('MAIN_MENU_STYLE'))
    @else
    @include('layouts.frontend.partials.partial-part.header_main_menu_1')
    @endif
</header>

{{-- Header Advance Search --}}
@include('layouts.frontend.partials.partial-part.header_advance_search')

@push('js')
<script>
$(document).ready(function() {
    // Header scroll effect for sticky navbar
    $(window).scroll(function() {
        let scrollTop = $(this).scrollTop();
        
        // Enhanced header scroll effect for sticky header
        if (scrollTop > 20) {
            $('header').addClass('scrolled');
        } else {
            $('header').removeClass('scrolled');
        }
    });

    // Sync search inputs
    $('#searchbox, #mobile-searchbox').on('input', function() {
        let value = $(this).val();
        $('#searchbox, #mobile-searchbox').not(this).val(value);
    });

    // Mobile menu functionality
    function toggleMobileMenu() {
        const menuToggle = $('#mobile-menu-toggle');
        const menuOverlay = $('#mobile-menu-overlay');
        const menuPanel = $('#mobile-menu-panel');
        
        menuToggle.toggleClass('active');
        menuOverlay.toggleClass('active');
        menuPanel.toggleClass('active');
        
        if (menuPanel.hasClass('active')) {
            $('body').css('overflow', 'hidden');
        } else {
            $('body').css('overflow', '');
        }
    }

    function closeMobileMenu() {
        $('#mobile-menu-toggle').removeClass('active');
        $('#mobile-menu-overlay').removeClass('active');
        $('#mobile-menu-panel').removeClass('active');
        $('body').css('overflow', '');
    }

    // Mobile menu events
    $('#mobile-menu-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleMobileMenu();
    });

    $('#mobile-menu-overlay, #mobile-menu-close').on('click', function(e) {
        e.preventDefault();
        closeMobileMenu();
    });

    // Close menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.mobile-menu-panel, .mobile-menu-toggle').length) {
            if ($('#mobile-menu-panel').hasClass('active')) {
                closeMobileMenu();
            }
        }
    });

    // Close menu on escape key
    $(document).keyup(function(e) {
        if (e.keyCode === 27) {
            closeMobileMenu();
        }
    });

    // Prevent menu panel clicks from closing menu
    $('#mobile-menu-panel').on('click', function(e) {
        e.stopPropagation();
    });

    // Search input enhancement
    $('.search-input').on('input', function() {
        const btn = $(this).closest('form').find('.search-btn');
        if ($(this).val().trim()) {
            btn.prop('disabled', false);
        } else {
            btn.prop('disabled', true);
        }
    });

    // Enhanced animations
    $('.action-item').hover(
        function() { 
            if ($(window).width() > 768) {
                $(this).addClass('fade-in-up');
            }
        },
        function() { 
            $(this).removeClass('fade-in-up');
        }
    );

    $('.search-input').on('focus', function() {
        $(this).closest('.search-input-group').addClass('slide-down');
    }).on('blur', function() {
        $(this).closest('.search-input-group').removeClass('slide-down');
    });

    // Mobile touch enhancements
    if ($(window).width() <= 768) {
        $('.action-item, .mobile-menu-toggle').on('touchstart', function() {
            $(this).css('transform', 'scale(0.95)');
        }).on('touchend', function() {
            $(this).css('transform', '');
        });

        $('.action-item').css('min-height', '44px');
    }

    // Responsive behavior
    $(window).resize(function() {
        if ($(window).width() > 768) {
            closeMobileMenu();
        }
    });

    // Badge animations
    $('.badge').each(function() {
        const badge = $(this);
        const count = parseInt(badge.text());
        if (count > 0) {
            badge.addClass('fade-in-up');
        }
    });
});

// Original functionality
$(window).on('load', function () {
    $('#myModal').modal('show');
});

var site_url = "{{ url('/') }}";
$.ajax({
    url: site_url + "/render/superCat",
    type: "get",
    datatype: "html",
    beforeSend: function () {
        $('.ajax-loading').show();
    },
    success: function (response) {
        var result = $.parseJSON(response);
        $('.ajax-loading').hide();
        $("#superCat").append(result);
        subCat();
    },
});

function subCat() {
    var site_url = "{{ url('/') }}";
    $.ajax({
        url: site_url + "/render/subCat",
        type: "get",
        datatype: "html",
        beforeSend: function () {
            $('.ajax-loading').show();
        },
        success: function (response) {
            var result = $.parseJSON(response);
            $('.ajax-loading').hide();
            $("#subCat").append(result);
        },
    });
}
</script>
@endpush