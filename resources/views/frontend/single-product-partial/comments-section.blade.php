{{-- single-product-partial/comments-section.blade.php --}}
<div id="spComments" class="collapse" aria-labelledby="spHeadingThree" data-parent="#spAccordion">
    <div class="sp-accordion-content">
        @forelse ($product->comments->where('parent_id',null) as $comment)
            <div class="sp-comment-item">
                <div class="sp-review-header">
                    @if($comment->user)
                        <img class="sp-review-avatar" src="{{ $comment->user->avatar == 'default.png' ? '/default/default.png' : '/uploads/admin/' . $comment->user->avatar }}" alt="">
                        <div>
                            <div class="sp-reviewer-name">{{ $comment->user->name }}</div>
                            <small style="color: var(--sp-text-light);">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                    @else
                        <div>
                            <div class="sp-reviewer-name">Anonymous User</div>
                            <small style="color: var(--sp-text-light);">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                    @endif
                    @if (auth()->check() && auth()->user()->role_id == 1)
                        <div style="margin-left: auto;">
                            @auth
                                <a href="javascript:void(0)" id="sp-comment-reply" data-id="{{ $comment->id }}" data-slug="{{ $product->slug }}" style="color: var(--sp-primary); text-decoration: none;">
                                    <i class="fa fa-reply"></i> Reply
                                </a>
                            @endauth
                        </div>
                    @endif
                </div>
                <p>{{ $comment->body }}</p>
                
                @forelse ($comment->replies as $reply)
                    <div class="sp-comment-item" style="margin-left: 40px; margin-top: 15px; background: var(--sp-bg-light);">
                        <div class="sp-review-header">
                            @if($reply->user)
                                <img class="sp-review-avatar" src="{{ $reply->user->avatar == 'default.png' ? '/default/default.png' : '/uploads/admin/' . $reply->user->avatar }}" alt="">
                                <div>
                                    <div class="sp-reviewer-name">{{ $reply->user->name }}</div>
                                    <small style="color: var(--sp-text-light);">{{ $reply->created_at->diffForHumans() }}</small>
                                </div>
                            @else
                                <div>
                                    <div class="sp-reviewer-name">Anonymous User</div>
                                    <small style="color: var(--sp-text-light);">{{ $reply->created_at->diffForHumans() }}</small>
                                </div>
                            @endif
                        </div>
                        <p>{{ $reply->body }}</p>
                    </div>
                @empty
                @endforelse
                
                <div class="sp-reply-box"></div>
            </div>
        @empty
            <div class="text-center" style="padding: 40px;">
                <i class="far fa-question-circle" style="font-size: 4rem; color: var(--sp-text-light); margin-bottom: 20px;"></i>
                <h3 style="color: var(--sp-text-light);">No Questions Yet</h3>
                <p style="color: var(--sp-text-light);">Be the first to ask a question about this book!</p>
            </div>
        @endforelse

        @auth
            <form class="sp-comment-form" action="{{ route('comment', $product->slug) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="spComment" style="font-weight: 600; margin-bottom: 10px;"><strong>Ask Your Question</strong></label>
                    <textarea class="form-control" name="comment" id="spComment" placeholder="What would you like to know about this book?" required></textarea>
                    @error('comment')
                        <small class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="sp-btn-buy-now">
                    <i class="fas fa-paper-plane"></i> Submit Question
                </button>
            </form>
        @endauth
    </div>
</div>

<style>
    /* Comment Section */
    .sp-comment-item {
        background: var(--sp-bg-primary);
        border-radius: var(--sp-radius-md);
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: var(--sp-shadow-sm);
        border: 1px solid var(--sp-border-light);
        transition: all var(--sp-transition-normal);
    }

    .sp-comment-item:hover {
        box-shadow: var(--sp-shadow-md);
        transform: translateY(-1px);
    }

    .sp-comment-form {
        background: var(--sp-bg-secondary);
        padding: 30px;
        border-radius: var(--sp-radius-md);
        margin-top: 30px;
        border: 1px solid var(--sp-border-light);
    }

    .sp-comment-form textarea {
        width: 100%;
        border: 2px solid var(--sp-border-light);
        border-radius: var(--sp-radius-md);
        padding: 15px;
        font-family: inherit;
        resize: vertical;
        min-height: 120px;
        transition: all var(--sp-transition-normal);
    }

    .sp-comment-form textarea:focus {
        outline: none;
        border-color: var(--sp-primary);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
</style>