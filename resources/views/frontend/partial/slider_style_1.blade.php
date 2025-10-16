@if($pop)
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <a href="{{$pop->url}}" class="d-block">
                    <div class="item">
                        <img src="{{asset('uploads/slider/'.$pop->image)}}" class="img-fluid w-100" alt="Popup Image" />
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<style>
/* Modern Slider Styles */
.hero-area {
    margin-top: 2px;
    position: relative;
    overflow: hidden;
}

.hero-slider {
    width: 100%;
    max-width: 1130px;
    margin: 0 auto;
    position: relative;
}

.slideshow {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    min-height: 400px; /* Prevent layout shift */
}

/* FOUC Prevention - Hide slides initially */
.slider.slick-slides {
    margin: 0;
    opacity: 0;
    transition: opacity 0.1s ease; /* Faster transition for instant feel */
}

/* Show slides after initialization */
.slider.slick-slides.slick-initialized {
    opacity: 1;
}

/* Hide all items initially except first */
.slider.slick-slides:not(.slick-initialized) .item {
    display: none;
}

.slider.slick-slides:not(.slick-initialized) .item:first-child {
    display: block;
}

.slider .item {
    position: relative;
    overflow: hidden;
    border-radius: 12px;
}

.slider .item img {
    width: 100%;
    height: auto;
    min-height: 400px;
    object-fit: cover;
    transition: transform 0.6s ease;
    border-radius: 12px;
}

.slider .item:hover img {
    transform: scale(1.05);
}

.slider .item a {
    display: block;
    position: relative;
    overflow: hidden;
    border-radius: 12px;
}

/* Slick Slider Custom Styling */
.slick-dots {
    bottom: 20px;
    z-index: 10;
}

.slick-dots li button:before {
    color: #fff;
    font-size: 12px;
    opacity: 0.7;
}

.slick-dots li.slick-active button:before {
    opacity: 1;
    color: #007bff;
}

/* Glassmorphic Hidden Arrows */
.slick-prev,
.slick-next {
    z-index: 10;
    width: 55px;
    height: 55px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    opacity: 0;
    visibility: hidden;
    transform: scale(0.8);
}

/* Show arrows on hover/touch */
.slideshow:hover .slick-prev,
.slideshow:hover .slick-next,
.slideshow:focus-within .slick-prev,
.slideshow:focus-within .slick-next {
    opacity: 1;
    visibility: visible;
    transform: scale(1);
}

.slick-prev:hover,
.slick-next:hover {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    transform: scale(1.1);
}

.slick-prev:active,
.slick-next:active {
    transform: scale(0.95);
    background: rgba(255, 255, 255, 0.3);
}

.slick-prev {
    left: 20px;
}

.slick-next {
    right: 20px;
}

.slick-prev:before,
.slick-next:before {
    color: rgba(255, 255, 255, 0.9);
    font-size: 22px;
    font-weight: bold;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.slick-prev:hover:before,
.slick-next:hover:before {
    color: rgba(255, 255, 255, 1);
}

/* Loading Animation - Show before initialization */
.slideshow::before {
    display: none; /* Completely remove loading animation for instant load */
}

/* Touch device support for arrow visibility */
@media (hover: none) and (pointer: coarse) {
    .slick-prev,
    .slick-next {
        opacity: 0.6;
        visibility: visible;
        transform: scale(0.9);
    }
    
    .slideshow:active .slick-prev,
    .slideshow:active .slick-next,
    .slick-prev:active,
    .slick-next:active {
        opacity: 1;
        transform: scale(1);
    }
}

/* Responsive Design */
@media (max-width: 1199px) {
    .slider .item img {
        min-height: 350px;
    }
    
    .slideshow {
        min-height: 350px;
    }
    
    .slick-prev,
    .slick-next {
        width: 50px;
        height: 50px;
    }
    
    .slick-prev {
        left: 15px;
    }
    
    .slick-next {
        right: 15px;
    }
}

@media (max-width: 768px) {
    .slider .item img {
        min-height: 250px;
    }
    
    .slideshow {
        min-height: 250px;
    }
    
    .slick-prev,
    .slick-next {
        width: 45px;
        height: 45px;
        display: none !important; /* Hide arrows on mobile for better UX */
    }
    
    .slideshow {
        border-radius: 8px;
    }
    
    .slider .item,
    .slider .item a,
    .slider .item img {
        border-radius: 8px;
    }
}

@media (max-width: 576px) {
    .hero-area {
        margin-top: 1px;
    }
    
    .slider .item img {
        min-height: 200px;
    }
    
    .slideshow {
        min-height: 200px;
    }
    
    .slick-dots {
        bottom: 10px;
    }
    
    .slideshow {
        border-radius: 6px;
    }
    
    .slider .item,
    .slider .item a,
    .slider .item img {
        border-radius: 6px;
    }
}

/* Additional Modern Effects */
.slider .item::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(0, 0, 0, 0.1) 0%, transparent 50%);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
    border-radius: 12px;
}

.slider .item:hover::after {
    opacity: 1;
}

/* Modal Improvements */
.modal-content {
    border: none;
    border-radius: 12px;
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
}

.modal-body .item img {
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.modal-body .item:hover img {
    transform: scale(1.02);
}

/* Navbar Fixed Styles */
.navbar_fixed {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 9999999;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
    transition: all 0.3s ease;
}

/* Additional glassmorphic effect for better visibility */
.slideshow::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    background: radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
    z-index: 1;
}

.slick-prev,
.slick-next {
    z-index: 15; /* Ensure arrows are above the glassmorphic overlay */
}
</style>

<section class="hero-area">
    <div class="container-fluid px-3">
        <div class="row">
            <div class="hero-slider col-12">
                <div class="slideshow">
                    <div class="slider slick-slides" data-slick='{
                        "autoplay": true,
                        "autoplaySpeed": 4000,
                        "dots": true,
                        "arrows": true,
                        "fade": false,
                        "speed": 800,
                        "pauseOnHover": true,
                        "responsive": [
                            {
                                "breakpoint": 768,
                                "settings": {
                                    "arrows": false,
                                    "dots": true
                                }
                            }
                        ]
                    }'>
                        @foreach ($sliders as $key => $slider)
                        <div class="item">
                            <a href="{{$slider->url}}" aria-label="Slider Image {{$key + 1}}">
                                <img src="{{asset('uploads/slider/'.$slider->image)}}" 
                                     alt="Slider {{$key + 1}}" 
                                     loading="{{$key === 0 ? 'eager' : 'lazy'}}" />
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Initialize Slick Slider with instant load (no loading animation)
$(document).ready(function() {
    if ($('.slick-slides').length) {
        // Initialize the slider immediately
        $('.slick-slides').slick({
            autoplay: true,
            autoplaySpeed: 4000,
            dots: true,
            arrows: true,
            fade: false,
            speed: 800,
            pauseOnHover: true,
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        dots: true
                    }
                }
            ]
        });
        
        // Make slider visible immediately after initialization
        $('.slick-slides').addClass('slick-initialized');
    }
    
    // Auto-show popup modal if exists
    @if($pop)
    $('#myModal').modal('show');
    @endif
});
</script>