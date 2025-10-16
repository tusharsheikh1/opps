{{-- single-product-partial/product-meta.blade.php --}}
<div class="sp-product-meta">
    {{-- Brand/Publisher Section --}}
    @if($product->brand)
        <div class="sp-meta-item" data-aos="fade-up" data-aos-delay="100">
            <div class="sp-meta-icon">
                <i class="fas fa-building" aria-hidden="true"></i>
            </div>
            <div class="sp-meta-content">
                <div class="sp-meta-label">প্রকাশনি</div>
                <a href="{{ route('brand.product', ['slug' => $product->brand->slug]) }}" 
                   class="sp-meta-link" 
                   title="{{$product->brand->name}} এর আরো বই দেখুন"
                   aria-label="প্রকাশনি: {{$product->brand->name}}">
                    {{ $product->brand->name }}
                    <i class="fas fa-external-link-alt sp-external-icon" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    @endif
    
    {{-- Author Section (Books Only) --}}
    @if ($product->book == 1 && $product->author)
        <div class="sp-meta-item" data-aos="fade-up" data-aos-delay="200">
            <div class="sp-meta-icon">
                <i class="fas fa-user-edit" aria-hidden="true"></i>
            </div>
            <div class="sp-meta-content">
                <div class="sp-meta-label">লেখক</div>
                <a href="{{ route('author.product', ['slug' => $product->author_id]) }}" 
                   class="sp-meta-link" 
                   title="{{$product->author->name}} এর আরো বই দেখুন"
                   aria-label="লেখক: {{$product->author->name}}">
                    {{ $product->author->name }}
                    <i class="fas fa-external-link-alt sp-external-icon" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    @endif
    
    {{-- Categories Section (Books Only) --}}
    @if ($product->book == 1 && $product->categories->count() > 0)
        <div class="sp-meta-item" data-aos="fade-up" data-aos-delay="300">
            <div class="sp-meta-icon">
                <i class="fas fa-tags" aria-hidden="true"></i>
            </div>
            <div class="sp-meta-content">
                <div class="sp-meta-label">বিষয় / ক্যাটেগরি</div>
                <div class="sp-categories" role="list">
                    @foreach($product->categories as $index => $category)
                        <a href="{{ route('category.products', ['slug' => $category->slug]) }}" 
                           class="sp-category-tag" 
                           role="listitem"
                           title="{{ $category->name }} বিষয়ের আরো বই দেখুন"
                           aria-label="বিষয়: {{ $category->name }}">
                            <span class="sp-category-name">{{ $category->name }}</span>
                            @if($category->products_count ?? 0 > 0)
                                <span class="sp-category-count">({{ $category->products_count }})</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    
    {{-- Additional Book Details --}}
    @if ($product->book == 1)
        <div class="sp-meta-item sp-book-details" data-aos="fade-up" data-aos-delay="400">
            <div class="sp-meta-icon">
                <i class="fas fa-book-open" aria-hidden="true"></i>
            </div>
            <div class="sp-meta-content">
                <div class="sp-meta-label">বইয়ের তথ্য</div>
                <div class="sp-book-info-grid">
                    {{-- Page Count --}}
                    @if($product->pages)
                        <div class="sp-book-info-item">
                            <i class="fas fa-file-alt" aria-hidden="true"></i>
                            <span class="sp-info-label">পৃষ্ঠা:</span>
                            <span class="sp-info-value">{{ number_format($product->pages) }} পৃষ্ঠা</span>
                        </div>
                    @endif
                    
                    {{-- Language --}}
                    @if($product->language)
                        <div class="sp-book-info-item">
                            <i class="fas fa-language" aria-hidden="true"></i>
                            <span class="sp-info-label">ভাষা:</span>
                            <span class="sp-info-value">{{ $product->language }}</span>
                        </div>
                    @endif
                    
                    {{-- ISBN --}}
                    @if($product->isbn)
                        <div class="sp-book-info-item">
                            <i class="fas fa-barcode" aria-hidden="true"></i>
                            <span class="sp-info-label">ISBN:</span>
                            <span class="sp-info-value">{{ $product->isbn }}</span>
                        </div>
                    @endif
                    
                    {{-- Publication Year --}}
                    @if($product->publication_year)
                        <div class="sp-book-info-item">
                            <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                            <span class="sp-info-label">প্রকাশকাল:</span>
                            <span class="sp-info-value">{{ $product->publication_year }}</span>
                        </div>
                    @endif
                    
                    {{-- Edition --}}
                    @if($product->edition)
                        <div class="sp-book-info-item">
                            <i class="fas fa-layer-group" aria-hidden="true"></i>
                            <span class="sp-info-label">সংস্করণ:</span>
                            <span class="sp-info-value">{{ $product->edition }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    
    {{-- Availability Status --}}
    @if(isset($product->stock_status))
        <div class="sp-meta-item sp-availability" data-aos="fade-up" data-aos-delay="500">
            <div class="sp-meta-icon">
                <i class="fas fa-{{ $product->stock_status === 'in_stock' ? 'check-circle' : 'exclamation-triangle' }}" 
                   aria-hidden="true"></i>
            </div>
            <div class="sp-meta-content">
                <div class="sp-meta-label">স্টক স্ট্যাটাস</div>
                <span class="sp-stock-status sp-stock-{{ $product->stock_status }}">
                    @switch($product->stock_status)
                        @case('in_stock')
                            <i class="fas fa-check" aria-hidden="true"></i> স্টকে আছে
                            @break
                        @case('out_of_stock')
                            <i class="fas fa-times" aria-hidden="true"></i> স্টকে নেই
                            @break
                        @case('limited_stock')
                            <i class="fas fa-exclamation" aria-hidden="true"></i> সীমিত স্টক
                            @break
                        @default
                            <i class="fas fa-question" aria-hidden="true"></i> স্ট্যাটাস অজানা
                    @endswitch
                </span>
            </div>
        </div>
    @endif
</div>

{{-- Add AOS (Animate On Scroll) Library if not already included --}}
@push('styles')
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 600,
        easing: 'ease-out-cubic',
        once: true,
        offset: 50
    });
});
</script>
@endpush

