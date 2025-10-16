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
</style>
@endpush

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Order</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ routeHelper('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Order Information</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Notification Container -->
    <div class="notification-container" id="notificationContainer">
        <!-- Notifications will be inserted here -->
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Display Flash Messages -->
            @if(session('success') || session('error') || session('warning') || session('info'))
                <div class="row">
                    <div class="col-12">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i>
                                <strong>Success!</strong> {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Error!</strong> {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if(session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i>
                                <strong>Warning!</strong> {{ session('warning') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if(session('info'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="fas fa-info-circle"></i>
                                <strong>Info!</strong> {{ session('info') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Display Validation Errors -->
            @if($errors->any())
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Validation Error!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="card-title">Customer Information</h3>
                        </div>
                        <div class="col-sm-12 col-12 text-right"
                            style="display: flex;
                    flex-direction: row;
                    align-items: center;
                    justify-content: flex-end;
                    grid-column-gap: 8px;">
                            @if ($order->status != 5)
                                @if ($order->status != 2)
                                    @if ($order->status != 3)
                                        <a title="@if ($order->pay_staus == 1) Unpaid @else Paid @endif"
                                            href="{{ route('admin.order.pay', ['id' => $order->id]) }}"
                                            class="btn @if ($order->pay_staus == 1) btn-danger @else btn-success @endif btn-sm">
                                            <i class="fas fa-money-bill"></i>
                                            @if ($order->pay_staus == 1)
                                                Unpaid
                                            @else
                                                Paid
                                            @endif
                                        </a>
                                    @endif
                                @endif

                                @if (setting('STEEDFAST_STATUS') == 1 && $order->status != 9)
                                    <form action="{{ route('admin.setting.courier.sendsteedfast') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="invoice" value="{{ $order->invoice }}">
                                        <input type="hidden" name="recipient_name" value="{{ $order->first_name }}">
                                        <input type="hidden" name="recipient_phone" value="{{ $order->phone }}">
                                        <input type="hidden" name="recipient_address"
                                            value="{{ $order->address . ', ' . $order->town . ', ' . $order->district . ', ' . $order->post_code }}">
                                        @if ($order->pay_staus == 1)
                                            <input type="hidden" name="cod_amount" value="0.00">
                                        @else
                                            <input type="hidden" name="cod_amount" value="{{ $order->total }}">
                                        @endif
                                        <input type="hidden" name="note" value="N/A">
                                        <input class="btn btn-info btn-sm" type="submit" value="Send Courier">
                                    </form>
                                @else
                                    <i class="btn btn-info btn-sm">Courierd Already</i>
                                @endif

                                <!-- Edit Order Button -->
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editOrderModal">
                                    <i class="fas fa-edit"></i>
                                    Edit Order
                                </button>

                                <!-- Custom SMS Button -->
                                @if(!empty($order->phone))
                                    <button type="button" class="btn btn-purple btn-sm" data-toggle="modal" data-target="#smsModal">
                                        <i class="fas fa-sms"></i>
                                        Send SMS
                                    </button>
                                @endif

                                <a title="Processing" href="{{ routeHelper('order/status/processing/' . $order->id) }}"
                                    onclick="return confirm('Are you sure you want to change the status of this order?')"
                                    class="btn btn-primary btn-sm">
                                    <i class="fas fa-running"></i>
                                    Processing
                                </a>

                                @if ($order->status == 6)
                                    <a title="Accept return request"
                                        href="{{ routeHelper('order/status/return_req_accept/' . $order->id) }}"
                                        onclick="return confirm('Return process will start. Are you sure?')" class="btn btn-success btn-sm">
                                        Return Accept
                                    </a>
                                @elseif ($order->status == 7)
                                    <a title="Complete the return process, you got the product from customer as a return completely."
                                        href="{{ routeHelper('order/status/return_complete/' . $order->id) }}"
                                        onclick="return confirm('Complete the return, you got the product from customer?')"
                                        class="btn btn-success btn-sm">
                                        Return Complete
                                    </a>
                                @elseif ($order->status != 2 && $order->status != 3 && $order->status != 6 && $order->status != 7 && $order->status != 8)
                                    <a title="Shipping" href="{{ routeHelper('order/status/shipping/' . $order->id) }}"
                                        id="btnShipping" onclick="return confirm('Are you sure you want to mark this order as shipping?')"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-plane"></i> Shipping
                                    </a>

                                    <a title="Delivered" href="{{ routeHelper('order/status/delivered/' . $order->id) }}"
                                        onclick="return confirm('Are you sure you want to mark this order as delivered?')"
                                        class="btn btn-success btn-sm">
                                        <i class="fas fa-thumbs-up"></i>
                                        Delivered
                                    </a>
                                @endif
                                @if ($order->status != 3 && $order->status != 2)
                                    <a title="Cancel" href="{{ routeHelper('order/status/cancel/' . $order->id) }}"
                                        onclick="return confirm('Are you sure you want to cancel this order?')"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-window-close"></i>
                                        Cancel
                                    </a>
                                @endif
                            @endif
                            @if ($order->status == 3)
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                    data-target="#refund">
                                    Refund
                                </button>
                            @endif
                            @if ($order->status == 2)
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                    data-target="#refund2">
                                    Refund
                                </button>
                            @endif
                            <a href="{{ route('admin.order.delete', ['did' => $order->id]) }}"
                                onclick="return confirm('Are you sure you want to delete this order permanently?')"
                                class="btn btn-danger btn-sm"><i class="nav-icon fas fa-trash-alt"></i> Delete</a>
                            <a href="{{ routeHelper('order/print/' . $order->id) }}" rel="noopener" target="_blank"
                                class="btn btn-default"><i class="fas fa-print"></i> Print</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <tbody>
                            @if (!empty($order->meet_time))
                                <tr>
                                    <th>Meet Time</th>
                                    <td>{{ $order->meet_time }}</td>

                                </tr>
                            @endif
                            <tr>
                                <th>Customer Name</th>
                                <td>{{ $order->first_name }}</td>
                                <th>Order ID</th>
                                <td>{{ $order->order_id }}</td>
                            </tr>
                            <tr>
                                <th>Invoice</th>
                                <td>{{ $order->invoice }}</td>
                                <th>Company Name</th>
                                <td>{{ $order->company_name }}</td>
                            </tr>
                            <tr>
                                <th>Country</th>
                                <td>{{ $order->country }}</td>
                                <th>Address</th>
                                <td>{{ $order->address }}</td>
                            </tr>
                            <tr>
                                <th>Town</th>
                                <td>{{ $order->town }}</td>
                                <th>District</th>
                                <td>{{ $order->district }}</td>
                            </tr>
                            <tr>
                                <th>Post Code</th>
                                <td>{{ $order->post_code }}</td>
                                <th>Phone</th>
                                <td>
                                    @if($order->phone)
                                        <div class="phone-container">
                                            <div class="phone-info">
                                                <a href="tel:{{ $order->phone }}" class="text-decoration-none">
                                                    <i class="fas fa-phone text-success"></i> {{ $order->phone }}
                                                </a>
                                                
                                                <!-- Order History Badge -->
                                                @php
                                                    $cleanPhone = preg_replace('/[^0-9+]/', '', $order->phone);
                                                    $phoneOrderCount = \App\Models\Order::where(function($query) use ($order, $cleanPhone) {
                                                        $query->where('phone', $order->phone)->orWhere('phone', $cleanPhone);
                                                        if (strlen($cleanPhone) >= 10) {
                                                            if (!str_starts_with($cleanPhone, '+88')) {
                                                                $query->orWhere('phone', '+88' . ltrim($cleanPhone, '0'));
                                                            }
                                                            if (str_starts_with($cleanPhone, '+88')) {
                                                                $query->orWhere('phone', '0' . substr($cleanPhone, 3))
                                                                      ->orWhere('phone', substr($cleanPhone, 3));
                                                            }
                                                        }
                                                    })->count();
                                                @endphp
                                                @if($phoneOrderCount > 1)
                                                    <span class="badge badge-primary cursor-pointer" 
                                                          title="Click to view {{ $phoneOrderCount }} orders from this customer"
                                                          onclick="showPhoneHistory('{{ $order->phone }}')"
                                                          data-toggle="tooltip"
                                                          style="cursor: pointer;">
                                                        {{ $phoneOrderCount }} orders
                                                    </span>
                                                @endif
                                                
                                                <!-- Courier History Button -->
                                                <button type="button" class="btn btn-xs btn-outline-info" 
                                                        onclick="showPhoneHistory('{{ $order->phone }}')"
                                                        title="View courier delivery history">
                                                    <i class="fas fa-shipping-fast"></i> History
                                                </button>
                                            </div>
                                            
                                            <!-- Courier Success Rate Card -->
                                            <div class="courier-success-card loading" id="courierSuccessCard" data-phone="{{ $order->phone }}">
                                                <div class="loading-spinner"></div>
                                                <div class="success-label">Loading...</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $order->email }}</td>
                                <th>Shipping Method</th>
                                <td>{{ $order->shipping_method }}</td>
                            </tr>
                            <tr>
                                <th>Payment Method</th>
                                <td colspan="3">{{ $order->payment_method }}</td>
                            </tr>
                            @if ($order->payment_method == 'Bkash' || $order->payment_method == 'Nagad' || $order->payment_method == 'Rocket')
                                <tr>
                                    <th>Mobile Number</th>
                                    <td>{{ $order->mobile_number }}</td>
                                    <th>Transaction ID</th>
                                    <td>{{ $order->transaction_id }}</td>
                                </tr>
                            @elseif ($order->payment_method == 'Bank')
                                <tr>
                                    <th>Bank Name</th>
                                    <td>{{ $order->bank_name }}</td>
                                    <th>Account Number</th>
                                    <td>{{ $order->account_number }}</td>
                                </tr>
                                <tr>
                                    <th>Holder Name</th>
                                    <td>{{ $order->holder_name }}</td>
                                    <th>Branch Name</th>
                                    <td>{{ $order->branch_name }}</td>
                                </tr>
                                <tr>
                                    <th>Routing Number</th>
                                    <td colspan="3">{{ $order->routing_number }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Coupon Code</th>
                                <td>{{ $order->coupon_code }}</td>
                                <th>Subtotal</th>
                                <td>{{ $order->subtotal }} <strong>{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <th>Shipping Charge</th>
                                <td>{{ $order->shipping_charge }}
                                    <strong>{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</strong></td>
                                <th>Discount</th>
                                <td>{{ $order->discount }} <strong>{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <th>Payment Status</th>
                                <td>{{ $order->pay_staus == 1 ? 'Paid' : 'Unpaid' }} </td>
                                <th>Payment Date</th>
                                <td>{{ $order->pay_date }} </td>
                            </tr>
                            <tr>
                                <th>Partial Payment</th>
                                <td>
                                    @php
                                        $part = App\Models\PartialPayment::where('order_id', $order->id)
                                            ->where('status', 1)
                                            ->sum('amount');
                                        $ds = $order->total;
                                    @endphp
                                    {{ $part }}<strong>{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</strong>
                                </td>
                                <th>Due</th>
                                <td> {{ $order->total - $part }} <strong>{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <td>{{ $ds }} <strong>{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</strong></td>
                                <th>Status</th>
                                <td>

                                    @if ($order->status == 0)
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif ($order->status == 1)
                                        <span class="badge badge-primary">Processing</span>
                                    @elseif ($order->status == 2)
                                        <span class="badge badge-danger">Canceled</span>
                                    @elseif ($order->status == 5)
                                        <span class="badge badge-danger">Refund</span>
                                    @elseif ($order->status == 4)
                                        <span class="badge" style="background: #7db1b1;">Shipping</span>
                                    @elseif ($order->status == 6)
                                        <span class="badge" style="background: #7db1b1;">Return Request By User</span>
                                    @elseif ($order->status == 7)
                                        <span class="badge" style="background: #7db1b1;">Return process accept by
                                            Owner</span>
                                    @elseif ($order->status == 8)
                                        <span class="badge" style="background: #7db1b1;">Returned</span>
                                    @elseif ($order->status == 9)
                                        <span class="badge" style="background: #7db1b1;">Sended to Courier</span>
                                    @elseif ($order->status == 3)
                                        <span class="badge badge-success">Delivered</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($order->status == 5)
                                <tr>
                                    <th>Refund Method</th>
                                    <td>{{ $order->refund_method }}</td>
                                </tr>
                            @endif
                            @if (!empty($order->admin_notes))
                                <tr>
                                    <th>Admin Notes</th>
                                    <td colspan="3">
                                        <div class="alert alert-info">
                                            <i class="fas fa-sticky-note"></i>
                                            {{ $order->admin_notes }}
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Edit Order Modal -->
            <div class="modal fade" id="editOrderModal" tabindex="-1" role="dialog" aria-labelledby="editOrderModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editOrderModalLabel">
                                <i class="fas fa-edit text-warning"></i> Edit Order: {{ $order->invoice }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('admin.order.update', $order->id) }}" method="POST" id="editOrderForm">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <!-- Customer Information -->
                                    <div class="col-md-6">
                                        <h6 class="text-primary"><i class="fas fa-user"></i> Customer Information</h6>
                                        <hr>
                                        
                                        <div class="form-group">
                                            <label for="first_name">First Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                                   value="{{ old('first_name', $order->first_name) }}" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                                   value="{{ old('last_name', $order->last_name) }}">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="{{ old('email', $order->email) }}">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="phone">Phone <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="phone" name="phone" 
                                                   value="{{ old('phone', $order->phone) }}" required>
                                        </div>
                                    </div>
                                    
                                    <!-- Address Information -->
                                    <div class="col-md-6">
                                        <h6 class="text-success"><i class="fas fa-map-marker-alt"></i> Address Information</h6>
                                        <hr>
                                        
                                        <div class="form-group">
                                            <label for="address">Address <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address', $order->address) }}</textarea>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="town">Town/City</label>
                                                    <input type="text" class="form-control" id="town" name="town" 
                                                           value="{{ old('town', $order->town) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="district">District</label>
                                                    <input type="text" class="form-control" id="district" name="district" 
                                                           value="{{ old('district', $order->district) }}">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="post_code">Post Code</label>
                                            <input type="text" class="form-control" id="post_code" name="post_code" 
                                                   value="{{ old('post_code', $order->post_code) }}">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <!-- Order Details -->
                                    <div class="col-md-8">
                                        <h6 class="text-info"><i class="fas fa-receipt"></i> Order Details</h6>
                                        <hr>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipping_charge">Shipping Charge</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="shipping_charge" 
                                                               name="shipping_charge" step="0.01" min="0" 
                                                               value="{{ old('shipping_charge', $order->shipping_charge) }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="discount">Discount</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="discount" 
                                                               name="discount" step="0.01" min="0" 
                                                               value="{{ old('discount', $order->discount) }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="coupon_code">Coupon Code</label>
                                            <input type="text" class="form-control" id="coupon_code" name="coupon_code" 
                                                   value="{{ old('coupon_code', $order->coupon_code) }}">
                                        </div>
                                        
                                        <!-- Order Summary Display -->
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="card-title">Order Summary</h6>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <td>Subtotal:</td>
                                                        <td class="text-right">{{ $order->subtotal }} {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Shipping:</td>
                                                        <td class="text-right" id="summary_shipping">{{ $order->shipping_charge }} {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Discount:</td>
                                                        <td class="text-right text-danger" id="summary_discount">-{{ $order->discount }} {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</td>
                                                    </tr>
                                                    <tr class="font-weight-bold">
                                                        <td>Total:</td>
                                                        <td class="text-right text-success" id="summary_total">{{ $order->total }} {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Admin Notes -->
                                    <div class="col-md-4">
                                        <h6 class="text-secondary"><i class="fas fa-sticky-note"></i> Admin Notes</h6>
                                        <hr>
                                        
                                        <div class="form-group">
                                            <label for="admin_notes">Internal Notes</label>
                                            <textarea class="form-control" id="admin_notes" name="admin_notes" 
                                                      rows="8" placeholder="Add internal notes about this order...">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                                            <small class="form-text text-muted">
                                                These notes are only visible to admin users and won't be shared with customers.
                                            </small>
                                        </div>
                                        
                                        <!-- Change Log Preview -->
                                        <div class="alert alert-light">
                                            <small>
                                                <strong>Last Updated:</strong><br>
                                                {{ $order->updated_at->format('d M Y, h:i A') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-warning" id="updateOrderBtn">
                                    <i class="fas fa-save"></i> Update Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Enhanced Phone History Modal with Courier Integration -->
            <div class="modal fade" id="phoneHistoryModal" tabindex="-1" role="dialog" aria-labelledby="phoneHistoryModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="phoneHistoryModalLabel">
                                <i class="fas fa-history text-primary"></i> Customer History for <span id="historyPhone" class="text-info"></span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Loading State -->
                            <div id="historyLoading" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading customer history...</p>
                            </div>
                            
                            <!-- Risk Assessment Alert -->
                            <div id="riskAssessment" style="display: none;" class="mb-3">
                                <div class="alert risk-indicator" id="riskAlert">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="mb-1"><i class="fas fa-shield-alt"></i> Delivery Risk Assessment</h6>
                                            <span id="riskRecommendation"></span>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <span class="badge badge-lg" id="riskBadge"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Courier History Section -->
                            <div id="courierHistorySection" style="display: none;">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 text-info">
                                            <i class="fas fa-shipping-fast"></i> Courier Delivery History
                                            <small class="text-muted">(Data from BD Courier)</small>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Overall Stats -->
                                        <div class="courier-stats">
                                            <div class="row text-center">
                                                <div class="col-md-3">
                                                    <div class="mb-2">
                                                        <div class="h4 mb-0 text-primary" id="courierTotalOrders">0</div>
                                                        <small class="text-muted"><i class="fas fa-box"></i> Total Orders</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-2">
                                                        <div class="h4 mb-0 text-success" id="courierSuccessful">0</div>
                                                        <small class="text-muted"><i class="fas fa-check-circle"></i> Delivered</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-2">
                                                        <div class="h4 mb-0 text-danger" id="courierCancelled">0</div>
                                                        <small class="text-muted"><i class="fas fa-times-circle"></i> Cancelled</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-2">
                                                        <div class="h4 mb-0" id="courierSuccessRate">0%</div>
                                                        <small class="text-muted"><i class="fas fa-percentage"></i> Success Rate</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Courier Details Table -->
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered table-hover">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th><i class="fas fa-truck"></i> Courier Service</th>
                                                        <th class="text-center"><i class="fas fa-list-ol"></i> Total</th>
                                                        <th class="text-center"><i class="fas fa-check"></i> Successful</th>
                                                        <th class="text-center"><i class="fas fa-ban"></i> Cancelled</th>
                                                        <th class="text-center"><i class="fas fa-chart-line"></i> Success Rate</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="courierDetailsTable">
                                                    <!-- Courier details will be populated here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- No Courier Data -->
                            <div id="noCourierData" style="display: none;" class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>No courier data available:</strong> Unable to fetch delivery history from courier services. This might be a new customer or the courier API is temporarily unavailable.
                            </div>
                            
                            <!-- Customer Summary (Our Store Orders) -->
                            <div id="customerSummary" style="display: none;">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 text-success">
                                            <i class="fas fa-store"></i> Order History (Our Store)
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <small class="text-muted"><i class="fas fa-user"></i> Customer Name:</small><br>
                                                        <strong id="customerName">-</strong>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted"><i class="fas fa-calendar-alt"></i> Customer Since:</small><br>
                                                        <strong id="customerSince">-</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <small class="text-muted"><i class="fas fa-shopping-cart"></i> Total Orders:</small><br>
                                                        <strong id="totalOrders">0</strong>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted"><i class="fas fa-money-bill-wave"></i> Total Spent:</small><br>
                                                        <strong id="totalSpent" class="text-success">৳0</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="h6 mb-0 text-success" id="completedOrders">0</div>
                                                    <small class="text-muted">Completed</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="h6 mb-0 text-warning" id="pendingOrders">0</div>
                                                    <small class="text-muted">Pending</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="h6 mb-0 text-danger" id="cancelledOrders">0</div>
                                                    <small class="text-muted">Cancelled</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="h6 mb-0 text-info" id="avgOrderValue">৳0</div>
                                                    <small class="text-muted">Avg Order Value</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Orders Table -->
                            <div id="ordersContainer" style="display: none;">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-list"></i> Recent Orders</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered table-hover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Invoice</th>
                                                        <th>Date</th>
                                                        <th>Customer</th>
                                                        <th>Total</th>
                                                        <th>Payment</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="historyOrdersList">
                                                    <!-- Orders will be populated here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- No Orders Found -->
                            <div id="noOrders" style="display: none;" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No orders found</h5>
                                <p class="text-muted">No orders found for this phone number in our store.</p>
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

            <!-- SMS Modal -->
            <div class="modal fade" id="smsModal" tabindex="-1" role="dialog"
                aria-labelledby="smsModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="smsModalTitle">
                                <i class="fas fa-sms text-purple"></i>
                                Send Custom SMS to {{ $order->first_name }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="post" action="{{ route('admin.order.send.sms') }}">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                
                                <div class="form-group">
                                    <label for="customer_info"><i class="fas fa-info-circle text-info"></i> Customer Info:</label>
                                    <div class="alert alert-info">
                                        <strong>Name:</strong> {{ $order->first_name }}<br>
                                        <strong>Phone:</strong> {{ $order->phone }}<br>
                                        <strong>Invoice:</strong> {{ $order->invoice }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="sms_templates"><i class="fas fa-list text-success"></i> Quick Templates:</label>
                                    <select class="form-control" id="sms_templates" onchange="loadTemplate()">
                                        <option value="">Select a template...</option>
                                        <option value="Your order {invoice} is being prepared. We will notify you once it's ready for delivery. Thank you!">Order Preparation</option>
                                        <option value="Hi {customer_name}, your order {invoice} is out for delivery and will reach you soon. Please keep your phone available.">Out for Delivery</option>
                                        <option value="Your order {invoice} has been delivered successfully. Thank you for shopping with us!">Delivery Confirmation</option>
                                        <option value="We apologize for the delay in your order {invoice}. We are working to process it as soon as possible.">Delay Notice</option>
                                        <option value="Your payment of {total} TK for order {invoice} has been confirmed. Thank you!">Payment Confirmation</option>
                                        <option value="Hi {customer_name}, please confirm your delivery address for order {invoice}. Contact us if you need to make changes.">Address Confirmation</option>
                                        <option value="Your order {invoice} is ready for pickup. Please visit our store with a valid ID. Thank you!">Pickup Ready</option>
                                        <option value="Thank you {customer_name} for your order {invoice}! Your order total is {total} TK. We appreciate your business.">Thank You Message</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="message"><i class="fas fa-edit text-primary"></i> SMS Message: <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="message" id="message" rows="4" 
                                              placeholder="Type your custom message here..." 
                                              maxlength="500" required 
                                              oninput="updateCharCount()"></textarea>
                                    <small class="form-text text-muted">
                                        <span id="charCount">0</span>/500 characters
                                        <br>
                                        <strong>Available variables:</strong> {invoice}, {customer_name}, {total}
                                    </small>
                                </div>

                                <div class="form-group">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Preview:</strong> SMS will be sent to <strong>{{ $order->phone }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-purple" id="sendSmsBtn">
                                    <i class="fas fa-paper-plane"></i> Send SMS
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="refund" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">

                        <div class="modal-body">
                            <form method="post" action="{{ route('admin.refund') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" name="order" value="{{ $order->id }}">
                                    <input class="form-control" type="text" placeholder="amount" name="amount">
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="method">
                                        <option value="wallate">Wallate</option>
                                        <option value="Bank">Bank</option>
                                        <option value="Bkash">Bkash</option>
                                        <option value="Nagad">Nagad</option>
                                        <option value="Rocket">Rocket</option>
                                        <option value="Cash">Cash</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Refund</button>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="refund2" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">

                        <div class="modal-body">
                            <form method="post" action="{{ route('admin.refund_2') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" name="order" value="{{ $order->id }}">
                                    <input class="form-control" type="text" placeholder="amount" name="amount">
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="method">
                                        <option value="wallate">Wallate</option>
                                        <option value="Bank">Bank</option>
                                        <option value="Bkash">Bkash</option>
                                        <option value="Nagad">Nagad</option>
                                        <option value="Rocket">Rocket</option>
                                        <option value="Cash">Cash</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Refund</button>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Order Products</h2>
                </div>
                <div class="card-body">
                    @php
                        $vendors = DB::table('multi_order')
                            ->where('order_id', $order->id)
                            ->get();
                    @endphp
                    @foreach ($vendors as $key => $vendor)
                        @php($us = App\Models\User::find($vendor->vendor_id))
                        <div class="gx" style="">
                            <div>
                                Seller:{{ $us->name }}
                            </div>
                            <div>
                                Total:{{ $vendor->total }}
                            </div>
                            <div>
                                Payment:{{ $vendor->partial_pay }}
                            </div>
                            <div>
                                Discount:{{ $vendor->discount }}
                            </div>
                            <div>
                                Status:
                                @if ($vendor->status == 0)
                                    <span class="badge badge-warning">Pending</span>
                                @elseif ($vendor->status == 1)
                                    <span class="badge badge-primary">Processing</span>
                                @elseif ($vendor->status == 2)
                                    <span class="badge badge-danger">Canceled</span>
                                @elseif ($vendor->status == 4)
                                    <span class="badge" style="background: #7db1b1;">Shipping</span>
                                @elseif ($vendor->status == 5)
                                    <span class="badge badge-danger">Refund</span>
                                @elseif ($order->status == 6)
                                    <span class="badge" style="background: #7db1b1;">Return Request By User</span>
                                @elseif ($order->status == 7)
                                    <span class="badge" style="background: #7db1b1;">Return process accept by
                                        Owner</span>
                                @elseif ($order->status == 8)
                                    <span class="badge" style="background: #7db1b1;">Returned</span>
                                @elseif ($order->status == 3)
                                    <span class="badge badge-success">Delivered</span>
                                @endif
                            </div>

                        </div>
                        <div>
                            <a
                                href="{{ route('admin.order.subStatus', ['id' => $order->id, 'status' => 1, 'vendor' => $vendor->vendor_id]) }}">Processing</a>
                            <a
                                href="{{ route('admin.order.subStatus', ['id' => $order->id, 'status' => 4, 'vendor' => $vendor->vendor_id]) }}">Shipping</a>
                            <a
                                href="{{ route('admin.order.subStatus', ['id' => $order->id, 'status' => 2, 'vendor' => $vendor->vendor_id]) }}">Canceled</a>
                            <a
                                href="{{ route('admin.order.subStatus', ['id' => $order->id, 'status' => 3, 'vendor' => $vendor->vendor_id]) }}">Delivered</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Product</th>
                                        <th>Title</th>
                                        <th>Size</th>
                                        <th>Color</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($order->orderDetails as $key => $item)
                                        @if (isset($item->product->user_id) && $item->product->user_id == $vendor->vendor_id)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <img src="{{ asset('uploads/product/' . $item->product->image) }}"
                                                        alt="Product Image" width="80px" height="80px">
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.product.show', $item->product->id) }}"
                                                        target="_blank">{{ $item->title }}</a>
                                                </td>
                                                <td><?php
                                                $data = json_decode($item->size);
                                                if ($data != null && $data != '""' && $data != '[]' && $data != '"\"\""') {
                                                    foreach ($data as $key => $attr) {
                                                        $value = DB::table('attribute_values')->where('id', $attr)->first();
                                                        $name = DB::table('attributes')->where('slug', $key)->first();
                                                        if ($name) {
                                                            echo $vl = $name->name . ':' . $value->name . ', ';
                                                        }
                                                    }
                                                }
                                                ?></td>
                                                <td> <?php
                                                if ($item->color != 'blank') {
                                                    echo $item->color;
                                                }
                                                ?></td>
                                                <td>{{ $item->qty }}</td>
                                                <td>{{ $item->price }}</td>
                                                <td>{{ $item->total_price }}</td>
                                            </tr>
                                        @endif
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>

            </div>

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

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

    // Edit Order Form Submission with improved feedback
    $('#editOrderForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#updateOrderBtn');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        
        // Clear any existing notifications in the modal
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
                // Show success notification
                showNotification('success', 'Order updated successfully!');
                
                // Close modal
                $('#editOrderModal').modal('hide');
                
                // Reload page to show updated data
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            },
            error: function(xhr) {
                let errorMessage = 'Failed to update order. Please try again.';
                
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    if (errors) {
                        const errorList = Object.values(errors).flat();
                        errorMessage = 'Validation Error: ' + errorList.join(', ');
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                // Show error in modal
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
                
                // Scroll to top of modal to show error
                $('.modal-body').scrollTop(0);
            },
            complete: function() {
                // Restore button state
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});

// Load courier success rate data
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

// Update courier success rate card
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
        
        // Add tooltip
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
    
    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        $('#notificationContainer .alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
}

function loadTemplate() {
    const template = document.getElementById('sms_templates').value;
    const messageField = document.getElementById('message');
    
    if (template) {
        messageField.value = template;
        updateCharCount();
    }
}

function updateCharCount() {
    const message = document.getElementById('message').value;
    const charCount = document.getElementById('charCount');
    const sendBtn = document.getElementById('sendSmsBtn');
    
    charCount.textContent = message.length;
    
    if (message.length > 500) {
        charCount.style.color = 'red';
        sendBtn.disabled = true;
    } else {
        charCount.style.color = 'green';
        sendBtn.disabled = false;
    }
}

// Enhanced showPhoneHistory function with courier integration
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
            
            // Show order history (existing functionality)
            if (response.success && response.orders && response.orders.length > 0) {
                document.getElementById('customerSummary').style.display = 'block';
                document.getElementById('ordersContainer').style.display = 'block';
                
                // Populate existing order data
                const summary = response.summary;
                document.getElementById('customerName').textContent = summary.primary_name || 'N/A';
                document.getElementById('customerSince').textContent = summary.customer_since || 'N/A';
                document.getElementById('totalOrders').textContent = summary.total_orders;
                document.getElementById('totalSpent').textContent = '৳' + summary.total_spent.toLocaleString();
                document.getElementById('completedOrders').textContent = summary.completed_orders;
                document.getElementById('pendingOrders').textContent = summary.pending_orders;
                document.getElementById('cancelledOrders').textContent = summary.cancelled_orders;
                document.getElementById('avgOrderValue').textContent = '৳' + summary.avg_order_value.toLocaleString();
                
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
                            <td><strong class="text-success">৳${parseFloat(order.total).toLocaleString()}</strong></td>
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

function showRiskAssessment(riskData) {
    const riskSection = document.getElementById('riskAssessment');
    const riskAlert = document.getElementById('riskAlert');
    const riskRecommendation = document.getElementById('riskRecommendation');
    const riskBadge = document.getElementById('riskBadge');
    
    // Remove existing risk classes
    riskAlert.classList.remove('risk-high', 'risk-medium', 'risk-low', 'risk-new');
    
    // Set alert class based on risk level
    riskAlert.className = `alert alert-${riskData.color} risk-indicator risk-${riskData.risk_level}`;
    riskRecommendation.textContent = riskData.recommendation;
    riskBadge.className = `badge badge-lg badge-${riskData.color}`;
    riskBadge.textContent = riskData.risk_level.toUpperCase() + ' RISK';
    
    riskSection.style.display = 'block';
}

function showCourierHistory(courierData) {
    if (courierData.success && courierData.data) {
        const data = courierData.data;
        
        // Show courier section
        document.getElementById('courierHistorySection').style.display = 'block';
        
        // Update overall stats
        document.getElementById('courierTotalOrders').textContent = data.total_orders;
        document.getElementById('courierSuccessful').textContent = data.total_successful;
        document.getElementById('courierCancelled').textContent = data.total_cancelled;
        document.getElementById('courierSuccessRate').textContent = data.success_rate + '%';
        
        // Update success rate color
        const successRateElement = document.getElementById('courierSuccessRate');
        successRateElement.classList.remove('success-rate-high', 'success-rate-medium', 'success-rate-low');
        
        if (data.success_rate >= 85) {
            successRateElement.classList.add('success-rate-high');
        } else if (data.success_rate >= 70) {
            successRateElement.classList.add('success-rate-medium');
        } else {
            successRateElement.classList.add('success-rate-low');
        }
        
        // Populate courier details table
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

// Edit Order Modal Functions
$(document).ready(function() {
    const subtotal = {{ $order->subtotal }};
    const currency = '{{ setting("CURRENCY_CODE_MIN") ?? "TK" }}';
    
    // Update order summary when shipping or discount changes
    $('#shipping_charge, #discount').on('input', function() {
        const shipping = parseFloat($('#shipping_charge').val()) || 0;
        const discount = parseFloat($('#discount').val()) || 0;
        const total = Math.max(0, subtotal + shipping - discount);
        
        $('#summary_shipping').text(shipping.toFixed(2) + ' ' + currency);
        $('#summary_discount').text('-' + discount.toFixed(2) + ' ' + currency);
        $('#summary_total').text(total.toFixed(2) + ' ' + currency);
    });
});

// Initialize character count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCharCount();
});
</script>
@endpush