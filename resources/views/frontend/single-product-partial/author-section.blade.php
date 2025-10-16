{{-- single-product-partial/author-section.blade.php --}}
@if ($product->book == 1 && $product->author)
    <div id="spAuthor" class="collapse" aria-labelledby="spHeadingThrees" data-parent="#spAccordion">
        <div class="sp-accordion-content">
            <div class="sp-author-section">
                <img class="sp-author-avatar" src="{{ asset('/') }}/uploads/admin/{{ $product->author->img }}">
                <div class="sp-author-info">
                    <h4>{{ $product->author->name }}</h4>
                    {!! $product->author->bio !!}
                </div>
            </div>
        </div>
    </div>
@endif

<style>
    /* Author Section */
    .sp-author-section {
        display: flex;
        gap: 25px;
        align-items: flex-start;
    }

    .sp-author-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: var(--sp-shadow-md);
        border: 4px solid var(--sp-border-light);
    }

    .sp-author-info h4 {
        color: var(--sp-text-dark);
        font-weight: 700;
        margin-bottom: 15px;
        font-size: 1.3rem;
    }

    @media (max-width: 768px) {
        .sp-author-section {
            flex-direction: column;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        .sp-author-avatar {
            width: 100px;
            height: 100px;
        }
    }
</style>