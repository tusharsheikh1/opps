<style>
    /* FULLY MOBILE RESPONSIVE COMPACT SIDEBAR DESIGN
    - Desktop: Width 200px (reduced from 240px for more content space)
    - Mobile: Full overlay with smooth transitions
    - No visible scrollbars (hidden but scrollable)
    - Touch-friendly navigation
    - Clean, professional styling
    */
    
    /* Classic Clean Sidebar Styling - Mobile First Approach */
    .main-sidebar {
        background: #ffffff !important;
        border-right: 1px solid #e9ecef !important;
        width: 200px !important;
        transform: translateX(-100%) !important;
        transition: transform 0.3s ease !important;
    }

    /* Desktop Sidebar */
    @media (min-width: 992px) {
        .main-sidebar {
            transform: translateX(0) !important;
            transition: width 0.2s ease !important;
        }
        
        .sidebar-collapse .main-sidebar {
            width: 55px !important;
        }
    }

    /* Mobile Sidebar Open State */
    .sidebar-open .main-sidebar {
        transform: translateX(0) !important;
    }

    .brand-link {
        background: #f8f9fa !important;
        border-bottom: 1px solid #e9ecef !important;
        padding: 0.5rem 0.75rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: background-color 0.2s ease !important;
        min-height: 50px !important;
    }

    @media (min-width: 768px) {
        .brand-link {
            padding: 0.6rem 0.8rem !important;
            min-height: 55px !important;
        }
    }

    @media (hover: hover) {
        .brand-link:hover {
            background: #e9ecef !important;
            text-decoration: none !important;
        }
    }

    .brand-image {
        max-height: 28px !important;
        width: auto !important;
    }

    @media (min-width: 768px) {
        .brand-image {
            max-height: 32px !important;
        }
    }

    .sidebar {
        padding: 0.4rem 0 !important;
        /* Hide scrollbar completely */
        scrollbar-width: none !important; /* Firefox */
        -ms-overflow-style: none !important; /* Internet Explorer 10+ */
    }

    @media (min-width: 768px) {
        .sidebar {
            padding: 0.5rem 0 !important;
        }
    }

    /* Hide scrollbar for WebKit browsers */
    .sidebar::-webkit-scrollbar {
        display: none !important;
    }

    /* Section Headers - Mobile Responsive */
    .nav-section-header {
        padding: 0.4rem 0.75rem 0.2rem 0.75rem !important;
        margin-top: 0.6rem !important;
        font-size: 9px !important;
        font-weight: 600 !important;
        color: #6c757d !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        border-top: 1px solid #f1f3f4 !important;
    }

    @media (min-width: 768px) {
        .nav-section-header {
            padding: 0.5rem 0.8rem 0.25rem 0.8rem !important;
            margin-top: 0.75rem !important;
            font-size: 10px !important;
        }
    }

    .nav-section-header:first-child {
        margin-top: 0 !important;
        border-top: none !important;
    }

    /* Navigation Links - Mobile Optimized */
    .nav-sidebar .nav-item > .nav-link {
        margin: 0 0.5rem 0.2rem 0.5rem !important;
        padding: 0.6rem 0.6rem !important;
        font-size: 12px !important;
        font-weight: 400 !important;
        color: #495057 !important;
        border-radius: 0.375rem !important;
        transition: all 0.2s ease !important;
        background: transparent !important;
        border: none !important;
        line-height: 1.3 !important;
        display: flex !important;
        align-items: center !important;
        min-height: 42px !important; /* Touch-friendly */
    }

    @media (min-width: 768px) {
        .nav-sidebar .nav-item > .nav-link {
            margin: 0 0.6rem 0.25rem 0.6rem !important;
            padding: 0.5rem 0.65rem !important;
            font-size: 13px !important;
            line-height: 1.4 !important;
            min-height: auto !important;
        }
    }

    .nav-sidebar .nav-item > .nav-link:hover {
        background: #f8f9fa !important;
        color: #212529 !important;
    }

    .nav-sidebar .nav-item > .nav-link.active,
    .nav-sidebar .nav-item.menu-open > .nav-link {
        background: #007bff !important;
        color: #ffffff !important;
    }

    .nav-sidebar .nav-item > .nav-link .nav-icon {
        margin-right: 0.5rem !important;
        width: 16px !important;
        text-align: center !important;
        font-size: 14px !important;
        flex-shrink: 0 !important;
    }

    @media (max-width: 575.98px) {
        .nav-sidebar .nav-item > .nav-link .nav-icon {
            width: 18px !important;
            font-size: 15px !important;
        }
    }

    .nav-sidebar .nav-item > .nav-link .right {
        transition: transform 0.2s ease !important;
        color: #adb5bd !important;
        font-size: 12px !important;
        margin-left: auto !important;
    }

    .nav-sidebar .nav-item.menu-open > .nav-link .right {
        transform: rotate(90deg) !important;
        color: #ffffff !important;
    }

    /* Sub-navigation (Treeview) - Mobile Optimized */
    .nav-treeview {
        background: #f8f9fa !important;
        border-radius: 0.375rem !important;
        margin: 0.2rem 0.5rem !important;
        padding: 0.2rem 0 !important;
        border-left: 2px solid #e9ecef !important;
    }

    @media (min-width: 768px) {
        .nav-treeview {
            margin: 0.25rem 0.6rem !important;
            padding: 0.25rem 0 !important;
        }
    }

    .nav-treeview .nav-item > .nav-link {
        margin: 0 0.35rem 0.2rem 0.35rem !important;
        padding: 0.5rem 0.55rem !important;
        font-size: 11px !important;
        color: #6c757d !important;
        border-radius: 0.25rem !important;
        font-weight: 400 !important;
        background: transparent !important;
        line-height: 1.2 !important;
        min-height: 38px !important; /* Touch-friendly */
        display: flex !important;
        align-items: center !important;
    }

    @media (min-width: 768px) {
        .nav-treeview .nav-item > .nav-link {
            margin: 0 0.4rem 0.25rem 0.4rem !important;
            padding: 0.375rem 0.6rem !important;
            font-size: 12px !important;
            line-height: 1.3 !important;
            min-height: auto !important;
        }
    }

    .nav-treeview .nav-item > .nav-link:hover {
        background: #ffffff !important;
        color: #495057 !important;
    }

    .nav-treeview .nav-item > .nav-link.active {
        background: #e3f2fd !important;
        color: #0056b3 !important;
        font-weight: 500 !important;
    }

    .nav-treeview .nav-item > .nav-link .nav-icon {
        margin-right: 0.4rem !important;
        width: 14px !important;
        font-size: 12px !important;
        color: #adb5bd !important;
        flex-shrink: 0 !important;
    }

    @media (max-width: 575.98px) {
        .nav-treeview .nav-item > .nav-link .nav-icon {
            width: 16px !important;
            font-size: 13px !important;
        }
    }

    .nav-treeview .nav-item > .nav-link:hover .nav-icon,
    .nav-treeview .nav-item > .nav-link.active .nav-icon {
        color: inherit !important;
    }

    /* Nested treeview - Mobile Optimized */
    .nav-treeview .nav-treeview {
        background: #f1f3f4 !important;
        border-left: 2px solid #dee2e6 !important;
        margin-left: 0.5rem !important;
        margin-right: 0.35rem !important;
    }

    @media (min-width: 768px) {
        .nav-treeview .nav-treeview {
            margin-left: 0.6rem !important;
            margin-right: 0.4rem !important;
        }
    }

    .nav-treeview .nav-treeview .nav-item > .nav-link {
        font-size: 10px !important;
        padding: 0.4rem !important;
        min-height: 36px !important;
    }

    @media (min-width: 768px) {
        .nav-treeview .nav-treeview .nav-item > .nav-link {
            font-size: 11px !important;
            padding: 0.25rem 0.4rem !important;
            min-height: auto !important;
        }
    }

    /* Logout Section - Mobile Optimized */
    .logout-section {
        margin-top: 0.8rem !important;
        border-top: 1px solid #e9ecef !important;
        padding-top: 0.6rem !important;
    }

    @media (min-width: 768px) {
        .logout-section {
            margin-top: 1rem !important;
            padding-top: 0.75rem !important;
        }
    }

    .logout-section .nav-link {
        color: #dc3545 !important;
    }

    .logout-section .nav-link:hover {
        background: #f8f9fa !important;
        color: #c82333 !important;
    }

    /* Collapsed Sidebar Adjustments - Desktop Only */
    @media (min-width: 992px) {
        .sidebar-collapse .nav-sidebar .nav-item > .nav-link {
            margin: 0 0.2rem 0.25rem 0.2rem !important;
            padding: 0.45rem !important;
            text-align: center !important;
        }

        .sidebar-collapse .nav-sidebar .nav-item > .nav-link .nav-icon {
            margin: 0 !important;
            font-size: 15px !important;
        }

        .sidebar-collapse .nav-sidebar .nav-item > .nav-link p,
        .sidebar-collapse .nav-sidebar .nav-item > .nav-link .right,
        .sidebar-collapse .nav-section-header {
            display: none !important;
        }

        .sidebar-collapse .nav-treeview {
            display: none !important;
        }
    }

    /* Mobile-specific Navigation Styles */
    @media (max-width: 991.98px) {
        /* Ensure touch-friendly navigation */
        .nav-sidebar .nav-item > .nav-link {
            min-height: 44px !important;
            touch-action: manipulation !important;
        }
        
        .nav-treeview .nav-item > .nav-link {
            min-height: 40px !important;
            touch-action: manipulation !important;
        }
        
        /* Improve text readability on mobile */
        .nav-sidebar .nav-item > .nav-link p {
            font-weight: 500 !important;
        }
        
        /* Add more padding for easier tapping */
        .nav-section-header {
            padding: 0.6rem 0.75rem 0.3rem 0.75rem !important;
        }
    }

    /* Small Mobile Adjustments */
    @media (max-width: 575.98px) {
        .nav-sidebar .nav-item > .nav-link {
            margin: 0 0.4rem 0.2rem 0.4rem !important;
            padding: 0.7rem 0.6rem !important;
            font-size: 13px !important;
            min-height: 46px !important;
        }
        
        .nav-treeview .nav-item > .nav-link {
            padding: 0.6rem 0.5rem !important;
            font-size: 12px !important;
            min-height: 42px !important;
        }

        .nav-section-header {
            font-size: 10px !important;
            padding: 0.7rem 0.75rem 0.4rem 0.75rem !important;
        }
    }

    /* Smooth animations for all devices */
    .nav-item {
        transition: all 0.2s ease !important;
    }

    /* Badge styling for counts/notifications - Mobile Friendly */
    .nav-link .badge {
        font-size: 9px !important;
        padding: 0.2rem 0.35rem !important;
        border-radius: 0.75rem !important;
        min-width: 18px !important;
        line-height: 1 !important;
    }

    @media (min-width: 768px) {
        .nav-link .badge {
            font-size: 10px !important;
            padding: 0.25rem 0.4rem !important;
            min-width: 20px !important;
        }
    }

    /* Mobile Swipe Indicator */
    @media (max-width: 991.98px) {
        .main-sidebar::before {
            content: '' !important;
            position: absolute !important;
            top: 50% !important;
            right: -8px !important;
            width: 4px !important;
            height: 30px !important;
            background: #dee2e6 !important;
            border-radius: 0 2px 2px 0 !important;
            transform: translateY(-50%) !important;
            opacity: 0 !important;
            transition: opacity 0.2s ease !important;
            z-index: 1 !important;
        }
        
        .sidebar-open .main-sidebar::before {
            opacity: 0.5 !important;
        }
    }

    /* Improved Mobile Menu Hierarchy */
    @media (max-width: 991.98px) {
        .nav-treeview {
            border-left-width: 3px !important;
            margin-left: 0.3rem !important;
            margin-right: 0.3rem !important;
        }
        
        .nav-treeview .nav-treeview {
            border-left-width: 2px !important;
            margin-left: 0.6rem !important;
        }
    }

    /* Focus styles for accessibility - Mobile optimized */
    @media (max-width: 991.98px) {
        .nav-sidebar .nav-item > .nav-link:focus,
        .nav-treeview .nav-item > .nav-link:focus {
            outline: 2px solid #007bff !important;
            outline-offset: 2px !important;
            z-index: 1 !important;
        }
    }

    /* Prevent text selection on mobile navigation */
    @media (max-width: 991.98px) {
        .nav-sidebar,
        .nav-treeview {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
        }
    }

    /* High contrast mode improvements for mobile */
    @media (prefers-contrast: high) and (max-width: 991.98px) {
        .nav-sidebar .nav-item > .nav-link {
            border: 1px solid transparent !important;
        }
        
        .nav-sidebar .nav-item > .nav-link:hover,
        .nav-sidebar .nav-item > .nav-link:focus {
            border-color: #495057 !important;
        }
    }

    /* Improved mobile sidebar close button area */
    @media (max-width: 991.98px) {
        .brand-link {
            position: relative !important;
        }
        
        .brand-link::after {
            content: '×' !important;
            position: absolute !important;
            right: 15px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            font-size: 24px !important;
            color: #6c757d !important;
            cursor: pointer !important;
            width: 30px !important;
            height: 30px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 50% !important;
            background: rgba(108, 117, 125, 0.1) !important;
            transition: all 0.2s ease !important;
        }
        
        .brand-link:hover::after {
            background: rgba(108, 117, 125, 0.2) !important;
            color: #495057 !important;
        }
    }

    /* Dark mode support for mobile */
    @media (prefers-color-scheme: dark) and (max-width: 991.98px) {
        .main-sidebar {
            background: #1a1a1a !important;
            border-right-color: #333 !important;
        }
        
        .brand-link {
            background: #2a2a2a !important;
            border-bottom-color: #333 !important;
        }
        
        .nav-sidebar .nav-item > .nav-link {
            color: #e0e0e0 !important;
        }
        
        .nav-sidebar .nav-item > .nav-link:hover {
            background: #333 !important;
            color: #fff !important;
        }
        
        .nav-section-header {
            color: #999 !important;
            border-top-color: #333 !important;
        }
        
        .nav-treeview {
            background: #2a2a2a !important;
        }
    }
