@extends('layouts.admin.e-commerce.app')

@section('title', 'SMS Log Details')

@push('css')
<style>
    .detail-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .status-badge-large {
        font-size: 1.1rem;
        padding: 8px 16px;
    }
    .message-content {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        font-family: 'Courier New', monospace;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .response-content {
        background-color: #f1f3f4;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        max-height: 200px;
        overflow-y: auto;
    }
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
    }
    .info-value {
        text-align: right;
        color: #6c757d;
    }
</style>
@endpush

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>SMS Log Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{routeHelper('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sms.logs.index') }}">SMS Logs</a></li>
                    <li class="breadcrumb-item active">Log Details</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">

    <div class="row">
        <!-- SMS Information -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sms text-primary"></i>
                        SMS Information
                    </h3>
                    <div class="card-tools">
                        {!! $smsLog->status_badge !!}
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-phone text-primary"></i>
                            Phone Number:
                        </span>
                        <span class="info-value">
                            <strong>{{ $smsLog->formatted_phone }}</strong>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-tag text-info"></i>
                            Type:
                        </span>
                        <span class="info-value">
                            {!! $smsLog->type_badge !!}
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-calendar text-secondary"></i>
                            Created:
                        </span>
                        <span class="info-value">
                            {{ $smsLog->created_at->format('F d, Y \a\t H:i:s') }}
                            <small class="text-muted d-block">{{ $smsLog->created_at->diffForHumans() }}</small>
                        </span>
                    </div>

                    @if($smsLog->sent_at)
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-paper-plane text-success"></i>
                            Sent:
                        </span>
                        <span class="info-value">
                            {{ $smsLog->sent_at->format('F d, Y \a\t H:i:s') }}
                            <small class="text-muted d-block">{{ $smsLog->sent_at->diffForHumans() }}</small>
                        </span>
                    </div>
                    @endif

                    @if($smsLog->user)
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-user text-warning"></i>
                            Sent By:
                        </span>
                        <span class="info-value">
                            <strong>{{ $smsLog->user->name }}</strong>
                            <small class="text-muted d-block">{{ $smsLog->user->email }}</small>
                        </span>
                    </div>
                    @else
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-robot text-secondary"></i>
                            Sent By:
                        </span>
                        <span class="info-value">
                            <em>System (Automated)</em>
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Message Content -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-comment-alt text-success"></i>
                        Message Content
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-secondary">{{ strlen($smsLog->message) }} characters</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="message-content">{{ $smsLog->message }}</div>
                </div>
            </div>

            <!-- API Response -->
            @if($smsLog->response)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-code text-info"></i>
                        API Response
                    </h3>
                </div>
                <div class="card-body">
                    <div class="response-content">{{ $smsLog->response }}</div>
                </div>
            </div>
            @endif
        </div>

        <!-- Related Information -->
        <div class="col-md-4">
            <!-- Order Information -->
            @if($smsLog->order)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shopping-bag text-primary"></i>
                        Related Order
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <span class="info-label">Invoice:</span>
                        <span class="info-value">
                            <a href="{{ route('admin.order.show', $smsLog->order->id) }}" 
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                {{ $smsLog->order->invoice }}
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Customer:</span>
                        <span class="info-value">
                            <strong>{{ $smsLog->order->first_name }} {{ $smsLog->order->last_name }}</strong>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Total:</span>
                        <span class="info-value">
                            <strong>{{ setting('CURRENCY_ICON') ?? '৳' }}{{ number_format($smsLog->order->total, 2) }}</strong>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="info-value">
                            @if ($smsLog->order->status == 0)
                                <span class="badge badge-warning">Pending</span>
                            @elseif ($smsLog->order->status == 1)
                                <span class="badge badge-primary">Processing</span>
                            @elseif ($smsLog->order->status == 2)
                                <span class="badge badge-danger">Canceled</span>
                            @elseif ($smsLog->order->status == 3)
                                <span class="badge badge-success">Delivered</span>
                            @elseif ($smsLog->order->status == 4)
                                <span class="badge badge-info">Shipping</span>
                            @elseif ($smsLog->order->status == 5)
                                <span class="badge badge-danger">Refund</span>
                            @else
                                <span class="badge badge-secondary">Unknown</span>
                            @endif
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Order Date:</span>
                        <span class="info-value">
                            {{ $smsLog->order->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

            <!-- SMS Statistics for this Phone -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar text-info"></i>
                        SMS History for {{ $smsLog->formatted_phone }}
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $phoneStats = \App\Models\SmsLog::where('phone', $smsLog->phone)->get();
                        $phoneTotalSms = $phoneStats->count();
                        $phoneSentSms = $phoneStats->where('status', 'sent')->count();
                        $phoneFailedSms = $phoneStats->where('status', 'failed')->count();
                        $phonePendingSms = $phoneStats->where('status', 'pending')->count();
                    @endphp

                    <div class="info-item">
                        <span class="info-label">Total SMS:</span>
                        <span class="info-value"><strong>{{ $phoneTotalSms }}</strong></span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Sent:</span>
                        <span class="info-value">
                            <span class="badge badge-success">{{ $phoneSentSms }}</span>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Failed:</span>
                        <span class="info-value">
                            <span class="badge badge-danger">{{ $phoneFailedSms }}</span>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Pending:</span>
                        <span class="info-value">
                            <span class="badge badge-warning">{{ $phonePendingSms }}</span>
                        </span>
                    </div>

                    @if($phoneTotalSms > 0)
                    <div class="info-item">
                        <span class="info-label">Success Rate:</span>
                        <span class="info-value">
                            <strong>{{ round(($phoneSentSms / $phoneTotalSms) * 100, 1) }}%</strong>
                        </span>
                    </div>
                    @endif

                    @if($phoneTotalSms > 1)
                    <div class="mt-3">
                        <a href="{{ route('admin.sms.logs.index', ['phone' => $smsLog->phone]) }}" 
                           class="btn btn-sm btn-outline-info btn-block">
                            <i class="fas fa-list"></i>
                            View All SMS for This Number
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools text-warning"></i>
                        Actions
                    </h3>
                </div>
                <div class="card-body">
                    @if($smsLog->order)
                    <a href="{{ route('admin.order.show', $smsLog->order->id) }}" 
                       class="btn btn-primary btn-block mb-2" target="_blank">
                        <i class="fas fa-shopping-bag"></i>
                        View Order Details
                    </a>
                    @endif

                    <a href="{{ route('admin.sms.logs.index', ['phone' => $smsLog->phone]) }}" 
                       class="btn btn-info btn-block mb-2">
                        <i class="fas fa-phone"></i>
                        SMS History for This Number
                    </a>

                    <button type="button" class="btn btn-danger btn-block" 
                            onclick="deleteLog({{ $smsLog->id }})">
                        <i class="fas fa-trash"></i>
                        Delete This Log
                    </button>
                </div>
            </div>
        </div>
    </div>

</section>

@endsection

@push('js')
<script>
function deleteLog(id) {
    if (confirm('Are you sure you want to delete this SMS log? This action cannot be undone.')) {
        $.ajax({
            url: `/admin/sms/logs/${id}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                window.location.href = '{{ route("admin.sms.logs.index") }}';
            },
            error: function(xhr) {
                alert('Error deleting SMS log. Please try again.');
            }
        });
    }
}
</script>
@endpush