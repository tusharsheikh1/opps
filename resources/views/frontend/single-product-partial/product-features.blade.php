{{-- single-product-partial/product-features.blade.php --}}
<div class="sp-product-features">
    <div class="sp-feature-item">
        <i class="fas fa-truck sp-feature-icon"></i>
        <span>৭ দিনের হ্যাপি রিটার্ন</span>
    </div>
    <div class="sp-feature-item">
        <i class="icofont icofont-cash-on-delivery sp-feature-icon"></i>
        <span>ক্যাশ অন ডেলিভারি</span>
    </div>
</div>

<style>
    /* Product Features */
    .sp-product-features {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin: 25px 0;
        padding: 25px;
        background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
        border-radius: var(--sp-radius-md);
        border: 1px solid #d4edda;
    }

    .sp-feature-item {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        color: var(--sp-text-dark);
        font-weight: 500;
    }

    .sp-feature-icon {
        color: var(--sp-success);
        font-size: 20px;
    }

    @media (max-width: 768px) {
        .sp-product-features {
            grid-template-columns: 1fr;
        }
    }
</style>