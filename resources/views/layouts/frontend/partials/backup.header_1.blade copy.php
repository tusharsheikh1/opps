<style>
:root{
    --brand-accent: #f97316;
    --brand-danger: #ef4444;
    --muted: #6b7280;
    --bg: #ffffff;
    --panel-bg: #fff;
    --shadow: 0 12px 30px rgba(2,6,23,0.08);
    --radius: 12px;
    --max-width: 1200px;
    --transition-fast: 0.18s;
}

/* Font */
@font-face{
    font-family: 'Muli';
    src: url('{{asset("/")}}assets/frontend/font/Muli/Muli-VariableFont_wght.ttf');
    font-display: swap;
}

/* Reset / base for header */
header {
    font-family: 'Muli', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    position: sticky;
    top: 0;
    z-index: 1000;
    background: var(--bg);
    transition: all 0.28s ease;
    margin: 0;
    padding: 0;
    border-bottom: 1px solid rgba(15,23,42,0.03);
}

/* Elevation when scrolled */
header.scrolled {
    box-shadow: 0 6px 28px rgba(2,6,23,0.08);
    backdrop-filter: blur(8px);
    background: rgba(255,255,255,0.96);
}

/* Top header wrapper */
.top-header {
    background: transparent;
    padding: 14px 0;
}

/* Flex container */
.header-content {
    display: flex;
    align-items: center;
    gap: 28px;
    position: relative;
    width: 100%;
    padding-left: 20px;
    padding-right: 20px;
    box-sizing: border-box;
    margin: 0 auto;
    max-width: var(--max-width);
}

/* Logo */
.logo-area {
    flex-shrink: 0;
}
.logo-area img {
    max-height: 44px;
    width: auto;
    display: block;
}

/* Main Nav */
.main-nav {
    margin: 0;
    padding: 0;
    flex: 1 1 auto;
}
.main-nav ul {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 26px;
    list-style: none;
    margin: 0;
    padding: 0;
}
.main-nav ul li {
    padding: 0;
    margin: 0;
    position: relative;
}
.main-nav ul a {
    font-size: 13px;
    font-weight: 700;
    color: #111827;
    text-transform: uppercase;
    text-decoration: none;
    padding: 8px 6px;
    letter-spacing: 0.6px;
    transition: color var(--transition-fast), transform var(--transition-fast);
    display: inline-block;
}
.main-nav ul a:hover,
.main-nav ul a:focus {
    color: var(--brand-accent);
    transform: translateY(-1px);
}
.main-nav ul a.highlight {
    color: var(--brand-danger);
}

/* Modern Mega Panel - full width visually but centered content */
.mega-panel {
    position: absolute;
    left: 50%;
    transform: translateX(-50%) translateY(12px);
    top: 100%;
    width: min(1100px, calc(100vw - 40px));
    background: var(--panel-bg);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 18px;
    display: grid;
    grid-template-columns: 1fr 320px; /* main grid + promo column */
    gap: 18px;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity var(--transition-fast) ease, transform var(--transition-fast) ease, visibility var(--transition-fast);
    z-index: 1100;
}

/* Inner content grid for subcategories */
.mega-panel .mega-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
}

/* Subcategory card column */
.mega-col {
    background: transparent;
    padding: 8px 10px;
    border-radius: 8px;
}
.mega-col .col-title {
    font-weight: 800;
    font-size: 15px;
    color: #0f172a;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.mega-col .col-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.mega-col .col-list a {
    color: #374151;
    font-weight: 500;
    font-size: 14px;
    text-decoration: none;
    padding: 6px 6px;
    border-radius: 6px;
    transition: background var(--transition-fast), color var(--transition-fast), transform var(--transition-fast);
}
.mega-col .col-list a:hover,
.mega-col .col-list a:focus {
    color: var(--brand-accent);
    background: rgba(249,115,22,0.06);
    transform: translateX(4px);
}

