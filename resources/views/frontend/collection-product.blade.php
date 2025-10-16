@extends('layouts.frontend.app')

@push('meta')
<meta name='description' content="Collection Products"/>
<meta name='keywords' content="@foreach($products as $product){{$product->title.', '}}@endforeach" />
@endpush

@section('title', 'Collection Products')

@push('css')
    <link rel="stylesheet" href="{{asset('/')}}assets/frontend/css/jquery-ui1.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            box-sizing: border-box;
        }
        
        .product-page {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }
        
        .page-subtitle {
            color: #64748b;
            font-size: 1.1rem;
            font-weight: 400;
        }
        
        .main-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        @media (max-width: 768px) {
            .main-container {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .page-title {
                font-size: 2rem;
            }
        }
        
        .sidebar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.2);
            height: fit-content;
            position: sticky;
            top: 2rem;
            transition: all 0.3s ease;
        }
        
        .sidebar:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
        
        .products-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .filter-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(226, 232, 240, 0.5);
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        @media (max-width: 640px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1rem;
            }
        }
        
        .product {
            background: white;
            border-radius: 12px;
            padding: 0;
            margin: 0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(226, 232, 240, 0.5);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }
        
        .product::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .product:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .product:hover::before {
            transform: scaleX(1);
        }
        
        /* Rating Stars Modern Design */
        .rating {
            display: inline-flex;
            margin-top: -5px;
            flex-direction: row-reverse;
            gap: 2px;
        }
        
        .rating > input {
            display: none;
        }
        
        .rating > label {
            position: relative;
            width: 24px;
            height: 24px;
            font-size: 20px;
            color: #e2e8f0;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .rating > label::before {
            content: "★";
            position: absolute;
            color: #fbbf24;
            opacity: 0;
            transition: all 0.2s ease;
            transform: scale(0.8);
        }
        
        .rating > label:hover::before,
        .rating > label:hover ~ label::before {
            opacity: 1;
            transform: scale(1);
        }
        
        .rating > input:checked ~ label::before {
            opacity: 1;
            transform: scale(1);
        }
        
        .rating:hover > input:checked ~ label::before {
            opacity: 0.6;
        }
        
        /* Loading Animation */
        .ajax-loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f3f4f6;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* View Toggle Buttons */
        .view-toggle {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .view-btn {
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .view-btn:hover {
            background: #f8fafc;
            border-color: #667eea;
        }
        
        .view-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }
        
        /* Quantity Controls */
        .quantity-controls {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border-radius: 8px;
            padding: 0.25rem;
            border: 1px solid #e2e8f0;
        }
        
        .value-plus, .value-minus {
            background: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
            color: #667eea;
        }
        
        .value-plus:hover, .value-minus:hover {
            background: #667eea;
            color: white;
            transform: scale(1.05);
        }
        
        .value {
            border: none;
            background: transparent;
            text-align: center;
            width: 60px;
            font-weight: 600;
            color: #374151;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #64748b;
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        /* Fade in animation for products */
        .product {
            animation: fadeInUp 0.6s ease forwards;
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
        
        /* Responsive improvements */
        @media (max-width: 1024px) {
            .main-container {
                grid-template-columns: 280px 1fr;
                gap: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .product-page {
                padding: 1rem 0;
            }
            
            .sidebar {
                position: relative;
                top: 0;
                order: 2;
            }
            
            .products-container {
                order: 1;
                padding: 1.5rem;
            }
        }
        
        /* Enhanced focus states for accessibility */
        .rating > label:focus-visible,
        .view-btn:focus-visible,
        .value-plus:focus-visible,
        .value-minus:focus-visible {
            outline: 2px solid #667eea;
            outline-offset: 2px;
        }
    </style>
@endpush

@section('content')
<!--================Modern Product Collection Area=================-->
<div class="product-page">
    <div class="page-header">
        <h1 class="page-title">Customer Favorites</h1>
        <p class="page-subtitle">Discover our most loved products, handpicked by our community</p>
    </div>
    
    <div class="main-container">
        <!-- Enhanced Sidebar -->
        <aside class="sidebar">
            <div class="menu-overly2"></div>
            <x-filter-search-component name="collection" :value="$collection->slug" />
        </aside>
        
        <!-- Modern Products Container -->
        <main class="products-container">
            <div class="filter-header">
                <x-filter-component />
            </div>
            
            <!-- Grid View -->
            <div class="products-grid" id="grid-view">
                @forelse ($products as $product)
                    <x-product-grid-view :product="$product" />
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">🛍️</div>
                        <x-product-empty-component />
                    </div>
                @endforelse
            </div>
            
            <!-- List View -->
            <div class="products-list" id="list-view" style="display: none;">
                @forelse ($products as $product)
                    <x-product-list-view :product="$product" />
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">🛍️</div>
                        <x-product-empty-component />
                    </div>
                @endforelse
            </div>
            
            <!-- Enhanced Loading Indicator -->
            <div class="ajax-loading" style="display: none;">
                <div class="loading-spinner"></div>
            </div>
        </main>
    </div>
</div>

<!-- Keep existing modals -->
<x-add-cart-modal />
@include('components.cart-modal-attri')
@endsection

@push('js')
    <script src="{{asset('/')}}assets/frontend/js/jquery-ui.js"></script>

    <script>
        $(document).ready(function () {
            // Enhanced quantity controls with animations
            $('.value-plus').on('click', function () {
                var divUpd = $(this).parent().find('.value'),
                newVal = parseInt(divUpd.val(), 10) + 1;
                
                // Add smooth transition
                divUpd.css('transform', 'scale(1.1)');
                setTimeout(() => {
                    divUpd.css('transform', 'scale(1)');
                }, 150);
                
                divUpd.val(newVal);
                $('input#qty').val(newVal);
            });

            $('.value-minus').on('click', function () {
                var divUpd = $(this).parent().find('.value'),
                newVal = parseInt(divUpd.val(), 10) - 1;
                if (newVal >= 1) {
                    // Add smooth transition
                    divUpd.css('transform', 'scale(1.1)');
                    setTimeout(() => {
                        divUpd.css('transform', 'scale(1)');
                    }, 150);
                    
                    divUpd.val(newVal);
                    $('input#qty').val(newVal);
                }
            });

            // Enhanced AJAX cart functionality with better UX
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
                        $(btn).html('<div class="loading-spinner" style="width: 20px; height: 20px; margin: 0 auto;"></div>');
                    },
                    success: function (response) {
                        if(response.alert!='Congratulations'){
                            $.toast({
                                heading: 'Warning',
                                text: response.message,
                                icon: 'warning',
                                position: 'top-right',
                                stack: false,
                                bgColor: '#f59e0b',
                                textColor: 'white'
                            });
                        } else {
                            $('span#total-cart-amount').text(response.subtotal);

                            $.toast({
                                heading: 'Success!',
                                text: response.message,
                                icon: 'success',
                                position: 'top-right',
                                stack: false,
                                bgColor: '#10b981',
                                textColor: 'white'
                            });

                            $('#cart-modal').modal('hide');
                        }
                    },
                    complete: function() {
                        $(btn).attr('disabled', false);
                        $(btn).html('Add to Cart');
                    },
                    error: function(xhr) {
                        $.toast({
                            heading: 'Error ' + xhr.status,
                            text: xhr.responseJSON?.message || 'Something went wrong',
                            icon: 'error',
                            position: 'top-right',
                            stack: false,
                            bgColor: '#ef4444',
                            textColor: 'white'
                        });
                    }
                });
            });

            // Enhanced sorting with smooth transitions
            $(document).on('change', 'select#sort', function() {
                let value = $(this).val();
                
                // Add loading state
                $('.products-grid, .products-list').css('opacity', '0.6');
                
                $('input[name="sort"]').val(value);
                $('form#form').submit();
            });

            // Enhanced price slider with modern styling
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
            
            // Add stagger animation to products
            $('.product').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });
        });
    </script>
    
    <script>
        var site_url = "{{ url('/') }}";   
        var page = 1;
        
        load_more(page);

        function load_more(page){
            var slug = '{!! $collection->slug !!}';
            var _totalCurrentResult = $(".product").length;
            
            $.ajax({
                url: site_url + "/collection/"+slug+"?page=" + page,
                type: "get",
                datatype: "html",
                data: {
                    skip: _totalCurrentResult
                },
                beforeSend: function() {
                    $('.ajax-loading').fadeIn(300);
                },
                success: function(response) {
                    var result = $.parseJSON(response);
                    $('.ajax-loading').fadeOut(300);
                    
                    if(result[0].length > 0) {
                        // Add stagger animation to new products
                        var $newGridItems = $(result[0]);
                        var $newListItems = $(result[1]);
                        
                        $newGridItems.css('opacity', '0');
                        $newListItems.css('opacity', '0');
                        
                        $("#grid-view").append($newGridItems);
                        $("#list-view").append($newListItems);
                        
                        // Animate in new items
                        $newGridItems.each(function(index) {
                            $(this).delay(index * 100).animate({opacity: 1}, 300);
                        });
                        
                        $newListItems.each(function(index) {
                            $(this).delay(index * 100).animate({opacity: 1}, 300);
                        });
                        
                        setTimeout(function() {
                            page++;
                            load_more(page);
                        }, 3000);
                    }
                },
                error: function() {
                    $('.ajax-loading').fadeOut(300);
                }
            });
        }
    </script>
@endpush