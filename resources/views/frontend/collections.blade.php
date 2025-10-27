@extends('layouts.frontend.app')

@section('title', 'All Collections')

@section('content')
    <div class="page-header">
        <div class="container">
            <h1 class="page-title">All Our Collections</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Collections</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="collection-page-area py-5">
        <div class="container">
            @if($collections->count() > 0)
                <div class="row g-4">
                    @foreach($collections as $collection)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="collection-card shadow-sm rounded-lg overflow-hidden transition-all duration-300 hover:shadow-lg">
                                <a href="{{ route('collection.product', $collection->slug) }}" class="d-block text-decoration-none">
                                    <div class="collection-image-wrapper">
                                        {{-- Fallback image if no cover photo exists --}}
                                        @if($collection->cover_photo && file_exists(public_path('uploads/collection/'.$collection->cover_photo)))
                                            <img src="{{ asset('uploads/collection/'.$collection->cover_photo) }}" alt="{{ $collection->name }}" class="img-fluid collection-cover-image">
                                        @else
                                            <div class="collection-placeholder">
                                                <i class="fas fa-box-open"></i>
                                                <p>{{ $collection->name }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="collection-info p-3">
                                        <h5 class="collection-title text-center mb-0">{{ $collection->name }}</h5>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info text-center" role="alert">
                    <i class="fas fa-info-circle me-2"></i> No active collections found at this time.
                </div>
            @endif
        </div>
    </div>
@endsection

@push('css')
<style>
    /* Custom Styles for Collections Page */
    .page-header {
        background: #06b6d4; /* Explicitly set to a vibrant teal/cyan, assuming this is the campaign color/desired color from the image */
        color: white;
        padding: 50px 0;
        margin-bottom: 30px;
    }
    .page-header .page-title {
        font-weight: 700;
        margin-bottom: 5px;
    }
    .collection-page-area {
        background-color: #f8f9fa; /* Light background for the content area */
    }
    .collection-card {
        background: white;
        border: 1px solid #e0e0e0;
        text-align: center;
    }
    .collection-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
    .collection-image-wrapper {
        height: 180px; /* Fixed height for consistency */
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f0f0f0;
        border-bottom: 1px solid #e0e0e0;
        overflow: hidden;
    }
    .collection-cover-image {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Ensures the image covers the area */
        transition: transform 0.3s ease;
    }
    .collection-card:hover .collection-cover-image {
        transform: scale(1.05);
    }
    .collection-placeholder {
        color: #999;
        font-size: 24px;
        padding: 20px;
        line-height: 1.2;
    }
    .collection-placeholder i {
        font-size: 40px;
        margin-bottom: 10px;
    }
    .collection-title {
        color: #333;
        font-size: 1.1rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .collection-card a:hover .collection-title {
        color: var(--brand-accent, #06b6d4);
    }
</style>
@endpush

@push('js')
    {{-- You can add any specific JS for collection interactions here if needed --}}
@endpush