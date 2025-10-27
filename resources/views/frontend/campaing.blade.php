@extends('layouts.frontend.app')
@push('meta')
<meta name='description' content="All active product campaigns and promotions."/>
<meta name='keywords' content="@foreach(\App\Models\Campaign::all() as $campaign){{$campaign->name.', '}}@endforeach, deals, promotions, special offers" />
@endpush

@section('title', 'Campaigns | Special Offers')

@section('content')

{{-- Inlining modern CSS for the campaign cards --}}
<style>
    /* Styling for a clean, modern header banner */
    .campaign-header {
        padding: 40px 0;
        background-color: #f7f7f7; /* Light gray background for a clean look */
        margin-bottom: 30px;
    }
    .campaign-header h1 {
        font-size: 2.25rem; /* Large, modern title */
        font-weight: 700;
        color: #1f2937; /* Dark text */
    }
    .campaign-header p.text-muted {
        color: #6b7280;
    }
    .campaign-grid {
        padding-bottom: 40px;
    }
    /* Styling for the individual campaign card */
    .campaign-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 12px; /* Rounded corners */
        overflow: hidden;
        margin-bottom: 25px;
        background-color: #ffffff;
        border: 1px solid #e5e7eb; /* Subtle border */
        height: 100%; /* Ensure all cards are same height */
        display: flex;
        flex-direction: column;
    }
    .campaign-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); /* Modern shadow effect */
    }
    .campaign-card .thumbnail {
        overflow: hidden;
        border-bottom: 1px solid #e5e7eb;
        /* Maintain aspect ratio for the image area */
        position: relative;
        padding-top: 66.66%; /* 3:2 Aspect Ratio */
    }
    .campaign-card .thumbnail img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover; /* Ensures image covers the area */
        display: block;
        transition: transform 0.5s ease;
    }
    .campaign-card:hover .thumbnail img {
        transform: scale(1.05); /* Slight zoom on hover */
    }
    .campaign-body {
        padding: 15px;
        text-align: left; /* Align text left for a professional look */
        flex-grow: 1;
    }
    .campaign-body h4 {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 5px;
        line-height: 1.4;
    }
    .campaign-body p.count {
        font-size: 0.85rem;
        color: #6b7280;
        margin: 0;
    }
    .campaign-cta {
        display: block;
        text-align: center;
        padding: 12px 15px;
        background-color: #f97316; /* Brand accent color (often orange) */
        color: white;
        text-decoration: none;
        font-weight: 600;
        border-radius: 0 0 12px 12px;
        transition: background-color 0.3s ease;
        /* No top margin needed, attached to card bottom */
    }
    .campaign-cta:hover {
        background-color: #ea580c; /* Darker accent on hover */
        text-decoration: none;
        color: white;
    }
    /* Ensure the container is used if the frontend framework requires it */
    .container {
        /* Assuming a max-width container is available globally */
        width: 100%;
        margin-right: auto;
        margin-left: auto;
        padding-right: 15px;
        padding-left: 15px;
    }
</style>

{{-- Modern Header/Banner --}}
<div class="campaign-header">
    <div class="container">
        <h1>All Active Campaigns</h1>
        <p class="text-muted">Discover our best deals and limited-time promotions.</p>
    </div>
</div>

<div class="campaign-grid">
    <div class="container">
        {{-- Row structure for the grid --}}
        <div class="row">
            
            @foreach ($campaigns as $data)
                {{-- Modern grid sizing: 4 items on large screens, 3 on medium, 2 on small --}}
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="campaign-card">
                        
                        {{-- Campaign Image Link --}}
                        <a href="{{route('campaing.product',['slug'=>$data->slug])}}" aria-label="View products in {{ $data->name }} campaign">
                            <div class="thumbnail">
                                <img src="{{asset('uploads/campaign/'.$data->cover_photo)}}" alt="{{$data->name}} Campaign Image">
                            </div>
                        </a>
                        
                        {{-- Campaign Content --}}
                        <div class="campaign-body">
                            <h4>{{$data->name}}</h4>
                            <p class="count">{{$data->campaing_products->count()}} Products</p>
                        </div>
                        
                        {{-- Call to Action --}}
                        <a href="{{route('campaing.product',['slug'=>$data->slug])}}" class="campaign-cta">
                            Shop Now <i class="fal fa-arrow-right ml-1"></i>
                        </a>
                        
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection