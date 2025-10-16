<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th style="width: 40px;">
                    <input type="checkbox" id="select-all">
                </th>
                <th>SL</th>
                <th>Invoice</th>
                <th>Customer</th>
                <th class="d-none d-md-table-cell">Address</th>
                <th>Phone</th>
                <th class="d-none d-lg-table-cell">Payment</th>
                <th>Total</th>
                <th class="d-none d-md-table-cell">Date</th>
                <th>Status</th>
                <th style="width: 120px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $key => $data)
                <tr>
                    <td>
                        <input type="checkbox" class="order-checkbox" value="{{ $data->id }}">
                    </td>
                    <td>{{ $orders->firstItem() + $key }}</td>
                    <td>
                        <strong class="text-primary">{{ $data->invoice }}</strong>
                        @if($data->is_pre == 1)
                            <br><small class="badge badge-info">Pre-Order</small>
                        @endif
                    </td>
                    <td>
                        <div>
                            <strong>{{ $data->first_name }} {{ $data->last_name }}</strong>
                            @if($data->email)
                                <br><small class="text-muted d-block d-md-none">{{ Str::limit($data->email, 25) }}</small>
                                <br><small class="text-muted d-none d-md-block">{{ $data->email }}</small>
                            @endif
                            <!-- Mobile: Show address here -->
                            <div class="d-block d-md-none mt-1">
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i> {{ Str::limit($data->address, 30) }}
                                </small>
                            </div>
                        </div>
                    </td>
                    <td class="d-none d-md-table-cell">
                        <span title="{{ $data->address }}" data-toggle="tooltip">
                            {{ Str::limit($data->address, 30) }}
                        </span>
                        @if($data->town || $data->district)
                            <br><small class="text-muted">
                                {{ $data->town ? $data->town . ', ' : '' }}{{ $data->district }}
                            </small>
                        @endif
                    </td>
                    <td>
                        @if($data->phone)
                            <div class="d-flex align-items-center flex-wrap">
                                <a href="tel:{{ $data->phone }}" class="text-decoration-none text-success mb-1">
                                    <i class="fas fa-phone"></i> 
                                    <span class="d-none d-sm-inline">{{ $data->phone }}</span>
                                    <span class="d-inline d-sm-none">{{ Str::limit($data->phone, 12) }}</span>
                                </a>
                                <!-- Order Count Badge -->
                                @php
                                    // Get order count more efficiently
                                    static $phoneOrderCounts = [];
                                    $phoneKey = $data->phone;
                                    
                                    if (!isset($phoneOrderCounts[$phoneKey])) {
                                        $cleanPhone = preg_replace('/[^0-9+]/', '', $data->phone);
                                        $phoneOrderCounts[$phoneKey] = \App\Models\Order::where(function($query) use ($data, $cleanPhone) {
                                            $query->where('phone', $data->phone)->orWhere('phone', $cleanPhone);
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
                                    }
                                    $phoneOrderCount = $phoneOrderCounts[$phoneKey];
                                @endphp
                                @if($phoneOrderCount > 1)
                                    <span class="badge badge-primary ml-1 cursor-pointer" 
                                          title="{{ $phoneOrderCount }} orders • Click to view history"
                                          onclick="showPhoneHistory('{{ $data->phone }}')"
                                          data-toggle="tooltip"
                                          style="cursor: pointer; font-size: 0.75rem;">
                                        {{ $phoneOrderCount }}
                                    </span>
                                @endif
                                <!-- Courier Success Rate Indicator -->
                                <span class="courier-success-indicator loading" 
                                      data-phone="{{ $data->phone }}"
                                      title="Loading courier success rate...">
                                    <span class="loading-dot"></span><span class="loading-dot"></span><span class="loading-dot"></span>
                                </span>
                                <!-- Mobile: Show payment method here -->
                                <div class="d-block d-lg-none w-100 mt-1">
                                    <span class="badge badge-info badge-sm">{{ ucfirst($data->payment_method) }}</span>
                                    @if(isset($data->pay_staus) && $data->pay_staus == 1)
                                        <span class="badge badge-success badge-sm ml-1">Paid</span>
                                    @else
                                        <span class="badge badge-warning badge-sm ml-1">Unpaid</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td class="d-none d-lg-table-cell">
                        <span class="badge badge-info">{{ ucfirst($data->payment_method) }}</span>
                        @if(isset($data->pay_staus) && $data->pay_staus == 1)
                            <br><small class="badge badge-success">Paid</small>
                        @else
                            <br><small class="badge badge-warning">Unpaid</small>
                        @endif
                    </td>
                    <td>
                        <strong class="text-success">৳{{ number_format($data->total, 2) }}</strong>
                        @if($data->discount > 0)
                            <br><small class="text-muted">-৳{{ number_format($data->discount, 2) }}</small>
                        @endif
                        <!-- Mobile: Show date here -->
                        <div class="d-block d-md-none mt-1">
                            <small class="text-muted">{{ date('d M Y', strtotime($data->created_at)) }}</small>
                        </div>
                    </td>
                    <td class="d-none d-md-table-cell">
                        <small>{{ date('d M Y', strtotime($data->created_at)) }}</small>
                        <br><small class="text-muted">{{ date('h:i A', strtotime($data->created_at)) }}</small>
                    </td>
                    <td>
                        @if ($data->status == 0)
                            <span class="badge badge-warning">Pending</span>
                        @elseif ($data->status == 1)
                            <span class="badge badge-primary">Processing</span>
                        @elseif ($data->status == 2)
                            <span class="badge badge-danger">Canceled</span>
                        @elseif ($data->status == 5)
                            <span class="badge badge-secondary">Refund</span>
                        @elseif ($data->status == 4)
                            <span class="badge" style="background: #17a2b8; color: white;">Shipping</span>
                        @elseif ($data->status == 6)
                            <span class="badge badge-warning" style="font-size: 0.7rem;">
                                Return<br>Requested
                            </span>
                        @elseif ($data->status == 7)
                            <span class="badge badge-info" style="font-size: 0.7rem;">
                                Return<br>Accepted
                            </span>
                        @elseif ($data->status == 8)
                            <span class="badge badge-dark">Returned</span>
                        @elseif ($data->status == 9)
                            <span class="badge badge-success" style="font-size: 0.7rem;">
                                Sent to<br>Courier
                            </span>
                        @elseif ($data->status == 3)
                            <span class="badge badge-success">Delivered</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group-vertical btn-group-sm d-block d-md-none" role="group">
                            <!-- Mobile View: Simplified buttons -->
                            <a href="{{ route('admin.order.show', $data->id) }}" 
                               class="btn btn-info btn-sm mb-1">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                        type="button" data-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <!-- Mobile dropdown content -->
                                    <a class="dropdown-item" href="{{ route('admin.order.show', $data->id) }}">
                                        <i class="fas fa-edit text-warning"></i> Edit Order
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('admin.order.invoice', $data->id) }}" target="_blank">
                                        <i class="fas fa-print"></i> Print Invoice
                                    </a>
                                    @if($data->phone && $phoneOrderCount > 1)
                                        <a class="dropdown-item" href="#" onclick="showPhoneHistory('{{ $data->phone }}')">
                                            <i class="fas fa-history"></i> Order History ({{ $phoneOrderCount }})
                                        </a>
                                    @endif
                                    @if($data->phone)
                                        <a class="dropdown-item" href="#" onclick="openSmsModal({{ $data->id }}, '{{ $data->invoice }}', '{{ $data->phone }}', '{{ $data->first_name }}', '{{ $data->total }}')">
                                            <i class="fas fa-sms"></i> Send SMS
                                        </a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    @if($data->status == 0)
                                        <a class="dropdown-item" href="{{ route('admin.order.status.processing', $data->id) }}">
                                            <i class="fas fa-cog"></i> Start Processing
                                        </a>
                                    @elseif($data->status == 1)
                                        <a class="dropdown-item" href="{{ route('admin.order.status.shipping', $data->id) }}">
                                            <i class="fas fa-shipping-fast"></i> Mark as Shipped
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Desktop View: Full button group -->
                        <div class="btn-group d-none d-md-flex" role="group">
                            <a title="Print Invoice" 
                               href="{{ route('admin.order.invoice', $data->id) }}" 
                               class="btn btn-warning btn-sm" 
                               target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                            
                            <a title="View Details" 
                               href="{{ route('admin.order.show', $data->id) }}" 
                               class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if (setting('STEEDFAST_STATUS') == 1 && $data->status != 9)
                                <form action="{{ route('admin.setting.courier.sendsteedfast') }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    <input type="hidden" name="invoice" value="{{ $data->invoice }}">
                                    <input type="hidden" name="recipient_name" value="{{ $data->first_name }}">
                                    <input type="hidden" name="recipient_phone" value="{{ $data->phone }}">
                                    <input type="hidden" name="recipient_address" 
                                           value="{{ $data->address . ', ' . ($data->town ?? '') . ', ' . ($data->district ?? '') . ', ' . ($data->post_code ?? '') }}">
                                    @if (isset($data->pay_staus) && $data->pay_staus == 1)
                                        <input type="hidden" name="cod_amount" value="0.00">
                                    @else
                                        <input type="hidden" name="cod_amount" value="{{ $data->total }}">
                                    @endif
                                    <input type="hidden" name="note" value="Order from {{ config('app.name') }}">
                                    <button type="submit" 
                                            title="Send to Courier" 
                                            class="btn btn-success btn-sm"
                                            onclick="return confirm('Send this order to courier?')">
                                        <i class="fas fa-truck"></i>
                                    </button>
                                </form>
                            @elseif (setting('STEEDFAST_STATUS') == 1)
                                <button type="button" 
                                        title="Already Sent to Courier" 
                                        class="btn btn-secondary btn-sm" 
                                        disabled>
                                    <i class="fas fa-truck"></i>
                                </button>
                            @endif
                            
                            <div class="btn-group">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                        data-toggle="dropdown"
                                        title="Quick Actions"
                                        aria-expanded="false">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <h6 class="dropdown-header">
                                        <i class="fas fa-cog"></i> Quick Actions
                                    </h6>
                                    
                                    <!-- Edit Order - New Feature -->
                                    <a class="dropdown-item" 
                                       href="{{ route('admin.order.show', $data->id) }}#editOrderModal"
                                       onclick="setTimeout(function(){ $('#editOrderModal').modal('show'); }, 100);">
                                        <i class="fas fa-edit text-warning"></i> Edit Order Details
                                    </a>
                                    
                                    <div class="dropdown-divider"></div>
                                    
                                    @if($data->status != 0)
                                        <a class="dropdown-item" 
                                           href="{{ route('admin.order.status.pending', $data->id) }}"
                                           onclick="return confirm('Change status to Pending?')">
                                            <i class="fas fa-clock text-warning"></i> Set to Pending
                                        </a>
                                        <div class="dropdown-divider"></div>
                                    @endif
                                    
                                    @if($data->status == 0)
                                        <a class="dropdown-item" 
                                           href="{{ route('admin.order.status.processing', $data->id) }}"
                                           onclick="return confirm('Start processing this order?')">
                                            <i class="fas fa-cog text-primary"></i> Start Processing
                                        </a>
                                    @endif
                                    
                                    @if($data->status == 1)
                                        <a class="dropdown-item" 
                                           href="{{ route('admin.order.status.shipping', $data->id) }}"
                                           onclick="return confirm('Mark as shipped?')">
                                            <i class="fas fa-shipping-fast text-info"></i> Mark as Shipped
                                        </a>
                                    @endif
                                    
                                    @if(in_array($data->status, [0, 1, 4]))
                                        <a class="dropdown-item" 
                                           href="{{ route('admin.order.status.delivered', $data->id) }}"
                                           onclick="return confirm('Mark as delivered?')">
                                            <i class="fas fa-check text-success"></i> Mark as Delivered
                                        </a>
                                    @endif
                                    
                                    @if(in_array($data->status, [0, 1]))
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" 
                                           href="{{ route('admin.order.status.cancel', $data->id) }}"
                                           onclick="return confirm('Are you sure you want to cancel this order?')">
                                            <i class="fas fa-times"></i> Cancel Order
                                        </a>
                                    @endif
                                    
                                    @if($data->status == 6)
                                        <div class="dropdown-divider"></div>
                                        <h6 class="dropdown-header text-warning">Return Actions</h6>
                                        <a class="dropdown-item text-warning" 
                                           href="{{ route('admin.order.status.return-accept', $data->id) }}"
                                           onclick="return confirm('Accept this return request?')">
                                            <i class="fas fa-undo"></i> Accept Return
                                        </a>
                                    @endif
                                    
                                    @if($data->status == 7)
                                        <a class="dropdown-item text-dark" 
                                           href="{{ route('admin.order.status.return-complete', $data->id) }}"
                                           onclick="return confirm('Complete the return process?')">
                                            <i class="fas fa-check-double"></i> Complete Return
                                        </a>
                                    @endif
                                    
                                    <div class="dropdown-divider"></div>
                                    
                                    @if($data->phone && $phoneOrderCount > 1)
                                        <a class="dropdown-item" 
                                           href="#" 
                                           onclick="showPhoneHistory('{{ $data->phone }}')">
                                            <i class="fas fa-history text-purple"></i> Order History ({{ $phoneOrderCount }})
                                        </a>
                                    @endif
                                    
                                    @if($data->phone)
                                        <a class="dropdown-item" 
                                           href="#" 
                                           onclick="openSmsModal({{ $data->id }}, '{{ $data->invoice }}', '{{ $data->phone }}', '{{ $data->first_name }}', '{{ $data->total }}')"
                                           data-toggle="modal" 
                                           data-target="#smsModal">
                                            <i class="fas fa-sms text-info"></i> Send SMS
                                        </a>
                                    @endif
                                    
                                    @if(in_array($data->status, [2, 5, 8]))
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" 
                                           href="{{ route('admin.order.delete', $data->id) }}"
                                           onclick="return confirm('Are you sure you want to permanently delete this order?')">
                                            <i class="fas fa-trash"></i> Delete Order
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No orders found</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['search', 'status_filter', 'date_from', 'date_to']))
                                    No orders match your search criteria. Try adjusting your filters or 
                                    <a href="{{ route('admin.order.index') }}" class="text-primary">clear all filters</a> 
                                    to see all orders.
                                @else
                                    No orders have been placed yet. Orders will appear here once customers start placing orders.
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Phone History Modal -->
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
<div class="modal fade" id="smsModal" tabindex="-1" role="dialog" aria-labelledby="smsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="smsModalLabel">
                    <i class="fas fa-sms text-info"></i> Send Custom SMS
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.order.send.sms') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="sms_order_id">
                    
                    <!-- Customer Info Display -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-receipt text-primary"></i> Order Invoice:</label>
                                <input type="text" class="form-control-plaintext font-weight-bold" id="sms_invoice" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-user text-success"></i> Customer Name:</label>
                                <input type="text" class="form-control-plaintext font-weight-bold" id="sms_customer_name" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-phone text-info"></i> Phone Number:</label>
                                <input type="text" class="form-control-plaintext font-weight-bold text-info" id="sms_phone" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-money-bill text-warning"></i> Order Total:</label>
                                <input type="text" class="form-control-plaintext font-weight-bold text-success" id="sms_total" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Templates -->
                    <div class="form-group">
                        <label for="sms_templates"><i class="fas fa-list text-primary"></i> Quick Templates:</label>
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-primary btn-sm btn-block mb-2" onclick="setTemplate('order_confirm')">
                                    <i class="fas fa-check-circle"></i> Order Confirmation
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm btn-block mb-2" onclick="setTemplate('shipping')">
                                    <i class="fas fa-shipping-fast"></i> Shipping Notification
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm btn-block mb-2" onclick="setTemplate('delivered')">
                                    <i class="fas fa-check-double"></i> Delivery Confirmation
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm btn-block mb-2" onclick="setTemplate('delay')">
                                    <i class="fas fa-clock"></i> Delay Notice
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-block mb-2" onclick="setTemplate('payment')">
                                    <i class="fas fa-credit-card"></i> Payment Confirmation
                                </button>
                                <button type="button" class="btn btn-outline-dark btn-sm btn-block mb-2" onclick="setTemplate('address')">
                                    <i class="fas fa-map-marker-alt"></i> Address Confirmation
                                </button>
                                <button type="button" class="btn btn-outline-purple btn-sm btn-block mb-2" onclick="setTemplate('pickup')">
                                    <i class="fas fa-hand-paper"></i> Pickup Ready
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm btn-block mb-2" onclick="setTemplate('custom')">
                                    <i class="fas fa-edit"></i> Clear (Custom)
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="form-group">
                        <label for="sms_message"><i class="fas fa-edit text-primary"></i> SMS Message: <span class="text-danger">*</span></label>
                        <textarea class="form-control" 
                                  name="message" 
                                  id="sms_message" 
                                  rows="4" 
                                  placeholder="Type your custom message here..." 
                                  maxlength="500" 
                                  required 
                                  oninput="updateCharCount()"></textarea>
                        <div class="d-flex justify-content-between">
                            <small class="form-text text-muted">
                                <strong>Available variables:</strong> {invoice}, {customer_name}, {total}
                            </small>
                            <small class="form-text">
                                <span id="charCount" class="text-success">0</span><span class="text-muted">/500 characters</span>
                            </small>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="form-group">
                        <label><i class="fas fa-eye text-info"></i> Preview:</label>
                        <div class="alert alert-light border" id="sms_preview">
                            <em class="text-muted">Message preview will appear here...</em>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="sendSmsBtn">
                        <i class="fas fa-paper-plane"></i> Send SMS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-outline-purple {
    color: #6f42c1;
    border-color: #6f42c1;
}
.btn-outline-purple:hover {
    color: #fff;
    background-color: #6f42c1;
    border-color: #6f42c1;
}
.text-purple {
    color: #6f42c1 !important;
}
.btn-xs {
    padding: 0.125rem 0.25rem;
    font-size: 0.75rem;
    line-height: 1.3;
    border-radius: 0.15rem;
}

