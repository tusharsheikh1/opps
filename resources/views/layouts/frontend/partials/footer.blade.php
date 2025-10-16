{{-- Modern Footer for Book Selling E-commerce - White Theme --}}
<style>
    .fixed-cart {
        width: 60px;
        height: 60px;
        text-align: center;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        position: fixed;
        bottom: 120px;
        right: 20px;
        z-index: 999;
        box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
        transition: all 0.3s ease;
        font-size: 14px;
        font-weight: 600;
    }

    .fixed-cart:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(79, 70, 229, 0.4);
    }

    .fixed_what {
        position: fixed !important;
        list-style: none;
        right: 20px;
        bottom: 20px;
        z-index: 999999;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .fixed_what a {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        box-shadow: 0 8px 30px rgba(37, 211, 102, 0.4);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .fixed_what a:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 40px rgba(37, 211, 102, 0.5);
    }

    /* WhatsApp Text Bubble */
    .whatsapp-text {
        background: #ffffff;
        color: #1f2937;
        padding: 12px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        position: relative;
        max-width: 200px;
        line-height: 1.4;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        border: 1px solid #e5e7eb;
        opacity: 0;
        transform: translateX(10px) scale(0.8);
        animation: whatsappTextCycle 20s infinite;
        pointer-events: none;
    }

    .whatsapp-text:after {
        content: '';
        position: absolute;
        top: 50%;
        right: -8px;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 8px solid #ffffff;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
    }

    .whatsapp-text:before {
        content: '';
        position: absolute;
        top: 50%;
        right: -9px;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 9px solid #e5e7eb;
        border-top: 9px solid transparent;
        border-bottom: 9px solid transparent;
        z-index: -1;
    }

    /* Enhanced hover effect */
    .fixed_what:hover .whatsapp-text {
        animation-play-state: paused;
        opacity: 1 !important;
        transform: translateX(0) scale(1) !important;
        pointer-events: auto;
    }

    /* WhatsApp Text Animation Cycle */
    @keyframes whatsappTextCycle {
        0% {
            opacity: 0;
            transform: translateX(10px) scale(0.8);
        }
        5% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
        50% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
        55% {
            opacity: 0;
            transform: translateX(10px) scale(0.8);
        }
        100% {
            opacity: 0;
            transform: translateX(10px) scale(0.8);
        }
    }

    /* Visual pulse effect only */
    .whatsapp-text.active {
        animation: whatsappNotificationPulse 0.6s ease-out;
    }

    @keyframes whatsappNotificationPulse {
        0% {
            transform: translateX(0) scale(1);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        50% {
            transform: translateX(0) scale(1.05);
            box-shadow: 0 12px 30px rgba(37, 211, 102, 0.3);
        }
        100% {
            transform: translateX(0) scale(1);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
    }

    /* Show text on mobile with different positioning */
    @media (max-width: 768px) {
        .fixed_what {
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }
        
        .whatsapp-text {
            max-width: 180px;
            font-size: 13px;
            padding: 10px 14px;
            order: -1; /* Show text above button on mobile */
            transform: translateY(-10px) scale(0.8);
        }
        
        .whatsapp-text:after {
            content: '';
            position: absolute;
            bottom: -8px;
            right: 15px;
            transform: none;
            width: 0;
            height: 0;
            border-top: 8px solid #ffffff;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: none;
        }

        .whatsapp-text:before {
            content: '';
            position: absolute;
            bottom: -9px;
            right: 14px;
            transform: none;
            width: 0;
            height: 0;
            border-top: 9px solid #e5e7eb;
            border-left: 9px solid transparent;
            border-right: 9px solid transparent;
            border-bottom: none;
            z-index: -1;
        }

        /* Mobile animation cycle */
        @keyframes whatsappTextCycle {
            0% {
                opacity: 0;
                transform: translateY(-10px) scale(0.8);
            }
            5% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            50% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            55% {
                opacity: 0;
                transform: translateY(-10px) scale(0.8);
            }
            100% {
                opacity: 0;
                transform: translateY(-10px) scale(0.8);
            }
        }

        /* Mobile hover effect */
        .fixed_what:hover .whatsapp-text {
            transform: translateY(0) scale(1) !important;
        }
    }

    /* WhatsApp specific styling */
    .fixed_what a[href*="wa.me"] {
        background: #25d366;
        background: linear-gradient(135deg, #25d366 0%, #20b858 50%, #128c7e 100%);
        animation: whatsappPulse 2s infinite;
    }

    .fixed_what a[href*="wa.me"]:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 50%;
        background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.3) 0%, transparent 50%);
        pointer-events: none;
    }

    .fixed_what a[href*="wa.me"]:after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
        transform: rotate(-45deg);
        transition: all 0.6s ease;
        opacity: 0;
    }

    .fixed_what a[href*="wa.me"]:hover:after {
        animation: whatsappShine 0.6s ease-in-out;
    }

    @keyframes whatsappPulse {
        0% {
            box-shadow: 0 8px 30px rgba(37, 211, 102, 0.4), 0 0 0 0 rgba(37, 211, 102, 0.7);
        }
        50% {
            box-shadow: 0 8px 30px rgba(37, 211, 102, 0.4), 0 0 0 8px rgba(37, 211, 102, 0);
        }
        100% {
            box-shadow: 0 8px 30px rgba(37, 211, 102, 0.4), 0 0 0 0 rgba(37, 211, 102, 0);
        }
    }

    @keyframes whatsappShine {
        0% {
            opacity: 0;
            transform: translateX(-100%) translateY(-100%) rotate(-45deg);
        }
        50% {
            opacity: 1;
        }
        100% {
            opacity: 0;
            transform: translateX(100%) translateY(100%) rotate(-45deg);
        }
    }

    /* Live Chat button styling */
    .fixed_what a[href*="live-chat"] {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        animation: liveChatGlow 3s infinite;
    }

    @keyframes liveChatGlow {
        0%, 100% {
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
        }
        50% {
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.6), 0 0 0 4px rgba(79, 70, 229, 0.2);
        }
    }

    /* Main Footer */
    footer {
        background: #ffffff;
        color: #374151;
        padding: 60px 0 0;
        margin-top: 80px;
        border-top: 1px solid #e5e7eb;
    }

    .footer-content {
        padding-bottom: 40px;
    }

    .footer-section {
        margin-bottom: 40px;
    }

    .footer-logo {
        margin-bottom: 20px;
    }

    .footer-logo img {
        max-width: 180px;
        height: auto;
    }

    .footer-description {
        font-size: 16px;
        line-height: 1.6;
        color: #6b7280;
        margin-bottom: 25px;
        max-width: 300px;
    }

    .footer-title {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
    }

    .footer-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 3px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border-radius: 2px;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
    }

    .footer-links a {
        color: #6b7280;
        text-decoration: none;
        font-size: 15px;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .footer-links a:hover {
        color: #4f46e5;
        transform: translateX(5px);
    }

    .contact-info {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .contact-info li {
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        font-size: 15px;
        color: #6b7280;
    }

    .contact-info i {
        margin-right: 12px;
        font-size: 16px;
        color: #4f46e5;
        width: 20px;
    }

    .social-links {
        display: flex;
        gap: 15px;
        margin-top: 25px;
    }

    .social-links a {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        transition: all 0.3s ease;
        text-decoration: none;
        border: 2px solid transparent;
    }

    .social-links a:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        border-color: rgba(0, 0, 0, 0.1);
    }

    .app-download {
        margin-top: 30px;
    }

    .app-download a {
        display: inline-block;
        margin-right: 15px;
        transition: transform 0.3s ease;
    }

    .app-download img {
        width: 150px;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    .app-download a:hover {
        transform: translateY(-2px);
    }

    .app-download h5 {
        color: #1f2937 !important;
    }

    .footer-bottom {
        background: #f9fafb;
        padding: 20px 0;
        text-align: center;
        border-top: 1px solid #e5e7eb;
    }

    .footer-bottom p {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .footer-section {
            text-align: center;
        }
        
        .footer-description {
            max-width: 100%;
        }
        
        .social-links {
            justify-content: center;
        }
        
        .app-download {
            text-align: center;
        }
    }

    /* Book-specific styling */
    .book-categories {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 20px;
    }

    .book-category-tag {
        background: #f3f4f6;
        color: #4f46e5;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .book-category-tag:hover {
        background: #e0e7ff;
        transform: translateY(-1px);
        border-color: #4f46e5;
    }
</style>

<script>
// Visual effects only - no sound
document.addEventListener('DOMContentLoaded', function() {
    const whatsappText = document.querySelector('.whatsapp-text');
    
    if (whatsappText) {
        // Function to add visual pulse effect only
        function addPulseEffect() {
            whatsappText.classList.add('active');
            setTimeout(() => {
                whatsappText.classList.remove('active');
            }, 600);
        }
        
        // Set up the visual notification cycle (every 20 seconds)
        setInterval(() => {
            // Small delay to sync with CSS animation
            setTimeout(() => {
                addPulseEffect();
            }, 1000); // 1s delay to match the 5% point in CSS animation
        }, 20000);
        
        // Initial pulse effect after 1 second
        setTimeout(() => {
            addPulseEffect();
        }, 1000);
    }
});
</script>

{{-- Floating Action Buttons - Hide on product details, checkout and buy now pages --}}
@unless(Request::routeIs('product.details') || Request::routeIs('product.cam.details') || Request::routeIs('checkout') || Request::routeIs('buy.product'))
    @if (setting('FLOAT_LIVE_CHAT') != 1 || setting('FLOAT_LIVE_CHAT') == "")
        @if(!empty(setting('whatsapp')))
        <li class="fixed_what">
            <div class="whatsapp-text">
                আসসালামুয়ালাইকুম! কিভাবে সহযোগীতা করতে পারি?
            </div>
            <a href="https://wa.me/{{setting('whatsapp')}}" title="Chat with us on WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </a>
        </li>
        @endif
    @else
    <li class="fixed_what">
        <a href="{{ url('/connection/live-chat') }}" title="Live Chat Support">
            <i class="fas fa-headset"></i>
        </a>
    </li>
    @endif

    <li class="fixed-cart d-none">
        <a href="{{route('cart')}}" style="color: white; text-decoration: none;">
            <div>
                <i class="fas fa-shopping-bag" style="font-size: 20px;"></i>
                <div style="font-size: 12px; margin-top: 2px;">{{Cart::count()}}</div>
            </div>
        </a>
    </li>
@endunless

{{-- Main Footer --}}
<footer>
    <div class="container footer-content">
        <div class="row">
            {{-- Company Info --}}
            <div class="col-lg-4 col-md-6 footer-section">
                <div class="footer-logo">
                    <a href="{{route('home')}}">
                        <img src="{{asset('uploads/setting/'.setting('logo'))}}" alt="{{setting('site_name')}}">
                    </a>
                </div>
                <p class="footer-description">
                    {{setting('footer_description') ?: 'Discover your next great read with our extensive collection of books. From bestsellers to hidden gems, we have something for every reader.'}}
                </p>
                
                {{-- Book Categories --}}
                <div class="book-categories">
                    <a href="#" class="book-category-tag">Fiction</a>
                    <a href="#" class="book-category-tag">Non-Fiction</a>
                    <a href="#" class="book-category-tag">Mystery</a>
                    <a href="#" class="book-category-tag">Romance</a>
                    <a href="#" class="book-category-tag">Sci-Fi</a>
                </div>
                
                {{-- Social Links --}}
                <div class="social-links">
                    @if(!empty(setting('facebook')))
                    <a href="{{setting('facebook')}}" style="background: #1877f2;" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    @endif
                    @if(!empty(setting('instagram')))
                    <a href="{{setting('instagram')}}" style="background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    @endif
                    @if(!empty(setting('twitter')))
                    <a href="{{setting('twitter')}}" style="background: #1da1f2;" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    @endif
                    @if(!empty(setting('youtube')))
                    <a href="{{setting('youtube')}}" style="background: #ff0000;" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    @endif
                    @if(!empty(setting('linkedin')))
                    <a href="{{setting('linkedin')}}" style="background: #0077b5;" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="col-lg-2 col-md-6 footer-section">
                <h4 class="footer-title">Quick Links</h4>
                <ul class="footer-links">
                    @foreach($footerPages as $page)
                    <li><a href="{{route('page',['slug'=>$page->name])}}">{{$page->name}}</a></li>
                    @endforeach
                    @foreach(App\Models\Page::where('position',2)->where('status',1)->get() as $page)
                    <li><a href="{{route('page',['slug'=>$page->name])}}">{{$page->name}}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Account & Services --}}
            <div class="col-lg-2 col-md-6 footer-section">
                <h4 class="footer-title">My Account</h4>
                <ul class="footer-links">
                    <li><a href="{{route('cart')}}">Shopping Cart</a></li>
                    @auth
                    <li><a href="{{route('account')}}">My Account</a></li>
                    <li><a href="{{route('order')}}">Order History</a></li>
                    <li><a href="{{route('checkout')}}">Checkout</a></li>
                    <li>
                        <a href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                    @else
                    <li><a href="{{route('login')}}">Login</a></li>
                    <li><a href="{{route('register')}}">Register</a></li>
                    <li><a href="{{route('vendorJoin')}}">Become a Seller</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Contact Info --}}
            <div class="col-lg-4 col-md-6 footer-section">
                <h4 class="footer-title">Contact Us</h4>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        {{setting('SITE_INFO_ADDRESS')}}
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        {{setting('SITE_INFO_SUPPORT_MAIL')}}
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        {{setting('SITE_INFO_PHONE')}}
                    </li>
                </ul>

                {{-- App Download --}}
                @if(setting('android_app'))
                <div class="app-download">
                    <h5 style="color: #1f2937; margin-bottom: 15px; font-size: 16px;">Download Our App</h5>
                    <a href="https://drive.google.com/file/d/16neRUFZf20QHgGXxtjFZdGAqU3kxr492/view?usp=drivesdk">
                        <img src="{{asset('/')}}/assets/uploads/images/google-play-png-logo-3799.png" alt="Download on Google Play">
                    </a>
                </div>
                @endif

                {!! setting('FOOTER_COL_4_HTML') !!}
            </div>
        </div>
    </div>

    {{-- Copyright --}}
    <div class="footer-bottom">
        <div class="container">
            <p>{{setting('copy_right_text') ?: '© 2024 BookStore. All rights reserved.'}}</p>
        </div>
    </div>
</footer>