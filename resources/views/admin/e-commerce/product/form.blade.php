@extends('layouts.admin.e-commerce.app')

@section('title')
    @isset($product)
        বই পণ্য সম্পাদনা করুন 
    @else 
        বই পণ্য যোগ করুন
    @endisset
@endsection

@push('css')
    <!-- Select2 -->
    <link rel="stylesheet" href="/assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog==" crossorigin="anonymous" />
    <link rel="stylesheet" href="/assets/plugins/summernote/summernote-bs4.min.css">
    <link rel="stylesheet" href="/assets/plugins/dropzone/min/dropzone.min.css">
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link type="text/css" rel="stylesheet" href="/assets/plugins/file-uploader/image-uploader.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #667eea;
            --primary-light: #f093fb;
            --secondary-color: #764ba2;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --border-color: #e5e7eb;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
        }

        body {
            font-family: 'Hind Siliguri', 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .content-wrapper {
            background: transparent;
        }

        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
        }

        .modern-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modern-card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
            padding: 1.5rem;
            border: none;
        }

        .modern-form-group {
            margin-bottom: 1.5rem;
        }

        .modern-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .modern-input {
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        .modern-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
            outline: none;
        }

        .modern-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .modern-select {
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        .modern-btn {
            border-radius: var(--radius-md);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modern-btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            box-shadow: var(--shadow-md);
        }

        .modern-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
            color: white;
        }

        .modern-btn-success {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
        }

        .modern-btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            color: white;
        }

        .modern-btn-info {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
        }

        .modern-section {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .modern-section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            cursor: pointer;
            padding: 1rem;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
        }

        .modern-section-header:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15), rgba(118, 75, 162, 0.15));
        }

        .modern-section-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .modern-section-icon {
            width: 24px;
            height: 24px;
            color: var(--primary-color);
        }

        .modern-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .modern-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .modern-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .modern-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .modern-slider {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        input:checked + .modern-slider:before {
            transform: translateX(26px);
        }

        .file-upload-modern {
            border: 2px dashed var(--border-color);
            border-radius: var(--radius-lg);
            padding: 2rem;
            text-align: center;
            background: rgba(255, 255, 255, 0.5);
            transition: all 0.2s ease;
        }

        .file-upload-modern:hover {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.05);
        }

        .price-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .price-card {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1rem;
            backdrop-filter: blur(10px);
        }

        .color-selector {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }

        .color-item {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid white;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-md);
        }

        .color-item:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-lg);
        }

        .breadcrumb-modern {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
        }

        .breadcrumb-modern a {
            color: white;
            text-decoration: none;
        }

        .breadcrumb-modern .active {
            color: rgba(255, 255, 255, 0.8);
        }

        .book-section {
            border-left: 4px solid #f59e0b;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
        }

        .media-section {
            border-left: 4px solid #06b6d4;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1), rgba(6, 182, 212, 0.05));
        }

        .alert-modern {
            border: none;
            border-radius: var(--radius-md);
            backdrop-filter: blur(10px);
        }

        .form-row-modern {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }

        .book-cover-highlight {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
            border: 2px dashed #f59e0b;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            text-align: center;
            position: relative;
        }

        .book-cover-highlight::before {
            content: "📚";
            font-size: 2rem;
            display: block;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .form-row-modern {
                grid-template-columns: 1fr;
            }
            
            .price-grid {
                grid-template-columns: 1fr;
            }
        }

        .dropify-wrapper .dropify-message p {
            font-size: initial;
        }

        .note-editor {
            box-shadow: none !important;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
        }

        .invalid-feedback {
            display: block;
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .is-invalid {
            border-color: var(--danger-color) !important;
        }

        .text-danger {
            color: var(--danger-color) !important;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-lg);
            backdrop-filter: blur(10px);
        }

        .step {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
            border-radius: var(--radius-md);
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .step.active {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
@endpush

@section('content')
<!-- কন্টেন্ট হেডার (পেজ হেডার) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-sm-6">
                <h1 class="text-white font-weight-bold">
                    @isset($product)
                        📖 বই পণ্য সম্পাদনা করুন 
                    @else 
                        📚 নতুন বই যোগ করুন
                    @endisset
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb breadcrumb-modern float-sm-right">
                    <li class="breadcrumb-item"><a href="{{routeHelper('dashboard')}}">🏠 হোম</a></li>
                    <li class="breadcrumb-item active">
                        @isset($product)
                            বই সম্পাদনা 
                        @else 
                            বই যোগ করুন
                        @endisset
                    </li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- মূল কন্টেন্ট -->
<section class="content">
    @if($errors->any())
        @foreach($errors->all() as $error)
            <div class="alert alert-danger alert-modern">
                <i class="fas fa-exclamation-triangle mr-2"></i>{{ $error }}
            </div>
        @endforeach
    @endif

    <!-- ধাপ নির্দেশক -->
    <div class="step-indicator">
        <div class="step active">📚 বইয়ের তথ্য</div>
        <div class="step active">💰 মূল্য নির্ধারণ</div>
        <div class="step active">📸 বইয়ের কভার</div>
        <div class="step active">🏷️ বিভাগ</div>
        <div class="step">⚙️ সেটিংস</div>
    </div>

    <div class="modern-card">
        <div class="modern-card-header">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-book mr-3"></i>
                        @isset($product)
                            বই পণ্য সম্পাদনা
                        @else 
                            ক্যাটালগে নতুন বই যোগ করুন
                        @endisset
                    </h3>
                </div>
                <div class="col-sm-6 text-right">
                    @isset($product)
                    <a href="{{routeHelper('product/'. $product->id)}}" class="modern-btn modern-btn-info">
                        <i class="fas fa-eye"></i>
                        বই প্রিভিউ
                    </a>
                    @endisset
                    <a href="{{routeHelper('product')}}" class="modern-btn modern-btn-danger">
                        <i class="fas fa-arrow-left"></i>
                        ক্যাটালগে ফিরুন
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <form class="col-lg-8" action="{{ isset($product) ? routeHelper('product/'.$product->id) : routeHelper('product') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @isset($product)
                    <input type="hidden" value="{{$product->id}}" id="id">
                    @method('PUT')
                @endisset
                <input type="hidden" value="book" name="ptypen">

                <div class="card-body p-4">
                    <!-- ১. বইয়ের তথ্য বিভাগ - সবচেয়ে গুরুত্বপূর্ণ -->
                    <div class="modern-section book-section">
                        <div class="modern-section-header" data-toggle="collapse" data-target="#bookDetails" aria-expanded="true">
                            <i class="fas fa-book-open modern-section-icon"></i>
                            <h4 class="modern-section-title">📚 বইয়ের তথ্য</h4>
                            <i class="fas fa-chevron-down ml-auto"></i>
                        </div>
                        
                        <div class="collapse show" id="bookDetails">
                            <div class="modern-form-group">
                                <label for="title" class="modern-label">📖 বইয়ের নাম <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" placeholder="সম্পূর্ণ বইয়ের নাম লিখুন" class="form-control modern-input @error('title') is-invalid @enderror" value="{{ $product->title ?? old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-row-modern">
                                <div class="modern-form-group">
                                    <label for="author_id" class="modern-label">✍️ লেখক <span class="text-danger">*</span></label>
                                    <select class="form-control modern-select select2" name="author_id" required>
                                        <option value="">লেখক নির্বাচন করুন</option>
                                        @foreach(App\Models\Author::get(['name','id']) as $author)
                                         <option @isset($product->author_id)@if($product->author_id==$author->id)selected @endif @endisset value="{{$author->id}}">{{$author->name}}</option>
                                         @endforeach
                                    </select>
                                </div>

                                <div class="modern-form-group">
                                    <label for="isbn" class="modern-label">📋 আইএসবিএন নম্বর</label>
                                    <input type="text" name="isbn" id="isbn" placeholder="৯৭৮-০-১২৩৪৫৬-৭৮-৯" class="form-control modern-input @error('isbn') is-invalid @enderror" value="{{ $product->isbn ?? old('isbn') }}">
                                    @error('isbn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="modern-form-group">
                                <label for="brand" class="modern-label">🏢 প্রকাশক/ব্র্যান্ড <span class="text-danger">*</span></label>
                                <select name="brand" id="brand" data-placeholder="প্রকাশক নির্বাচন করুন" class="form-control select2 modern-select @error('brand') is-invalid @enderror" required>
                                    <option value="">প্রকাশক নির্বাচন করুন</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{$brand->id}}" @isset($product) {{$brand->id == $product->brand_id ? 'selected':''}} @endisset>{{$brand->name}}</option>
                                    @endforeach
                                </select>
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-row-modern">
                                <div class="modern-form-group">
                                    <label for="edition" class="modern-label">📑 সংস্করণ</label>
                                    <input type="text" name="edition" id="edition" placeholder="১ম সংস্করণ, সংশোধিত সংস্করণ..." class="form-control modern-input @error('edition') is-invalid @enderror" value="{{ $product->edition ?? old('edition') }}">
                                    @error('edition')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="modern-form-group">
                                    <label for="pages" class="modern-label">📄 মোট পৃষ্ঠা</label>
                                    <input type="number" name="pages" id="pages" placeholder="পৃষ্ঠার সংখ্যা" class="form-control modern-input @error('pages') is-invalid @enderror" value="{{ $product->pages ?? old('pages') }}">
                                    @error('pages')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row-modern">
                                <div class="modern-form-group">
                                    <label for="language" class="modern-label">🗣️ ভাষা</label>
                                    <input type="text" name="language" id="language" placeholder="বাংলা, ইংরেজি, ইত্যাদি" class="form-control modern-input @error('language') is-invalid @enderror" value="{{ $product->language ?? old('language') }}">
                                    @error('language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="modern-form-group">
                                    <label for="country" class="modern-label">🌍 প্রকাশনার দেশ</label>
                                    <input type="text" name="country" id="country" placeholder="যে দেশে প্রকাশিত" class="form-control modern-input @error('country') is-invalid @enderror" value="{{ $product->country ?? old('country') }}">
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="modern-form-group">
                                <label for="short_description" class="modern-label">📝 বইয়ের সংক্ষিপ্ত বিবরণ</label>
                                <textarea name="short_description" id="short_description" rows="3" placeholder="বইয়ের সংক্ষিপ্ত বিবরণ বা পিছনের কভারের সারাংশ" class="form-control modern-input modern-textarea @error('short_description') is-invalid @enderror">{{ $product->short_description ?? old('short_description') }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="modern-form-group">
                                <label for="full_description" class="modern-label">📖 বিস্তারিত বিবরণ <span class="text-danger">*</span></label>
                                <textarea name="full_description" id="full_description" class="form-control" placeholder="বিস্তারিত বিবরণ, প্লটের সারাংশ, মূল বৈশিষ্ট্য, লক্ষ্য পাঠক...">{{$product->full_description??old('full_description')}}</textarea>
                                @error('full_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="modern-form-group">
                                <label for="pdf" class="modern-label">📄 পিডিএফ ফাইল (ডিজিটাল সংস্করণ)</label>
                                <input type="file" name="pdf" class="form-control modern-input" accept=".pdf">
                                <small class="text-muted">ডিজিটাল ডাউনলোডের জন্য পিডিএফ সংস্করণ আপলোড করুন</small>
                            </div>
                        </div>
                    </div>

                    <!-- ২. মৌলিক পণ্যের তথ্য -->
                    <div class="modern-section">
                        <div class="modern-section-header" data-toggle="collapse" data-target="#basicInfo" aria-expanded="true">
                            <i class="fas fa-info-circle modern-section-icon"></i>
                            <h4 class="modern-section-title">📋 মৌলিক পণ্যের তথ্য</h4>
                            <i class="fas fa-chevron-down ml-auto"></i>
                        </div>
                        
                        <div class="collapse show" id="basicInfo">
                            <div class="form-row-modern">
                                <div class="modern-form-group">
                                    <label for="sku" class="modern-label">🏷️ পণ্য কোড</label>
                                    <input type="text" name="sku" id="sku" placeholder="পণ্য কোড/এসকেইউ" class="form-control modern-input @error('sku') is-invalid @enderror" value="{{ $product->sku ?? old('sku') }}">
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="modern-form-group">
                                    <label for="vendor" class="modern-label">🏪 বিক্রেতা</label>
                                    <select class="form-control modern-select" name="vendor">
                                        <option value="">বিক্রেতা নির্বাচন করুন (ঐচ্ছিক)</option>
                                        @foreach(App\Models\ShopInfo::get(['name','user_id']) as $vend)
                                        <option @isset($product->user_id)@if($product->user_id==$vend->user_id)selected @endif @endisset value="{{$vend->user_id}}">{{$vend->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="modern-form-group">
                                <label for="prdct_extra_msg" class="modern-label">📢 বিশেষ বার্তা</label>
                                <input type="text" name="prdct_extra_msg" id="prdct_extra_msg" placeholder="দ্রুত ডেলিভারি উপলব্ধ, সীমিত সংস্করণ, ইত্যাদি" class="form-control modern-input @error('prdct_extra_msg') is-invalid @enderror" value="{{ $product->prdct_extra_msg ?? '' }}">
                                @error('prdct_extra_msg')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- ৩. মূল্য ও ইনভেন্টরি বিভাগ -->
                    <div class="modern-section">
                        <div class="modern-section-header" data-toggle="collapse" data-target="#pricing" aria-expanded="true">
                            <i class="fas fa-dollar-sign modern-section-icon"></i>
                            <h4 class="modern-section-title">💰 মূল্য ও ইনভেন্টরি</h4>
                            <i class="fas fa-chevron-down ml-auto"></i>
                        </div>

                        <div class="collapse show" id="pricing">
                            <div class="price-grid">
                                <div class="price-card">
                                    <label for="buying_price" class="modern-label">💰 ক্রয় মূল্য</label>
                                    <input step="0.01" type="number" name="buying_price" id="buying_price" placeholder="আপনার ক্রয় মূল্য" class="form-control modern-input @error('buying_price') is-invalid @enderror" value="{{ $product->buying_price ?? old('buying_price') }}">
                                    @error('buying_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="price-card">
                                    <label for="whole_price" class="modern-label">📦 পাইকারি মূল্য</label>
                                    <input step="0.01" type="number" name="whole_price" id="whole_price" placeholder="বাল্ক বিক্রয় মূল্য" class="form-control modern-input @error('whole_price') is-invalid @enderror" value="{{ $product->whole_price ?? old('whole_price') }}">
                                    @error('whole_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="price-card">
                                    <label for="regular_price" class="modern-label">🏷️ নিয়মিত মূল্য <span class="text-danger">*</span></label>
                                    <input step="0.01" type="number" name="regular_price" id="regular_price" placeholder="গ্রাহক মূল্য" class="form-control modern-input @error('regular_price') is-invalid @enderror" value="{{ $product->regular_price ?? old('regular_price') }}" required>
                                    @error('regular_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="price-card">
                                    <label for="quantity" class="modern-label">📚 স্টক পরিমাণ <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" id="quantity" placeholder="উপলব্ধ কপি" class="form-control modern-input @error('quantity') is-invalid @enderror" value="{{ $product->quantity ?? old('quantity') }}" required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row-modern">
                                <div class="modern-form-group">
                                    <label for="dis_type" class="modern-label">🎫 ছাড়ের ধরন</label>
                                    <select name="dis_type" id="dis_type" class="form-control modern-select @error('dis_type') is-invalid @enderror">
                                        <option value="0" @isset($product) {{$product->dis_type == '0' ? 'selected':''}} @endisset>কোন ছাড় নেই</option>
                                        <option value="1" @isset($product) {{$product->dis_type == '1' ? 'selected':''}} @endisset>নির্দিষ্ট পরিমাণ</option>
                                        <option value="2" @isset($product) {{$product->dis_type == '2' ? 'selected':''}} @endisset>শতকরা %</option>
                                    </select>
                                    @error('dis_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @isset($product) 
                                    @if($product->dis_type == '2')
                                        @php($discount_price=(($product->regular_price - $product->discount_price) / ($product->regular_price ))*100)
                                    @else
                                        @if($product->discount_price<1)
                                            @php($discount_price='')
                                        @else
                                            @php($discount_price=$product->regular_price-$product->discount_price)
                                        @endif
                                    @endif
                                @endisset

                                <div class="modern-form-group">
                                    <label for="discount_price" class="modern-label">💸 ছাড়ের পরিমাণ</label>
                                    <input step="0.01" type="number" name="discount_price" id="discount_price" placeholder="ছাড়ের মান" class="form-control modern-input @error('discount_price') is-invalid @enderror" value="{{ $discount_price ?? old('discount_price') }}">
                                    @error('discount_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="modern-form-group">
                                    <label for="point" class="modern-label">⭐ লয়ালটি পয়েন্ট</label>
                                    <input type="number" name="point" id="point" placeholder="অর্জিত পয়েন্ট" class="form-control modern-input @error('point') is-invalid @enderror" value="{{ $product->point ?? old('point') }}">
                                    @error('point')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ৪. বইয়ের কভার ও মিডিয়া বিভাগ - ডিফল্টভাবে খোলা -->
                    <div class="modern-section media-section">
                        <div class="modern-section-header" data-toggle="collapse" data-target="#media" aria-expanded="true">
                            <i class="fas fa-camera modern-section-icon"></i>
                            <h4 class="modern-section-title">📸 বইয়ের কভার ও মিডিয়া</h4>
                            <i class="fas fa-chevron-down ml-auto"></i>
                        </div>

                        <div class="collapse show" id="media">
                            <!-- বইয়ের কভার আপলোড - হাইলাইটেড -->
                            <div class="modern-form-group">
                                <div class="book-cover-highlight">
                                    <label for="image" class="modern-label">📖 মূল বইয়ের কভার <span class="text-danger">*</span></label>
                                    <p class="text-muted mb-3">আপনার বইয়ের সামনের কভার আপলোড করুন। এটি গ্রাহকরা দেখতে পাবেন প্রধান ছবি হিসেবে।</p>
                                    <input type="file" name="image" id="image" accept="image/*" class="form-control dropify modern-input @error('image') is-invalid @enderror" data-default-file="@isset($product) /uploads/product/{{$product->image}}@endisset">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- অতিরিক্ত ছবি -->
                            <div class="modern-form-group">
                                <label class="modern-label">📸 অতিরিক্ত বইয়ের ছবি</label>
                                <p class="text-muted mb-3">পিছনের কভার, ভিতরের পৃষ্ঠা, বা অন্যান্য প্রাসঙ্গিক ছবি আপলোড করুন</p>
                                <div class="input-group" id="increment">
                                    <input type="file" class="form-control modern-input" accept="image/*" id="images" name="images[]" @isset($product) @else required @endisset>
                                    <select name="imagesc[]" id="imagesc" class="form-control modern-select">
                                        <option value="">রঙের ভ্যারিয়েন্ট নির্বাচন করুন</option>
                                        @foreach ($colors as $color)
                                            <option style="color:white;background: {{$color->code}}" value="{{$color->slug}}">{{$color->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append" id="add" style="cursor:pointer">
                                        <span class="input-group-text bg-success text-white">আরো যোগ করুন</span>
                                    </div>
                                </div>
                                @error('images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @isset($product)
                                    <div class="mt-3">
                                        @foreach($product->images as $image)
                                            <div class="d-flex align-items-center p-3 mb-2 border rounded" @foreach ($colors as $color) @if($color->slug==$image->color_attri) style="background: {{$color->code}}20; border-color: {{$color->code}};" @endif @endforeach>
                                                <img src="{{asset('uploads/product/'.$image->name)}}" style="width: 100px;height: 70px;object-fit: cover;border-radius: 8px;">
                                                <div class="flex-grow-1 text-right">
                                                    <a class="modern-btn modern-btn-danger" href="{{route('admin.idelte',$image->id)}}">🗑️ মুছুন</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endisset
                            </div>

                            <!-- রঙ নির্বাচন - ডিফল্টভাবে খোলা -->
                            <div class="modern-form-group">
                                <div class="modern-section">
                                    <label class="modern-label" for="color">
                                        <button style="width: 100%;text-align:left;background:none;border:none;" type="button" data-toggle="collapse" data-target="#collapseExampleColor" aria-expanded="true" aria-controls="collapseExampleColor">
                                            🎨 বইয়ের কভারের রঙ
                                            <i style="float: right;top: 8px;position: relative;" class="fas fa-chevron-down"></i>
                                        </button>
                                    </label>
                                    
                                    <div class="collapse show" id="collapseExampleColor">
                                        <div class="input-group">
                                            <select id="select_color" data-placeholder="রঙ নির্বাচন করুন" class="form-control modern-select @error('colors') is-invalid @enderror">
                                                <option value="">রঙ নির্বাচন করুন</option>
                                                @foreach ($colors as $color)
                                                    <option style="color:white;background: {{$color->code}}" value="{{$color->slug.','.$color->id}}">{{$color->name}}</option>
                                                @endforeach
                                            </select>
                                            @error('colors')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div id="increment_color">
                                            @isset($product)
                                            @foreach($colors_product as $pro_color)
                                                <div class="input-group mt-2">
                                                    <input class="form-control modern-input" type="hidden" readonly="" name="colors[]" value="{{$pro_color->id}}">
                                                    <input class="form-control modern-input" type="text" readonly="" value="{{$pro_color->name}}">
                                                    <input class="form-control modern-input" type="number" placeholder="অতিরিক্ত মূল্য" name="color_prices[]" value="{{$pro_color->price}}">
                                                    <input class="form-control modern-input" type="number" placeholder="অতিরিক্ত পরিমাণ" name="color_quantits[]" value="{{$pro_color->qnty}}">
                                                    <div class="input-group-append" id="remove" style="cursor:pointer">
                                                        <a href="{{route('admin.color.delete.n2',['cc'=>$pro_color->id,'pp'=>$product->id])}}">
                                                            <span class="input-group-text bg-danger text-white">সরান</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach 
                                            @endisset
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ভিডিও আপলোড -->
                            <div class="modern-form-group">
                                @isset($product)
                                    <div class="mb-3">
                                        <a target="_blank" href="{{asset('uploads/product/video/'.$product->video)}}" class="modern-btn modern-btn-info">📹 বর্তমান ভিডিও দেখুন</a>
                                        <a target="_blank" href="{{asset('uploads/product/video/'.$product->video_thumb)}}" class="modern-btn modern-btn-info">🖼️ ভিডিও থাম্বনেইল দেখুন</a>
                                    </div>
                                @endisset
                                
                                <label for="video" class="modern-label">🎬 বইয়ের ট্রেইলার/ভিডিও</label>
                                <input type="file" name="video" class="form-control modern-input @error('video') is-invalid @enderror" accept="video/*">
                                
                                <label for="yvideo" class="modern-label mt-3">📺 অথবা ইউটিউব ভিডিও ইউআরএল</label>
                                <input value="{{ $product->yvideo ?? old('yvideo') }}" type="text" name="yvideo" placeholder="https://youtube.com/watch?v=..." class="form-control modern-input @error('yvideo') is-invalid @enderror">
                                
                                <label for="video_thumb" class="modern-label mt-3">🖼️ ভিডিও থাম্বনেইল</label>
                                <input type="file" name="video_thumb" class="form-control modern-input @error('video_thumb') is-invalid @enderror" accept="image/*">
                                
                                @error('video')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- ৫. বিভাগ ও শ্রেণীবিভাগ -->
                    <div class="modern-section">
                        <div class="modern-section-header" data-toggle="collapse" data-target="#categories" aria-expanded="true">
                            <i class="fas fa-tags modern-section-icon"></i>
                            <h4 class="modern-section-title">🏷️ বিভাগ ও শ্রেণীবিভাগ</h4>
                            <i class="fas fa-chevron-down ml-auto"></i>
                        </div>

                        <div class="collapse show" id="categories">
                            <div class="form-row-modern">
                                <div class="modern-form-group">
                                    <label for="category" class="modern-label">📚 ধরন/বিভাগ <span class="text-danger">*</span></label>
                                    <select name="categories[]" id="category" multiple data-placeholder="ধরন নির্বাচন করুন" class="category form-control select2 modern-select @error('categories') is-invalid @enderror" required>
                                        <option value="">বিভাগ নির্বাচন করুন</option>
                                        @foreach ($categories as $category)
                                            <option value="{{$category->id}}" @isset($product) @foreach($product->categories as $pro_category) {{$category->id == $pro_category->id ? 'selected':''}} @endforeach @endisset>{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('categories')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row-modern">
                                <div class="modern-form-group">
                                    <label for="sub_category" class="modern-label">📖 উপ ধরন</label>
                                    <select name="sub_categories[]" id="sub_category" data-placeholder="উপ বিভাগ নির্বাচন করুন" class="sub_category form-control {{isset($product) ? 'select2':''}} modern-select @error('sub_categories') is-invalid @enderror" {{isset($product) ? 'multiple':''}}>
                                        @isset($product)
                                            @foreach ($product->sub_categories as $sub_category)
                                                <option value="{{$sub_category->id}}" selected>{{$sub_category->name}}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    @error('sub_categories')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="modern-form-group">
                                    <label for="mini_category" class="modern-label">📑 নির্দিষ্ট বিষয়</label>
                                    <select name="mini_categories[]" id="mini_category" data-placeholder="বিষয় নির্বাচন করুন" class="mini_category form-control {{isset($product) ? 'select2':''}} modern-select @error('mini_categories') is-invalid @enderror" {{isset($product) ? 'multiple':''}}>
                                        @isset($product)
                                            @foreach ($product->mini_categories as $mini_category)
                                                <option value="{{$mini_category->id}}" selected>{{$mini_category->name}}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    @error('mini_categories')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row-modern">
                                <div class="modern-form-group">
                                    <label for="extra_category" class="modern-label">🎯 অতিরিক্ত ট্যাগ</label>
                                    <select name="extra_categories[]" id="extra_category" data-placeholder="অতিরিক্ত বিভাগ নির্বাচন করুন" class="extra_categories form-control {{isset($product) ? 'select2':''}} modern-select @error('extra_categories') is-invalid @enderror" {{isset($product) ? 'multiple':''}}>
                                        @isset($product)
                                            @foreach ($product->extra_categories as $extra_category)
                                                <option value="{{$extra_category->id}}" selected>{{$extra_category->name}}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    @error('extra_categories')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="modern-form-group">
                                    <label for="tag" class="modern-label">🏷️ কীওয়ার্ড</label>
                                    <select name="tags[]" id="tag" multiple data-placeholder="ট্যাগ নির্বাচন করুন" class="form-control select2 modern-select @error('tags') is-invalid @enderror">
                                        <option value="">ট্যাগ নির্বাচন করুন</option>
                                        @foreach ($tags as $tag)
                                            <option value="{{$tag->id}}" @isset($product) @foreach($product->tags as $pro_tag) {{$tag->id == $pro_tag->id ? 'selected':''}} @endforeach @endisset>{{$tag->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row-modern">
                                <div class="modern-form-group">
                                    <label for="campaign" class="modern-label">📢 মার্কেটিং ক্যাম্পেইন</label>
                                    <select name="campaigns[]" id="campaign" multiple data-placeholder="ক্যাম্পেইন নির্বাচন করুন" class="category form-control select2 modern-select @error('campaigns') is-invalid @enderror">
                                        <option value="">ক্যাম্পেইন নির্বাচন করুন</option>
                                        @foreach ($campaigns as $campaign)
                                            <option value="{{$campaign->id}}" @isset($product) {{$campaign->id == $product->brand_id ? 'selected':''}} @endisset>{{$campaign->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('campaigns')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(isset($sizes) && count($sizes) > 0)
                                <div class="modern-form-group">
                                    <label for="size" class="modern-label">📏 বইয়ের ফরম্যাট</label>
                                    <select name="sizes[]" id="size" multiple data-placeholder="ফরম্যাট নির্বাচন করুন" class="form-control select2 modern-select @error('sizes') is-invalid @enderror">
                                        <option value="">সাইজ নির্বাচন করুন</option>
                                        @foreach ($sizes as $size)
                                            <option value="{{$size->id}}" @isset($product) @foreach($product->sizes as $pro_size) {{$size->id == $pro_size->id ? 'selected':''}} @endforeach @endisset>{{$size->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('sizes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <input type='hidden' name="shipping_charge" value="1">

                    <!-- ৬. পণ্যের সেটিংস -->
                    <div class="modern-section">
                        <div class="modern-section-header">
                            <i class="fas fa-cog modern-section-icon"></i>
                            <h4 class="modern-section-title">⚙️ পণ্যের সেটিংস</h4>
                        </div>

                        <div class="form-row">
                            <div class="col-md-3">
                                <div class="modern-form-group">
                                    <label class="modern-switch">
                                        <input type="checkbox" name="status" id="status" @isset ($product) {{ $product->status ? 'checked':'' }} @else checked @endisset>
                                        <span class="modern-slider"></span>
                                    </label>
                                    <label for="status" class="modern-label">✅ সক্রিয় অবস্থা</label>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="modern-form-group">
                                    <label class="modern-switch">
                                        <input type="checkbox" name="book" id="book" @isset ($product) {{ $product->book ? 'checked':'' }} @else checked @endisset>
                                        <span class="modern-slider"></span>
                                    </label>
                                    <label for="book" class="modern-label">📚 বই পণ্য</label>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="modern-form-group">
                                    <label class="modern-switch">
                                        <input type="checkbox" name="sheba" id="sheba" @isset ($product) {{ $product->sheba ? 'checked':'' }} @endisset>
                                        <span class="modern-slider"></span>
                                    </label>
                                    <label for="sheba" class="modern-label">🚚 শেবা ডেলিভারি</label>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="modern-form-group">
                                    <label class="modern-switch">
                                        <input type="checkbox" name="download_able" id="download_able" @isset($product){{ $product->download_able ? 'checked':''}} @endisset>
                                        <span class="modern-slider"></span>
                                    </label>
                                    <label for="download_able" class="modern-label">💾 ডাউনলোডযোগ্য</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ডাউনলোড ফাইল মোডাল -->
                    @isset($product)
                        @if ($product->downloads->count() < 1)
                        <div class="modal fade" id="modal-default">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content modern-card">
                                    <div class="modal-header modern-card-header">
                                        <h4 class="modal-title">📥 ডাউনলোডযোগ্য ফাইল যোগ করুন</h4>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-horizontal">
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <label for="inputEmail3" class="col-sm-2 col-form-label modern-label">📁 ডাউনলোডযোগ্য ফাইল</label>
                                                    <div class="col-sm-10">
                                                        <div class="card border modern-section">
                                                            <div class="card-header">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <strong>📝 নাম:</strong>
                                                                    </div>
                                                                    <div class="col-md-4"><strong>🔗 ফাইল ইউআরএল:</strong></div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body px-1 py-2">
                                                                <div class="row">
                                                                    <div class="col-12" id="increment-file">
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-footer">
                                                                <span id="add-file" class="modern-btn modern-btn-success">➕ ফাইল যোগ করুন</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="download_limit" class="col-sm-2 col-form-label modern-label">📊 ডাউনলোড সীমা</label>
                                                    <div class="col-sm-4">
                                                        <input type="number" class="form-control modern-input" id="download_limit" name="download_limit" value="{{$product->download_limit ?? old('download_limit')}}">
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted">সীমাহীন ডাউনলোডের জন্য খালি রাখুন</span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="download_expire" class="col-sm-2 col-form-label modern-label">⏰ ডাউনলোড মেয়াদ</label>
                                                    <div class="col-sm-4">
                                                        <input type="date" class="form-control modern-input" id="download_expire" name="download_expire" value="{{$product->download_expire ?? old('download_expire')}}">
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted">মেয়াদ উত্তীর্ণের তারিখ সেট করুন বা খালি রাখুন</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="modern-btn modern-btn-danger" data-dismiss="modal">❌ বন্ধ করুন</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @else
                    <div class="modal fade" id="modal-default">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content modern-card">
                                <div class="modal-header modern-card-header">
                                    <h4 class="modal-title">📥 ডাউনলোডযোগ্য ফাইল যোগ করুন</h4>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-horizontal">
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 col-form-label modern-label">📁 ডাউনলোডযোগ্য ফাইল</label>
                                                <div class="col-sm-10">
                                                    <div class="card border modern-section">
                                                        <div class="card-header">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <strong>📝 নাম:</strong>
                                                                </div>
                                                                <div class="col-md-4"><strong>🔗 ফাইল ইউআরএল:</strong></div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body px-1 py-2">
                                                            <div class="row">
                                                                <div class="col-12" id="increment-file">
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer">
                                                            <span id="add-file" class="modern-btn modern-btn-success">➕ ফাইল যোগ করুন</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="download_limit" class="col-sm-2 col-form-label modern-label">📊 ডাউনলোড সীমা</label>
                                                <div class="col-sm-4">
                                                    <input type="number" class="form-control modern-input" id="download_limit" name="download_limit" value="{{$product->download_limit ?? old('download_limit')}}">
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted">সীমাহীন ডাউনলোডের জন্য খালি রাখুন</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="download_expire" class="col-sm-2 col-form-label modern-label">⏰ ডাউনলোড মেয়াদ</label>
                                                <div class="col-sm-4">
                                                    <input type="date" class="form-control modern-input" id="download_expire" name="download_expire" value="{{$product->download_expire ?? old('download_expire')}}">
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted">মেয়াদ উত্তীর্ণের তারিখ সেট করুন বা খালি রাখুন</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="modern-btn modern-btn-danger" data-dismiss="modal">❌ বন্ধ করুন</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endisset
                </div>

                <div class="card-footer p-4" style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px);">
                    <button type="submit" class="modern-btn modern-btn-primary">
                        @isset($product)
                            <i class="fas fa-save"></i>
                            বই আপডেট করুন
                        @else
                            <i class="fas fa-plus-circle"></i>
                            ক্যাটালগে বই যোগ করুন
                        @endisset
                    </button>
                </div>
            </form>

            <div class="col-lg-4">
                @include('components.product-sidebar')
            </div>
        </div>
    </div>

    <!-- ডাউনলোড ফাইল আপডেট বিভাগ -->
    @if(isset($product->downloads) && $product->downloads->count() > 0)
    <div class="modern-card mt-4">
        <div class="modern-card-header">
            <h3 class="mb-0">📥 ডাউনলোড ফাইল আপডেট করুন</h3>
        </div>
        
        <form action="{{routeHelper('update/product/download')}}" class="form-horizontal" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="product_id" value="{{$product->id}}">
            <div class="card-body p-4">
                <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-2 col-form-label modern-label">📁 ডাউনলোডযোগ্য ফাইল</label>
                    <div class="col-sm-10">
                        <div class="card border modern-section">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>📝 নাম:</strong>
                                    </div>
                                    <div class="col-md-4"><strong>🔗 ফাইল ইউআরএল:</strong></div>
                                </div>
                            </div>
                            <div class="card-body px-1 py-2">
                                <div class="row">
                                    <div class="col-12" id="increment-file">
                                        @isset($product->downloads)
                                            @foreach ($product->downloads as $download)
                                                <div class="row mt-2">
                                                    <div class="col-md-4">
                                                        <input type="text" name="file_name[]" class="form-control modern-input" placeholder="ফাইলের নাম লিখুন" value="{{$download->name}}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" name="file_url[]" class="form-control modern-input" placeholder="ফাইল ইউআরএল লিখুন" value="{{$download->url}}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="file" name="files[]" class="form-control modern-input">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="hidden" name="ids[]" value="{{$download->id}}">
                                                        <a href="#" id="remove-file" data-id="{{$download->id}}" class="modern-btn modern-btn-danger"><i class="fa fa-trash-alt"></i></a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endisset
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <span id="add-file" class="modern-btn modern-btn-success">➕ ফাইল যোগ করুন</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="download_limit" class="col-sm-2 col-form-label modern-label">📊 ডাউনলোড সীমা</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control modern-input" id="download_limit" name="download_limit" value="{{$product->download_limit ?? old('download_limit')}}">
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">সীমাহীন ডাউনলোডের জন্য খালি রাখুন</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="download_expire" class="col-sm-2 col-form-label modern-label">⏰ ডাউনলোড মেয়াদ</label>
                    <div class="col-sm-4">
                        <input type="date" class="form-control modern-input" id="download_expire" name="download_expire" value="{{$product->download_expire ?? old('download_expire')}}">
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">মেয়াদ উত্তীর্ণের তারিখ সেট করুন বা খালি রাখুন</span>
                    </div>
                </div>
            </div>
            <div class="card-footer p-4">
                <button type="submit" class="modern-btn modern-btn-primary">💾 ফাইল আপডেট করুন</button>
            </div>
        </form>
    </div>
    @endif
</section>

@endsection

@push('js')
    <!-- Select2 -->
    <script src="/assets/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('/assets/plugins/dropify/dropify.min.js') }}"></script>
    <script src="/assets/plugins/summernote/summernote-bs4.min.js"></script>
    <script src="/assets/dist/extra.js"></script>
    <script type="text/javascript" src="/assets/plugins/file-uploader/image-uploader.min.js"></script>
    
    @isset($product)
        @if ($product->downloads->count() < 1)
        <script>
            $(document).on('click', '#download_able', function(e) {
                if (this.checked) {
                    $('#modal-default').modal('show')
                } else {
                    $('#modal-default').modal('hide')
                }
            })
        </script>
        @endif
    @else
    <script>
        $(document).on('click', '#download_able', function(e) {
            if (this.checked) {
                $('#modal-default').modal('show')
            } else {
                $('#modal-default').modal('hide')
            }
        })
    </script>
    @endisset
    
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                theme: 'bootstrap4'
            });
            $('.dropify').dropify();
            $('#full_description').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            $('#short_description').summernote({
                height: 150,
                toolbar: [
                    ['font', ['bold', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']]
                ]
            });

            // ছবি বৃদ্ধি
            $(document).on('click', '#add', function (e) { 
                let htmlData = '<div class="input-group mt-2">';
                htmlData += '<input type="file" class="form-control modern-input" accept="image/*" name="images[]" required>';
                htmlData += '<select name="imagesc[]" class="form-control modern-select">';
                htmlData += $('#imagesc').html();
                htmlData += '</select>';
                htmlData += '<div class="input-group-append" id="remove" style="cursor:pointer">';
                htmlData += '<span class="input-group-text bg-danger text-white">সরান</span>';
                htmlData += '</div>';
                htmlData += '</div>';
                $('#increment').append(htmlData);
            });

            // রঙ বৃদ্ধি
            $(document).on('change', '#select_color', function (e) { 
               let colors = $(this).val();
                var color = colors.split(',');

                let htmlData = '<div class="input-group mt-2">';
                htmlData += ' <input class="form-control modern-input" type="hidden" name="colors[]" readonly value="'+color[1]+'">';
                htmlData += ' <input class="form-control modern-input" type="text" readonly value="'+color[0]+'">';
                htmlData += ' <input class="form-control modern-input" type="number" placeholder="অতিরিক্ত মূল্য" name="color_prices[]" value="">';
                htmlData += ' <input class="form-control modern-input" type="number" placeholder="অতিরিক্ত পরিমাণ" name="color_quantits[]" value="">';
                htmlData += '<div class="input-group-append" id="remove" style="cursor:pointer">';
                htmlData += '<span class="input-group-text bg-danger text-white">সরান</span>';
                htmlData += '</div>';
                htmlData += '</div>';
                $('#increment_color').append(htmlData);
            });

            // উপাদান সরান
            $(document).on('click', '#remove', function(e) {
                $(this).parent().remove();
            });

            // ফাইল বৃদ্ধি
            $(document).on('click', '#add-file', function (e) {
                let htmlData = '<div class="row mt-2">';
                htmlData += '<div class="col-md-4"><input type="text" name="file_name[]" class="form-control modern-input" placeholder="ফাইলের নাম লিখুন"></div>';
                htmlData += '<div class="col-md-4"><input type="text" name="file_url[]" class="form-control modern-input" placeholder="ফাইল ইউআরএল লিখুন"></div>';
                htmlData += '<div class="col-md-2"><input type="file" name="files[]" class="form-control modern-input"></div>';
                htmlData += '<div class="col-md-2">';
                htmlData += '<input type="hidden" name="ids[]" value="0">';
                htmlData += '<button type="button" data-id="0" id="remove-file" class="modern-btn modern-btn-danger"><i class="fa fa-trash-alt"></i></button></div>';
                htmlData += '</div>';

                $('#increment-file').append(htmlData);
            });

            // ফাইল সরান
            $(document).on('click', '#remove-file', function(e) {
                e.preventDefault();
                let btn = $(this);
                let id = $(this).data('id');

                if (id == 0) {
                    $(this).parent().parent().remove();
                } else {
                    $.ajax({
                        type: 'GET',
                        url: '/admin/delete/product/download/'+id,
                        dataType: "JSON",
                        beforeSend: function() {
                            $(btn).addClass('disabled');
                        },
                        success: function (response) {
                            $(btn).parent().parent().remove();
                        },
                        complete: function() {
                            $(btn).removeClass('disabled');
                        }
                    });
                }
            });

            // বিভাগ পরিবর্তন হ্যান্ডলার
            $(document).on('change', '#category', function() {
                var options = document.getElementById('category').selectedOptions;
                var values = Array.from(options).map(({ value }) => value);
                
                $.ajax({
                    type: 'POST',
                    url: '/admin/get/sub-categories',
                    data: {
                        'ids': values,
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                    },
                    dataType: "JSON",
                    success: function (response) {
                        let data = '<option value="">উপ বিভাগ নির্বাচন করুন</option>';
                        $.each(response, function (key, val) { 
                            data += '<option value="'+val.id+'">'+val.name+'</option>';
                        });
                        $('#sub_category').html(data).attr('multiple', true).select2();
                    }
                });
            });
        
            $(document).on('change', '#sub_category', function() {
                var options = document.getElementById('sub_category').selectedOptions;
                var values = Array.from(options).map(({ value }) => value);
                
                $.ajax({
                    type: 'POST',
                    url: '/admin/get/mini-categories',
                    data: {
                        'ids': values,
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                    },
                    dataType: "JSON",
                    success: function (response) {
                        let data = '<option value="">ছোট বিভাগ নির্বাচন করুন</option>';
                        $.each(response, function (key, val) { 
                            data += '<option value="'+val.id+'">'+val.name+'</option>';
                        });
                        $('#mini_category').html(data).attr('multiple', true).select2();
                    }
                });
            });

            $(document).on('change', '#mini_category', function() {
                var options = document.getElementById('mini_category').selectedOptions;
                var values = Array.from(options).map(({ value }) => value);
                
                $.ajax({
                    type: 'POST',
                    url: '/admin/get/extra-categories',
                    data: {
                        'ids': values,
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                    },
                    dataType: "JSON",
                    success: function (response) {
                        let data = '<option value="">অতিরিক্ত বিভাগ নির্বাচন করুন</option>';
                        $.each(response, function (key, val) { 
                            data += '<option value="'+val.id+'">'+val.name+'</option>';
                        });
                        $('#extra_category').html(data).attr('multiple', true).select2();
                    }
                });
            });

            // ছাড়ের ধরন পরিবর্তন
            $(document).on('change', '#dis_type', function(e) {
                if ($(this).val() != "0") {
                    $('#discount_price').prop('required', true);
                } else {
                    $('#discount_price').prop('required', false);
                }
            });
        });
    </script>

    @isset($product)
        <script>
            function productImages() {
                let id = '{!! $product->id !!}';
                console.log(id);
                $.ajax({
                    type: 'GET',
                    url: '/admin/get/product/image/'+id,
                    dataType: 'JSON',
                    success: function (response) {
                        let preloaded = [];
                        $.each(response, function (key, val) { 
                            preloaded.push({
                                id: val.id,
                                src: '/uploads/product/'+val.name
                            });
                        });

                        $('.input-images-1').imageUploader({
                            preloaded: preloaded,
                            imagesInputName: 'photos',
                            preloadedInputName: 'old'
                        });
                    }
                });
            }
            productImages();

            function attributes(){
                var options = document.getElementById('category').selectedOptions;
                var values = Array.from(options).map(({ value }) => value);
                var product_id = $('#id').val();
                
                $.ajax({
                    type: 'POST',
                    url: '/admin/get/attributes',
                    data: {
                        'ids': values,
                        'product_id': product_id,
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                    },
                    dataType: "JSON",
                    success: function (response) {
                        $('#sho_attributes').html(response);
                    }
                });
            }
            attributes();

            $(document).on('change', '#category', function() {
                var options = document.getElementById('category').selectedOptions;
                var values = Array.from(options).map(({ value }) => value);
                var product_id = $('#id').val();
                $.ajax({
                    type: 'POST',
                    url: '/admin/get/attributes',
                    data: {
                        'ids': values,
                        'product_id': product_id,
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                    },
                    dataType: "JSON",
                    success: function (response) {
                        $('#sho_attributes').html(response);
                    }
                });
            });
        </script>
    @else
        <script>
            $(document).on('change', '#category', function() {
                var options = document.getElementById('category').selectedOptions;
                var values = Array.from(options).map(({ value }) => value);
                
                $.ajax({
                    type: 'POST',
                    url: '/admin/get/attributes',
                    data: {
                        'ids': values,
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                    },
                    dataType: "JSON",
                    success: function (response) {
                        $('#sho_attributes').html(response);
                    }
                });
            });
        </script>
    @endisset

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.min.js"></script>
    <script>
        $('#ncolor').colorpicker();
    </script>
@endpush