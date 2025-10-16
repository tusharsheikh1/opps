{{-- single-product-partial/attribute-selection.blade.php --}}
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
        <div class="sp-attribute-selection">
            <h4>{{ $attribute->name }} নির্বাচন করুন:</h4>
            @foreach ($attribute_prouct as $attr)
                <div class="form-check" style="display:inline-block">
                    <input id="sp_{{ $attr->vName }}" class="form-check-input get_attri_price" type="radio" name="{{ $attribute->slug }}" value="{{ $attr->vid }}" {{ $attribute_prouct->count() == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="sp_{{ $attr->vName }}">{{ $attr->vName }}</label>
                </div>
            @endforeach
            @push('js')
                <script>
                    $(document).on('click', 'input[type="radio"][name="{{ $attribute->slug }}"]', function(e) {
                        $('input#sp_{{ $attribute->slug }}').val(this.value);
                        $('input#spBuyNow{{ ucfirst($attribute->slug) }}').val(this.value);
                    })
                </script>
            @endpush
        </div>
    @endif
@endforeach

<style>
    /* Attribute Selection */
    .sp-attribute-selection {
        margin: 25px 0;
        padding: 20px;
        background: var(--sp-bg-secondary);
        border-radius: var(--sp-radius-md);
    }

    .sp-attribute-selection h4 {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--sp-text-dark);
    }

    .single-product-container .get_attri_price + label {
        background: var(--sp-bg-primary);
        border: 2px solid var(--sp-border-light);
        padding: 12px 24px;
        margin-right: 12px;
        display: inline-block;
        border-radius: var(--sp-radius-md);
        cursor: pointer;
        margin-bottom: 12px;
        transition: all var(--sp-transition-normal);
        font-weight: 500;
        color: var(--sp-text-dark);
    }

    .single-product-container .get_attri_price + label:hover {
        border-color: var(--sp-primary);
        transform: translateY(-2px);
        box-shadow: var(--sp-shadow-md);
    }

    .single-product-container input[type="radio"].get_attri_price {
        display: none;
    }

    .single-product-container input[type="radio"].get_attri_price:checked + label {
        background: var(--sp-primary);
        color: white;
        border-color: var(--sp-primary);
        transform: translateY(-2px);
        box-shadow: var(--sp-shadow-md);
    }
</style>