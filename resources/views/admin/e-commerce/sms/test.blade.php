@extends('layouts.admin.e-commerce.app')

@section('title', 'Test SMS Configuration')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Test SMS Configuration</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{routeHelper('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sms.logs.index') }}">SMS Logs</a></li>
                    <li class="breadcrumb-item active">Test SMS</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-paper-plane text-primary"></i>
                        Send Test SMS
                    </h3>
                </div>
                <div class="card-body">
                    <!-- SMS Configuration Status -->
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> SMS Configuration Status</h5>
                        <ul class="mb-0">
                            <li><strong>SMS Service:</strong> 
                                @if(setting('sms_config_status') == 1)
                                    <span class="text-success">Enabled</span>
                                @else
                                    <span class="text-danger">Disabled</span>
                                @endif
                            </li>
                            <li><strong>API URL:</strong> 
                                @if(!empty(setting('SMS_API_URL')))
                                    <span class="text-success">Configured</span>
                                @else
                                    <span class="text-danger">Not Set</span>
                                @endif
                            </li>
                            <li><strong>API Key:</strong> 
                                @if(!empty(setting('SMS_API_KEY')))
                                    <span class="text-success">Configured</span>
                                @else
                                    <span class="text-danger">Not Set</span>
                                @endif
                            </li>
                            <li><strong>Sender ID:</strong> 
                                @if(!empty(setting('SMS_API_SENDER_ID')))
                                    <span class="text-success">{{ setting('SMS_API_SENDER_ID') }}</span>
                                @else
                                    <span class="text-danger">Not Set</span>
                                @endif
                            </li>
                        </ul>
                    </div>

                    @if(setting('sms_config_status') != 1)
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> SMS Service Disabled</h5>
                            SMS service is currently disabled. Please enable it in the 
                            <a href="{{ route('admin.setting.mailsmsapireglog') }}" target="_blank">SMS Configuration</a> 
                            before testing.
                        </div>
                    @endif

                    <!-- Test Form -->
                    <form method="POST" action="{{ route('admin.sms.test.send') }}">
                        @csrf
                        
                        <div class="form-group">
                            <label for="phone">
                                <i class="fas fa-phone text-primary"></i>
                                Phone Number <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   placeholder="Enter phone number (e.g., 01712345678 or 8801712345678)"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Enter phone number with or without country code. For Bangladesh: 01XXXXXXXXX or 8801XXXXXXXXX
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="message">
                                <i class="fas fa-edit text-primary"></i>
                                Test Message
                            </label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" 
                                      name="message" 
                                      rows="4"
                                      maxlength="500"
                                      placeholder="Enter your test message (optional)">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Leave empty to use default test message. Maximum 500 characters.
                                <span id="charCount">0</span>/500
                            </small>
                        </div>

                        <!-- Quick Test Messages -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-list text-success"></i>
                                Quick Test Messages:
                            </label>
                            <div class="btn-group-vertical btn-block">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setMessage('Hello! This is a test SMS from {{ setting('APP_NAME') ?? 'Your Website' }}. SMS service is working correctly!')">
                                    Basic Test Message
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="setMessage('Order confirmed! Invoice: #12345. Total: ৳500. Payment: Cash on Delivery. Thank you for shopping with {{ setting('APP_NAME') ?? 'us' }}!')">
                                    Order Confirmation Sample
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="setMessage('Your order has been delivered successfully! Thank you for your business.')">
                                    Delivery Confirmation Sample
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" 
                                    class="btn btn-primary"
                                    @if(setting('sms_config_status') != 1) disabled @endif>
                                <i class="fas fa-paper-plane"></i>
                                Send Test SMS
                            </button>
                            <a href="{{ route('admin.sms.logs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-list"></i>
                                View SMS Logs
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Test Results -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history text-info"></i>
                        Recent Test Results
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $recentTests = \App\Models\SmsLog::where('type', 'custom')
                                                        ->where('user_id', auth()->id())
                                                        ->orderBy('created_at', 'desc')
                                                        ->limit(5)
                                                        ->get();
                    @endphp

                    @if($recentTests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTests as $test)
                                        <tr>
                                            <td>
                                                <small>{{ $test->created_at->format('M d, H:i') }}</small>
                                            </td>
                                            <td>{{ $test->formatted_phone }}</td>
                                            <td>{!! $test->status_badge !!}</td>
                                            <td>
                                                <small title="{{ $test->message }}">
                                                    {{ Str::limit($test->message, 50) }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">
                            <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                            No test SMS sent yet. Send your first test SMS above!
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</section>

@endsection

@push('js')
<script>
function setMessage(message) {
    document.getElementById('message').value = message;
    updateCharCount();
}

function updateCharCount() {
    const message = document.getElementById('message').value;
    const charCount = document.getElementById('charCount');
    charCount.textContent = message.length;
    
    if (message.length > 500) {
        charCount.style.color = 'red';
    } else if (message.length > 400) {
        charCount.style.color = 'orange';
    } else {
        charCount.style.color = 'green';
    }
}

// Initialize character count
document.addEventListener('DOMContentLoaded', function() {
    const messageField = document.getElementById('message');
    messageField.addEventListener('input', updateCharCount);
    updateCharCount();
});
</script>
@endpush