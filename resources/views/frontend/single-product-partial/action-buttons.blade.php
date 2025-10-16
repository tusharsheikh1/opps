{{-- single-product-partial/action-buttons.blade.php --}}
<div class="sp-action-buttons">
    @if ($product->download_able == 1)
        <style>.sp-gcg { display: none; }</style>
    @endif
    
    <!-- Add to Cart -->
    @if ($product->quantity > 0)
        <div class="sp-gcg">
            <form action="{{ route('add.cart') }}" method="post" id="spAddToCart" style="display: inline;">
                @csrf
                <fieldset>
                    <?php if(isset($campaigns_product)){?>
                    <input type="hidden" name="camp" id="sp_camp" value="{{ $campaigns_product->id }}">
                    <?php }?>
                    <input type="hidden" name="id" id="sp_id" value="{{ $product->id }}">
                    <input type="hidden" name="qty" id="sp_qty" value="1">
                    <input type="hidden" name="color" id="sp_color" value="blank">
                    @foreach ($attributes as $attribute)
                        <?php
                        $attribute_prouct = DB::table('attribute_product')
                            ->select('*')
                            ->join('attribute_values', 'attribute_values.id', '=', 'attribute_product.attribute_value_id')
                            ->addselect('attribute_values.name as vName')
                            ->addselect('attribute_values.id as vid')
                            ->join('attributes', 'attributes.id', '=', 'attribute_values.attributes_id')
                            ->where('attribute_product.product_id', $product->id)
                            ->where('attributes.id', $attribute->id)
                            ->get();
                        ?>
                        @if ($attribute_prouct->count() > 0)
                            @foreach ($attribute_prouct as $attr)
                                <?php $vid = $attr->vid; ?>
                            @endforeach
                            <input type="hidden" name="{{ $attribute->slug }}" id="sp_{{ $attribute->slug }}" value="{{ $attribute_prouct->count() == 1 ? $vid : 'blank' }}">
                        @endif
                    @endforeach
                    @if ($product->sheba != 1)
                        <button class="sp-btn-cart" type="submit" name="submit">
                            <i class="fal fa-shopping-cart"></i> কার্টে যোগ করুন
                        </button>
                    @endif
                </fieldset>
            </form>
        </div>
    @endif

    <!-- Buy Now Button -->
    <div class="sp-gcg buy-now-section">
        <form action="{{ route('buy.product') }}" method="GET" id="spBuyNowForm" style="display: inline;">
            <?php if(isset($campaigns_product)){?>
            <input type="hidden" name="camp" value="{{ $campaigns_product->id }}">
            <?php }?>
            <fieldset>
                <?php if (isset($campaigns_product)) {
                    $product->discount_price = $campaigns_product->price;
                } ?>
                @if (!empty($product->discount_price))
                    <input type="hidden" name="just" id="spBuyNowJust" value="{{ $product->discount_price }}">
                    <input type="hidden" name="dynamic_price" id="spBuyNowDynamicPrice" value="{{ $product->discount_price }}">
                @else
                    <input type="hidden" name="just" id="spBuyNowJust" value="{{ $product->regular_price }}">
                    <input type="hidden" name="dynamic_price" id="spBuyNowDynamicPrice" value="{{ $product->regular_price }}">
                @endif
                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="qty" id="spBuyNowQty" value="1">
                <input type="hidden" name="color" id="spBuyNowColor" value="blank">
                @foreach ($attributes as $attribute)
                    <?php
                    $attribute_prouct = DB::table('attribute_product')
                        ->select('*')
                        ->join('attribute_values', 'attribute_values.id', '=', 'attribute_product.attribute_value_id')
                        ->addselect('attribute_values.name as vName')
                        ->addselect('attribute_values.id as vid')
                        ->join('attributes', 'attributes.id', '=', 'attribute_values.attributes_id')
                        ->where('attribute_product.product_id', $product->id)
                        ->where('attributes.id', $attribute->id)
                        ->get();
                    ?>
                    @if ($attribute_prouct->count() > 0)
                        @foreach ($attribute_prouct as $attr)
                            <?php $vid = $attr->vid; ?>
                        @endforeach
                        <input type="hidden" name="{{ $attribute->slug }}" id="spBuyNow{{ ucfirst($attribute->slug) }}" value="{{ $attribute_prouct->count() == 1 ? $vid : 'blank' }}">
                    @endif
                @endforeach
                @if ($product->quantity <= 0)
                    <div class="sp-out-of-stock">স্টক শেষ</div>
                @else
                    <div class="sticky-buy-fixed">
                        <button class="sp-btn-buy-now" type="submit">
                            <i class="fas fa-shopping-bag"></i> এখনি কিনুন
                        </button>
                    </div>
                @endif
            </fieldset>
        </form>
    </div>
</div>

<style>
    /* Action Buttons */
    .sp-action-buttons {
        display: flex;
        gap: 15px;
        margin: 30px 0;
        flex-wrap: wrap;
    }

    .sp-btn-buy-now {
        background: linear-gradient(135deg, var(--sp-pink), #ad1457);
        color: white;
        border: none;
        padding: 16px 32px;
        font-size: 16px;
        font-weight: 600;
        border-radius: var(--sp-radius-md);
        cursor: pointer;
        transition: all var(--sp-transition-normal);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-width: 220px;
        box-shadow: 0 4px 15px rgba(233, 30, 99, 0.4);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-decoration: none;
    }

    .sp-btn-buy-now:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(233, 30, 99, 0.5);
        color: white;
        text-decoration: none;
    }

    .sp-btn-cart {
        background: var(--sp-bg-primary);
        color: var(--sp-primary);
        border: 2px solid var(--sp-primary);
        padding: 14px 28px;
        font-size: 15px;
        font-weight: 600;
        border-radius: var(--sp-radius-md);
        cursor: pointer;
        transition: all var(--sp-transition-normal);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 180px;
    }

    .sp-btn-cart:hover {
        background: var(--sp-primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--sp-shadow-md);
    }

    /* Out of Stock */
    .sp-out-of-stock {
        background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);
        color: white;
        padding: 18px;
        text-align: center;
        border-radius: var(--sp-radius-md);
        font-weight: 600;
        font-size: 16px;
        box-shadow: var(--sp-shadow-md);
    }

    /* Loading States */
    .sp-btn-buy-now:disabled, .sp-btn-cart:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sp-action-buttons {
            flex-direction: column;
            gap: 12px;
        }
        
        .sp-btn-buy-now, .sp-btn-cart {
            width: 100%;
            min-width: 100%;
            justify-content: center;
        }
    }
</style>