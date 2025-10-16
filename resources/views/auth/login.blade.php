@extends('layouts.frontend.app')

@section('title', 'Shopping Now')

@section('content')
<style>
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --primary-light: #818cf8;
        --secondary: #f1f5f9;
        --success: #10b981;
        --warning: #f59e0b;
        --error: #ef4444;
        --text-900: #0f172a;
        --text-700: #334155;
        --text-500: #64748b;
        --text-400: #94a3b8;
        --bg-white: #ffffff;
        --bg-gray-50: #f8fafc;
        --border: #e2e8f0;
        --radius: 16px;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    /* Animated background elements */
    body::before {
        content: '';
        position: fixed;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 50%);
        animation: float 20s ease-in-out infinite;
        z-index: -1;
    }

    body::after {
        content: '';
        position: fixed;
        bottom: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(118, 75, 162, 0.1) 0%, transparent 50%);
        animation: float 25s ease-in-out infinite reverse;
        z-index: -1;
    }

    @keyframes float {
        0%, 100% { transform: rotate(0deg) translate(-20px, -10px); }
        33% { transform: rotate(120deg) translate(-20px, -10px); }
        66% { transform: rotate(240deg) translate(-20px, -10px); }
    }

    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        position: relative;
    }

    .auth-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        box-shadow: var(--shadow-xl);
        border: 1px solid rgba(255, 255, 255, 0.2);
        width: 100%;
        max-width: 420px;
        overflow: hidden;
        position: relative;
        animation: slideInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .auth-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light), var(--primary));
        border-radius: 24px 24px 0 0;
    }

    .auth-header {
        text-align: center;
        padding: 3rem 2.5rem 2rem;
        position: relative;
    }

    .auth-logo {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        border-radius: 16px;
        margin: 0 auto 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        box-shadow: var(--shadow-lg);
        animation: pulse 2s infinite;
    }

    .auth-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-900);
        margin: 0 0 0.5rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .auth-subtitle {
        color: var(--text-500);
        font-size: 1rem;
        margin: 0;
        font-weight: 400;
    }

    .auth-form {
        padding: 0 2.5rem 3rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
        position: relative;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-700);
        margin-bottom: 0.75rem;
        transition: color 0.2s ease;
    }

    .form-input-wrapper {
        position: relative;
    }

    .form-input {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid var(--border);
        border-radius: var(--radius);
        font-size: 1rem;
        color: var(--text-900);
        background: var(--bg-white);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
        font-family: inherit;
    }

    .form-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        transform: translateY(-1px);
    }

    .form-input:focus + .form-label {
        color: var(--primary);
    }

    .form-input.error {
        border-color: var(--error);
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        animation: shake 0.4s ease-in-out;
    }

    .form-input::placeholder {
        color: var(--text-400);
        opacity: 1;
    }

    /* Password field with toggle */
    .password-wrapper {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-400);
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        z-index: 10;
    }

    .password-toggle:hover {
        color: var(--primary);
        background: rgba(99, 102, 241, 0.1);
    }

    .error-message {
        display: flex;
        align-items: center;
        color: var(--error);
        font-size: 0.875rem;
        margin-top: 0.5rem;
        font-weight: 500;
        opacity: 0;
        transform: translateY(-10px);
        animation: slideInError 0.3s ease forwards;
    }

    .error-message::before {
        content: '⚠️';
        margin-right: 0.5rem;
        font-size: 1rem;
    }

    .submit-btn {
        width: 100%;
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        border-radius: var(--radius);
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .submit-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
    }

    .submit-btn:hover::before {
        left: 100%;
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    .divider {
        display: flex;
        align-items: center;
        margin: 2rem 0;
        color: var(--text-400);
        font-size: 0.875rem;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    .divider span {
        margin: 0 1rem;
        background: var(--bg-white);
        padding: 0 0.5rem;
        font-weight: 500;
    }

    .auth-links {
        text-align: center;
    }

    .auth-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        position: relative;
    }

    .auth-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 50%;
        width: 0;
        height: 2px;
        background: var(--primary);
        transition: all 0.3s ease;
        transform: translateX(-50%);
    }

    .auth-link:hover {
        color: var(--primary-dark);
    }

    .auth-link:hover::after {
        width: 100%;
    }

    .signup-section {
        background: var(--bg-gray-50);
        padding: 2rem;
        text-align: center;
        border-top: 1px solid var(--border);
        margin-top: 2rem;
    }

    .signup-text {
        color: var(--text-500);
        font-size: 0.95rem;
        margin: 0;
    }

    .signup-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        margin-left: 0.5rem;
        transition: color 0.2s ease;
    }

    .signup-link:hover {
        color: var(--primary-dark);
    }

    /* Loading state */
    .loading .submit-btn {
        position: relative;
        color: transparent;
    }

    .loading .submit-btn::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    /* Animations */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(60px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInError {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-8px); }
        75% { transform: translateX(8px); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive design */
    @media (max-width: 480px) {
        .auth-container {
            padding: 1rem;
        }

        .auth-card {
            max-width: 100%;
        }

        .auth-header {
            padding: 2rem 1.5rem 1.5rem;
        }

        .auth-form {
            padding: 0 1.5rem 2rem;
        }

        .auth-title {
            font-size: 1.75rem;
        }

        .signup-section {
            padding: 1.5rem;
        }
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .auth-card {
            border: 2px solid var(--text-900);
        }
        
        .form-input:focus {
            outline: 3px solid var(--primary);
            outline-offset: 2px;
        }
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>

<?php
Session::forget('link');
 if(route('home').'/register'==url()->previous()){
      Session::put(['link' => route('home')]);
 }elseif(route('home').'/join'==url()->previous()){
      Session::put(['link' => route('home')]);
 }elseif(route('home').'/login'==url()->previous()){
      Session::put(['link' => route('home')]);
 }elseif(route('home').'/seller'==url()->previous()){
      Session::put(['link' => route('home')]);
 }
 elseif(route('home').'/password/reset'==url()->previous()){
      Session::put(['link' => route('home')]);
 }
 else{
      Session::put(['link' => url()->previous()]);
 }
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">🛍️</div>
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Sign in to your account to continue shopping</p>
        </div>

        <form class="auth-form" action="{{route('login.get')}}" method="get" id="loginForm">
            <div class="form-group">
                <label class="form-label" for="username">Username / Email / Phone<sup style="color: var(--error);">*</sup></label>
                <div class="form-input-wrapper">
                    <input 
                        type="text" 
                        name="username" 
                        id="username"
                        class="form-input @error('username') error @enderror" 
                        placeholder="Enter your username, email, or phone"
                        required 
                        autocomplete="username"
                        value="{{ old('username') }}"
                    />
                </div>
                @error('username')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password<sup style="color: var(--error);">*</sup></label>
                <div class="form-input-wrapper password-wrapper">
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        class="form-input @error('password') error @enderror" 
                        placeholder="Enter your password"
                        required 
                        autocomplete="current-password"
                    />
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                        <i class="fal fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                Sign In
            </button>

            <div class="divider">
                <span>or</span>
            </div>

            <div class="auth-links">
                @if (setting('recovrAC') == "email")
                    <a href="{{route('password.request')}}" class="auth-link">Forgot your password?</a>
                @elseif (setting('recovrAC') == "sms")
                    <a href="{{route('password.recover.mobile')}}" class="auth-link">Forgot your password?</a>
                @else
                    <a href="{{route('password.request')}}" class="auth-link">Forgot your password?</a>
                @endif
            </div>
        </form>

        <div class="signup-section">
            <p class="signup-text">
                Don't have an account?
                <a href="{{route('register')}}" class="signup-link">Create account</a>
            </p>
        </div>
    </div>
</div>

@push('js')
<script>
$(document).ready(function () {
    const togglePassword = $('#togglePassword');
    const passwordInput = $('#password');
    const eyeIcon = $('#eyeIcon');
    const loginForm = $('#loginForm');
    const submitBtn = $('#submitBtn');

    // Password toggle functionality
    togglePassword.on('click', function () {
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        
        if (type === 'text') {
            eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Form submission with loading state
    loginForm.on('submit', function() {
        submitBtn.prop('disabled', true);
        $(this).addClass('loading');
        
        // Re-enable after 5 seconds if form doesn't submit
        setTimeout(function() {
            submitBtn.prop('disabled', false);
            loginForm.removeClass('loading');
        }, 5000);
    });

    // Enhanced form validation
    $('.form-input').on('blur', function() {
        const input = $(this);
        const value = input.val().trim();
        
        if (input.prop('required') && !value) {
            input.addClass('error');
            if (!input.siblings('.error-message').length) {
                input.parent().after('<div class="error-message">This field is required</div>');
            }
        } else {
            input.removeClass('error');
            input.siblings('.error-message').remove();
        }
    });

    // Remove error state on input
    $('.form-input').on('input', function() {
        $(this).removeClass('error');
        $(this).siblings('.error-message').remove();
    });

    // Auto-focus first input
    $('#username').focus();
});
</script>
@endpush

@push('css')
<style>
    /* Additional component-specific styles */
    .form-input:autofill,
    .form-input:-webkit-autofill {
        -webkit-box-shadow: 0 0 0 30px var(--bg-white) inset !important;
        -webkit-text-fill-color: var(--text-900) !important;
    }
</style>
@endpush

@endsection