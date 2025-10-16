{{-- single-product-partial/related-sidebar.blade.php --}}
<div class="col-md-3">
    <div class="sp-related-sidebar">
        <h3>সম্পর্কিত বই</h3>
        <?php
        foreach ($product->categories as $s) {
            $ppis = $s->id;
        }
        
        $productIds = DB::table('category_product')->where('category_id', $ppis)->get()->pluck('product_id');
        $products = App\Models\Product::whereIn('id', $productIds)->where('status', true)->inRandomOrder()->take(5)->get();
        ?>

        @foreach ($products as $producter)
            <x-single-related :product="$producter" classes="" />
        @endforeach
    </div>
</div>

<style>
    /* Related Products Sidebar */
    .sp-related-sidebar {
        background: var(--sp-bg-primary);
        border-radius: var(--sp-radius-lg);
        padding: 25px;
        box-shadow: var(--sp-shadow-md);
        height: fit-content;
        position: sticky;
        top: 20px;
        border: 1px solid var(--sp-border-light);
    }

    .sp-related-sidebar h3 {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--sp-text-dark);
        margin-bottom: 20px;
        border-bottom: 3px solid var(--sp-primary);
        padding-bottom: 12px;
        position: relative;
    }

    .sp-related-sidebar h3::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        width: 50px;
        height: 3px;
        background: var(--sp-accent);
    }

    @media (max-width: 768px) {
        .sp-related-sidebar {
            margin-top: 30px;
            position: static;
        }
    }
</style>