<!-- admin-otp.blade.php -->
@extends('layouts.frontend.app')

@section('title', 'Shopping Now')

@section('content')
<style>
    :root {
        --primary_color: #dc2626;
        --primary-dark: #b91c1c;
        --primary-light: #ef4444;
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
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.1) 0%, rgba(185, 28, 28, 0.1) 100%);
    }

    form .form2 {
        background: var(--bg-secondary);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: var(--shadow-xl);
        border: 1px solid rgba(220, 38, 38, 0.1);
        padding: 3rem 2.5rem;
        margin-top: 0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        text-align: center;
    }

    form .form2::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary_color), var(--primary-light), var(--primary_color));
        border-radius: 20px 20px 0 0;
    }

    form .form2:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px -12px rgba(220, 38, 38, 0.25);
    }

    .otp-header {
        margin-bottom: 2.5rem;
    }

    .otp-header::before {
        content: '🔐';
        display: block;
        font-size: 4rem;
        margin-bottom: 1rem;
        animation: pulse 2s infinite;
    }

    .otp-header h4 {
        color: var(--text-primary);
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .otp-header p {
        color: var(--text-secondary);
        font-size: 1rem;
        margin: 0;
    }

    .form-group {
        margin-bottom: 2rem;
        position: relative;
    }

    .form-group label {
        display: block;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        text-align: center;
    }

    .otp-input {
        width: 100%;
        padding: 1.5rem;
        border: 3px solid var(--border-color);
        border-radius: 16px;
        background-color: var(--bg-secondary);
        color: var(--text-primary);
        font-size: 2rem;
        font-weight: 700;
        text-align: center;
        letter-spacing: 1rem;
        transition: all 0.3s ease;
        outline: none;
        font-family: 'Courier New', monospace;
    }

    .otp-input:focus {
        border-color: var(--primary_color);
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
        transform: translateY(-2px);
    }

    .otp-input.is-invalid,
    .otp-input.is-in-valid {
        border-color: var(--error-color);
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        animation: shake 0.5s;
    }

    .invalid-feedback {
        display: block;
        color: var(--error-color);
        font-size: 0.875rem;
        margin-top: 1rem;
        font-weight: 500;
        text-align: center;
    }

    input[type="submit"] {
        width: 100%;
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, var(--primary_color), var(--primary-dark));
        color: white;
        border: none;
        border-radius: 16px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: none;
        letter-spacing: 0.025em;
        margin-top: 1.5rem;
    }

    input[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(220, 38, 38, 0.4);
        background: linear-gradient(135deg, var(--primary-dark), var(--primary_color));
    }

    input[type="submit"]:active {
        transform: translateY(0);
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    @keyframes shake {
        0% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        50% { transform: translateX(5px); }
        75% { transform: translateX(-5px); }
        100% { transform: translateX(0); }
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

    @media (max-width: 768px) {
        .wrapper {
            padding: 1rem;
        }
        
        form .form2 {
            padding: 2rem 1.5rem;
            margin: 1rem 0;
        }
        
        .otp-header::before {
            font-size: 3rem;
        }
        
        .otp-header h4 {
            font-size: 1.5rem;
        }
        
        .otp-input {
            font-size: 1.5rem;
            letter-spacing: 0.5rem;
            padding: 1.25rem;
        }
        
        .col-md-4 {
            width: 100%;
        }
        
        .offset-md-4 {
            margin-left: 0;
        }
    }

    @media (max-width: 576px) {
        form .form2 {
            padding: 1.5rem 1rem;
        }
        
        .otp-input {
            font-size: 1.25rem;
            letter-spacing: 0.25rem;
            padding: 1rem;
        }
    }
</style>

<div class="wrapper">
    <form class="col-md-4 offset-md-4" action="{{route('super.login.confirm')}}" method="post">
        @csrf
        <div class="form form2">
            <div class="otp-header">
                <h4><b>Verify OTP</b></h4>
                <p>Please enter the verification code sent to your device</p>
            </div>
            
            <div class="form-group">
                <label>Enter OTP Code<sup style="color: red;">*</sup></label>
                <input type="text" name="otp" id="otp" class="form-control otp-input @error('otp') is-in-valid @enderror" maxlength="6" placeholder="••••••" required />
                @error('otp')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>
           
            <input class="form-control" type="submit" value="Verify & Login">
        </div>
    </form>
</div>

@endsection