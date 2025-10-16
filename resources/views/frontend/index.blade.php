@extends('layouts.frontend.app')
@push('meta')
<meta property="og:image" content="{{asset('uploads/setting/'.setting('auth_logo'))}}" />
<meta name="description" content="Discover thousands of books - novels, textbooks, children's books, and more at the best prices. Shop online for your favorite authors and genres.">
<meta name="keywords" content="books, novels, textbooks, children books, online bookstore, buy books online">
@endpush

@section('content')
@php
$pop=App\Models\Slider::where('is_pop','1')->orderBy('id','desc')->first() ;
@endphp

@push('css')
<style>
/* Modern Book Store Styles */
.modern-section {
    padding: 40px 0;
    background: linear-gradient(135deg, #f8fafc 0%, #e3f2fd 100%);
}

.modern-section:nth-child(even) {
    background: #ffffff;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1a202c;
    margin: 0;
    position: relative;
    display: inline-block;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 30px;
    height: 2px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 1px;
}

.section-subtitle {
    display: none;
}

.view-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: #f8f9fa;
    color: #495057;
    padding: 4px 8px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 400;
    font-size: 0.65rem;
    transition: all 0.2s ease;
}

.view-all-btn:hover {
    background: #e9ecef;
    color: #495057;
    text-decoration: none;
    border-color: #adb5bd;
}

/* Modern Category Cards */
.book-genres {
    padding: 40px 0;
}

.genre-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 12px;
    margin-top: 25px;
}

.genre-card {
    background: white;
    border-radius: 10px;
    padding: 16px 12px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    border: 1px solid rgba(102, 126, 234, 0.1);
    position: relative;
    overflow: hidden;
    min-height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.genre-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.genre-card:hover::before {
    transform: scaleX(1);
}

.genre-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15);
    text-decoration: none;
    color: inherit;
    border-color: rgba(102, 126, 234, 0.2);
}

.genre-card h3 {
    font-size: 0.9rem;
    font-weight: 500;
    margin: 0;
    color: #1a202c;
    transition: color 0.3s ease;
    line-height: 1.3;
}

.genre-card:hover h3 {
    color: #667eea;
}

/* Book Announcement */
.book-announcement {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
}

.book-announcement .card-header {
    background: rgba(255, 255, 255, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.book-announcement .card-body {
    background: rgba(255, 255, 255, 0.95);
    color: #1a202c;
    padding: 15px;
    font-size: 0.8rem;
}

/* Product Grid Layouts */
.products-section {
    padding: 40px 0;
}

.products-grid {
    display: flex;
    flex-wrap: wrap;
    margin: -8px;
}

.products-row {
    display: flex;
    flex-wrap: wrap;
    margin: -8px;
    align-items: stretch;
}

/* Product Card Height Consistency */
.products-row > * {
    display: flex;
    flex-direction: column;
}

.products-row .card,
.products-row .product-card,
.products-row [class*="product"] {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.products-row .card-body,
.products-row .product-info,
.products-row .product-content {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.products-row .product-title,
.products-row .card-title {
    min-height: 2.5em;
    line-height: 1.25;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.products-row .product-price,
.products-row .price-section {
    margin-top: auto;
}

/* Force minimum height for consistency */
.products-row .card,
.products-row .product-card {
    min-height: 350px;
}

.products-row img {
    height: 200px;
    object-fit: cover;
    width: 100%;
}

/* Slick Slider Overrides (for publishers and featured collections only) */
.slick-slide {
    padding: 0 6px;
}

.slick-track {
    display: flex !important;
    align-items: stretch;
}

.slick-slide > div {
    height: 100%;
}

/* Publishers Section */
.publishers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 25px;
}

.publisher-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    text-decoration: none;
}

.publisher-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
    text-decoration: none;
}

.publisher-card .cover-image {
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
}

.publisher-card .profile-section {
    padding: 12px;
    text-align: center;
    position: relative;
    margin-top: -20px;
}

.publisher-card .profile-image {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid white;
    object-fit: cover;
    margin-bottom: 6px;
}

.publisher-card h4 {
    font-size: 0.7rem;
    font-weight: 500;
    color: #1a202c;
    margin-bottom: 3px;
}

.publisher-card .publisher-label {
    color: #6b7280;
    font-size: 0.6rem;
}

/* Newsletter Section */
.newsletter-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 0;
}

.newsletter-content {
    text-align: center;
    max-width: 500px;
    margin: 0 auto;
}

.newsletter-content h2 {
    font-size: 1.2rem;
    font-weight: 600;
}

.newsletter-content p {
    font-size: 0.8rem;
}

.newsletter-form {
    display: flex;
    gap: 8px;
    margin-top: 20px;
    max-width: 350px;
    margin-left: auto;
    margin-right: auto;
}

.newsletter-input {
    flex: 1;
    padding: 8px 12px;
    border: none;
    border-radius: 15px;
    font-size: 0.7rem;
    outline: none;
}

.newsletter-btn {
    background: white;
    color: #667eea;
    padding: 8px 15px;
    border: none;
    border-radius: 15px;
    font-weight: 500;
    font-size: 0.7rem;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.newsletter-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(255, 255, 255, 0.3);
}

/* Collections Grid */
.collections-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 25px;
}

