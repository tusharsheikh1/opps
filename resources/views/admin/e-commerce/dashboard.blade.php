@extends('layouts.admin.e-commerce.app')

@section('title', 'Admin Dashboard')

@push('css')
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --light-gray: #f8fafc;
            --medium-gray: #e2e8f0;
            --dark-gray: #475569;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --white: #ffffff;
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --card-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --border-radius: 12px;
            --transition: all 0.2s ease-in-out;
        }

        .dashboard-container {
            background-color: var(--light-gray);
            min-height: 100vh;
            padding: 24px 0;
        }

        .low-warning {
            background: linear-gradient(135deg, var(--danger-color) 0%, #f87171 100%);
            color: white;
            padding: 16px 24px;
            border-radius: var(--border-radius);
            border: none;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin: 24px 0;
            box-shadow: var(--card-shadow-lg);
            transition: var(--transition);
            font-weight: 600;
        }

        .low-warning:hover {
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .dashboard-header {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--medium-gray);
        }

        .dashboard-header h1 {
            color: var(--text-dark);
            font-weight: 700;
            font-size: 2.25rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .dashboard-header h1 i {
            color: var(--primary-color);
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item a {
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb-item a:hover {
            color: var(--primary-color);
        }

        .breadcrumb-item.active {
            color: var(--text-dark);
        }

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 24px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            border: 1px solid var(--medium-gray);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--card-shadow-lg);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .stat-info h3 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            line-height: 1;
        }

        .stat-info p {
            color: var(--text-light);
            font-size: 0.875rem;
            font-weight: 500;
            margin: 4px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            background: var(--primary-color);
        }

        .stat-card.variant-success .stat-icon { background: var(--success-color); }
        .stat-card.variant-warning .stat-icon { background: var(--warning-color); }
        .stat-card.variant-danger .stat-icon { background: var(--danger-color); }
        .stat-card.variant-info .stat-icon { background: var(--info-color); }
        .stat-card.variant-secondary .stat-icon { background: var(--secondary-color); }

        .progress-container {
            margin: 16px 0;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background-color: var(--light-gray);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 3px;
            transition: width 1s ease-in-out;
        }

        .stat-card.variant-success .progress-fill { 
            background: linear-gradient(90deg, var(--success-color), #34d399); 
        }
        .stat-card.variant-warning .progress-fill { 
            background: linear-gradient(90deg, var(--warning-color), #fbbf24); 
        }
        .stat-card.variant-danger .progress-fill { 
            background: linear-gradient(90deg, var(--danger-color), #f87171); 
        }
        .stat-card.variant-info .progress-fill { 
            background: linear-gradient(90deg, var(--info-color), #22d3ee); 
        }

        .stat-footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--medium-gray);
        }

        .stat-footer a {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: var(--transition);
            font-size: 0.875rem;
        }

        .stat-footer a:hover {
            color: var(--primary-color);
            text-decoration: none;
        }

        .stat-footer i {
            transition: var(--transition);
        }

        .stat-footer a:hover i {
            transform: translateX(4px);
        }

        .charts-section {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            margin-top: 32px;
        }

        .chart-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 24px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--medium-gray);
        }

        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
            overflow: hidden;
        }

        .chart-container canvas {
            max-width: 100%;
            height: auto !important;
        }

        .chart-header {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--medium-gray);
        }

        .chart-header h3 {
            color: var(--text-dark);
            font-weight: 600;
            font-size: 1.125rem;
            margin: 0;
        }

        .chart-header p {
            color: var(--text-light);
            font-size: 0.875rem;
            margin: 4px 0 0 0;
        }

        .quick-stats {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .quick-stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            background: var(--light-gray);
            border-radius: 8px;
            transition: var(--transition);
        }

        .quick-stat-item:hover {
            background: var(--medium-gray);
        }

        .quick-stat-label {
            color: var(--text-dark);
            font-weight: 500;
            font-size: 0.875rem;
        }

        .quick-stat-value {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1rem;
        }

        @media (max-width: 1024px) {
            .charts-section {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                height: 300px;
            }
        }

        @media (max-width: 768px) {
            .stats-overview {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .dashboard-header {
                padding: 24px;
                text-align: center;
            }
            
            .dashboard-header h1 {
                font-size: 1.875rem;
                justify-content: center;
            }
            
            .stat-card {
                padding: 20px;
            }
            
            .stat-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 16px;
            }
            
            .chart-container {
                height: 250px;
            }
            
            .chart-card {
                padding: 16px;
            }
        }

        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .metric-trend {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-top: 8px;
        }

        .trend-up { color: var(--success-color); }
        .trend-down { color: var(--danger-color); }
        .trend-neutral { color: var(--text-light); }


        /* Analytics Styles */
        .analytics-section {
            margin-top: 32px;
            margin-bottom: 30px;
        }
        /* Overwrite .quick-stats for analytics section */
        .analytics-section .quick-stats {
            margin-bottom: 20px;
            display: grid; /* Use grid for responsiveness */
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        /* Overwrite .stat-card for analytics section */
        .analytics-section .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
            border: none; /* Remove border from dashboard .stat-card */
        }
        .analytics-section .stat-card:before {
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
        .analytics-section .stat-card.revenue { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .analytics-section .stat-card.orders { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .analytics-section .stat-card.average { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .analytics-section .stat-card.conversion { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        
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
        
        /* Overwrite .progress-container for analytics */
        .analytics-section .progress-container {
            margin-top: 5px;
        }
        
        .progress {
            height: 6px;
            border-radius: 3px;
        }
        
        /* Overwrite .chart-container for analytics */
        .analytics-section .chart-container {
            height: 300px;
            margin-top: 20px;
        }
    </style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="container-fluid">
        @php
            $low_products = \App\Models\Product::where('quantity', '<', '6')->count();
        @endphp
        
        @if($low_products > 0)
            <div class="row">
                <div class="col-12">
                    <a class="low-warning" href="{{ route('admin.low.product') }}">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>{{ $low_products }} {{ Str::plural('product', $low_products) }} across platform with low stock</span>
                    </a>
                </div>
            </div>
        @endif

        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-chart-line"></i>
                        Admin Dashboard
                    </h1>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-sm-end mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-home"></i> Home
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Admin Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="stats-overview">
            <div class="stat-card variant-info">
                <div class="stat-header">
                    <div class="stat-info">
                        <h3>{{ number_format($products ?? 0) }}</h3>
                        <p>Total Products</p>
                        @if(isset($growth_metrics['products']))
                            <div class="metric-trend trend-up">
                                <i class="fas fa-arrow-up"></i>
                                <span>{{ $growth_metrics['products'] }} from last month</span>
                            </div>
                        @endif
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $progress_data['products'] ?? 75 }}%"></div>
                    </div>
                </div>
                <div class="stat-footer">
                    <a href="{{ route('admin.product.index') }}">
                        <span>Manage Products</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="stat-card variant-warning">
                <div class="stat-header">
                    <div class="stat-info">
                        <h3>{{ number_format($quantity ?? 0) }}</h3>
                        <p>Total Inventory</p>
                        @if(isset($growth_metrics['quantity']))
                            <div class="metric-trend trend-neutral">
                                <i class="fas fa-minus"></i>
                                <span>{{ $growth_metrics['quantity'] }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $progress_data['quantity'] ?? 60 }}%"></div>
                    </div>
                </div>
                <div class="stat-footer">
                    <a href="{{ route('admin.product.index') }}">
                        <span>View Inventory</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="stat-card variant-success">
                <div class="stat-header">
                    <div class="stat-info">
                        <h3>{{ number_format($orders ?? 0) }}</h3>
                        <p>Total Orders</p>
                        @if(isset($growth_metrics['orders']))
                            <div class="metric-trend trend-up">
                                <i class="fas fa-arrow-up"></i>
                                <span>{{ $growth_metrics['orders'] }} from last month</span>
                            </div>
                        @endif
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $progress_data['orders'] ?? 85 }}%"></div>
                    </div>
                </div>
                <div class="stat-footer">
                    <a href="{{ route('admin.order.index') }}">
                        <span>View All Orders</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-info">
                        <h3>{{ number_format($pending_orders ?? 0) }}</h3>
                        <p>Pending Orders</p>
                        <div class="metric-trend trend-down">
                            <i class="fas fa-arrow-down"></i>
                            <span>Needs attention</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 45%"></div>
                    </div>
                </div>
                <div class="stat-footer">
                    <a href="{{ route('admin.order.pending') }}">
                        <span>Process Orders</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="stat-card variant-secondary">
                <div class="stat-header">
                    <div class="stat-info">
                        <h3>{{ number_format($processing_orders ?? 0) }}</h3>
                        <p>Processing Orders</p>
                        <div class="metric-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>In progress</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-cog fa-spin"></i>
                    </div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 70%"></div>
                    </div>
                </div>
                <div class="stat-footer">
                    <a href="{{ route('admin.order.processing') }}">
                        <span>View Processing</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="stat-card variant-success">
                <div class="stat-header">
                    <div class="stat-info">
                        <h3>{{ number_format($delivered_orders ?? 0) }}</h3>
                        <p>Completed Orders</p>
                        <div class="metric-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>Successfully delivered</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 90%"></div>
                    </div>
                </div>
                <div class="stat-footer">
                    <a href="{{ route('admin.order.delivered') }}">
                        <span>View Completed</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="stat-card variant-danger">
                <div class="stat-header">
                    <div class="stat-info">
                        <h3>{{ number_format($cancel_orders ?? 0) }}</h3>
                        <p>Cancelled Orders</p>
                        <div class="metric-trend trend-neutral">
                            <i class="fas fa-times"></i>
                            <span>Refund pending</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 25%"></div>
                    </div>
                </div>
                <div class="stat-footer">
                    <a href="{{ route('admin.order.cancel') }}">
                        <span>View Cancelled</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="stat-card variant-info">
                <div class="stat-header">
                    <div class="stat-info">
                        <h3>{{ number_format($customers ?? 0) }}</h3>
                        <p>Total Customers</p>
                        @if(isset($growth_metrics['customers']))
                            <div class="metric-trend trend-up">
                                <i class="fas fa-arrow-up"></i>
                                <span>{{ $growth_metrics['customers'] }} new this week</span>
                            </div>
                        @endif
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 65%"></div>
                    </div>
                </div>
                <div class="stat-footer">
                    <a href="{{ route('admin.customer.index') }}">
                        <span>View Customers</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="charts-section">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Platform Performance</h3>
                    <p>Orders and revenue trends over the last 7 days</p>
                </div>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="analytics-section">
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
                                    <option value: "this_week">This Week</option>
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

            <div class="quick-stats">
                <div class="row" style="width: 100%;">
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
        </div> </div>
