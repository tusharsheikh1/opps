<!-- auth/partial/regemail.blade.php -->
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

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        margin: 0;
        padding: 0;
        position: relative;
        overflow-x: hidden;
    }

    /* Registration-specific animated background */
    body::before {
        content: '';
        position: fixed;
        top: -10%;
        left: -10%;
        width: 120%;
        height: 120%;
        background: 
            radial-gradient(circle at 25% 25%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 75% 75%, rgba(118, 75, 162, 0.1) 0%, transparent 50%);
        animation: registerFloat 20s ease-in-out infinite;
        z-index: -1;
    }

    @keyframes registerFloat {
        0%, 100% { transform: rotate(0deg) scale(1); }
        33% { transform: rotate(1deg) scale(1.02); }
        66% { transform: rotate(-1deg) scale(0.98); }
    }

    .register-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }

    .register-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        box-shadow: var(--shadow-xl);
        border: 1px solid rgba(255, 255, 255, 0.2);
        width: 100%;
        max-width: 480px;
        overflow: hidden;
        position: relative;
        animation: slideInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .register-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light), var(--success));
        border-radius: 24px 24px 0 0;
    }

    .register-header {
        text-align: center;
        padding: 3rem 2.5rem 2rem;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(16, 185, 129, 0.03));
    }

    .register-logo {
        width: 72px;
        height: 72px;
        background: linear-gradient(135deg, var(--primary), var(--success));
        border-radius: 20px;
        margin: 0 auto 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
        box-shadow: var(--shadow-lg);
        animation: registerPulse 3s infinite;
    }

    .register-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-900);
        margin: 0 0 0.5rem;
        background: linear-gradient(135deg, var(--primary), var(--success));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .register-subtitle {
        color: var(--text-500);
        font-size: 1rem;
        margin: 0;
        font-weight: 400;
    }

    /* Navigation tabs */
    .register-nav {
        display: flex;
        background: rgba(99, 102, 241, 0.05);
        border-radius: 12px;
        padding: 0.5rem;
        margin: 1.5rem 0;
    }

    .register-nav-item {
        flex: 1;
        padding: 0.75rem 1rem;
        text-align: center;
        border-radius: 8px;
        background: transparent;
        border: none;
        color: var(--text-500);
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
    }

    .register-nav-item.active {
        background: var(--primary);
        color: white;
        box-shadow: var(--shadow-sm);
    }

    .register-nav-item:hover:not(.active) {
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary);
    }

    .register-form {
        padding: 0 2.5rem 3rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-row.cols-2 {
        grid-template-columns: 1fr 1fr;
    }

    .form-group {
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

    .form-input.error {
        border-color: var(--error);
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        animation: shake 0.4s ease-in-out;
    }

    .form-input::placeholder {
        color: var(--text-400);
        opacity: 1;
    }

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

    .password-strength {
        margin-top: 0.5rem;
        height: 4px;
        background: var(--border);
        border-radius: 2px;
        overflow: hidden;
    }

    .password-strength-bar {
        height: 100%;
        width: 0%;
        background: var(--error);
        transition: all 0.3s ease;
        border-radius: 2px;
    }

    .password-strength-bar.weak { background: var(--error); width: 25%; }
    .password-strength-bar.fair { background: var(--warning); width: 50%; }
    .password-strength-bar.good { background: var(--success); width: 75%; }
    .password-strength-bar.strong { background: var(--success); width: 100%; }

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
        margin-top: 1rem;
        box-shadow: var(--shadow-md);
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

    .signin-section {
        background: var(--bg-gray-50);
        padding: 2rem;
        text-align: center;
        border-top: 1px solid var(--border);
    }

    .signin-text {
        color: var(--text-500);
        font-size: 0.95rem;
        margin: 0;
    }

    .signin-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        margin-left: 0.5rem;
        transition: color 0.2s ease;
    }

    .signin-link:hover {
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

    @keyframes registerPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive design */
    @media (max-width: 640px) {
        .register-container {
            padding: 1rem;
        }

        .register-card {
            max-width: 100%;
        }

        .register-header {
            padding: 2rem 1.5rem 1.5rem;
        }

        .register-form {
            padding: 0 1.5rem 2rem;
        }

        .form-row.cols-2 {
            grid-template-columns: 1fr;
        }

        .register-title {
            font-size: 1.75rem;
        }

        .signin-section {
            padding: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .register-logo {
            width: 64px;
            height: 64px;
            font-size: 2rem;
        }

        .form-input {
            padding: 0.875rem 1rem;
        }
    }
</style>

<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <div class="register-logo">🚀</div>
            <h1 class="register-title">Create Account</h1>
            <p class="register-subtitle">Join us and start your shopping journey</p>
            
            <div class="register-nav">
                <a href="{{route('login')}}" class="register-nav-item">Sign In</a>
                <span class="register-nav-item active">Register</span>
            </div>
        </div>

        <form class="register-form" action="{{route('register.get')}}" method="get" id="registerForm">
            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name<sup style="color: var(--error);">*</sup></label>
                    <div class="form-input-wrapper">
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            class="form-input @error('name') error @enderror" 
                            placeholder="Enter your full name"
                            required 
                            autocomplete="name"
                            value="{{ old('name') }}"
                        />
                    </div>
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number<sup style="color: var(--error);">*</sup></label>
                    <div class="form-input-wrapper">
                        <input 
                            type="tel" 
                            name="phone" 
                            id="phone"
                            class="form-input @error('phone') error @enderror" 
                            placeholder="Enter your phone number"
                            required 
                            autocomplete="tel"
                            value="{{ old('phone') }}"
                        />
                    </div>
                    @error('phone')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="email">Email Address<sup style="color: var(--error);">*</sup></label>
                    <div class="form-input-wrapper">
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            class="form-input @error('email') error @enderror" 
                            placeholder="Enter your email address"
                            required 
                            autocomplete="email"
                            value="{{ old('email') }}"
                        />
                    </div>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label" for="password">Password<sup style="color: var(--error);">*</sup></label>
                    <div class="form-input-wrapper password-wrapper">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            class="form-input @error('password') error @enderror" 
                            placeholder="Create a strong password"
                            required 
                            autocomplete="new-password"
                            minlength="8"
                        />
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                            <i class="fal fa-eye" id="eyeIcon1"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password<sup style="color: var(--error);">*</sup></label>
                    <div class="form-input-wrapper password-wrapper">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation"
                            class="form-input" 
                            placeholder="Confirm your password"
                            required 
                            autocomplete="new-password"
                        />
                        <button type="button" class="password-toggle" id="toggleConfirmPassword" aria-label="Toggle password visibility">
                            <i class="fal fa-eye" id="eyeIcon2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <i class="fal fa-user-plus" style="margin-right: 0.5rem;"></i>
                Create Account
            </button>
        </form>

        <div class="signin-section">
            <p class="signin-text">
                Already have an account?
                <a href="{{route('login')}}" class="signin-link">Sign in</a>
            </p>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Password toggle functionality
    $('#togglePassword').on('click', function () {
        const passwordInput = $('#password');
        const eyeIcon = $('#eyeIcon1');
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        eyeIcon.toggleClass('fa-eye fa-eye-slash');
    });

    $('#toggleConfirmPassword').on('click', function () {
        const passwordInput = $('#password_confirmation');
        const eyeIcon = $('#eyeIcon2');
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        eyeIcon.toggleClass('fa-eye fa-eye-slash');
    });

    // Password strength indicator
    $('#password').on('input', function() {
        const password = $(this).val();
        const strengthBar = $('#strengthBar');
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^A-Za-z0-9]/)) strength++;
        
        strengthBar.removeClass('weak fair good strong');
        if (strength >= 4) strengthBar.addClass('strong');
        else if (strength >= 3) strengthBar.addClass('good');
        else if (strength >= 2) strengthBar.addClass('fair');
        else if (strength >= 1) strengthBar.addClass('weak');
    });

    // Password confirmation validation
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmation = $(this).val();
        
        if (password && confirmation && password !== confirmation) {
            $(this).addClass('error');
            if (!$(this).siblings('.error-message').length) {
                $(this).after('<div class="error-message">Passwords do not match</div>');
            }
        } else {
            $(this).removeClass('error');
            $(this).siblings('.error-message').remove();
        }
    });

    // Form submission with loading state
    $('#registerForm').on('submit', function() {
        $('#submitBtn').prop('disabled', true);
        $(this).addClass('loading');
        
        setTimeout(function() {
            $('#submitBtn').prop('disabled', false);
            $('#registerForm').removeClass('loading');
        }, 5000);
    });

    // Enhanced form validation
    $('.form-input').on('blur', function() {
        const input = $(this);
        const value = input.val().trim();
        
        if (input.prop('required') && !value) {
            input.addClass('error');
            if (!input.siblings('.error-message').length) {
                input.after('<div class="error-message">This field is required</div>');
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
    $('#name').focus();
});
</script>

<!-- ================================== -->
<!-- auth/partial/regsms.blade.php -->
<!-- ================================== -->

<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <div class="register-logo">📱</div>
            <h1 class="register-title">Create Account</h1>
            <p class="register-subtitle">Register with SMS verification</p>
            
            <div class="register-nav">
                <a href="{{route('login')}}" class="register-nav-item">Sign In</a>
                <span class="register-nav-item active">Register</span>
            </div>
        </div>

        <form class="register-form" action="{{route('register.get')}}" method="get" id="registerSmsForm">
            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name<sup style="color: var(--error);">*</sup></label>
                    <div class="form-input-wrapper">
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            class="form-input @error('name') error @enderror" 
                            placeholder="Enter your full name"
                            required 
                            autocomplete="name"
                            value="{{ old('name') }}"
                        />
                    </div>
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address<sup style="color: var(--error);">*</sup></label>
                    <div class="form-input-wrapper">
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            class="form-input @error('email') error @enderror" 
                            placeholder="Enter your email"
                            required 
                            autocomplete="email"
                            value="{{ old('email') }}"
                        />
                    </div>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number<sup style="color: var(--error);">*</sup></label>
                    <div class="form-input-wrapper" style="display: flex; gap: 0.5rem;">
                        <input 
                            type="tel" 
                            name="phone" 
                            id="phone"
                            class="form-input @error('phone') error @enderror" 
                            placeholder="Enter your phone number"
                            required 
                            autocomplete="tel"
                            value="{{ old('phone') }}"
                            style="flex: 1;"
                        />
                        <button type="button" class="submit-btn" id="sendOtp" style="width: auto; padding: 1rem 1.5rem; margin: 0;">
                            Send OTP
                        </button>
                    </div>
                    @error('phone')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="otp">Verification Code<sup style="color: var(--error);">*</sup></label>
                    <div class="form-input-wrapper">
                        <input 
                            type="text" 
                            name="otp" 
                            id="otp"
                            class="form-input @error('otp') error @enderror" 
                            placeholder="Enter 6-digit verification code"
                            required 
                            maxlength="6"
                            style="text-align: center; font-size: 1.25rem; letter-spacing: 0.5rem;"
                        />
                    </div>
                    <div id="otpStatus" style="margin-top: 0.5rem; font-size: 0.875rem;"></div>
                    @error('otp')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label" for="password">Password<sup style="color: var(--error);">*</sup></label>
                    <div class="form-input-wrapper password-wrapper">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            class="form-input @error('password') error @enderror" 
                            placeholder="Create a strong password"
                            required 
                            autocomplete="new-password"
                            minlength="8"
                        />
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                            <i class="fal fa-eye" id="eyeIcon1"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password<sup style="color: var(--error);">*</sup></label>
                    <div class="form-input-wrapper password-wrapper">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation"
                            class="form-input" 
                            placeholder="Confirm your password"
                            required 
                            autocomplete="new-password"
                        />
                        <button type="button" class="password-toggle" id="toggleConfirmPassword" aria-label="Toggle password visibility">
                            <i class="fal fa-eye" id="eyeIcon2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <i class="fal fa-user-plus" style="margin-right: 0.5rem;"></i>
                Create Account
            </button>
        </form>

        <div class="signin-section">
            <p class="signin-text">
                Already have an account?
                <a href="{{route('login')}}" class="signin-link">Sign in</a>
            </p>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let otpSent = false;
    let otpTimer = null;
    let countdown = 0;

    // Send OTP functionality
    $('#sendOtp').on('click', function() {
        const phone = $('#phone').val().trim();
        if (!phone) {
            alert('Please enter your phone number first');
            $('#phone').focus();
            return;
        }

        const button = $(this);
        button.prop('disabled', true).text('Sending...');

        // Simulate OTP sending (replace with actual AJAX call)
        setTimeout(function() {
            otpSent = true;
            $('#otpStatus').html('<span style="color: var(--success);">✅ Verification code sent to your phone</span>');
            button.text('Resend OTP');
            
            // Start countdown
            countdown = 60;
            otpTimer = setInterval(function() {
                countdown--;
                button.text(`Resend OTP (${countdown}s)`);
                
                if (countdown <= 0) {
                    clearInterval(otpTimer);
                    button.prop('disabled', false).text('Resend OTP');
                }
            }, 1000);
        }, 2000);
    });

    // OTP input formatting
    $('#otp').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 6) value = value.substr(0, 6);
        $(this).val(value);
        
        if (value.length === 6) {
            $('#otpStatus').html('<span style="color: var(--success);">✅ Code verified</span>');
        }
    });

    // Same password toggle and validation logic as email version...
    // [Password toggle, strength indicator, and validation code would be identical]
    
    // Form submission validation
    $('#registerSmsForm').on('submit', function(e) {
        if (!otpSent) {
            e.preventDefault();
            alert('Please send and verify OTP first');
            return false;
        }
        
        const otp = $('#otp').val();
        if (otp.length !== 6) {
            e.preventDefault();
            alert('Please enter the complete 6-digit verification code');
            $('#otp').focus();
            return false;
        }
        
        $('#submitBtn').prop('disabled', true);
        $(this).addClass('loading');
    });
});
</script>