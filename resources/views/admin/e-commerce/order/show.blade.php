@extends('layouts.admin.e-commerce.app')

@section('title', 'Order Information')

@push('css')
<style>
.btn-purple {
    background-color: #6f42c1;
    border-color: #6f42c1;
    color: white;
}

.btn-purple:hover {
    background-color: #5a359e;
    border-color: #5a359e;
    color: white;
}

.text-purple {
    color: #6f42c1 !important;
}

.gx {
    display: flex;
    background: #6dca6d24;
    padding: 5px;
    border-radius: 5px;
    margin-bottom: 10px;
}

.gx div {
    flex: 1;
}

.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    width: 350px;
}

.alert-dismissible {
    position: relative;
    padding-right: 4rem;
}

.alert-dismissible .close {
    position: absolute;
    top: 0;
    right: 0;
    padding: 0.75rem 1.25rem;
    color: inherit;
}

/* Courier Success Rate Card Styles */
.courier-success-card {
    display: inline-block;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 8px 12px;
    margin-left: 10px;
    min-width: 120px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
    animation: fadeIn 0.3s ease-in;
}

.courier-success-card.loading {
    background: #f8f9fa;
    border-color: #6c757d;
}

.courier-success-card .success-rate {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 2px;
}

.courier-success-card .success-rate.high {
    color: #28a745;
}

.courier-success-card .success-rate.medium {
    color: #ffc107;
}

.courier-success-card .success-rate.low {
    color: #dc3545;
}

.courier-success-card .success-rate.unknown {
    color: #6c757d;
}

