{{-- single-product-partial/similar-products.blade.php --}}
<div class="sp-similar-products-section">
    <h3>📖 একই বিভাগের অন্যান্য বই</h3>
    <?php
    foreach ($product->categories as $s) {
        $ppis = $s->id;
    }
    
    $productIds = DB::table('category_product')->where('category_id', $ppis)->get()->pluck('product_id');
    $products = App\Models\Product::whereIn('id', $productIds)->where('status', true)->inRandomOrder()->take(18)->get();
    ?>

    <div class="row">
        @forelse ($products as $product_item)
            <x-product-grid-view :product="$product_item" classes="" />
        @empty
            <x-product-empty-component />
        @endforelse
    </div>
</div>

<style>
    /* Similar Products Section */
    .sp-similar-products-section {
        background: var(--sp-bg-primary);
        border-radius: var(--sp-radius-lg);
        box-shadow: var(--sp-shadow-md);
        padding: 35px;
        margin-top: 30px;
        border: 1px solid var(--sp-border-light);
    }

    .sp-similar-products-section h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--sp-text-dark);
        margin-bottom: 30px;
        text-align: center;
        position: relative;
        padding-bottom: 15px;
    }

    .sp-similar-products-section h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: linear-gradient(135deg, var(--sp-primary) 0%, var(--sp-accent) 100%);
        border-radius: 2px;
    }

    @media (max-width: 768px) {
        .sp-similar-products-section {
            margin: 20px 5px;
        }
    }
</style>