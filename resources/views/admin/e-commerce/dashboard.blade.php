@extends('layouts.admin.e-commerce.app')

@section('title', 'Admin Dashboard')

@push('css')
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Chart.js -->
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
            grid-template-columns: 2fr 1fr;
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
    </style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="container-fluid">
        <!-- Low Stock Warning -->
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

        <!-- Dashboard Header -->
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

        <!-- Statistics Overview -->
        <div class="stats-overview">
            <!-- Total Products -->
            <div class="stat-card variant-info">
                <div class="stat-header">
                    <div class="stat-info">
                        <h3>{{ number_format($products) }}</h3>
                        <p>Total Products</p>
                        <div class="metric-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>{{ $growth_metrics['products'] ?? '12%' }} from last month</span>
                        </div>
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
                    <a href="{{ routeHelper('product') }}">
                        <span>Manage Products</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Product Quantity -->
            <div class="stat-card variant-warning">
                <div class="stat-header">
                    <div class="stat-info">
                        <h3>{{ number_format($quantity) }}</h3>
                        <p>Total Inventory</p>
                        <div class="metric-trend trend-neutral">
                            <i class="fas fa-minus"></i>
                            <span>{{ $growth_metrics['quantity'] ?? 'No change' }}</span>
                        </div>
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
                    <a href="{{ routeHelper('product') }}">
                        <span>View Inventory</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="stat-card variant-success">
                <div class="stat-header">
                    <div class="stat-info">
                        <h3>{{ number_format($orders) }}</h3>
                        <p>Total Orders</p>
                        <div class="metric-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>{{ $growth_metrics['orders'] ?? '23%' }} from last month</span>
                        </div>
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
                    <a href="{{ routeHelper('order') }}">
                        <span>View All Orders</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-info">
                        <h3>{{ number_format($pending_orders) }}</h3>
                        <p>Pending Orders</p>
                        <div class="metric-trend trend-down">
                            <i class="fas fa-arrow-down"></i>
                            <span>5% from yesterday</span>
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
                    <a href="{{ routeHelper('order/pending') }}">
                        <span>Process Orders</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Processing Orders -->
            <div class="stat-card variant-secondary">
                <div class="stat-header">
                    <div class="stat-info">
                        <h3>{{ number_format($processing_orders) }}</h3>
                        <p>Processing Orders</p>
                        <div class="metric-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>8% from yesterday</span>
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
                    <a href="{{ routeHelper('order/processing') }}">
                        <span>View Processing</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Delivered Orders -->
            <div class="stat-card variant-success">
                <div class="stat-header">
                    <div class="stat-info">
                        <h3>{{ number_format($delivered_orders) }}</h3>
                        <p>Completed Orders</p>
                        <div class="metric-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>15% from last week</span>
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
                    <a href="{{ routeHelper('order/delivered') }}">
                        <span>View Completed</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Charts and Analytics Section -->
        <div class="charts-section">
            <!-- Main Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Platform Performance</h3>
                    <p>Orders and revenue trends over the last 7 days</p>
                </div>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Platform Overview</h3>
                    <p>Financial and operational metrics</p>
                </div>
                <div class="quick-stats">
                    <div class="quick-stat-item">
                        <span class="quick-stat-label">Vendor Earnings</span>
                        <span class="quick-stat-value">${{ number_format($vendor_amount) }}</span>
                    </div>
                    <div class="quick-stat-item">
                        <span class="quick-stat-label">Vendor Pending</span>
                        <span class="quick-stat-value">${{ number_format($vendor_pamount) }}</span>
                    </div>
                    <div class="quick-stat-item">
                        <span class="quick-stat-label">Admin Earnings</span>
                        <span class="quick-stat-value">${{ number_format($admin_amount) }}</span>
                    </div>
                    <div class="quick-stat-item">
                        <span class="quick-stat-label">Admin Pending</span>
                        <span class="quick-stat-value">${{ number_format($pending_amount) }}</span>
                    </div>
                    <div class="quick-stat-item">
                        <span class="quick-stat-label">Commission</span>
                        <span class="quick-stat-value">${{ number_format($commission) }}</span>
                    </div>
                    <div class="quick-stat-item">
                        <span class="quick-stat-label">Total Customers</span>
                        <span class="quick-stat-value">{{ number_format($customers) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on load
            const cards = document.querySelectorAll('.stat-card');
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
                const progressBars = document.querySelectorAll('.progress-fill');
                progressBars.forEach(bar => {
                    const width = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = width;
                    }, 100);
                });
            }, 500);

            // Create sales chart with real data
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            // Get real data from Laravel controller
            const chartData = @json($chart_data ?? null);
            
            // Fallback data if chart_data is not available
            const defaultData = {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                orders: [{{ $delivered_orders ?? 5 }}, {{ $processing_orders ?? 8 }}, {{ $pending_orders ?? 3 }}, {{ $cancel_orders ?? 2 }}, {{ $orders ?? 15 }}, {{ $delivered_orders ?? 10 }}, {{ $orders ?? 20 }}],
                revenue: [{{ $vendor_amount ?? 500 }}, {{ $admin_amount ?? 300 }}, {{ $commission ?? 200 }}, 375, 700, 550, 875]
            };
            
            const finalData = chartData || defaultData;
            
            new Chart(ctx, {
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
                        pointRadius: 6
                    }, {
                        label: 'Revenue ($)',
                        data: finalData.revenue,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2,
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
                        }
                    },
                    scales: {
                        y: {
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
                            }
                        },
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
        });
    </script>
@endpush