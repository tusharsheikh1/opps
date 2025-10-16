{{-- single-product-partial/price-section.blade.php --}}
<div class="sp-price-section">
    <?php if (isset($campaigns_product)) {
        $product->discount_price = $campaigns_product->price;
    } ?>
    
    <div class="sp-price-display">
        @if ($product->discount_price > 0)
            <div class="sp-current-price">৳<span id="sp_dynamic_price">{{ $product->discount_price }}</span></div>
            <div class="sp-original-price">৳{{ $product->regular_price }}</div>
            @php
                $per = $product->regular_price / 100;
                $amc = $product->regular_price - $product->discount_price;
                $discount_percentage = round($amc / $per);
            @endphp
            <div class="sp-discount-badge">{{ $discount_percentage }}% ছাড়</div>
        @else
            <div class="sp-current-price">৳<span id="sp_dynamic_price">{{ $product->regular_price }}</span></div>
        @endif
    </div>
    
    @if ($product->discount_price > 0)
        <div class="sp-savings-text">আপনি সাশ্রয় করবেন ৳{{ $amc }}</div>
    @endif
</div>

<style>
    /* Price Section */
    .sp-price-section {
        margin: 30px 0;
        padding: 25px;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: var(--sp-radius-md);
        box-shadow: var(--sp-shadow-sm);
        border: 1px solid var(--sp-border-light);
    }

    .sp-price-display {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .sp-current-price {
        font-family: 'Inter', monospace;
        font-weight: 800;
        font-size: 2.8rem;
        color: var(--sp-accent);
        line-height: 1;
        text-shadow: 0 2px 4px rgba(231, 76, 60, 0.2);
    }

    .sp-original-price {
        font-size: 1.2rem;
        color: var(--sp-text-light);
        text-decoration: line-through;
        font-weight: 500;
    }

    .sp-discount-badge {
        background: linear-gradient(135deg, var(--sp-accent) 0%, #c0392b 100%);
        color: white;
        padding: 6px 12px;
        border-radius: var(--sp-radius-sm);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
    }

    .sp-savings-text {
        color: var(--sp-success);
        font-size: 14px;
        font-weight: 600;
        background: rgba(39, 174, 96, 0.1);
        padding: 4px 8px;
        border-radius: var(--sp-radius-sm);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sp-current-price {
            font-size: 2.2rem;
        }

        .sp-price-display {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
    }

    @media (max-width: 480px) {
        .sp-current-price {
            font-size: 2rem;
        }

        .sp-original-price {
            font-size: 1rem;
        }

        .sp-discount-badge {
            font-size: 11px;
            padding: 4px 8px;
        }
    }
</style>