.collection-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.collection-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
}

.collection-card img {
    width: 100%;
    height: 140px;
    object-fit: cover;
}

.collection-card .content {
    padding: 12px;
    text-align: center;
}

.collection-card h4 {
    font-size: 0.8rem;
    font-weight: 500;
    color: #1a202c;
    margin-bottom: 5px;
}

.collection-card .book-count {
    color: #6b7280;
    font-size: 0.65rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 15px;
    color: #6b7280;
}

.empty-state i {
    font-size: 2.5rem;
    margin-bottom: 15px;
    opacity: 0.5;
}

.empty-state h4 {
    font-size: 0.9rem;
    font-weight: 500;
}

.empty-state p {
    font-size: 0.75rem;
}

/* Category Toggle - Enhanced for categories section */
.category-toggle {
    background: none;
    border: 2px solid #667eea;
    color: #667eea;
    padding: 10px 20px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 20px;
}

.category-toggle:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.categories-container {
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.category-item {
    display: none;
}

.category-item.visible {
    display: block;
}

/* Responsive Design */
/* See More Button - Mobile positioning */
.mobile-see-more {
    display: none;
    text-align: center;
    margin-top: 20px;
}

.mobile-see-more .view-all-btn {
    padding: 8px 16px;
    font-size: 0.7rem;
}

@media (max-width: 768px) {
    .section-title {
        font-size: 1.3rem;
    }
    
    .view-all-btn {
        padding: 3px 6px;
        font-size: 0.6rem;
    }
    
    .section-header {
        flex-direction: row;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .section-header .view-all-btn {
        display: none;
    }
    
    .mobile-see-more {
        display: block;
    }
    
    .genre-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .genre-card {
        padding: 14px 8px;
        min-height: 55px;
    }
    
    .genre-card h3 {
        font-size: 0.8rem;
    }
    
    .category-toggle {
        padding: 8px 16px;
        font-size: 0.8rem;
    }
    
    .newsletter-form {
        flex-direction: column;
        align-items: center;
    }
    
    .newsletter-input,
    .newsletter-btn {
        width: 100%;
        max-width: 250px;
    }
    
    .newsletter-content h2 {
        font-size: 1rem;
    }
    
    .newsletter-content p {
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .genre-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    
    .genre-card {
        padding: 12px 6px;
        min-height: 50px;
    }
    
    .genre-card h3 {
        font-size: 0.75rem;
    }
    
    .section-title {
        font-size: 1.2rem;
    }
    
    .view-all-btn {
        padding: 3px 5px;
        font-size: 0.55rem;
    }
    
    .mobile-see-more .view-all-btn {
        padding: 6px 12px;
        font-size: 0.65rem;
    }
    
    .category-toggle {
        padding: 7px 14px;
        font-size: 0.75rem;
    }
}

@media (min-width: 992px) and (max-width: 1199px) {
    .genre-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (min-width: 769px) and (max-width: 991px) {
    .genre-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Slick Slider Overrides */
.slick-slide {
    padding: 0 6px;
}

.slick-track {
    display: flex;
    align-items: stretch;
}

.slick-slide > div {
    height: 100%;
}
</style>
@endpush

@if (setting('SLIDER_LAYOUT_STATUS') != 0 || setting('SLIDER_LAYOUT_STATUS') == "")
@if (!empty(setting('SLIDER_LAYOUT')))
<!--================ Hero Book Slider =================-->
@include('frontend.partial.slider_style_' . setting('SLIDER_LAYOUT'))
@else
@include('frontend.partial.slider_style_1')
<!--================ / Hero Book Slider =================-->
@endif
@endif

@if (setting('BELOW_SLIDER_HTML_CODE_STATUS') != 0 || setting('BELOW_SLIDER_HTML_CODE_STATUS') == "")
<!--================ CUSTOM HTML BELOW SLIDER =================-->
<div class="modern-section">
    <div class="container">
        @php
        echo setting('BELOW_SLIDER_HTML_CODE');
        @endphp
    </div>
</div>
<!--================ / CUSTOM HTML BELOW SLIDER =================-->
@endif

@if (setting('LATEST_PRODUCT_STATUS') != 0 || setting('LATEST_PRODUCT_STATUS') == "")
<!--================ New Arrivals =================-->
<div class="products-section modern-section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">🆕 New Arrivals</h2>
            </div>
            <a href="{{route('product')}}" class="view-all-btn">
                <span>See More</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        @if($products->count() > 0)
            <div class="products-row">
                @foreach ($products->take(6) as $product)
                <x-product-grid-view :product="$product" classes="modern-product-col" />
                @endforeach
            </div>
            <div class="mobile-see-more">
                <a href="{{route('product')}}" class="view-all-btn">
                    <span>See More</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <h4>No books available</h4>
                <p>Check back soon for new arrivals!</p>
            </div>
        @endif
    </div>
</div>
<!--================ / New Arrivals =================-->
@endif

@if (setting('NOTICE_STATUS') != 0 || setting('NOTICE_STATUS') == "")
<!--================ BOOK ANNOUNCEMENTS =================-->
<div class="modern-section">
    <div class="container">
        <div class="card book-announcement">
            <div class="card-header">
                📚 Latest Book Updates & Offers
            </div>
            <div class="card-body">
                @php
                echo setting('CUSTOM_NOTICE');
                @endphp
            </div>
        </div>
    </div>
</div>
<!--================ / BOOK ANNOUNCEMENTS =================-->
@endif

@if (setting('TOP_CAT_STATUS') != 0 || setting('TOP_CAT_STATUS') == "")
<!--================ Book Genres & Categories =================-->
<div class="book-genres modern-section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">📚 জনপ্রিয় বিষয়</h2>
            </div>
        </div>
        <div id="categories-container" class="categories-container">
            <div class="genre-grid">
                @php
                $allCategories = collect($categories_f)->merge($mini_f);
                @endphp
                @foreach ($allCategories as $index => $category)
                    @if(isset($category->slug))
                        <a href="{{route('category.product',$category->slug)}}" class="genre-card category-item" data-index="{{$index}}">
                            <h3>{{$category->name}}</h3>
                        </a>
                    @else
                        <a href="{{route('miniCategory.product',$category->slug)}}" class="genre-card category-item" data-index="{{$index}}">
                            <h3>{{$category->name}}</h3>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
        
        <div class="text-center">
            <button id="load-more-categories" class="category-toggle" style="display: none;">Load More Categories</button>
        </div>
    </div>
</div>
<!--================ / Book Genres & Categories =================-->
@endif

@if (setting('HERO_SLIDER_1') != 0 || setting('HERO_SLIDER_1') == "")
<!--================ Featured Book Collections =================-->
@if (setting('HERO_SLIDER_1_TEXT'))
<div class="modern-section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">📚 {{ setting('HERO_SLIDER_1_TEXT') ?: 'Featured Book Collections' }}</h2>
            </div>
        </div>
        <div class="autoplay2 slick-slides">
            @foreach ($sliders_f as $key => $slider_f)
            <div>
                <a href="{{$slider_f->url}}">
                    <img src="{{asset('uploads/slider/'.$slider_f->image)}}" alt="Book Collection" style="width: 100%; border-radius: 8px;">
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endif

@if (setting('SELLER_STATUS') != 0 || setting('SELLER_STATUS') == "")
<!--================ Publishers & Authors =================-->
<div class="modern-section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">🏢 Featured Publishers</h2>
            </div>
            <a href="{{route('vendors')}}" class="view-all-btn">
                <span>See More</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="autoplay slick-slides">
            @foreach ($shops as $shop)
            <div>
                <a href="{{route('vendor', $shop->slug)}}" class="publisher-card">
                    <div class="cover-image"></div>
                    <div class="profile-section">
                        <img src="{{asset('uploads/shop/profile/'.$shop->profile)}}" alt="{{$shop->name}}" class="profile-image">
                        <h4>{{$shop->name}}</h4>
                        <p class="publisher-label">Publisher</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        <div class="mobile-see-more">
            <a href="{{route('vendors')}}" class="view-all-btn">
                <span>See More</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
<!--================ / Publishers & Authors ===============-->
@endif

@if (setting('FEATURE_PRODUCT_STATUS') != 0 || setting('FEATURE_PRODUCT_STATUS') == "")
<!--================ Bestsellers =================-->
<div class="products-section modern-section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">🏆 Bestselling Books</h2>
            </div>
            <a href="{{route('product')}}" class="view-all-btn">
                <span>See More</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        @if($randomProducts->count() > 0)
            <div class="products-row">
                @foreach ($randomProducts->take(6) as $randomProduct)
                <x-product-grid-view :product="$randomProduct" classes="modern-product-col" />
                @endforeach
            </div>
            <div class="mobile-see-more">
                <a href="{{route('product')}}" class="view-all-btn">
                    <span>See More</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-star"></i>
                <h4>No bestsellers available</h4>
            </div>
        @endif
    </div>
</div>
<!--================ / Bestsellers =================-->
@endif

@if (setting('HERO_SLIDER_2') != 0 || setting('HERO_SLIDER_2') == "")
<!--================ Book Promotions =================-->
<div class="modern-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @foreach (App\Models\Slider::where('status',1)->where('is_sub',1)->take(2)->get() as $key => $slider)
                <div class="mb-3">
                    <a href="{{$slider->url}}">
                        <img src="{{asset('uploads/slider/'.$slider->image)}}" alt="Book Promotion" style="width: 100%; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    </a>
                </div>
                @endforeach
            </div>
            <div class="col-md-6">
                @foreach (App\Models\Slider::where('status',1)->where('is_sub',1)->skip(2)->take(1)->get() as $key => $slider)
                <div class="mb-3">
                    <a href="{{$slider->url}}">
                        <img src="{{asset('uploads/slider/'.$slider->image)}}" alt="Featured Book Promotion" style="width: 100%; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    </a>
                </div>
                @endforeach
            </div>
            <div class="col-md-3">
                @foreach (App\Models\Slider::where('status',1)->where('is_sub',1)->skip(3)->take(2)->get() as $key => $slider)
                <div class="mb-3">
                    <a href="{{$slider->url}}">
                        <img src="{{asset('uploads/slider/'.$slider->image)}}" alt="Book Offer" style="width: 100%; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

@if (setting('MEGA_CAT_PRODUCT_STATUS') != 0 || setting('MEGA_CAT_PRODUCT_STATUS') == "")
@if(!empty(setting('mega_cat')))
@foreach(json_decode(setting('mega_cat')) as $c)
@php
$cat = DB::table('categories')->where('id',$c)->first();
$productIds = DB::table('category_product')->where('category_id', $c)->get()->pluck('product_id');
$products = \App\Models\Product::whereIn('id', $productIds)->take(12)->where('status',1)->get();
@endphp
@if($cat && $products->count() > 0)
<div class="products-section modern-section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">📚 {{$cat->name}}</h2>
            </div>
            <a href="{{route('category.product',$cat->slug)}}" class="view-all-btn">
                <span>See More</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="products-row">
            @foreach ($products->take(6) as $product)
            <x-product-grid-view :product="$product" classes="modern-product-col" />
            @endforeach
        </div>
        <div class="mobile-see-more">
            <a href="{{route('category.product',$cat->slug)}}" class="view-all-btn">
                <span>See More</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
@endif
@endforeach
@endif
@endif

@if (setting('BRAND_STATUS') != 0 || setting('BRAND_STATUS') == "")
<!--================ Featured Publishers =================-->
<div class="modern-section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">🏢 Shop by Publishers</h2>
            </div>
            <a href="/brands/list" class="view-all-btn">
                <span>See More</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="publishers-grid">
            @foreach (App\Models\Brand::where('status',1)->take(8)->get() as $brand)
            <a href="{{route('brand.product',['slug'=>$brand->slug])}}" class="publisher-card">
                <div class="cover-image"></div>
                <div class="profile-section">
                    <img src="{{asset('uploads/brand/'.$brand->cover_photo)}}" alt="{{$brand->name}} Publisher" class="profile-image">
                    <h4>{{$brand->name}}</h4>
                    <p class="publisher-label">Publisher</p>
                </div>
            </a>
            @endforeach
        </div>
        <div class="mobile-see-more">
            <a href="/brands/list" class="view-all-btn">
                <span>See More</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
<!--================ / Featured Publishers =================-->
@endif

@if (setting('CATEGORY_SMALL_SUMMERY') != 0 || setting('CATEGORY_SMALL_SUMMERY') == "")
<!--================ Book Collections Summary =================-->
<div class="modern-section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">📖 Book Collections</h2>
            </div>
        </div>
        <div class="collections-grid">
            @foreach ($collections as $key => $collection)
            <div class="collection-card">
                <a href="{{route('collection.product', $collection->slug)}}">
                    <img src="{{asset('uploads/collection/'.$collection->cover_photo)}}" alt="{{$collection->name}} Book Collection">
                    <div class="content">
                        <h4>{{$collection->name}}</h4>
                        @php
                        $categoryIds = $collection->categories->pluck('id');
                        $productIds = DB::table('category_product')->whereIn('category_id', $categoryIds)->get()->pluck('product_id');
                        $products = \App\Models\Product::whereIn('id', $productIds)->where('status',1)->count();
                        @endphp
                        <p class="book-count">{{$products}} books available</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@if (setting('NEWS_LETTER_STATUS') != 0 || setting('NEWS_LETTER_STATUS') == "")
<!--================ Book Newsletter =================-->
<div class="newsletter-section">
    <div class="container">
        <div class="newsletter-content">
            <h2>📚 Stay Updated with New Book Releases</h2>
            <p>Get notified about new arrivals, bestsellers, and exclusive book deals. We respect your privacy and won't spam your inbox.</p>
            <form action="{{route('subscription')}}" method="Post" id="newsletter-form" class="newsletter-form">
                @csrf
                <input type="email" name="subscription" placeholder="Enter Your Email for Book Updates" class="newsletter-input" required>
                <button type="submit" class="newsletter-btn">Subscribe</button>
            </form>
        </div>
    </div>
</div>
@endif

<x-add-cart-modal />
@include('components.cart-modal-attri')

@endsection

@push('js')
<script>
$(document).ready(function () {
    // Category progressive loading functionality
    let currentlyShowing = 0;
    const totalCategories = $('.category-item').length;
    
    function getCategoriesPerLoad() {
        return window.innerWidth <= 768 ? 6 : 12;
    }
    
    function showMoreCategories() {
        const categoriesPerLoad = getCategoriesPerLoad();
        const nextBatch = currentlyShowing + categoriesPerLoad;
        
        $('.category-item').slice(currentlyShowing, nextBatch).addClass('visible');
        currentlyShowing = nextBatch;
        
        if (currentlyShowing >= totalCategories) {
            $('#load-more-categories').hide();
        }
    }
    
    // Initialize categories display
    function initializeCategories() {
        $('.category-item').removeClass('visible');
        currentlyShowing = 0;
        showMoreCategories();
        
        if (currentlyShowing < totalCategories) {
            $('#load-more-categories').show();
        } else {
            $('#load-more-categories').hide();
        }
    }
    
    // Load more categories button click
    $('#load-more-categories').click(function() {
        showMoreCategories();
    });
    
    // Initialize on page load
    initializeCategories();
    
    // Reinitialize on window resize
    $(window).resize(function() {
        initializeCategories();
    });

    // Cart quantity controls
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

    // Add to cart form submission
    $(document).on('submit', '#addToCart', function (e) {
        e.preventDefault();
        let url = $(this).attr('action');
        let type = $(this).attr('method');
        let btn = $(this);
        let formData = $(this).serialize();

        $.ajax({
            type: type,
            url: url,
            data: formData,
            dataType: 'JSON',
            beforeSend: function () {
                $(btn).attr('disabled', true);
            },
            success: function (response) {
                if (response.alert != 'Congratulations') {
                    showToast('Warning', response.message, 'warning');
                } else {
                    $('span#total-cart-amount').text(response.subtotal);
                    showToast('Book Added!', response.message, 'success');
                    $('#cart-modal').modal('hide');
                }
            },
            complete: function () {
                $(btn).attr('disabled', false);
            },
            error: function (xhr) {
                showToast('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
            }
        });
    });

    // Newsletter subscription
    $(document).on('submit', '#newsletter-form', function (e) {
        e.preventDefault();
        let url = $(this).attr('action');
        let type = $(this).attr('method');
        let btn = $(this);
        let formData = $(this).serialize();

        $.ajax({
            type: type,
            url: url,
            data: formData,
            dataType: 'JSON',
            beforeSend: function () {
                btn.find('button').attr('disabled', true).text('Subscribing...');
            },
            success: function (response) {
                showToast('Success!', 'You have been subscribed to book updates!', 'success');
                btn[0].reset();
            },
            complete: function () {
                btn.find('button').attr('disabled', false).text('Subscribe');
            },
            error: function (xhr) {
                showToast('Error', xhr.responseJSON?.message || 'Subscription failed', 'error');
            }
        });
    });

    // Toast notification function
    function showToast(heading, message, icon) {
        if (typeof $.toast === 'function') {
            $.toast({
                heading: heading,
                text: message,
                icon: icon,
                position: 'top-right',
                stack: false,
                hideAfter: 3000
            });
        } else {
            alert(`${heading}: ${message}`);
        }
    }
});

// Slick sliders initialization
$(document).ready(function() {
    // Main slider
    $('.slider').slick({
        draggable: true,
        autoplay: true,
        autoplaySpeed: 3000,
        arrows: false,
        dots: true,
        fade: true,
        speed: 500,
        infinite: true,
        cssEase: 'ease-in-out',
        touchThreshold: 100
    });

    // Featured collections slider
    $('.autoplay2').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2500,
        arrows: false,
        speed: 500,
        infinite: true,
        cssEase: 'ease-in-out',
        touchThreshold: 100,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
    });

    // Product carousels (for publishers and featured collections only)
    $('.autoplay').slick({
        slidesToShow: 6,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2500,
        arrows: false,
        speed: 500,
        infinite: true,
        cssEase: 'ease-in-out',
        touchThreshold: 100,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 4,
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 3,
                }
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 2,
                }
            }
        ]
    });
});
</script>
@endpush