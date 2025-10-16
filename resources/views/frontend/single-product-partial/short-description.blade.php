{{-- single-product-partial/short-description.blade.php --}}
<div class="sp-short-description">
    <div class="sp-description-text" id="spShortDesc">
        @php
            $shortText = strip_tags($product->short_description);
            $words = explode(' ', $shortText);
            $truncated = implode(' ', array_slice($words, 0, 30));
            $needsExpansion = count($words) > 30;
        @endphp
        <span id="sp-truncated-text">{{ $truncated }}{{ $needsExpansion ? '...' : '' }}</span>
        @if($needsExpansion)
            <span id="sp-full-text" style="display: none;">{!! $product->short_description !!}</span>
            <button class="sp-see-more-btn" id="spSeeMoreBtn" onclick="spToggleDescription()">আরও দেখুন</button>
        @else
            {!! $product->short_description !!}
        @endif
    </div>
</div>

<style>
    /* Short Description */
    .sp-short-description {
        margin: 25px 0;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: var(--sp-radius-md);
        border-left: 4px solid var(--sp-secondary);
    }

    .sp-description-text {
        color: var(--sp-text-medium);
        font-size: 16px;
        line-height: 1.7;
        margin-bottom: 10px;
    }

    .sp-see-more-btn {
        background: none;
        border: none;
        color: var(--sp-secondary);
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
        padding: 0;
        text-decoration: underline;
        transition: color var(--sp-transition-fast);
    }

    .sp-see-more-btn:hover {
        color: var(--sp-accent);
    }
</style>

<script>
    // Toggle description function for single product
    function spToggleDescription() {
        const truncatedText = document.getElementById('sp-truncated-text');
        const fullText = document.getElementById('sp-full-text');
        const seeMoreBtn = document.getElementById('spSeeMoreBtn');
        
        if (fullText.style.display === 'none') {
            truncatedText.style.display = 'none';
            fullText.style.display = 'inline';
            seeMoreBtn.textContent = 'কম দেখুন';
        } else {
            truncatedText.style.display = 'inline';
            fullText.style.display = 'none';
            seeMoreBtn.textContent = 'আরও দেখুন';
        }
    }
</script>