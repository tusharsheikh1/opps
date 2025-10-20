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
    /* border-bottom: 1px solid #e5e7eb; */ /* <-- REMOVED BORDER */
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
    /* border-bottom: 1px solid #f3f4f6; */ /* <-- REMOVED BORDER */
    margin: 0;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 32px;
    position: relative;
    
    /* ADDED FOR FLUID LAYOUT */
    width: 100%;
    padding-left: 16px;
    padding-right: 16px;
    box-sizing: border-box;
}

/* Logo */
.logo-area {
    flex-shrink: 0;
}

.logo-area img {
    max-height: 40px;
    width: auto;
}

/* --- Main Navigation Styles --- */
.main-nav {
    margin: 0;
    padding: 0;
    flex: 1 1 auto; /* <-- ADDED THIS to make nav fill space */
}
.main-nav ul {
    display: flex;
    align-items: center;
    justify-content: center; /* <-- ADDED THIS to center the links */
    gap: 28px; /* Spacing between nav items */
    list-style: none;
    margin: 0;
    padding: 0;
}
.main-nav ul li {
    padding: 0;
    margin: 0;
}
.main-nav ul a {
    font-family: 'Muli', sans-serif;
    font-size: 14px;
    font-weight: 700; /* Bolder like target image */
    color: #374151;
    text-transform: uppercase; /* Uppercase like target image */
    text-decoration: none;
    padding: 8px 0;
    transition: all 0.2s ease;
}
.main-nav ul a:hover {
    color: #f97316; /* Use your site's accent color */
}
/* Add this class to your "Summer Collection" link */
.main-nav ul a.highlight {
    color: #ef4444; /* Red color like target image */
}
/* --- END: Main Navigation Styles --- */


/* --- Dropdown Menu Styles --- */
.main-nav ul li.has-dropdown {
    position: relative;
}

.main-nav ul .dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    min-width: 220px;
    padding: 8px 0;
    z-index: 1001;
    
    /* Hide by default */
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: all 0.2s ease;
}

.main-nav ul li.has-dropdown:hover .dropdown-menu {
    /* Show on hover */
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.main-nav ul .dropdown-menu li {
    padding: 0;
    margin: 0;
    display: block; /* Make list items stack vertically */
}

.main-nav ul .dropdown-menu a {
    display: block;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 500; /* Lighter than main nav */
    color: #374151;
    text-transform: none; /* No uppercase */
    white-space: nowrap;
}

.main-nav ul .dropdown-menu a:hover {
    background: #f9fafb;
    color: #f97316; /* Your accent color */
}
/* --- END: Dropdown Menu Styles --- */


/* --- REMOVED: All .search-container, .search-box, .search-input-group, etc. styles --- */
/* (These are no longer in the desktop header) */


/* Header Actions */
.header-actions {
    display: flex;
    align-items: center;
    gap: 16px; /* Adjusted gap for icon-only layout */
    flex-shrink: 0;
}

.action-item {
    position: relative;
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #374151; /* Darker icon color */
    font-weight: 500;
    transition: all 0.2s ease;
    padding: 8px; /* Uniform padding for icons */
    border-radius: 6px;
    font-size: 14px;
}

.action-item:hover {
    color: #f97316;
    background: #f9fafb; /* Lighter hover */
    text-decoration: none;
}

.action-item i {
    font-size: 22px; /* Slightly larger icons */
    margin-right: 0; /* REMOVED margin, as text is hidden */
}

/* MOVED FROM MEDIA QUERY: This now applies to all screen sizes */
.action-item span:not(.badge) {
    display: none;
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

/* --- REMOVED: .auth-buttons, .auth-btn, .signin-btn, .signup-btn, .logout-btn styles --- */
/* (These are replaced by .action-item styles on desktop) */


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

/* Mobile Menu Panel & Overlay */
.mobile-menu-overlay {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);
    z-index: 9998; opacity: 0; visibility: hidden; transition: all 0.3s ease;
}
.mobile-menu-overlay.active { opacity: 1; visibility: visible; }

.mobile-menu-panel {
    position: fixed; top: 0; left: -100%; width: 280px; height: 100vh;
    background: #ffffff; z-index: 9999; transition: all 0.3s ease;
    box-shadow: 4px 0 20px rgba(0,0,0,0.1); overflow-y: auto; padding-top: 0;
}
.mobile-menu-panel.active { left: 0; }

.mobile-menu-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 20px; border-bottom: 1px solid #f3f4f6; background: #f9fafb;
}

