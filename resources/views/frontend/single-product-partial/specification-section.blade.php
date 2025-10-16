{{-- single-product-partial/specification-section.blade.php --}}
@if ($product->book == 1 && $product->author)
    <div id="spSpecification" class="collapse" aria-labelledby="spHeadingThree" data-parent="#spAccordion">
        <div class="sp-accordion-content">
            <table class="sp-spec-table">
                <tr><th>Title</th><td>{{ $product->title }}</td></tr>
                <tr><th>Author</th><td><a href="{{ route('author.product', ['slug' => $product->author_id]) }}">{{ $product->author->name }}</a></td></tr>
                <tr><th>Publisher</th><td>
                    @if($product->user && $product->user->shop_info)
                        <a href="{{ route('vendor', $product->user->shop_info->slug) }}">{{ $product->user->shop_info->name }}</a>
                    @else
                        Not Available
                    @endif
                </td></tr>
                <tr><th>ISBN</th><td>{{ $product->isbn }}</td></tr>
                <tr><th>Edition</th><td>{{ $product->edition }}</td></tr>
                <tr><th>Number of Pages</th><td>{{ $product->pages }}</td></tr>
                <tr><th>Country</th><td>{{ $product->country }}</td></tr>
                <tr><th>Language</th><td>{{ $product->language }}</td></tr>
            </table>
        </div>
    </div>
@endif

<style>
    /* Specification Table */
    .sp-spec-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        border-radius: var(--sp-radius-md);
        overflow: hidden;
        box-shadow: var(--sp-shadow-sm);
    }

    .sp-spec-table th {
        background: var(--sp-bg-secondary);
        padding: 18px;
        text-align: left;
        font-weight: 600;
        color: var(--sp-text-dark);
        width: 200px;
        border-bottom: 1px solid var(--sp-border-light);
    }

    .sp-spec-table td {
        padding: 18px;
        border-bottom: 1px solid var(--sp-border-light);
        background: var(--sp-bg-primary);
    }

    .sp-spec-table tr:last-child td,
    .sp-spec-table tr:last-child th {
        border-bottom: none;
    }

    @media (max-width: 768px) {
        .sp-spec-table th {
            width: 150px;
            padding: 12px;
        }

        .sp-spec-table td {
            padding: 12px;
        }
    }
</style>