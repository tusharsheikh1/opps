<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.frontend.partials.meta')
    @include('layouts.global')
    @include('layouts.frontend.partials.style')
    {{-- Make sure your partials.style file includes Font Awesome for the cart icon --}}
    @php echo setting('fb_pixel'); @endphp
    {{-- --}}
    @php echo setting('header_code'); @endphp
    
    {{-- Page Loader Styles --}}
    <style>
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: transparent;
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.3s ease;
        }
        
        #page-loader.show {
            display: flex;
        }
        
        #page-loader.fade-out {
            opacity: 0;
            pointer-events: none;
        }
        
        .loader-logo {
            width: 150px;
            height: auto;
        }
    </style>
</head>
<body{{--  class="" --}}>
    @php echo setting('body_code'); @endphp

    {{-- Page Loader --}}
    <div id="page-loader">
        <img src="{{ asset('Opps_logo.png') }}" alt="Loading..." class="loader-logo">
    </div>

    {{-- Facebook SDK --}}
    @if (env('FACEBOOK_SKD_ON') == 1)
        <div id="fb-root"></div>
        <div id="fb-customer-chat" class="fb-customerchat"></div>
        <script>
            var chatbox = document.getElementById('fb-customer-chat');
            chatbox.setAttribute("page_id", "523283677850901");
            chatbox.setAttribute("attribution", "biz_inbox");
        </script>
        <script>
            window.fbAsyncInit = function () {
                FB.init({
                    xfbml: true,
                    version: 'v13.0'
                });
            };
            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        <style>
            .fb_dialog_content iframe {
                bottom: 105px !important;
            }
        </style>
    @endif

    {{-- Top Header Style --}}
    @if (!empty(setting('TOP_HEADER_STYLE')))
        @include('layouts.frontend.partials.header_' . setting('TOP_HEADER_STYLE'))
    @else
        @include('layouts.frontend.partials.header_1')
    @endif

    @yield('content')

    @include('layouts.frontend.partials.footer')

    <style>
        @stack('override_css')
        {{ setting('override_css') }}
    </style>

    @include('layouts.frontend.partials.script')
    <script src="{{ asset('js/product-variations.js') }}"></script>
    
    {{-- Page Loader Script --}}
    <script>
        // Only show loader if page takes more than 200ms to load
        var loaderTimer = setTimeout(function() {
            document.getElementById('page-loader').classList.add('show');
        }, 200);
        
        window.addEventListener('load', function() {
            clearTimeout(loaderTimer);
            var loader = document.getElementById('page-loader');
            loader.classList.add('fade-out');
            setTimeout(function() {
                loader.style.display = 'none';
            }, 300);
        });
    </script>
</body>
</html>