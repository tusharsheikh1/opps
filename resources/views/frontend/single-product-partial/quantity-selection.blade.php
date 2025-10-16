{{-- single-product-partial/quantity-selection.blade.php --}}
<div class="sp-quantity-section">
    <div class="sp-quantity-label">পরিমাণ:</div>
    <div class="sp-quantity-controls">
        <button type="button" class="sp-quantity-btn sp-value-minus">−</button>
        <input type="text" class="sp-quantity-input sp-entry sp-value" value="1" readonly>
        <button type="button" class="sp-quantity-btn sp-value-plus">+</button>
    </div>
</div>

<style>
    /* Quantity Section */
    .sp-quantity-section {
        margin: 25px 0;
    }

    .sp-quantity-label {
        font-size: 16px;
        color: var(--sp-text-dark);
        margin-bottom: 12px;
        font-weight: 600;
    }

    .sp-quantity-controls {
        display: flex;
        align-items: center;
        gap: 0;
        background: var(--sp-bg-primary);
        border: 2px solid var(--sp-border-medium);
        border-radius: var(--sp-radius-sm);
        width: fit-content;
        overflow: hidden;
    }

    .sp-quantity-btn {
        width: 45px;
        height: 45px;
        background: var(--sp-bg-secondary);
        border: none;
        border-right: 1px solid var(--sp-border-medium);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-weight: bold;
        font-size: 18px;
        color: var(--sp-text-dark);
        transition: all var(--sp-transition-fast);
    }

    .sp-quantity-btn:last-child {
        border-right: none;
        border-left: 1px solid var(--sp-border-medium);
    }

    .sp-quantity-btn:hover {
        background: var(--sp-primary);
        color: white;
        transform: scale(1.05);
    }

    .sp-quantity-input {
        width: 70px;
        height: 45px;
        border: none;
        text-align: center;
        font-weight: 600;
        font-size: 16px;
        background: var(--sp-bg-primary);
        color: var(--sp-text-dark);
    }

    .sp-quantity-input:focus {
        outline: none;
    }

    @media (max-width: 768px) {
        .sp-quantity-controls {
            width: 100%;
            justify-content: center;
        }
    }
</style>