@extends('layouts.frontend.app')

@section('title', 'Track Your Order')

@push('css')
<style>
    .tracking-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    .tracking-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 40px;
        text-align: center;
    }
    .tracking-icon {
        font-size: 4rem;
        color: #007bff;
        margin-bottom: 20px;
    }
    .tracking-form {
        margin-top: 30px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-control {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 12px 16px;
        font-size: 16px;
        transition: border-color 0.3s;
    }
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    .btn-track {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        border-radius: 8px;
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 600;
        color: white;
        transition: transform 0.2s;
    }
    .btn-track:hover {
        transform: translateY(-2px);
        color: white;
    }
    .help-text {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-top: 30px;
        text-align: left;
    }
    .help-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    .help-item i {
        color: #28a745;
        margin-right: 10px;
        width: 20px;
    }
</style>
@endpush

@section('content')

<div class="container">
    <div class="tracking-container">
        <div class="tracking-card">
            <i class="fas fa-search-location tracking-icon"></i>
            <h2>Track Your Order</h2>
            <p class="text-muted">Enter your invoice number or tracking code to check delivery status</p>

            <form action="{{ route('tracking.track') }}" method="POST" class="tracking-form">
                @csrf
                <div class="form-group">
                    <input type="text" 
                           class="form-control @error('tracking_input') is-invalid @enderror" 
                           name="tracking_input" 
                           value="{{ old('tracking_input') }}"
                           placeholder="Enter Invoice Number or Tracking Code"
                           required
                           autocomplete="off">
                    @error('tracking_input')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-track btn-lg">
                    <i class="fas fa-search"></i>
                    Track Order
                </button>
            </form>

            <div class="help-text">
                <h6><i class="fas fa-info-circle text-info"></i> How to track your order:</h6>
                <div class="help-item">
                    <i class="fas fa-check"></i>
                    <span>Use your <strong>Invoice Number</strong> (e.g., #00123)</span>
                </div>
                <div class="help-item">
                    <i class="fas fa-check"></i>
                    <span>Or use the <strong>Tracking Code</strong> provided by courier</span>
                </div>
                <div class="help-item">
                    <i class="fas fa-check"></i>
                    <span>You can find these details in your order confirmation email</span>
                </div>
            </div>

            @auth
            <div class="mt-4">
                <hr>
                <p class="text-muted">
                    <i class="fas fa-user"></i>
                    Logged in as {{ auth()->user()->name }}
                </p>
                <a href="{{ route('order') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list"></i>
                    View All My Orders
                </a>
            </div>
            @endauth
        </div>
    </div>
</div>

@endsection