</div>
@endsection

@push('js')
    <script>
        // Analytics Functions (MOVED FROM index.blade.php)
        function initializeCharts() {
            // Sales Trend Chart
            const salesCtx = document.getElementById('sales-trend-chart');
            if (salesCtx) {
                analyticsSalesChart = new Chart(salesCtx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Sales ({{ setting('CURRENCY_SYMBOL') ?? '৳' }})',
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
                                    text: 'Sales ({{ setting('CURRENCY_SYMBOL') ?? '৳' }})'
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
            const currency = '{{ setting('CURRENCY_SYMBOL') ?? '৳' }}';

            // Update quick stats
            $('#quick_revenue').text(currency + current.total_sales.toLocaleString());
            $('#quick_orders').text(current.total_orders.toLocaleString());
            $('#quick_avg').text(currency + current.avg_order_value.toLocaleString());
            $('#quick_conversion').text(`${current.completion_rate}%`);

            // Update detailed metrics
            $('#total_sales').text(currency + current.total_sales.toLocaleString());
            $('#total_orders').text(current.total_orders.toLocaleString());
            $('#avg_order').text(currency + current.avg_order_value.toLocaleString());
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
            if (data.charts && analyticsSalesChart && statusChart) {
                // Update sales trend chart
                if (data.charts.sales_trend) {
                    analyticsSalesChart.data.labels = data.charts.sales_trend.labels;
                    analyticsSalesChart.data.datasets[0].data = data.charts.sales_trend.sales;
                    analyticsSalesChart.data.datasets[1].data = data.charts.sales_trend.orders;
                    analyticsSalesChart.update('none'); // No animation for better performance
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
        
        // --- End of Moved Analytics JS ---


        // Chart variable definitions for Analytics
        let analyticsSalesChart = null;
        let statusChart = null; // This was in index.js, keeping it for consistency

        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on load
            const cards = document.querySelectorAll('.stats-overview .stat-card'); // Target only main cards
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease-out';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Animate progress bars
            setTimeout(() => {
                const progressBars = document.querySelectorAll('.stats-overview .progress-fill'); // Target only main bars
                progressBars.forEach(bar => {
                    const width = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = width;
                    }, 100);
                });
            }, 500);

            // Create sales chart with real data (Original Dashboard Chart)
            const ctx = document.getElementById('salesChart');
            if (ctx) {
                const chartContext = ctx.getContext('2d');
                
                // Get real data from Laravel controller
                const chartData = @json($chart_data ?? null);
                
                // Fallback data if chart_data is not available
                const defaultData = {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    orders: [
                        {{ $delivered_orders ?? 5 }}, 
                        {{ $processing_orders ?? 8 }}, 
                        {{ $pending_orders ?? 3 }}, 
                        {{ $cancel_orders ?? 2 }}, 
                        {{ round(($orders ?? 15) * 0.8) }}, 
                        {{ round(($delivered_orders ?? 10) * 1.2) }}, 
                        {{ $orders ?? 20 }}
                    ],
                    revenue: [
                        {{ round(($vendor_amount ?? 500) * 0.7) }}, 
                        {{ round(($admin_amount ?? 300) * 0.9) }}, 
                        {{ round(($commission ?? 200) * 1.1) }}, 
                        {{ round(($vendor_amount ?? 500) * 0.75) }}, 
                        {{ round(($admin_amount ?? 300) * 1.3) }}, 
                        {{ round(($commission ?? 200) * 1.4) }}, 
                        {{ round(($vendor_amount ?? 500) * 0.95) }}
                    ]
                };
                
                const finalData = chartData || defaultData;
                
                new Chart(chartContext, {
                    type: 'line',
                    data: {
                        labels: finalData.labels,
                        datasets: [{
                            label: 'Orders',
                            data: finalData.orders,
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#6366f1',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            yAxisID: 'y', // <-- ADDED
                        }, {
                            label: 'Revenue ({{ setting('CURRENCY_SYMBOL') ?? '$' }})',
                            data: finalData.revenue,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            yAxisID: 'y1', // <-- ADDED
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // <-- CHANGED
                        // aspectRatio: 2, // <-- REMOVED
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        layout: {
                            padding: {
                                top: 10,
                                bottom: 10
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            if (context.dataset.label.includes('Revenue')) {
                                                label += '{{ setting('CURRENCY_SYMBOL') ?? '$' }}' + context.parsed.y.toFixed(2);
                                            } else {
                                                label += context.parsed.y;
                                            }
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            // --- REPLACED 'y' scale ---
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                beginAtZero: true,
                                grid: {
                                    color: '#e2e8f0',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#64748b',
                                    font: {
                                        size: 11
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Orders',
                                    color: '#6366f1',
                                    font: {
                                        weight: 'bold'
                                    }
                                }
                            },
                            // --- ADDED 'y1' scale ---
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true,
                                grid: {
                                    drawOnChartArea: false, // only show grid for left axis
                                },
                                ticks: {
                                    color: '#64748b',
                                    font: {
                                        size: 11
                                    },
                                    callback: function(value, index, values) {
                                        return '{{ setting('CURRENCY_SYMBOL') ?? '$' }}' + value.toFixed(0);
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Revenue',
                                    color: '#10b981',
                                    font: {
                                        weight: 'bold'
                                    }
                                }
                            },
                            // --- 'x' scale is unchanged ---
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#64748b',
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            }


            // --- Analytics Initialization (MOVED) ---
            // Initialize analytics charts
            initializeCharts();
            
            // Load initial analytics data
            updateAnalytics();
            
            // Auto-update analytics every 5 minutes
            setInterval(updateAnalytics, 300000);

            // Analytics period change
            $('#analytics_period').on('change', function() {
                updateAnalytics();
            });
            // --- End of Analytics Initialization ---

        });
    </script>
@endpush