@extends('layouts.frontend.app')

@push('meta')
<meta name='description' content="Category Products"/>
<meta name='keywords' content="@foreach(\App\Models\Tag::all() as $tag){{$tag->name.', '}}@endforeach" />
@endpush

@section('title', 'Category Products')

@push('css')
    <link rel="stylesheet" href="{{asset('/')}}assets/frontend/css/jquery-ui1.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        /* Modern Container Styles */
        .modern-product-page {
            padding: 40px 0;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Page Title */
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 40px;
            text-align: center;
            letter-spacing: -0.5px;
        }
        
        /* Sidebar Styles */
        .modern-sidebar {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            position: sticky;
            top: 20px;
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }
        
        .modern-sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .modern-sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .modern-sidebar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        .modern-sidebar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Products Container */
        .modern-products-container {
            background: transparent;
        }
        
        /* Subcategory Cards - Modern Design */
        .subcategories-section {
            background: white;
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 24px;
            letter-spacing: -0.3px;
        }
        
        .subcategory-card {
            display: block;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
        }
        
        .subcategory-card:hover {
            transform: translateY(-8px);
        }
        
        .subcategory-image-wrapper {
            overflow: hidden;
            border-radius: 12px;
            background: #f8f9fa;
            position: relative;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .subcategory-card:hover .subcategory-image-wrapper {
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        
        .subcategory-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .subcategory-card:hover .subcategory-image {
            transform: scale(1.08);
        }
        
        .subcategory-name {
            margin-top: 16px;
        }
        
        .subcategory-name h5 {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
            text-align: center;
            transition: color 0.3s ease;
        }
        
        .subcategory-card:hover .subcategory-name h5 {
            color: #2563eb;
        }
        
        /* Products Grid */
        .products-grid-wrapper {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        
        .product-page .products .product {
            margin: 0 !important;
            box-shadow: none;
            border: none;
            margin-bottom: 16px !important;
            transition: transform 0.3s ease;
        }
        
        .product-page .products .product:hover {
            transform: translateY(-4px);
        }
        
        /* Divider */
        .modern-divider {
            border: none;
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
            margin: 32px 0;
        }
        
        /* Rating Stars */
        .rating {
            display: inline-flex;
            margin-top: -10px;
            flex-direction: row-reverse;
        }
        
        .rating > input {
            display: none;
        }
        
        .rating > label {
            position: relative;
            width: 28px;
            font-size: 35px;
            color: #fbbf24;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .rating > label::before {
            content: "\2605";
            position: absolute;
            opacity: 0;
        }
        
        .rating > label:hover:before,
        .rating > label:hover ~ label:before {
            opacity: 1 !important;
        }
        
        .rating > input:checked ~ label:before {
            opacity: 1;
        }
        
        .rating:hover > input:checked ~ label:before {
            opacity: 0.4;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .modern-product-page {
                padding: 20px 0;
            }
            
            .page-title {
                font-size: 1.75rem;
                margin-bottom: 24px;
            }
            
            .modern-sidebar {
                margin-bottom: 24px;
                position: relative;
                top: 0;
                max-height: none;
            }
            
            .subcategory-image {
                height: 180px;
            }
            
            .subcategories-section {
                padding: 20px;
            }
            
            .products-grid-wrapper {
                padding: 16px;
            }
        }
        
        @media (max-width: 576px) {
            .subcategory-image {
                height: 150px;
            }
            
            .subcategory-name h5 {
                font-size: 14px;
            }
        }
        
        /* Loading Animation */
        .ajax-loading {
            text-align: center;
            padding: 20px;
        }
        
        /* Empty State */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }
        
        /* Filter Toggle Button for Mobile */
        .filter-toggle-btn {
            display: none;
            width: 100%;
            padding: 12px 24px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-toggle-btn:hover {
            background: #f8f9fa;
            border-color: #2563eb;
            color: #2563eb;
        }
        
        @media (max-width: 768px) {
            .filter-toggle-btn {
                display: block;
            }
            
            .modern-sidebar {
                display: none;
            }
            
            .modern-sidebar.active {
                display: block;
            }
        }
    </style>
@endpush

@section('content')
<div class="container modern-product-page product-page">
    <h1 class="page-title">Customer Favorites</h1>
    
    <div class="row">
        <div class="menu-overly2"></div>
        
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4">
            <button class="filter-toggle-btn" onclick="toggleFilters()">
                <i class="fas fa-filter"></i> Filters & Sorting
            </button>
            <div class="modern-sidebar side-bar" id="filterSidebar">
                <x-filter-search-component name="category" :value="$slug" />
            </div>
        </div>
        
        <!-- Products Area -->
        <div class="col-lg-9 col-md-8">
            <div class="modern-products-container products">
                
                {{-- Subcategories Section --}}
                @if(isset($subCategories) && $subCategories->count() > 0)
                <div class="subcategories-section">
                    <h2 class="section-title">Shop by Subcategory</h2>
                    <div class="row g-4">
                        @foreach($subCategories as $subCategory)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('subCategory.product', $subCategory->slug) }}" class="subcategory-card">
                                <div class="subcategory-image-wrapper">
                                    <img src="{{ asset('uploads/sub category/' . $subCategory->cover_photo) }}" 
                                         alt="{{ $subCategory->name }}"
                                         class="subcategory-image"
                                         onerror="this.src='{{ asset('uploads/sub category/default.png') }}'">
                                </div>
                                <div class="subcategory-name">
                                    <h5>{{ $subCategory->name }}</h5>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                {{-- Products Grid --}}
                <div class="products-grid-wrapper">
                    <div class="row mb-3">
                        <x-filter-component />
                    </div>
                    
                    <div class="row g-3" id="grid-view">
                        @forelse ($category->products as $product)
                            <x-product-grid-view :product="$product" />
                        @empty
                            <x-product-empty-component />
                        @endforelse
                    </div>
                    
                    <div class="row g-3" id="list-view" style="display: none;">
                        @forelse ($category->products as $product)
                            <x-product-list-view :product="$product" />
                        @empty
                            <x-product-empty-component />
                        @endforelse
                    </div>
                    
                    <div class="ajax-loading" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<x-add-cart-modal />
@include('components.cart-modal-attri')
@endsection

@push('js')
    <script src="{{asset('/')}}assets/frontend/js/jquery-ui.js"></script>

    <script>
        // Toggle filters on mobile
        function toggleFilters() {
            $('#filterSidebar').toggleClass('active');
        }

        $(document).ready(function () {
            // Quantity controls
            $('.value-plus').on('click', function () {
                var divUpd = $(this).parent().find('.value'),
                newVal = parseInt(divUpd.val(), 10) + 1;
                divUpd.val(newVal);
                $('input#qty').val(newVal);
            });

            $('.value-minus').on('click', function () {
                var divUpd = $(this).parent().find('.value'),
                newVal = parseInt(divUpd.val(), 10) - 1;
                if (newVal >= 1) {
                    divUpd.val(newVal);
                    $('input#qty').val(newVal);
                }
            });

            // Add to cart
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
                    },
                    success: function (response) {
                        if(response.alert!='Congratulations'){
                            $.toast({
                                heading: 'Warning',
                                text: response.message,
                                icon: 'warning',
                                position: 'top-right',
                                stack: false
                            });
                        }else{
                            $('span#total-cart-amount').text(response.subtotal);
                            $.toast({
                                heading: 'Congratulations',
                                text: response.message,
                                icon: 'success',
                                position: 'top-right',
                                stack: false
                            });
                            $('#cart-modal').modal('hide');
                        }
                    },
                    complete: function() {
                        $(btn).attr('disabled', false);
                    },
                    error: function(xhr) {
                        $.toast({
                            heading: xhr.status,
                            text: xhr.responseJSON.message,
                            icon: 'error',
                            position: 'top-right',
                            stack: false
                        });
                    }
                });
            });

            // Sort change
            $(document).on('change', 'select#sort', function() {
                let value = $(this).val();
                $('input[name="sort"]').val(value);
                $('form#form').submit();
            });

            // Price range slider
            $("#slider-range").slider({
                range: true,
                min: 0,
                max: 9000,
                values: [50, 6000],
                slide: function (event, ui) {
                    $("#amount").val("{!! setting('CURRENCY_CODE_MIN') ?? 'TK' !!}" + ui.values[0] + " - " + "{!! setting('CURRENCY_CODE_MIN') ?? 'TK' !!}" + ui.values[1]);
                }
            });
            $("#amount").val("{!! setting('CURRENCY_CODE_MIN') ?? 'TK' !!}" + $("#slider-range").slider("values", 0) + " - " + "{!! setting('CURRENCY_CODE_MIN') ?? 'TK' !!}" + $("#slider-range").slider("values", 1));
        });
    </script>
    
    <script>
        var site_url = "{{ url('/') }}";   
        var page = 1;
        
        load_more(page);

        function load_more(page){
            var slug = '{!! $slug !!}';
            var _totalCurrentResult = $(".product").length;
            
            $.ajax({
                url: site_url + "/category/"+slug+"?page=" + page,
                type: "get",
                datatype: "html",
                data: {
                    skip: _totalCurrentResult
                },
                beforeSend: function() {
                    $('.ajax-loading').show();
                },
                success: function(response) {
                    var result = $.parseJSON(response);
                    $('.ajax-loading').hide();
                    $("#grid-view").append(result[0]);
                    $("#list-view").append(result[1]);
                    
                    if(result[0].length == 0){
                        // No more results
                    } else {
                        setTimeout(function() {
                            page++;
                            load_more(page);
                        }, 3000);
                    }
                }
            });
        }
    </script>
@endpush