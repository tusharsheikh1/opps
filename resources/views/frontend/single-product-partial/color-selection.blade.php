{{-- single-product-partial/color-selection.blade.php --}}
@if ($product->colors->count() != 0)
    <div class="sp-color-selection">
        <h4>রং নির্বাচন করুন:</h4>
        <div class="btn-group btn-group-toggle sp-btn-color-group d-block" data-toggle="buttons">
            @foreach ($colors_product as $color)
                <label class="btn color {{ $product->colors->count() == 1 ? 'active' : '' }}" style="background: {{ $color->code }}">
                    @if ($product->images)
                        @foreach ($product->images as $image)
                            @if ($image->color_attri == $color->slug)
                                <img src="{{ asset('uploads/product/' . $image->name) }}" style="height: 70px;width: 70px;object-fit: contain;border: 5px solid white;background: white;" alt="" draggable="true">
                            @endif
                        @endforeach
                    @endif
                    <input type="radio" name="color" autocomplete="off" value="{{ $color->slug }}" {{ $product->colors->count() == 1 ? 'checked' : '' }}>
                </label>
            @endforeach
        </div>
    </div>
@endif

<style>
    /* Color Selection */
    .sp-color-selection {
        margin: 25px 0;
        padding: 20px;
        background: var(--sp-bg-secondary);
        border-radius: var(--sp-radius-md);
    }

    .sp-color-selection h4 {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--sp-text-dark);
    }

    .sp-btn-color-group .color {
        border: 3px solid transparent;
        border-radius: var(--sp-radius-md);
        transition: all var(--sp-transition-normal);
        overflow: hidden;
        margin: 8px;
        cursor: pointer;
    }

    .sp-btn-color-group .color:hover {
        transform: translateY(-4px);
        box-shadow: var(--sp-shadow-lg);
    }

    .sp-btn-color-group .color.active {
        border-color: var(--sp-primary);
        transform: translateY(-4px);
        box-shadow: var(--sp-shadow-lg);
    }

    .sp-btn-color-group .color img {
        border-radius: var(--sp-radius-sm);
        transition: transform var(--sp-transition-normal);
    }
</style>