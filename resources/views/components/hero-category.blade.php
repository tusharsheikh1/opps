<div class="category-side col-lg-2.5">
    <div class="hero-categories">
        <ul class="modern-cat-list">
            <!-- Header -->
            <li class="cat-header">
                <div class="header-content">
                    <i class="fas fa-grid-2" style="margin-right: 12px; font-size: 16px;"></i>
                    <span class="header-text">Categories</span>
                </div>
            </li>

            <!-- Categories -->
            @if(Request::route()->getName()=='home')
                @php($t='11')
            @else
                @php($t='18')
            @endif
            
            @foreach (\App\Models\Category::where('status',true)->orderBy('pos','asc')->get()->take($t) as $category)
                <li class="cat-item">
                    <a href="{{route('category.product',$category->slug)}}" class="cat-link">
                        <div class="cat-content">
                            <img src="{{asset('uploads/category/'.$category->cover_photo)}}" 
                                 alt="{{$category->name}}" 
                                 class="cat-icon">
                            <span class="cat-name">{{$category->name}}</span>
                            @if ($category->sub_categories->count() > 0)
                                <i class="fas fa-chevron-right cat-arrow"></i>
                            @endif
                        </div>
                    </a>
                    
                    @if ($category->sub_categories->count() > 0)
                        <ul class="sub-cat-modern">
                            @foreach (\App\Models\SubCategory::where('status',true)->where('category_id',$category->id)->get(['id','name', 'slug']) as $sub_category)
                                <li class="sub-cat-item">
                                    <a href="{{route('subCategory.product', $sub_category->slug)}}" class="sub-cat-link">
                                        <span class="sub-cat-name">{{$sub_category->name}}</span>
                                        @if ($sub_category->miniCategory->count() > 0)
                                            <i class="fas fa-chevron-right sub-cat-arrow"></i>
                                        @endif
                                    </a>
                                    
                                    @if ($sub_category->miniCategory->count() > 0)
                                        <ul class="mini-cat-modern">
                                            @foreach (\App\Models\miniCategory::where('status',true)->where('category_id',$sub_category->id)->get(['id','name', 'slug']) as $miniCategory)
                                                <li class="mini-cat-item">
                                                    <a href="{{route('miniCategory.product', $miniCategory->slug)}}" class="mini-cat-link">
                                                        <span class="mini-cat-name">{{$miniCategory->name}}</span>
                                                        @if ($miniCategory->extraCategory->count() > 0)
                                                            <i class="fas fa-chevron-right mini-cat-arrow"></i>
                                                        @endif
                                                    </a>
                                                    
                                                    @if ($miniCategory->extraCategory->count() > 0)
                                                        <ul class="extra-cat-modern">
                                                            @foreach (\App\Models\ExtraMiniCategory::where('status',true)->where('mini_category_id',$miniCategory->id)->get(['id','name', 'slug']) as $extraCategory)
                                                                <li class="extra-cat-item">
                                                                    <a href="{{route('extraCategory.product', $extraCategory->slug)}}" class="extra-cat-link">
                                                                        <span class="extra-cat-name">{{$extraCategory->name}}</span>
                                                                    </a>
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
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>

<style>
/* Modern Category Navigation Styles */
.hero-categories {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    border: 1px solid #f0f0f0;
}

.modern-cat-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

/* Header Styles */
.cat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0;
    margin: 0;
}

.header-content {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    font-weight: 600;
    font-size: 16px;
    letter-spacing: 0.5px;
}

.header-text {
    text-transform: uppercase;
    font-size: 14px;
    font-weight: 700;
}

/* Main Category Styles */
.cat-item {
    border-bottom: 1px solid #f8f9fa;
    position: relative;
    transition: all 0.3s ease;
}

.cat-item:hover {
    background-color: #f8f9fb;
}

.cat-item:last-child {
    border-bottom: none;
}

.cat-link {
    text-decoration: none;
    color: inherit;
    display: block;
    padding: 0;
    transition: all 0.3s ease;
}

.cat-content {
    display: flex;
    align-items: center;
    padding: 14px 20px;
    gap: 12px;
}

.cat-icon {
    width: 24px;
    height: 24px;
    object-fit: cover;
    border-radius: 6px;
    flex-shrink: 0;
}

.cat-name {
    flex: 1;
    font-size: 14px;
    font-weight: 500;
    color: #2d3748;
    line-height: 1.4;
}

.cat-arrow {
    font-size: 10px;
    color: #a0aec0;
    transition: transform 0.3s ease;
}

.cat-item:hover .cat-arrow {
    transform: translateX(4px);
    color: #667eea;
}

/* Submenu Styles */
.sub-cat-modern,
.mini-cat-modern,
.extra-cat-modern {
    list-style: none;
    margin: 0;
    padding: 0;
    position: absolute;
    left: 100%;
    top: 0;
    min-width: 220px;
    background: white;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    opacity: 0;
    visibility: hidden;
    transform: translateX(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
}

.cat-item:hover > .sub-cat-modern,
.sub-cat-item:hover > .mini-cat-modern,
.mini-cat-item:hover > .extra-cat-modern {
    opacity: 1;
    visibility: visible;
    transform: translateX(0);
}

/* Sub Category Items */
.sub-cat-item,
.mini-cat-item,
.extra-cat-item {
    border-bottom: 1px solid #f1f5f9;
    position: relative;
}

.sub-cat-item:last-child,
.mini-cat-item:last-child,
.extra-cat-item:last-child {
    border-bottom: none;
}

.sub-cat-link,
.mini-cat-link,
.extra-cat-link {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    text-decoration: none;
    color: #4a5568;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.3s ease;
    gap: 8px;
}

.sub-cat-link:hover,
.mini-cat-link:hover,
.extra-cat-link:hover {
    background-color: #f7fafc;
    color: #667eea;
    text-decoration: none;
}

.sub-cat-name,
.mini-cat-name,
.extra-cat-name {
    flex: 1;
    line-height: 1.4;
}

.sub-cat-arrow,
.mini-cat-arrow {
    font-size: 9px;
    color: #cbd5e0;
    transition: all 0.3s ease;
}

.sub-cat-item:hover .sub-cat-arrow,
.mini-cat-item:hover .mini-cat-arrow {
    color: #667eea;
    transform: translateX(2px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-categories {
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    }
    
    .cat-content {
        padding: 12px 16px;
    }
    
    .cat-name {
        font-size: 13px;
    }
    
    .sub-cat-modern,
    .mini-cat-modern,
    .extra-cat-modern {
        position: static;
        opacity: 1;
        visibility: visible;
        transform: none;
        box-shadow: none;
        border: none;
        border-radius: 0;
        background: #f8f9fa;
        margin-top: 8px;
        padding: 8px 0;
    }
    
    .sub-cat-link,
    .mini-cat-link,
    .extra-cat-link {
        padding: 8px 32px;
        font-size: 12px;
    }
}

/* Smooth Animations */
.cat-item,
.sub-cat-item,
.mini-cat-item,
.extra-cat-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Focus States for Accessibility */
.cat-link:focus,
.sub-cat-link:focus,
.mini-cat-link:focus,
.extra-cat-link:focus {
    outline: 2px solid #667eea;
    outline-offset: -2px;
    background-color: #f7fafc;
}

/* Loading State */
.hero-categories.loading {
    opacity: 0.7;
    pointer-events: none;
}

.hero-categories.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #e2e8f0;
    border-top-color: #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>