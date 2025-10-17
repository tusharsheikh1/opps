@extends('layouts.admin.e-commerce.app')

@section('title')
    @isset($product)
        Edit Product
    @else
        Add New Product
    @endisset
@endsection

@push('css')
    <link rel="stylesheet" href="/assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" />
    <link rel="stylesheet" href="/assets/plugins/summernote/summernote-bs4.min.css">
    <style>
        .dropify-wrapper .dropify-message p {
            font-size: initial;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff;
            border-color: #006fe6;
            color: #fff;
        }
        .attribute-details {
            display: none;
        }
        .color-swatch {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #ddd;
            margin-right: 10px;
        }
        .color-variation-item {
            transition: all 0.3s ease;
        }
        .color-variation-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .color-images-area {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
        }
        .attribute-item {
            transition: all 0.2s ease;
        }
        .attribute-item:hover {
            background-color: #f8f9fa !important;
        }
    </style>
@endpush

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    @isset($product)
                        Edit Product
                    @else
                        Add New Product
                    @endisset
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.product.index') }}">Products</a></li>
                    <li class="breadcrumb-item active">
                        @isset($product)
                            Edit Product
                        @else
                            Add Product
                        @endisset
                    </li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ $error }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endforeach
        @endif

        <form action="{{ isset($product) ? route('admin.product.update', $product->id) : route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @isset($product)
                @method('PUT')
                <input type="hidden" value="{{ $product->id }}" id="productId">
            @endisset

            <div class="row">
                <!-- Left Column -->
                <div class="col-md-8">
                    <!-- General Information -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle"></i> General Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Product Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ $product->title ?? old('title') }}" required placeholder="Enter product title">
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label for="short_description">Short Description</label>
                                <textarea name="short_description" id="short_description" rows="3" class="form-control @error('short_description') is-invalid @enderror" placeholder="Brief product description">{{ $product->short_description ?? old('short_description') }}</textarea>
                                @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label for="full_description">Full Description <span class="text-danger">*</span></label>
                                <textarea name="full_description" id="full_description" class="form-control @error('full_description') is-invalid @enderror">{{ $product->full_description ?? old('full_description') }}</textarea>
                                @error('full_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pricing & Inventory -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-dollar-sign"></i> Pricing & Inventory</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="buying_price">Buying Price</label>
                                    <input type="number" step="0.01" name="buying_price" id="buying_price" class="form-control @error('buying_price') is-invalid @enderror" value="{{ $product->buying_price ?? old('buying_price') }}" placeholder="0.00">
                                    @error('buying_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="regular_price">Regular Price <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="regular_price" id="regular_price" class="form-control @error('regular_price') is-invalid @enderror" value="{{ $product->regular_price ?? old('regular_price') }}" required placeholder="0.00">
                                    @error('regular_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="dis_type">Discount Type</label>
                                    <select name="dis_type" id="dis_type" class="form-control @error('dis_type') is-invalid @enderror">
                                        <option value="1" @isset($product) {{ $product->dis_type == '1' ? 'selected' : '' }} @endisset>Fixed Amount</option>
                                        <option value="2" @isset($product) {{ $product->dis_type == '2' ? 'selected' : '' }} @endisset>Percentage %</option>
                                    </select>
                                    @error('dis_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                @php
                                    $discount_price_value = '';
                                    if (isset($product)) {
                                        if ($product->dis_type == '2') {
                                            $discount_price_value = ($product->discount_price > 0) ? (($product->regular_price - $product->discount_price) / $product->regular_price) * 100 : '';
                                        } else {
                                            $discount_price_value = ($product->discount_price > 0) ? $product->regular_price - $product->discount_price : '';
                                        }
                                    }
                                @endphp

                                <div class="col-md-6 form-group">
                                    <label for="discount_price">Discount Amount</label>
                                    <input type="number" step="0.01" name="discount_price" id="discount_price" class="form-control @error('discount_price') is-invalid @enderror" value="{{ $discount_price_value ?? old('discount_price') }}" placeholder="0.00">
                                    @error('discount_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="sku">SKU</label>
                                    <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ $product->sku ?? old('sku') }}" placeholder="Product SKU">
                                    @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="quantity">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ $product->quantity ?? old('quantity') }}" required min="0">
                                    <small class="form-text text-muted">For simple products. Variable product stock is auto-calculated.</small>
                                    @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Variations & Stock Management -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-boxes"></i> Variations & Stock Management</h3>
                        </div>
                        <div class="card-body">
                            <!-- Color Variations Section -->
                            <div class="form-group">
                                <label for="select_color"><i class="fas fa-palette"></i> Colors</label>
                                <small class="form-text text-muted mb-2">Add color variations with individual stock quantities and prices</small>
                                <select id="select_color" class="form-control">
                                    <option value="">Select a color to add</option>
                                    @foreach ($colors as $color)
                                        <option value="{{ $color->name.','.$color->id.','.$color->code }}">{{ $color->name }}</option>
                                    @endforeach
                                </select>
                                <div id="increment_color" class="mt-3">
                                    @isset($product)
                                        @foreach($colors_product as $pro_color)
                                            <div class="color-variation-item border rounded p-3 mb-3" data-color-id="{{$pro_color->id}}">
                                                <div class="row align-items-center">
                                                    <div class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="color-swatch" style="background-color: {{$pro_color->code}};"></div>
                                                            <strong>{{$pro_color->name}}</strong>
                                                        </div>
                                                        <input type="hidden" name="colors[]" value="{{$pro_color->id}}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="small mb-1">Stock Quantity *</label>
                                                        <input class="form-control form-control-sm color-qty-input" type="number" placeholder="Qty" name="color_quantits[]" value="{{$pro_color->qnty}}" required min="0">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="small mb-1">Price Adjustment</label>
                                                        <input class="form-control form-control-sm" type="number" step="0.01" placeholder="+ Price" name="color_prices[]" value="{{$pro_color->price}}">
                                                    </div>
                                                    <div class="col-md-3 text-right">
                                                        <button type="button" class="btn btn-sm btn-info upload-color-images" data-color-id="{{$pro_color->id}}">
                                                            <i class="fas fa-images"></i> Images
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger remove_color_item">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <!-- Color-specific image upload area -->
                                                <div class="color-images-area mt-3" style="display:none;">
                                                    <label class="small"><i class="fas fa-camera"></i> Images for {{$pro_color->name}}</label>
                                                    <div class="color-images-container">
                                                        <input type="file" class="form-control-file" name="color_images[{{$pro_color->id}}][]" multiple accept="image/*">
                                                    </div>
                                                    @php
                                                        $colorImages = $product->images->where('color_attri', $pro_color->id);
                                                    @endphp
                                                    @if($colorImages->count() > 0)
                                                        <div class="mt-2">
                                                            <small class="text-muted">Existing images:</small>
                                                            <div class="d-flex flex-wrap mt-2">
                                                                @foreach($colorImages as $img)
                                                                    <div class="position-relative mr-2 mb-2">
                                                                        <img src="{{ asset('uploads/product/'.$img->name) }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 2px solid #ddd;">
                                                                        <a href="{{ route('admin.idelte', $img->id) }}" class="btn btn-sm btn-danger position-absolute" style="top: -5px; right: -5px; padding: 2px 6px;" onclick="return confirm('Delete this image?')">
                                                                            <i class="fas fa-times"></i>
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endisset
                                </div>
                                <small class="text-info"><i class="fas fa-info-circle"></i> Total stock = Sum of all color quantities + attribute quantities</small>
                            </div>
                            
                            <hr class="my-4"/>
                            
                            <!-- Attribute Variations Section -->
                            <div id="attribute_section">
                                <label><i class="fas fa-tags"></i> Attributes (Size, Material, etc.)</label>
                                <small class="form-text text-muted mb-3">Add attribute variations with individual stock quantities and prices</small>
                                @php
                                    $product_attributes_map = [];
                                    if (isset($product)) {
                                        foreach($product->attributes_values as $val) {
                                            $product_attributes_map[$val->id] = [
                                                'qnty' => $val->pivot->qnty ?? '',
                                                'price' => $val->pivot->price ?? ''
                                            ];
                                        }
                                    }
                                @endphp

                                @if(isset($attributes) && count($attributes) > 0)
                                    @foreach($attributes as $attribute)
                                    <div class="mb-4 p-3 border rounded bg-light">
                                        <h6 class="font-weight-bold"><i class="fas fa-tag"></i> {{ $attribute->name }}</h6>
                                        <hr class="mt-2 mb-3">
                                        <div class="row">
                                            @foreach($attribute->values as $key => $value)
                                                @php
                                                    $isChecked = isset($product_attributes_map[$value->id]);
                                                    $qnty = $isChecked ? $product_attributes_map[$value->id]['qnty'] : '';
                                                    $price = $isChecked ? $product_attributes_map[$value->id]['price'] : '';
                                                @endphp
                                                <div class="col-md-6 mb-3">
                                                    <div class="attribute-item p-2 border rounded bg-white">
                                                        <div class="form-check">
                                                            <input class="form-check-input attribute-checkbox" type="checkbox" name="attributes[]" value="{{ $value->id }}" id="attr_{{ $value->id }}" {{ $isChecked ? 'checked' : '' }}>
                                                            <label class="form-check-label font-weight-bold" for="attr_{{ $value->id }}">
                                                                {{ $value->name }}
                                                            </label>
                                                        </div>
                                                        <div class="attribute-details row mt-2" style="{{ $isChecked ? '' : 'display: none;' }}">
                                                            <div class="col-6">
                                                                <label class="small mb-1">Stock Qty *</label>
                                                                <input type="number" name="attributes_quantits[]" class="form-control form-control-sm attr-qty-input" placeholder="Quantity" value="{{ $qnty }}" {{ $isChecked ? '' : 'disabled' }} min="0">
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="small mb-1">Price Adj.</label>
                                                                <input type="number" step="0.01" name="attribute_prices[]" class="form-control form-control-sm" placeholder="+ Price" value="{{ $price }}" {{ $isChecked ? '' : 'disabled' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> No attributes defined. <a href="{{ route('admin.attribute.index') }}" target="_blank">Create attributes</a> to add variations like Size, Material, etc.
                                    </div>
                                @endif
                            </div>
                            
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="fas fa-calculator"></i> <strong>Stock Calculation:</strong> 
                                <span id="total-stock-display">Total stock will be calculated automatically</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-4">
                    <!-- Publish Card -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-cog"></i> Publish</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="status" name="status" @isset($product) {{ $product->status ? 'checked' : '' }} @else checked @endisset>
                                    <label class="custom-control-label" for="status">Publish Product</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="shipping_charge">Shipping</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="shipping_charge" name="shipping_charge" value="1" @isset($product) {{ $product->shipping_charge ? 'checked' : '' }} @else checked @endisset>
                                    <label class="custom-control-label" for="shipping_charge">Enable Shipping Charge</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="prdct_extra_msg">Special Message</label>
                                <input type="text" name="prdct_extra_msg" id="prdct_extra_msg" class="form-control @error('prdct_extra_msg') is-invalid @enderror" value="{{ $product->prdct_extra_msg ?? old('prdct_extra_msg') }}" placeholder="e.g., Limited Stock!">
                                @error('prdct_extra_msg')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="card-footer">
                             <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i>
                                @isset($product)
                                    Update Product
                                @else
                                    Save Product
                                @endisset
                            </button>
                        </div>
                    </div>
                    
                    <!-- Categorization Card -->
                    <div class="card card-primary card-outline">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-folder"></i> Categorization</h3></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="brand">Brand <span class="text-danger">*</span></label>
                                <select name="brand" id="brand" class="form-control select2 @error('brand') is-invalid @enderror" required>
                                    <option value="">Select Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" @isset($product) {{ $brand->id == $product->brand_id ? 'selected' : '' }} @endisset>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label for="category">Categories <span class="text-danger">*</span></label>
                                <select name="categories[]" id="category" multiple class="form-control select2 @error('categories') is-invalid @enderror" required>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @isset($product) @foreach($product->categories as $pro_cat) {{ $category->id == $pro_cat->id ? 'selected' : '' }} @endforeach @endisset>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('categories')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label for="sub_category">Sub Categories</label>
                                <select name="sub_categories[]" id="sub_category" class="form-control select2" multiple></select>
                            </div>
                             <div class="form-group">
                                <label for="tags">Tags</label>
                                <select name="tags[]" id="tags" multiple class="form-control select2">
                                      @foreach ($tags as $tag)
                                        <option value="{{$tag->id}}" @isset($product) @foreach($product->tags as $pro_tag) {{$tag->id == $pro_tag->id ? 'selected':''}} @endforeach @endisset>{{$tag->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Media Card -->
                    <div class="card card-primary card-outline">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-images"></i> Media</h3></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="image">Main Image <span class="text-danger">*</span></label>
                                <input type="file" name="image" id="image" class="form-control dropify @error('image') is-invalid @enderror" data-default-file="@isset($product)/uploads/product/{{$product->image}}@endisset">
                                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="images">General Gallery Images</label>
                                <small class="form-text text-muted">Images not tied to any specific color</small>
                                <div class="input-group mt-2" id="gallery_increment">
                                     <input type="file" class="form-control" name="images[]" accept="image/*">
                                     <div class="input-group-append" id="add_gallery_item" style="cursor:pointer">
                                        <span class="input-group-text bg-success"><i class="fas fa-plus"></i> Add More</span>
                                    </div>
                                </div>
                                 @isset($product)
                                    @php
                                        $generalImages = $product->images->whereNull('color_attri');
                                    @endphp
                                    @if($generalImages->count() > 0)
                                        <div class="mt-3">
                                            <small class="text-muted">Existing general images:</small>
                                            <div class="d-flex flex-wrap mt-2">
                                                @foreach($generalImages as $image)
                                                    <div class="position-relative mr-2 mb-2">
                                                        <img src="{{ asset('uploads/product/'.$image->name) }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 2px solid #ddd;">
                                                        <a href="{{ route('admin.idelte', $image->id) }}" class="btn btn-sm btn-danger position-absolute" style="top: -5px; right: -5px; padding: 2px 6px;" onclick="return confirm('Delete this image?')">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endisset
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@push('js')
    <script src="/assets/plugins/select2/js/select2.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
    <script src="/assets/plugins/summernote/summernote-bs4.min.js"></script>
    
    <script>
        $(document).ready(function () {
            // Initialize plugins
            $('.select2').select2({ theme: 'bootstrap4' });
            $('.dropify').dropify();
            $('#full_description, #short_description').summernote({ height: 150 });

            // Calculate and display total stock
            function calculateTotalStock() {
                let total = 0;
                let hasVariations = false;
                
                // Sum color quantities
                $('input[name="color_quantits[]"]').each(function() {
                    let val = parseInt($(this).val()) || 0;
                    if(val > 0) {
                        total += val;
                        hasVariations = true;
                    }
                });
                
                // Sum attribute quantities
                $('input[name="attributes_quantits[]"]:not(:disabled)').each(function() {
                    let val = parseInt($(this).val()) || 0;
                    if(val > 0) {
                        total += val;
                        hasVariations = true;
                    }
                });
                
                // Update display
                if(hasVariations && total > 0) {
                    $('#total-stock-display').html(`<strong>Total Stock: ${total} units</strong> (from all variations)`);
                    $('#quantity').val(total).prop('readonly', true);
                } else {
                    $('#total-stock-display').html('Add color or attribute variations to calculate total stock automatically');
                    $('#quantity').prop('readonly', false);
                }
            }

            // Dynamic color fields with enhanced functionality
            $('#select_color').on('change', function() {
                if (!$(this).val()) return;
                let colorData = $(this).val().split(',');
                let colorName = colorData[0];
                let colorId = colorData[1];
                let colorCode = colorData[2];

                let colorHtml = `
                <div class="color-variation-item border rounded p-3 mb-3" data-color-id="${colorId}">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="color-swatch" style="background-color: ${colorCode};"></div>
                                <strong>${colorName}</strong>
                            </div>
                            <input type="hidden" name="colors[]" value="${colorId}">
                        </div>
                        <div class="col-md-3">
                            <label class="small mb-1">Stock Quantity *</label>
                            <input class="form-control form-control-sm color-qty-input" type="number" placeholder="Qty" name="color_quantits[]" value="" required min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="small mb-1">Price Adjustment</label>
                            <input class="form-control form-control-sm" type="number" step="0.01" placeholder="+ Price" name="color_prices[]" value="">
                        </div>
                        <div class="col-md-3 text-right">
                            <button type="button" class="btn btn-sm btn-info upload-color-images" data-color-id="${colorId}">
                                <i class="fas fa-images"></i> Images
                            </button>
                            <button type="button" class="btn btn-sm btn-danger remove_color_item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="color-images-area mt-3" style="display:none;">
                        <label class="small"><i class="fas fa-camera"></i> Images for ${colorName}</label>
                        <div class="color-images-container">
                            <input type="file" class="form-control-file" name="color_images[${colorId}][]" multiple accept="image/*">
                        </div>
                    </div>
                </div>`;
                
                $('#increment_color').append(colorHtml);
                $(this).val(''); // Reset select
                calculateTotalStock();
            });

            // Remove color variation
            $(document).on('click', '.remove_color_item', function() {
                if(confirm('Remove this color variation?')) {
                    $(this).closest('.color-variation-item').fadeOut(300, function() {
                        $(this).remove();
                        calculateTotalStock();
                    });
                }
            });

            // Toggle color image upload area
            $(document).on('click', '.upload-color-images', function() {
                $(this).closest('.color-variation-item').find('.color-images-area').slideToggle();
            });

            // Update stock calculation on quantity change
            $(document).on('input', '.color-qty-input, .attr-qty-input', function() {
                calculateTotalStock();
            });

            // Attribute checkbox logic
            $(document).on('change', '.attribute-checkbox', function() {
                const detailsDiv = $(this).closest('.attribute-item').find('.attribute-details');
                const detailInputs = detailsDiv.find('input');

                if ($(this).is(':checked')) {
                    detailsDiv.slideDown(200);
                    detailInputs.prop('disabled', false).prop('required', true);
                } else {
                    detailsDiv.slideUp(200);
                    detailInputs.prop('disabled', true).prop('required', false);
                    detailInputs.val('');
                }
                calculateTotalStock();
            });

            // Gallery images
            $('#add_gallery_item').on('click', function() {
                let galleryHtml = `
                <div class="input-group mt-2">
                    <input type="file" class="form-control" name="images[]" accept="image/*">
                    <div class="input-group-append remove_gallery_item" style="cursor:pointer">
                        <span class="input-group-text bg-danger"><i class="fas fa-minus"></i> Remove</span>
                    </div>
                </div>`;
                $('#gallery_increment').append(galleryHtml);
            });
            
            $(document).on('click', '.remove_gallery_item', function() {
                $(this).parent().fadeOut(300, function() {
                    $(this).remove();
                });
            });

            // Fetch SubCategories on Category change
            function fetchSubCategories(categoryIds) {
                if (!categoryIds || categoryIds.length === 0) {
                    $('#sub_category').html('').select2({theme: 'bootstrap4'});
                    return;
                }
                
                $.ajax({
                    type: 'POST',
                    url: '{{ url("/admin/get/sub-categories") }}',
                    data: {
                        'ids': categoryIds,
                        '_token': '{{ csrf_token() }}',
                    },
                    dataType: "JSON",
                    success: function (response) {
                        let options = '';
                        let selectedSubCategories = [];
                        @if(isset($product))
                            selectedSubCategories = @json($product->sub_categories->pluck('id'));
                        @endif

                        $.each(response, function (key, val) {
                            let isSelected = selectedSubCategories.includes(val.id) ? 'selected' : '';
                            options += `<option value="${val.id}" ${isSelected}>${val.name}</option>`;
                        });
                        $('#sub_category').html(options).select2({theme: 'bootstrap4'});
                    }
                });
            }

            $('#category').on('change', function() {
                fetchSubCategories($(this).val());
            });

            // Initial calculations and setup
            @if(isset($product))
                fetchSubCategories($('#category').val());
                calculateTotalStock();
            @endif

            // Form validation before submit
            $('form').on('submit', function(e) {
                let hasColors = $('input[name="colors[]"]').length > 0;
                let hasAttributes = $('input[name="attributes[]"]:checked').length > 0;
                
                if(hasColors || hasAttributes) {
                    let totalStock = 0;
                    
                    $('input[name="color_quantits[]"]').each(function() {
                        totalStock += parseInt($(this).val()) || 0;
                    });
                    
                    $('input[name="attributes_quantits[]"]:not(:disabled)').each(function() {
                        totalStock += parseInt($(this).val()) || 0;
                    });
                    
                    if(totalStock === 0) {
                        e.preventDefault();
                        alert('Please add stock quantities for your variations!');
                        return false;
                    }
                }
            });
        });
    </script>
@endpush