@extends('layouts.admin.e-commerce.app')

@section('title', 'Order Information')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/order-show.css') }}">
@endpush

@section('content')
    {{-- Header / Breadcrumb --}}
    @include('admin.e-commerce.order.partials.header')

    {{-- Notification Container --}}
    <div class="notification-container" id="notificationContainer"></div>

    <section class="content">
        <div class="container-fluid">
            {{-- Session Alerts --}}
            @include('admin.e-commerce.order.partials.alerts')

            {{-- Customer Information Card --}}
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="card-title">Customer Information</h3>
                        </div>
                        {{-- Action Buttons --}}
                        @include('admin.e-commerce.order.partials.action-buttons')
                    </div>
                </div>
                <div class="card-body" data-customer-phone="{{ $order->phone }}" data-subtotal="{{ $order->subtotal }}" data-currency="{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}">
                    @include('admin.e-commerce.order.partials.customer-info-table')
                </div>
            </div>

            {{-- Modals --}}
            @include('admin.e-commerce.order.partials.modals.edit-order')
            @include('admin.e-commerce.order.partials.modals.phone-history')
            @include('admin.e-commerce.order.partials.modals.sms')
            @include('admin.e-commerce.order.partials.modals.refund')

            {{-- Order Products --}}
            @include('admin.e-commerce.order.partials.order-products')
        </div>
    </section>
@endsection

@push('js')
    <script>
        // Set route variables for JavaScript
        window.phoneHistoryRoute = '{{ route("admin.order.phone-history") }}';
        window.orderShowRouteBase = '{{ route('admin.order.show', '') }}';
    </script>
    <script src="{{ asset('js/order-show.js') }}"></script>
    
    {{-- Load template function for SMS --}}
    <script>
        function loadTemplate() {
            const template = document.getElementById('sms_templates').value;
            const messageField = document.getElementById('message');
            
            if (template) {
                // Replace template variables with actual values
                let message = template;
                message = message.replace('{invoice}', '{{ $order->invoice }}');
                message = message.replace('{customer_name}', '{{ $order->first_name }}');
                message = message.replace('{total}', '{{ $order->total }}');
                
                messageField.value = message;
                updateCharCount();
            }
        }
    </script>
@endpush