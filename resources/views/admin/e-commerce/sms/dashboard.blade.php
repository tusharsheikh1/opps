@extends('layouts.admin.e-commerce.app')

@section('title', 'SMS Dashboard')

@push('css')
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
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .config-status {
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .config-enabled {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    .config-disabled {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    .recent-activity {
        max-height: 400px;
        overflow-y: auto;
    }
    .activity-item {
        border-left: 3px solid #007bff;
        padding-left: 15px;
        margin-bottom: 15px;
        padding-bottom: 10px;
    }
    .activity-item.success {
        border-left-color: #28a745;
    }
    .activity-item.failed {
        border-left-color: #dc3545;
    }
    .activity-item.pending {
        border-left-color: #ffc107;
    }
</style>
@endpush

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>SMS Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{routeHelper('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">SMS Dashboard</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt text-warning"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.sms.test') }}" class="btn btn-primary mr-2">
                        <i class="fas fa-paper-plane"></i>
                        Test SMS
                    </a>
                    <a href="{{ route('admin.sms.logs.index') }}" class="btn btn-info mr-2">
                        <i class="fas fa-list"></i>
                        View All Logs
                    </a>
                    <a href="{{ route('admin.setting.mailsmsapireglog') }}" class="btn btn-secondary">
                        <i class="fas fa-cog"></i>
                        SMS Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs text-secondary"></i>
                        Configuration Status
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="config-status {{ $configStatus['enabled'] ? 'config-enabled' : 'config-disabled' }}">
                                <i class="fas fa-{{ $configStatus['enabled'] ? 'check-circle' : 'times-circle' }}"></i>
                                <strong>SMS Service:</strong> {{ $configStatus['enabled'] ? 'Enabled' : 'Disabled' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="config-status {{ $configStatus['api_url'] ? 'config-enabled' : 'config-disabled' }}">
                                <i class="fas fa-{{ $configStatus['api_url'] ? 'check-circle' : 'times-circle' }}"></i>
                                <strong>API URL:</strong> {{ $configStatus['api_url'] ? 'Configured' : 'Not Set' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="config-status {{ $configStatus['api_key'] ? 'config-enabled' : 'config-disabled' }}">
                                <i class="fas fa-{{ $configStatus['api_key'] ? 'check-circle' : 'times-circle' }}"></i>
                                <strong>API Key:</strong> {{ $configStatus['api_key'] ? 'Configured' : 'Not Set' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="config-status {{ $configStatus['sender_id'] ? 'config-enabled' : 'config-disabled' }}">
                                <i class="fas fa-{{ $configStatus['sender_id'] ? 'check-circle' : 'times-circle' }}"></i>
                                <strong>Sender ID:</strong> {{ $configStatus['sender_id'] ? 'Set' : 'Not Set' }}
                            </div>
                        </div>
                    </div>
                    @if(!$configStatus['enabled'] || !$configStatus['api_url'] || !$configStatus['api_key'] || !$configStatus['sender_id'])
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Configuration Required:</strong> Please complete your SMS configuration to start sending SMS messages.
                            <a href="{{ route('admin.setting.mailsmsapireglog') }}" class="btn btn-sm btn-warning ml-2">
                                Configure Now
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number text-primary">{{ $stats['total'] }}</div>
                <div class="stat-label">Total SMS</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number text-success">{{ $stats['sent'] }}</div>
                <div class="stat-label">Successfully Sent</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number text-danger">{{ $stats['failed'] }}</div>
                <div class="stat-label">Failed</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number text-warning">{{ $stats['pending'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
    </div>

    <!-- Today's Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-day text-info"></i>
                        Today's SMS Activity
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h4 class="text-primary">{{ $todayStats['total'] }}</h4>
                            <p class="text-muted">Total Today</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-success">{{ $todayStats['sent'] }}</h4>
                            <p class="text-muted">Sent Today</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-danger">{{ $todayStats['failed'] }}</h4>
                            <p class="text-muted">Failed Today</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-warning">{{ $todayStats['pending'] }}</h4>
                            <p class="text-muted">Pending Today</p>
                        </div>
                    </div>
                    @if($todayStats['total'] > 0)
                        <div class="progress mt-3">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ ($todayStats['sent'] / $todayStats['total']) * 100 }}%">
                                {{ round(($todayStats['sent'] / $todayStats['total']) * 100, 1) }}% Success Rate
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & SMS Types -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock text-info"></i>
                        Recent SMS Activity
                    </h3>
                </div>
                <div class="card-body recent-activity">
                    @if($recentLogs->count() > 0)
                        @foreach($recentLogs as $log)
                            <div class="activity-item {{ $log->status }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $log->formatted_phone }}</strong>
                                        <span class="badge badge-{{ $log->status == 'sent' ? 'success' : ($log->status == 'failed' ? 'danger' : 'warning') }} ml-2">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                        @if($log->type == 'order_confirmation')
                                            <span class="badge badge-info ml-1">Order Confirmation</span>
                                        @elseif($log->type == 'status_update')
                                            <span class="badge badge-primary ml-1">Status Update</span>
                                        @else
                                            <span class="badge badge-secondary ml-1">Custom</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                        @if($log->order)
                                            <small class="text-muted">• Order: {{ $log->order->invoice }}</small>
                                        @endif
                                        <br>
                                        <small class="text-truncate d-block" style="max-width: 500px;">
                                            {{ Str::limit($log->message, 100) }}
                                        </small>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.sms.logs.show', $log->id) }}" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.sms.logs.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list"></i>
                                View All SMS Logs
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No SMS Activity Yet</h5>
                            <p class="text-muted">SMS logs will appear here once you start sending messages.</p>
                            <a href="{{ route('admin.sms.test') }}" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                Send Test SMS
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- SMS Types Breakdown -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie text-success"></i>
                        SMS Types
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $orderConfirmations = \App\Models\SmsLog::where('type', 'order_confirmation')->count();
                        $statusUpdates = \App\Models\SmsLog::where('type', 'status_update')->count();
                        $customSms = \App\Models\SmsLog::where('type', 'custom')->count();
                        $total = $orderConfirmations + $statusUpdates + $customSms;
                    @endphp

                    @if($total > 0)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Order Confirmations</span>
                                <strong>{{ $orderConfirmations }}</strong>
                            </div>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-info" style="width: {{ ($orderConfirmations / $total) * 100 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Status Updates</span>
                                <strong>{{ $statusUpdates }}</strong>
                            </div>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-primary" style="width: {{ ($statusUpdates / $total) * 100 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Custom SMS</span>
                                <strong>{{ $customSms }}</strong>
                            </div>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-secondary" style="width: {{ ($customSms / $total) * 100 }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-chart-pie fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No SMS data available yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tachometer-alt text-warning"></i>
                        Quick Stats
                    </h3>
                </div>
                <div class="card-body">
                    @if($stats['total'] > 0)
                        <div class="mb-2">
                            <strong>Success Rate:</strong>
                            <span class="float-right">{{ round(($stats['sent'] / $stats['total']) * 100, 1) }}%</span>
                        </div>
                        <div class="mb-2">
                            <strong>Failure Rate:</strong>
                            <span class="float-right">{{ round(($stats['failed'] / $stats['total']) * 100, 1) }}%</span>
                        </div>
                        <div class="mb-2">
                            <strong>Avg. per Day:</strong>
                            <span class="float-right">
                                @php
                                    $firstLog = \App\Models\SmsLog::oldest()->first();
                                    $daysSince = $firstLog ? $firstLog->created_at->diffInDays(now()) + 1 : 1;
                                    $avgPerDay = round($stats['total'] / $daysSince, 1);
                                @endphp
                                {{ $avgPerDay }}
                            </span>
                        </div>
                    @else
                        <p class="text-muted text-center">No statistics available yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</section>

@endsection