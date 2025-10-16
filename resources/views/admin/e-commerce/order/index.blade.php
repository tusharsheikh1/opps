@extends('layouts.admin.e-commerce.app')

@section('title', 'Order List')

@push('css')
    <link rel="stylesheet" href="/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <style>
        .search-container {
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
        .bulk-actions-card {
            border-left: 4px solid #007bff;
        }
        .bulk-actions-disabled {
            opacity: 0.6;
            pointer-events: none;
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
            
            /* Quick stats mobile */
            .stat-card {
                margin-bottom: 10px;
                padding: 15px;
            }
            
            .stat-value {
                font-size: 1.5rem;
            }
            
            .stat-label {
                font-size: 0.8rem;
            }
            
            /* Search form mobile */
            .search-container {
                padding: 15px;
            }
            
            .search-container .col-md-4,
            .search-container .col-md-3,
            .search-container .col-md-2,
            .search-container .col-md-1 {
                margin-bottom: 15px;
            }
            
            /* Bulk actions mobile */
            .bulk-actions-card .card-tools {
                margin-top: 10px;
            }
            
            .bulk-actions-card .card-tools .btn {
                display: block;
                width: 100%;
                margin: 5px 0;
            }
            
            .bulk-actions-card .col-md-3,
            .bulk-actions-card .col-md-4,
            .bulk-actions-card .col-md-5 {
                margin-bottom: 15px;
            }
            
            .bulk-actions-card .alert {
                font-size: 0.85rem;
            }
            
            /* Analytics mobile */
            .analytics-controls .col-md-6 {
                margin-bottom: 15px;
            }
            
            .analytics-controls .col-md-8,
            .analytics-controls .col-md-4 {
                margin-bottom: 10px;
            }
            
            .detailed-analytics {
                margin-bottom: 15px;
                padding: 15px;
            }
            
            .metric-row {
                flex-direction: column;
                align-items: flex-start;
                padding: 8px 0;
            }
            
            .metric-value {
                margin-top: 5px;
            }
            
            /* Chart container mobile */
            .chart-container {
                height: 250px;
                margin-top: 15px;
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
        
        /* Analytics Styles */
        .analytics-section {
            margin-bottom: 30px;
        }
        .quick-stats {
            margin-bottom: 20px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
        }
        .stat-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.1);
            transform: skewY(-2deg);
            transform-origin: top left;
        }
        .stat-card.revenue { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .stat-card.orders { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card.average { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card.conversion { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .analytics-controls {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .detailed-analytics {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .metric-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .metric-row:last-child {
            border-bottom: none;
        }
        
        .metric-label {
            font-weight: 500;
            color: #333;
        }
        
        .metric-value {
            font-weight: bold;
            color: #007bff;
        }
        
        .percentage-change {
            font-size: 0.8rem;
            margin-left: 10px;
        }
        
        .percentage-change.positive {
            color: #28a745;
        }
        
        .percentage-change.negative {
            color: #dc3545;
        }
        
        .percentage-change.neutral {
            color: #6c757d;
        }
        
        .progress-container {
            margin-top: 5px;
        }
        
        .progress {
            height: 6px;
            border-radius: 3px;
        }
        
        .chart-container {
            height: 300px;
            margin-top: 20px;
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

    <!-- Analytics Section -->
    <div class="analytics-section">
        <!-- Analytics Controls -->
        <div class="analytics-controls">
            <div class="row align-items-center">
                <div class="col-md-6 col-12">
                    <h5 class="mb-2 mb-md-0"><i class="fas fa-chart-line"></i> Order Analytics</h5>
                </div>
                <div class="col-md-6 col-12">
                    <div class="row">
                        <div class="col-md-8 col-8">
                            <select id="analytics_period" class="form-control">
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="this_week">This Week</option>
                                <option value="last_week">Last Week</option>
                                <option value="this_month" selected>This Month</option>
                                <option value="last_month">Last Month</option>
                                <option value="this_year">This Year</option>
                                <option value="last_year">Last Year</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-4">
                            <button type="button" class="btn btn-outline-primary btn-block btn-sm" onclick="updateAnalytics()">
                                <i class="fas fa-sync-alt d-none d-md-inline"></i>
                                <span class="d-none d-md-inline"> Refresh</span>
                                <i class="fas fa-sync-alt d-md-none"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="stat-card revenue">
                        <div class="stat-value" id="quick_revenue">৳0</div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="stat-card orders">
                        <div class="stat-value" id="quick_orders">0</div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="stat-card average">
                        <div class="stat-value" id="quick_avg">৳0</div>
                        <div class="stat-label">Avg Order Value</div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="stat-card conversion">
                        <div class="stat-value" id="quick_conversion">0%</div>
                        <div class="stat-label">Completion Rate</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analytics -->
        <div class="row">
            <div class="col-md-8">
                <div class="detailed-analytics">
                    <h6 class="mb-3"><i class="fas fa-chart-bar"></i> Sales Overview</h6>
                    
                    <div class="metric-row">
                        <div class="metric-label">Total Sales</div>
                        <div>
                            <span class="metric-value" id="total_sales">৳0</span>
                            <span class="percentage-change" id="sales_change">
                                <i class="fas fa-minus"></i> No change
                            </span>
                        </div>
                    </div>
                    
                    <div class="metric-row">
                        <div class="metric-label">Total Orders</div>
                        <div>
                            <span class="metric-value" id="total_orders">0</span>
                            <span class="percentage-change" id="orders_change">
                                <i class="fas fa-minus"></i> No change
                            </span>
                        </div>
                    </div>
                    
                    <div class="metric-row">
                        <div class="metric-label">Average Order Value</div>
                        <div>
                            <span class="metric-value" id="avg_order">৳0</span>
                            <span class="percentage-change" id="avg_change">
                                <i class="fas fa-minus"></i> No change
                            </span>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <canvas id="sales-trend-chart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="detailed-analytics">
                    <h6 class="mb-3"><i class="fas fa-list-ul"></i> Order Status</h6>
                    
                    <div class="metric-row">
                        <div class="metric-label">Pending Orders</div>
                        <div>
                            <span class="metric-value" id="pending_orders">0</span>
                            <div class="percentage-change" id="pending_change">
                                <i class="fas fa-clock"></i> All clear
                            </div>
                        </div>
                    </div>
                    
                    <div class="metric-row">
                        <div class="metric-label">Processing Orders</div>
                        <div>
                            <span class="metric-value" id="processing_orders">0</span>
                            <div class="percentage-change" id="processing_change">
                                <i class="fas fa-cog"></i> 0 in progress
                            </div>
                        </div>
                    </div>
                    
                    <div class="metric-row">
                        <div class="metric-label">Delivered Orders</div>
                        <div>
                            <span class="metric-value" id="delivered_orders">0</span>
                            <div class="percentage-change" id="delivered_change">
                                <i class="fas fa-check"></i> 0 completed
                            </div>
                        </div>
                    </div>
                    
                    <div class="metric-row">
                        <div class="metric-label">Cancelled Orders</div>
                        <div>
                            <span class="metric-value" id="cancelled_orders">0</span>
                            <div class="percentage-change" id="cancelled_change">
                                <i class="fas fa-times"></i> No cancellations
                            </div>
                        </div>
                    </div>
                    
                    <div class="metric-row">
                        <div class="metric-label">Refund Orders</div>
                        <div>
                            <span class="metric-value" id="refund_orders">0</span>
                            <div class="percentage-change" id="refund_change">
                                <i class="fas fa-undo"></i> No refunds
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="detailed-analytics mt-3">
                    <h6 class="mb-3"><i class="fas fa-tachometer-alt"></i> Performance</h6>
                    
                    <div class="metric-row">
                        <div class="metric-label">Completion Rate</div>
                        <div>
                            <span class="metric-value" id="completion_rate">0%</span>
                            <div class="progress-container">
                                <div class="progress">
                                    <div class="progress-bar bg-success" id="completion_bar" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="metric-row">
                        <div class="metric-label">Cancellation Rate</div>
                        <div>
                            <span class="metric-value" id="cancellation_rate">0%</span>
                            <div class="progress-container">
                                <div class="progress">
                                    <div class="progress-bar bg-danger" id="cancellation_bar" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="metric-row">
                        <div class="metric-label">Processing Efficiency</div>
                        <div>
                            <span class="metric-value" id="processing_rate">0%</span>
                            <div class="progress-container">
                                <div class="progress">
                                    <div class="progress-bar bg-info" id="processing_bar" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="metric-row">
                        <div class="metric-label">Return Rate</div>
                        <div>
                            <span class="metric-value" id="return_rate">0%</span>
                            <div class="progress-container">
                                <div class="progress">
                                    <div class="progress-bar bg-warning" id="return_bar" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-search"></i> Search & Filter Orders</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-secondary" id="clear-filters">
                    <i class="fas fa-times"></i> Clear All
                </button>
            </div>
        </div>
        <div class="card-body search-container">
            <form id="search-form">
                <div class="row">
                    <!-- Search Input -->
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

                    <!-- Status Filter -->
                    <div class="col-md-3 col-6 mb-3">
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

                    <!-- Per Page -->
                    <div class="col-md-1 col-6 mb-3">
                        <label for="per_page" class="form-label">Show</label>
                        <select class="form-control" id="per_page" name="per_page">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div class="col-md-2 col-6 mb-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" 
                               class="form-control" 
                               id="date_from" 
                               name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>

                    <!-- Date To -->
                    <div class="col-md-2 col-6 mb-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" 
                               class="form-control" 
                               id="date_to" 
                               name="date_to" 
                               value="{{ request('date_to') }}">
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
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card bulk-actions-card" id="bulk-actions-card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6 col-12">
                    <h3 class="card-title">
                        <i class="fas fa-tasks"></i> Bulk Actions
                        <span id="bulk-selected-info" class="ml-2 text-muted">(0 orders selected)</span>
                    </h3>
                </div>
                <div class="col-md-6 col-12">
                    <div class="card-tools">
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
        </div>
        <div class="card-body" id="bulk-actions-body">
            <form action="{{ route('admin.order.bulk-status-update') }}" method="POST" id="bulk-action-form">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-3 col-12">
                        <div class="form-group mb-0">
                            <label for="bulk_status">Change Status To:</label>
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
                    <div class="col-md-4 col-12">
                        <div class="form-group mb-0">
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
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="alert alert-info mb-0 py-2">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> Only orders in valid states for the selected action will be updated.
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Hidden container for selected order IDs -->
                <div id="selected-order-ids"></div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
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
        
        <!-- Non-Sticky Pagination -->
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
            let salesChart = null;
            let statusChart = null;
            let courierDataCache = new Map(); // Cache for courier data to avoid duplicate requests

            // Initialize analytics charts
            initializeCharts();
            
            // Load initial analytics data
            updateAnalytics();
            
            // Auto-update analytics every 5 minutes
            setInterval(updateAnalytics, 300000);

            // Load courier success rates for visible orders
            loadCourierSuccessRates();

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

            // Analytics period change
            $('#analytics_period').on('change', function() {
                updateAnalytics();
            });

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

            function initializeCharts() {
                // Sales Trend Chart
                const salesCtx = document.getElementById('sales-trend-chart');
                if (salesCtx) {
                    salesChart = new Chart(salesCtx, {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Sales (৳)',
                                data: [],
                                borderColor: '#007bff',
                                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                                tension: 0.4,
                                yAxisID: 'y'
                            }, {
                                label: 'Orders',
                                data: [],
                                borderColor: '#28a745',
                                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                tension: 0.4,
                                yAxisID: 'y1'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            },
                            scales: {
                                x: {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: 'Period'
                                    }
                                },
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    title: {
                                        display: true,
                                        text: 'Sales (৳)'
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    title: {
                                        display: true,
                                        text: 'Orders'
                                    },
                                    grid: {
                                        drawOnChartArea: false,
                                    },
                                }
                            }
                        }
                    });
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

            // Analytics Functions - Updated for Real Data
            function updateAnalytics() {
                const period = $('#analytics_period').val();
                
                // Show loading state
                $('.metric-value').html('<i class="fas fa-spinner fa-spin"></i>');
                $('#quick_revenue, #quick_orders, #quick_avg, #quick_conversion').html('<i class="fas fa-spinner fa-spin"></i>');
                
                // AJAX call to get real analytics data
                $.ajax({
                    url: '{{ route("admin.order.analytics") }}',
                    type: 'GET',
                    data: { period: period },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.success) {
                            updateAnalyticsDisplay(response);
                            updateChartsData(response);
                        } else {
                            console.error('Analytics fetch failed:', response.message);
                            showAnalyticsError();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Analytics AJAX failed:', {xhr, status, error});
                        showAnalyticsError();
                    }
                });
            }

            function updateAnalyticsDisplay(data) {
                const current = data.current;
                const trends = data.trends;

                // Update quick stats
                $('#quick_revenue').text(`৳${current.total_sales.toLocaleString()}`);
                $('#quick_orders').text(current.total_orders.toLocaleString());
                $('#quick_avg').text(`৳${current.avg_order_value.toLocaleString()}`);
                $('#quick_conversion').text(`${current.completion_rate}%`);

                // Update detailed metrics
                $('#total_sales').text(`৳${current.total_sales.toLocaleString()}`);
                $('#total_orders').text(current.total_orders.toLocaleString());
                $('#avg_order').text(`৳${current.avg_order_value.toLocaleString()}`);
                $('#pending_orders').text(current.pending.toLocaleString());
                $('#processing_orders').text(current.processing.toLocaleString());
                $('#delivered_orders').text(current.delivered.toLocaleString());
                $('#cancelled_orders').text(current.cancelled.toLocaleString());
                $('#refund_orders').text(current.refund.toLocaleString());

                // Update percentage changes using trends data
                if (trends.total_sales) {
                    updatePercentageChangeFromTrend('#sales_change', trends.total_sales);
                }
                if (trends.total_orders) {
                    updatePercentageChangeFromTrend('#orders_change', trends.total_orders);
                }
                if (trends.avg_order_value) {
                    updatePercentageChangeFromTrend('#avg_change', trends.avg_order_value);
                }

                // Update performance indicators
                updateProgressBar('#completion_rate', '#completion_bar', current.completion_rate);
                updateProgressBar('#cancellation_rate', '#cancellation_bar', current.cancellation_rate);
                updateProgressBar('#processing_rate', '#processing_bar', current.processing_efficiency);
                updateProgressBar('#return_rate', '#return_bar', current.return_rate);

                // Update other status indicators
                $('#pending_change').html(`<i class="fas fa-clock"></i> ${current.pending > 0 ? 'Needs attention' : 'All clear'}`);
                $('#processing_change').html(`<i class="fas fa-cog"></i> ${current.processing} in progress`);
                $('#delivered_change').html(`<i class="fas fa-check"></i> ${current.delivered} successfully completed`);
                $('#cancelled_change').html(`<i class="fas fa-times"></i> ${current.cancelled > 0 ? 'Monitor trends' : 'No cancellations'}`);
                $('#refund_change').html(`<i class="fas fa-undo"></i> ${current.refund > 0 ? current.refund + ' pending' : 'No refunds'}`);
            }

            function updatePercentageChangeFromTrend(selector, trendData) {
                const element = $(selector);
                const change = trendData.change;
                const direction = trendData.direction;
                
                if (direction === 'up') {
                    element.html(`<i class="fas fa-arrow-up"></i> +${Math.abs(change)}% from last period`);
                    element.removeClass('negative neutral').addClass('positive');
                } else if (direction === 'down') {
                    element.html(`<i class="fas fa-arrow-down"></i> -${Math.abs(change)}% from last period`);
                    element.removeClass('positive neutral').addClass('negative');
                } else {
                    element.html(`<i class="fas fa-minus"></i> No change from last period`);
                    element.removeClass('positive negative').addClass('neutral');
                }
            }

            function updateChartsData(data) {
                if (data.charts && salesChart && statusChart) {
                    // Update sales trend chart
                    if (data.charts.sales_trend) {
                        salesChart.data.labels = data.charts.sales_trend.labels;
                        salesChart.data.datasets[0].data = data.charts.sales_trend.sales;
                        salesChart.data.datasets[1].data = data.charts.sales_trend.orders;
                        salesChart.update('none'); // No animation for better performance
                    }

                    // Update status distribution chart
                    if (data.charts.status_distribution) {
                        const statusLabels = Object.keys(data.charts.status_distribution);
                        const statusValues = Object.values(data.charts.status_distribution);
                        
                        statusChart.data.labels = statusLabels;
                        statusChart.data.datasets[0].data = statusValues;
                        statusChart.update('none'); // No animation for better performance
                    }
                }
            }

            function updateProgressBar(valueSelector, barSelector, percentage) {
                $(valueSelector).text(`${percentage}%`);
                $(barSelector).css('width', `${percentage}%`);
            }

            function showAnalyticsError() {
                // Show error state in analytics
                $('.metric-value').text('Error');
                $('#quick_revenue, #quick_orders, #quick_avg, #quick_conversion').text('Error');
                
                // Show error message
                if (!$('#analytics-error').length) {
                    $('.quick-stats').before(`
                        <div id="analytics-error" class="alert alert-warning alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Analytics Error:</strong> Unable to load analytics data. 
                            <button type="button" class="btn btn-sm btn-outline-warning ml-2" onclick="updateAnalytics()">
                                <i class="fas fa-retry"></i> Retry
                            </button>
                        </div>
                    `);
                }
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