<!-- register.blade.php -->
@extends('layouts.frontend.app')
@section('title', 'Shopping Now')

@section('content')
    @if (setting('regVerify') == "sms")
        @include('auth.partial.regsms')
    @else
        @include('auth.partial.regemail')
    @endif
@endsection

@push('css')
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

    body {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        min-height: 100vh;
    }

    form .form2 {
        background: var(--bg-secondary);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: var(--shadow-xl);
        border: 1px solid rgba(99, 102, 241, 0.1);
        padding: 2.5rem;
        margin-top: 2rem;
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
        background: linear-gradient(90deg, var(--primary_color), var(--primary-light), var(--primary_color));
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

    span.select2.select2-container {
        width: 100% !important;
    }

    .login-box,
    .register-box {
        width: 450px !important;
        margin: 2rem auto;
        background: transparent;
    }

    @media (max-width: 576px) {
        .login-box,
        .register-box {
            margin-top: .5rem;
            width: 90% !important;
        }

        form .form2 {
            padding: 1.5rem;
            margin-top: 1rem;
        }
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

    /* Modern form elements */
    .form-group {
        margin-bottom: 1.5rem;
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
    }

    .form-control:focus {
        border-color: var(--primary_color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        transform: translateY(-1px);
    }

    .form-control.is-invalid {
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
        background: linear-gradient(135deg, var(--primary_color), var(--primary-dark));
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
    }

    input[type="submit"]:hover,
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(99, 102, 241, 0.4);
        background: linear-gradient(135deg, var(--primary-dark), var(--primary_color));
    }
</style>
@endpush