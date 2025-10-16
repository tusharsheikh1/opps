@extends('layouts.frontend.app')

@section('title', 'Order Tracking - ' . $order->invoice)

@push('css')
<style>
    .tracking-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    .tracking-header {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        margin-bottom: 30px;
    }
    .tracking-status {
        font-size: 1.5rem;
        font-weight: bold;
        margin-top: 10px;
    }
    .order-info {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 30px;
        margin-bottom: 30px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
    }
    .info-value {
        color: #6c757d;
        text-align: right;
    }
    .tracking-code {
        font-family: 'Courier New', monospace;
        background: #f8f9fa;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 1.1rem;
    }
    .timeline {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 30px;
        margin-bottom: 30px;
    }
    .timeline-item {
        position: relative;
        padding-left: 40px;
        margin-bottom: 25px;
    }
    .timeline-item:last-child {
        margin-bottom: 0;
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
    .timeline-item:last-child::before {
        display: none;
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
    .timeline-item.completed::after {
        background: #28a745;
    }
    .timeline-item.current::after {
        background: #007bff;
        width: 16px;
        height: 16px;
        left: 8px;
        top: 3px;
    }
    .timeline-title {
        font-weight: 600;
        margin-bottom: 5px;
    }
    .timeline-date {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .refresh-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #007bff;
        color: white;
        border: none;
        box-shadow: 0 4px 20px rgba(0,123,255,0.3);
        font-size: 1.2rem;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .refresh-btn:hover {
        transform: scale(1.1);
        color: white;
    }
    .refresh-btn:focus {
        outline: none;
        color: white;
    }
    .alert-custom {
        border-radius: 8px;
        border: none;
        padding: 15px 20px;
    }
    @media (max-width: 768px) {
        .tracking-container {
            padding: 10px;
        }
        .info-row {
            flex-direction: column;
            align-items: flex-start;
        }
        .info-value {
            text-align: left;
            margin-top: 5px;
        }
    }
</style>
@endpush

@section('content')

<div class="container">
    <div class="tracking-container">
        
        <!-- Tracking Header -->
        <div class="tracking-header">
            <h2><i class="fas fa-receipt"></i> {{ $order->invoice }}</h2>
            <div class="tracking-status" id="deliveryStatus">
                {!! $order->delivery_status_badge !!}
            </div>
            @if($order->delivery_status_updated_at)
            <p class="mb-0 mt-2">
                <i class="fas fa-clock"></i>
                Last updated: <span id="lastUpdated">{{ $order->delivery_status_updated_at->diffForHumans() }}</span>
            </p>
            @endif
        </div>

        <!-- Order Information -->
        <div class="order-info">
            <h5><i class="fas fa-info-circle text-primary"></i> Order Information</h5>
            <hr>
            
            <div class="info-row">
                <span class="info-label">Order Date:</span>
                <span class="info-value">{{ $order->created_at->format('F d, Y \a\t H:i A') }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Customer:</span>
                <span class="info-value">{{ $order->first_name }} {{ $order->last_name }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $order->formatted_phone }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Delivery Address:</span>
                <span class="info-value">
                    {{ $order->address }}<br>
                    {{ $order->town }}, {{ $order->district }}
                    @if($order->post_code), {{ $order->post_code }}@endif
                </span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Total Amount:</span>
                <span class="info-value">
                    <strong>৳{{ number_format($order->total, 2) }}</strong>
                </span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Payment Status:</span>
                <span class="info-value">
                    @if($order->pay_staus == 1)
                        <span class="badge badge-success">Paid</span>
                    @else
                        <span class="badge badge-warning">Cash on Delivery</span>
                    @endif
                </span>
            </div>

            @if($order->hasBeenSentToCourier())
            <div class="info-row">
                <span class="info-label">Tracking Code:</span>
                <span class="info-value">
                    <span class="tracking-code" id="trackingCode">{{ $order->tracking_code }}</span>
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Consignment ID:</span>
                <span class="info-value">
                    <span class="tracking-code">{{ $order->consignment_id }}</span>
                </span>
            </div>
            @endif
        </div>

        @if($order->hasBeenSentToCourier())
        <!-- Delivery Status Alert -->
        @if($order->delivery_status == 'delivered')
        <div class="alert alert-success alert-custom">
            <i class="fas fa-check-circle"></i>
            <strong>Great news!</strong> Your order has been delivered successfully.
        </div>
        @elseif($order->delivery_status == 'cancelled')
        <div class="alert alert-danger alert-custom">
            <i class="fas fa-times-circle"></i>
            <strong>Order Cancelled:</strong> Your order delivery has been cancelled. Please contact customer support for more information.
        </div>
        @elseif($order->delivery_status == 'hold')
        <div class="alert alert-warning alert-custom">
            <i class="fas fa-pause-circle"></i>
            <strong>On Hold:</strong> Your order is temporarily on hold. Our delivery team will contact you soon.
        </div>
        @else
        <div class="alert alert-info alert-custom">
            <i class="fas fa-truck"></i>
            <strong>In Transit:</strong> Your order is on its way to you. We'll update you once it's delivered.
        </div>
        @endif

        <!-- Delivery Timeline -->
        <div class="timeline">
            <h5><i class="fas fa-route text-primary"></i> Delivery Timeline</h5>
            <hr>
            
            <div class="timeline-item completed">
                <div class="timeline-title">Order Placed</div>
                <div class="timeline-date">{{ $order->created_at->format('M d, Y \a\t H:i A') }}</div>
            </div>

            <div class="timeline-item completed">
                <div class="timeline-title">Order Confirmed</div>
                <div class="timeline-date">Payment and order details verified</div>
            </div>

            <div class="timeline-item completed">
                <div class="timeline-title">Sent to Courier</div>
                <div class="timeline-date">Order dispatched to delivery partner</div>
            </div>

            @php
                $currentStatus = $order->delivery_status;
                $statusFlow = [
                    'in_review' => ['title' => 'In Review', 'desc' => 'Order is being reviewed by courier'],
                    'pending' => ['title' => 'Pending Pickup', 'desc' => 'Waiting for courier pickup'],
                    'hold' => ['title' => 'On Hold', 'desc' => 'Temporarily held for processing'],
                    'cancelled' => ['title' => 'Cancelled', 'desc' => 'Delivery cancelled'],
                    'delivered' => ['title' => 'Delivered', 'desc' => 'Successfully delivered to customer'],
                    'partial_delivered' => ['title' => 'Partially Delivered', 'desc' => 'Part of order delivered']
                ];
            @endphp

            @if(isset($statusFlow[$currentStatus]))
            <div class="timeline-item {{ in_array($currentStatus, ['delivered', 'partial_delivered']) ? 'completed' : 'current' }}">
                <div class="timeline-title">{{ $statusFlow[$currentStatus]['title'] }}</div>
                <div class="timeline-date">
                    {{ $statusFlow[$currentStatus]['desc'] }}
                    @if($order->delivery_status_updated_at)
                        <br>{{ $order->delivery_status_updated_at->format('M d, Y \a\t H:i A') }}
                    @endif
                </div>
            </div>
            @endif

            @if(!in_array($currentStatus, ['delivered', 'partial_delivered', 'cancelled']))
            <div class="timeline-item">
                <div class="timeline-title">Out for Delivery</div>
                <div class="timeline-date">Order will be out for final delivery</div>
            </div>

            <div class="timeline-item">
                <div class="timeline-title">Delivered</div>
                <div class="timeline-date">Order successfully delivered</div>
            </div>
            @endif
        </div>
        @else
        <!-- Order not sent to courier yet -->
        <div class="alert alert-info alert-custom">
            <i class="fas fa-clock"></i>
            <strong>Processing:</strong> Your order is being prepared and will be sent to courier soon.
        </div>

        <div class="timeline">
            <h5><i class="fas fa-route text-primary"></i> Order Progress</h5>
            <hr>
            
            <div class="timeline-item completed">
                <div class="timeline-title">Order Placed</div>
                <div class="timeline-date">{{ $order->created_at->format('M d, Y \a\t H:i A') }}</div>
            </div>

            <div class="timeline-item current">
                <div class="timeline-title">Order Processing</div>
                <div class="timeline-date">Your order is being prepared for shipment</div>
            </div>

            <div class="timeline-item">
                <div class="timeline-title">Sent to Courier</div>
                <div class="timeline-date">Order will be dispatched to delivery partner</div>
            </div>

            <div class="timeline-item">
                <div class="timeline-title">Out for Delivery</div>
                <div class="timeline-date">Order will be out for delivery</div>
            </div>

            <div class="timeline-item">
                <div class="timeline-title">Delivered</div>
                <div class="timeline-date">Order will be delivered to you</div>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="text-center mt-4">
            <a href="{{ route('tracking.index') }}" class="btn btn-primary">
                <i class="fas fa-search"></i>
                Track Another Order
            </a>
            
            @auth
                @if($order->user_id == auth()->id())
                <a href="{{ route('order') }}" class="btn btn-outline-primary ml-2">
                    <i class="fas fa-list"></i>
                    View All Orders
                </a>
                @endif
            @endauth

            @if($order->hasBeenSentToCourier() && $order->tracking_url)
            <a href="{{ $order->tracking_url }}" target="_blank" class="btn btn-outline-info ml-2">
                <i class="fas fa-external-link-alt"></i>
                Track on Courier Website
            </a>
            @endif
        </div>
    </div>
</div>

<!-- Refresh Button -->
@if($order->hasBeenSentToCourier())
<button class="refresh-btn" onclick="refreshStatus()" title="Refresh Status" id="refreshBtn">
    <i class="fas fa-sync-alt"></i>
</button>
@endif

@endsection

@push('js')
<script>
@if($order->hasBeenSentToCourier())
function refreshStatus() {
    const btn = document.getElementById('refreshBtn');
    const icon = btn.querySelector('i');
    
    // Show loading state
    icon.className = 'fas fa-spinner fa-spin';
    btn.disabled = true;
    
    $.ajax({
        url: '{{ route("tracking.status") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            order_id: {{ $order->id }}
        },
        success: function(response) {
            if (response.success) {
                // Update the display
                if (response.updated) {
                    document.getElementById('deliveryStatus').innerHTML = response.data.delivery_status_badge;
                    if (response.data.last_updated) {
                        document.getElementById('lastUpdated').textContent = response.data.last_updated;
                    }
                    
                    // Show success message
                    showNotification('Status updated successfully!', 'success');
                    
                    // Reload page after a short delay to show updated timeline
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showNotification('Status is up to date', 'info');
                }
            }
        },
        error: function(xhr) {
            showNotification('Failed to update status', 'error');
        },
        complete: function() {
            // Reset button state
            icon.className = 'fas fa-sync-alt';
            btn.disabled = false;
        }
    });
}

function showNotification(message, type) {
    // Simple notification - you can replace this with your notification system
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            ${message}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    `);
    
    $('body').append(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.alert('close');
    }, 3000);
}

// Auto refresh every 5 minutes
setInterval(refreshStatus, 300000);
@endif
</script>
@endpush