</style>

<aside class="main-sidebar sidebar-light-primary elevation-1">
    {{-- KEEPING the onclick here as this is the brand link/close button on mobile --}}
    <a href="{{route('admin.dashboard')}}" class="brand-link" onclick="if(window.innerWidth < 992) document.body.classList.remove('sidebar-open')">
        <img src="/uploads/setting/{{setting('logo')}}" alt="Logo" class="brand-image">
    </a>

    <div class="sidebar">
        <nav class="mt-1">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-section-header">Overview</li>
                
                <li class="nav-item {{Request::is('admin') ? 'menu-is-opening menu-open':''}}">
                    {{-- onclick removed previously --}}
                    <a href="{{route('admin.dashboard')}}" class="nav-link {{Request::is('admin') ? 'active':''}}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                @if(auth()->user()->desig ==1 || auth()->user()->desig ==2 || auth()->user()->desig ==4)
                <li class="nav-section-header">Orders</li>
                
                <li class="nav-item {{Request::is('admin/order*') ? 'menu-is-opening menu-open':''}}">
                    {{-- Dropdown trigger, no onclick needed --}}
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Orders
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.order.index')}}"
                                class="nav-link {{Request::is('admin/order') ? 'active':''}}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>All Orders</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.order.pending')}}"
                                class="nav-link {{Request::is('admin/order/pending') ? 'active':''}}">
                                <i class="fas fa-clock nav-icon"></i>
                                <p>New Orders</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.order.processing')}}"
                                class="nav-link {{Request::is('admin/order/processing') ? 'active':''}}">
                                <i class="fas fa-cog nav-icon"></i>
                                <p>Processing</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.order.delivered')}}"
                                class="nav-link {{Request::is('admin/order/delivered') ? 'active':''}}">
                                <i class="fas fa-check nav-icon"></i>
                                <p>Delivered</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.order.pre')}}"
                                class="nav-link {{Request::is('admin/order/pre') ? 'active':''}}">
                                <i class="fas fa-calendar-plus nav-icon"></i>
                                <p>Pre Orders</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.order.partials')}}"
                                class="nav-link {{Request::is('admin/order/partials') ? 'active':''}}">
                                <i class="fas fa-shipping-fast nav-icon"></i>
                                <p>Partial Delivery</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.order.cancel')}}"
                                class="nav-link {{Request::is('admin/order/cancel') ? 'active':''}}">
                                <i class="fas fa-ban nav-icon"></i>
                                <p>Cancelled</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->desig ==1 || auth()->user()->desig ==2 || auth()->user()->desig ==3)
                <li class="nav-section-header">Products</li>

                <li class="nav-item {{Request::is('admin/product*') ? 'menu-is-opening menu-open':''}}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="nav-icon fas fa-box"></i>
                        <p>
                            Products
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.product.create')}}"
                                class="nav-link {{Request::is('admin/product/create') ? 'active':''}}">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Add Product</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.product.index')}}"
                                class="nav-link {{Request::is('admin/product') ? 'active':''}}">
                                <i class="fas fa-boxes nav-icon"></i>
                                <p>All Products</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.product.active')}}"
                                class="nav-link {{Request::is('admin/product/active') ? 'active':''}}">
                                <i class="fas fa-check-circle nav-icon"></i>
                                <p>Active Products</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.product.unaproved')}}"
                                class="nav-link {{Request::is('admin/product/unaproved') ? 'active':''}}">
                                <i class="fas fa-clock nav-icon"></i>
                                <p>Pending Approval</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.product.disable')}}"
                                class="nav-link {{Request::is('admin/product/disable') ? 'active':''}}">
                                <i class="fas fa-times-circle nav-icon"></i>
                                <p>Disabled</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.low.product')}}"
                                class="nav-link {{Request::is('admin/low-stock-products') ? 'active':''}}">
                                <i class="fas fa-exclamation-triangle nav-icon"></i>
                                <p>Low Stock</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.product.reached')}}"
                                class="nav-link {{Request::is('admin/product/reached') ? 'active':''}}">
                                <i class="fas fa-star nav-icon"></i>
                                <p>Featured</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.product.imex')}}"
                                class="nav-link {{Request::is('admin/product/bulk') ? 'active':''}}">
                                <i class="fas fa-file-import nav-icon"></i>
                                <p>Import/Export</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->desig ==1 || auth()->user()->desig ==2)
                <li class="nav-item {{Request::is('admin/classic*') ? 'menu-is-opening menu-open':''}}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="nav-icon fas fa-gem"></i>
                        <p>
                            Classic Products
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.classic.form')}}"
                                class="nav-link {{Request::is('admin/classic/form') ? 'active':''}}">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Add Classic</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.classic.index')}}"
                                class="nav-link {{Request::is('admin/classic/list') ? 'active':''}}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>List Classic</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{Request::is('admin/category*') || Request::is('admin/sub-category*') || Request::is('admin/mini-categories*') || Request::is('admin/extra-categories*') ? 'menu-is-opening menu-open':''}}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="nav-icon fas fa-sitemap"></i>
                        <p>
                            Categories
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{Request::is('admin/category*') ? 'menu-is-opening menu-open':''}}">
                            <a href="javascript:void(0)" class="nav-link">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <p>
                                    Main Categories
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    {{-- onclick removed previously --}}
                                    <a href="{{route('admin.category.create')}}"
                                        class="nav-link {{Request::is('admin/category/create') ? 'active':''}}">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Add</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    {{-- onclick removed previously --}}
                                    <a href="{{route('admin.category.index')}}"
                                        class="nav-link {{Request::is('admin/category') ? 'active':''}}">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>List</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item {{Request::is('admin/sub-category*') ? 'menu-is-opening menu-open':''}}">
                            <a href="javascript:void(0)" class="nav-link">
                                <i class="nav-icon fas fa-stream"></i>
                                <p>
                                    Sub Categories
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    {{-- onclick removed previously --}}
                                    <a href="{{route('admin.sub-category.create')}}"
                                        class="nav-link {{Request::is('admin/sub-category/create') ? 'active':''}}">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Add</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    {{-- onclick removed previously --}}
                                    <a href="{{route('admin.sub-category.index')}}"
                                        class="nav-link {{Request::is('admin/sub-category') ? 'active':''}}">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>List</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item {{Request::is('admin/mini-categories*') ? 'menu-is-opening menu-open':''}}">
                            <a href="javascript:void(0)" class="nav-link">
                                <i class="nav-icon fas fa-th-list"></i>
                                <p>
                                    Mini Categories
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    {{-- onclick removed previously --}}
                                    <a href="{{route('admin.minicategory')}}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Add</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    {{-- onclick removed previously --}}
                                    <a href="{{route('admin.minicategory.list')}}" class="nav-link">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>List</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item {{Request::is('admin/extra-categories*') ? 'menu-is-opening menu-open':''}}">
                            <a href="javascript:void(0)" class="nav-link">
                                <i class="nav-icon fas fa-plus-square"></i>
                                <p>
                                    Extra Categories
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    {{-- onclick removed previously --}}
                                    <a href="{{route('admin.extracategory')}}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Add</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    {{-- onclick removed previously --}}
                                    <a href="{{route('admin.extracategory.list')}}" class="nav-link">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>List</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{Request::is('admin/brand*') ? 'menu-is-opening menu-open':''}}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="nav-icon fas fa-stamp"></i>
                        <p>
                            Brands
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.brand.create')}}"
                                class="nav-link {{Request::is('admin/brand/create') ? 'active':''}}">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Add Brand</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.brand.index')}}"
                                class="nav-link {{Request::is('admin/brand') ? 'active':''}}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>Brand List</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{Request::is('admin/collection*') ? 'menu-is-opening menu-open':''}}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="nav-icon fas fa-shapes"></i>
                        <p>
                            Collections
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.collection.create')}}"
                                class="nav-link {{Request::is('admin/collection/create') ? 'active':''}}">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Add Collection</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.collection.index')}}"
                                class="nav-link {{Request::is('admin/collection') ? 'active':''}}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>Collection List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->desig ==1)
                <li class="nav-item {{Request::is('admin/attribute*') || Request::is('admin/color*') || Request::is('admin/size*') ? 'menu-is-opening menu-open':''}}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="nav-icon fas fa-sliders-h"></i>
                        <p>
                            Attributes
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.attribute.form')}}"
                                class="nav-link {{Request::is('admin/attribute/form') ? 'active':''}}">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Add Attribute</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.attribute.index')}}"
                                class="nav-link {{Request::is('admin/attribute/list') ? 'active':''}}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>Attribute List</p>
                            </a>
                        </li>
                        <li class="nav-item {{Request::is('admin/color*') ? 'menu-is-opening menu-open':''}}">
                            <a href="javascript:void(0)" class="nav-link">
                                <i class="nav-icon fas fa-palette"></i>
                                <p>
                                    Colors
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    {{-- onclick removed previously --}}
                                    <a href="{{route('admin.color.create')}}"
                                        class="nav-link {{Request::is('admin/color/create') ? 'active':''}}">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Add Color</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    {{-- onclick removed previously --}}
                                    <a href="{{route('admin.color.index')}}"
                                        class="nav-link {{Request::is('admin/color') ? 'active':''}}">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>Color List</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{Request::is('admin/size*') ? 'menu-is-opening menu-open':''}}">
                            <a href="javascript:void(0)" class="nav-link">
                                <i class="nav-icon fas fa-ruler-vertical"></i>
                                <p>
                                    Sizes
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    {{-- onclick removed previously --}}
                                    <a href="{{route('admin.size.create')}}"
                                        class="nav-link {{Request::is('admin/size/create') ? 'active':''}}">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Add Size</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    {{-- onclick removed previously --}}
                                    <a href="{{route('admin.size.index')}}"
                                        class="nav-link {{Request::is('admin/size') ? 'active':''}}">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>Size List</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{Request::is('admin/tag*') ? 'menu-is-opening menu-open':''}}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="nav-icon fas fa-hashtag"></i>
                        <p>
                            Tags
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.tag.create')}}"
                                class="nav-link {{Request::is('admin/tag/create') ? 'active':''}}">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Add Tag</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.tag.index')}}"
                                class="nav-link {{Request::is('admin/tag') ? 'active':''}}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>Tag List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->desig ==1 || auth()->user()->desig ==2)
                <li class="nav-section-header">Customers & Vendors</li>

                <li class="nav-item {{Request::is('admin/customer*') || Request::is('admin/subscribe*') ? 'menu-is-opening menu-open':''}}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Customers
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.customer.create')}}"
                                class="nav-link {{Request::is('admin/customer/create') ? 'active':''}}">
                                <i class="fas fa-user-plus nav-icon"></i>
                                <p>Add Customer</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.customer.index')}}"
                                class="nav-link {{Request::is('admin/customer') ? 'active':''}}">
                                <i class="fas fa-users nav-icon"></i>
                                <p>Customer List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.subscribe')}}" 
                                class="nav-link {{Request::is('admin/subscribe') ? 'active':''}}">
                                <i class="fas fa-envelope nav-icon"></i>
                                <p>Subscribers</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->desig ==1)
                <li class="nav-section-header">Marketing</li>

                <li class="nav-item {{Request::is('admin/campaing*') ? 'menu-is-opening menu-open':''}}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="nav-icon fas fa-bullhorn"></i>
                        <p>
                            Campaigns
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.campaing.create')}}"
                                class="nav-link {{Request::is('admin/campaing/create') ? 'active':''}}">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Create Campaign</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.campaing.index')}}"
                                class="nav-link {{Request::is('admin/campaing/list') ? 'active':''}}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>Campaign List</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{Request::is('admin/coupon*') ? 'menu-is-opening menu-open':''}}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>
                            Coupons
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.coupon.create')}}"
                                class="nav-link {{Request::is('admin/coupon/create') ? 'active':''}}">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Add Coupon</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.coupon.index')}}"
                                class="nav-link {{Request::is('admin/coupon') ? 'active':''}}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>Coupon List</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{Request::is('admin/slider*') ? 'menu-is-opening menu-open':''}}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="nav-icon fas fa-images"></i>
                        <p>
                            Sliders
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.slider.create')}}"
                                class="nav-link {{Request::is('admin/slider/create') ? 'active':''}}">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Add Slider</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.slider.index')}}"
                                class="nav-link {{Request::is('admin/slider') ? 'active':''}}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>Slider List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->desig ==1 || auth()->user()->desig ==2)
                <li class="nav-section-header">Content</li>

                <li class="nav-item">
                    {{-- onclick removed previously --}}
                    <a href="{{ route('admin.notice_index') }}" class="nav-link {{Request::is('admin/notice*') ? 'active':''}}">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notices</p>
                    </a>
                </li>

                <li class="nav-item {{Request::is('admin/blog*') || Request::is('admin/blogs*') || Request::is('admin/Create-new-blog*') || Request::is('admin/user-blogs*') ? 'menu-is-opening menu-open':''}}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-blog"></i>
                        <p>
                            Blog
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.new_blog')}}"
                                class="nav-link {{Request::is('admin/Create-new-blog') ? 'active':''}}">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Create Blog</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.index')}}"
                                class="nav-link {{Request::is('admin/blogs') ? 'active':''}}">
                                <i class="fas fa-edit nav-icon"></i>
                                <p>My Blogs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.user_blog')}}"
                                class="nav-link {{Request::is('admin/user-blogs') ? 'active':''}}">
                                <i class="fas fa-users nav-icon"></i>
                                <p>User Blogs</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->desig ==1)
                <li class="nav-item {{Request::is('admin/page*') ? 'menu-is-opening menu-open':''}}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                            Pages
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.page.create')}}"
                                class="nav-link {{Request::is('admin/page/create') ? 'active':''}}">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Create Page</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.pages')}}"
                                class="nav-link {{Request::is('admin/pages') ? 'active':''}}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>All Pages</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    {{-- onclick removed previously --}}
                    <a href="{{route('admin.gallery')}}" 
                        class="nav-link {{Request::is('admin/gallery') ? 'active':''}}">
                        <i class="nav-icon fas fa-images"></i>
                        <p>Media Gallery</p>
                    </a>
                </li>
                @endif

                @if(auth()->user()->desig ==1)
                <li class="nav-section-header">Communication</li>

                <li class="nav-item {{Request::is('admin/connection*') || Request::is('admin/mail*') || Request::is('admin/ticket*') ? 'menu-is-opening menu-open':''}}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-comments"></i>
                        <p>
                            Communication
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.connection.live.chat')}}"
                                class="nav-link {{Request::is('admin/connection/live-chat') ? 'active':''}}">
                                <i class="fas fa-comment-dots nav-icon"></i>
                                <p>Live Chat</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.mail')}}" 
                                class="nav-link {{Request::is('admin/mail') ? 'active':''}}">
                                <i class="fas fa-envelope nav-icon"></i>
                                <p>Mail</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.ticket')}}" 
                                class="nav-link {{Request::is('admin/ticket') ? 'active':''}}">
                                <i class="fas fa-ticket-alt nav-icon"></i>
                                <p>Support Tickets</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->desig ==1)
                <li class="nav-section-header">Users</li>

                <li class="nav-item {{Request::is('admin/staff*') || Request::is('admin/staf*') ? 'menu-is-opening menu-open':''}}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="nav-icon fas fa-user-nurse"></i>
                        <p>
                            Staff
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.staff.create')}}"
                                class="nav-link {{Request::is('admin/staf/create') ? 'active':''}}">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Add Staff</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.staff.list')}}"
                                class="nav-link {{Request::is('admin/staf/list') ? 'active':''}}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>Staff List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->desig ==1)
                <li class="nav-section-header">Settings</li>

                <li class="nav-item {{Request::is('admin/setting*') ? 'menu-is-opening menu-open':''}}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Settings
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.setting')}}" 
                                class="nav-link {{Request::is('admin/setting') && !Request::is('admin/setting/*') ? 'active':''}}">
                                <i class="fas fa-cog nav-icon"></i>
                                <p>Basic Settings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.setting.site_info')}}" 
                                class="nav-link {{Request::is('admin/setting/site_info') ? 'active':''}}">
                                <i class="fas fa-info-circle nav-icon"></i>
                                <p>Site Information</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.setting.shop_settings')}}" 
                                class="nav-link {{Request::is('admin/setting/shop_settings') ? 'active':''}}">
                                <i class="fas fa-store nav-icon"></i>
                                <p>Shop Settings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.setting.getway')}}" 
                                class="nav-link {{Request::is('admin/setting/getway') ? 'active':''}}">
                                <i class="fas fa-credit-card nav-icon"></i>
                                <p>Payment Gateway</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.setting.mailsmsapireglog')}}" 
                                class="nav-link {{Request::is('admin/setting/mailsmsapireglog') ? 'active':''}}">
                                <i class="fas fa-envelope-open nav-icon"></i>
                                <p>Mail & SMS</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.setting.home')}}" 
                                class="nav-link {{Request::is('admin/setting/home') ? 'active':''}}">
                                <i class="fas fa-home nav-icon"></i>
                                <p>Homepage</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.setting.layout')}}" 
                                class="nav-link {{Request::is('admin/setting/layout') ? 'active':''}}">
                                <i class="fas fa-th-large nav-icon"></i>
                                <p>Layout</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.setting.header')}}" 
                                class="nav-link {{Request::is('admin/setting/header') ? 'active':''}}">
                                <i class="fas fa-window-maximize nav-icon"></i>
                                <p>Header & Footer</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.setting.seo')}}" 
                                class="nav-link {{Request::is('admin/setting/seo') ? 'active':''}}">
                                <i class="fas fa-search nav-icon"></i>
                                <p>SEO Settings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.setting.color')}}" 
                                class="nav-link {{Request::is('admin/setting/color') ? 'active':''}}">
                                <i class="fas fa-palette nav-icon"></i>
                                <p>Color Scheme</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.setting.courier')}}" 
                                class="nav-link {{Request::is('admin/setting/courier') ? 'active':''}}">
                                <i class="fas fa-shipping-fast nav-icon"></i>
                                <p>Courier</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- onclick removed previously --}}
                            <a href="{{route('admin.setting.docs')}}" 
                                class="nav-link {{Request::is('admin/setting/docs') ? 'active':''}}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Documents</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <div class="logout-section">
                    <li class="nav-section-header">Account</li>
                    
                    <li class="nav-item">
                        {{-- REMOVING conflicting inline onclick on this regular link --}}
                        <a href="{{route('admin.profile.change.password')}}" 
                            class="nav-link {{Request::is('admin/profile/change-password') ? 'active':''}}">
                            <i class="fas fa-key nav-icon"></i>
                            <p>Change Password</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        {{-- KEEPING the onclick here as it's required for the logout form submission --}}
                        <a href="{{ route('logout') }}" class="nav-link" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </div>
            </ul>
        </nav>
    </div>
</aside>