.mobile-menu-close {
    background: none; border: none; font-size: 24px; color: #6b7280;
    cursor: pointer; padding: 0; line-height: 1;
}

.mobile-menu-content { padding: 0; }
.mobile-menu-section { border-bottom: 1px solid #f3f4f6; }
.mobile-menu-item {
    display: flex; align-items: center; padding: 16px 20px;
    color: #374151; text-decoration: none; font-weight: 500; transition: all 0.2s ease;
}
.mobile-menu-item:hover { background: #f9fafb; color: #f97316; text-decoration: none; }
.mobile-menu-item i { font-size: 18px; margin-right: 12px; width: 20px; text-align: center; }
.mobile-menu-item .badge { position: static; margin-left: auto; }

/* Mobile Search Bar Container (Unchanged) */
.mobile-search-bar-container {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: #fff;
    padding: 16px; /* Default padding */
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 999;
    transform: translateY(-10px);
    opacity: 0;
    visibility: hidden;
    transition: transform 0.3s ease, opacity 0.3s ease, visibility 0.3s ease;
    
    /* ADDED FOR FLUID LAYOUT */
    padding-left: 16px;
    padding-right: 16px;
    box-sizing: border-box;
}
/* Re-adding mobile search bar styles, as it's still used */
.mobile-search-bar-container .search-input-group {
    display: flex;
    background: #f9fafb;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.mobile-search-bar-container .search-input-group:focus-within {
    border-color: #f97316;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
    background: #ffffff;
}
.mobile-search-bar-container .search-input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 12px 16px;
    font-size: 14px;
    outline: none;
    color: #374151;
    font-weight: 400;
}
.mobile-search-bar-container .search-btn {
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


.mobile-search-bar-container.active {
    display: block;
    transform: translateY(0);
    opacity: 1;
    visibility: visible;
}

/* Responsive Design */
@media (max-width: 991px) {
    /* (d-lg-none) handles hiding the .auth-buttons, so this is not needed */
    /* .header-actions .d-none.d-lg-flex { ... } */
    
    /* --- REMOVED rules for span and i, as they are global now --- */

    /* --- REMOVED .search-container rule --- */

    .mobile-menu-toggle {
        display: flex;
        order: 1;
    }
    .logo-area {
        order: 2;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }
    .header-actions {
        order: 3;
        margin-left: auto; /* Push actions to the right */
        gap: 12px;
    }
    .action-item {
        padding: 10px;
    }
    
    /* --- REMOVED .logout-btn rules --- */

    /* ADDED: Center nav in mobile too */
    .main-nav {
        flex: 0 0 auto; /* Don't grow in mobile */
    }
}

@media (max-width: 767px) {
    .top-header {
        padding: 8px 0;
    }
    .header-content {
        gap: 8px;
        
        /* ADDED: Adjust padding for small screens */
        padding-left: 12px;
        padding-right: 12px;
    }
    .logo-area img {
        max-height: 36px;
    }
    .header-actions {
        gap: 8px;
    }

    /* ADDED: Adjust padding for small screens */
    .mobile-search-bar-container {
        padding-left: 12px;
        padding-right: 12px;
    }
}
</style>

<header class="not-home">
    <div class="top-header">
        <div class="header-content">
            <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Toggle mobile menu">
                <span></span><span></span><span></span>
            </button>

            <div class="logo-area">
                <a href="{{route('home')}}">
                    <img src="{{asset('uploads/setting/'.setting('logo'))}}" alt="Application Logo">
                </a>
            </div>

            <nav class="main-nav d-none d-lg-block">
                <ul>
                    <li><a href="#">Men</a></li>
                    <li><a href="#">Women</a></li>
                    <li><a href="#">Kids</a></li>
                    
                    <li class="has-dropdown">
                        <a href="#" class="highlight">Collections</a>
                        <ul class="dropdown-menu">
                            @foreach($collections as $collection)
                                <li><a href="{{ route('collection.product', $collection->slug) }}">{{ $collection->name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    
                    <li><a href="{{ route('product') }}">All Products</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="#" id="mobile-search-trigger" class="action-item d-lg-flex" title="Search">
                    <i class="fal fa-search"></i>
                </a>
                
                <a href="{{route('cart')}}" class="action-item" title="Shopping Cart">
                    <i class="fal fa-shopping-bag"></i>
                    <span>Cart</span> <span class="badge cart-count-badge">{{Cart::count()}}</span>
                </a>

                @guest
                <a href="{{route('login')}}" class="action-item d-none d-lg-flex" title="Sign In">
                    <i class="fal fa-user"></i>
                </a>
                <div class="auth-buttons d-none">
                    <a href="{{route('login')}}" class="auth-btn signin-btn"><i class="fal fa-sign-in-alt"></i> Sign In</a>
                    <a href="{{route('register')}}" class="auth-btn signup-btn"><i class="fal fa-user-plus"></i> Sign Up</a>
                </div>
                @else
                <a href="{{route('dashboard')}}" class="action-item d-none d-lg-flex" title="My Account">
                    <i class="fal fa-user-circle"></i>
                </a>
                <a href="{{route('logout')}}" class="action-item d-none d-lg-flex" title="Logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fal fa-sign-out-alt"></i>
                </a>

                <div class="auth-buttons d-none">
                    @if(auth()->user()->role_id != 1)
                    <a href="{{route('dashboard')}}" class="auth-btn signin-btn"><i class="fal fa-user-circle"></i> My Account</a>
                    @endif
                    @if(auth()->user()->role_id == 2)
                    <a href="{{routeHelper('dashboard')}}" class="auth-btn signin-btn"><i class="fal fa-tachometer-alt"></i> Dashboard</a>
                    @endif
                </div>
                <a href="{{route('logout')}}" class="logout-btn d-none" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fal fa-sign-out-alt"></i><span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                @endauth
            </div>
        </div>
        </div>

    <div class="mobile-search-bar-container" id="mobile-search-container">
        <form action="{{route('product.search')}}" method="GET">
            <div class="search-input-group">
                <input type="search" name="keyword" class="search-input" placeholder="Search For Products..." id="mobile-searchbox">
                <button type="submit" class="search-btn" name="go"><i class="fal fa-search"></i></button>
            </div>
        </form>
    </div>

    <div class="mobile-menu-overlay" id="mobile-menu-overlay"></div>

    <div class="mobile-menu-panel" id="mobile-menu-panel">
        <div class="mobile-menu-header">
            <h3 style="margin: 0; color: #374151; font-size: 18px; font-weight: 600;">Menu</h3>
            <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Close menu"><i class="fal fa-times"></i></button>
        </div>
        <div class="mobile-menu-content">
            @guest
            <div class="mobile-menu-section">
                <a href="{{route('login')}}" class="mobile-menu-item"><i class="fal fa-sign-in-alt"></i>Sign In</a>
                <a href="{{route('register')}}" class="mobile-menu-item"><i class="fal fa-user-plus"></i>Sign Up</a>
            </div>
            @else
            <div class="mobile-menu-section">
                @if(auth()->user()->role_id != 1)
                <a href="{{route('dashboard')}}" class="mobile-menu-item"><i class="fal fa-user-circle"></i>My Account</a>
                @endif
                @if(auth()->user()->role_id == 2)
                <a href="{{routeHelper('dashboard')}}" class="mobile-menu-item"><i class="fal fa-tachometer-alt"></i>Dashboard</a>
                @endif
            </div>
            @endguest
            <div class="mobile-menu-section">
                <a href="{{route('cart')}}" class="mobile-menu-item">
                    <i class="fal fa-shopping-bag"></i>Shopping Cart
                    <span class="badge cart-count-badge">{{Cart::count()}}</span>
                </a>
            </div>
            @auth
            <div class="mobile-menu-section">
                <a href="{{route('logout')}}" class="mobile-menu-item" style="color: #ef4444;" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                    <i class="fal fa-sign-out-alt"></i>Logout
                </a>
                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
            @endauth
        </div>
    </div>

    {{-- Main Menu --}}
    </header>

{{-- Header Advance Search --}}
@include('layouts.frontend.partials.partial-part.header_advance_search')

@push('js')
<script>
$(document).ready(function() {
    const $header = $('header');
    const $mobileMenuToggle = $('#mobile-menu-toggle');
    const $mobileMenuOverlay = $('#mobile-menu-overlay');
    const $mobileMenuPanel = $('#mobile-menu-panel');
    const $mobileSearchTrigger = $('#mobile-search-trigger');
    const $mobileSearchContainer = $('#mobile-search-container');
    const $mobileSearchInput = $('#mobile-searchbox');

    // Header scroll effect
    $(window).scroll(function() {
        $header.toggleClass('scrolled', $(this).scrollTop() > 20);
    });

    // --- Mobile Menu Functions ---
    function closeMobileMenu() {
        $mobileMenuToggle.removeClass('active');
        $mobileMenuOverlay.removeClass('active');
        $mobileMenuPanel.removeClass('active');
        $('body').css('overflow', '');
    }

    function openMobileMenu() {
        closeMobileSearch(); // Close search if open
        $mobileMenuToggle.addClass('active');
        $mobileMenuOverlay.addClass('active');
        $mobileMenuPanel.addClass('active');
        $('body').css('overflow', 'hidden');
    }

    // --- Mobile Search Functions ---
    function closeMobileSearch() {
        $mobileSearchContainer.removeClass('active');
    }

    function openMobileSearch() {
        closeMobileMenu(); // Close menu if open
        $mobileSearchContainer.addClass('active');
        setTimeout(() => $mobileSearchInput.focus(), 300); // Focus after transition
    }

    // --- Event Handlers ---
    $mobileMenuToggle.on('click', function(e) {
        e.stopPropagation();
        if ($mobileMenuPanel.hasClass('active')) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    });

    $mobileSearchTrigger.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if ($mobileSearchContainer.hasClass('active')) {
            closeMobileSearch();
        } else {
            openMobileSearch();
        }
    });

    // Close menus when clicking overlay or close button
    $('#mobile-menu-overlay, #mobile-menu-close').on('click', closeMobileMenu);

    // Close menus when clicking outside
    $(document).on('click', function(e) {
        // Close menu if click is outside panel and toggle
        if ($mobileMenuPanel.hasClass('active') && !$(e.target).closest('.mobile-menu-panel, .mobile-menu-toggle').length) {
            closeMobileMenu();
        }
        // Close search if click is outside search container and trigger
        // MODIFIED: Also check if click is on the search container *itself*
        if ($mobileSearchContainer.hasClass('active') && !$(e.target).closest('.mobile-search-bar-container, #mobile-search-trigger').length) {
            closeMobileSearch();
        }
    });

    // Close menus on escape key
    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            closeMobileMenu();
            closeMobileSearch();
        }
    });

    // Sync search inputs (Only mobile searchbox exists now)
    $('#mobile-searchbox').on('input', function() {
        // let value = $(this).val();
        // No other box to sync with
    });

    // --- 3. ADDED THIS GLOBAL AJAX LISTENER ---
    // This listens for any successful AJAX call that returns a JSON response
    // (like from CartController.php)
    $(document).ajaxSuccess(function(event, xhr, settings) {
        // Check if the response has a 'count' property
        if (xhr.responseJSON && typeof xhr.responseJSON.count !== 'undefined') {
            let cartCount = xhr.responseJSON.count;
            // Update all elements with the 'cart-count-badge' class
            $('.cart-count-badge').text(cartCount);
        }
    });

});

// Original functionality
$(window).on('load', function () {
    $('#myModal').modal('show');
});

var site_url = "{{ url('/') }}";
$.ajax({
    url: site_url + "/render/superCat", type: "get", datatype: "html",
    beforeSend: function () { $('.ajax-loading').show(); },
    success: function (response) {
        var result = $.parseJSON(response);
        $('.ajax-loading').hide();
        $("#superCat").append(result);
        subCat();
    },
});

function subCat() {
    $.ajax({
        url: site_url + "/render/subCat", type: "get", datatype: "html",
        beforeSend: function () { $('.ajax-loading').show(); },
        success: function (response) {
            var result = $.parseJSON(response);
            $('.ajax-loading').hide();
            $("#subCat").append(result);
        },
    });
}
</script>
@endpush