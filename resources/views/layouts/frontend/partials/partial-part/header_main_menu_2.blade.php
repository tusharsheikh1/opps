<div class="main-menu">
    <div class="container">

        <div class="back">
            <i class="fas fa-long-arrow-alt-left"></i> back
        </div>
        <div class="collpase-menu-open" style="display: none;">
            <a id="menu" class="active" href="#">MENU</a>
            {{-- <a id="cat" href="#">CATEGORIES</a> --}}
        </div>

        {{-- <div class="nav-bar"> --}}
            <div class="menu-container">
                <div class="{{-- nav-menus --}} menu_style_2">
                    <ul id="menuList">
                        {{-- <li><a href="{{route('home')}}" class="{{Request::is('/') ? 'active':''}}">Home</a></li>
                        <li><a href="{{route('product')}}" class="{{Request::is('product*') ? 'active':''}}">All Products</a></li> --}}
                        @if(Request::route()->getName()=='home')
                            @php($t='11')
                        @else
                            @php($t='18')
                        @endif
                        @foreach (\App\Models\Category::where('status',true)->orderBy('pos','asc')->get()->take($t) as $category)
                            <li>
                                <a href="{{route('category.product',$category->slug)}}" class="menu-card">
                                    <span class="menu-text">{{$category->name}}</span>
                                </a>
                                @if ($category->sub_categories->count() > 0)
                                <ul class="sub-cat">
                                    @foreach (\App\Models\SubCategory::where('status',true)->where('category_id',$category->id)->get(['id','name', 'slug']) as $sub_category)
                                        <li>
                                            <a href="{{route('subCategory.product', $sub_category->slug)}}">{{$sub_category->name}}</a>
                                            @if ($sub_category->miniCategory->count() > 0)
                                                <ul class="sub-cat">
                                                        @foreach (\App\Models\miniCategory::where('status',true)->where('category_id',$sub_category->id)->get(['id','name', 'slug']) as $miniCategory)
                                                        <li>
                                                            <a href="{{route('miniCategory.product', $miniCategory->slug)}}">{{$miniCategory->name}}</a>
                                                            @if ($miniCategory->extraCategory->count() > 0)
                                                                <ul class="sub-cat">
                                                                    @foreach (\App\Models\ExtraMiniCategory::where('status',true)->where('mini_category_id',$miniCategory->id)->get(['id','name', 'slug'])  as $extraCategory)
                                                                        <li><a href="{{route('extraCategory.product', $extraCategory->slug)}}">{{$extraCategory->name}}</a></li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="see-more-arrow" id="seeMoreArrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
        {{-- </div> --}}

    </div>
</div>

