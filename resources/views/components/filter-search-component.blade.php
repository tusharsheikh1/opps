<form action="{{ route('product.filter') }}" method="GET" id="form">
    
    {{-- Price Range Filter --}}
    <div class="filter-section price-range-section">
        <h3 class="filter-title">Price Range</h3>
        <div class="price-range-wrapper">
            <div id="slider-range"></div>
            <input type="text" name="amount" readonly id="amount" class="price-display" />
        </div>
    </div>

    @isset($request->mini_category)
        @php
            $input_name = 'Mini Category';
            $input = 'mini_category';
            $data = DB::table('mini_categories')->where('slug', $request->mini_category)->first();
            $datan = DB::table('sub_categories')->where('id', $data->sub_category_id)->first();
            $cidn = $datan->category_id;
        @endphp
    @endisset
    
    @isset($request->extra_category)
        @php
            $input_name = 'Extra Category';
            $input = 'extra_category';
            $data = DB::table('extra_mini_categories')->where('slug', $request->extra_category)->first();
            $mini = DB::table('mini_categories')->where('id', $data->mini_category_id)->first();
            $datas = DB::table('sub_categories')->where('id', $mini->sub_category_id)->first();
            $cidn = $datas->category_id;
        @endphp
    @endisset
    
    @isset($request->sub_category)
        @php
            $input_name = 'Sub Category';
            $input = 'sub_category';
            $data = DB::table('sub_categories')->where('slug', $request->sub_category)->first();
            $cidn = $data->category_id;
            $idn = $data->id;
        @endphp
    @endisset
    
    @isset($request->category)
        @php
            $input_name = 'Category';
            $input = 'category';
            $data = DB::table('categories')->where('slug', $request->category)->first();
            $cidn = $data->id;
        @endphp
    @endisset
    
    @isset($request->collection)
        @php
            $input_name = 'Collection';
            $input = 'collection';
            $data = DB::table('collections')->where('slug', $request->collection)->first();
        @endphp
    @endisset

    @if ($name == 'category')
        @php
            $input_name = 'Category';
            $input = 'category';
            $data = DB::table('categories')->where('slug', $value)->first();
            $cidn = $data->id;
        @endphp
    @endif
    
    @if ($name == 'sub_category')
        @php
            $input_name = 'Sub Category';
            $input = 'sub_category';
            $data = DB::table('sub_categories')->where('slug', $value)->first();
            $cidn = $data->category_id;
            $idn = $data->id;
        @endphp
    @endif
    
    @if ($name == 'mini_category')
        @php
            $input_name = 'Mini Category';
            $input = 'mini_category';
            $mini = DB::table('mini_categories')->where('slug', $value)->first();
            $data = DB::table('sub_categories')->where('id', $mini->sub_category_id)->first();
            $cidn = $data->category_id;
        @endphp
    @endif
    
    @if ($name == 'extra_category')
        @php
            $input_name = 'Extra Category';
            $input = 'extra_category';
            $emini = DB::table('extra_mini_categories')->where('slug', $value)->first();
            $mini = DB::table('mini_categories')->where('id', $emini->mini_category_id)->first();
            $data = DB::table('sub_categories')->where('id', $mini->sub_category_id)->first();
            $cidn = $data->category_id;
        @endphp
    @endif

    {{-- Attributes Filter --}}
    @isset($cidn)
        @foreach (App\Models\Attribute::where('category_id', $cidn)->get() as $attribute)
            @if ($attribute->values->count() > 0)
                <div class="filter-section attribute-section">
                    <h3 class="filter-title">{{ $attribute->name }}</h3>
                    <div class="attribute-chips">
                        @foreach ($attribute->values as $avalue)
                            <label class="chip-label @isset($request->attri) @foreach ($request->attri as $req_brand) {{ $avalue->slug == $req_brand ? 'active' : '' }} @endforeach @endisset">
                                <input type="checkbox" 
                                       id="{{ $avalue->slug }}" 
                                       name="attri[]" 
                                       class="chip-input"
                                       value="{{ $avalue->slug }}"
                                       @isset($request->attri) @foreach ($request->attri as $req_brand) {{ $avalue->slug == $req_brand ? 'checked' : '' }} @endforeach @endisset>
                                <span class="chip-text">{{ $avalue->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    @endisset

    {{-- Category Navigation --}}
    @isset($input_name)
        <div class="filter-section category-section">
            <h3 class="filter-title">{{ $input_name }}</h3>
            <div class="category-breadcrumb">
                <input type="checkbox" name="{{ $input }}" class="d-none" id="dcd" value="{{ $data->slug }}" checked>
                
                @if ($input == 'category')
                    <div class="breadcrumb-item active">{{ $data->name }}</div>
                @endif
                
                @if ($input == 'sub_category')
                    @php($cs = App\Models\Category::where('id', $cidn)->first())
                    <a href="{{ route('category.product', $cs->slug) }}" class="breadcrumb-item">
                        {{ $cs->name }}
                    </a>
                    <span class="breadcrumb-separator">›</span>
                    <div class="breadcrumb-item active">{{ $data->name }}</div>
                @endif
                
                @if ($input == 'mini_category')
                    @if (isset($datan))
                        @php($cs = App\Models\Category::where('id', $cidn)->first())
                        <a href="{{ route('category.product', $cs->slug) }}" class="breadcrumb-item">
                            {{ $cs->name }}
                        </a>
                        <span class="breadcrumb-separator">›</span>
                        <a href="{{ route('subCategory.product', $datan->slug) }}" class="breadcrumb-item">
                            {{ $datan->name }}
                        </a>
                        <span class="breadcrumb-separator">›</span>
                        <div class="breadcrumb-item active">{{ $data->name }}</div>
                    @else
                        @php($cs = App\Models\Category::where('id', $cidn)->first())
                        <a href="{{ route('category.product', $cs->slug) }}" class="breadcrumb-item">
                            {{ $cs->name }}
                        </a>
                        <span class="breadcrumb-separator">›</span>
                        <a href="{{ route('subCategory.product', $data->slug) }}" class="breadcrumb-item">
                            {{ $data->name }}
                        </a>
                        <span class="breadcrumb-separator">›</span>
                        <div class="breadcrumb-item active">{{ $mini->name }}</div>
                    @endif
                @endif
            </div>
            
            <ul class="subcategory-list">
                @if ($input == 'category')
                    @foreach (App\Models\SubCategory::where('category_id', $cidn)->get() as $cat)
                        <li class="subcategory-item">
                            <input class="subcategory-radio" type="radio" id="{{ $cat->slug }}" 
                                   name="sub_category" value="{{ $cat->slug }}">
                            <label for="{{ $cat->slug }}" class="subcategory-label">
                                {{ $cat->name }}
                            </label>
                        </li>
                    @endforeach
                @endif
                
                @if ($input == 'sub_category')
                    @foreach (App\Models\miniCategory::where('sub_category_id', $idn)->get() as $cat)
                        <li class="subcategory-item">
                            <input class="subcategory-radio" type="radio" id="{{ $cat->slug }}" 
                                   name="mini_category" value="{{ $cat->slug }}">
                            <label for="{{ $cat->slug }}" class="subcategory-label">
                                {{ $cat->name }}
                            </label>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
    @else
        <div class="filter-section category-section">
            <h3 class="filter-title">Categories</h3>
            <ul class="subcategory-list">
                @foreach (App\Models\Category::get() as $cat)
                    <li class="subcategory-item">
                        <input class="subcategory-radio" type="radio" id="{{ $cat->slug }}" 
                               name="category" value="{{ $cat->slug }}">
                        <label for="{{ $cat->slug }}" class="subcategory-label">
                            {{ $cat->name }}
                        </label>
                    </li>
                @endforeach
            </ul>
        </div>
    @endisset

    {{-- Brand Filter --}}
    <div class="filter-section brand-section">
        <h3 class="filter-title">Brand</h3>
        <div class="brand-list-wrapper">
            <ul class="brand-list">
                @foreach ($brands as $brand)
                    <li class="brand-item">
                        <label class="brand-checkbox">
                            <input type="checkbox" 
                                   name="brands[]" 
                                   value="{{ $brand->slug }}"
                                   @isset($request->brands) 
                                       @foreach ($request->brands as $req_brand) 
                                           {{ $brand->slug == $req_brand ? 'checked' : '' }} 
                                       @endforeach 
                                   @endisset>
                            <span class="checkmark"></span>
                            <span class="brand-name">{{ $brand->name }}</span>
                        </label>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Sort Options --}}
    <div class="filter-section sort-section">
        <h3 class="filter-title">Sort By</h3>
        <div class="sort-options">
            <label class="sort-radio">
                <input @isset($request->sort) @if ($request->sort == 'Best Sellers') checked @endif @endisset
                       name="sort" type="radio" value="Best Sellers">
                <span class="radio-custom"></span>
                <span class="sort-text">Best Sellers</span>
            </label>
            
            <label class="sort-radio">
                <input @isset($request->sort) @if ($request->sort == 'New To Old') checked @endif @endisset
                       name="sort" type="radio" value="New To Old">
                <span class="radio-custom"></span>
                <span class="sort-text">New Released</span>
            </label>
            
            <label class="sort-radio">
                <input @isset($request->sort) @if ($request->sort == 'High To Low') checked @endif @endisset
                       name="sort" type="radio" value="High To Low">
                <span class="radio-custom"></span>
                <span class="sort-text">Price: High to Low</span>
            </label>
            
            <label class="sort-radio">
                <input @isset($request->sort) @if ($request->sort == 'Low To High') checked @endif @endisset
                       name="sort" type="radio" value="Low To High">
                <span class="radio-custom"></span>
                <span class="sort-text">Price: Low to High</span>
            </label>
            
            <label class="sort-radio">
                <input @isset($request->sort) @if ($request->sort == 'dHigh To Low') checked @endif @endisset
                       name="sort" type="radio" value="dHigh To Low">
                <span class="radio-custom"></span>
                <span class="sort-text">Discount: High to Low</span>
            </label>
            
            <label class="sort-radio">
                <input @isset($request->sort) @if ($request->sort == 'dLow To High') checked @endif @endisset
                       name="sort" type="radio" value="dLow To High">
                <span class="radio-custom"></span>
                <span class="sort-text">Discount: Low to High</span>
            </label>
        </div>
    </div>

    <input type="hidden" name="unr" value="{{ $request->unr ?? Request::url() }}">

    {{-- Action Buttons --}}
    <div class="filter-actions">
        <button type="submit" class="btn-filter">
            <i class="fas fa-check"></i> Apply Filters
        </button>
        <a href="{{ $request->unr ?? Request::url() }}" class="btn-reset">
            <i class="fas fa-redo"></i> Reset All
        </a>
    </div>
