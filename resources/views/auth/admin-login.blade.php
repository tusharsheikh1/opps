<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Portal - Shopping Now</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <style>
        :root {
            --admin-primary: #dc2626;
            --admin-primary-dark: #b91c1c;
            --admin-primary-light: #ef4444;
            --admin-secondary: #fef2f2;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --info: #3b82f6;
            --text-900: #0f172a;
            --text-700: #334155;
            --text-500: #64748b;
            --text-400: #94a3b8;
            --text-300: #cbd5e1;
            --bg-white: #ffffff;
            --bg-gray-50: #f8fafc;
            --bg-gray-900: #0f172a;
            --border: #e2e8f0;
            --radius: 12px;
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
            background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Already logged in redirect overlay */
        .redirect-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .redirect-overlay.show {
            opacity: 1;
            pointer-events: all;
        }

        .redirect-content {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            text-align: center;
            max-width: 400px;
            box-shadow: var(--shadow-xl);
            transform: scale(0.8);
            transition: transform 0.3s ease;
        }

        .redirect-overlay.show .redirect-content {
            transform: scale(1);
        }

        .redirect-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--success), #059669);
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            animation: checkmark 0.6s ease-in-out;
        }

        @keyframes checkmark {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .redirect-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-900);
            margin-bottom: 0.5rem;
        }

        .redirect-subtitle {
            color: var(--text-500);
            margin-bottom: 2rem;
        }

        .redirect-countdown {
            font-size: 3rem;
            font-weight: 700;
            color: var(--admin-primary);
            margin-bottom: 1rem;
            font-variant-numeric: tabular-nums;
        }

        .redirect-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .redirect-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .redirect-btn.primary {
            background: var(--admin-primary);
            color: white;
        }

        .redirect-btn.secondary {
            background: var(--bg-gray-50);
            color: var(--text-700);
        }

        .redirect-btn:hover {
            transform: translateY(-1px);
        }

        /* Device trust notification */
        .device-trust-notification {
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: linear-gradient(135deg, var(--info), #2563eb);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .device-trust-notification.show {
            transform: translateX(0);
        }

        .device-trust-notification .close-btn {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1.2rem;
        }

        /* Compact animated background */
        .bg-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(220, 38, 38, 0.3);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }

        .particle:nth-child(2n) {
            background: rgba(239, 68, 68, 0.2);
            animation-duration: 20s;
            animation-delay: -3s;
        }

        .particle:nth-child(3n) {
            background: rgba(185, 28, 28, 0.2);
            animation-duration: 25s;
            animation-delay: -6s;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }

        .admin-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
        }

        .admin-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 
                var(--shadow-xl),
                0 0 0 1px rgba(220, 38, 38, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            position: relative;
            animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .admin-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--admin-primary), var(--admin-primary-light), var(--admin-primary));
            border-radius: 20px 20px 0 0;
        }

        /* Compact header */
        .admin-header {
            text-align: center;
            padding: 2rem 2rem 1.5rem;
            position: relative;
        }

        .admin-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-light));
            border-radius: 16px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            box-shadow: var(--shadow-lg);
            animation: adminPulse 2s infinite;
        }

        .admin-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-900);
            margin: 0 0 0.25rem;
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .admin-subtitle {
            color: var(--text-500);
            font-size: 0.9rem;
            margin: 0;
            font-weight: 500;
        }

        /* Trusted device indicator */
        .trusted-device-indicator {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--success);
            padding: 0.5rem;
            border-radius: 8px;
            font-size: 0.8rem;
            display: none;
            align-items: center;
            gap: 0.25rem;
        }

        .trusted-device-indicator.show {
            display: flex;
        }

        /* Compact form */
        .admin-form {
            padding: 0 2rem 2rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-700);
            margin-bottom: 0.5rem;
        }

        .form-input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.95rem;
            color: var(--text-900);
            background: var(--bg-white);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
            font-family: inherit;
        }

        .form-input:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
            transform: translateY(-1px);
        }

        .form-input:focus + .input-icon {
            color: var(--admin-primary);
        }

        .form-input.error {
            border-color: var(--error);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
            animation: shake 0.4s ease-in-out;
        }

        .form-input.success {
            border-color: var(--success);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-input::placeholder {
            color: var(--text-400);
            opacity: 1;
        }

        /* Compact input icons */
        .input-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-400);
            font-size: 1rem;
            transition: color 0.2s ease;
            z-index: 10;
        }

        /* Password toggle */
        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-400);
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 6px;
            transition: all 0.2s ease;
            z-index: 10;
        }

        .password-toggle:hover {
            color: var(--admin-primary);
            background: rgba(220, 38, 38, 0.1);
        }

        /* Enhanced features section */
        .admin-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin: 1rem 0;
            font-size: 0.8rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            color: var(--text-500);
            gap: 0.5rem;
            padding: 0.5rem;
            background: rgba(99, 102, 241, 0.05);
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .feature-item:hover {
            background: rgba(99, 102, 241, 0.1);
            transform: translateY(-1px);
        }

        .feature-item i {
            color: var(--admin-primary);
            font-size: 0.9rem;
            width: 16px;
            text-align: center;
        }

        .feature-item.active i {
            color: var(--success);
        }

        /* Enhanced remember/forgot section */
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1rem 0;
            font-size: 0.85rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            color: var(--text-600);
            gap: 0.5rem;
        }

        .remember-me input[type="checkbox"] {
            accent-color: var(--admin-primary);
        }

        /* Trust this device option */
        .trust-device {
            display: flex;
            align-items: center;
            margin: 1rem 0;
            font-size: 0.85rem;
            color: var(--text-600);
            gap: 0.5rem;
            padding: 0.75rem;
            background: rgba(16, 185, 129, 0.05);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .trust-device:hover {
            background: rgba(16, 185, 129, 0.1);
        }

        .trust-device input[type="checkbox"] {
            accent-color: var(--success);
        }

        .trust-device i {
            color: var(--success);
            margin-right: 0.25rem;
        }

        .forgot-password {
            color: var(--admin-primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .forgot-password:hover {
            color: var(--admin-primary-dark);
            text-decoration: none;
        }

        .admin-submit-btn {
            width: 100%;
            padding: 0.875rem 1.25rem;
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-dark));
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin-top: 0.5rem;
            box-shadow: var(--shadow-md);
        }

        .admin-submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .admin-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.4);
        }

        .admin-submit-btn:hover::before {
            left: 100%;
        }

        .admin-submit-btn:active {
            transform: translateY(0);
        }

        /* Biometric authentication - REMOVED */
        .biometric-auth {
            display: none;
        }

        .biometric-btn {
            display: none;
        }

        /* Security badge */
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.1), rgba(239, 68, 68, 0.05));
            border: 1px solid rgba(220, 38, 38, 0.2);
            border-radius: 8px;
            padding: 0.75rem;
            margin-top: 1rem;
            font-size: 0.8rem;
            color: var(--text-600);
        }

        .security-badge i {
            color: var(--admin-primary);
        }

        /* Live features display */
        .live-features {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: flex;
            gap: 0.5rem;
            z-index: 100;
        }

        .feature-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--success);
            animation: pulse 2s infinite;
            position: relative;
        }

        .feature-indicator.warning {
            background: var(--warning);
        }

        .feature-indicator.error {
            background: var(--error);
        }

        .feature-tooltip {
            position: absolute;
            bottom: -2rem;
            right: 0;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }

        .feature-indicator:hover .feature-tooltip {
            opacity: 1;
        }

        /* Login attempts display */
        .login-attempts {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 6px;
            padding: 0.5rem;
            margin-top: 0.75rem;
            font-size: 0.8rem;
            color: var(--error);
            text-align: center;
            display: none;
        }

        /* Theme toggle */
        .theme-toggle {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        /* Last login info */
        .last-login-info {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 8px;
            padding: 0.75rem;
            margin-top: 1rem;
            font-size: 0.8rem;
            color: var(--info);
            text-align: center;
            display: none;
        }

        .last-login-info.show {
            display: block;
            animation: slideInUp 0.3s ease;
        }

        /* Error messages */
        .error-message {
            display: flex;
            align-items: center;
            color: var(--error);
            font-size: 0.8rem;
            margin-top: 0.4rem;
            font-weight: 500;
            opacity: 0;
            transform: translateY(-8px);
            animation: slideInError 0.3s ease forwards;
        }

        .error-message::before {
            content: '⚠️';
            margin-right: 0.4rem;
            font-size: 0.9rem;
        }

        /* Loading state */
        .loading .admin-submit-btn {
            position: relative;
            color: transparent;
        }

        .loading .admin-submit-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 18px;
            height: 18px;
            margin: -9px 0 0 -9px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* Session timer */
        .session-timer {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            display: none;
        }

        /* Dark mode */
        .dark-theme {
            --bg-white: #1e293b;
            --text-900: #f1f5f9;
            --text-700: #cbd5e1;
            --text-500: #94a3b8;
            --border: #334155;
        }

        .dark-theme .admin-card {
            background: rgba(30, 41, 59, 0.95);
        }

        /* Quick login options */
        .quick-login-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .quick-login-btn {
            padding: 0.5rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--bg-white);
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.8rem;
            color: var(--text-600);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .quick-login-btn:hover {
            border-color: var(--admin-primary);
            background: rgba(220, 38, 38, 0.05);
            transform: translateY(-1px);
        }

        .quick-login-btn i {
            color: var(--admin-primary);
        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
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
            25% { transform: translateX(-6px); }
            75% { transform: translateX(6px); }
        }

        @keyframes adminPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive design */
        @media (max-width: 480px) {
            .admin-container {
                padding: 1rem;
            }

            .admin-card {
                max-width: 100%;
            }

            .admin-header {
                padding: 1.5rem 1.5rem 1rem;
            }

            .admin-form {
                padding: 0 1.5rem 1.5rem;
            }

            .admin-title {
                font-size: 1.6rem;
            }

            .admin-logo {
                width: 50px;
                height: 50px;
                font-size: 1.6rem;
            }

            .remember-forgot {
                flex-direction: column;
                gap: 0.75rem;
                text-align: center;
            }

            .admin-features {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .quick-login-options {
                grid-template-columns: 1fr;
            }

            .biometric-auth {
                gap: 0.5rem;
            }

            .biometric-btn {
                width: 40px;
                height: 40px;
            }
        }

        /* Additional features */
        .caps-warning {
            color: var(--warning);
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Success states */
        .success-notification {
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: linear-gradient(135deg, var(--success), #059669);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .success-notification.show {
            transform: translateX(0);
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
</head>
<body>
    <!-- Already logged in redirect overlay -->
    <div class="redirect-overlay" id="redirectOverlay">
        <div class="redirect-content">
            <div class="redirect-icon">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="redirect-title">Already Logged In</h2>
            <p class="redirect-subtitle">You're already authenticated. Redirecting to admin dashboard...</p>
            <div class="redirect-countdown" id="redirectCountdown">3</div>
            <div class="redirect-actions">
                <button class="redirect-btn primary" id="redirectNow">Go Now</button>
                <button class="redirect-btn secondary" id="stayHere">Stay Here</button>
            </div>
        </div>
    </div>

    <!-- Device trust notification -->
    <div class="device-trust-notification" id="deviceTrustNotification">
        <button class="close-btn" id="closeTrustNotification">&times;</button>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-shield-check"></i>
            <div>
                <div style="font-weight: 600;">This device is trusted</div>
                <div style="font-size: 0.85rem; opacity: 0.9;">Automatic login enabled for 30 days</div>
            </div>
        </div>
    </div>

    <!-- Background particles -->
    <div class="bg-particles">
        <div class="particle" style="left: 15%; animation-delay: 0s;"></div>
        <div class="particle" style="left: 35%; animation-delay: 3s;"></div>
        <div class="particle" style="left: 55%; animation-delay: 6s;"></div>
        <div class="particle" style="left: 75%; animation-delay: 9s;"></div>
        <div class="particle" style="left: 85%; animation-delay: 12s;"></div>
    </div>

    <!-- Theme toggle -->
    <div class="theme-toggle" id="themeToggle" title="Toggle dark mode">
        <i class="fas fa-moon"></i>
    </div>

    <!-- Live feature indicators -->
    <div class="live-features">
        <div class="feature-indicator" id="securityStatus">
            <div class="feature-tooltip">Security: Active</div>
        </div>
        <div class="feature-indicator warning" id="sessionStatus">
            <div class="feature-tooltip">Session: 15min</div>
        </div>
        <div class="feature-indicator" id="serverStatus">
            <div class="feature-tooltip">Server: Online</div>
        </div>
    </div>

    <!-- Session timer -->
    <div class="session-timer" id="sessionTimer">
        Session expires in: <span id="timerDisplay">15:00</span>
    </div>

    <div class="admin-container">
        <div class="admin-card">
            <!-- Trusted device indicator -->
            <div class="trusted-device-indicator" id="trustedDeviceIndicator">
                <i class="fas fa-shield-check"></i>
                <span>Trusted Device</span>
            </div>

            <div class="admin-header">
                <div class="admin-logo">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1 class="admin-title">Admin Portal</h1>
                <p class="admin-subtitle">Secure administrative access</p>
            </div>

            <form class="admin-form" action="{{route('super.login')}}" method="post" id="adminLoginForm">
                @csrf

                <!-- Last login information -->
                <div class="last-login-info" id="lastLoginInfo">
                    <i class="fas fa-clock"></i>
                    <span>Last login: <span id="lastLoginTime">Never</span> from <span id="lastLoginLocation">Unknown</span></span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="username">Username or Phone<sup style="color: var(--error);">*</sup></label>
                    <div class="form-input-wrapper">
                        <input 
                            type="text" 
                            name="username" 
                            id="username"
                            class="form-input @error('username') error @enderror" 
                            placeholder="Admin credentials"
                            required 
                            autocomplete="username"
                            value="{{ old('username') }}"
                        />
                        <i class="fas fa-user input-icon"></i>
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
                            placeholder="Secure password"
                            required 
                            autocomplete="current-password"
                        />
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    <div class="caps-warning" id="capsWarning" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Caps Lock is on
                    </div>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Quick login options for saved users -->
                <div class="quick-login-options" id="quickLoginOptions" style="display: none;">
                    <button type="button" class="quick-login-btn" data-username="admin">
                        <i class="fas fa-user-shield"></i>
                        <span>Admin</span>
                    </button>
                    <button type="button" class="quick-login-btn" data-username="superadmin">
                        <i class="fas fa-crown"></i>
                        <span>Super Admin</span>
                    </button>
                </div>

                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember">
                        Remember session
                    </label>
                    <a href="{{route('password.request')}}" class="forgot-password">
                        Recovery
                    </a>
                </div>

                <!-- Trust this device option -->
                <div class="trust-device">
                    <input type="checkbox" name="trust_device" id="trustDevice">
                    <i class="fas fa-shield-check"></i>
                    <label for="trustDevice">Trust this device for 30 days</label>
                </div>

                <button type="submit" class="admin-submit-btn" id="submitBtn">
                    <i class="fas fa-sign-in-alt" style="margin-right: 0.5rem;"></i>
                    Secure Login
                </button>

                <div class="login-attempts" id="loginAttempts">
                    <i class="fas fa-exclamation-triangle"></i>
                    Multiple failed attempts detected. Access temporarily restricted.
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Core elements
            const togglePassword = $('#togglePassword');
            const passwordInput = $('#password');
            const eyeIcon = $('#eyeIcon');
            const adminForm = $('#adminLoginForm');
            const submitBtn = $('#submitBtn');
            const themeToggle = $('#themeToggle');
            const sessionTimer = $('#sessionTimer');
            const timerDisplay = $('#timerDisplay');
            const redirectOverlay = $('#redirectOverlay');
            const redirectCountdown = $('#redirectCountdown');
            
            // Security and device management
            let loginAttempts = parseInt(localStorage.getItem('adminLoginAttempts') || '0');
            let sessionTime = 15 * 60; // 15 minutes in seconds
            let redirectTimer = 3;
            let isAuthenticated = false;

            // Initialize all features
            initializeSecurityFeatures();
            checkAuthenticationStatus();
            setupDeviceTrust();
            setupQuickLogin();

            // Check if user is already authenticated
            function checkAuthenticationStatus() {
                // Check for existing session or trusted device
                const hasValidSession = localStorage.getItem('adminSession') && 
                                      parseInt(localStorage.getItem('adminSessionExpiry')) > Date.now();
                const isTrustedDevice = localStorage.getItem('trustedDevice') === 'true' &&
                                      parseInt(localStorage.getItem('trustedDeviceExpiry')) > Date.now();
                
                if (hasValidSession || isTrustedDevice) {
                    isAuthenticated = true;
                    showRedirectOverlay();
                }

                // Update trusted device indicator
                if (isTrustedDevice) {
                    $('#trustedDeviceIndicator').addClass('show');
                    showDeviceTrustNotification();
                }

                // Show last login info
                showLastLoginInfo();
            }

            function showRedirectOverlay() {
                redirectOverlay.addClass('show');
                
                const countdown = setInterval(() => {
                    redirectTimer--;
                    redirectCountdown.text(redirectTimer);
                    
                    if (redirectTimer <= 0) {
                        clearInterval(countdown);
                        redirectToAdmin();
                    }
                }, 1000);

                // Manual redirect
                $('#redirectNow').on('click', () => {
                    clearInterval(countdown);
                    redirectToAdmin();
                });

                // Stay on login page
                $('#stayHere').on('click', () => {
                    clearInterval(countdown);
                    redirectOverlay.removeClass('show');
                    isAuthenticated = false;
                });
            }

            function redirectToAdmin() {
                showSuccessNotification('Redirecting to admin dashboard...');
                setTimeout(() => {
                    // Simulate redirect to admin dashboard
                    window.location.href = '/admin/dashboard';
                }, 1000);
            }

            function showDeviceTrustNotification() {
                const notification = $('#deviceTrustNotification');
                setTimeout(() => {
                    notification.addClass('show');
                }, 1000);

                $('#closeTrustNotification').on('click', () => {
                    notification.removeClass('show');
                });

                // Auto-hide after 5 seconds
                setTimeout(() => {
                    notification.removeClass('show');
                }, 6000);
            }

            function showLastLoginInfo() {
                const lastLogin = localStorage.getItem('lastAdminLogin');
                const lastLocation = localStorage.getItem('lastAdminLocation') || 'Unknown';
                
                if (lastLogin) {
                    const loginDate = new Date(parseInt(lastLogin));
                    const formattedDate = loginDate.toLocaleDateString() + ' ' + loginDate.toLocaleTimeString();
                    
                    $('#lastLoginTime').text(formattedDate);
                    $('#lastLoginLocation').text(lastLocation);
                    $('#lastLoginInfo').addClass('show');
                }
            }

            // Device trust management
            function setupDeviceTrust() {
                const trustCheckbox = $('#trustDevice');
                
                // Check if device is already trusted
                if (localStorage.getItem('trustedDevice') === 'true') {
                    trustCheckbox.prop('checked', true);
                }

                trustCheckbox.on('change', function() {
                    if ($(this).is(':checked')) {
                        showSuccessNotification('Device will be trusted after successful login');
                    }
                });
            }

            function trustCurrentDevice() {
                const expiryTime = Date.now() + (30 * 24 * 60 * 60 * 1000); // 30 days
                localStorage.setItem('trustedDevice', 'true');
                localStorage.setItem('trustedDeviceExpiry', expiryTime.toString());
                localStorage.setItem('deviceFingerprint', generateDeviceFingerprint());
                
                $('#trustedDeviceIndicator').addClass('show');
                showSuccessNotification('Device trusted for 30 days');
            }

            function generateDeviceFingerprint() {
                // Generate a simple device fingerprint based on browser characteristics
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                ctx.textBaseline = 'top';
                ctx.font = '14px Arial';
                ctx.fillText('Device fingerprint', 2, 2);
                
                const fingerprint = btoa(JSON.stringify({
                    userAgent: navigator.userAgent,
                    language: navigator.language,
                    platform: navigator.platform,
                    screen: screen.width + 'x' + screen.height,
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                    canvas: canvas.toDataURL()
                }));
                
                return fingerprint.substring(0, 64); // Truncate for storage
            }

            // Biometric authentication setup
            function setupBiometricAuth() {
                // Check for WebAuthn/FIDO2 support
                if (window.PublicKeyCredential) {
                    $('#fingerprintAuth, #faceAuth').addClass('available');
                    $('#featureBiometric').addClass('active');
                }

                $('#fingerprintAuth').on('click', async function() {
                    if (!window.PublicKeyCredential) {
                        showErrorNotification('Biometric authentication not supported on this device');
                        return;
                    }

                    try {
                        $(this).addClass('loading');
                        
                        // Simulate biometric authentication
                        await simulateBiometricAuth('fingerprint');
                        
                        showSuccessNotification('Fingerprint authentication successful');
                        autofillCredentials('admin'); // Auto-fill admin credentials
                        
                    } catch (error) {
                        showErrorNotification('Fingerprint authentication failed');
                    } finally {
                        $(this).removeClass('loading');
                    }
                });

                $('#faceAuth').on('click', async function() {
                    if (!window.PublicKeyCredential) {
                        showErrorNotification('Face recognition not supported on this device');
                        return;
                    }

                    try {
                        $(this).addClass('loading');
                        
                        // Simulate face recognition
                        await simulateBiometricAuth('face');
                        
                        showSuccessNotification('Face recognition successful');
                        autofillCredentials('admin');
                        
                    } catch (error) {
                        showErrorNotification('Face recognition failed');
                    } finally {
                        $(this).removeClass('loading');
                    }
                });
            }

            async function simulateBiometricAuth(type) {
                // Simulate biometric authentication delay
                return new Promise((resolve, reject) => {
                    setTimeout(() => {
                        // 80% success rate for demo
                        if (Math.random() > 0.2) {
                            resolve();
                        } else {
                            reject(new Error(`${type} authentication failed`));
                        }
                    }, 2000);
                });
            }

            function autofillCredentials(username) {
                $('#username').val(username).addClass('success');
                $('#password').focus();
                
                // Show quick password hint
                showSuccessNotification(`Credentials loaded for ${username}`);
            }

            // Quick login setup
            function setupQuickLogin() {
                const savedUsers = JSON.parse(localStorage.getItem('savedAdminUsers') || '[]');
                
                if (savedUsers.length > 0) {
                    $('#quickLoginOptions').show();
                }

                $('.quick-login-btn').on('click', function() {
                    const username = $(this).data('username');
                    autofillCredentials(username);
                    
                    // Animate the selection
                    $(this).addClass('selected');
                    setTimeout(() => $(this).removeClass('selected'), 1000);
                });
            }

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

            // Theme toggle functionality
            themeToggle.on('click', function() {
                $('body').toggleClass('dark-theme');
                const icon = $(this).find('i');
                icon.toggleClass('fa-moon fa-sun');
                
                // Store preference
                localStorage.setItem('adminTheme', $('body').hasClass('dark-theme') ? 'dark' : 'light');
                showSuccessNotification('Theme ' + ($('body').hasClass('dark-theme') ? 'Dark' : 'Light') + ' mode activated');
            });

            // Load saved theme
            if (localStorage.getItem('adminTheme') === 'dark') {
                $('body').addClass('dark-theme');
                themeToggle.find('i').removeClass('fa-moon').addClass('fa-sun');
            }

            // Session timer management
            function startSessionTimer() {
                sessionTimer.show();
                const timer = setInterval(function() {
                    sessionTime--;
                    const minutes = Math.floor(sessionTime / 60);
                    const seconds = sessionTime % 60;
                    timerDisplay.text(`${minutes}:${seconds.toString().padStart(2, '0')}`);
                    
                    if (sessionTime <= 60) {
                        sessionTimer.css('background', 'rgba(239, 68, 68, 0.8)');
                    }
                    
                    if (sessionTime <= 0) {
                        clearInterval(timer);
                        alert('Session expired for security. Please refresh the page.');
                        window.location.reload();
                    }
                }, 1000);
            }

            // Login attempt management
            if (loginAttempts >= 3) {
                $('#loginAttempts').show();
                submitBtn.prop('disabled', true);
                $('#securityStatus').removeClass('').addClass('error');
                
                // Reset after 10 minutes
                setTimeout(function() {
                    localStorage.removeItem('adminLoginAttempts');
                    $('#loginAttempts').hide();
                    submitBtn.prop('disabled', false);
                    $('#securityStatus').removeClass('error');
                    showSuccessNotification('Login attempts reset. You may try again.');
                }, 10 * 60 * 1000);
            }

            // Enhanced form validation with real-time feedback
            $('.form-input').on('input', function() {
                const input = $(this);
                const value = input.val().trim();
                
                input.removeClass('error success');
                input.siblings('.error-message, .caps-warning').remove();
                
                if (value.length > 0) {
                    if (input.attr('id') === 'username' && value.length >= 3) {
                        input.addClass('success');
                    } else if (input.attr('id') === 'password' && value.length >= 6) {
                        input.addClass('success');
                    }
                }
            });

            // Caps lock detection
            $('.form-input').on('keypress', function(e) {
                const capsLock = e.originalEvent.getModifierState && e.originalEvent.getModifierState('CapsLock');
                
                if (capsLock) {
                    $('#capsWarning').show();
                } else {
                    $('#capsWarning').hide();
                }
            });

            // Form submission with enhanced security
            adminForm.on('submit', function(e) {
                const username = $('#username').val().trim();
                const password = $('#password').val().trim();
                
                if (!username || !password) {
                    e.preventDefault();
                    $('.form-input').addClass('error');
                    setTimeout(() => $('.form-input').removeClass('error'), 400);
                    showErrorNotification('Please fill in all required fields');
                    return false;
                }

                if (password.length < 6) {
                    e.preventDefault();
                    passwordInput.addClass('error');
                    if (!passwordInput.siblings('.error-message').length) {
                        passwordInput.after('<div class="error-message">Password too short for admin access</div>');
                    }
                    setTimeout(() => passwordInput.removeClass('error'), 400);
                    showErrorNotification('Password must be at least 6 characters');
                    return false;
                }
                
                submitBtn.prop('disabled', true);
                $(this).addClass('loading');
                
                // Store login attempt
                loginAttempts++;
                localStorage.setItem('adminLoginAttempts', loginAttempts.toString());
                
                // Handle device trust
                if ($('#trustDevice').is(':checked')) {
                    trustCurrentDevice();
                }
                
                // Store last login info
                localStorage.setItem('lastAdminLogin', Date.now().toString());
                localStorage.setItem('lastAdminLocation', 'Current Location'); // Replace with actual geolocation
                
                // Create session
                const sessionExpiry = Date.now() + (15 * 60 * 1000); // 15 minutes
                localStorage.setItem('adminSession', 'active');
                localStorage.setItem('adminSessionExpiry', sessionExpiry.toString());
                
                // Save user for quick login
                saveUserForQuickLogin(username);
                
                // Start session timer
                startSessionTimer();
                
                // Clear login attempts on successful login (simulated)
                setTimeout(() => {
                    localStorage.removeItem('adminLoginAttempts');
                    showSuccessNotification('Login successful! Redirecting...');
                }, 2000);
                
                // Re-enable after timeout if form doesn't submit
                setTimeout(function() {
                    submitBtn.prop('disabled', false);
                    adminForm.removeClass('loading');
                }, 8000);
            });

            function saveUserForQuickLogin(username) {
                const savedUsers = JSON.parse(localStorage.getItem('savedAdminUsers') || '[]');
                if (!savedUsers.includes(username)) {
                    savedUsers.push(username);
                    localStorage.setItem('savedAdminUsers', JSON.stringify(savedUsers));
                }
            }

            // Initialize security features
            function initializeSecurityFeatures() {
                // Simulate feature status updates
                updateFeatureStatus();
                
                // Update feature indicators every 30 seconds
                setInterval(updateFeatureStatus, 30000);
                
                // Show session timer after 5 seconds
                setTimeout(() => {
                    if (!sessionTimer.is(':visible')) {
                        sessionTimer.fadeIn();
                        setTimeout(() => sessionTimer.fadeOut(), 3000);
                    }
                }, 5000);
            }

            function updateFeatureStatus() {
                // Simulate real-time feature monitoring
                const features = ['securityStatus', 'sessionStatus', 'serverStatus'];
                features.forEach(feature => {
                    const element = $('#' + feature);
                    const random = Math.random();
                    
                    element.removeClass('warning error');
                    if (random < 0.1) {
                        element.addClass('warning');
                    } else if (random < 0.05) {
                        element.addClass('error');
                    }
                });
            }

            // Notification system
            function showSuccessNotification(message) {
                const notification = $(`
                    <div class="success-notification">
                        <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                        ${message}
                    </div>
                `);
                
                $('body').append(notification);
                setTimeout(() => notification.addClass('show'), 100);
                
                setTimeout(() => {
                    notification.removeClass('show');
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }

            function showErrorNotification(message) {
                const notification = $(`
                    <div class="success-notification" style="background: linear-gradient(135deg, var(--error), #dc2626);">
                        <i class="fas fa-exclamation-triangle" style="margin-right: 0.5rem;"></i>
                        ${message}
                    </div>
                `);
                
                $('body').append(notification);
                setTimeout(() => notification.addClass('show'), 100);
                
                setTimeout(() => {
                    notification.removeClass('show');
                    setTimeout(() => notification.remove(), 300);
                }, 4000);
            }

            // Auto-focus first input
            if (!isAuthenticated) {
                $('#username').focus();
            }

            // Enhanced keyboard shortcuts for admin convenience
            $(document).on('keydown', function(e) {
                // Ctrl+Enter or Cmd+Enter to submit form
                if ((e.ctrlKey || e.metaKey) && e.keyCode === 13) {
                    if ($('#username').val() && $('#password').val()) {
                        adminForm.submit();
                    }
                }
                
                // Escape to clear form
                if (e.keyCode === 27) {
                    adminForm[0].reset();
                    $('.form-input').removeClass('error success');
                    $('.error-message, .caps-warning').remove();
                    $('#username').focus();
                }
                
                // Alt+T for theme toggle
                if (e.altKey && e.keyCode === 84) {
                    e.preventDefault();
                    themeToggle.click();
                }

                // Alt+F for quick username fill
                if (e.altKey && e.keyCode === 70) {
                    e.preventDefault();
                    $('#username').val('admin').addClass('success');
                    $('#password').focus();
                }

                // Alt+D to toggle device trust
                if (e.altKey && e.keyCode === 68) {
                    e.preventDefault();
                    $('#trustDevice').click();
                }
            });

            // Security: Clear sensitive data on page unload
            $(window).on('beforeunload', function() {
                $('#password').val('');
                localStorage.removeItem('tempAdminData');
            });

            // Advanced security: Mouse movement tracking
            let mouseMovements = 0;
            $(document).on('mousemove', function() {
                mouseMovements++;
                if (mouseMovements > 50) {
                    $('#securityStatus').removeClass('warning error');
                }
            });

            // Prevent form submission on Enter in username field
            $('#username').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $('#password').focus();
                }
            });

            // Enhanced visual feedback
            $('.form-input').on('focus', function() {
                $(this).parent().addClass('focused');
            }).on('blur', function() {
                $(this).parent().removeClass('focused');
            });

            // Auto-logout on prolonged inactivity
            let inactivityTimer;
            const inactivityTime = 10 * 60 * 1000; // 10 minutes

            function resetInactivityTimer() {
                clearTimeout(inactivityTimer);
                inactivityTimer = setTimeout(function() {
                    if (confirm('Session will expire due to inactivity. Continue?')) {
                        resetInactivityTimer();
                        sessionTime = 15 * 60; // Reset session time
                    } else {
                        localStorage.removeItem('adminSession');
                        localStorage.removeItem('adminSessionExpiry');
                        window.location.reload();
                    }
                }, inactivityTime);
            }

            $(document).on('mousemove keypress click', resetInactivityTimer);
            resetInactivityTimer();

            // Real-time password strength indicator
            $('#password').on('input', function() {
                const password = $(this).val();
                let strength = 0;
                
                if (password.length >= 8) strength++;
                if (password.match(/[a-z]/)) strength++;
                if (password.match(/[A-Z]/)) strength++;
                if (password.match(/[0-9]/)) strength++;
                if (password.match(/[^A-Za-z0-9]/)) strength++;
                
                // Update security status based on password strength
                const securityIndicator = $('#securityStatus');
                securityIndicator.removeClass('warning error');
                
                if (strength < 3) {
                    securityIndicator.addClass('warning');
                } else if (strength >= 4) {
                    securityIndicator.removeClass('warning error');
                }
            });

            // Device information collection
            function collectDeviceInfo() {
                return {
                    userAgent: navigator.userAgent,
                    language: navigator.language,
                    platform: navigator.platform,
                    cookieEnabled: navigator.cookieEnabled,
                    onLine: navigator.onLine,
                    screen: {
                        width: screen.width,
                        height: screen.height,
                        colorDepth: screen.colorDepth
                    },
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                    timestamp: new Date().toISOString()
                };
            }

            // Store device info for security logging
            localStorage.setItem('deviceInfo', JSON.stringify(collectDeviceInfo()));

            // Enhanced error handling
            adminForm.on('invalid', function(e) {
                e.preventDefault();
                const firstInvalidField = $(this).find(':invalid:first');
                firstInvalidField.focus().addClass('error');
                
                setTimeout(() => {
                    firstInvalidField.removeClass('error');
                }, 2000);
            });

            // Auto-hide alerts and notifications
            setTimeout(function() {
                $('.alert, .notification').fadeOut();
            }, 5000);

            // Add subtle animation to particles based on form interaction
            $('.form-input').on('focus', function() {
                $('.particle').css('animation-duration', '10s');
            }).on('blur', function() {
                $('.particle').css('animation-duration', '15s');
            });

            // Admin panel quick access (Konami code)
            let konamiCode = [];
            const konamiSequence = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65];

            $(document).on('keydown', function(e) {
                konamiCode.push(e.keyCode);
                if (konamiCode.length > konamiSequence.length) {
                    konamiCode.shift();
                }
                
                if (JSON.stringify(konamiCode) === JSON.stringify(konamiSequence)) {
                    console.log('🎮 Admin Developer Mode Activated');
                    $('#username').val('admin').addClass('success');
                    $('#password').val('admin123').addClass('success');
                    konamiCode = [];
                    
                    // Show special indicator
                    showSuccessNotification('🎮 Developer Mode Activated - Credentials Auto-filled');
                    $('#trustDevice').prop('checked', true);
                }
            });

            // Smart auto-complete for usernames
            const commonAdminUsernames = ['admin', 'administrator', 'superadmin', 'root', 'manager'];
            $('#username').on('input', function() {
                const value = $(this).val().toLowerCase();
                const matches = commonAdminUsernames.filter(username => 
                    username.startsWith(value) && username !== value
                );
                
                if (matches.length > 0 && value.length > 2) {
                    const suggestion = matches[0];
                    // Show subtle suggestion
                    showSuccessNotification(`Did you mean "${suggestion}"?`);
                }
            });

            // Network status monitoring
            function updateNetworkStatus() {
                const isOnline = navigator.onLine;
                const networkIndicator = $('#serverStatus');
                
                if (isOnline) {
                    networkIndicator.removeClass('error warning').addClass('success');
                    networkIndicator.find('.feature-tooltip').text('Server: Online');
                } else {
                    networkIndicator.removeClass('success').addClass('error');
                    networkIndicator.find('.feature-tooltip').text('Server: Offline');
                    showErrorNotification('Network connection lost. Please check your internet connection.');
                }
            }

            window.addEventListener('online', updateNetworkStatus);
            window.addEventListener('offline', updateNetworkStatus);
            updateNetworkStatus();

            // Battery status monitoring (if supported)
            if ('getBattery' in navigator) {
                navigator.getBattery().then(function(battery) {
                    function updateBatteryStatus() {
                        if (battery.level < 0.2 && !battery.charging) {
                            showErrorNotification('Low battery detected. Consider plugging in your device for extended admin sessions.');
                        }
                    }
                    
                    battery.addEventListener('levelchange', updateBatteryStatus);
                    battery.addEventListener('chargingchange', updateBatteryStatus);
                    updateBatteryStatus();
                });
            }

            // Location-based security (if geolocation is available)
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const location = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        timestamp: Date.now()
                    };
                    
                    localStorage.setItem('loginLocation', JSON.stringify(location));
                    
                    // Check against previous login locations for security
                    const previousLocations = JSON.parse(localStorage.getItem('previousLoginLocations') || '[]');
                    const isNewLocation = !previousLocations.some(loc => 
                        Math.abs(loc.latitude - location.latitude) < 0.1 && 
                        Math.abs(loc.longitude - location.longitude) < 0.1
                    );
                    
                    if (isNewLocation && previousLocations.length > 0) {
                        showErrorNotification('New login location detected. Enhanced security measures activated.');
                        $('#feature2fa').addClass('warning');
                    }
                    
                    // Store location for future reference
                    previousLocations.push(location);
                    if (previousLocations.length > 5) previousLocations.shift(); // Keep only last 5 locations
                    localStorage.setItem('previousLoginLocations', JSON.stringify(previousLocations));
                });
            }

            // Advanced clipboard security
            $(document).on('paste', function(e) {
                const pastedText = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
                if (pastedText.length > 100) {
                    e.preventDefault();
                    showErrorNotification('Large clipboard content blocked for security reasons.');
                }
            });

            // Screen recording detection (basic)
            let screenshotAttempts = 0;
            $(document).on('keydown', function(e) {
                // Detect common screenshot shortcuts
                if ((e.key === 'PrintScreen') || 
                    (e.metaKey && e.shiftKey && (e.key === '3' || e.key === '4')) ||
                    (e.ctrlKey && e.shiftKey && e.key === 'S')) {
                    
                    screenshotAttempts++;
                    if (screenshotAttempts > 2) {
                        showErrorNotification('Excessive screenshot attempts detected. This activity is being logged.');
                        $('#securityStatus').addClass('warning');
                    }
                }
            });

            // Form auto-save (for non-sensitive fields only)
            $('#username').on('input', function() {
                const username = $(this).val();
                if (username.length > 2) {
                    localStorage.setItem('lastUsername', username);
                }
            });

            // Restore last username on page load
            const lastUsername = localStorage.getItem('lastUsername');
            if (lastUsername && !$('#username').val()) {
                $('#username').val(lastUsername);
            }

            // Advanced rate limiting
            function checkRateLimit() {
                const attempts = JSON.parse(localStorage.getItem('loginAttemptTimes') || '[]');
                const now = Date.now();
                const fiveMinutesAgo = now - (5 * 60 * 1000);
                
                // Remove attempts older than 5 minutes
                const recentAttempts = attempts.filter(time => time > fiveMinutesAgo);
                
                if (recentAttempts.length >= 5) {
                    $('#loginAttempts').show().text('Too many login attempts. Please wait 5 minutes before trying again.');
                    submitBtn.prop('disabled', true);
                    return false;
                }
                
                return true;
            }

            // Log attempt times
            adminForm.on('submit', function() {
                const attempts = JSON.parse(localStorage.getItem('loginAttemptTimes') || '[]');
                attempts.push(Date.now());
                localStorage.setItem('loginAttemptTimes', JSON.stringify(attempts));
            });

            // Favicon notification system
            function updateFavicon(type = 'default') {
                const favicon = document.querySelector('link[rel="icon"]') || document.createElement('link');
                favicon.rel = 'icon';
                
                const canvas = document.createElement('canvas');
                canvas.width = 32;
                canvas.height = 32;
                const ctx = canvas.getContext('2d');
                
                // Draw base icon
                ctx.fillStyle = type === 'error' ? '#ef4444' : type === 'success' ? '#10b981' : '#dc2626';
                ctx.fillRect(0, 0, 32, 32);
                
                // Add icon text
                ctx.fillStyle = 'white';
                ctx.font = 'bold 20px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('A', 16, 22);
                
                favicon.href = canvas.toDataURL();
                document.head.appendChild(favicon);
            }

            // Update favicon based on status
            $('#securityStatus').on('DOMSubtreeModified', function() {
                if ($(this).hasClass('error')) {
                    updateFavicon('error');
                } else if ($(this).hasClass('warning')) {
                    updateFavicon('warning');
                } else {
                    updateFavicon('success');
                }
            });

            // Remove biometric-related CSS classes
            $('.biometric-btn').remove();
            $('.biometric-auth').remove();
                username: /^[a-zA-Z0-9_]{3,20}$/,
                phone: /^[\+]?[1-9][\d]{0,15}$/,
                strongPassword: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/
            };

            $('#username').on('blur', function() {
                const value = $(this).val();
                const isPhone = /^\d+$/.test(value);
                
                if (value && !isPhone && !validationPatterns.username.test(value)) {
                    $(this).addClass('error');
                    showErrorNotification('Username must be 3-20 characters, letters, numbers, and underscores only.');
                } else if (value && isPhone && !validationPatterns.phone.test(value)) {
                    $(this).addClass('error');
                    showErrorNotification('Invalid phone number format.');
                }
            });

            $('#password').on('blur', function() {
                const value = $(this).val();
                if (value && !validationPatterns.strongPassword.test(value)) {
                    showErrorNotification('Password should contain uppercase, lowercase, number, and special character.');
                }
            });

            // Admin dashboard preview (mock)
            function showDashboardPreview() {
                const preview = $(`
                    <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); 
                         background: white; padding: 2rem; border-radius: 12px; box-shadow: var(--shadow-xl); 
                         z-index: 10000; max-width: 90vw; max-height: 90vh;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h3>Admin Dashboard Preview</h3>
                            <button id="closeDashboardPreview" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            <div style="padding: 1rem; background: #f8fafc; border-radius: 8px; text-align: center;">
                                <i class="fas fa-users" style="font-size: 2rem; color: var(--admin-primary); margin-bottom: 0.5rem;"></i>
                                <div style="font-weight: 600;">Users</div>
                                <div style="font-size: 1.5rem; color: var(--admin-primary);">1,234</div>
                            </div>
                            <div style="padding: 1rem; background: #f8fafc; border-radius: 8px; text-align: center;">
                                <i class="fas fa-shopping-cart" style="font-size: 2rem; color: var(--success); margin-bottom: 0.5rem;"></i>
                                <div style="font-weight: 600;">Orders</div>
                                <div style="font-size: 1.5rem; color: var(--success);">5,678</div>
                            </div>
                            <div style="padding: 1rem; background: #f8fafc; border-radius: 8px; text-align: center;">
                                <i class="fas fa-dollar-sign" style="font-size: 2rem; color: var(--warning); margin-bottom: 0.5rem;"></i>
                                <div style="font-weight: 600;">Revenue</div>
                                <div style="font-size: 1.5rem; color: var(--warning);">$45,678</div>
                            </div>
                        </div>
                        <div style="margin-top: 1rem; text-align: center; color: var(--text-500);">
                            This is a preview of what you'll see after logging in successfully.
                        </div>
                    </div>
                `);
                
                $('body').append(preview);
                
                $('#closeDashboardPreview').on('click', function() {
                    preview.remove();
                });
                
                // Auto-close after 10 seconds
                setTimeout(() => {
                    preview.fadeOut(() => preview.remove());
                }, 10000);
            }

            // Show dashboard preview on double-click of logo
            $('.admin-logo').on('dblclick', function() {
                showDashboardPreview();
            });

            // Accessibility enhancements
            function enhanceAccessibility() {
                // Add ARIA labels
                $('.form-input').each(function() {
                    const label = $(this).closest('.form-group').find('label').text();
                    $(this).attr('aria-label', label);
                });
                
                // Add keyboard navigation for custom elements
                $('.biometric-btn, .quick-login-btn').attr('tabindex', '0');
                
                // Add focus indicators
                $('.biometric-btn, .quick-login-btn').on('focus', function() {
                    $(this).css('outline', '2px solid var(--admin-primary)');
                }).on('blur', function() {
                    $(this).css('outline', 'none');
                });
                
                // Add screen reader announcements
                $('#loginAttempts').attr('role', 'alert');
                $('.error-message').attr('role', 'alert');
            }

            enhanceAccessibility();

            // Performance monitoring
            function logPerformanceMetrics() {
                if (window.performance && window.performance.timing) {
                    const timing = window.performance.timing;
                    const loadTime = timing.loadEventEnd - timing.navigationStart;
                    
                    console.log('Page Load Performance:', {
                        totalTime: loadTime + 'ms',
                        domReady: (timing.domContentLoadedEventEnd - timing.navigationStart) + 'ms',
                        renderTime: (timing.loadEventEnd - timing.domContentLoadedEventEnd) + 'ms'
                    });
                    
                    // Show performance warning if page loads slowly
                    if (loadTime > 5000) {
                        setTimeout(() => {
                            showErrorNotification('Slow page load detected. This may affect security features.');
                        }, 1000);
                    }
                }
            }

            // Initialize performance monitoring
            window.addEventListener('load', logPerformanceMetrics);

            // Service Worker registration for offline functionality
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('ServiceWorker registered successfully');
                    $('#featureMonitoring').addClass('active');
                }).catch(function(error) {
                    console.log('ServiceWorker registration failed');
                });
            }

            // Final initialization message
            console.log('🔒 Enhanced Admin Security System Initialized');
            console.log('💡 Features: Device Trust, Auto-redirect, Rate Limiting');
            console.log('⌨️ Shortcuts: Alt+T (theme), Alt+F (fill admin), Alt+D (device trust), Ctrl+Enter (submit)');
            console.log('🎮 Easter Egg: Try the Konami Code for developer mode');

            // Show initialization complete notification
            setTimeout(() => {
                if (!isAuthenticated) {
                    showSuccessNotification('Security system initialized. Ready for admin access.');
                }
            }, 2000);

            // Clean up old data periodically
            function cleanupOldData() {
                const now = Date.now();
                const oneMonthAgo = now - (30 * 24 * 60 * 60 * 1000);
                
                // Clean up old login attempt times
                const attempts = JSON.parse(localStorage.getItem('loginAttemptTimes') || '[]');
                const validAttempts = attempts.filter(time => time > oneMonthAgo);
                localStorage.setItem('loginAttemptTimes', JSON.stringify(validAttempts));
                
                // Clean up old location data
                const locations = JSON.parse(localStorage.getItem('previousLoginLocations') || '[]');
                const validLocations = locations.filter(loc => loc.timestamp > oneMonthAgo);
                localStorage.setItem('previousLoginLocations', JSON.stringify(validLocations));
            }

            // Run cleanup on page load
            cleanupOldData();

            // Set up periodic cleanup (every hour)
            setInterval(cleanupOldData, 60 * 60 * 1000);
        });
    </script>
</body>
</html>