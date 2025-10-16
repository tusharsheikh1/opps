@extends('layouts.frontend.app')

@push('meta')
<meta name='description' content="Order Successfully Placed - Thank You for Your Purchase"/>
<meta name='keywords' content="Order Success, Book Order, E-commerce Success" />
@endpush

@section('title', 'Order Success - Thank You!')

@section('content')

<div class="order-success-container">
    <div class="container">
        <div class="order-success-card">
            <style>
                .order-success-container {
                    background: #f8fafc;
                    min-height: 90vh;
                    padding: 40px 0;
                }

                .order-success-card {
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                    max-width: 800px;
                    margin: 0 auto;
                    overflow: hidden;
                }

                .success-header {
                    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                }

                .success-icon {
                    width: 60px;
                    height: 60px;
                    background: rgba(255, 255, 255, 0.2);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 15px;
                }

                .success-icon i {
                    font-size: 28px;
                    color: white;
                }

                .success-title {
                    font-size: 1.8rem;
                    font-weight: 700;
                    margin-bottom: 8px;
                }

                .success-subtitle {
                    font-size: 1rem;
                    opacity: 0.9;
                }

                .order-content {
                    padding: 30px;
                }

                .order-number-section {
                    background: #f8fafc;
                    border: 2px solid #e2e8f0;
                    border-radius: 8px;
                    padding: 20px;
                    text-align: center;
                    margin-bottom: 30px;
                }

                .order-label {
                    font-size: 0.85rem;
                    color: #64748b;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin-bottom: 8px;
                }

                .order-number {
                    font-size: 1.5rem;
                    font-weight: 700;
                    color: #1e293b;
                    font-family: 'Courier New', monospace;
                }

                .details-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 30px;
                    margin-bottom: 30px;
                }

                .detail-section {
                    background: #f8fafc;
                    border-radius: 8px;
                    padding: 20px;
                }

                .section-title {
                    font-size: 1.1rem;
                    font-weight: 700;
                    color: #1e293b;
                    margin-bottom: 15px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }

                .section-title i {
                    color: #3b82f6;
                }

                .detail-item {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 10px;
                    padding: 8px 0;
                    border-bottom: 1px solid #e2e8f0;
                }

                .detail-item:last-child {
                    border-bottom: none;
                    margin-bottom: 0;
                }

                .detail-label {
                    font-weight: 500;
                    color: #64748b;
                }

                .detail-value {
                    font-weight: 600;
                    color: #1e293b;
                }

                .billing-section {
                    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
                    border-radius: 8px;
                    padding: 20px;
                    margin-bottom: 30px;
                }

                .total-amount {
                    font-size: 1.4rem;
                    font-weight: 700;
                    color: #059669;
                    text-align: center;
                    margin-top: 10px;
                }

                .notifications-section {
                    background: #fef3c7;
                    border: 2px solid #f59e0b;
                    border-radius: 8px;
                    padding: 20px;
                    margin-bottom: 30px;
                }

                .notification-item {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    margin-bottom: 10px;
                }

                .notification-item:last-child {
                    margin-bottom: 0;
                }

                .notification-icon {
                    width: 20px;
                    text-align: center;
                }

                .notification-icon.enabled {
                    color: #059669;
                }

                .notification-icon.disabled {
                    color: #dc2626;
                }

                .delivery-timeline {
                    background: #ecfdf5;
                    border: 2px solid #10b981;
                    border-radius: 8px;
                    padding: 20px;
                    margin-bottom: 30px;
                    text-align: center;
                }

                .timeline-title {
                    font-size: 1.1rem;
                    font-weight: 700;
                    color: #065f46;
                    margin-bottom: 15px;
                }

                .timeline-dates {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 15px;
                }

                .date-item {
                    text-align: center;
                }

                .date-label {
                    font-size: 0.85rem;
                    color: #059669;
                    font-weight: 600;
                    margin-bottom: 5px;
                }

                .date-value {
                    font-size: 1rem;
                    font-weight: 700;
                    color: #065f46;
                }

                .timeline-bar {
                    height: 4px;
                    background: #d1fae5;
                    border-radius: 2px;
                    position: relative;
                    margin-bottom: 10px;
                }

                .timeline-progress {
                    height: 100%;
                    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                    border-radius: 2px;
                    width: 20%;
                }

                .action-buttons {
                    display: flex;
                    gap: 15px;
                    justify-content: center;
                }

                .action-btn {
                    padding: 12px 24px;
                    border-radius: 8px;
                    font-weight: 600;
                    text-decoration: none;
                    transition: all 0.3s ease;
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    border: 2px solid transparent;
                    min-width: 150px;
                    justify-content: center;
                }

                .btn-primary {
                    background: #3b82f6;
                    color: white;
                    border-color: #3b82f6;
                }

                .btn-primary:hover {
                    background: #2563eb;
                    border-color: #2563eb;
                    transform: translateY(-1px);
                    color: white;
                    text-decoration: none;
                }

                .btn-secondary {
                    background: white;
                    color: #64748b;
                    border-color: #e2e8f0;
                }

                .btn-secondary:hover {
                    background: #f8fafc;
                    border-color: #cbd5e1;
                    color: #475569;
                    text-decoration: none;
                }

                /* Mobile Responsiveness */
                @media (max-width: 768px) {
                    .order-success-container {
                        padding: 20px 0;
                    }

                    .order-success-card {
                        margin: 0 20px;
                        border-radius: 8px;
                    }

                    .success-header {
                        padding: 20px;
                    }

                    .success-title {
                        font-size: 1.5rem;
                    }

                    .order-content {
                        padding: 20px;
                    }

                    .details-grid {
                        grid-template-columns: 1fr;
                        gap: 20px;
                    }

                    .timeline-dates {
                        flex-direction: column;
                        gap: 15px;
                    }

                    .action-buttons {
                        flex-direction: column;
                        align-items: center;
                    }

                    .action-btn {
                        width: 100%;
                        max-width: 250px;
                    }
                }
            </style>

            <!-- Success Header -->
            <div class="success-header">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="success-title">Order Confirmed!</h1>
                <p class="success-subtitle">Thank you for your purchase. Your order has been successfully placed.</p>
            </div>

            <!-- Order Content -->
            <div class="order-content">
                <!-- Order Number -->
                <div class="order-number-section">
                    <div class="order-label">Order Number</div>
                    <div class="order-number">{{ $data['invoice'] }}</div>
                </div>

                <!-- User & Billing Details Grid -->
                <div class="details-grid">
                    <!-- Customer Information -->
                    <div class="detail-section">
                        <h3 class="section-title">
                            <i class="fas fa-user"></i>
                            Customer Information
                        </h3>
                        <div class="detail-item">
                            <span class="detail-label">Name:</span>
                            <span class="detail-value">{{ $data['name'] ?? $data['customer_name'] ?? $data['billing_name'] ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Mobile:</span>
                            <span class="detail-value">{{ $data['phone'] ?? $data['mobile'] ?? $data['contact'] ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value">{{ $data['email'] ?? $data['customer_email'] ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Address:</span>
                            <span class="detail-value">{{ $data['address'] ?? $data['shipping_address'] ?? $data['billing_address'] ?? 'N/A' }}</span>
                        </div>
                        @if(isset($data['city']) || isset($data['area']))
                        <div class="detail-item">
                            <span class="detail-label">City/Area:</span>
                            <span class="detail-value">{{ $data['city'] ?? $data['area'] ?? 'N/A' }}</span>
                        </div>
                        @endif
                        @if(isset($data['postal_code']) || isset($data['zip']))
                        <div class="detail-item">
                            <span class="detail-label">Postal Code:</span>
                            <span class="detail-value">{{ $data['postal_code'] ?? $data['zip'] ?? 'N/A' }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Order Information -->
                    <div class="detail-section">
                        <h3 class="section-title">
                            <i class="fas fa-shopping-bag"></i>
                            Order Information
                        </h3>
                        <div class="detail-item">
                            <span class="detail-label">Order Date:</span>
                            <span class="detail-value">{{ now()->format('F d, Y') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Payment Method:</span>
                            <span class="detail-value">{{ $data['payment_method'] ?? 'Cash on Delivery' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value" style="color: #f59e0b;">Processing</span>
                        </div>
                    </div>
                </div>

                <!-- Billing Summary -->
                <div class="billing-section">
                    <h3 class="section-title">
                        <i class="fas fa-receipt"></i>
                        Billing Summary
                    </h3>
                    @if(isset($data['subtotal']))
                    <div class="detail-item">
                        <span class="detail-label">Subtotal:</span>
                        <span class="detail-value">{{ setting('CURRENCY_ICON') ?? '৳' }}{{ $data['subtotal'] }}</span>
                    </div>
                    @endif
                    @if(isset($data['shipping_cost']))
                    <div class="detail-item">
                        <span class="detail-label">Shipping Cost:</span>
                        <span class="detail-value">{{ setting('CURRENCY_ICON') ?? '৳' }}{{ $data['shipping_cost'] }}</span>
                    </div>
                    @endif
                    @if(isset($data['discount']))
                    <div class="detail-item">
                        <span class="detail-label">Discount:</span>
                        <span class="detail-value" style="color: #dc2626;">-{{ setting('CURRENCY_ICON') ?? '৳' }}{{ $data['discount'] }}</span>
                    </div>
                    @endif
                    <div class="total-amount">
                        Total: {{ setting('CURRENCY_ICON') ?? '৳' }}{{ $data['total'] ?? $data['grand_total'] ?? 'N/A' }}
                    </div>
                </div>

                <!-- Notification Preferences -->
                <div class="notifications-section">
                    <h3 class="section-title">
                        <i class="fas fa-bell"></i>
                        Notification Preferences
                    </h3>
                    <div class="notification-item">
                        <i class="fas fa-sms notification-icon enabled"></i>
                        <span><strong>SMS Notifications:</strong> You will receive order updates via SMS</span>
                    </div>
                    <div class="notification-item">
                        <i class="fas fa-envelope notification-icon disabled"></i>
                        <span><strong>Email Notifications:</strong> Email notifications are disabled</span>
                    </div>
                </div>

                <!-- Delivery Timeline -->
                <div class="delivery-timeline">
                    <h3 class="timeline-title">
                        <i class="fas fa-truck"></i>
                        Estimated Delivery Timeline
                    </h3>
                    <div class="timeline-dates">
                        <div class="date-item">
                            <div class="date-label">Order Date</div>
                            <div class="date-value">{{ now()->format('M d, Y') }}</div>
                        </div>
                        <div class="date-item">
                            <div class="date-label">Estimated Delivery</div>
                            <div class="date-value">{{ now()->addDays(5)->format('M d, Y') }}</div>
                        </div>
                    </div>
                    <div class="timeline-bar">
                        <div class="timeline-progress"></div>
                    </div>
                    <p style="color: #059669; font-weight: 600; margin: 0;">
                        Your order will be delivered within 5 business days
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{route('order')}}" class="action-btn btn-primary">
                        <i class="fas fa-list"></i>
                        Track My Orders
                    </a>
                    <a href="{{route('home')}}" class="action-btn btn-secondary">
                        <i class="fas fa-book"></i>
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
$(document).ready(function() {
    // Auto-scroll to top
    window.scrollTo(0, 0);
    
    // Simple animation for timeline progress
    setTimeout(() => {
        $('.timeline-progress').css('width', '20%');
    }, 500);
});
</script>
@endpush