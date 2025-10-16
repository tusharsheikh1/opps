{{-- single-product-partial/product-images.blade.php --}}
<div class="col-md-4 single-right-left">
    <div class="sp-product-images">
        <div class="flexslider">
            <div class="clearfix"></div>
            <div class="flex-viewport" style="overflow: hidden; position: relative;">
                <ul class="slides my-gallery" style="width: 1000%; transition-duration: 0.6s; transform: translate3d(-1311px, 0px, 0px);">
                    {{-- Video Slides --}}
                    @if ($product->yvideo)
                        <li data-thumb="{{ asset('uploads/product/video/' . $product->video_thumb) }}" style="width: 437px; float: left; display: block;" class="">
                            <div class="thumb-image">
                                <iframe width="100%" style="height:300px;background:black;border-radius:8px;" src="{{ $product->yvideo }}"></iframe>
                            </div>
                        </li>
                    @elseif($product->video)
                        <li data-thumb="{{ asset('uploads/product/video/' . $product->video_thumb) }}" style="width: 437px; float: left; display: block;" class="">
                            <div class="thumb-image">
                                <video class="video-js" controls data-setup="{}" controls width="100%" style="height:300px;background:black">
                                    <source src="{{ asset('uploads/product/video/' . $product->video) }}" type="video/mp4">
                                </video>
                            </div>
                        </li>
                    @endif

                    {{-- Product Images --}}
                    @foreach ($product->images as $image)
                        <li data-cl="{{ $image->color_attri }}" data-thumb="{{ asset('uploads/product/' . $image->name) }}" style="width: 437px; float: left; display: block;" class="">
                            <div class="thumb-image">
                                <a class="" href="{{ asset('uploads/product/' . $image->name) }}" target="_blank">
                                    <img src="{{ asset('uploads/product/' . $image->name) }}" class="my-gallery-image img-responsive" alt="" draggable="true">
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Navigation Arrows --}}
            <ul class="flex-direction-nav">
                <li class="flex-nav-prev"><a class="flex-prev" href="#">Previous</a></li>
                <li class="flex-nav-next"><a class="flex-next" href="#">Next</a></li>
            </ul>
        </div>
    </div>
</div>

<style>
    /* Product Images Section */
    .sp-product-images {
        background: var(--sp-bg-primary);
        border-radius: var(--sp-radius-md);
        overflow: hidden;
        position: relative;
    }

    .single-product-container .flexslider {
        border: none !important;
        box-shadow: none !important;
        border-radius: var(--sp-radius-md);
        overflow: hidden;
        background: var(--sp-bg-primary);
        margin: 0 !important;
    }

    .single-product-container .flexslider .slides img {
        background: var(--sp-bg-primary);
        border-radius: var(--sp-radius-sm);
        transition: transform var(--sp-transition-normal);
        max-height: 400px;
        object-fit: contain;
        width: 100%;
    }

    .single-product-container .flexslider .slides img:hover {
        transform: scale(1.05);
    }

    .single-product-container .flex-direction-nav a {
        background: rgba(44, 62, 80, 0.8) !important;
        color: white !important;
        border-radius: 50% !important;
        width: 40px !important;
        height: 40px !important;
        margin: -20px 0 0 0 !important;
        transition: all var(--sp-transition-fast) !important;
        text-indent: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .single-product-container .flex-direction-nav a:hover {
        background: var(--sp-primary) !important;
        transform: scale(1.1) !important;
    }

    .single-product-container .flex-direction-nav a:before {
        font-family: "Font Awesome 5 Pro" !important;
        font-size: 16px !important;
    }

    .single-product-container .flex-direction-nav .flex-prev:before {
        content: "\f104" !important;
    }

    .single-product-container .flex-direction-nav .flex-next:before {
        content: "\f105" !important;
    }

    /* Video Player Styling */
    .single-product-container .video-js {
        border-radius: var(--sp-radius-md);
        overflow: hidden;
    }
</style>