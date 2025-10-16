@extends('layouts.frontend.app')

@push('meta')
<meta property="og:image" content="{{asset('uploads/setting/'.setting('auth_logo'))}}" />
@endpush

@section('content')

@if (setting('TOP_CAT_STATUS') != 0 || setting('TOP_CAT_STATUS') == "")
<!--================ top category Area =================-->
<section class="shop-category modern-categories" style="padding: 40px 0;">
    <div class="container">
        <div class="cat-row">
            @foreach ($categories_f as $category)
            <a href="{{route('category.product',$category->slug)}}" class="cat-item">
                <div class="cat-content">
                    <div class="thumbnail">
                        <img src="{{asset('uploads/category/'.$category->cover_photo)}}" alt="{{$category->name}}" loading="lazy">
                        <div class="overlay"></div>
                    </div>
                    <h3>{{$category->name}}</h3>
                </div>
            </a>
            @endforeach
            
            @foreach ($mini_f as $category)
            <a href="{{route('miniCategory.product',$category->slug)}}" class="cat-item">
                <div class="cat-content">
                    <div class="thumbnail">
                        <img src="{{asset('uploads/mini-category/'.$category->cover_photo)}}" alt="{{$category->name}}" loading="lazy">
                        <div class="overlay"></div>
                    </div>
                    <h3>{{$category->name}}</h3>
                </div>
            </a>
            @endforeach
            
            @foreach ($sub_f as $category)
            <a href="{{route('subCategory.product',$category->slug)}}" class="cat-item">
                <div class="cat-content">
                    <div class="thumbnail">
                        <img src="{{asset('uploads/sub category/'.$category->cover_photo)}}" alt="{{$category->name}}" loading="lazy">
                        <div class="overlay"></div>
                    </div>
                    <h3>{{$category->name}}</h3>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

<style>
.modern-categories {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.modern-categories .cat-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 24px;
    padding: 0 16px;
}

.modern-categories .cat-item {
    text-decoration: none;
    color: inherit;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateY(0);
}

.modern-categories .cat-item:hover {
    transform: translateY(-8px);
    text-decoration: none;
    color: inherit;
}

.modern-categories .cat-content {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
}

.modern-categories .cat-item:hover .cat-content {
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.modern-categories .thumbnail {
    position: relative;
    overflow: hidden;
    aspect-ratio: 1;
}

.modern-categories .thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.modern-categories .cat-item:hover .thumbnail img {
    transform: scale(1.05);
}

.modern-categories .overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.3) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.modern-categories .cat-item:hover .overlay {
    opacity: 1;
}

.modern-categories h3 {
    padding: 20px;
    margin: 0;
    text-align: center;
    font-weight: 600;
    font-size: 16px;
    color: #2d3748;
    transition: color 0.3s ease;
}

.modern-categories .cat-item:hover h3 {
    color: #4299e1;
}

@media (max-width: 768px) {
    .modern-categories .cat-row {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    
    .modern-categories h3 {
        padding: 16px;
        font-size: 14px;
    }
}
</style>
<!--================ / top category Area end =================-->
@endif

@if (setting('CATEGORY_SMALL_SUMMERY') != 0 || setting('CATEGORY_SMALL_SUMMERY') == "")
<section class="collections-section" style="padding: 60px 0;">
    <div class="container">
        <div class="collections-grid">
            @foreach ($collections as $collection)
            <div class="collection-card">
                <div class="collection-content">
                    <div class="collection-thumbnail">
                        <a href="{{route('collection.product', $collection->slug)}}">
                            <img src="{{asset('uploads/collection/'.$collection->cover_photo)}}" 
                                 alt="{{$collection->name}}" loading="lazy">
                            <div class="collection-overlay">
                                <span class="view-collection">View Collection</span>
                            </div>
                        </a>
                    </div>
                    <div class="collection-info">
                        <h4>{{$collection->name}}</h4>
                        @php
                        $categoryIds = $collection->categories->pluck('id');
                        $productIds = DB::table('category_product')->whereIn('category_id', $categoryIds)->get()->pluck('product_id');
                        $products = \App\Models\Product::whereIn('id', $productIds)->where('status',1)->count();
                        @endphp
                        <p class="product-count">{{$products}} products</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
.collections-section {
    background: #fafbfc;
}

.collections-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 32px;
}

.collection-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.04);
}

.collection-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 24px 48px rgba(0, 0, 0, 0.12);
}

.collection-thumbnail {
    position: relative;
    overflow: hidden;
    aspect-ratio: 4/3;
}

.collection-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.collection-card:hover .collection-thumbnail img {
    transform: scale(1.08);
}

.collection-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(66, 153, 225, 0.9), rgba(147, 51, 234, 0.9));
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.collection-card:hover .collection-overlay {
    opacity: 1;
}

.view-collection {
    color: white;
    font-weight: 600;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 12px 24px;
    border: 2px solid white;
    border-radius: 30px;
    transition: all 0.3s ease;
}

