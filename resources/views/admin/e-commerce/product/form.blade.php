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
        .color-variation-card {
            transition: all 0.3s ease;
            border-left: 4px solid #007bff;
        }
        .color-variation-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .size-only-card {
            border-left: 4px solid #17a2b8;
        }
        .size-stock-input {
            max-width: 100px;
        }
        .stock-matrix-table {
            width: 100%;
            margin-top: 15px;
        }
        .stock-matrix-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        .stock-matrix-table td {
            padding: 8px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        .color-images-area {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-top: 15px;
        }
        .attribute-item {
            transition: all 0.2s ease;
        }
        .attribute-item:hover {
            background-color: #f8f9fa !important;
        }
        .stock-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .stock-summary h4 {
            color: white;
            margin-bottom: 10px;
        }
        .stock-badge {
            background-color: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
            margin-right: 10px;
        }
        .color-header {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            border-bottom: 2px solid #007bff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .remove-color-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .price-display {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .price-display h5 {
            color: white;
            margin: 0;
            font-size: 18px;
        }
        .price-display .original-price {
            text-decoration: line-through;
            opacity: 0.8;
            font-size: 14px;
        }
        .price-display .discount-percentage {
            background-color: rgba(255,255,255,0.3);
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin-left: 10px;
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

        <form action="{{ isset($product) ? route('admin.product.update', $product->id) : route('admin.product.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            @isset($product)
                @method('PUT')
                <input type="hidden" value="{{ $product->id }}" id="productId">
            @endisset

            <div class="row">
                <div class="col-md-8">
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

                            <div id="priceDisplay" class="price-display" style="display: none;">
                                <h5>
                                    <i class="fas fa-tag"></i> Final Price: 
                                    <span id="finalPrice">৳0.00</span>
                                    <span id="originalPrice" class="original-price"></span>
                                    <span id="discountPercentage" class="discount-percentage"></span>
                                </h5>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6 form-group">
                                    <label for="sku">SKU</label>
                                    <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ $product->sku ?? old('sku') }}" placeholder="Product SKU">
                                    @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="quantity">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ $product->quantity ?? old('quantity') ?? 0 }}" required min="0">
                                    <small class="form-text text-muted">Enter total stock or add variations.</small>
                                    @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-expand-arrows-alt"></i> Size-Only Variations (No Color)</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Use this section if your product has size variations but no color variations.
                            </div>

                            <div id="size_only_section" class="card card-body size-only-card">
                                <h5 class="mb-3"><i class="fas fa-ruler-combined"></i> Size Variations</h5>
                                <table class="stock-matrix-table table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Size</th>
                                            <th>Stock Quantity</th>
                                            <th>Price Adjustment</th>
                                        </tr>
                                    </thead>
                                    <tbody id="size_only_rows">
                                        @isset($product)
                                            @php
                                                // Get size-only variations (where color_id is null)
                                                $sizeOnlyVariations = [];
                                                if(isset($colorSizeStock)) {
                                                    foreach($colorSizeStock as $item) {
                                                        if($item->color_id === null) {
                                                            $sizeOnlyVariations[$item->size_id] = $item;
                                                        }
                                                    }
                                                }
                                            @endphp

                                            @foreach($sizes as $size)
                                                @php
                                                    $sizeData = $sizeOnlyVariations[$size->id] ?? null;
                                                    $quantity = $sizeData->quantity ?? 0;
                                                    $price = $sizeData->price ?? 0;
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ $size->name }}</strong></td>
                                                    <td>
                                                        <input type="number" 
                                                               class="form-control form-control-sm size-stock-input size-only-qty-input" 
                                                               name="size_only_stock[{{ $size->id }}][quantity]" 
                                                               value="{{ $quantity }}" 
                                                               min="0" 
                                                               placeholder="Qty">
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               step="0.01" 
                                                               class="form-control form-control-sm size-stock-input" 
                                                               name="size_only_stock[{{ $size->id }}][price]" 
                                                               value="{{ $price }}" 
                                                               placeholder="+ Price">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            @foreach($sizes as $size)
                                                <tr>
                                                    <td><strong>{{ $size->name }}</strong></td>
                                                    <td>
                                                        <input type="number" 
                                                               class="form-control form-control-sm size-stock-input size-only-qty-input" 
                                                               name="size_only_stock[{{ $size->id }}][quantity]" 
                                                               value="0" 
                                                               min="0" 
                                                               placeholder="Qty">
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               step="0.01" 
                                                               class="form-control form-control-sm size-stock-input" 
                                                               name="size_only_stock[{{ $size->id }}][price]" 
                                                               value="0" 
                                                               placeholder="+ Price">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-th"></i> Color & Size Stock Management</h3>
                        </div>
                        <div class="card-body">
                            <div class="stock-summary" id="stockSummary" style="display: none;">
                                <h4><i class="fas fa-chart-line"></i> Stock Summary</h4>
                                <div id="stockSummaryContent"></div>
                            </div>

                            <div class="form-group">
                                <label for="select_color"><i class="fas fa-palette"></i> Add Color Variation</label>
                                <small class="form-text text-muted mb-2">Select a color to add size variations with individual stock quantities</small>
                                <select id="select_color" class="form-control select2">
                                    <option value="">Select a color to add</option>
                                    @foreach ($colors as $color)
                                        <option value="{{ $color->id }}" data-name="{{ $color->name }}" data-code="{{ $color->code }}">{{ $color->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="color_variations_container">
                                @isset($product)
                                    @isset($colorSizeStock)
                                        @php
                                            // Group stock data by color for easier processing (excluding null color_id)
                                            $colorGroups = [];
                                            foreach($colorSizeStock as $item) {
                                                if($item->color_id !== null) {
                                                    if(!isset($colorGroups[$item->color_id])) {
                                                        $colorGroups[$item->color_id] = [
                                                            'color_name' => $item->color_name,
                                                            'color_code' => $item->color_code,
                                                            'sizes_data' => []
                                                        ];
                                                    }
                                                    $colorGroups[$item->color_id]['sizes_data'][$item->size_id] = $item;
                                                }
                                            }
                                        @endphp

                                        @foreach($colorGroups as $colorId => $colorData)
                                            <div class="color-variation-card card mb-4" data-color-id="{{ $colorId }}">
                                                <div class="card-body position-relative">
                                                    <button type="button" class="btn btn-sm btn-danger remove-color-btn remove-color-variation">
                                                        <i class="fas fa-trash"></i> Remove Color
                                                    </button>
                                                    
                                                    <div class="color-header">
                                                        <div class="d-flex align-items-center">
                                                            <div class="color-swatch" style="background-color: {{ $colorData['color_code'] }};"></div>
                                                            <h5 class="mb-0">{{ $colorData['color_name'] }}</h5>
                                                        </div>
                                                    </div>

                                                    <table class="stock-matrix-table table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Size</th>
                                                                <th>Stock Quantity *</th>
                                                                <th>Price Adjustment</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="size-rows-container">
                                                            @foreach($sizes as $size)
                                                                @php
                                                                    $stockData = $colorData['sizes_data'][$size->id] ?? null;
                                                                    $quantity = $stockData->quantity ?? 0;
                                                                    $price = $stockData->price ?? 0;
                                                                @endphp
                                                                <tr class="size-row">
                                                                    <td><strong>{{ $size->name }}</strong></td>
                                                                    <td>
                                                                        <input type="number" 
                                                                               class="form-control form-control-sm size-stock-input color-size-qty-input" 
                                                                               name="color_size_stock[{{ $colorId }}][{{ $size->id }}][quantity]" 
                                                                               value="{{ $quantity }}" 
                                                                               min="0" 
                                                                               required
                                                                               placeholder="Qty">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" 
                                                                               step="0.01" 
                                                                               class="form-control form-control-sm size-stock-input" 
                                                                               name="color_size_stock[{{ $colorId }}][{{ $size->id }}][price]" 
                                                                               value="{{ $price }}" 
                                                                               placeholder="+ Price">
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>

                                                    <div class="mt-3">
                                                        <button type="button" class="btn btn-sm btn-info toggle-color-images">
                                                            <i class="fas fa-images"></i> Manage Images for {{ $colorData['color_name'] }}
                                                        </button>
                                                        <div class="color-images-area" style="display:none;">
                                                            <label class="small font-weight-bold"><i class="fas fa-camera"></i> Upload Images</label>
                                                            <input type="file" class="form-control-file" name="color_images[{{ $colorId }}][]" multiple accept="image/*">
                                                            
                                                            @php
                                                                $colorImages = $product->images->where('color_attri', $colorId);
                                                            @endphp
                                                            @if($colorImages->count() > 0)
                                                                <div class="mt-3">
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
                                                </div>
                                            </div>
                                        @endforeach
                                    @endisset
                                @endisset
                            </div>

                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i> <strong>How it works:</strong> Each color can have multiple sizes with individual stock quantities. Total stock is automatically calculated.
                            </div>
                        </div>
                    </div>

                    <div class="card card-warning card-outline" id="attribute_section">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-tags"></i> Attribute Variations</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Attributes can be combined with color-size variations or used independently.
                            </div>
                            
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
                                    <i class="fas fa-info-circle"></i> No attributes defined. <a href="{{ route('admin.attribute.index') }}" target="_blank">Create attributes</a> first.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
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
                            
                            {{-- START: ADDED HTML --}}
                            <div class="form-group">
                                <label for="mini_category">Mini Categories</label>
                                <select name="mini_categories[]" id="mini_category" class="form-control select2" multiple></select>
                            </div>
                            <div class="form-group">
                                <label for="extra_category">Extra Categories</label>
                                <select name="extra_categories[]" id="extra_category" class="form-control select2" multiple></select>
                            </div>
                            {{-- END: ADDED HTML --}}

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

            // All available sizes (from backend)
            const allSizes = @json($sizes);

            // Calculate and display final price after discount
            function calculateFinalPrice() {
                let regularPrice = parseFloat($('#regular_price').val()) || 0;
                let discountValue = parseFloat($('#discount_price').val()) || 0;
                let discountType = $('#dis_type').val();
                
                if(regularPrice > 0 && discountValue > 0) {
                    let finalPrice = regularPrice;
                    let discountAmount = 0;
                    let discountPercentage = 0;
                    
                    if(discountType == '2') {
                        // Percentage discount
                        discountPercentage = discountValue;
                        discountAmount = (regularPrice * discountValue) / 100;
                        finalPrice = regularPrice - discountAmount;
                    } else {
                        // Fixed amount discount
                        discountAmount = discountValue;
                        finalPrice = regularPrice - discountValue;
                        discountPercentage = ((discountAmount / regularPrice) * 100).toFixed(2);
                    }
                    
                    if(finalPrice < 0) {
                        finalPrice = 0;
                    }
                    
                    $('#priceDisplay').fadeIn();
                    $('#finalPrice').text('$' + finalPrice.toFixed(2));
                    $('#originalPrice').text('$' + regularPrice.toFixed(2));
                    $('#discountPercentage').text(discountPercentage + '% OFF');
                } else {
                    $('#priceDisplay').fadeOut();
                }
            }

            // Calculate and display total stock
            function calculateTotalStock() {
                let totalFromVariations = 0;
                let hasColorSizeVariations = false;
                let hasSizeOnlyVariations = false;
                let hasAttributes = false;
                let colorStockSummary = {};
                let sizeOnlyStock = 0;
                let attributeStock = 0;
                
                // Sum color-size quantities
                $('.color-size-qty-input').each(function() {
                    let val = parseInt($(this).val()) || 0;
                    if(val > 0) {
                        totalFromVariations += val;
                        hasColorSizeVariations = true;
                        
                        let colorCard = $(this).closest('.color-variation-card');
                        let colorName = colorCard.find('.color-header h5').text().trim();
                        if(!colorStockSummary[colorName]) {
                            colorStockSummary[colorName] = 0;
                        }
                        colorStockSummary[colorName] += val;
                    }
                });
                
                // Sum size-only quantities
                $('.size-only-qty-input').each(function() {
                    let val = parseInt($(this).val()) || 0;
                    if(val > 0) {
                        totalFromVariations += val;
                        sizeOnlyStock += val;
                        hasSizeOnlyVariations = true;
                    }
                });

                // Sum attribute quantities
                $('input[name="attributes_quantits[]"]:not(:disabled)').each(function() {
                    let val = parseInt($(this).val()) || 0;
                    if(val > 0) {
                        totalFromVariations += val;
                        attributeStock += val;
                        hasAttributes = true;
                    }
                });
                
                let hasAnyVariation = hasColorSizeVariations || hasSizeOnlyVariations || hasAttributes;
                let finalTotal = 0;

                if (hasAnyVariation) {
                    // Variations exist: lock the field and set the calculated value
                    $('#quantity').val(totalFromVariations).prop('readonly', true);
                    $('#quantity').next('small.form-text').text('Auto-calculated from variations');
                    finalTotal = totalFromVariations;
                } else {
                    // No variations: unlock the field.
                    $('#quantity').prop('readonly', false);
                    $('#quantity').next('small.form-text').text('Enter total stock for this simple product.');
                    // The total is whatever is in the field.
                    finalTotal = parseInt($('#quantity').val()) || 0;
                }
                
                // Update stock summary
                if(finalTotal > 0) {
                    let summaryHtml = `<div class="stock-badge"><strong>Total: ${finalTotal} units</strong></div>`;
                    
                    if(hasColorSizeVariations) {
                        for(let color in colorStockSummary) {
                            summaryHtml += `<div class="stock-badge">${color}: ${colorStockSummary[color]}</div>`;
                        }
                    }
                    
                    if(hasSizeOnlyVariations) {
                        summaryHtml += `<div class="stock-badge">Size-Only: ${sizeOnlyStock}</div>`;
                    }
                    
                    if(hasAttributes) {
                        summaryHtml += `<div class="stock-badge">Attributes: ${attributeStock}</div>`;
                    }
                    
                    $('#stockSummaryContent').html(summaryHtml);
                    $('#stockSummary').fadeIn();
                } else {
                    $('#stockSummary').fadeOut();
                }
            }

            // Add new color variation
            $('#select_color').on('change', function() {
                if (!$(this).val()) return;
                
                let colorId = $(this).val();
                let colorName = $(this).find(':selected').data('name');
                let colorCode = $(this).find(':selected').data('code');

                // Check if color already added
                if($(`.color-variation-card[data-color-id="${colorId}"]`).length > 0) {
                    alert('This color has already been added!');
                    $(this).val('').trigger('change');
                    return;
                }

                // Generate size rows
                let sizeRows = '';
                allSizes.forEach(function(size) {
                    sizeRows += `
                    <tr class="size-row">
                        <td><strong>${size.name}</strong></td>
                        <td>
                            <input type="number" 
                                   class="form-control form-control-sm size-stock-input color-size-qty-input" 
                                   name="color_size_stock[${colorId}][${size.id}][quantity]" 
                                   value="0" 
                                   min="0" 
                                   required
                                   placeholder="Qty">
                        </td>
                        <td>
                            <input type="number" 
                                   step="0.01" 
                                   class="form-control form-control-sm size-stock-input" 
                                   name="color_size_stock[${colorId}][${size.id}][price]" 
                                   value="0" 
                                   placeholder="+ Price">
                        </td>
                    </tr>`;
                });

                let colorHtml = `
                <div class="color-variation-card card mb-4" data-color-id="${colorId}">
                    <div class="card-body position-relative">
                        <button type="button" class="btn btn-sm btn-danger remove-color-btn remove-color-variation">
                            <i class="fas fa-trash"></i> Remove Color
                        </button>
                        
                        <div class="color-header">
                            <div class="d-flex align-items-center">
                                <div class="color-swatch" style="background-color: ${colorCode};"></div>
                                <h5 class="mb-0">${colorName}</h5>
                            </div>
                        </div>

                        <table class="stock-matrix-table table table-bordered">
                            <thead>
                                <tr>
                                    <th>Size</th>
                                    <th>Stock Quantity *</th>
                                    <th>Price Adjustment</th>
                                </tr>
                            </thead>
                            <tbody class="size-rows-container">
                                ${sizeRows}
                            </tbody>
                        </table>

                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-info toggle-color-images">
                                <i class="fas fa-images"></i> Manage Images for ${colorName}
                            </button>
                            <div class="color-images-area" style="display:none;">
                                <label class="small font-weight-bold"><i class="fas fa-camera"></i> Upload Images</label>
                                <input type="file" class="form-control-file" name="color_images[${colorId}][]" multiple accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>`;
                
                $('#color_variations_container').append(colorHtml);
                $(this).val('').trigger('change');
                calculateTotalStock();
            });

            // Remove color variation
            $(document).on('click', '.remove-color-variation', function() {
                if(confirm('Remove this entire color variation including all sizes?')) {
                    $(this).closest('.color-variation-card').fadeOut(300, function() {
                        $(this).remove();
                        calculateTotalStock();
                    });
                }
            });

            // Toggle color images area
            $(document).on('click', '.toggle-color-images', function() {
                $(this).closest('.card-body').find('.color-images-area').slideToggle();
            });

            // Update stock calculation on quantity change
            $(document).on('input', '#quantity, .color-size-qty-input, .size-only-qty-input, .attr-qty-input', function() {
                calculateTotalStock();
            });

            // Update price display on input change
            $('#regular_price, #discount_price, #dis_type').on('input change', function() {
                calculateFinalPrice();
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

                        // Trigger change to load mini categories if any are pre-selected
                        if (selectedSubCategories.length > 0) {
                                $('#sub_category').trigger('change');
                        }
                    }
                });
            }

            // START: ADDED JAVASCRIPT

            // Fetch MiniCategories on SubCategory change
            function fetchMiniCategories(subCategoryIds) {
                if (!subCategoryIds || subCategoryIds.length === 0) {
                    $('#mini_category').html('').select2({theme: 'bootstrap4'});
                    return;
                }
                
                $.ajax({
                    type: 'POST',
                    url: '{{ url("/admin/get/mini-categories") }}', // From admin.php routes
                    data: {
                        'ids': subCategoryIds,
                        '_token': '{{ csrf_token() }}',
                    },
                    dataType: "JSON",
                    success: function (response) {
                        let options = '';
                        let selectedMiniCategories = [];
                        @if(isset($product))
                            selectedMiniCategories = @json($product->mini_categories->pluck('id'));
                        @endif

                        $.each(response, function (key, val) {
                            let isSelected = selectedMiniCategories.includes(val.id) ? 'selected' : '';
                            options += `<option value="${val.id}" ${isSelected}>${val.name}</option>`;
                        });
                        $('#mini_category').html(options).select2({theme: 'bootstrap4'});
                        
                        // Trigger change to load extra categories if any are pre-selected
                        if (selectedMiniCategories.length > 0) {
                             $('#mini_category').trigger('change');
                        }
                    }
                });
            }

            // Fetch ExtraCategories on MiniCategory change
            function fetchExtraCategories(miniCategoryIds) {
                if (!miniCategoryIds || miniCategoryIds.length === 0) {
                    $('#extra_category').html('').select2({theme: 'bootstrap4'});
                    return;
                }
                
                $.ajax({
                    type: 'POST',
                    url: '{{ url("/admin/get/extra-categories") }}', // From admin.php routes
                    data: {
                        'ids': miniCategoryIds,
                        '_token': '{{ csrf_token() }}',
                    },
                    dataType: "JSON",
                    success: function (response) {
                        let options = '';
                        let selectedExtraCategories = [];
                        @if(isset($product))
                            selectedExtraCategories = @json($product->extra_categories->pluck('id'));
                        @endif

                        $.each(response, function (key, val) {
                            let isSelected = selectedExtraCategories.includes(val.id) ? 'selected' : '';
                            options += `<option value="${val.id}" ${isSelected}>${val.name}</option>`;
                        });
                        $('#extra_category').html(options).select2({theme: 'bootstrap4'});
                    }
                });
            }

            // END: ADDED JAVASCRIPT

            $('#category').on('change', function() {
                fetchSubCategories($(this).val());
                // When category changes, clear the children
                $('#mini_category').html('').select2({theme: 'bootstrap4'});
                $('#extra_category').html('').select2({theme: 'bootstrap4'});
            });

            // --- ADDED NEW LISTENERS ---
            $('#sub_category').on('change', function() {
                fetchMiniCategories($(this).val());
                // When sub-category changes, clear the child
                $('#extra_category').html('').select2({theme: 'bootstrap4'});
            });

            $('#mini_category').on('change', function() {
                fetchExtraCategories($(this).val());
            });
            // --- END OF NEW LISTENERS ---

            // Initial calculations and setup
            @if(isset($product))
                fetchSubCategories($('#category').val());
            @endif
            calculateTotalStock();
            calculateFinalPrice();

            // === START: FORM VALIDATION ===
            $('#productForm').on('submit', function(e) {
                // This checks the FINAL value of the quantity field.
                // It works for both simple products (user typed) and
                // variable products (auto-calculated).
                
                let totalStock = parseInt($('#quantity').val()) || 0;
                if(totalStock === 0) {
                    e.preventDefault(); // Stop the form from submitting
                    alert('Please add stock quantities! A product cannot be saved with zero total stock.');
                    
                    // Try to focus the user
                    if ($('#quantity').is('[readonly]')) {
                        // It's a variable product
                        $('#stockSummary').css('border', '2px solid red');
                    } else {
                        // It's a simple product
                        $('#quantity').focus().css('border', '2px solid red');
                    }
                    
                    return false;
                }
            });
            // === END: FORM VALIDATION ===
        });
    </script>
@endpush