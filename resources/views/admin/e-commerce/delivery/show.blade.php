@extends('layouts.admin.e-commerce.app')

@section('title', 'Delivery Details - ' . $order->invoice)

@push('css')
<style>
    .detail-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
    }
    .info-value {
        text-align: right;
        color: #6c757d;
    }
    .tracking-code {
        font-family: 'Courier New', monospace;
        background: #f8f9fa;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 1.1rem;
    }
    .consignment-id {
        font-family: 'Courier New', monospace;
        background: #e3f2fd;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 1.1rem;
        color: #1565c0;
    }
    .timeline {
        position: relative;
        padding: 20px 0;
    }
    .timeline-item {
        position: relative;
        padding-left: 40px;
        margin-bottom: 20px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        width: 2px;
        height: 100%;
        background: #dee2e6;
    }
    .timeline-item::after {
        content: '';
        position: absolute;
        left: 10px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #6c757d;
    }
    .timeline-item.active::after {
        background: #28a745;
    }
    .timeline-item.current::after {
        background: #007bff;
        width: 16px;
        height: 16px;
        left: 8px;
        top: 3px;
    }
</style>
@endpush

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Delivery Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{routeHelper('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.delivery.index') }}">Delivery Tracking</a></li>
                    <li class="breadcrumb-item active">{{ $order->invoice }}</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">

    <div class="row">
        <!-- Order Information -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-truck text-primary"></i>
                        Delivery Information
                    </h3>
                    <div class="card-tools">
                        @if($order->hasBeenSentToCourier())
                            <button type="button" class="btn btn-warning btn-sm" onclick="updateStatus()">
                                <i class="fas fa-sync"></i> Update Status
                            </button>
                        @else
                            <button type="button" class="btn btn-success btn-sm" onclick="sendToCourier()">
                                <i class="fas fa-truck"></i> Send to Courier
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-receipt text-primary"></i>
                            Order Invoice:
                        </span>
                        <span class="info-value">
                            <strong>{{ $order->invoice }}</strong>
                            <a href="{{ route('admin.order.show', $order->id) }}" class="btn btn-xs btn-outline-primary ml-2" target="_blank">
                                <i class="fas fa-external-link-alt"></i> View Order
                            </a>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-user text-info"></i>
                            Customer:
                        </span>
                        <span class="info-value">
                            <strong>{{ $order->first_name }} {{ $order->last_name }}</strong>
                            <br>
                            <small>{{ $order->phone }}</small>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            Delivery Address:
                        </span>
                        <span class="info-value">
                            {{ $order->address }}<br>
                            {{ $order->town }}, {{ $order->district }}
                            @if($order->post_code), {{ $order->post_code }}@endif
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-calendar text-secondary"></i>
                            Order Date:
                        </span>
                        <span class="info-value">
                            {{ $order->created_at->format('F d, Y \a\t H:i:s') }}
                            <small class="text-muted d-block">{{ $order->created_at->diffForHumans() }}</small>
                        </span>
                    </div>

                    @if($order->consignment_id)
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-barcode text-primary"></i>
                            Consignment ID:
                        </span>
                        <span class="info-value">
                            <span class="consignment-id">{{ $order->consignment_id }}</span>
                        </span>
                    </div>
                    @endif

                    @if($order->tracking_code)
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-search text-success"></i>
                            Tracking Code:
                        </span>
                        <span class="info-value">
                            <span class="tracking-code">{{ $order->tracking_code }}</span>
                            @if($order->tracking_url)
                                <br>
                                <a href="{{ $order->tracking_url }}" target="_blank" class="btn btn-sm btn-outline-info mt-1">
                                    <i class="fas fa-external-link-alt"></i> Track Online
                                </a>
                            @endif
                        </span>
                    </div>
                    @endif

                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-shipping-fast text-warning"></i>
                            Delivery Status:
                        </span>
                        <span class="info-value">
                            {!! $order->delivery_status_badge !!}
                            @if($order->delivery_status_updated_at)
                                <small class="text-muted d-block">Last updated: {{ $order->delivery_status_updated_at->diffForHumans() }}</small>
                            @endif
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-money-bill text-success"></i>
                            Payment:
                        </span>
                        <span class="info-value">
                            <strong>৳{{ number_format($order->total, 2) }}</strong>
                            @if($order->pay_staus == 1)
                                <br><span class="badge badge-success">Paid</span>
                                <small class="text-muted d-block">COD Amount: ৳0.00</small>
                            @else
                                <br><span class="badge badge-warning">Unpaid</span>
                                <small class="text-muted d-block">COD Amount: ৳{{ number_format($order->total, 2) }}</small>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Delivery Timeline -->
            @if($order->hasBeenSentToCourier())
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-route text-info"></i>
                        Delivery Timeline
                    </h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item active">
                            <h6>Order Placed</h6>
                            <p class="text-muted">{{ $order->created_at->format('M d, Y \a\t H:i A') }}</p>
                        </div>

                        <div class="timeline-item active">
                            <h6>Sent to Courier</h6>
                            <p class="text-muted">Order dispatched to Steadfast Courier</p>
                        </div>

                        @php
                            $currentStatus = $order->delivery_status;
                            $statuses = [
                                'in_review' => 'In Review',
                                'pending' => 'Pending Pickup',
                                'hold' => 'On Hold',
                                'cancelled' => 'Cancelled',
                                'delivered' => 'Delivered',
                                'partial_delivered' => 'Partially Delivered'
                            ];
                        @endphp

                        @if($currentStatus && $currentStatus !== 'in_review')
                        <div class="timeline-item {{ $currentStatus === 'delivered' || $currentStatus === 'partial_delivered' ? 'active' : 'current' }}">
                            <h6>{{ $statuses[$currentStatus] ?? ucfirst(str_replace('_', ' ', $currentStatus)) }}</h6>
                            @if($order->delivery_status_updated_at)
                                <p class="text-muted">{{ $order->delivery_status_updated_at->format('M d, Y \a\t H:i A') }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- API Response -->
            @if($order->delivery_response)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-code text-secondary"></i>
                        Latest API Response
                    </h3>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($order->delivery_response, JSON_PRETTY_PRINT) }}</code></pre>
                </div>
            </div>
            @endif
        </div>

        <!-- Action Panel -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools text-warning"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    @if(!$order->hasBeenSentToCourier())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            This order has not been sent to courier yet.
                        </div>
                        
                        <form onsubmit="return sendToCourierWithNote(event)">
                            <div class="form-group">
                                <label for="note">Delivery Note (Optional):</label>
                                <textarea class="form-control" id="delivery_note" rows="3" placeholder="Special delivery instructions..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-truck"></i>
                                Send to Courier
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-warning btn-block mb-2" onclick="updateStatus()">
                            <i class="fas fa-sync"></i>
                            Update Delivery Status
                        </button>

                        <a href="{{ route('admin.order.show', $order->id) }}" class="btn btn-primary btn-block mb-2" target="_blank">
                            <i class="fas fa-eye"></i>
                            View Full Order Details
                        </a>

                        @if($order->tracking_url)
                        <a href="{{ $order->tracking_url }}" class="btn btn-info btn-block mb-2" target="_blank">
                            <i class="fas fa-external-link-alt"></i>
                            Track on Courier Website
                        </a>
                        @endif

                        <hr>

                        <h6>Delivery Information:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Consignment ID:</strong> {{ $order->consignment_id }}</li>
                            <li><strong>Tracking Code:</strong> {{ $order->tracking_code }}</li>
                            <li><strong>Current Status:</strong> {{ $order->delivery_status_text }}</li>
                            @if($order->delivery_status_updated_at)
                            <li><strong>Last Updated:</strong> {{ $order->delivery_status_updated_at->format('M d, Y H:i') }}</li>
                            @endif
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Delivery Stats -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar text-info"></i>
                        Delivery Statistics
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $customerStats = \App\Models\Order::where('phone', $order->phone)
                                                         ->sentToCourier()
                                                         ->get();
                        $totalDeliveries = $customerStats->count();
                        $successfulDeliveries = $customerStats->where('delivery_status', 'delivered')->count();
                        $cancelledDeliveries = $customerStats->where('delivery_status', 'cancelled')->count();
                    @endphp

                    <div class="info-item">
                        <span class="info-label">Customer Deliveries:</span>
                        <span class="info-value"><strong>{{ $totalDeliveries }}</strong></span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Successful:</span>
                        <span class="info-value">
                            <span class="badge badge-success">{{ $successfulDeliveries }}</span>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Cancelled:</span>
                        <span class="info-value">
                            <span class="badge badge-danger">{{ $cancelledDeliveries }}</span>
                        </span>
                    </div>

                    @if($totalDeliveries > 0)
                    <div class="info-item">
                        <span class="info-label">Success Rate:</span>
                        <span class="info-value">
                            <strong>{{ round(($successfulDeliveries / $totalDeliveries) * 100, 1) }}%</strong>
                        </span>
                    </div>
                    @endif

                    @if($totalDeliveries > 1)
                    <div class="mt-3">
                        <a href="{{ route('admin.delivery.index', ['search' => $order->phone]) }}" 
                           class="btn btn-sm btn-outline-info btn-block">
                            <i class="fas fa-list"></i>
                            View All Deliveries for This Customer
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</section>

@endsection

@push('js')
<script>
function sendToCourier() {
    if (confirm('Are you sure you want to send this order to courier?')) {
        $.ajax({
            url: `/admin/delivery/send-to-courier/{{ $order->id }}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                note: 'Order from admin panel'
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Error sending order to courier');
            }
        });
    }
}

function sendToCourierWithNote(event) {
    event.preventDefault();
    
    const note = document.getElementById('delivery_note').value;
    
    if (confirm('Are you sure you want to send this order to courier?')) {
        $.ajax({
            url: `/admin/delivery/send-to-courier/{{ $order->id }}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                note: note || 'Order from admin panel'
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Error sending order to courier');
            }
        });
    }
    
    return false;
}

function updateStatus() {
    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    btn.disabled = true;

    $.ajax({
        url: `/admin/delivery/update-status/{{ $order->id }}`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            location.reload();
        },
        error: function(xhr) {
            alert('Error updating delivery status');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
}
</script>
@endpush