{{-- single-product-partial/secondary-actions.blade.php --}}
<div class="sp-secondary-actions">
    @php($typeid = $product->slug)
    @if (App\Models\wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->first())
        @php($k = App\Models\wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->first())
        <a class="sp-wishlist-btn" href="{{ route('wishlist.remove', ['item' => $k->id]) }}">
            <i class="fal fa-heart-broken" aria-hidden="true"></i> ইচ্ছেতালিকা থেকে সরান
        </a>
    @else
        <form style="display: inline;" action="{{ route('wishlist.add') }}" method="post">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->slug }}">
            <button class="sp-wishlist-btn" type="submit">
                <i class="fal fa-heart" aria-hidden="true"></i> ইচ্ছেতালিকায় যোগ করুন
            </button>
        </form>
    @endif

    <!-- Share Dropdown -->
    <div class="dropdown">
        <button class="sp-share-btn dropdown-toggle" type="button" id="spDropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="icofont icofont-share"></i> শেয়ার করুন
        </button>
        <div class="dropdown-menu" aria-labelledby="spDropdownMenuButton">
            <div class="sp-share-groups">
                <div>Share:</div>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ Request::url() }}">
                    <span style="background-color: rgb(24, 119, 242);">
                        <svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="20" height="20">
                            <path fill="#FFF" d="M17.78 27.5V17.008h3.522l.527-4.09h-4.05v-2.61c0-1.182.33-1.99 2.023-1.99h2.166V4.66c-.375-.05-1.66-.16-3.155-.16-3.123 0-5.26 1.905-5.26 5.405v3.016h-3.53v4.09h3.53V27.5h4.223z"></path>
                        </svg>
                    </span>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ Request::url() }}&text=">
                    <span style="background-color: rgb(29, 155, 240);">
                        <svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="20" height="20">
                            <path fill="#FFF" d="M28 8.557a9.913 9.913 0 01-2.828.775 4.93 4.93 0 002.166-2.725 9.738 9.738 0 01-3.13 1.194 4.92 4.92 0 00-3.593-1.55 4.924 4.924 0 00-4.794 6.049c-4.09-.21-7.72-2.17-10.15-5.15a4.942 4.942 0 00-.665 2.477c0 1.71.87 3.214 2.19 4.1a4.968 4.968 0 01-2.23-.616v.06c0 2.39 1.7 4.38 3.952 4.83-.414.115-.85.174-1.297.174-.318 0-.626-.03-.928-.086a4.935 4.935 0 004.6 3.42 9.893 9.893 0 01-6.114 2.107c-.398 0-.79-.023-1.175-.068a13.953 13.953 0 007.55 2.213c9.056 0 14.01-7.507 14.01-14.013 0-.213-.005-.426-.015-.637.96-.695 1.795-1.56 2.455-2.55z"></path>
                        </svg>
                    </span>
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ Request::url() }}">
                    <span style="background-color: rgb(0, 123, 181);">
                        <svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="20" height="20">
                            <path d="M6.227 12.61h4.19v13.48h-4.19V12.61zm2.095-6.7a2.43 2.43 0 010 4.86c-1.344 0-2.428-1.09-2.428-2.43s1.084-2.43 2.428-2.43m4.72 6.7h4.02v1.84h.058c.56-1.058 1.927-2.176 3.965-2.176 4.238 0 5.02 2.792 5.02 6.42v7.395h-4.183v-6.56c0-1.564-.03-3.574-2.178-3.574-2.18 0-2.514 1.7-2.514 3.46v6.668h-4.187V12.61z" fill="#FFF"></path>
                        </svg>
                    </span>
                </a>
                <a href="https://www.addtoany.com/add_to/whatsapp?linkurl={{ Request::url() }}&linknote=">
                    <span style="background-color: rgb(18, 175, 10);">
                        <svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="20" height="20">
                            <path fill-rule="evenodd" clip-rule="evenodd" fill="#FFF" d="M16.21 4.41C9.973 4.41 4.917 9.465 4.917 15.7c0 2.134.592 4.13 1.62 5.832L4.5 27.59l6.25-2.002a11.241 11.241 0 005.46 1.404c6.234 0 11.29-5.055 11.29-11.29 0-6.237-5.056-11.292-11.29-11.292zm0 20.69c-1.91 0-3.69-.57-5.173-1.553l-3.61 1.156 1.173-3.49a9.345 9.345 0 01-1.79-5.512c0-5.18 4.217-9.4 9.4-9.4 5.183 0 9.397 4.22 9.397 9.4 0 5.188-4.214 9.4-9.398 9.4zm5.293-6.832c-.284-.155-1.673-.906-1.934-1.012-.265-.106-.455-.16-.658.12s-.78.91-.954 1.096c-.176.186-.345.203-.628.048-.282-.154-1.2-.494-2.264-1.517-.83-.795-1.373-1.76-1.53-2.055-.158-.295 0-.445.15-.584.134-.124.3-.326.45-.488.15-.163.203-.28.306-.47.104-.19.06-.36-.005-.506-.066-.147-.59-1.587-.81-2.173-.218-.586-.46-.498-.63-.505-.168-.007-.358-.038-.55-.045-.19-.007-.51.054-.78.332-.277.274-1.05.943-1.1 2.362-.055 1.418.926 2.826 1.064 3.023.137.2 1.874 3.272 4.76 4.537 2.888 1.264 2.9.878 3.43.85.53-.027 1.734-.633 2-1.297.266-.664.287-1.24.22-1.363-.07-.123-.26-.203-.54-.357z"></path>
                        </svg>
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* Secondary Actions */
    .sp-secondary-actions {
        display: flex;
        gap: 15px;
        margin: 25px 0;
        flex-wrap: wrap;
    }

    .sp-wishlist-btn, .sp-share-btn {
        background: var(--sp-bg-primary);
        border: 2px solid var(--sp-border-light);
        color: var(--sp-text-dark);
        padding: 12px 20px;
        border-radius: var(--sp-radius-md);
        text-decoration: none;
        font-weight: 500;
        transition: all var(--sp-transition-normal);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sp-wishlist-btn:hover, .sp-share-btn:hover {
        border-color: var(--sp-accent);
        color: var(--sp-accent);
        transform: translateY(-2px);
        box-shadow: var(--sp-shadow-md);
        text-decoration: none;
    }

    /* Share Dropdown */
    .sp-share-groups {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px 20px;
    }

    .sp-share-groups span {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--sp-radius-sm);
        transition: transform var(--sp-transition-normal);
        cursor: pointer;
    }

    .sp-share-groups span:hover {
        transform: scale(1.15);
    }

    @media (max-width: 768px) {
        .sp-secondary-actions {
            flex-direction: column;
        }
    }