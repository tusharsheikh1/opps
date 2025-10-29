<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" type="image/jpg" href="/uploads/setting/{{setting('favicon')}}" />

    <title>@yield('title')</title>
    @include('layouts.global')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/assets/plugins/fontawesome-free/css/all.min.css">

    <link rel="stylesheet" href="/assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">

    @notifyCss
    @stack('css')

    <link rel="stylesheet" href="/assets/dist/css/adminlte.min.css">
    
    {{-- Custom CSS for a modern look --}}
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        /* Modern Navbar */
        .main-header {
            border-bottom: 0;
            box-shadow: 0 1px 15px rgba(0, 0, 0, 0.04), 0 1px 6px rgba(0, 0, 0, 0.04);
        }
        
        /* Softer "Visit Site" button */
        .visit-site-btn {
            border-radius: 6px;
            font-weight: 500;
        }
        
        /* Modern Sidebar */
        .brand-link {
            border-bottom: 0 !important;
        }
        .nav-sidebar .nav-header {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6c757d;
            padding: 0.5rem 1rem 0.5rem 1.1rem;
        }
        .main-sidebar, .main-sidebar::before {
            width: 260px; /* Slightly wider sidebar */
        }
        .sidebar-light-primary .nav-sidebar > .nav-item > .nav-link.active {
            background-color: #f0f5ff;
            color: #007bff;
            border-radius: 6px;
            font-weight: 500;
        }
        .nav-sidebar .nav-item > .nav-link {
            border-radius: 6px;
            margin: 0.1rem 0.5rem;
            padding: 0.6rem 1rem;
        }
        .nav-sidebar .nav-item > .nav-treeview {
            padding-left: 1.2rem;
        }
        .nav-pills .nav-link:not(.active):hover {
            background-color: #f8f9fa;
        }

        /* Modern User Menu Avatar */
        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #007bff;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            margin-right: 8px;
            margin-top: -5px;
        }
        .navbar-nav .user-avatar {
             margin-right: 0;
             width: 28px;
             height: 28px;
             font-size: 0.8rem;
        }
        .dropdown-menu .user-avatar {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
            margin-right: 10px;
        }

        /* Form submission loading spinner */
        .btn.loading {
            position: relative;
            color: transparent !important;
            pointer-events: none;
        }
        .btn.loading::after {
            content: '';
            display: block;
            position: absolute;
            top: 50%;
            left: 50%;
            width: 1.2em;
            height: 1.2em;
            margin-top: -0.6em;
            margin-left: -0.6em;
            border: 2px solid #fff;
            border-bottom-color: transparent;
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
        }
        /* Darker spinner for light buttons (e.g., outline) */
        .btn-outline-primary.loading::after,
        .btn-outline-secondary.loading::after,
        .btn-light.loading::after {
             border-color: #007bff;
             border-bottom-color: transparent;
        }

    </style>
    
</head>

<body class="hold-transition sidebar-mini layout-fixed"> 
    <div class="wrapper">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

        @include('layouts.admin.e-commerce.partials.navbar')
        @include('layouts.admin.e-commerce.partials.aside')
        <div class="content-wrapper">
            @yield('content')
        </div>
        <aside class="control-sidebar control-sidebar-dark">
        </aside>
    </div>
    <script src="/assets/plugins/jquery/jquery.min.js"></script>
    <script src="/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script src="/assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    
    <script src="/assets/dist/js/adminlte.min.js"></script>

    <x:notify-messages />
    @notifyJs
    @stack('js')

    <script>
        // Mobile sidebar toggle functionality
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
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
        
        // Wrap tables in responsive containers
        function handleResponsiveTables() {
            $('.table').each(function() {
                if (!$(this).closest('.table-responsive').length) {
                    $(this).wrap('<div class="table-responsive"></div>');
                }
            });
        }

        $(document).ready(function() {
            
            // 💡 **UPDATED FIX:** Centralized sidebar click handler.
            // This now only closes the mobile sidebar if the clicked link
            // is NOT a dropdown menu (i.e., it has no `.nav-treeview` inside its parent `.nav-item`).
            $(document).on('click', '.sidebar .nav-link', function() {
                // Check if the screen is mobile-sized (less than 992px)
                if (window.innerWidth < 992) {
                    const $navItem = $(this).closest('.nav-item');
                    
                    // If the parent nav-item does *not* contain a .nav-treeview (i.e., it's a final page link),
                    // then we close the sidebar.
                    if ($navItem.find('.nav-treeview').length === 0) {
                        document.body.classList.remove('sidebar-open');
                    }
                    // If it is a dropdown (like 'Orders'), we do nothing, allowing the
                    // AdminLTE treeview plugin to open the menu instantly.
                }
            });

            // Auto-hide notifications
            setTimeout(function() {
                $('.notify').fadeOut('slow');
            }, 3000);
            
            // Confirmation dialog for delete actions
            $(document).on('click', '#deleteData', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                
                if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    $(this).addClass('loading').prop('disabled', true);
                    $('#delete-data-form-' + id).submit();
                }
            });
            
            // UPDATED: Form submission loading states (with spinner)
            $('form').on('submit', function() {
                const form = $(this);
                // Check if form is for deletion (already handled)
                if (form.attr('id') && form.attr('id').startsWith('delete-data-form-')) {
                    return; 
                }
                
                const submitBtn = form.find('button[type="submit"]');
                submitBtn.addClass('loading').prop('disabled', true);
                
                // Failsafe to re-enable button after 10s
                setTimeout(function() {
                    if (submitBtn.hasClass('loading')) {
                        submitBtn.removeClass('loading').prop('disabled', false);
                    }
                }, 10000);
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
            
            // File input styling
            $('input[type="file"]').on('change', function() {
                const fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName || 'Choose file');
            });
            
            // Initialize Tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Handle responsive tables on load
            handleResponsiveTables();
            
            // Window resize handler
            $(window).on('resize', debounce(function() {
                if ($(window).width() >= 992) {
                    $('body').removeClass('sidebar-open');
                }
                handleResponsiveTables();
            }, 250));

            // Close mobile sidebar with Escape key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $(window).width() < 992 && $('body').hasClass('sidebar-open')) {
                    $('body').removeClass('sidebar-open');
                }
            });
        });
    </script>
    <script src="/assets/dist/js/demo.js"></script>
</body>

</html>