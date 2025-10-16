<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" type="image/jpg" href="/uploads/setting/{{setting('favicon')}}" />

    <title>@yield('title')</title>
    @include('layouts.global')

    <!-- Classic Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/plugins/fontawesome-free/css/all.min.css">

    @notifyCss
    @stack('css')

    <!-- AdminLTE Core -->
    <link rel="stylesheet" href="/assets/dist/css/adminlte.min.css">
    
    <!-- Classic Clean Theme with Advanced Functionality - FULLY MOBILE RESPONSIVE -->
    <style>
        /* 
        CLEAN CLASSIC DESIGN - FULLY MOBILE RESPONSIVE
        All elements follow normal document flow with perfect mobile support
        */
        :root {
            /* Classic Professional Color Palette */
            --primary: #007bff;
            --primary-dark: #0056b3;
            --primary-light: #e3f2fd;
            --secondary: #6c757d;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            
            /* Clean Neutral Colors */
            --white: #ffffff;
            --gray-50: #f8f9fa;
            --gray-100: #f1f3f4;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-400: #ced4da;
            --gray-500: #adb5bd;
            --gray-600: #6c757d;
            --gray-700: #495057;
            --gray-800: #343a40;
            --gray-900: #212529;
            
            /* Classic Typography */
            --font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            
            /* Responsive Spacing */
            --spacing-xs: 0.25rem;
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            
            /* Mobile spacing adjustments */
            --mobile-spacing-xs: 0.2rem;
            --mobile-spacing-sm: 0.4rem;
            --mobile-spacing-md: 0.75rem;
            --mobile-spacing-lg: 1rem;
            --mobile-spacing-xl: 1.25rem;
            
            /* Subtle Border Radius */
            --radius-sm: 0.25rem;
            --radius-md: 0.375rem;
            --radius-lg: 0.5rem;
            
            /* Clean Shadows */
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.12);
            --shadow-md: 0 2px 6px rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 4px 12px rgba(0, 0, 0, 0.15);
            
            /* Smooth Transitions */
            --transition-fast: 0.15s ease;
            --transition-normal: 0.2s ease;
            --transition-slow: 0.3s ease;
        }

        /* Reset and Base Styles */
        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100% !important;
            overflow: hidden !important;
        }

        body {
            font-family: var(--font-family) !important;
            font-size: 14px !important;
            background-color: var(--gray-100) !important;
            color: var(--gray-700) !important;
            line-height: 1.5 !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
        }

        /* Clean Layout Structure - Mobile First */
        .wrapper {
            height: 100vh !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
        }

        /* Fixed Navbar - Mobile Responsive */
        .main-header {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1030 !important;
            height: 60px !important;
            background: var(--white) !important;
            border-bottom: 1px solid var(--gray-200) !important;
            box-shadow: var(--shadow-sm) !important;
            flex-shrink: 0 !important;
            transition: left var(--transition-normal) !important;
        }

        /* Desktop Navbar Positioning */
        @media (min-width: 992px) {
            .main-header {
                left: 200px !important;
            }
            
            .sidebar-collapse .main-header {
                left: 55px !important;
            }
        }

        /* Mobile Sidebar - Hidden by default */
        .main-sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 200px !important;
            height: 100vh !important;
            z-index: 1025 !important;
            background: var(--white) !important;
            border-right: 1px solid var(--gray-200) !important;
            overflow: hidden !important;
            transition: transform var(--transition-normal) !important;
            transform: translateX(-100%) !important;
        }

        /* Desktop Sidebar */
        @media (min-width: 992px) {
            .main-sidebar {
                transform: translateX(0) !important;
                transition: width var(--transition-normal) !important;
            }
            
            .sidebar-collapse .main-sidebar {
                width: 55px !important;
            }
        }

        /* Mobile Sidebar Open State */
        .sidebar-open .main-sidebar {
            transform: translateX(0) !important;
        }

        /* Mobile Overlay */
        .sidebar-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: rgba(0, 0, 0, 0.5) !important;
            z-index: 1024 !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: all var(--transition-normal) !important;
        }

        .sidebar-open .sidebar-overlay {
            opacity: 1 !important;
            visibility: visible !important;
        }

        @media (min-width: 992px) {
            .sidebar-overlay {
                display: none !important;
            }
        }

        .sidebar {
            height: 100% !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: var(--spacing-md) 0 !important;
        }

        /* Content Area - Fully Responsive */
        .content-wrapper {
            background: var(--gray-100) !important;
            margin-left: 0 !important;
            margin-top: 60px !important;
            min-height: calc(100vh - 60px) !important;
            max-height: calc(100vh - 60px) !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: var(--mobile-spacing-md) !important;
            padding-bottom: var(--mobile-spacing-xl) !important;
            flex: 1 !important;
            box-sizing: border-box !important;
            scroll-behavior: smooth !important;
            transition: margin-left var(--transition-normal) !important;
        }

        /* Desktop Content Wrapper */
        @media (min-width: 992px) {
            .content-wrapper {
                margin-left: 200px !important;
                padding: var(--spacing-lg) !important;
                padding-bottom: var(--spacing-xl) !important;
            }
            
            .sidebar-collapse .content-wrapper {
                margin-left: 55px !important;
            }
        }

        /* Tablet Adjustments */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .content-wrapper {
                padding: var(--spacing-md) !important;
                padding-bottom: var(--spacing-lg) !important;
            }
        }

        /* Small Mobile Adjustments */
        @media (max-width: 575.98px) {
            .content-wrapper {
                padding: var(--mobile-spacing-sm) !important;
                padding-bottom: var(--mobile-spacing-lg) !important;
            }
        }

        /* Proper spacing for content elements - Responsive */
        .content-wrapper > * {
            margin-bottom: var(--spacing-lg) !important;
        }

        .content-wrapper > *:last-child {
            margin-bottom: var(--spacing-lg) !important;
        }

        @media (max-width: 767.98px) {
            .content-wrapper > * {
                margin-bottom: var(--mobile-spacing-lg) !important;
            }

            .content-wrapper > *:last-child {
                margin-bottom: var(--mobile-spacing-lg) !important;
            }
        }

        /* Content Header - Mobile Responsive */
        .content-header {
            background: var(--white) !important;
            padding: var(--mobile-spacing-md) !important;
            margin: var(--mobile-spacing-sm) 0 var(--mobile-spacing-lg) 0 !important;
            border: 1px solid var(--gray-200) !important;
            border-radius: var(--radius-lg) !important;
            box-shadow: var(--shadow-sm) !important;
            position: relative !important;
        }

        @media (min-width: 768px) {
            .content-header {
                padding: var(--spacing-lg) !important;
                margin: var(--spacing-sm) 0 var(--spacing-lg) 0 !important;
            }
        }

        .content-wrapper > *:first-child {
            margin-top: var(--spacing-sm) !important;
        }

        @media (max-width: 767.98px) {
            .content-wrapper > *:first-child {
                margin-top: var(--mobile-spacing-sm) !important;
            }
        }

        .content-header h1 {
            font-size: 1.25rem !important;
            font-weight: 600 !important;
            color: var(--gray-800) !important;
            margin: 0 0 var(--mobile-spacing-sm) 0 !important;
            line-height: 1.3 !important;
        }

        @media (min-width: 768px) {
            .content-header h1 {
                font-size: 1.5rem !important;
                margin: 0 0 var(--spacing-sm) 0 !important;
            }
        }

        .breadcrumb {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 0.8125rem !important;
            flex-wrap: wrap !important;
        }

        @media (min-width: 768px) {
            .breadcrumb {
                font-size: 0.875rem !important;
            }
        }

        .breadcrumb-item {
            color: var(--gray-500) !important;
        }

        .breadcrumb-item.active {
            color: var(--gray-700) !important;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "/" !important;
            color: var(--gray-400) !important;
        }

        /* Cards - Mobile Responsive */
        .card {
            background: var(--white) !important;
            border: 1px solid var(--gray-200) !important;
            border-radius: var(--radius-lg) !important;
            box-shadow: var(--shadow-sm) !important;
            margin-bottom: var(--mobile-spacing-lg) !important;
            transition: box-shadow var(--transition-normal) !important;
        }

        @media (min-width: 768px) {
            .card {
                margin-bottom: var(--spacing-xl) !important;
            }
            
            .card:hover {
                box-shadow: var(--shadow-md) !important;
            }
        }

        .card:last-of-type {
            margin-bottom: var(--mobile-spacing-lg) !important;
        }

        @media (min-width: 768px) {
            .card:last-of-type {
                margin-bottom: var(--spacing-xl) !important;
            }
        }

        .card-header {
            background: var(--gray-50) !important;
            border-bottom: 1px solid var(--gray-200) !important;
            padding: var(--mobile-spacing-md) !important;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important;
        }

        @media (min-width: 768px) {
            .card-header {
                padding: var(--spacing-lg) !important;
            }
        }

        .card-title {
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: var(--gray-800) !important;
            margin: 0 !important;
            line-height: 1.3 !important;
        }

        @media (min-width: 768px) {
            .card-title {
                font-size: 1.1rem !important;
            }
        }

        .card-body {
            padding: var(--mobile-spacing-md) !important;
        }

        @media (min-width: 768px) {
            .card-body {
                padding: var(--spacing-lg) !important;
            }
        }

        .card-footer {
            background: var(--gray-50) !important;
            border-top: 1px solid var(--gray-200) !important;
            padding: var(--mobile-spacing-md) !important;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg) !important;
        }

        @media (min-width: 768px) {
            .card-footer {
                padding: var(--spacing-lg) !important;
            }
        }

        /* Buttons - Mobile Responsive */
        .btn {
            font-weight: 500 !important;
            border-radius: var(--radius-md) !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.8125rem !important;
            transition: all var(--transition-fast) !important;
            border-width: 1px !important;
            border-style: solid !important;
            line-height: 1.4 !important;
        }

        @media (min-width: 768px) {
            .btn {
                padding: 0.5rem 1rem !important;
                font-size: 0.875rem !important;
            }
        }

        @media (hover: hover) {
            .btn-primary:hover {
                background: var(--primary-dark) !important;
                border-color: var(--primary-dark) !important;
                color: var(--white) !important;
                transform: translateY(-1px) !important;
                box-shadow: var(--shadow-md) !important;
            }

            .btn-success:hover {
                background: #218838 !important;
                border-color: #1e7e34 !important;
                transform: translateY(-1px) !important;
            }
        }

        .btn-primary {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: var(--white) !important;
        }

        .btn-success {
            background: var(--success) !important;
            border-color: var(--success) !important;
            color: var(--white) !important;
        }

        .btn-warning {
            background: var(--warning) !important;
            border-color: var(--warning) !important;
            color: var(--gray-800) !important;
        }

        .btn-danger {
            background: var(--danger) !important;
            border-color: var(--danger) !important;
            color: var(--white) !important;
        }

        .btn-secondary {
            background: var(--secondary) !important;
            border-color: var(--secondary) !important;
            color: var(--white) !important;
        }

        .btn-outline-primary {
            background: transparent !important;
            border-color: var(--primary) !important;
            color: var(--primary) !important;
        }

        @media (hover: hover) {
            .btn-outline-primary:hover {
                background: var(--primary) !important;
                color: var(--white) !important;
            }
        }

        /* Tables - Mobile Responsive */
        .table {
            background: var(--white) !important;
            border-radius: var(--radius-lg) !important;
            overflow: hidden !important;
            font-size: 0.8125rem !important;
        }

        @media (min-width: 768px) {
            .table {
                font-size: 0.875rem !important;
            }
        }

        .table thead th {
            background: var(--gray-50) !important;
            border-bottom: 1px solid var(--gray-200) !important;
            font-weight: 600 !important;
            color: var(--gray-700) !important;
            padding: var(--mobile-spacing-sm) var(--mobile-spacing-xs) !important;
            font-size: 0.75rem !important;
        }

        @media (min-width: 768px) {
            .table thead th {
                padding: var(--spacing-md) !important;
                font-size: 0.875rem !important;
            }
        }

        .table tbody td {
            padding: var(--mobile-spacing-sm) var(--mobile-spacing-xs) !important;
            border-bottom: 1px solid var(--gray-200) !important;
            color: var(--gray-600) !important;
            font-size: 0.75rem !important;
            vertical-align: middle !important;
        }

        @media (min-width: 768px) {
            .table tbody td {
                padding: var(--spacing-md) !important;
                font-size: 0.875rem !important;
            }
        }

        @media (hover: hover) {
            .table tbody tr:hover {
                background: var(--gray-50) !important;
            }
        }

        /* Table Responsive Container */
        .table-responsive {
            border-radius: var(--radius-lg) !important;
            border: 1px solid var(--gray-200) !important;
        }

        /* Forms - Mobile Responsive */
        .form-control {
            border: 1px solid var(--gray-300) !important;
            border-radius: var(--radius-md) !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
            transition: border-color var(--transition-normal) !important;
            background: var(--white) !important;
        }

        @media (max-width: 575.98px) {
            .form-control {
                font-size: 16px !important; /* Prevent zoom on iOS */
            }
        }

        .form-control:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25) !important;
            outline: none !important;
        }

        .form-group label {
            font-weight: 500 !important;
            color: var(--gray-700) !important;
            margin-bottom: var(--mobile-spacing-xs) !important;
            font-size: 0.8125rem !important;
        }

        @media (min-width: 768px) {
            .form-group label {
                margin-bottom: var(--spacing-sm) !important;
                font-size: 0.875rem !important;
            }
        }

        /* Alerts - Mobile Responsive */
        .alert {
            border-radius: var(--radius-lg) !important;
            border-width: 1px !important;
            padding: var(--mobile-spacing-md) !important;
            margin-bottom: var(--mobile-spacing-lg) !important;
            font-size: 0.8125rem !important;
        }

        @media (min-width: 768px) {
            .alert {
                padding: var(--spacing-md) var(--spacing-lg) !important;
                margin-bottom: var(--spacing-lg) !important;
                font-size: 0.875rem !important;
            }
        }

        .alert-success {
            background: #d4edda !important;
            border-color: #c3e6cb !important;
            color: #155724 !important;
        }

        .alert-danger {
            background: #f8d7da !important;
            border-color: #f5c6cb !important;
            color: #721c24 !important;
        }

        .alert-warning {
            background: #fff3cd !important;
            border-color: #ffeaa7 !important;
            color: #856404 !important;
        }

        .alert-info {
            background: #d1ecf1 !important;
            border-color: #bee5eb !important;
            color: #0c5460 !important;
        }

        /* Notifications */
        div.fixed.inset-0.flex.items-end.justify-center {
            z-index: 999999 !important;
            padding: var(--mobile-spacing-md) !important;
        }

        @media (min-width: 768px) {
            div.fixed.inset-0.flex.items-end.justify-center {
                padding: var(--spacing-lg) !important;
            }
        }

        .notify {
            border-radius: var(--radius-lg) !important;
            box-shadow: var(--shadow-lg) !important;
            max-width: 90vw !important;
        }

        @media (min-width: 768px) {
            .notify {
                max-width: 400px !important;
            }
        }

        /* Loading States */
        .loading {
            opacity: 0.6 !important;
            pointer-events: none !important;
        }

        .loading::after {
            content: '' !important;
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            width: 20px !important;
            height: 20px !important;
            margin: -10px 0 0 -10px !important;
            border: 2px solid var(--gray-300) !important;
            border-radius: 50% !important;
            border-top-color: var(--primary) !important;
            animation: spin 1s ease-in-out infinite !important;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg) !important;
            }
        }

        /* Clean Scrollbars - Mobile Friendly */
        .content-wrapper::-webkit-scrollbar,
        .sidebar::-webkit-scrollbar {
            width: 4px !important;
        }

        @media (min-width: 768px) {
            .content-wrapper::-webkit-scrollbar,
            .sidebar::-webkit-scrollbar {
                width: 6px !important;
            }
        }

        .content-wrapper::-webkit-scrollbar-track,
        .sidebar::-webkit-scrollbar-track {
            background: var(--gray-100) !important;
        }

        .content-wrapper::-webkit-scrollbar-thumb,
        .sidebar::-webkit-scrollbar-thumb {
            background: var(--gray-300) !important;
            border-radius: 3px !important;
        }

        @media (hover: hover) {
            .content-wrapper::-webkit-scrollbar-thumb:hover,
            .sidebar::-webkit-scrollbar-thumb:hover {
                background: var(--gray-400) !important;
            }
        }

        .content-wrapper {
            scrollbar-width: thin !important;
            scrollbar-color: var(--gray-300) transparent !important;
        }

        .sidebar {
            scrollbar-width: thin !important;
            scrollbar-color: var(--gray-300) transparent !important;
        }

        /* Mobile-specific styles */
        @media (max-width: 575.98px) {
            .btn {
                padding: 0.375rem 0.625rem !important;
                font-size: 0.75rem !important;
            }
            
            .card-body {
                padding: var(--mobile-spacing-sm) !important;
            }
            
            .form-group {
                margin-bottom: var(--mobile-spacing-md) !important;
            }
        }

        /* Focus Styles for Accessibility - Touch Friendly */
        .btn:focus,
        .form-control:focus,
        .nav-link:focus {
            outline: 2px solid var(--primary) !important;
            outline-offset: 2px !important;
        }

        /* Print Styles */
        @media print {
            .main-sidebar,
            .main-header,
            .sidebar-overlay {
                display: none !important;
            }
            
            .content-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
                overflow: visible !important;
            }
        }

        /* Form handling - normal flow */
        .form-container,
        .card-form,
        .product-form {
            margin-bottom: var(--mobile-spacing-lg) !important;
        }

        @media (min-width: 768px) {
            .form-container,
            .card-form,
            .product-form {
                margin-bottom: var(--spacing-lg) !important;
            }
        }

        .content-wrapper .row:last-child,
        .content-wrapper .col:last-child > .card:last-child {
            margin-bottom: var(--mobile-spacing-lg) !important;
        }

        @media (min-width: 768px) {
            .content-wrapper .row:last-child,
            .content-wrapper .col:last-child > .card:last-child {
                margin-bottom: var(--spacing-lg) !important;
            }
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth !important;
        }

        /* Touch-friendly interaction zones */
        @media (max-width: 991.98px) {
            .btn,
            .form-control,
            .nav-link {
                min-height: 44px !important;
            }
        }

        /* Prevent horizontal scrolling on mobile */
        @media (max-width: 767.98px) {
            body {
                overflow-x: hidden !important;
            }
            
            .wrapper {
                overflow-x: hidden !important;
            }
        }

        /* Bootstrap row/col mobile fixes */
        @media (max-width: 575.98px) {
            .row {
                margin-left: -0.5rem !important;
                margin-right: -0.5rem !important;
            }
            
            .row > * {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Mobile Sidebar Overlay -->
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

        <!-- Navbar -->
        @include('layouts.admin.e-commerce.partials.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('layouts.admin.e-commerce.partials.aside')
        <!-- /.main sidebar container -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="/assets/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/assets/dist/js/adminlte.min.js"></script>

    <x:notify-messages />
    @notifyJs
    @stack('js')

    <!-- Enhanced JavaScript with Mobile Support -->
    <script>
        // Mobile sidebar toggle functionality
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }

        $(document).ready(function() {
            // Auto-hide notifications
            setTimeout(function() {
                $('.notify').fadeOut('slow');
            }, 3000);
            
            // Enhanced confirmation dialog for delete actions
            $(document).on('click', '#deleteData', function(e) {
                let id = $(this).data('id');
                e.preventDefault();
                
                if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    $(this).addClass('loading').prop('disabled', true);
                    document.getElementById('delete-data-form-' + id).submit();
                }
            });
            
            // Form submission loading states
            $('form').on('submit', function() {
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.addClass('loading').prop('disabled', true);
                
                setTimeout(function() {
                    submitBtn.removeClass('loading').prop('disabled', false);
                }, 10000);
            });
            
            // Enhanced table interactions
            $('.table tbody tr').on('click', function() {
                $(this).addClass('table-active').siblings().removeClass('table-active');
            });
            
            // Smooth scroll for anchor links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    $('.content-wrapper').animate({
                        scrollTop: target.offset().top - $('.content-wrapper').offset().top + $('.content-wrapper').scrollTop() - 100
                    }, 500);
                }
            });
            
            // Auto-resize textareas
            $('textarea').on('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            
            // Enhanced file input styling
            $('input[type="file"]').on('change', function() {
                const fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName || 'Choose file');
            });
            
            // Tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Auto-save form data with error handling
            $('form input, form textarea, form select').on('change', function() {
                try {
                    const formId = $(this).closest('form').attr('id');
                    if (formId) {
                        const formData = $(this).closest('form').serialize();
                        localStorage.setItem('form_' + formId, formData);
                    }
                } catch (e) {
                    console.log('LocalStorage not available');
                }
            });
            
            // Enhanced keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Ctrl+S to save form
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    const activeForm = $('form').first();
                    if (activeForm.length) {
                        activeForm.submit();
                    }
                }
                
                // Escape to close modals and mobile sidebar
                if (e.key === 'Escape') {
                    $('.modal').modal('hide');
                    if ($(window).width() < 992) {
                        $('body').removeClass('sidebar-open');
                    }
                }
            });
            
            // Handle scrollbar visibility
            let scrollTimeout;
            
            $('.content-wrapper').on('scroll', function() {
                $(this).addClass('scrolling');
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    $(this).removeClass('scrolling');
                }, 1000);
            });
            
            $('.sidebar').on('scroll', function() {
                $(this).addClass('scrolling');
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    $(this).removeClass('scrolling');
                }, 1000);
            });
            
            // Mobile sidebar toggle
            $('[data-widget="pushmenu"]').on('click', function() {
                if ($(window).width() < 992) {
                    $('body').toggleClass('sidebar-open');
                } else {
                    $('body').toggleClass('sidebar-collapse');
                }
            });
            
            // Close mobile sidebar when clicking outside
            $(document).on('click', function(e) {
                if ($(window).width() < 992 && !$(e.target).closest('.main-sidebar, [data-widget="pushmenu"]').length) {
                    $('body').removeClass('sidebar-open');
                }
            });
            
            // Touch events for mobile swipe gestures
            let startX = null;
            let startY = null;
            
            $(document).on('touchstart', function(e) {
                startX = e.originalEvent.touches[0].clientX;
                startY = e.originalEvent.touches[0].clientY;
            });
            
            $(document).on('touchmove', function(e) {
                if (!startX || !startY) return;
                
                const currentX = e.originalEvent.touches[0].clientX;
                const currentY = e.originalEvent.touches[0].clientY;
                const diffX = startX - currentX;
                const diffY = startY - currentY;
                
                if (Math.abs(diffX) > Math.abs(diffY)) {
                    // Horizontal swipe
                    if (diffX > 50 && $('body').hasClass('sidebar-open')) {
                        // Swipe left to close sidebar
                        $('body').removeClass('sidebar-open');
                    } else if (diffX < -50 && !$('body').hasClass('sidebar-open') && $(window).width() < 992) {
                        // Swipe right to open sidebar (only on mobile)
                        $('body').addClass('sidebar-open');
                    }
                }
                
                startX = null;
                startY = null;
            });
            
            // Responsive table handling
            function handleResponsiveTables() {
                $('.table').each(function() {
                    if (!$(this).closest('.table-responsive').length) {
                        $(this).wrap('<div class="table-responsive"></div>');
                    }
                });
            }
            
            handleResponsiveTables();
            
            // Enhanced performance with Intersection Observer
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });
                
                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }

            // Debounce function
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
            
            // Window resize handler
            $(window).on('resize', debounce(function() {
                // Close mobile sidebar on desktop resize
                if ($(window).width() >= 992) {
                    $('body').removeClass('sidebar-open');
                }
                
                // Re-handle responsive tables
                handleResponsiveTables();
            }, 250));
            
            // Prevent zoom on iOS double tap
            let lastTouchEnd = 0;
            $(document).on('touchend', function(e) {
                const now = (new Date()).getTime();
                if (now - lastTouchEnd <= 300) {
                    e.preventDefault();
                }
                lastTouchEnd = now;
            });
            
            // Mobile-friendly dropdown handling
            if ($(window).width() < 768) {
                $('.dropdown-toggle').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const $dropdown = $(this).next('.dropdown-menu');
                    $('.dropdown-menu').not($dropdown).hide();
                    $dropdown.toggle();
                });
                
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.dropdown').length) {
                        $('.dropdown-menu').hide();
                    }
                });
            }
        });
        
        // Enhanced global error handler
        window.addEventListener('error', function(e) {
            console.error('Global error:', e.error);
        });

        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
        });
        
        // Service worker registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful');
                }).catch(function(err) {
                    console.log('ServiceWorker registration failed:', err);
                });
            });
        }
        
        // Prevent horizontal scroll on mobile
        $(document).ready(function() {
            if ($(window).width() < 768) {
                $('body').css('overflow-x', 'hidden');
            }
        });
    </script>
    <script src="/assets/dist/js/demo.js"></script>
</body>

</html>