.courier-success-card .success-label {
    font-size: 11px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.courier-success-card .loading-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.courier-success-card .order-count {
    font-size: 10px;
    color: #6c757d;
    margin-top: 2px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Courier History Styles */
.courier-logo {
    width: 40px;
    height: 40px;
    object-fit: contain;
    margin-right: 10px;
}

.risk-indicator {
    border-left: 4px solid;
    padding-left: 15px;
}

.risk-high {
    border-color: #dc3545;
}

.risk-medium {
    border-color: #ffc107;
}

.risk-low {
    border-color: #28a745;
}

.risk-new {
    border-color: #17a2b8;
}

.courier-stats {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

.success-rate-high {
    color: #28a745 !important;
    font-weight: bold;
}

.success-rate-medium {
    color: #ffc107 !important;
    font-weight: bold;
}

.success-rate-low {
    color: #dc3545 !important;
    font-weight: bold;
}

/* Phone number container */
.phone-container {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.phone-info {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 5px;
}

.stat-box {
    padding: 15px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.badge-lg {
    font-size: 1.2rem;
    padding: 0.5rem 1rem;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
}
</style>
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
            @include('admin.e-commerce.order.partials.modals.sms')
            @include('admin.e-commerce.order.partials.modals.refund')

            {{-- Phone History Modal - INLINE --}}
            <div class="modal fade" id="phoneHistoryModal" tabindex="-1" role="dialog" aria-labelledby="phoneHistoryModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="phoneHistoryModalLabel">
                                <i class="fas fa-history"></i> Customer History - <span id="historyPhone"></span>
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            {{-- Loading State --}}
                            <div id="historyLoading" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted">Loading customer history and courier data...</p>
                            </div>

                            {{-- Risk Assessment Section --}}
                            <div id="riskAssessment" style="display: none;">
                                <div class="card mb-3">
                                    <div class="card-header bg-gradient-warning">
                                        <h6 class="mb-0"><i class="fas fa-shield-alt"></i> Risk Assessment</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="riskAlert" class="alert risk-indicator" role="alert">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-2">
                                                        <span id="riskBadge" class="badge badge-lg"></span>
                                                    </h5>
                                                    <p class="mb-0" id="riskRecommendation"></p>
                                                </div>
                                                <div>
                                                    <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Courier History Section --}}
                            <div id="courierHistorySection" style="display: none;">
                                <div class="card mb-3">
                                    <div class="card-header bg-gradient-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-truck"></i> Courier Delivery History</h6>
                                    </div>
                                    <div class="card-body">
                                        {{-- Overall Stats --}}
                                        <div class="row courier-stats mb-3">
                                            <div class="col-md-3 text-center">
                                                <div class="stat-box">
                                                    <h4 class="text-primary mb-1" id="courierTotalOrders">0</h4>
                                                    <small class="text-muted text-uppercase">Total Orders</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <div class="stat-box">
                                                    <h4 class="text-success mb-1" id="courierSuccessful">0</h4>
                                                    <small class="text-muted text-uppercase">Delivered</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <div class="stat-box">
                                                    <h4 class="text-danger mb-1" id="courierCancelled">0</h4>
                                                    <small class="text-muted text-uppercase">Cancelled</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <div class="stat-box">
                                                    <h4 class="mb-1" id="courierSuccessRate">0%</h4>
                                                    <small class="text-muted text-uppercase">Success Rate</small>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Detailed Courier Breakdown --}}
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover table-sm">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Courier Service</th>
                                                        <th class="text-center">Total</th>
                                                        <th class="text-center">Delivered</th>
                                                        <th class="text-center">Cancelled</th>
                                                        <th class="text-center">Success Rate</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="courierDetailsTable">
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">
                                                            <i class="fas fa-spinner fa-spin"></i> Loading...
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- No Courier Data Message --}}
                            <div id="noCourierData" style="display: none;">
                                <div class="alert alert-info" role="alert">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>No Courier Data Available</strong>
                                    <p class="mb-0 mt-2">No courier delivery history found for this phone number.</p>
                                </div>
                            </div>

                            {{-- Customer Summary Section --}}
                            <div id="customerSummary" style="display: none;">
                                <div class="card mb-3">
                                    <div class="card-header bg-gradient-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-user"></i> Customer Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <p class="mb-2"><strong>Customer Name:</strong></p>
                                                <h5 class="text-primary" id="customerName">N/A</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-2"><strong>Customer Since:</strong></p>
                                                <h5 class="text-muted" id="customerSince">N/A</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-2"><strong>Total Orders:</strong></p>
                                                <h5 class="text-info" id="totalOrders">0</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-2"><strong>Total Spent:</strong></p>
                                                <h5 class="text-success" id="totalSpent">0</h5>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <p class="mb-2"><strong>Completed:</strong></p>
                                                <h5 class="badge badge-success badge-lg" id="completedOrders">0</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-2"><strong>Pending:</strong></p>
                                                <h5 class="badge badge-warning badge-lg" id="pendingOrders">0</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-2"><strong>Cancelled:</strong></p>
                                                <h5 class="badge badge-danger badge-lg" id="cancelledOrders">0</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-2"><strong>Avg Order Value:</strong></p>
                                                <h5 class="text-primary" id="avgOrderValue">0</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Orders List Section --}}
                            <div id="ordersContainer" style="display: none;">
                                <div class="card">
                                    <div class="card-header bg-gradient-secondary">
                                        <h6 class="mb-0"><i class="fas fa-shopping-cart"></i> Order History</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped mb-0">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Invoice</th>
                                                        <th>Date</th>
                                                        <th>Customer</th>
                                                        <th>Total</th>
                                                        <th>Payment</th>
                                                        <th>Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="historyOrdersList">
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted py-4">
                                                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                                                            <p class="mt-2">Loading orders...</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- No Orders Message --}}
                            <div id="noOrders" style="display: none;">
                                <div class="alert alert-warning text-center py-5" role="alert">
                                    <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                                    <h5>No Orders Found</h5>
                                    <p class="mb-0">No order history found for this phone number.</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Products --}}
            @include('admin.e-commerce.order.partials.order-products')
        </div>
    </section>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Load courier success rate on page load
    loadCourierSuccessRate();

    // Edit Order Form Submission
    $('#editOrderForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#updateOrderBtn');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        $('.modal-body .alert').remove();

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                showNotification('success', 'Order updated successfully!');
                $('#editOrderModal').modal('hide');
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            },
            error: function(xhr) {
                let errorMessage = 'Failed to update order. Please try again.';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const errorList = Object.values(errors).flat();
                    errorMessage = 'Validation Error: ' + errorList.join(', ');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                const errorAlert = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Error!</strong> ${errorMessage}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `;
                $('.modal-body').prepend(errorAlert);
                $('.modal-body').scrollTop(0);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Update order summary when shipping or discount changes
    const subtotal = parseFloat($('[data-subtotal]').attr('data-subtotal')) || 0;
    const currency = $('[data-currency]').attr('data-currency') || 'TK';

    $('#shipping_charge, #discount').on('input', function() {
        const shipping = parseFloat($('#shipping_charge').val()) || 0;
        const discount = parseFloat($('#discount').val()) || 0;
        const total = Math.max(0, subtotal + shipping - discount);

        $('#summary_shipping').text(shipping.toFixed(2) + ' ' + currency);
        $('#summary_discount').text('-' + discount.toFixed(2) + ' ' + currency);
        $('#summary_total').text(total.toFixed(2) + ' ' + currency);
    });
});

// Load courier success rate
function loadCourierSuccessRate() {
    const phone = $('#courierSuccessCard').data('phone');
    if (!phone) return;

    $.ajax({
        url: '{{ route("admin.order.phone-history") }}',
        type: 'GET',
        data: { phone: phone },
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        success: function(response) {
            updateCourierSuccessCard(response.courier_history);
        },
        error: function() {
            updateCourierSuccessCard(null);
        }
    });
}

// Update courier success card
function updateCourierSuccessCard(courierData) {
    const card = $('#courierSuccessCard');

    if (courierData && courierData.success && courierData.data) {
        const data = courierData.data;
        const successRate = data.success_rate;
        const totalOrders = data.total_orders;

        let rateClass = 'unknown';
        if (totalOrders === 0) {
            rateClass = 'unknown';
        } else if (successRate >= 85) {
            rateClass = 'high';
        } else if (successRate >= 70) {
            rateClass = 'medium';
        } else {
            rateClass = 'low';
        }

        card.removeClass('loading').html(`
            <div class="success-rate ${rateClass}">${totalOrders > 0 ? successRate + '%' : 'N/A'}</div>
            <div class="success-label">Delivery Rate</div>
            <div class="order-count">${totalOrders} courier orders</div>
        `);

        card.attr('title',
            totalOrders > 0
                ? `Courier success rate: ${successRate}% (${data.total_successful}/${totalOrders} delivered)`
                : 'No courier delivery history found'
        ).tooltip();

    } else {
        card.removeClass('loading').addClass('unknown').html(`
            <div class="success-rate unknown">N/A</div>
            <div class="success-label">No Data</div>
        `);
        card.attr('title', 'No courier delivery data available').tooltip();
    }
}

// Show notification
function showNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

    const notification = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${icon}"></i>
            <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

    $('#notificationContainer').html(notification);

    setTimeout(function() {
        $('#notificationContainer .alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
}

// SMS Template loader
function loadTemplate() {
    const template = document.getElementById('sms_templates').value;
    const messageField = document.getElementById('message');

    if (template) {
        messageField.value = template;
        updateCharCount();
    }
}

// Update SMS character count
function updateCharCount() {
    const messageField = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    const sendBtn = document.getElementById('sendSmsBtn');

    if (!messageField || !charCount || !sendBtn) return;

    const message = messageField.value;
    charCount.textContent = message.length;

    if (message.length > 500) {
        charCount.style.color = 'red';
        sendBtn.disabled = true;
    } else {
        charCount.style.color = 'green';
        sendBtn.disabled = false;
    }
}

// Show phone history modal - WORKING VERSION
function showPhoneHistory(phone) {
    // Show modal
    $('#phoneHistoryModal').modal('show');

    // Set phone number in modal title
    document.getElementById('historyPhone').textContent = phone;

    // Show loading state and hide all sections
    document.getElementById('historyLoading').style.display = 'block';
    document.getElementById('riskAssessment').style.display = 'none';
    document.getElementById('courierHistorySection').style.display = 'none';
    document.getElementById('noCourierData').style.display = 'none';
    document.getElementById('customerSummary').style.display = 'none';
    document.getElementById('ordersContainer').style.display = 'none';
    document.getElementById('noOrders').style.display = 'none';

    // Fetch phone history with courier data
    $.ajax({
        url: '{{ route("admin.order.phone-history") }}',
        type: 'GET',
        data: { phone: phone },
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        success: function(response) {
            document.getElementById('historyLoading').style.display = 'none';

            // Show risk assessment
            if (response.risk_assessment) {
                showRiskAssessment(response.risk_assessment);
            }

            // Show courier history
            if (response.courier_history) {
                showCourierHistory(response.courier_history);
            }

            // Show order history
            if (response.success && response.orders && response.orders.length > 0) {
                document.getElementById('customerSummary').style.display = 'block';
                document.getElementById('ordersContainer').style.display = 'block';

                const summary = response.summary;
                const currency = '{{ setting("CURRENCY_CODE_MIN") ?? "TK" }}';
                
                document.getElementById('customerName').textContent = summary.primary_name || 'N/A';
                document.getElementById('customerSince').textContent = summary.customer_since || 'N/A';
                document.getElementById('totalOrders').textContent = summary.total_orders;
                document.getElementById('totalSpent').textContent = currency + ' ' + summary.total_spent.toLocaleString();
                document.getElementById('completedOrders').textContent = summary.completed_orders;
                document.getElementById('pendingOrders').textContent = summary.pending_orders;
                document.getElementById('cancelledOrders').textContent = summary.cancelled_orders;
                document.getElementById('avgOrderValue').textContent = currency + ' ' + summary.avg_order_value.toLocaleString();

                // Populate orders table
                const tbody = document.getElementById('historyOrdersList');
                tbody.innerHTML = '';

                response.orders.forEach(function(order) {
                    const statusBadge = getStatusBadge(order.status);
                    const row = `
                        <tr>
                            <td><strong class="text-primary">${order.invoice}</strong></td>
                            <td><small>${order.created_at}</small></td>
                            <td>${order.customer_name}</td>
                            <td><strong class="text-success">${currency} ${parseFloat(order.total).toLocaleString()}</strong></td>
                            <td><span class="badge badge-info">${order.payment_method}</span></td>
                            <td>${statusBadge}</td>
                            <td>
                                <a href="{{ route('admin.order.show', '') }}/${order.id}" class="btn btn-sm btn-outline-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });

            } else {
                document.getElementById('noOrders').style.display = 'block';
            }
        },
        error: function(xhr, status, error) {
            console.error('Phone history fetch failed:', {xhr, status, error});
            document.getElementById('historyLoading').style.display = 'none';
            document.getElementById('noOrders').style.display = 'block';
        }
    });
}

