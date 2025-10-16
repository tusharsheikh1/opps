{{-- single-product-partial/stock-status.blade.php --}}
@if ($product->quantity > 0)
    <div class="sp-stock-status">
        <div class="sp-stock-icon">
            <i class="fas fa-check"></i>
        </div>
        <span><strong>স্টক আছে</strong> ({{ $product->quantity }} কপি বাকি)</span>
    </div>
@else
    <div class="sp-stock-status" style="background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%); color: white;">
        <div class="sp-stock-icon" style="background: #e74c3c;">
            <i class="fas fa-times"></i>
        </div>
        <span><strong>স্টক শেষ</strong></span>
    </div>
@endif

<style>
    /* Stock Status */
    .sp-stock-status {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 20px 0;
        padding: 12px 16px;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border-radius: var(--sp-radius-md);
        border: 1px solid #c3e6cb;
        font-size: 14px;
    }

    .sp-stock-icon {
        width: 20px;
        height: 20px;
        background: var(--sp-success);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        box-shadow: 0 2px 4px rgba(39, 174, 96, 0.3);
    }