/* Risk Assessment Styles */
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
</style>

<script>
function openSmsModal(orderId, invoice, phone, customerName, total) {
    document.getElementById('sms_order_id').value = orderId;
    document.getElementById('sms_invoice').value = invoice;
    document.getElementById('sms_phone').value = phone;
    document.getElementById('sms_customer_name').value = customerName;
    document.getElementById('sms_total').value = '৳' + parseFloat(total).toLocaleString();
    document.getElementById('sms_message').value = '';
    document.getElementById('sms_preview').innerHTML = '<em class="text-muted">Message preview will appear here...</em>';
    updateCharCount();
}

function setTemplate(type) {
    const templates = {
        'order_confirm': 'Dear {customer_name}, your order {invoice} has been confirmed. Total amount: ৳{total}. Thank you for shopping with us!',
        'shipping': 'Good news! Your order {invoice} is now being shipped. You will receive it soon. Track your order on our website.',
        'delivered': 'Your order {invoice} has been delivered successfully. Thank you for choosing us! Please rate your experience.',
        'delay': 'We apologize for the delay in your order {invoice}. We are working to process it as soon as possible. Thank you for your patience.',
        'payment': 'Your payment of ৳{total} for order {invoice} has been confirmed. Thank you!',
        'address': 'Hi {customer_name}, please confirm your delivery address for order {invoice}. Contact us if you need to make changes.',
        'pickup': 'Your order {invoice} is ready for pickup. Please visit our store with a valid ID. Thank you!',
        'custom': ''
    };
    
    const messageField = document.getElementById('sms_message');
    messageField.value = templates[type];
    updateCharCount();
    updatePreview();
}

