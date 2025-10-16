{{-- single-product-partial/reviews-section.blade.php --}}
<div id="spReviews" class="collapse" aria-labelledby="spHeadingThree" data-parent="#spAccordion">
    <div class="sp-accordion-content">
        <div class="sp-review-summary">
            <span class="heading">Product Reviews</span>
            <p>{{ $product->reviews->count() }} reviews for this product.</p>
        </div>

        @forelse ($product->reviews as $review)
            <div class="sp-review-item">
                <div class="sp-review-header">
                    @if($review->user)
                        <img class="sp-review-avatar" src="{{ $review->user->avatar == 'default.png' ? '/default/default.png' : '/uploads/admin/' . $review->user->avatar }}" alt="">
                        <div>
                            <div class="sp-reviewer-name">{{ $review->user->name }}</div>
                        </div>
                    @else
                        <div>
                            <div class="sp-reviewer-name">Anonymous User</div>
                        </div>
                    @endif
                </div>
                <p>{{ $review->body }}</p>
                @if($review->file || $review->file2 || $review->file3 || $review->file4 || $review->file5)
                    <div class="d-flex crv" style="gap: 10px; margin-top: 15px;">
                        @if ($review->file)
                            <a href="{{ asset('/') }}uploads/review/{{ $review->file }}">
                                <img width="80px" height="80px" style="object-fit: cover; border-radius: 8px;" src="{{ asset('/') }}uploads/review/{{ $review->file }}">
                            </a>
                        @endif
                        @if ($review->file2)
                            <a href="{{ asset('/') }}uploads/review/{{ $review->file2 }}">
                                <img width="80px" height="80px" style="object-fit: cover; border-radius: 8px;" src="{{ asset('/') }}uploads/review/{{ $review->file2 }}">
                            </a>
                        @endif
                        @if ($review->file3)
                            <a href="{{ asset('/') }}uploads/review/{{ $review->file3 }}">
                                <img width="80px" height="80px" style="object-fit: cover; border-radius: 8px;" src="{{ asset('/') }}uploads/review/{{ $review->file3 }}">
                            </a>
                        @endif
                        @if ($review->file4)
                            <a href="{{ asset('/') }}uploads/review/{{ $review->file4 }}">
                                <img width="80px" height="80px" style="object-fit: cover; border-radius: 8px;" src="{{ asset('/') }}uploads/review/{{ $review->file4 }}">
                            </a>
                        @endif
                        @if ($review->file5)
                            <a href="{{ asset('/') }}uploads/review/{{ $review->file5 }}">
                                <img width="80px" height="80px" style="object-fit: cover; border-radius: 8px;" src="{{ asset('/') }}uploads/review/{{ $review->file5 }}">
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center" style="padding: 40px;">
                <i class="far fa-comments" style="font-size: 4rem; color: var(--sp-text-light); margin-bottom: 20px;"></i>
                <h3 style="color: var(--sp-text-light);">No Reviews Yet</h3>
                <p style="color: var(--sp-text-light);">Be the first to review this book!</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    /* Reviews Section */
    .sp-review-summary {
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        padding: 30px;
        border-radius: var(--sp-radius-lg);
        margin-bottom: 30px;
        border: 1px solid #ffd6a5;
    }

    .sp-review-summary .heading {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--sp-text-dark);
        margin-bottom: 20px;
    }

    .sp-review-item {
        background: var(--sp-bg-primary);
        border-radius: var(--sp-radius-md);
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: var(--sp-shadow-sm);
        border: 1px solid var(--sp-border-light);
        transition: all var(--sp-transition-normal);
    }

    .sp-review-item:hover {
        box-shadow: var(--sp-shadow-md);
        transform: translateY(-2px);
    }

    .sp-review-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }

    .sp-review-avatar {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--sp-border-light);
    }

    .sp-reviewer-name {
        font-weight: 600;
        color: var(--sp-text-dark);
        font-size: 16px;
    }
</style>