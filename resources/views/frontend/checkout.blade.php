{{-- 
    Checkout Page Handler
    Handles different checkout flows based on user authentication and settings
--}}

@auth
    {{-- Authenticated User Checkout --}}
    @if (in_array(auth()->user()->role_id, [2, 3]))
        @if (setting('CHECKOUT_TYPE') == 1)
            {{-- Standard Checkout --}}
            @includeWhen(isset($request), 'frontend.partial.checkout.bc')
            @includeUnless(isset($request), 'frontend.partial.checkout.c')
        @else
            {{-- Minimal Checkout --}}
            @includeWhen(isset($request), 'frontend.partial.checkout.bc_minimal')
            @includeUnless(isset($request), 'frontend.partial.checkout.c_minimal')
        @endif
    @else
        {{-- Unauthorized user - redirect to login --}}
        <script>
            window.location.href = "{{ route('login') }}";
        </script>
    @endif
@else
    {{-- Guest User Checkout --}}
    @if (setting('GUEST_CHECKOUT') == 0)
        {{-- Guest checkout disabled - redirect to login --}}
        <script>
            window.location.href = "{{ route('login') }}";
        </script>
    @else
        @if (setting('CHECKOUT_TYPE') == 1)
            {{-- Standard Guest Checkout --}}
            @includeWhen(isset($request), 'frontend.partial.checkout.bc_guest')
            @includeUnless(isset($request), 'frontend.partial.checkout.c_guest')
        @else
            {{-- Minimal Guest Checkout --}}
            @includeWhen(isset($request), 'frontend.partial.checkout.bc_minimal')
            @includeUnless(isset($request), 'frontend.partial.checkout.c_minimal')
        @endif
    @endif
@endauth