.collection-overlay:hover .view-collection {
    background: white;
    color: #4299e1;
}

.collection-info {
    padding: 24px;
    text-align: center;
}

.collection-info h4 {
    margin: 0 0 8px 0;
    font-weight: 700;
    font-size: 20px;
    color: #2d3748;
    line-height: 1.3;
}

.product-count {
    margin: 0;
    color: #718096;
    font-size: 14px;
    font-weight: 500;
}

@media (max-width: 768px) {
    .collections-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .collection-info {
        padding: 20px 16px;
    }
    
    .collection-info h4 {
        font-size: 16px;
    }
    
    .product-count {
        font-size: 13px;
    }
}

@media (max-width: 480px) {
    .collections-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endif

{{-- Category Collapse and Expand System --}}
@push('internal_css')
.superCatHomeToggle {
    height: 330px;
    overflow-y: hidden;
    position: relative;
}

.superCatHomeToggle #superCatViewAll {
    bottom: 0;
}

#superCatViewAll {
    position: absolute;
    bottom: -1.5rem;
    left: 0;
    right: 0;
    background: var(--MAIN_MENU_BG);
    color: var(--MAIN_MENU_ul_li_color);
    z-index: 999;
    outline: none;
    border: none;
    padding: 12px 24px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

#superCatViewAll:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const buttonElement = document.createElement('button');
    buttonElement.id = 'superCatViewAll';
    buttonElement.innerText = 'View All';
    
    const superCatElement = document.getElementById('superCat');
    if (superCatElement) {
        superCatElement.appendChild(buttonElement);
        superCatElement.classList.add('superCatHomeToggle');
        
        buttonElement.addEventListener('click', function() {
            superCatElement.classList.toggle('superCatHomeToggle');
            buttonElement.innerText = buttonElement.innerText === 'View All' ? 'Close' : 'View All';
        });
    }
});
</script>
@endpush

@endsection

@push('js')
<script>
$(document).ready(function() {
    // Quantity controls
    $('.value-plus').on('click', function() {
        const divUpd = $(this).parent().find('.value');
        const newVal = parseInt(divUpd.val(), 10) + 1;
        divUpd.val(newVal);
        $('input#qty').val(newVal);
    });

    $('.value-minus').on('click', function() {
        const divUpd = $(this).parent().find('.value');
        const newVal = parseInt(divUpd.val(), 10) - 1;
        if (newVal >= 1) {
            divUpd.val(newVal);
            $('input#qty').val(newVal);
        }
    });

    // Add to cart handler
    $(document).on('submit', '#addToCart', function(e) {
        e.preventDefault();
        
        const url = $(this).attr('action');
        const type = $(this).attr('method');
        const btn = $(this);
        const formData = $(this).serialize();

        $.ajax({
            type: type,
            url: url,
            data: formData,
            dataType: 'JSON',
            beforeSend: function() {
                $(btn).attr('disabled', true);
            },
            success: function(response) {
                if (response.alert !== 'Congratulations') {
                    $.toast({
                        heading: 'Warning',
                        text: response.message,
                        icon: 'warning',
                        position: 'top-right',
                        stack: false
                    });
                } else {
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

    // Subscription handler
    $(document).on('submit', '#subs', function(e) {
        e.preventDefault();
        
        const url = $(this).attr('action');
        const type = $(this).attr('method');
        const btn = $(this);
        const formData = $(this).serialize();

        $.ajax({
            type: type,
            url: url,
            data: formData,
            dataType: 'JSON',
            beforeSend: function() {
                $(btn).attr('disabled', true);
            },
            success: function(response) {
                if (response.alert !== 'Congratulations') {
                    $.toast({
                        heading: 'Warning',
                        text: response.message,
                        icon: 'warning',
                        position: 'top-right',
                        stack: false
                    });
                } else {
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

    // Sliders
    $('.slider').slick({
        draggable: true,
        autoplay: true,
        autoplaySpeed: 3000,
        arrows: false,
        dots: true,
        fade: true,
        speed: 600,
        infinite: true,
        cssEase: 'cubic-bezier(0.4, 0, 0.2, 1)',
        touchThreshold: 100,
        pauseOnHover: true
    });

    $('.autoplay2').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        arrows: false,
        speed: 600,
        infinite: true,
        cssEase: 'cubic-bezier(0.4, 0, 0.2, 1)',
        touchThreshold: 100,
        pauseOnHover: true,
        responsive: [{
            breakpoint: 767,
            settings: {
                slidesToShow: 2,
            }
        }]
    });

    $('.catplay').slick({
        slidesToShow: 7,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        arrows: false,
        speed: 600,
        infinite: true,
        cssEase: 'cubic-bezier(0.4, 0, 0.2, 1)',
        touchThreshold: 100,
        pauseOnHover: true,
        responsive: [{
            breakpoint: 767,
            settings: {
                slidesToShow: 2,
            }
        }]
    });
});
</script>
@endpush