// Show risk assessment
function showRiskAssessment(riskData) {
    const riskSection = document.getElementById('riskAssessment');
    const riskAlert = document.getElementById('riskAlert');
    const riskRecommendation = document.getElementById('riskRecommendation');
    const riskBadge = document.getElementById('riskBadge');

    riskAlert.className = `alert alert-${riskData.color} risk-indicator risk-${riskData.risk_level}`;
    riskRecommendation.textContent = riskData.recommendation;
    riskBadge.className = `badge badge-lg badge-${riskData.color}`;
    riskBadge.textContent = riskData.risk_level.toUpperCase() + ' RISK';

    riskSection.style.display = 'block';
}

// Show courier history
function showCourierHistory(courierData) {
    if (courierData.success && courierData.data) {
        const data = courierData.data;

        document.getElementById('courierHistorySection').style.display = 'block';

        document.getElementById('courierTotalOrders').textContent = data.total_orders;
        document.getElementById('courierSuccessful').textContent = data.total_successful;
        document.getElementById('courierCancelled').textContent = data.total_cancelled;
        document.getElementById('courierSuccessRate').textContent = data.success_rate + '%';

        const successRateElement = document.getElementById('courierSuccessRate');
        successRateElement.classList.remove('success-rate-high', 'success-rate-medium', 'success-rate-low');

        if (data.success_rate >= 85) {
            successRateElement.classList.add('success-rate-high');
        } else if (data.success_rate >= 70) {
            successRateElement.classList.add('success-rate-medium');
        } else {
            successRateElement.classList.add('success-rate-low');
        }

        const tbody = document.getElementById('courierDetailsTable');
        tbody.innerHTML = '';

        if (data.couriers && data.couriers.length > 0) {
            data.couriers.forEach(function(courier) {
                let successRateClass = 'text-secondary';
                if (courier.success_rate >= 85) {
                    successRateClass = 'text-success';
                } else if (courier.success_rate >= 70) {
                    successRateClass = 'text-warning';
                } else if (courier.success_rate > 0) {
                    successRateClass = 'text-danger';
                }

                const row = `
                    <tr>
                        <td><strong><i class="fas fa-truck text-muted mr-2"></i>${courier.name}</strong></td>
                        <td class="text-center"><span class="badge badge-secondary">${courier.total}</span></td>
                        <td class="text-center"><span class="badge badge-success">${courier.successful}</span></td>
                        <td class="text-center"><span class="badge badge-danger">${courier.cancelled}</span></td>
                        <td class="text-center"><strong class="${successRateClass}">${courier.success_rate}%</strong></td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted"><i class="fas fa-inbox"></i> No courier data available</td></tr>';
        }

    } else {
        document.getElementById('noCourierData').style.display = 'block';
    }
}

// Get status badge
function getStatusBadge(status) {
    const statusMap = {
        0: '<span class="badge badge-warning">Pending</span>',
        1: '<span class="badge badge-primary">Processing</span>',
        2: '<span class="badge badge-danger">Cancelled</span>',
        3: '<span class="badge badge-success">Delivered</span>',
        4: '<span class="badge" style="background: #17a2b8; color: white;">Shipping</span>',
        5: '<span class="badge badge-secondary">Refund</span>',
        6: '<span class="badge badge-warning">Return Requested</span>',
        7: '<span class="badge badge-info">Return Accepted</span>',
        8: '<span class="badge badge-dark">Returned</span>',
        9: '<span class="badge badge-success">Sent to Courier</span>'
    };

    return statusMap[status] || '<span class="badge badge-secondary">Unknown</span>';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const messageField = document.getElementById('message');
    if (messageField) {
        updateCharCount();
    }
});
</script>
@endpush