/* Promo column on right, supports image & CTA */
.mega-promo {
    border-radius: 10px;
    overflow: hidden;
    background: linear-gradient(180deg, rgba(249,115,22,0.06), rgba(239,68,68,0.02));
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 140px;
    padding: 12px;
}
.mega-promo .promo-media {
    height: 90px;
    background-position: center;
    background-size: cover;
    border-radius: 8px;
}
.mega-promo .promo-body {
    padding-top: 10px;
}
.mega-promo .promo-title {
    font-weight: 800;
    color: #111827;
    font-size: 15px;
}
.mega-promo .promo-desc {
    font-size: 13px;
    color: var(--muted);
    margin-top: 6px;
}
.mega-promo .promo-cta {
    margin-top: 10px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--brand-accent);
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 700;
    font-size: 13px;
}

/* Visible state */
.main-nav ul > li.has-dropdown:hover > .mega-panel,
.main-nav ul > li.has-dropdown:focus-within > .mega-panel,
.main-nav ul > li.has-dropdown.open > .mega-panel {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(6px);
    pointer-events: auto;
}

/* Small dropdown fallback for simple lists inside mega */
.mega-panel .simple-list {
    display: block;
}
.mega-panel .simple-list a {
    display: block;
    padding: 8px 6px;
    color: #374151;
    text-decoration: none;
    border-radius: 6px;
}
.mega-panel .simple-list a:hover {
    color: var(--brand-accent);
    background: rgba(249,115,22,0.04);
}

/* Collections dropdown (keeps same but inside panel) */
.main-nav ul > li.collections .mega-panel {
    grid-template-columns: 1fr;
}

/* Header actions */
.header-actions {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-shrink: 0;
}
.action-item {
    position: relative;
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #374151;
    font-weight: 500;
    transition: all 0.18s ease;
    padding: 8px;
    border-radius: 8px;
    font-size: 14px;
    background: transparent;
}
.action-item:hover { color: var(--brand-accent); background: rgba(249,115,22,0.04); }
.action-item i { font-size: 18px; margin-right: 8px; }
.action-item span:not(.badge) { display: none; }

/* Badge */
.badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: var(--brand-danger);
    color: white;
    font-size: 10px;
    font-weight: 700;
    padding: 4px 6px;
    border-radius: 999px;
    min-width: 18px;
    text-align: center;
    line-height: 1;
    box-shadow: 0 4px 10px rgba(239,68,68,0.12);
}

/* Mobile toggles */
.mobile-menu-toggle {
    display: none;
    flex-direction: column;
    cursor: pointer;
    padding: 8px;
    border-radius: 8px;
    transition: all 0.18s ease;
    background: transparent;
    border: none;
    z-index: 1001;
}
.mobile-menu-toggle:hover { background: rgba(15,23,42,0.03); }
.mobile-menu-toggle span {
    width: 20px;
    height: 2px;
    background: #374151;
    margin: 3px 0;
    transition: all 0.25s ease;
    border-radius: 2px;
}

/* Mobile overlay/panel - kept from original but cleaned */
.mobile-menu-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    z-index: 9998;
    opacity: 0;
    visibility: hidden;
    transition: all 0.28s ease;
}
.mobile-menu-overlay.active { opacity: 1; visibility: visible; }
.mobile-menu-panel {
    position: fixed;
    top: 0;
    left: -100%;
    width: 320px;
    height: 100vh;
    background: var(--panel-bg);
    z-index: 9999;
    transition: all 0.28s ease;
    box-shadow: 4px 0 30px rgba(2,6,23,0.08);
    overflow-y: auto;
    padding-top: 0;
}
.mobile-menu-panel.active { left: 0; }

