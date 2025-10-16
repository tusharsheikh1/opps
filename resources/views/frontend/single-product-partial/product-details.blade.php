{{-- frontend/single-product-partial/product-details.blade.php --}}
<div class="sp-product-details-section">
    <div class="sp-details-header">
        📚 Product Specification & Summary
    </div>
    
    <div class="sp-accordion-navigation">
        <button class="collapsed" data-toggle="collapse" data-target="#spDescription" aria-expanded="true" aria-controls="spDescription">
            Summary
        </button>
        @if ($product->book == 1 && $product->author)
            <button class="collapsed" data-toggle="collapse" data-target="#spSpecification" aria-expanded="false" aria-controls="spSpecification">
                Specification
            </button>
            <button class="collapsed" data-toggle="collapse" data-target="#spAuthor" aria-expanded="false" aria-controls="spAuthor">
                Author
            </button>
        @endif
        <button class="collapsed" data-toggle="collapse" data-target="#spReviews" aria-expanded="false" aria-controls="spReviews">
            Reviews({{ $product->reviews->count() }})
        </button>
        <button class="collapsed" data-toggle="collapse" data-target="#spComments" aria-expanded="false" aria-controls="spComments">
            Ask Question({{ $product->comments->where('parent_id', null)->count() }})
        </button>
    </div>

    <div id="spAccordion" style="width: 100%;">
        @include('frontend.single-product-partial.description-section')
        @include('frontend.single-product-partial.author-section')
        @include('frontend.single-product-partial.specification-section')
        @include('frontend.single-product-partial.reviews-section')
        @include('frontend.single-product-partial.comments-section')
    </div>
</div>

<style>
    /* Product Details Section */
    .sp-product-details-section {
        background: var(--sp-bg-primary);
        border-radius: var(--sp-radius-lg);
        box-shadow: var(--sp-shadow-md);
        overflow: hidden;
        margin-top: 30px;
        border: 1px solid var(--sp-border-light);
    }

    .sp-details-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        font-size: 1.5rem;
        font-weight: 700;
        text-align: center;
    }

    .sp-accordion-navigation {
        display: flex;
        background: var(--sp-bg-secondary);
        border-bottom: 1px solid var(--sp-border-light);
    }

    .sp-accordion-navigation button {
        flex: 1;
        background: none;
        border: none;
        padding: 18px 20px;
        font-weight: 600;
        color: var(--sp-text-light);
        cursor: pointer;
        transition: all var(--sp-transition-normal);
        border-bottom: 3px solid transparent;
        font-size: 14px;
    }

    .sp-accordion-navigation button:hover,
    .sp-accordion-navigation button:not(.collapsed) {
        color: var(--sp-primary);
        background: var(--sp-bg-primary);
        border-bottom-color: var(--sp-primary);
    }

    .sp-accordion-content {
        padding: 35px;
        line-height: 1.8;
    }

    @media (max-width: 768px) {
        .sp-accordion-navigation {
            flex-direction: column;
        }
        
        .sp-accordion-navigation button {
            padding: 15px;
            border-bottom: 1px solid var(--sp-border-light);
        }
    }

    @media (max-width: 480px) {
        .sp-accordion-navigation {
            display: block;
        }

        .sp-accordion-navigation button {
            display: block;
            width: 100%;
            text-align: left;
        }
    }
</style>