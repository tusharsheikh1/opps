{{-- frontend/single-product-partial/product-info.blade.php --}}
<div class="col-md-5 single-right-left simpleCart_shelfItem">
    <div class="sp-product-info">
        <!-- Product Title -->
        <h1 class="sp-product-title">{{ $product->title }}</h1>
        
        <!-- Product Meta Information -->
        @include('frontend.single-product-partial.product-meta')

        <!-- Short Description -->
        @include('frontend.single-product-partial.short-description')

        <!-- Price Section -->
        @include('frontend.single-product-partial.price-section')

        <!-- Stock Status -->
        @include('frontend.single-product-partial.stock-status')

        <!-- Color Selection -->
        @include('frontend.single-product-partial.color-selection')

        <!-- Attribute Selection -->
        @include('frontend.single-product-partial.attribute-selection')

        <!-- Quantity Selection -->
        @include('frontend.single-product-partial.quantity-selection')

        <!-- Action Buttons -->
        @include('frontend.single-product-partial.action-buttons')

        <!-- Secondary Actions -->
        @include('frontend.single-product-partial.secondary-actions')

        <!-- Product Features -->
        @include('frontend.single-product-partial.product-features')
    </div>
</div>

<style>
    /* Product Info Section */
    .sp-product-info {
        background: transparent;
        padding: 20px;
    }

    .sp-product-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--sp-text-dark);
        margin: 0 0 20px 0;
        line-height: 1.3;
        letter-spacing: -0.5px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sp-product-title {
            font-size: 22px;
            margin-bottom: 15px;
        }
    }

    @media (max-width: 480px) {
        .sp-product-title {
            font-size: 20px;
        }
    }
</style>