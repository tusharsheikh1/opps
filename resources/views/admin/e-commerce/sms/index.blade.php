@extends('layouts.admin.e-commerce.app')

@section('title', 'SMS Logs')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <style>
        .badge-purple {
            background-color: #6f42c1;
            color: white;
        }
        .sms-stats {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            flex: 1;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .filters-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
@endpush

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>SMS Logs</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{routeHelper('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">SMS Logs</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">

    <!-- SMS Statistics -->
    <div class="sms-stats">
        <div class="stat-card">
            <div class="stat-number text-primary">{{ $stats['total'] }}</div>
            <div class="stat-label">Total SMS</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-success">{{ $stats['sent'] }}</div>
            <div class="stat-label">Sent</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-danger">{{ $stats['failed'] }}</div>
            <div class="stat-label">Failed</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-warning">{{ $stats['pending'] }}</div>
            <div class="stat-label">Pending</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.sms.logs.index') }}">
            <div class="row">
                <div class="col-md-2">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        <option value="order_confirmation" {{ request('type') == 'order_confirmation' ? 'selected' : '' }}>Order Confirmation</option>
                        <option value="status_update" {{ request('type') == 'status_update' ? 'selected' : '' }}>Status Update</option>
                        <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="phone" class="form-control" placeholder="Phone number" value="{{ request('phone') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.sms.logs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-refresh"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="card-title">SMS Logs List</h3>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('admin.sms.test') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-paper-plane"></i> Test SMS
                    </a>
                    <a href="{{ route('admin.sms.dashboard') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-chart-bar"></i> Dashboard
                    </a>
                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#clearOldModal">
                        <i class="fas fa-broom"></i> Clear Old
                    </button>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table id="smsLogsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="3%">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Date</th>
                            <th>Phone</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Message Preview</th>
                            <th>Order</th>
                            <th>Sent By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($smsLogs as $log)
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_logs[]" value="{{ $log->id }}" class="log-checkbox">
                                </td>
                                <td>
                                    <small>{{ $log->created_at->format('d M Y') }}</small><br>
                                    <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                </td>
                                <td>{{ $log->formatted_phone }}</td>
                                <td>{!! $log->type_badge !!}</td>
                                <td>{!! $log->status_badge !!}</td>
                                <td>
                                    <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                         title="{{ $log->message }}">
                                        {{ Str::limit($log->message, 50) }}
                                    </div>
                                </td>
                                <td>
                                    @if($log->order)
                                        <a href="{{ route('admin.order.show', $log->order->id) }}" 
                                           class="text-primary" target="_blank">
                                            {{ $log->order->invoice }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->user)
                                        <small>{{ $log->user->name }}</small>
                                    @else
                                        <small class="text-muted">System</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.sms.logs.show', $log->id) }}" 
                                           class="btn btn-info btn-sm" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                onclick="deleteSmsLog({{ $log->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $smsLogs->appends(request()->query())->links() }}
            </div>
        </div>
        <!-- /.card-body -->
    </div>

    <!-- Bulk Actions -->
    <div class="card">
        <div class="card-body">
            <button type="button" class="btn btn-danger" onclick="bulkDeleteSelected()" id="bulkDeleteBtn" disabled>
                <i class="fas fa-trash"></i> Delete Selected
            </button>
            <small class="text-muted ml-2">Select items above to enable bulk actions</small>
        </div>
    </div>

</section>

<!-- Clear Old Modal -->
<div class="modal fade" id="clearOldModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clear Old SMS Logs</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.sms.logs.clear-old') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="days">Delete SMS logs older than:</label>
                        <select name="days" id="days" class="form-control" required>
                            <option value="30">30 days</option>
                            <option value="60">60 days</option>
                            <option value="90">90 days</option>
                            <option value="180">6 months</option>
                            <option value="365">1 year</option>
                        </select>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        This action cannot be undone. Old SMS logs will be permanently deleted.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Clear Old Logs</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js')
    <!-- DataTables  & Plugins -->
    <script src="/assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(function () {
            $("#smsLogsTable").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "paging": false,
                "searching": false,
                "info": false
            });

            // Select all functionality
            $('#selectAll').change(function() {
                $('.log-checkbox').prop('checked', this.checked);
                updateBulkDeleteButton();
            });

            $('.log-checkbox').change(function() {
                updateBulkDeleteButton();
            });

            function updateBulkDeleteButton() {
                const selectedCount = $('.log-checkbox:checked').length;
                $('#bulkDeleteBtn').prop('disabled', selectedCount === 0);
            }
        });

        function deleteSmsLog(id) {
            if (confirm('Are you sure you want to delete this SMS log?')) {
                $.ajax({
                    url: `/admin/sms/logs/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error deleting SMS log');
                    }
                });
            }
        }

        function bulkDeleteSelected() {
            const selectedIds = $('.log-checkbox:checked').map(function() {
                return this.value;
            }).get();

            if (selectedIds.length === 0) {
                alert('Please select at least one SMS log to delete');
                return;
            }

            if (confirm(`Are you sure you want to delete ${selectedIds.length} SMS logs?`)) {
                $.ajax({
                    url: '{{ route("admin.sms.logs.bulk-delete") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error deleting SMS logs');
                    }
                });
            }
        }
    </script>
@endpush