<style>
    .main-menu {
        background: #ffffff;
        padding: 10px 0;
        border-bottom: 1px solid #e9ecef;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .menu-container {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .menu_style_2 {
        flex: 1;
        overflow: hidden;
    }

    .menu_style_2 ul{
        display: flex;
        justify-content: flex-start;
        align-items: center;
        flex-direction: row;
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;
        width: 100%;
        position: relative;
        flex-wrap: nowrap;
        gap: 8px;
        overflow-x: auto;
        overflow-y: hidden;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .menu_style_2 ul::-webkit-scrollbar {
        display: none;
    }

    .menu_style_2 ul li{
        position: relative;
        margin: 0;
        padding: 0;
    }

    .menu_style_2 ul li .menu-card{
        display: inline-block;
        background: #ffffff;
        color: #333333 !important;
        font-weight: 600;
        font-size: 14px;
        padding: 12px 20px;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border: 1px solid #e9ecef;
        position: relative;
        overflow: hidden;
        min-width: 100px;
        text-align: center;
        white-space: nowrap;
    }

    .menu_style_2 ul li .menu-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(0,0,0,0.05), transparent);
        transition: left 0.5s;
    }

    .menu_style_2 ul li .menu-card:hover::before {
        left: 100%;
    }

    .menu_style_2 ul li .menu-card:hover{
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        background: #f8f9fa;
        color: #000000 !important;
    }

    .menu_style_2 ul li .menu-card:active{
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .menu_style_2 ul li .menu-text {
        position: relative;
        z-index: 1;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        letter-spacing: 0.5px;
    }

    /* Dropdown Styles */
    .menu_style_2 ul li ul{
        display: none;
        position: absolute;
        width: max-content;
        min-width: 220px;
        top: calc(100% + 10px);
        left: 0;
        z-index: 999;
        padding: 8px 0;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        border: 1px solid #e9ecef;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .menu_style_2 ul li:hover ul{
        display: block;
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .menu_style_2 ul li ul li{
        all: unset;
        display: block;
        padding: 0;
        margin: 0;
        width: 100%;
    }

    .menu_style_2 ul li ul li a{
        display: block;
        background: transparent !important;
        color: #495057 !important;
        padding: 12px 20px;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.2s ease;
        border-radius: 0;
        text-decoration: none;
        border-left: 3px solid transparent;
    }

    .menu_style_2 ul li ul li:hover a{
        background: #f8f9fa !important;
        color: #333333 !important;
        border-left-color: #6c757d;
        padding-left: 24px;
    }

    .menu_style_2 ul li ul li:first-child a {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .menu_style_2 ul li ul li:last-child a {
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .menu_style_2 ul {
            gap: 6px;
            padding-right: 20px;
        }
        
        .menu_style_2 ul li .menu-card {
            padding: 10px 16px;
            font-size: 13px;
            min-width: auto;
            flex-shrink: 0;
        }
        
        .menu_style_2 ul li ul {
            min-width: 200px;
            left: -10px;
        }
    }

    @media (max-width: 480px) {
        .menu_style_2 ul {
            padding-right: 20px;
        }
        
        .menu_style_2 ul li .menu-card {
            padding: 8px 14px;
            font-size: 12px;
            flex-shrink: 0;
        }
    }

    /* Animation for menu load */
    .menu_style_2 ul li {
        animation: slideInUp 0.6s ease forwards;
        opacity: 0;
        transform: translateY(20px);
    }

    .menu_style_2 ul li:nth-child(1) { animation-delay: 0.1s; }
    .menu_style_2 ul li:nth-child(2) { animation-delay: 0.2s; }
    .menu_style_2 ul li:nth-child(3) { animation-delay: 0.3s; }
    .menu_style_2 ul li:nth-child(4) { animation-delay: 0.4s; }
    .menu_style_2 ul li:nth-child(5) { animation-delay: 0.5s; }
    .menu_style_2 ul li:nth-child(6) { animation-delay: 0.6s; }
    .menu_style_2 ul li:nth-child(7) { animation-delay: 0.7s; }
    .menu_style_2 ul li:nth-child(8) { animation-delay: 0.8s; }

    @keyframes slideInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .see-more-arrow {
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        background: linear-gradient(to left, #ffffff 0%, #ffffff 70%, rgba(255,255,255,0) 100%);
        padding: 10px 15px 10px 25px;
        cursor: pointer;
        z-index: 10;
        transition: all 0.3s ease;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    .see-more-arrow.visible {
        opacity: 1;
        visibility: visible;
        pointer-events: all;
    }

    .see-more-arrow i {
        color: #6c757d;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .see-more-arrow:hover i {
        color: #333333;
        transform: translateX(3px);
    }

    .see-more-arrow::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 30px;
        background: linear-gradient(to right, rgba(255,255,255,0) 0%, #ffffff 100%);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuList = document.getElementById('menuList');
    const seeMoreArrow = document.getElementById('seeMoreArrow');
    
    function checkScrollability() {
        if (menuList.scrollWidth > menuList.clientWidth) {
            const isScrolledToEnd = menuList.scrollLeft >= (menuList.scrollWidth - menuList.clientWidth - 5);
            
            if (isScrolledToEnd) {
                seeMoreArrow.classList.remove('visible');
            } else {
                seeMoreArrow.classList.add('visible');
            }
        } else {
            seeMoreArrow.classList.remove('visible');
        }
    }
    
    // Check on load
    setTimeout(checkScrollability, 100);
    
    // Check on scroll
    menuList.addEventListener('scroll', checkScrollability);
    
    // Check on window resize
    window.addEventListener('resize', checkScrollability);
    
    // Click arrow to scroll
    seeMoreArrow.addEventListener('click', function() {
        menuList.scrollBy({
            left: 200,
            behavior: 'smooth'
        });
    });
});
</script>