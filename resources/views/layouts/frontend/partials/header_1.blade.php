<header class="not-home" role="banner">
    {{-- INJECT CAMPAIGN DATA: Quick fix for Undefined variable $campaigns error --}}
    @inject('campaignModel', 'App\Models\Campaign')
    @php
        $campaigns = $campaignModel::where('status', 1)->get(); // Assuming 'status' is used to filter active campaigns
        
        // SET CORRECT CART INSTANCE FOR LOGGED-IN USERS
        if (auth()->check()) {
            Cart::instance('cart_' . auth()->id());
        } else {
            Cart::instance('default');
        }
        
        // Get cart count using the correct instance
        $cartCount = Cart::count();
    @endphp

    {{-- Include the dedicated CSS partial --}}
    @include('layouts.frontend.partials.partial-part.header_1_style')

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
                            <li class="has-dropdown" data-dropdown="category-{{$category->id}}" tabindex="0" aria-haspopup="true" aria-expanded="false">
                                <a href="{{ route('category.product', $category->slug) }}">{{ $category->name }}</a>

                                <div class="mega-panel" role="region" aria-label="{{ $category->name }} menu">
                                    <div class="mega-grid" role="menu">
                                        @foreach($category->sub_categories->where('status', true) as $subCategory)
                                            <div class="mega-col" role="presentation">
                                                <a href="{{ route('subCategory.product', $subCategory->slug) }}" class="col-title" role="menuitem" tabindex="-1">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    <span>{{ $subCategory->name }}</span>
                                                </a>

                                                @if($subCategory->miniCategory && $subCategory->miniCategory->count() > 0)
                                                    <div class="col-list" role="group" aria-label="{{ $subCategory->name }} mini categories">
                                                        @foreach($subCategory->miniCategory->where('status', true)->take(8) as $miniCat)
                                                            <a role="menuitem" tabindex="-1" href="{{ route('miniCategory.product', $miniCat->slug) }}">{{ $miniCat->name }}</a>
                                                        @endforeach
                                                        @if($subCategory->miniCategory->where('status', true)->count() > 8)
                                                            <a role="menuitem" tabindex="-1" href="{{ route('subCategory.product', $subCategory->slug) }}" style="color: var(--brand-accent); font-weight: 700;">View All →</a>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="col-list">
                                                        <a role="menuitem" tabindex="-1" href="{{ route('subCategory.product', $subCategory->slug) }}">Browse all</a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mega-promo" aria-hidden="false">
                                        <div class="promo-media" style="
                                            @if(isset($category->cover_photo) && $category->cover_photo && $category->cover_photo !== 'default.png')
                                                background-image: url('{{ asset('uploads/category/'.$category->cover_photo) }}');
                                            @else
                                                background-image: linear-gradient(135deg, rgba(249,115,22,0.16), rgba(239,68,68,0.06));
                                            @endif
                                        "></div>

                                        <div class="promo-body">
                                            <div class="promo-title">{{ $category->name }}</div>
                                            <div class="promo-desc">Discover trending products and latest arrivals</div>
                                            <a href="{{ route('category.product', $category->slug) }}" class="promo-cta">
                                                Shop Now
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                                    <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @else
                            <li><a href="{{ route('category.product', $category->slug) }}">{{ $category->name }}</a></li>
                        @endif
                    @endforeach


                    {{-- START: Collections Dropdown Menu - Updated to use the new route('collection.list') --}}
                    <li class="has-dropdown collections" data-dropdown="collections" tabindex="0" aria-haspopup="true" aria-expanded="false">
                        <a href="{{ route('collection.list') }}">Collections</a>
                        <div class="mega-panel" role="region" aria-label="Collections">
                            <div class="simple-list" role="menu">
                                @foreach($collections as $collection)
                                    <a role="menuitem" tabindex="-1" href="{{ route('collection.product', $collection->slug) }}">{{ $collection->name }}</a>
                                @endforeach
                                <a role="menuitem" tabindex="-1" href="{{ route('collection.list') }}" style="color: var(--brand-accent); font-weight: 700;">View All Collections →</a>
                            </div>
                        </div>
                    </li>
                    {{-- END: Collections Dropdown Menu --}}

                    {{-- START: Campaigns Dropdown Menu - Now uses the injected $campaigns variable --}}
                    <li class="has-dropdown campaigns" data-dropdown="campaigns" tabindex="0" aria-haspopup="true" aria-expanded="false">
                        <a href="{{ route('campaing.index') }}" class="highlight">Campaigns</a>
                        <div class="mega-panel" role="region" aria-label="Campaigns">
                            <div class="simple-list" role="menu">
                                @foreach($campaigns as $campaign)
                                    <a role="menuitem" tabindex="-1" href="{{ route('campaing.product', $campaign->slug) }}">{{ $campaign->name }}</a>
                                @endforeach
                                <a role="menuitem" tabindex="-1" href="{{ route('campaing.index') }}" style="color: var(--brand-accent); font-weight: 700;">View All Campaigns →</a>
                            </div>
                        </div>
                    </li>
                    {{-- END: Campaigns Dropdown Menu --}}


                    <li><a href="{{ route('product') }}">All Products</a></li>
                </ul>
            </nav>

            <div class="header-actions">
                <a href="#" id="mobile-search-trigger" class="action-item d-lg-flex" title="Search" aria-controls="mobile-search-container" aria-expanded="false">
                    <i class="fal fa-search" aria-hidden="true"></i>
                </a>

                {{-- CART COUNT ELEMENT: Fixed to use correct cart instance for logged-in users --}}
                <a href="{{route('cart')}}" class="action-item" title="Shopping Cart" aria-label="Shopping cart">
                    <i class="fal fa-shopping-bag" aria-hidden="true"></i>
                    <span>Cart</span>
                    <span class="badge cart-count-badge" aria-live="polite">{{ $cartCount }}</span>
                </a>
                {{-- END CART COUNT ELEMENT --}}

                @guest
                <a href="{{route('login')}}" class="action-item d-none d-lg-flex" title="Sign In" aria-label="Sign in">
                    <i class="fal fa-user" aria-hidden="true"></i>
                </a>
                @else
                <a href="{{route('dashboard')}}" class="action-item d-none d-lg-flex" title="My Account" aria-label="My account">
                    <i class="fal fa-user-circle" aria-hidden="true"></i>
                </a>
                <a href="{{route('logout')}}" class="action-item d-none d-lg-flex" title="Logout" aria-label="Logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
            <div class="logo-area">
                <a href="{{route('home')}}" aria-label="Home">
                    <img src="{{asset('uploads/setting/'.setting('logo'))}}" alt="Application Logo">
                </a>
            </div>
            <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Close mobile menu">
                <i class="fal fa-times"></i>
            </button>
        </div>

        <nav class="mobile-menu-nav" aria-label="Mobile navigation">
            <ul class="mobile-menu-list">
                @foreach($categories as $category)
                    <li>
                        <a href="{{ route('category.product', $category->slug) }}" 
                           class="mobile-category-item" 
                           data-category-id="{{$category->id}}" 
                           aria-expanded="false" 
                           aria-controls="subcategory-{{$category->id}}">
                            <span>{{ $category->name }}</span>
                            @if($category->sub_categories && $category->sub_categories->count() > 0)
                                <i class="fal fa-angle-right expand-icon"></i>
                            @endif
                        </a>
                        
                        @if($category->sub_categories && $category->sub_categories->count() > 0)
                            <div class="mobile-subcategory-list" id="subcategory-{{$category->id}}">
                                <a href="{{ route('category.product', $category->slug) }}" class="mobile-menu-item category-title">
                                    <i class="fal fa-th-large"></i>All {{ $category->name }}
                                </a>

                                @foreach($category->sub_categories->where('status', true) as $subCategory)
                                    <a href="{{ route('subCategory.product', $subCategory->slug) }}" 
                                       class="mobile-subcategory-item" 
                                       data-subcategory-id="{{$subCategory->id}}" 
                                       aria-expanded="false" 
                                       aria-controls="minicategory-{{$subCategory->id}}">
                                        <span>{{ $subCategory->name }}</span>
                                        @if($subCategory->miniCategory && $subCategory->miniCategory->count() > 0)
                                            <i class="fal fa-angle-right expand-icon"></i>
                                        @endif
                                    </a>

                                    @if($subCategory->miniCategory && $subCategory->miniCategory->count() > 0)
                                        <div class="mobile-mini-category-list" id="minicategory-{{$subCategory->id}}">
                                            <a href="{{ route('subCategory.product', $subCategory->slug) }}" class="mobile-menu-item subcategory-title">
                                                <i class="fal fa-cube"></i>All {{ $subCategory->name }}
                                            </a>
                                            @foreach($subCategory->miniCategory->where('status', true) as $miniCat)
                                                <a href="{{ route('miniCategory.product', $miniCat->slug) }}" class="mobile-menu-item mini-category-item">
                                                    <span>{{ $miniCat->name }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </li>
                @endforeach
                
                <li class="mobile-separator"></li>
                
                {{-- Collections --}}
                <li>
                    <a href="{{ route('collection.list') }}" class="mobile-menu-item">
                        <i class="fal fa-gem"></i>Collections
                    </a>
                </li>
                
                {{-- Campaigns --}}
                <li>
                    <a href="{{ route('campaing.index') }}" class="mobile-menu-item" style="color: var(--brand-accent);">
                        <i class="fal fa-badge-percent"></i>Campaigns
                    </a>
                </li>

                {{-- All Products --}}
                <li>
                    <a href="{{ route('product') }}" class="mobile-menu-item">
                        <i class="fal fa-tags"></i>All Products
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="mobile-menu-footer">
            <div class="mobile-menu-section">
                <a href="{{route('cart')}}" class="mobile-menu-item">
                    <i class="fal fa-shopping-bag"></i>Shopping Cart
                    <span class="badge cart-count-badge">{{ $cartCount }}</span>
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

        @guest
        <div class="mobile-auth-buttons">
            <a href="{{route('login')}}" class="mobile-auth-btn signin">
                <i class="fal fa-sign-in-alt"></i>
                <span>Sign In</span>
            </a>
            <a href="{{route('register')}}" class="mobile-auth-btn signup">
                <i class="fal fa-user-plus"></i>
                <span>Sign Up</span>
            </a>
        </div>
        @endguest
    </div>
</header>

@include('layouts.frontend.partials.partial-part.header_advance_search')

{{-- Push the dedicated JavaScript partial --}}
@include('layouts.frontend.partials.partial-part.header_1_script')