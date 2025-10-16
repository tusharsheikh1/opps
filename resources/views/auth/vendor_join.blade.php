@extends('layouts.frontend.app')

@section('title', 'Shopping Now')

@section('content')
<style>
    :root {
        --primary_color: #6366f1;
        --primary-dark: #4f46e5;
        --primary-light: #818cf8;
        --success-color: #10b981;
        --error-color: #ef4444;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --bg-primary: #f8fafc;
        --bg-secondary: #ffffff;
        --border-color: #e5e7eb;
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    .wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%);
    }

    form .form2 {
        background: var(--bg-secondary);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: var(--shadow-xl);
        border: 1px solid rgba(99, 102, 241, 0.1);
        padding: 2.5rem;
        margin-top: 0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    form .form2::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary_color), var(--success-color), var(--primary_color));
        border-radius: 20px 20px 0 0;
    }

    form .form2:hover {
        transform: translateY(-3px);
        box-shadow: 0 25px 50px -12px rgba(99, 102, 241, 0.2);
    }

    ul.cc {
        display: flex;
        padding: 0;
        margin: 0 0 0;
        border-radius: 16px 16px 0 0;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    ul.cc li {
        flex: 1;
        list-style: none;
    }

    ul.cc li a {
        display: block;
        padding: 1.25rem;
        text-align: center;
        color: white;
        background: rgba(99, 102, 241, 0.8);
        transition: all 0.3s ease;
        font-weight: 600;
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    ul.cc li:last-child a {
        background: var(--success-color);
    }

    ul.cc li a::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    ul.cc li a:hover::before {
        left: 100%;
    }

    ul.cc li a:hover {
        transform: translateY(-2px);
        text-decoration: none;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -0.75rem;
    }

    .col-md-6, .col-md-12, .col-6 {
        padding: 0 0.75rem;
        margin-bottom: 1.5rem;
    }

    .col-md-6 { flex: 0 0 50%; }
    .col-md-12 { flex: 0 0 100%; }
    .col-6 { flex: 0 0 50%; }

    .col-md-6.offset-md-3 {
        flex: 0 0 50%;
        margin-left: 25%;
    }

    .form-group {
        margin-bottom: 1.5rem;
        position: relative;
    }

    .form-group label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
    }

    .form-control {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        background-color: var(--bg-secondary);
        color: var(--text-primary);
        font-size: 1rem;
        transition: all 0.3s ease;
        outline: none;
        font-family: inherit;
    }

    .form-control:focus {
        border-color: var(--primary_color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        transform: translateY(-1px);
    }

    .form-control.is-invalid,
    .form-control.is-in-valid {
        border-color: var(--error-color);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .invalid-feedback {
        display: block;
        color: var(--error-color);
        font-size: 0.875rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }

    input[type="submit"],
    .btn {
        width: 100%;
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, var(--success-color), #059669);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: none;
        letter-spacing: 0.025em;
        margin-top: 1rem;
    }

    input[type="submit"]:hover,
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
        background: linear-gradient(135deg, #059669, var(--success-color));
    }

    input[type="submit"]:active,
    .btn:active {
        transform: translateY(0);
    }

    a {
        color: var(--primary_color);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    a:hover {
        color: var(--primary-dark);
        text-decoration: none;
        text-shadow: 0 0 8px rgba(99, 102, 241, 0.3);
    }

    .signup-link {
        text-align: center;
        margin-top: 2rem;
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 12px;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form2 {
        animation: fadeInUp 0.6s ease-out;
    }

    /* Form field focus animations */
    .form-control::placeholder {
        color: var(--text-secondary);
        opacity: 0.7;
        transition: all 0.3s ease;
    }

    .form-control:focus::placeholder {
        opacity: 0.5;
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .wrapper {
            padding: 1rem 0.5rem;
        }
        
        form .form2 {
            padding: 2rem 1.5rem;
            margin: 1rem 0;
        }
        
        .col-md-6, .col-md-12 {
            flex: 0 0 100%;
        }
        
        .col-md-6.offset-md-3 {
            flex: 0 0 100%;
            margin-left: 0;
        }
        
        .row {
            margin: 0 -0.5rem;
        }
        
        .col-md-6, .col-md-12 {
            padding: 0 0.5rem;
        }
        
        ul.cc li a {
            padding: 1rem 0.5rem;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 576px) {
        form .form2 {
            padding: 1.5rem 1rem;
        }
        
        .form-control {
            padding: 0.875rem 1rem;
        }
        
        ul.cc li a {
            padding: 0.875rem 0.25rem;
            font-size: 0.85rem;
        }
        
        .row {
            margin: 0;
        }
        
        .col-md-6, .col-md-12 {
            padding: 0;
            margin-bottom: 1rem;
        }
    }

    /* Special styling for vendor registration */
    .vendor-title {
        text-align: center;
        margin-bottom: 2rem;
        color: var(--text-primary);
    }

    .vendor-title::before {
        content: '🏪';
        display: block;
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }

    /* Hover effects for form sections */
    .form-section {
        padding: 1rem;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .form-section:hover {
        background: rgba(99, 102, 241, 0.02);
    }
</style>

@push('css')
<style>
    span.select2.select2-container{width: 100% !important}
    .login-box, .register-box {
        width: 450px !important;
    }
    @media (max-width: 576px) {
        .login-box, .register-box {
            margin-top: .5rem;
            width: 90% !important;
        }
    }
</style>
@endpush

<br>

<div class="wrapper">
    <form class="col-md-6 offset-md-3" action="{{route('register2')}}" method="post">
        @csrf
        <ul class="cc row">
            <li class="col-6"><a href="{{route('login')}}">Login</a></li>
            <li class="col-6"><a>Vendor Register</a></li>
        </ul>
        
        <div class="form form2">
            <div class="vendor-title">
                <h3><b>Vendor Registration</b></h3>
            </div>
            
            <div class="form-section">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="name">Name <sup style="color: red;">*</sup></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter your full name" required />
                        @error('name')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="username">Username (unique) <sup style="color: red;">*</sup></label>
                        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" placeholder="Choose a unique username" required />
                        @error('username')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="email">Email <sup style="color: red;">*</sup></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email address" required />
                        @error('email')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-md-12">
                        <label for="address">Address <sup style="color: red;">*</sup></label>
                        <input type="address" name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Enter your business address" required />
                        @error('address')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-md-12">
                        <label for="password">Phone <sup style="color: red;">*</sup></label>
                        <input type="number" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter your phone number" required />
                        @error('phone')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <input type="hidden" value="12345" name="otp" id="otp" class="form-control @error('otp') is-invalid @enderror" required />
                </div>
            </div>
            
            <div class="form-section">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="password">Password <sup style="color: red;">*</sup></label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Create a strong password" required />
                        @error('password')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="confirm-password">Confirm Password <sup style="color: red;">*</sup></label>
                        <input type="password" name="password_confirmation" id="confirm-password" class="form-control" placeholder="Confirm your password" required />
                    </div>
                </div>
            </div>
            
            <input class="form-control" type="submit" value="Submit">
        </div>
    </form>
    
    <div class="signup-link">
        <span>Already have an Account? <a href="{{route('login')}}">Sign In</a></span>
    </div>
</div>

<input type="hidden" value="{{ csrf_token() }}" name="cr" id="cr">

@endsection

@push('js')
<script>
   $(document).on('click', '#otp-send', function() {
                
                var number = document.getElementById('phone').value;
                var cr = document.getElementById('cr').value;
                $.ajax({
                    type: 'POST',
                    url: 'register/send-otp',
                    data: {
                        'number': number,
                        '_token': cr,
                    },
                    dataType: "JSON",
                    success: function (response) {
                        $('#sm').html(response);
                   }
                });
            });
</script>
@endpush