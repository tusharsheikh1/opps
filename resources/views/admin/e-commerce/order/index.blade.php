@extends('layouts.admin.e-commerce.app')

@section('title', 'Order List')

@push('css')
    <link rel="stylesheet" href="/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <style>
        .controls-container { /* Replaces .search-container */
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .table-container {
            position: relative;
        }
        .search-info {
            color: #6c757d;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        
        /* Pagination - Non-sticky & Mobile-friendly */
        .pagination-wrapper {
            position: static !important;
            background: transparent;
            padding: 15px 0;
            margin-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        
        .pagination-wrapper .pagination {
            margin-bottom: 0;
            justify-content: center;
        }

        /* Courier Success Rate Styles */
        .courier-success-indicator {
            display: inline-block;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 2px 6px;
            margin-left: 5px;
            font-size: 10px;
            font-weight: bold;
            min-width: 35px;
            text-align: center;
            position: relative;
            cursor: help;
        }

        .courier-success-indicator.loading {
            background: #f8f9fa;
            border-color: #6c757d;
            color: #6c757d;
        }

        .courier-success-indicator.high {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-color: #28a745;
            color: #155724;
        }

        .courier-success-indicator.medium {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-color: #ffc107;
            color: #856404;
        }

        .courier-success-indicator.low {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border-color: #dc3545;
            color: #721c24;
        }

        .courier-success-indicator.unknown {
            background: #f8f9fa;
            border-color: #6c757d;
            color: #6c757d;
        }

        .courier-success-indicator .loading-dot {
            width: 4px;
            height: 4px;
            background: #6c757d;
            border-radius: 50%;
            display: inline-block;
            animation: loading-dots 1.4s infinite ease-in-out both;
        }

        .courier-success-indicator .loading-dot:nth-child(1) { animation-delay: -0.32s; }
        .courier-success-indicator .loading-dot:nth-child(2) { animation-delay: -0.16s; }

        @keyframes loading-dots {
            0%, 80%, 100% { 
                transform: scale(0);
            } 40% { 
                transform: scale(1);
            }
        }
        
        /* Bulk actions styling within the combined card */
        .bulk-actions-section {
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
            margin-top: 20px;
        }
        
        .bulk-actions-disabled {
            opacity: 0.6;
            pointer-events: none;
        }
        
        /* Ensure date input group icon is clickable */
        .input-group-append {
            cursor: pointer;
        }

        /* Mobile-friendly improvements */
        @media (max-width: 768px) {
            .pagination-wrapper {
                padding: 10px;
            }
            
            .pagination-wrapper .d-flex {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .pagination-info {
                font-size: 0.9rem;
                order: 2;
            }
            
            .pagination-links {
                order: 1;
            }
            
            .pagination .page-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.9rem;
            }

            /* Search form mobile */
            .controls-container { /* Updated class */
                padding: 15px;
            }
            
            .controls-container .col-md-4,
            .controls-container .col-md-3,
            .controls-container .col-md-2,
            .controls-container .col-md-1 {
                margin-bottom: 15px;
            }
            
            /* Bulk actions mobile */
            .bulk-actions-header {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .bulk-actions-header .card-tools {
                margin-top: 10px;
                width: 100%;
                display: flex;
                justify-content: space-between;
            }
            
            .bulk-actions-header .card-tools .btn {
                flex-grow: 1;
            }
            
            .bulk-actions-body .col-md-3,
            .bulk-actions-body .col-md-4,
            .bulk-actions-body .col-md-5,
            .bulk-actions-body .col-md-6 {
                margin-bottom: 15px;
            }
            
            .bulk-actions-body .btn {
                width: 100%;
            }
            
            .bulk-actions-body .alert {
                font-size: 0.85rem;
                text-align: center;
            }
            
            /* Card headers mobile */
            .card-header h3.card-title {
                font-size: 1.1rem;
            }
            
            .card-header .card-tools {
                margin-top: 10px;
            }
            
            /* Button improvements for mobile */
            .btn-sm {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
            
            /* Content header mobile */
            .content-header .breadcrumb {
                background: none;
                margin-bottom: 0;
                padding: 0;
            }

            /* Courier indicator mobile */
            .courier-success-indicator {
                font-size: 9px;
                padding: 1px 4px;
                min-width: 30px;
            }
        }
    </style>
@endpush

@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Order List</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{routeHelper('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Order List</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-search"></i> Search, Filter & Bulk Actions</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-secondary" id="clear-filters">
                    <i class="fas fa-times"></i> Clear All Filters
                </button>
            </div>
        </div>
        <div class="card-body controls-container">
            <form id="search-form" class="mb-4">
                <div class="row">
                    <div class="col-md-4 col-12 mb-3">
                        <label for="search" class="form-label">Search Orders</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   placeholder="Search by invoice, name, phone..."
                                   value="{{ request('search') }}">
                        </div>
                        <small class="text-muted d-none d-md-block">Search across invoice, customer name, phone, email, address, payment method</small>
                    </div>

                    <div class="col-md-2 col-6 mb-3">
                        <label for="status_filter" class="form-label">Status</label>
                        <select class="form-control" id="status_filter" name="status_filter">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status_filter') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Processing" {{ request('status_filter') == 'Processing' ? 'selected' : '' }}>Processing</option>
                            <option value="Shipping" {{ request('status_filter') == 'Shipping' ? 'selected' : '' }}>Shipping</option>
                            <option value="Delivered" {{ request('status_filter') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="Canceled" {{ request('status_filter') == 'Canceled' ? 'selected' : '' }}>Canceled</option>
                            <option value="refund" {{ request('status_filter') == 'refund' ? 'selected' : '' }}>Refund</option>
                            <option value="Return Requested" {{ request('status_filter') == 'Return Requested' ? 'selected' : '' }}>Return Requested</option>
                            <option value="Return Accepted" {{ request('status_filter') == 'Return Accepted' ? 'selected' : '' }}>Return Accepted</option>
                            <option value="Returned" {{ request('status_filter') == 'Returned' ? 'selected' : '' }}>Returned</option>
                        </select>
                    </div>

                    <div class="col-md-1 col-6 mb-3">
                        <label for="per_page" class="form-label">Show</label>
                        <select class="form-control" id="per_page" name="per_page">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-6 mb-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="date_from" 
                                   name="date_from" 
                                   value="{{ request('date_from') }}"
                                   placeholder="mm/dd/yyyy">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 col-6 mb-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="date_to" 
                                   name="date_to" 
                                   value="{{ request('date_to') }}"
                                   placeholder="mm/dd/yyyy">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> 
                            <span class="d-none d-sm-inline">Search</span>
                        </button>
                        <button type="button" class="btn btn-secondary ml-2" id="live-search-toggle">
                            <i class="fas fa-bolt"></i> 
                            <span id="live-search-text" class="d-none d-sm-inline">Enable Live Search</span>
                        </button>
                    </div>
                </div>
            </form>

            <div class="bulk-actions-section">
                <div class="row align-items-center mb-3 bulk-actions-header">
                    <div class="col-md-6 col-12">
                        <h5 class="mb-0">
                            <i class="fas fa-tasks"></i> Bulk Actions
                            <span id="bulk-selected-info" class="ml-2 text-muted" style="font-weight: 400; font-size: 0.9em;">(0 orders selected)</span>
                        </h5>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="card-tools d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="select-all-btn">
                                <i class="fas fa-check-square"></i> 
                                <span class="d-none d-md-inline">Select All Visible</span>
                                <span class="d-md-none">Select All</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary ml-1" id="deselect-all-btn">
                                <i class="fas fa-square"></i> 
                                <span class="d-none d-md-inline">Deselect All</span>
                                <span class="d-md-none">Deselect</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="bulk-actions-body" class="bulk-actions-body">
                    <form action="{{ route('admin.order.bulk-status-update') }}" method="POST" id="bulk-action-form">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-3 col-12">
                                <div class="form-group mb-0"> <label for="bulk_status">Change Status To:</label>
                                    <select name="status" id="bulk_status" class="form-control" required>
                                        <option value="">Select Status</option>
                                        <option value="0">Pending</option>
                                        <option value="1">Processing</option>
                                        <option value="4">Shipping</option>
                                        <option value="3">Delivered</option>
                                        <option value="2">Cancel</option>
                                        <option value="7">Return Accepted</option>
                                        <option value="8">Returned</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 mt-3 mt-md-0">
                                <button type="submit" class="btn btn-primary" id="bulk-apply-btn">
                                    <i class="fas fa-check"></i> 
                                    <span class="d-none d-sm-inline">Apply to Selected Orders</span>
                                    <span class="d-sm-none">Apply</span>
                                </button>
                                <span id="bulk-loading" class="ml-2" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i> 
                                    <span class="d-none d-sm-inline">Processing...</span>
                                </span>
                            </div>
                            <div class="col-md-6 col-12 mt-3 mt-md-0">
                                <div class="alert alert-info mb-0 py-2">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Note:</strong> Only orders in valid states for the selected action will be updated.
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div id="selected-order-ids"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-sm-6 col-12">
                    <h3 class="card-title">Order List</h3>
                </div>
                <div class="col-sm-6 col-12 text-sm-right text-center mt-2 mt-sm-0">
                    <div id="search-info" class="search-info">
                        @if(request()->hasAny(['search', 'status_filter', 'date_from', 'date_to']))
                            Showing filtered results
                        @else
                            Showing all orders
                        @endif
                        ({{ $orders->total() }} total)
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-container">
                <div id="loading-overlay" class="loading-overlay" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                
                <div id="table-wrapper">
                    @include('admin.e-commerce.order.partials.order-table', ['orders' => $orders])
                </div>
            </div>
        </div>
        
        <div class="pagination-wrapper" id="pagination-wrapper">
            <div class="d-flex justify-content-between align-items-center">
                <div class="pagination-info text-muted">
                    @if($orders->total() > 0)
                        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} results
                    @else
                        No results found
                    @endif
                </div>
                <div class="pagination-links">
                    {{ $orders->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>

</section>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            let liveSearchEnabled = false;
            let searchTimeout;
            let selectedOrderIds = new Set();
            let isFilteredView = false;
            let courierDataCache = new Map(); // Cache for courier data to avoid duplicate requests

            // Load courier success rates for visible orders
            loadCourierSuccessRates();
            
            // Initialize bulk actions state on load
            updateBulkActionsState();

            // Initialize on page load
            $(document).ready(function() {
                // Check if page loaded with parameters and treat as filtered
                const currentUrl = new URL(window.location);
                const hasParams = currentUrl.search && currentUrl.search.length > 1;
                
                if (hasParams) {
                    // Populate form fields from URL
                    ['search', 'status_filter', 'date_from', 'date_to', 'per_page'].forEach(param => {
                        const value = currentUrl.searchParams.get(param);
                        if (value) {
                            $(`#${param}`).val(value);
                        }
                    });
                }
            });
            
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // !! NOTE: Add your JS Datepicker initialization here, e.g.:
            // $('#date_from, #date_to').datepicker({
            //     format: 'yyyy-mm-dd' // Adjust format as needed
            // });
            // For the demo, the text inputs will work but won't have a calendar popup.

            // Live search toggle
            $('#live-search-toggle').on('click', function() {
                liveSearchEnabled = !liveSearchEnabled;
                const text = liveSearchEnabled ? 'Disable Live Search' : 'Enable Live Search';
                const icon = liveSearchEnabled ? 'fas fa-bolt-lightning' : 'fas fa-bolt';
                
                $('#live-search-text').text(text);
                $(this).find('i').attr('class', icon);
                
                if (liveSearchEnabled) {
                    $(this).removeClass('btn-secondary').addClass('btn-success');
                } else {
                    $(this).removeClass('btn-success').addClass('btn-secondary');
                }
            });

            // Form submission
            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                isFilteredView = true; // Mark as filtered when searching
                performSearch();
            });

            // Live search on input
            $('#search, #status_filter, #date_from, #date_to, #per_page').on('input change', function() {
                if (liveSearchEnabled) {
                    isFilteredView = true; // Mark as filtered when using live search
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        performSearch();
                    }, 500);
                }
            });

            // Clear filters
            $('#clear-filters').on('click', function() {
                $('#search-form')[0].reset();
                selectedOrderIds.clear();
                updateBulkActionsState();
                isFilteredView = false; // Reset filtered state
                courierDataCache.clear(); // Clear courier cache
                
                // Always redirect to base URL when clearing filters
                window.location.href = '{{ route("admin.order.index") }}';
            });

            // Select all visible checkbox
            $(document).on('change', '#select-all', function() {
                const isChecked = this.checked;
                $('.order-checkbox').each(function() {
                    $(this).prop('checked', isChecked);
                    const orderId = $(this).val();
                    if (isChecked) {
                        selectedOrderIds.add(orderId);
                    } else {
                        selectedOrderIds.delete(orderId);
                    }
                });
                updateBulkActionsState();
            });

            // Individual checkbox change
            $(document).on('change', '.order-checkbox', function() {
                const orderId = $(this).val();
                if (this.checked) {
                    selectedOrderIds.add(orderId);
                } else {
                    selectedOrderIds.delete(orderId);
                }
                
                updateBulkActionsState();
                updateSelectAllState();
            });

            // Select all visible button
            $('#select-all-btn').on('click', function() {
                $('.order-checkbox').each(function() {
                    $(this).prop('checked', true);
                    selectedOrderIds.add($(this).val());
                });
                updateBulkActionsState();
                updateSelectAllState();
            });

            // Deselect all button
            $('#deselect-all-btn').on('click', function() {
                $('.order-checkbox').prop('checked', false);
                $('#select-all').prop('checked', false);
                selectedOrderIds.clear();
                updateBulkActionsState();
            });

            // Bulk form submission
            $('#bulk-action-form').on('submit', function(e) {
                e.preventDefault();
                
                if (selectedOrderIds.size === 0) {
                    alert('Please select at least one order.');
                    return false;
                }
                
                const selectedStatus = $('#bulk_status').val();
                if (!selectedStatus) {
                    alert('Please select a status to apply.');
                    return false;
                }
                
                if (!confirm(`Are you sure you want to update ${selectedOrderIds.size} order(s) to the selected status?`)) {
                    return false;
                }
                
                // Show loading state
                $('#bulk-apply-btn').prop('disabled', true);
                $('#bulk-loading').show();
                
                // Clear existing hidden inputs
                $('#selected-order-ids').empty();
                
                // Add selected IDs to form
                selectedOrderIds.forEach(function(orderId) {
                    $('#selected-order-ids').append(`<input type="hidden" name="order_ids[]" value="${orderId}">`);
                });
                
                // Submit the form
                this.submit();
            });

            // Pagination links - Handle both search and regular pagination
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const urlObj = new URL(url);
                const page = urlObj.searchParams.get('page') || 1;
                
                // Always use AJAX if we're in filtered view or if URL has parameters
                if (isFilteredView || urlObj.search.length > 0) {
                    loadSearchPage(page);
                } else {
                    // For unfiltered views, we can use regular navigation
                    window.location.href = url;
                }
            });

            function performSearch() {
                showLoading();
                isFilteredView = true; // Mark as filtered when performing search
                loadSearchPage(1); // Always start from page 1 for new searches
            }

            function loadSearchPage(page = 1) {
                // Get current form data
                let formData = $('#search-form').serialize();
                
                // If no form data, try to get from current URL
                if (!formData || formData.trim() === '') {
                    const currentUrl = new URL(window.location);
                    const params = new URLSearchParams();
                    
                    // Copy relevant parameters from current URL
                    ['search', 'status_filter', 'date_from', 'date_to', 'per_page'].forEach(param => {
                        const value = currentUrl.searchParams.get(param);
                        if (value) {
                            params.set(param, value);
                        }
                    });
                    
                    formData = params.toString();
                }
                
                const searchUrl = '{{ route("admin.order.search") }}';
                
                // Add page parameter
                const params = new URLSearchParams(formData);
                params.set('page', page);
                
                $.ajax({
                    url: searchUrl,
                    type: 'GET',
                    data: params.toString(),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.success !== false && response.html) {
                            $('#table-wrapper').html(response.html);
                            
                            // Update pagination with improved info
                            const paginationHtml = `
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="pagination-info text-muted">
                                        Showing ${response.showing.from || 0} to ${response.showing.to || 0} of ${response.showing.total} results (filtered)
                                    </div>
                                    <div class="pagination-links">
                                        ${response.pagination}
                                    </div>
                                </div>
                            `;
                            $('#pagination-wrapper').html(paginationHtml);
                            
                            // Update search info
                            $('#search-info').html(`
                                Showing filtered results (${response.showing.total} total)
                            `);
                            
                            // Restore selection states for visible checkboxes
                            $('.order-checkbox').each(function() {
                                if (selectedOrderIds.has($(this).val())) {
                                    $(this).prop('checked', true);
                                }
                            });
                            
                            updateBulkActionsState();
                            updateSelectAllState();
                            hideLoading();
                            
                            // Update URL without page reload
                            const newUrl = new URL(window.location);
                            newUrl.search = params.toString();
                            window.history.pushState({}, '', newUrl);
                            
                            // Mark as filtered view since we successfully loaded filtered data
                            isFilteredView = true;
                            
                            // Initialize tooltips for new content
                            $('[data-toggle="tooltip"]').tooltip();

                            // Load courier success rates for new content
                            loadCourierSuccessRates();
                        } else {
                            throw new Error(response.message || 'Invalid response format');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Search failed:', {xhr, status, error});
                        hideLoading();
                        
                        let errorMessage = 'Search failed. Please try again.';
                        
                        if (xhr.status === 422) {
                            errorMessage = 'Invalid search parameters. Please check your input.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error occurred. Please try again later.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        // Show error message
                        $('#table-wrapper').html(`
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> ${errorMessage}
                                <button type="button" class="btn btn-sm btn-outline-danger ml-2" onclick="location.reload()">
                                    Reload Page
                                </button>
                            </div>
                        `);
                    }
                });
            }

            function showLoading() {
                $('#loading-overlay').show();
            }

            function hideLoading() {
                $('#loading-overlay').hide();
            }

            function updateBulkActionsState() {
                const selectedCount = selectedOrderIds.size;
                
                $('#bulk-selected-info').text(`(${selectedCount} orders selected)`);
                
                if (selectedCount > 0) {
                    $('#bulk-actions-body').removeClass('bulk-actions-disabled');
                    $('#bulk-apply-btn').prop('disabled', false);
                } else {
                    $('#bulk-actions-body').addClass('bulk-actions-disabled');
                    $('#bulk-apply-btn').prop('disabled', true);
                }
            }

            function updateSelectAllState() {
                const totalVisible = $('.order-checkbox').length;
                const checkedVisible = $('.order-checkbox:checked').length;
                
                if (checkedVisible === 0) {
                    $('#select-all').prop('indeterminate', false).prop('checked', false);
                } else if (checkedVisible === totalVisible) {
                    $('#select-all').prop('indeterminate', false).prop('checked', true);
                } else {
                    $('#select-all').prop('indeterminate', true);
                }
            }

            // Courier Success Rate Functions
            function loadCourierSuccessRates() {
                // Get all unique phone numbers from visible orders
                const phoneNumbers = new Set();
                $('.courier-success-indicator[data-phone]').each(function() {
                    const phone = $(this).data('phone');
                    if (phone && phone !== 'null' && phone !== '') {
                        phoneNumbers.add(phone);
                    }
                });

                // Load courier data for each unique phone number
                phoneNumbers.forEach(phone => {
                    loadCourierDataForPhone(phone);
                });
            }

            function loadCourierDataForPhone(phone) {
                // Check if we already have cached data
                if (courierDataCache.has(phone)) {
                    const cachedData = courierDataCache.get(phone);
                    updateCourierIndicatorsForPhone(phone, cachedData);
                    return;
                }

                // Show loading state for this phone
                $(`.courier-success-indicator[data-phone="${phone}"]`).each(function() {
                    $(this).removeClass('high medium low unknown').addClass('loading')
                           .html('<span class="loading-dot"></span><span class="loading-dot"></span><span class="loading-dot"></span>');
                });

                // Make AJAX request
                $.ajax({
                    url: '{{ route("admin.order.phone-history") }}',
                    type: 'GET',
                    data: { phone: phone },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        let courierData = null;
                        
                        if (response.courier_history && response.courier_history.success && response.courier_history.data) {
                            courierData = response.courier_history.data;
                        }

                        // Cache the result
                        courierDataCache.set(phone, courierData);
                        
                        // Update indicators
                        updateCourierIndicatorsForPhone(phone, courierData);
                    },
                    error: function() {
                        // Cache the error result to avoid repeated failed requests
                        courierDataCache.set(phone, null);
                        updateCourierIndicatorsForPhone(phone, null);
                    }
                });
            }

            function updateCourierIndicatorsForPhone(phone, courierData) {
                $(`.courier-success-indicator[data-phone="${phone}"]`).each(function() {
                    const indicator = $(this);
                    
                    if (courierData && courierData.total_orders > 0) {
                        const successRate = courierData.success_rate;
                        const totalOrders = courierData.total_orders;
                        
                        let rateClass = 'unknown';
                        if (successRate >= 85) {
                            rateClass = 'high';
                        } else if (successRate >= 70) {
                            rateClass = 'medium';
                        } else {
                            rateClass = 'low';
                        }
                        
                        indicator.removeClass('loading high medium low unknown').addClass(rateClass);
                        indicator.html(`${successRate}%`);
                        
                        // Update tooltip
                        const tooltipText = `Courier Success Rate: ${successRate}% (${courierData.total_successful}/${totalOrders} delivered)`;
                        indicator.attr('title', tooltipText).tooltip('dispose').tooltip();
                        
                    } else {
                        indicator.removeClass('loading high medium low').addClass('unknown');
                        indicator.html('N/A');
                        indicator.attr('title', 'No courier delivery data available').tooltip('dispose').tooltip();
                    }
                });
            }
        });

        // Global functions for table interactions
        function showPhoneHistory(phone) {
            // This function is defined in order-table.blade.php
            console.log('showPhoneHistory called for:', phone);
        }

        function openSmsModal(orderId, invoice, phone, customerName, total) {
            // This function is defined in order-table.blade.php
            console.log('openSmsModal called for order:', orderId);
        }
    </script>
@endpush