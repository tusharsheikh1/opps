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

    // Header shadow on scroll
    $(window).on('scroll', function() {
        $header.toggleClass('scrolled', $(this).scrollTop() > 20);
    });

    // Mobile menu functions
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

    // Mobile search functions
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
        if ($mobileSearchContainer.hasClass('active')) {
            closeMobileSearch();
        } else {
            openMobileSearch();
        }
    });

    // Click outside to close
    $(document).on('click', function(e) {
        if ($mobileMenuPanel.hasClass('active') && !$(e.target).closest('.mobile-menu-panel, .mobile-menu-toggle').length) {
            closeMobileMenu();
        }
        if ($mobileSearchContainer.hasClass('active') && !$(e.target).closest('.mobile-search-bar-container, #mobile-search-trigger').length) {
            closeMobileSearch();
        }
        // Close any open desktop dropdown when clicking outside
        if (!$(e.target).closest('.has-dropdown').length) {
            closeAllDropdowns();
        }
    });

    // Escape key closes overlays
    $(document).on('keyup', function(e) {
        if (e.key === "Escape") {
            closeMobileMenu();
            closeMobileSearch();
            closeAllDropdowns();
        }
    });

    // ===================================
    // MOBILE MENU EXPANDABLE CATEGORIES
    // ===================================
    
    // Toggle main categories
    $('.mobile-category-item').on('click', function(e) {
        e.preventDefault();
        const $this = $(this);
        const categoryId = $this.data('category-id');
        const $subcategoryList = $('#subcategory-' + categoryId);
        
        // Toggle active state
        $this.toggleClass('active');
        $subcategoryList.toggleClass('active');
        
        // Close other categories
        $('.mobile-category-item').not($this).removeClass('active');
        $('.mobile-subcategory-list').not($subcategoryList).removeClass('active');
        
        // Close all nested items when collapsing
        if (!$subcategoryList.hasClass('active')) {
            $subcategoryList.find('.mobile-subcategory-item').removeClass('active');
            $subcategoryList.find('.mobile-mini-category-list').removeClass('active');
            $subcategoryList.find('.mobile-mini-category-item').removeClass('active');
            $subcategoryList.find('.mobile-extra-category-list').removeClass('active');
        }
    });
    
    // Toggle subcategories
    $('.mobile-subcategory-item').on('click', function(e) {
        // Only prevent default if it's a button (has expand icon)
        if ($(this).find('.expand-icon').length > 0) {
            e.preventDefault();
            const $this = $(this);
            const subcategoryId = $this.data('subcategory-id');
            const $miniList = $('#minicategory-' + subcategoryId);
            
            // Toggle active state
            $this.toggleClass('active');
            $miniList.toggleClass('active');
            
            // Close other subcategories at same level
            $this.parent().find('.mobile-subcategory-item').not($this).removeClass('active');
            $this.parent().find('.mobile-mini-category-list').not($miniList).removeClass('active');
            
            // Close all nested items when collapsing
            if (!$miniList.hasClass('active')) {
                $miniList.find('.mobile-mini-category-item').removeClass('active');
                $miniList.find('.mobile-extra-category-list').removeClass('active');
            }
        }
    });
    
    // Toggle mini categories
    $('.mobile-mini-category-item').on('click', function(e) {
        // Only prevent default if it's a button (has expand icon)
        if ($(this).find('.expand-icon').length > 0) {
            e.preventDefault();
            const $this = $(this);
            const miniId = $this.data('mini-id');
            const $extraList = $('#extracategory-' + miniId);
            
            // Toggle active state
            $this.toggleClass('active');
            $extraList.toggleClass('active');
            
            // Close other mini categories at same level
            $this.parent().find('.mobile-mini-category-item').not($this).removeClass('active');
            $this.parent().find('.mobile-extra-category-list').not($extraList).removeClass('active');
        }
    });

    // AJAX cart count update
    $(document).ajaxSuccess(function(event, xhr, settings) {
        if (xhr.responseJSON && typeof xhr.responseJSON.count !== 'undefined') {
            let cartCount = xhr.responseJSON.count;
            $('.cart-count-badge').text(cartCount);
        }
    });

    // ============================================
    // IMPROVED DESKTOP DROPDOWN: One at a time
    // ============================================
    
    let currentOpenDropdown = null;
    let openTimer = null;
    let closeTimer = null;

    function closeAllDropdowns() {
        $navItems.removeClass('active').attr('aria-expanded', 'false');
        currentOpenDropdown = null;
    }

    function openDropdown($item) {
        // Close any currently open dropdown first
        if (currentOpenDropdown && !currentOpenDropdown.is($item)) {
            currentOpenDropdown.removeClass('active').attr('aria-expanded', 'false');
        }
        
        // Open the new dropdown
        $item.addClass('active').attr('aria-expanded', 'true');
        currentOpenDropdown = $item;
    }

    function closeDropdown($item) {
        $item.removeClass('active').attr('aria-expanded', 'false');
        if (currentOpenDropdown && currentOpenDropdown.is($item)) {
            currentOpenDropdown = null;
        }
    }

    // Mouse events with small delays to prevent flickering
    $navItems.on('mouseenter', function() {
        const $this = $(this);
        clearTimeout(closeTimer);
        clearTimeout(openTimer);
        
        openTimer = setTimeout(() => {
            openDropdown($this);
        }, 150);
    });

    $navItems.on('mouseleave', function() {
        const $this = $(this);
        clearTimeout(openTimer);
        clearTimeout(closeTimer);
        
        closeTimer = setTimeout(() => {
            closeDropdown($this);
        }, 200);
    });

    // Click events - toggle behavior
    $navItems.find('> a').on('click', function(e) {
        const $parent = $(this).parent();
        
        // If clicking on a link that has a dropdown
        if ($parent.hasClass('has-dropdown')) {
            e.preventDefault();
            
            if ($parent.hasClass('active')) {
                closeDropdown($parent);
            } else {
                openDropdown($parent);
            }
        }
    });

    // Keyboard accessibility
    $navItems.on('keydown', function(e) {
        const $this = $(this);
        
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            if ($this.hasClass('active')) {
                closeDropdown($this);
            } else {
                openDropdown($this);
            }
        }
        
        // Arrow key navigation between top-level items
        if (e.key === 'ArrowRight') {
            e.preventDefault();
            const $next = $this.next('li');
            if ($next.length) {
                closeDropdown($this);
                $next.find('> a').focus();
            }
        } else if (e.key === 'ArrowLeft') {
            e.preventDefault();
            const $prev = $this.prev('li');
            if ($prev.length) {
                closeDropdown($this);
                $prev.find('> a').focus();
            }
        }
    });

    // Focus management within dropdown
    $navItems.on('focusin', function() {
        const $this = $(this);
        // Only open if focus is on the parent link, not when tabbing through dropdown items
        if ($(document.activeElement).is($this.find('> a'))) {
            openDropdown($this);
        }
    });

    $navItems.on('focusout', function(e) {
        const $this = $(this);
        // Close dropdown if focus moves completely outside
        setTimeout(() => {
            if (!$this.find(':focus').length && !$this.is(':focus')) {
                closeDropdown($this);
            }
        }, 100);
    });

    // Original AJAX category loading
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
        error: function() { 
            $('.ajax-loading').hide(); 
        }
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
            error: function() { 
                $('.ajax-loading').hide(); 
            }
        });
    }

    // Modal behavior on load
    $(window).on('load', function () {
        if (typeof $('#myModal').modal === 'function') {
            $('#myModal').modal('show');
        }
    });
});
</script>
@endpush