/* Mobile small tweaks */
.mobile-menu-header { display:flex; justify-content:space-between; align-items:center; padding:18px; border-bottom: 1px solid #f3f4f6; background: #fff; }
.mobile-menu-close { background: none; border: none; font-size: 22px; color: var(--muted); cursor: pointer; padding: 0; }

/* Mobile search container */
.mobile-search-bar-container {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: #fff;
    padding: 14px;
    box-shadow: 0 8px 24px rgba(2,6,23,0.06);
    z-index: 999;
    transform: translateY(-12px);
    opacity: 0;
    visibility: hidden;
    transition: transform 0.22s ease, opacity 0.22s ease, visibility 0.22s ease;
    padding-left: 18px;
    padding-right: 18px;
    box-sizing: border-box;
}
.mobile-search-bar-container.active {
    display: block;
    transform: translateY(0);
    opacity: 1;
    visibility: visible;
}
.search-input-group {
    display:flex;
    background:#f9fafb;
    border: 1px solid #e6e9ee;
    border-radius: 10px;
    overflow: hidden;
}
.search-input { flex:1; border:none; padding:12px 14px; font-size:14px; outline:none; background:transparent; color:#111827; }
.search-btn { background:var(--brand-accent); border:none; padding:10px 14px; color:#fff; cursor:pointer; }

/* Responsive */
@media (max-width: 991px) {
    .mobile-menu-toggle { display:flex; order:1; }
    .logo-area { order:2; position: absolute; left: 50%; transform: translateX(-50%); }
    .header-actions { order:3; margin-left:auto; gap:10px; }
    .main-nav { flex: 0 0 auto; }
    .mega-panel { display: none !important; } /* hide desktop mega on tablet/mobile */
    .main-nav.d-none.d-lg-block { display: none !important; }
}

@media (max-width: 767px) {
    .top-header { padding: 8px 0; }
    .header-content { gap: 8px; padding-left: 12px; padding-right: 12px; }
    .logo-area img { max-height: 36px; }
    .header-actions { gap: 8px; }
}
</style>

<header class="not-home" role="banner">
    <div class="top-header">
        <div class="header-content">
            <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Toggle mobile menu" aria-expanded="false" aria-controls="mobile-menu-panel">
                <span></span><span></span><span></span>
            </button>

            <div class="logo-area">
                <a href="{{route('home')}}" aria-label="Home">
                    <img src="{{asset('uploads/setting/'.setting('logo'))}}" alt="Application Logo">
                </a>
            </div>

            <nav class="main-nav d-none d-lg-block" aria-label="Main navigation">
                <ul>
                    @foreach($categories as $category)
                        @if($category->sub_categories && $category->sub_categories->count() > 0)
                            <li class="has-dropdown" tabindex="0" aria-haspopup="true" aria-expanded="false">
                                <a href="{{ route('category.product', $category->slug) }}">{{ $category->name }}</a>

                                <!-- Modern mega-panel -->
                                <div class="mega-panel" role="region" aria-label="{{ $category->name }} menu">
                                    <div class="mega-grid" role="menu">
                                        @foreach($category->sub_categories->where('status', true) as $subCategory)
                                            <div class="mega-col" role="presentation">
                                                <a href="{{ route('subCategory.product', $subCategory->slug) }}" class="col-title" role="menuitem" tabindex="-1">
                                                    {{-- optional icon placeholder --}}
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 12h18" stroke="#e5e7eb" stroke-width="2" stroke-linecap="round"/><path d="M3 6h18" stroke="#e5e7eb" stroke-width="2" stroke-linecap="round"/></svg>
                                                    <span>{{ $subCategory->name }}</span>
                                                </a>

                                                @if($subCategory->miniCategory && $subCategory->miniCategory->count() > 0)
                                                    <div class="col-list" role="group" aria-label="{{ $subCategory->name }} mini categories">
                                                        @foreach($subCategory->miniCategory->where('status', true) as $miniCat)
                                                            <a role="menuitem" tabindex="-1" href="{{ route('miniCategory.product', $miniCat->slug) }}">{{ $miniCat->name }}</a>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="col-list">
                                                        <a role="menuitem" tabindex="-1" href="{{ route('subCategory.product', $subCategory->slug) }}">Browse all {{ $subCategory->name }}</a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Promo area (right column) -->
                                    <div class="mega-promo" aria-hidden="false">
                                        <div class="promo-media" style="
                                            @if(isset($category->image) && $category->image)
                                                background-image: url('{{ asset('uploads/category/'.$category->image) }}');
                                            @else
                                                background-image: linear-gradient(135deg, rgba(249,115,22,0.16), rgba(239,68,68,0.06));
                                            @endif
                                        "></div>

                                        <div class="promo-body">
                                            <div class="promo-title">Explore {{ $category->name }}</div>
                                            <div class="promo-desc">Handpicked collections, trending picks and latest arrivals from this category.</div>
                                            <a href="{{ route('category.product', $category->slug) }}" class="promo-cta">Shop {{ $category->name }}</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @else
                            <li><a href="{{ route('category.product', $category->slug) }}">{{ $category->name }}</a></li>
                        @endif
                    @endforeach

                    <li class="has-dropdown collections" tabindex="0" aria-haspopup="true" aria-expanded="false">
                        <a href="#" class="highlight">Collections</a>
                        <div class="mega-panel" role="region" aria-label="Collections">
                            <div class="simple-list" role="menu">
                                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px,1fr)); gap:10px;">
                                    @foreach($collections as $collection)
                                        <a role="menuitem" tabindex="-1" href="{{ route('collection.product', $collection->slug) }}">{{ $collection->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </li>

                    <li><a href="{{ route('product') }}">All Products</a></li>
                </ul>
            </nav>

            <div class="header-actions">
                <a href="#" id="mobile-search-trigger" class="action-item d-lg-flex" title="Search" aria-controls="mobile-search-container" aria-expanded="false">
                    <i class="fal fa-search" aria-hidden="true"></i>
                </a>

                <a href="{{route('cart')}}" class="action-item" title="Shopping Cart" aria-label="Shopping cart">
                    <i class="fal fa-shopping-bag" aria-hidden="true"></i>
                    <span>Cart</span>
                    <span class="badge cart-count-badge" aria-live="polite">{{Cart::count()}}</span>
                </a>

                @guest
                <a href="{{route('login')}}" class="action-item d-none d-lg-flex" title="Sign In" aria-label="Sign in">
                    <i class="fal fa-user" aria-hidden="true"></i>
                </a>
                @else
                <a href="{{route('dashboard')}}" class="action-item d-none d-lg-flex" title="My Account" aria-label="My account">
                    <i class="fal fa-user-circle" aria-hidden="true"></i>
                </a>
                <a href="{{route('logout')}}" class="action-item d-none d-lg-flex" title="Logout" aria-label="Logout"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fal fa-sign-out-alt" aria-hidden="true"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                @endguest
            </div>
        </div>
    </div>

    <div class="mobile-search-bar-container" id="mobile-search-container" aria-hidden="true">
        <form action="{{route('product.search')}}" method="GET">
            <div class="search-input-group">
                <input type="search" name="keyword" class="search-input" placeholder="Search For Products..." id="mobile-searchbox" aria-label="Search products">
                <button type="submit" class="search-btn" name="go" aria-label="Search"><i class="fal fa-search" aria-hidden="true"></i></button>
            </div>
        </form>
    </div>

    <div class="mobile-menu-overlay" id="mobile-menu-overlay" tabindex="-1" aria-hidden="true"></div>

    <div class="mobile-menu-panel" id="mobile-menu-panel" aria-hidden="true">
        <div class="mobile-menu-header">
            <h3 style="margin: 0; color: #111827; font-size: 18px; font-weight: 700;">Menu</h3>
            <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Close menu">
                <i class="fal fa-times" aria-hidden="true"></i>
            </button>
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
                @foreach($categories as $category)
                    <a href="{{ route('category.product', $category->slug) }}" class="mobile-menu-item">
                        <i class="fal fa-folder"></i>{{ $category->name }}
                    </a>
                @endforeach
                <a href="{{ route('product') }}" class="mobile-menu-item">
                    <i class="fal fa-box"></i>All Products
                </a>
            </div>

            <div class="mobile-menu-section">
                <a href="{{route('cart')}}" class="mobile-menu-item">
                    <i class="fal fa-shopping-bag"></i>Shopping Cart
                    <span class="badge cart-count-badge">{{Cart::count()}}</span>
                </a>
            </div>

            @auth
            <div class="mobile-menu-section">
                <a href="{{route('logout')}}" class="mobile-menu-item" style="color: var(--brand-danger);"
                   onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                    <i class="fal fa-sign-out-alt"></i>Logout
                </a>
                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
            @endauth
        </div>
    </div>
</header>

@include('layouts.frontend.partials.partial-part.header_advance_search')

@push('js')
<script>
$(document).ready(function() {
    const $header = $('header');
    const $mobileMenuToggle = $('#mobile-menu-toggle');
    const $mobileMenuOverlay = $('#mobile-menu-overlay');
    const $mobileMenuPanel = $('#mobile-menu-panel');
    const $mobileMenuClose = $('#mobile-menu-close');
    const $mobileSearchTrigger = $('#mobile-search-trigger');
    const $mobileSearchContainer = $('#mobile-search-container');
    const $mobileSearchInput = $('#mobile-searchbox');
    const $navItems = $('.main-nav > ul > li.has-dropdown');

    // header shadow on scroll
    $(window).on('scroll', function() {
        $header.toggleClass('scrolled', $(this).scrollTop() > 20);
    });

    // mobile menu open/close
    function openMobileMenu() {
        $mobileMenuToggle.attr('aria-expanded','true');
        $mobileMenuOverlay.addClass('active').attr('aria-hidden','false');
        $mobileMenuPanel.addClass('active').attr('aria-hidden','false');
        $('body').css('overflow','hidden');
    }
    function closeMobileMenu() {
        $mobileMenuToggle.attr('aria-expanded','false');
        $mobileMenuOverlay.removeClass('active').attr('aria-hidden','true');
        $mobileMenuPanel.removeClass('active').attr('aria-hidden','true');
        $('body').css('overflow','');
    }

    $mobileMenuToggle.on('click', function(e){
        e.stopPropagation();
        if ($mobileMenuPanel.hasClass('active')) {
            closeMobileMenu();
        } else {
            closeMobileSearch();
            openMobileMenu();
        }
    });
    $mobileMenuClose.on('click', closeMobileMenu);
    $mobileMenuOverlay.on('click', closeMobileMenu);

    // mobile search
    function openMobileSearch() {
        closeMobileMenu();
        $mobileSearchContainer.addClass('active').attr('aria-hidden','false');
        $mobileSearchTrigger.attr('aria-expanded','true');
        setTimeout(()=> $mobileSearchInput.focus(), 250);
    }
    function closeMobileSearch() {
        $mobileSearchContainer.removeClass('active').attr('aria-hidden','true');
        $mobileSearchTrigger.attr('aria-expanded','false');
    }
    $mobileSearchTrigger.on('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        if ($mobileSearchContainer.hasClass('active')) closeMobileSearch(); else openMobileSearch();
    });

    // clicking outside to close
    $(document).on('click', function(e) {
        if ($mobileMenuPanel.hasClass('active') && !$(e.target).closest('.mobile-menu-panel, .mobile-menu-toggle').length) {
            closeMobileMenu();
        }
        if ($mobileSearchContainer.hasClass('active') && !$(e.target).closest('.mobile-search-bar-container, #mobile-search-trigger').length) {
            closeMobileSearch();
        }
    });

    // escape key closes overlays
    $(document).on('keyup', function(e) {
        if (e.key === "Escape") {
            closeMobileMenu();
            closeMobileSearch();
            // close any open mega on desktop
            $navItems.removeClass('open').attr('aria-expanded','false');
            $('.mega-panel').css({'opacity':'0','visibility':'hidden'});
        }
    });

    // AJAX cart count update listener (original behavior)
    $(document).ajaxSuccess(function(event, xhr, settings) {
        if (xhr.responseJSON && typeof xhr.responseJSON.count !== 'undefined') {
            let cartCount = xhr.responseJSON.count;
            $('.cart-count-badge').text(cartCount);
        }
    });

    // Desktop mega: accessible hover / focus + small delay to avoid flicker
    let openTimer = null, closeTimer = null;
    function openMega($item){
        clearTimeout(closeTimer);
        $navItems.not($item).removeClass('open').attr('aria-expanded','false');
        $item.addClass('open').attr('aria-expanded','true');
        const $panel = $item.find('.mega-panel');
        $panel.css({'opacity':'1','visibility':'visible','pointer-events':'auto','transform':'translateX(-50%) translateY(6px)'});
    }
    function closeMega($item){
        $item.removeClass('open').attr('aria-expanded','false');
        const $panel = $item.find('.mega-panel');
        $panel.css({'opacity':'0','visibility':'hidden','pointer-events':'none','transform':'translateX(-50%) translateY(12px)'});
    }

    $navItems.on('mouseenter', function(){
        const $this = $(this);
        clearTimeout(openTimer);
        openTimer = setTimeout(()=> openMega($this), 120);
    }).on('mouseleave', function(){
        const $this = $(this);
        clearTimeout(openTimer);
        clearTimeout(closeTimer);
        closeTimer = setTimeout(()=> closeMega($this), 180);
    });

    // keyboard accessibility for nav items
    $navItems.on('focusin', function(){
        openMega($(this));
    }).on('focusout', function(e){
        const $this = $(this);
        // if moving focus to inside panel, don't close
        setTimeout(()=> {
            if (!$this.find(':focus').length) closeMega($this);
        }, 80);
    });

    // Toggle via Enter/Space for keyboard users
    $navItems.on('keydown', function(e){
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            const $this = $(this);
            if ($this.hasClass('open')) closeMega($this); else openMega($this);
        }
        // support arrow keys to move between top-level items
        if (e.key === 'ArrowRight') {
            $(this).next('li').find('> a').focus();
        } else if (e.key === 'ArrowLeft') {
            $(this).prev('li').find('> a').focus();
        }
    });

    // keep header modal behavior on window load (original)
    $(window).on('load', function () {
        $('#myModal').modal && $('#myModal').modal('show');
    });

    // render super categories (original AJAX)
    var site_url = "{{ url('/') }}";
    $.ajax({
        url: site_url + "/render/superCat",
        type: "get",
        datatype: "html",
        beforeSend: function () {
            $('.ajax-loading').show();
        },
        success: function (response) {
            try {
                var result = $.parseJSON(response);
                $('.ajax-loading').hide();
                $("#superCat").append(result);
                subCat();
            } catch(err) {
                $('.ajax-loading').hide();
                console.error('Failed to parse superCat response', err);
            }
        },
        error: function() { $('.ajax-loading').hide(); }
    });

    function subCat() {
        $.ajax({
            url: site_url + "/render/subCat",
            type: "get",
            datatype: "html",
            beforeSend: function () {
                $('.ajax-loading').show();
            },
            success: function (response) {
                try {
                    var result = $.parseJSON(response);
                    $('.ajax-loading').hide();
                    $("#subCat").append(result);
                } catch(err) {
                    $('.ajax-loading').hide();
                    console.error('Failed to parse subCat response', err);
                }
            },
            error: function() { $('.ajax-loading').hide(); }
        });
    }
});
</script>
@endpush