function updateCharCount() {
    const message = document.getElementById('sms_message').value;
    const charCount = document.getElementById('charCount');
    const sendBtn = document.getElementById('sendSmsBtn');
    
    charCount.textContent = message.length;
    
    if (message.length > 500) {
        charCount.className = 'text-danger';
        sendBtn.disabled = true;
    } else if (message.length > 400) {
        charCount.className = 'text-warning';
        sendBtn.disabled = false;
    } else {
        charCount.className = 'text-success';
        sendBtn.disabled = false;
    }
    
    updatePreview();
}

function updatePreview() {
    const message = document.getElementById('sms_message').value;
    const invoice = document.getElementById('sms_invoice').value;
    const customerName = document.getElementById('sms_customer_name').value;
    const total = document.getElementById('sms_total').value.replace('৳', '');
    
    let preview = message;
    preview = preview.replace(/{invoice}/g, invoice);
    preview = preview.replace(/{customer_name}/g, customerName);
    preview = preview.replace(/{total}/g, total);
    
    if (preview.trim() === '') {
        preview = '<em class="text-muted">Message preview will appear here...</em>';
    }
    
    document.getElementById('sms_preview').innerHTML = preview;
}

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

// Auto-update preview when typing
document.addEventListener('DOMContentLoaded', function() {
    const messageField = document.getElementById('sms_message');
    if (messageField) {
        messageField.addEventListener('input', function() {
            updateCharCount();
        });
    }
});
</script>