</form>

<style>
    /* Filter Section Styles */
    .filter-section {
        margin-bottom: 28px;
        padding-bottom: 24px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .filter-section:last-of-type {
        border-bottom: none;
    }
    
    .filter-title {
        font-size: 16px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 16px;
        letter-spacing: -0.2px;
    }
    
    /* Price Range Styles */
    .price-range-wrapper {
        padding: 8px 4px;
    }
    
    .price-display {
        border: none;
        color: #4b5563;
        font-weight: 600;
        font-size: 14px;
        background: transparent;
        text-align: center;
        width: 100%;
        margin-top: 12px;
    }
    
    #slider-range {
        margin: 16px 0;
    }
    
    .ui-slider {
        background: #e5e7eb;
        border: none;
        border-radius: 10px;
        height: 6px;
    }
    
    .ui-slider-range {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        border-radius: 10px;
    }
    
    .ui-slider-handle {
        width: 18px;
        height: 18px;
        background: white;
        border: 3px solid #2563eb;
        border-radius: 50%;
        top: -6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .ui-slider-handle:hover {
        transform: scale(1.2);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    
    /* Attribute Chips */
    .attribute-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .chip-label {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 13px;
        font-weight: 500;
        color: #4b5563;
        user-select: none;
    }
    
    .chip-label:hover {
        background: #e5e7eb;
        border-color: #d1d5db;
    }
    
    .chip-label.active {
        background: #2563eb;
        border-color: #2563eb;
        color: white;
    }
    
    .chip-input {
        display: none;
    }
    
    .chip-text {
        white-space: nowrap;
    }
    
    /* Category Breadcrumb */
    .category-breadcrumb {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        padding: 12px 16px;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 13px;
    }
    
    .breadcrumb-item {
        color: #2563eb;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }
    
    .breadcrumb-item:hover {
        color: #1d4ed8;
    }
    
    .breadcrumb-item.active {
        color: #1a1a1a;
        font-weight: 600;
    }
    
    .breadcrumb-separator {
        color: #9ca3af;
        font-weight: 300;
    }
    
    /* Subcategory List */
    .subcategory-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .subcategory-item {
        margin-bottom: 8px;
    }
    
    .subcategory-radio {
        display: none;
    }
    
    .subcategory-label {
        display: block;
        padding: 10px 16px;
        background: #f9fafb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
        color: #4b5563;
        font-weight: 500;
    }
    
    .subcategory-label:hover {
        background: #f3f4f6;
        color: #1a1a1a;
        transform: translateX(4px);
    }
    
    .subcategory-radio:checked + .subcategory-label {
        background: #eff6ff;
        color: #2563eb;
        font-weight: 600;
    }
    
    /* Brand List */
    .brand-list-wrapper {
        max-height: 280px;
        overflow-y: auto;
        padding-right: 8px;
    }
    
    .brand-list-wrapper::-webkit-scrollbar {
        width: 6px;
    }
    
    .brand-list-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .brand-list-wrapper::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    
    .brand-list-wrapper::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    
    .brand-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .brand-item {
        margin-bottom: 12px;
    }
    
    .brand-checkbox {
        display: flex;
        align-items: center;
        cursor: pointer;
        position: relative;
        padding-left: 32px;
        user-select: none;
        font-size: 14px;
        color: #4b5563;
        transition: color 0.2s ease;
    }
    
    .brand-checkbox:hover {
        color: #1a1a1a;
    }
    
    .brand-checkbox input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }
    
    .checkmark {
        position: absolute;
        left: 0;
        height: 20px;
        width: 20px;
        background-color: white;
        border: 2px solid #d1d5db;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    
    .brand-checkbox:hover .checkmark {
        border-color: #2563eb;
    }
    
    .brand-checkbox input:checked ~ .checkmark {
        background-color: #2563eb;
        border-color: #2563eb;
    }
    
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
        left: 6px;
        top: 2px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
    
    .brand-checkbox input:checked ~ .checkmark:after {
        display: block;
    }
    
    /* Sort Options */
    .sort-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .sort-radio {
        display: flex;
        align-items: center;
        cursor: pointer;
        position: relative;
        padding-left: 32px;
        user-select: none;
        font-size: 14px;
        color: #4b5563;
        transition: color 0.2s ease;
    }
    
    .sort-radio:hover {
        color: #1a1a1a;
    }
    
    .sort-radio input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }
    
    .radio-custom {
        position: absolute;
        left: 0;
        height: 20px;
        width: 20px;
        background-color: white;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        transition: all 0.2s ease;
    }
    
    .sort-radio:hover .radio-custom {
        border-color: #2563eb;
    }
    
    .sort-radio input:checked ~ .radio-custom {
        background-color: white;
        border-color: #2563eb;
    }
    
    .radio-custom:after {
        content: "";
        position: absolute;
        display: none;
        top: 4px;
        left: 4px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #2563eb;
    }
    
    .sort-radio input:checked ~ .radio-custom:after {
        display: block;
    }
    
    /* Action Buttons */
    .filter-actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 24px;
    }
    
    .btn-filter,
    .btn-reset {
        width: 100%;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-filter {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
    
    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
    }
    
    .btn-reset {
        background: white;
        color: #6b7280;
        border: 1px solid #e5e7eb;
    }
    
    .btn-reset:hover {
        background: #f9fafb;
        color: #1a1a1a;
        border-color: #d1d5db;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .filter-section {
            margin-bottom: 20px;
            padding-bottom: 20px;
        }
        
        .brand-list-wrapper {
            max-height: 200px;
        }
    }
</style>

@push('js')
    <script>
        $(document).ready(function() {
            // Toggle chip active state
            $('.chip-label').on('click', function() {
                $(this).toggleClass('active');
            });
            
            // Handle subcategory radio change
            $('.subcategory-radio').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#dcd').prop('checked', false);
                }
            });
        });
    </script>
@endpush