<style>
    /* Enhanced Product Meta Styles */
    .sp-product-meta {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 30px;
        font-family: 'Siyam Rupali', 'Inter', sans-serif;
    }

    .sp-meta-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 20px;
        background: linear-gradient(135deg, 
            rgba(255, 255, 255, 0.9) 0%, 
            rgba(248, 249, 250, 0.9) 100%);
        border: 1px solid var(--sp-border-light);
        border-left: 5px solid var(--sp-secondary);
        border-radius: var(--sp-radius-md);
        transition: all var(--sp-transition-normal);
        box-shadow: var(--sp-shadow-sm);
        position: relative;
        overflow: hidden;
    }

    .sp-meta-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, 
            var(--sp-secondary) 0%, 
            var(--sp-primary) 50%, 
            var(--sp-secondary) 100%);
        transform: scaleX(0);
        transition: transform var(--sp-transition-normal);
    }

    .sp-meta-item:hover {
        background: linear-gradient(135deg, 
            rgba(255, 255, 255, 1) 0%, 
            rgba(240, 248, 255, 1) 100%);
        border-left-color: var(--sp-primary);
        transform: translateY(-2px);
        box-shadow: var(--sp-shadow-md);
    }

    .sp-meta-item:hover::before {
        transform: scaleX(1);
    }

    .sp-meta-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--sp-secondary), var(--sp-primary));
        color: white;
        border-radius: 50%;
        font-size: 18px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        transition: all var(--sp-transition-normal);
    }

    .sp-meta-item:hover .sp-meta-icon {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
    }

    .sp-meta-content {
        flex: 1;
        min-width: 0;
    }

    .sp-meta-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--sp-text-light);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
    }

    .sp-meta-link {
        color: var(--sp-secondary);
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        transition: all var(--sp-transition-fast);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 4px 0;
        border-bottom: 2px solid transparent;
    }

    .sp-meta-link:hover {
        color: var(--sp-primary);
        border-bottom-color: var(--sp-primary);
        transform: translateX(4px);
    }

    .sp-external-icon {
        font-size: 12px;
        opacity: 0.7;
        transition: all var(--sp-transition-fast);
    }

    .sp-meta-link:hover .sp-external-icon {
        opacity: 1;
        transform: translateX(2px) translateY(-1px);
    }

    /* Categories Styling */
    .sp-categories {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 8px;
    }

    .sp-category-tag {
        background: linear-gradient(135deg, var(--sp-success) 0%, #2ecc71 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: 500;
        letter-spacing: 0.3px;
        text-decoration: none;
        transition: all var(--sp-transition-fast);
        box-shadow: 0 3px 10px rgba(39, 174, 96, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        position: relative;
        overflow: hidden;
    }

    .sp-category-tag::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left var(--sp-transition-normal);
    }

    .sp-category-tag:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        color: white;
    }

    .sp-category-tag:hover::before {
        left: 100%;
    }

    .sp-category-count {
        background: rgba(255, 255, 255, 0.2);
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
    }

    /* Book Details Grid */
    .sp-book-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin-top: 8px;
    }

    .sp-book-info-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px;
        background: var(--sp-bg-secondary);
        border-radius: var(--sp-radius-sm);
        transition: all var(--sp-transition-fast);
        border: 1px solid var(--sp-border-light);
    }

    .sp-book-info-item:hover {
        background: rgba(52, 152, 219, 0.1);
        border-color: var(--sp-secondary);
    }

    .sp-book-info-item i {
        color: var(--sp-secondary);
        width: 16px;
        text-align: center;
    }

    .sp-info-label {
        font-weight: 500;
        color: var(--sp-text-medium);
        font-size: 13px;
    }

    .sp-info-value {
        font-weight: 600;
        color: var(--sp-text-dark);
        font-size: 14px;
    }

    /* Stock Status Styling */
    .sp-availability {
        border-left-color: var(--sp-warning);
    }

    .sp-stock-status {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all var(--sp-transition-fast);
    }

    .sp-stock-in_stock {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        border: 2px solid #10b981;
    }

    .sp-stock-out_of_stock {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        border: 2px solid #ef4444;
    }

    .sp-stock-limited_stock {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        border: 2px solid #f59e0b;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sp-meta-item {
            padding: 16px;
            gap: 12px;
        }

        .sp-meta-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .sp-categories {
            gap: 8px;
        }

        .sp-category-tag {
            padding: 6px 12px;
            font-size: 12px;
        }

        .sp-book-info-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .sp-book-info-item {
            padding: 10px;
        }
    }

    @media (max-width: 480px) {
        .sp-meta-item {
            flex-direction: column;
            text-align: center;
            gap: 12px;
        }

        .sp-meta-icon {
            align-self: center;
        }

        .sp-categories {
            justify-content: center;
        }
    }

    /* Print Styles */
    @media print {
        .sp-meta-item {
            break-inside: avoid;
            box-shadow: none;
            border: 1px solid #ccc;
        }

        .sp-meta-icon {
            background: #666 !important;
        }

        .sp-category-tag {
            background: #666 !important;
            color: white !important;
        }
    }

    /* Accessibility Improvements */
    @media (prefers-reduced-motion: reduce) {
        .sp-meta-item,
        .sp-meta-icon,
        .sp-category-tag,
        .sp-meta-link {
            transition: none;
        }

        .sp-category-tag::before {
            display: none;
        }

        .sp-stock-limited_stock {
            animation: none;
        }
    }

    /* Focus States */
    .sp-meta-link:focus,
    .sp-category-tag:focus {
        outline: 3px solid rgba(52, 152, 219, 0.5);
        outline-offset: 2px;
    }

    /* High Contrast Mode */
    @media (prefers-contrast: high) {
        .sp-meta-item {
            border-width: 2px;
            border-color: #000;
        }

        .sp-meta-link {
            border-bottom-width: 3px;
        }
    }
</style>