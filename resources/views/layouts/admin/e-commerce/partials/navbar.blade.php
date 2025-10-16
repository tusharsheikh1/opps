<style>
    /* 
    ENHANCED MOBILE-FIRST NAVBAR DESIGN
    - Touch-friendly interactions (minimum 44px touch targets)
    - Better mobile dropdown positioning
    - Improved mobile navigation flow
    - Enhanced accessibility and performance
    */
    
    :root {
        /* Mobile-optimized variables */
        --mobile-touch-target: 44px;
        --mobile-spacing-xs: 0.25rem;
        --mobile-spacing-sm: 0.5rem;
        --mobile-spacing-md: 0.75rem;
        --mobile-spacing-lg: 1rem;
        
        /* Enhanced colors */
        --navbar-bg: #ffffff;
        --navbar-border: #e9ecef;
        --primary-color: #007bff;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --text-dark: #212529;
        --text-muted: #6c757d;
        --gray-50: #f8f9fa;
        --gray-100: #f1f3f4;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        
        /* Mobile-friendly shadows and transitions */
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 2px 8px rgba(0, 0, 0, 0.15);
        --shadow-lg: 0 4px 12px rgba(0, 0, 0, 0.2);
        --transition-fast: 0.15s ease;
        --transition-normal: 0.25s ease;
    }

    /* Enhanced Navbar Base - Mobile First */
    .main-header.navbar {
        background: var(--navbar-bg) !important;
        border-bottom: 1px solid var(--navbar-border) !important;
        box-shadow: var(--shadow-sm) !important;
        padding: 0 var(--mobile-spacing-md) !important;
        min-height: 60px !important;
        height: 60px !important;
        max-height: 60px !important;
        display: flex !important;
        align-items: center !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 1030 !important;
        margin: 0 !important;
        flex-shrink: 0 !important;
        border-radius: 0 !important;
        transition: left var(--transition-normal) !important;
    }

    /* Desktop positioning adjustments */
    @media (min-width: 992px) {
        .main-header.navbar {
            left: 200px !important;
            padding: 0 1.5rem !important;
        }
        
        .sidebar-collapse .main-header.navbar {
            left: 55px !important;
        }
    }

    /* Tablet adjustments */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .main-header.navbar {
            padding: 0 1rem !important;
        }
    }

    /* Small mobile adjustments */
    @media (max-width: 575.98px) {
        .main-header.navbar {
            padding: 0 var(--mobile-spacing-sm) !important;
        }
    }

    /* Enhanced Navigation Layout - Mobile First */
    .navbar-nav {
        display: flex !important;
        align-items: center !important;
        list-style: none !important;
        margin: 0 !important;
        padding: 0 !important;
        gap: var(--mobile-spacing-sm) !important;
        height: 60px !important;
    }

    .navbar-nav .nav-item {
        display: flex !important;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Enhanced Touch-Friendly Navigation Links */
    .navbar-nav .nav-link {
        color: var(--text-muted) !important;
        font-weight: 500 !important;
        padding: 0.75rem !important;
        border-radius: 0.5rem !important;
        transition: all var(--transition-fast) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 14px !important;
        background: transparent !important;
        border: 1px solid transparent !important;
        text-decoration: none !important;
        min-height: var(--mobile-touch-target) !important;
        min-width: var(--mobile-touch-target) !important;
        margin: 0 !important;
        position: relative !important;
        cursor: pointer !important;
        -webkit-tap-highlight-color: transparent !important;
    }

    /* Enhanced hover states for desktop */
    @media (hover: hover) and (pointer: fine) {
        .navbar-nav .nav-link:hover {
            background: var(--gray-50) !important;
            color: var(--text-dark) !important;
            transform: translateY(-1px) !important;
            box-shadow: var(--shadow-md) !important;
        }
    }

    /* Touch feedback for mobile */
    .navbar-nav .nav-link:active {
        transform: translateY(0) !important;
        background: var(--gray-200) !important;
    }

    .navbar-nav .nav-link:focus,
    .navbar-nav .nav-link:focus-visible {
        outline: 2px solid var(--primary-color) !important;
        outline-offset: 2px !important;
        background: var(--gray-50) !important;
    }

    /* Enhanced Hamburger Menu Button */
    .nav-link[data-widget="pushmenu"] {
        background: var(--gray-50) !important;
        border: 1px solid var(--gray-300) !important;
        width: var(--mobile-touch-target) !important;
        height: var(--mobile-touch-target) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 0.5rem !important;
        margin-right: var(--mobile-spacing-md) !important;
        color: var(--text-muted) !important;
        padding: 0 !important;
    }

    .nav-link[data-widget="pushmenu"]:hover {
        background: var(--gray-200) !important;
        color: var(--text-dark) !important;
        border-color: #ced4da !important;
        transform: translateY(-1px) !important;
    }

    .nav-link[data-widget="pushmenu"]:active {
        transform: translateY(0) !important;
        background: var(--gray-300) !important;
    }

    .nav-link[data-widget="pushmenu"] i {
        font-size: 16px !important;
        line-height: 1 !important;
        margin: 0 !important;
    }

    /* Enhanced Visit Site Button */
    .visit-site-btn {
        background: var(--success-color) !important;
        color: #ffffff !important;
        border: 1px solid var(--success-color) !important;
        padding: 0.75rem 1rem !important;
        font-weight: 500 !important;
        font-size: 14px !important;
        border-radius: 0.5rem !important;
        text-decoration: none !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        transition: all var(--transition-normal) !important;
        min-height: var(--mobile-touch-target) !important;
        margin: 0 !important;
        white-space: nowrap !important;
    }

    .visit-site-btn:hover {
        background: #218838 !important;
        color: #ffffff !important;
        border-color: #1e7e34 !important;
        text-decoration: none !important;
        transform: translateY(-1px) !important;
        box-shadow: var(--shadow-md) !important;
    }

    .visit-site-btn:active {
        transform: translateY(0) !important;
        background: #1e7e34 !important;
    }

    .visit-site-btn i {
        font-size: 12px !important;
        margin: 0 !important;
    }

    /* Right Side Navigation - Enhanced */
    .navbar-nav.ml-auto {
        margin-left: auto !important;
        display: flex !important;
        align-items: center !important;
        gap: var(--mobile-spacing-sm) !important;
        height: 60px !important;
    }

    .navbar-nav.ml-auto .nav-link {
        width: var(--mobile-touch-target) !important;
        height: var(--mobile-touch-target) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 0.5rem !important;
        border: 1px solid var(--gray-300) !important;
        background: #ffffff !important;
        color: var(--text-muted) !important;
        position: relative !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .navbar-nav.ml-auto .nav-link:hover {
        background: var(--gray-50) !important;
        border-color: #ced4da !important;
        color: var(--text-dark) !important;
        transform: translateY(-1px) !important;
    }

    .navbar-nav.ml-auto .nav-link:active {
        transform: translateY(0) !important;
        background: var(--gray-200) !important;
    }

    .navbar-nav.ml-auto .nav-link i {
        font-size: 16px !important;
        line-height: 1 !important;
        margin: 0 !important;
    }

    /* Enhanced User Profile Dropdown - Desktop */
    .user-dropdown {
        display: none !important;
        align-items: center !important;
        background: var(--gray-50) !important;
        border: 1px solid var(--gray-300) !important;
        border-radius: 0.75rem !important;
        padding: 0.5rem 1rem !important;
        margin-left: var(--mobile-spacing-lg) !important;
        transition: all var(--transition-normal) !important;
        cursor: pointer !important;
        min-width: 180px !important;
        color: var(--text-muted) !important;
        text-decoration: none !important;
        min-height: var(--mobile-touch-target) !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }

    @media (min-width: 992px) {
        .user-dropdown {
            display: flex !important;
        }
    }

    .user-dropdown:hover {
        background: var(--gray-200) !important;
        border-color: #ced4da !important;
        text-decoration: none !important;
        color: var(--text-dark) !important;
        transform: translateY(-1px) !important;
        box-shadow: var(--shadow-md) !important;
    }

    .user-dropdown:focus {
        outline: 2px solid var(--primary-color) !important;
        outline-offset: 2px !important;
    }

    .user-avatar {
        width: 32px !important;
        height: 32px !important;
        border-radius: 50% !important;
        background: var(--primary-color) !important;
        color: #ffffff !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        margin-right: 0.75rem !important;
        flex-shrink: 0 !important;
        line-height: 1 !important;
        text-transform: uppercase !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }

    .user-info {
        display: flex !important;
        flex-direction: column !important;
        flex-grow: 1 !important;
        min-width: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .user-name {
        font-weight: 600 !important;
        color: var(--text-dark) !important;
        font-size: 14px !important;
        line-height: 1.2 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        margin-bottom: 2px !important;
        margin-top: 0 !important;
    }

    .user-role {
        font-size: 11px !important;
        color: var(--text-muted) !important;
        line-height: 1.2 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        margin: 0 !important;
    }

    /* Enhanced Mobile User Dropdown */
    .mobile-user-dropdown {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: var(--mobile-touch-target) !important;
        height: var(--mobile-touch-target) !important;
        border-radius: 0.5rem !important;
        border: 1px solid var(--gray-300) !important;
        background: var(--gray-50) !important;
        color: var(--text-muted) !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    @media (min-width: 992px) {
        .mobile-user-dropdown {
            display: none !important;
        }
    }

    .mobile-user-dropdown:hover {
        background: var(--gray-200) !important;
        border-color: #ced4da !important;
        color: var(--text-dark) !important;
        text-decoration: none !important;
        transform: translateY(-1px) !important;
    }

    .mobile-user-dropdown .user-avatar {
        width: 28px !important;
        height: 28px !important;
        font-size: 10px !important;
        margin: 0 !important;
    }

    /* Enhanced Notification Badge */
    .notification-badge {
        position: absolute !important;
        top: -6px !important;
        right: -6px !important;
        background: var(--danger-color) !important;
        color: #ffffff !important;
        border-radius: 50% !important;
        width: 20px !important;
        height: 20px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        border: 2px solid #ffffff !important;
        line-height: 1 !important;
        margin: 0 !important;
        min-width: 20px !important;
        animation: pulse 2s infinite !important;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    /* Enhanced Dropdown Menus - Mobile Optimized */
    .dropdown-menu {
        border: 1px solid var(--gray-300) !important;
        box-shadow: var(--shadow-lg) !important;
        border-radius: 0.75rem !important;
        padding: 0.75rem !important;
        min-width: 280px !important;
        margin-top: 0.5rem !important;
        background: #ffffff !important;
        z-index: 1050 !important;
        max-height: 80vh !important;
        overflow-y: auto !important;
    }

    /* Mobile-specific dropdown positioning */
    @media (max-width: 767.98px) {
        .dropdown-menu {
            position: fixed !important;
            top: 70px !important;
            left: 1rem !important;
            right: 1rem !important;
            width: auto !important;
            min-width: auto !important;
            max-width: none !important;
            transform: none !important;
            margin-top: 0 !important;
        }
        
        .dropdown-menu.dropdown-menu-right {
            left: 1rem !important;
            right: 1rem !important;
        }
    }

    /* Enhanced Dropdown Items */
    .dropdown-item {
        border-radius: 0.5rem !important;
        padding: 0.75rem !important;
        font-size: 14px !important;
        color: var(--text-muted) !important;
        transition: all var(--transition-fast) !important;
        display: flex !important;
        align-items: center !important;
        text-decoration: none !important;
        line-height: 1.4 !important;
        margin-bottom: 0.25rem !important;
        min-height: var(--mobile-touch-target) !important;
        margin-top: 0 !important;
    }

    .dropdown-item:last-child {
        margin-bottom: 0 !important;
    }

    .dropdown-item:hover,
    .dropdown-item:focus {
        background: var(--gray-50) !important;
        color: var(--text-dark) !important;
        text-decoration: none !important;
        transform: translateX(4px) !important;
        outline: none !important;
    }

    .dropdown-item:focus-visible {
        outline: 2px solid var(--primary-color) !important;
        outline-offset: 2px !important;
    }

    .dropdown-item i {
        margin-right: 0.75rem !important;
        width: 20px !important;
        text-align: center !important;
        font-size: 14px !important;
        line-height: 1 !important;
    }

    .dropdown-divider {
        margin: 0.75rem 0 !important;
        border-color: var(--gray-300) !important;
        height: 1px !important;
        background: var(--gray-300) !important;
        border: none !important;
    }

    .dropdown-header {
        padding: 0.75rem !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        color: var(--text-muted) !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        margin-bottom: 0.5rem !important;
        border-bottom: 1px solid var(--gray-100) !important;
    }

    /* Special Dropdown Item Styling */
    .dropdown-item.logout-item {
        color: var(--danger-color) !important;
        border-top: 1px solid var(--gray-100) !important;
        margin-top: 0.5rem !important;
        padding-top: 1rem !important;
    }

    .dropdown-item.logout-item:hover {
        background: var(--gray-50) !important;
        color: #c82333 !important;
    }

    /* Enhanced Notification Items */
    .notification-item {
        padding: 1rem !important;
        border-bottom: 1px solid var(--gray-100) !important;
        display: block !important;
        border-radius: 0.5rem !important;
        margin-bottom: 0.5rem !important;
        transition: all var(--transition-fast) !important;
        margin-top: 0 !important;
    }

    .notification-item:last-child {
        border-bottom: none !important;
        margin-bottom: 0 !important;
    }

    .notification-item:hover {
        background: var(--gray-50) !important;
        transform: translateX(4px) !important;
    }

    .notification-content {
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
        margin: 0 !important;
    }

    .notification-icon {
        width: 36px !important;
        height: 36px !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 14px !important;
        flex-shrink: 0 !important;
        margin: 0 !important;
    }

    .notification-text {
        flex-grow: 1 !important;
        font-size: 13px !important;
        line-height: 1.4 !important;
        margin: 0 !important;
    }

    .notification-text strong {
        font-weight: 600 !important;
        color: var(--text-dark) !important;
        display: block !important;
        margin-bottom: 2px !important;
        margin-top: 0 !important;
    }

    .notification-text small {
        color: var(--text-muted) !important;
        font-size: 12px !important;
        margin: 0 !important;
    }

    .notification-time {
        font-size: 11px !important;
        color: var(--text-muted) !important;
        flex-shrink: 0 !important;
        margin: 0 !important;
    }

    /* Responsive Design Breakpoints */
    @media (max-width: 575.98px) {
        .navbar-nav {
            gap: 0.25rem !important;
        }
        
        .navbar-nav.ml-auto .nav-link {
            width: 40px !important;
            height: 40px !important;
        }

        .nav-link[data-widget="pushmenu"] {
            width: 40px !important;
            height: 40px !important;
            margin-right: var(--mobile-spacing-sm) !important;
        }

        .visit-site-btn {
            padding: 0.625rem 0.75rem !important;
            font-size: 13px !important;
            height: 40px !important;
        }

        .mobile-user-dropdown {
            width: 40px !important;
            height: 40px !important;
        }

        .notification-badge {
            width: 18px !important;
            height: 18px !important;
            font-size: 9px !important;
            top: -4px !important;
            right: -4px !important;
        }
    }

    /* Hide text on very small screens */
    @media (max-width: 480px) {
        .visit-site-btn span {
            display: none !important;
        }
        
        .visit-site-btn {
            padding: 0.625rem !important;
            min-width: 40px !important;
            justify-content: center !important;
        }
    }

    /* Loading States */
    .loading {
        opacity: 0.6 !important;
        pointer-events: none !important;
        position: relative !important;
    }

    .loading::after {
        content: '' !important;
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        width: 16px !important;
        height: 16px !important;
        margin: -8px 0 0 -8px !important;
        border: 2px solid var(--gray-300) !important;
        border-radius: 50% !important;
        border-top-color: var(--primary-color) !important;
        animation: spin 1s ease-in-out infinite !important;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Enhanced Accessibility */
    .navbar-nav .nav-link:focus-visible,
    .dropdown-item:focus-visible,
    .user-dropdown:focus-visible {
        outline: 3px solid var(--primary-color) !important;
        outline-offset: 2px !important;
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .navbar-nav .nav-link {
            border: 2px solid transparent !important;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus {
            border-color: var(--text-dark) !important;
        }
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    /* Touch-specific enhancements */
    @media (pointer: coarse) {
        .navbar-nav .nav-link,
        .dropdown-item {
            min-height: 48px !important; /* Larger touch targets for touch devices */
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        :root {
            --navbar-bg: #1a1a1a;
            --navbar-border: #333333;
            --text-dark: #ffffff;
            --text-muted: #cccccc;
            --gray-50: #2a2a2a;
            --gray-100: #333333;
            --gray-200: #404040;
            --gray-300: #555555;
        }
    }
</style>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link visit-site-btn" href="{{route('home')}}" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>Visit Site</span>
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-label="Notifications">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header">
                    Notifications
                </div>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item notification-item">
                    <div class="notification-content">
                        <div class="notification-icon" style="background: #e3f2fd; color: #1976d2;">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="notification-text">
                            <strong>New order received</strong>
                            <small class="text-muted">Order #12345</small>
                        </div>
                        <div class="notification-time">3m</div>
                    </div>
                </a>
                <a href="#" class="dropdown-item notification-item">
                    <div class="notification-content">
                        <div class="notification-icon" style="background: #e8f5e8; color: #2e7d32;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="notification-text">
                            <strong>New customer registered</strong>
                            <small class="text-muted">John Doe</small>
                        </div>
                        <div class="notification-time">5m</div>
                    </div>
                </a>
                <a href="#" class="dropdown-item notification-item">
                    <div class="notification-content">
                        <div class="notification-icon" style="background: #fff3e0; color: #f57c00;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="notification-text">
                            <strong>Low stock alert</strong>
                            <small class="text-muted">5 products</small>
                        </div>
                        <div class="notification-time">10m</div>
                    </div>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item text-center">
                    <small>View All Notifications</small>
                </a>
            </div>
        </li>

        <!-- Messages -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-label="Messages">
                <i class="fas fa-comments"></i>
                <span class="notification-badge">2</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header">
                    Messages
                </div>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item notification-item">
                    <div class="notification-content">
                        <div class="user-avatar" style="width: 32px; height: 32px; font-size: 11px; margin-right: 0;">
                            JD
                        </div>
                        <div class="notification-text">
                            <strong>John Doe</strong>
                            <small class="text-muted">Order inquiry about product...</small>
                        </div>
                        <div class="notification-time">2m</div>
                    </div>
                </a>
                <a href="#" class="dropdown-item notification-item">
                    <div class="notification-content">
                        <div class="user-avatar" style="width: 32px; height: 32px; font-size: 11px; margin-right: 0; background: #28a745;">
                            SM
                        </div>
                        <div class="notification-text">
                            <strong>Sarah Miller</strong>
                            <small class="text-muted">Question about shipping...</small>
                        </div>
                        <div class="notification-time">15m</div>
                    </div>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item text-center">
                    <small>View All Messages</small>
                </a>
            </div>
        </li>

        <!-- Quick Actions -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-label="Quick Actions">
                <i class="fas fa-plus"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header">
                    Quick Actions
                </div>
                <div class="dropdown-divider"></div>
                <a href="{{route('admin.product.type')}}" class="dropdown-item">
                    <i class="fas fa-box"></i>
                    Add Product
                </a>
                @if(auth()->user()->desig == 1 || auth()->user()->desig == 2)
                <a href="{{routeHelper('category/create')}}" class="dropdown-item">
                    <i class="fas fa-sitemap"></i>
                    Add Category
                </a>
                <a href="{{routeHelper('customer/create')}}" class="dropdown-item">
                    <i class="fas fa-user-plus"></i>
                    Add Customer
                </a>
                @endif
                @if(auth()->user()->desig == 1)
                <a href="{{route('admin.staff.create')}}" class="dropdown-item">
                    <i class="fas fa-user-nurse"></i>
                    Add Staff
                </a>
                @endif
            </div>
        </li>

        <!-- Fullscreen -->
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button" aria-label="Toggle fullscreen">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        <!-- Mobile User Dropdown (hidden on desktop) -->
        <li class="nav-item dropdown d-md-none">
            <a class="nav-link mobile-user-dropdown" data-toggle="dropdown" href="#" role="button" aria-label="User menu">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->name ?? 'ser', 1, 1)) }}
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header">
                    {{ auth()->user()->name ?? 'User' }}
                    <br>
                    <small class="text-muted">
                        @if(auth()->user()->desig == 1)
                            Super Admin
                        @elseif(auth()->user()->desig == 2)
                            Admin
                        @elseif(auth()->user()->desig == 3)
                            Product Manager
                        @elseif(auth()->user()->desig == 4)
                            Order Manager
                        @else
                            User
                        @endif
                    </small>
                </div>
                <div class="dropdown-divider"></div>
                <a href="{{routeHelper('dashboard')}}" class="dropdown-item">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="{{routeHelper('profile/change-password')}}" class="dropdown-item">
                    <i class="fas fa-key"></i>
                    Change Password
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item logout-item" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>

    <!-- Desktop User Profile Dropdown -->
    <div class="dropdown d-none d-md-block">
        <a href="#" class="user-dropdown dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->name ?? 'ser', 1, 1)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name ?? 'User' }}</div>
                <div class="user-role">
                    @if(auth()->user()->desig == 1)
                        Super Admin
                    @elseif(auth()->user()->desig == 2)
                        Admin
                    @elseif(auth()->user()->desig == 3)
                        Product Manager
                    @elseif(auth()->user()->desig == 4)
                        Order Manager
                    @else
                        User
                    @endif
                </div>
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <div class="dropdown-header">
                Account Settings
            </div>
            <div class="dropdown-divider"></div>
            <a href="{{routeHelper('dashboard')}}" class="dropdown-item">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            <a href="{{routeHelper('profile/change-password')}}" class="dropdown-item">
                <i class="fas fa-key"></i>
                Change Password
            </a>
            @if(auth()->user()->desig == 1)
            <a href="{{routeHelper('setting')}}" class="dropdown-item">
                <i class="fas fa-cogs"></i>
                Settings
            </a>
            @endif
            <div class="dropdown-divider"></div>
            <a href="{{ route('logout') }}" class="dropdown-item logout-item" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </div>

    <!-- Hidden logout form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced mobile dropdown handling
    function isMobile() {
        return window.innerWidth < 768;
    }

    // Mobile-friendly dropdown positioning
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(function(toggle) {
        toggle.addEventListener('show.bs.dropdown', function() {
            if (isMobile()) {
                const dropdown = this.nextElementSibling;
                if (dropdown && dropdown.classList.contains('dropdown-menu')) {
                    dropdown.style.position = 'fixed';
                    dropdown.style.top = '70px';
                    dropdown.style.left = '1rem';
                    dropdown.style.right = '1rem';
                    dropdown.style.width = 'auto';
                    dropdown.style.transform = 'none';
                }
            }
        });
    });

    // Touch feedback for mobile devices
    const touchElements = document.querySelectorAll('.nav-link, .dropdown-item, .user-dropdown');
    touchElements.forEach(function(element) {
        element.addEventListener('touchstart', function() {
            this.classList.add('touching');
        });
        
        element.addEventListener('touchend', function() {
            this.classList.remove('touching');
        });
        
        element.addEventListener('touchcancel', function() {
            this.classList.remove('touching');
        });
    });

    // Enhanced keyboard navigation
    document.addEventListener('keydown', function(e) {
        // Escape key closes dropdowns
        if (e.key === 'Escape') {
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(dropdown => {
                const toggle = dropdown.previousElementSibling;
                if (toggle && typeof bootstrap !== 'undefined') {
                    bootstrap.Dropdown.getInstance(toggle)?.hide();
                }
            });
        }
    });

    // Mobile swipe gesture support
    let startX = null;
    let startY = null;

    document.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
    });

    document.addEventListener('touchmove', function(e) {
        if (!startX || !startY) return;

        const currentX = e.touches[0].clientX;
        const currentY = e.touches[0].clientY;
        const diffX = startX - currentX;
        const diffY = startY - currentY;

        // Only handle horizontal swipes
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
            if (diffX > 0) {
                // Swipe left - could trigger sidebar close
                console.log('Swiped left');
            } else {
                // Swipe right - could trigger sidebar open
                console.log('Swiped right');
            }
        }

        startX = null;
        startY = null;
    });

    // Fullscreen toggle functionality
    const fullscreenBtn = document.querySelector('[data-widget="fullscreen"]');
    if (fullscreenBtn) {
        fullscreenBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().then(() => {
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.className = icon.className.replace('fa-expand-arrows-alt', 'fa-compress-arrows-alt');
                    }
                });
            } else {
                document.exitFullscreen().then(() => {
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.className = icon.className.replace('fa-compress-arrows-alt', 'fa-expand-arrows-alt');
                    }
                });
            }
        });
    }

    // Enhanced loading states
    const actionButtons = document.querySelectorAll('.visit-site-btn, .dropdown-item');
    actionButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
                
                this.classList.add('loading');
                this.style.pointerEvents = 'none';
                
                // Simulate loading
                setTimeout(() => {
                    this.classList.remove('loading');
                    this.style.pointerEvents = '';
                }, 2000);
            }
        });
    });

    // Auto-hide dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(dropdown => {
                const toggle = dropdown.previousElementSibling;
                if (toggle && typeof bootstrap !== 'undefined') {
                    bootstrap.Dropdown.getInstance(toggle)?.hide();
                }
            });
        }
    });

    // Window resize handler
    window.addEventListener('resize', function() {
        // Close mobile dropdowns on desktop resize
        if (!isMobile()) {
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(dropdown => {
                const toggle = dropdown.previousElementSibling;
                if (toggle && typeof bootstrap !== 'undefined') {
                    bootstrap.Dropdown.getInstance(toggle)?.hide();
                }
            });
        }
    });

    // Accessibility enhancements
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(function(link) {
        link.addEventListener('focus', function() {
            this.classList.add('focused');
        });
        
        link.addEventListener('blur', function() {
            this.classList.remove('focused');
        });
    });

    // Enhanced touch device detection
    function addTouchClass() {
        if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
            document.body.classList.add('touch-device');
        }
    }

    addTouchClass();

    console.log('Enhanced Mobile-Friendly Navbar initialized');
});
</script>