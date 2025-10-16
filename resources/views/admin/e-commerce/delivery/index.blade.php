@extends('layouts.admin.e-commerce.app')

@section('title', 'Delivery Tracking')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <style>
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .filters-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .tracking-code {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.9rem;
        }
        .consignment-id {
            font-family: 'Courier New', monospace;
            background: #e3f2fd;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.9rem;
            color: #1565c0;
        }
    </style>
@endpush

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Delivery Tracking</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{routeHelper('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Delivery Tracking</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-number text-primary">{{ $stats['total_sent'] }}</div>
                <div class="stat-label">Total Sent</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-number text-success">{{ $stats['delivered'] }}</div>
                <div class="stat-label">Delivered</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-number text-warning">{{ $stats['pending'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-number text-info">{{ $stats['in_review'] }}</div>
                <div class="stat-label">In Review</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-number text-danger">{{ $stats['cancelled'] }}</div>
                <div class="stat-label">Cancelled</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-number text-secondary">{{ $stats['on_hold'] }}</div>
                <div class="stat-label">On Hold</div>
            </div>
        </div>
    </div>

    <!-- Today's Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-day text-info"></i>
                        Today's Activity
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <h4 class="text-primary">{{ $stats['today_sent'] }}</h4>
                            <p class="text-muted">Orders Sent Today</p>
                        </div>
                        <div class="col-md-6 text-center">
                            <h4 class="text-success">{{ $stats['today_delivered'] }}</h4>
                            <p class="text-muted">Delivered Today</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.delivery.index') }}">
            <div class="row">
                <div class="col-md-2">
                    <select name="delivery_status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('delivery_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_review" {{ request('delivery_status') == 'in_review' ? 'selected' : '' }}>In Review</option>
                        <option value="delivered" {{ request('delivery_status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('delivery_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="hold" {{ request('delivery_status') == 'hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search invoice, tracking, name, phone..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.delivery.index') }}" class="btn btn-secondary">
                        <i class="fas fa-refresh"></i> Clear
                    </a>
                    <button type="button" class="btn btn-info" onclick="checkBalance()">
                        <i class="fas fa-wallet"></i> Balance
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="card-title">Delivery Tracking List</h3>
                </div>
                <div class="col-sm-6 text-right">
                    <button type="button" class="btn btn-success btn-sm" onclick="bulkUpdateStatus()" id="bulkUpdateBtn" disabled>
                        <i class="fas fa-sync"></i> Update Selected
                    </button>
                    <a href="{{ route('admin.delivery.export', request()->query()) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-download"></i> Export
                    </a>
                    <button type="button" class="btn btn-warning btn-sm" onclick="syncAllStatus()">
                        <i class="fas fa-sync-alt"></i> Sync All
                    </button>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table id="deliveryTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="3%">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Consignment ID</th>
                            <th>Tracking Code</th>
                            <th>Delivery Status</th>
                            <th>Last Updated</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_orders[]" value="{{ $order->id }}" class="order-checkbox">
                                </td>
                                <td>
                                    <a href="{{ route('admin.order.show', $order->id) }}" target="_blank" class="text-primary">
                                        {{ $order->invoice }}
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $order->first_name }} {{ $order->last_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($order->address . ', ' . $order->town, 30) }}</small>
                                </td>
                                <td>{{ $order->formatted_phone }}</td>
                                <td>
                                    @if($order->consignment_id)
                                        <span class="consignment-id">{{ $order->consignment_id }}</span>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->tracking_code)
                                        <span class="tracking-code">{{ $order->tracking_code }}</span>
                                        @if($order->tracking_url)
                                            <br>
                                            <a href="{{ $order->tracking_url }}" target="_blank" class="btn btn-xs btn-outline-info">
                                                <i class="fas fa-external-link-alt"></i> Track
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    {!! $order->delivery_status_badge !!}
                                    @if($order->delivery_status_updated_at)
                                        <br>
                                        <small class="text-muted">{{ $order->delivery_status_updated_at->diffForHumans() }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($order->delivery_status_updated_at)
                                        {{ $order->delivery_status_updated_at->format('M d, H:i') }}
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>৳{{ number_format($order->total, 2) }}</strong>
                                    @if($order->pay_staus != 1)
                                        <br>
                                        <small class="text-warning">COD: ৳{{ number_format($order->total, 2) }}</small>
                                    @else
                                        <br>
                                        <small class="text-success">Paid</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.delivery.show', $order->id) }}" 
                                           class="btn btn-info btn-sm" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($order->hasBeenSentToCourier())
                                            <button type="button" class="btn btn-warning btn-sm" 
                                                    onclick="updateSingleStatus({{ $order->id }})" title="Update Status">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-success btn-sm" 
                                                    onclick="sendToCourier({{ $order->id }})" title="Send to Courier">
                                                <i class="fas fa-truck"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
        <!-- /.card-body -->
    </div>

</section>

@endsection

@push('js')
    <!-- DataTables  & Plugins -->
    <script src="/assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(function () {
            $("#deliveryTable").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "paging": false,
                "searching": false,
                "info": false
            });

            // Select all functionality
            $('#selectAll').change(function() {
                $('.order-checkbox').prop('checked', this.checked);
                updateBulkUpdateButton();
            });

            $('.order-checkbox').change(function() {
                updateBulkUpdateButton();
            });

            function updateBulkUpdateButton() {
                const selectedCount = $('.order-checkbox:checked').length;
                $('#bulkUpdateBtn').prop('disabled', selectedCount === 0);
            }
        });

        function sendToCourier(orderId) {
            if (confirm('Are you sure you want to send this order to courier?')) {
                $.ajax({
                    url: `/admin/delivery/send-to-courier/${orderId}`,
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

        function updateSingleStatus(orderId) {
            $.ajax({
                url: `/admin/delivery/update-status/${orderId}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error updating delivery status');
                }
            });
        }

        function bulkUpdateStatus() {
            const selectedIds = $('.order-checkbox:checked').map(function() {
                return this.value;
            }).get();

            if (selectedIds.length === 0) {
                alert('Please select at least one order to update');
                return;
            }

            if (confirm(`Are you sure you want to update delivery status for ${selectedIds.length} orders?`)) {
                $.ajax({
                    url: '{{ route("admin.delivery.bulk-update") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order_ids: selectedIds
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error updating delivery statuses');
                    }
                });
            }
        }

        function checkBalance() {
            $.ajax({
                url: '{{ route("admin.delivery.balance") }}',
                type: 'GET',
                success: function(response) {
                    // Balance will be shown via notification
                },
                error: function(xhr) {
                    alert('Error checking courier balance');
                }
            });
        }

        function syncAllStatus() {
            if (confirm('This will sync delivery status for all pending orders. This may take a while. Continue?')) {
                // Show loading state
                const btn = event.target;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';
                btn.disabled = true;

                $.ajax({
                    url: '{{ route("admin.delivery.bulk-update") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order_ids: @json($orders->pluck('id')->toArray())
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error syncing delivery statuses');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                });
            }
        }
    </script>
@endpush