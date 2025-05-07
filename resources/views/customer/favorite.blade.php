@extends('layouts.customer')
@section('content')
<div class="container">
    <h2>Favorite Posts List</h2>

    <!-- Filter -->
    <div class="filter-tabs" style="margin-bottom: 20px;">
        <a href="{{ route('customer.favorites') }}?filter=landlord" class="filter-tab {{ $filter === 'landlord' || $filter === '' ? 'active' : '' }}">Landlord</a>
        <a href="{{ route('customer.favorites') }}?filter=customer" class="filter-tab {{ $filter === 'customer' ? 'active' : '' }}">Customer</a>
    </div>

    @if($favorites->isEmpty())
        <p>You have no posts in your favorites list.</p>
    @else
        @foreach($favorites as $favorite)
            @if($favorite->post_type === 'landlord')
                <!-- Format for Landlord Post -->
                <div class="listing" onclick="redirectToDetail({{ $favorite->id }}, 'landlord')" style="cursor: pointer;">
                    <div class="images">
                        @if($favorite->images->isNotEmpty())
                            <img src="{{ asset('storage/' . $favorite->images->first()->image_path) }}" alt="Main image">
                            <div class="grid">
                                @foreach ($favorite->images->slice(1, 3) as $image)
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="Additional image">
                                @endforeach
                            </div>
                        @else
                            <img src="{{ asset('assets/image/placeholder.jpg') }}" alt="No image">
                        @endif
                    </div>
                    <div class="details">
                        <div class="header">
                            <h2>{{ $favorite->title }}</h2>
                        </div>
                        <div class="price">
                            {{ $favorite->price }} VND
                            <span>. {{ $favorite->acreage }} m²</span>
                        </div>
                        <div class="location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $favorite->address }}</span>
                        </div>
                        <p>{{ Str::limit($favorite->description, 150) }}</p>
                        <div class="footer">
                            <div class="profile">
                                <img src="{{ asset('assets/image/userimage.jpg') }}" alt="Profile picture">
                                <div>
                                    <p>{{ $favorite->user->name ?? 'Landlord' }}</p>
                                </div>
                            </div>
                            <div class="actions">
                                <button onclick="event.stopPropagation();">
                                    <i class="fas fa-phone-alt"></i> {{ $favorite->user->phone ?? 'No phone number' }}
                                </button>
                                <button class="favorite-btn" data-post-id="{{ $favorite->id }}" data-post-type="landlord" style="cursor: pointer;" @auth onclick="toggleLike(event)" @else onclick="showLoginAlert()" @endauth>
                                    <i class="fas fa-heart" style="color: {{ $favorite->isFavorited ? 'red' : 'gray' }};"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Format for Customer Post (reused from list_customer_post.blade.php) -->
                <div class="list-post-container">
                    <div class="post-find-roommate">
                        <div class="post-find-roommate-header">
                            <h4>{{ $favorite->user->name }}</h4>
                            <p>{{ $favorite->approved_at ? \Carbon\Carbon::parse($favorite->approved_at)->diffForHumans() : 'Not yet approved' }}</p>
                        </div>
                        <div class="post-find-roommate-content">
                            <h3><strong>{{ $favorite->title }}</strong></h3>
                            <p>{{ $favorite->content }}</p>
                            <p>Contact: {{ $favorite->user->phone }}</p>
                        </div>
                        <div class="post-find-roommate-actions">
                            <div class="like-btn" data-post-id="{{ $favorite->id }}" data-post-type="customer" @auth onclick="toggleLike(event)" @else onclick="showLoginAlert()" @endauth>
                                <i class="fas fa-thumbs-up" style="color: {{ $favorite->isFavorited ? '#1877f2' : '#65676b' }};"></i>
                                <span class="action-text">{{ $favorite->isFavorited ? 'Liked' : 'Like' }}</span>
                                @if($favorite->favoritedby->count() > 0)
                                    <span class="action-count">{{ $favorite->favoritedby->count() }}</span>
                                @endif
                            </div>
                            <button class="comment-toggle" onclick="toggleCommentSection(this)">
                                <i class="far fa-comment"></i>
                                <span class="action-text">Comment</span>
                            </button>
                            <button class="message-btn" @auth onclick="goToChat({{ $favorite->user->id }})" @else onclick="showLoginAlert()" @endauth>
                                <i class="far fa-envelope"></i>
                                <span class="action-text">Message</span>
                            </button>
                        </div>

                        <!-- Comment section -->
                        <div class="post-comments-section comment-section" style="display: none;">
                            @auth
                            <div class="post-comment-form">
                                <div class="comment-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <form action="{{ route('customer.favorite.comments.store', $favorite->id) }}" method="POST" class="comment-form">
                                    @csrf
                                    <input type="hidden" name="post_type" value="customer">
                                    <textarea name="content" placeholder="Write your comment..." required>{{ old('content') }}</textarea>
                                    @if($errors->has('content') && session('failed_post_id') == $favorite->id && !old('parent_id'))
                                    <div class="error-message" style="margin-top: 5px;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $errors->first('content') }}
                                    </div>
                                    @endif
                                    <button type="submit" class="submit-comment">Send</button>
                                </form>
                            </div>
                            @else
                            <div class="login-to-comment">
                                <p>Please <a href="{{ route('login') }}">log in</a> to comment</p>
                            </div>
                            @endauth

                            <!-- Comment list -->
                            <div class="post-comments-list">
                                @foreach ($favorite->comments->whereNull('parent_id')->sortByDesc('created_at') as $comment)
                                <div class="post-comment">
                                    <div class="comment-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="comment-content-wrapper">
                                        <div class="comment-content-header">
                                            <span class="comment-author">{{ $comment->user->name }}</span>
                                            <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="comment-text" id="comment-text-{{ $comment->id }}">
                                            {{ $comment->content }}
                                        </div>

                                        @auth
                                        @if (Auth::id() === $comment->user_id)
                                        <!-- Edit form -->
                                        <form action="{{ route('customer.favorite.comments.update', $comment->id) }}" method="POST"
                                            class="comment-edit-form edit-form" id="edit-form-{{ $comment->id }}" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                            <textarea name="content" required>{{ old('content', $comment->content) }}</textarea>
                                            @if($errors->has('content') && old('comment_id') == $comment->id)
                                            <div class="error-message" style="margin-top: 5px;">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                {{ $errors->first('content') }}
                                            </div>
                                            @endif
                                            <div class="comment-form-actions">
                                                <button type="submit" class="btn-submit">Save</button>
                                                <button type="button" class="btn-cancel"
                                                    onclick="toggleEditForm({{ $comment->id }}, event)">Cancel</button>
                                            </div>
                                        </form>
                                        @endif
                                        @endauth

                                        <div class="comment-actions">
                                            @auth
                                            <button class="btn-reply" onclick="toggleReplyForm({{ $comment->id }})">
                                                <i class="fas fa-reply"></i> Reply
                                            </button>
                                            @if (Auth::id() === $comment->user_id)
                                            <button class="btn-edit" onclick="toggleEditForm({{ $comment->id }}, event)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            @endif
                                            @endauth

                                            @if ($comment->replies->count() > 0)
                                            <button class="btn-view-replies" onclick="toggleReplies({{ $comment->id }})"
                                                id="toggle-btn-{{ $comment->id }}">
                                                <i class="fas fa-comments"></i>
                                                <span class="toggle-text">View {{ $comment->replies->count() }} replies</span>
                                                <i class="fas fa-chevron-down toggle-icon"></i>
                                            </button>
                                            @endif
                                        </div>

                                        <!-- Reply form -->
                                        @auth
                                        <form action="{{ route('customer.favorite.comments.store', $favorite->id) }}" method="POST"
                                            class="comment-reply-form reply-form" id="reply-form-{{ $comment->id }}" style="display: none;">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                            <input type="hidden" name="post_type" value="customer">
                                            <textarea name="content" placeholder="Write your reply..." required>{{ old('content') }}</textarea>
                                            @if($errors->has('content') && session('failed_post_id') == $favorite->id && old('parent_id') == $comment->id)
                                            <div class="error-message" style="margin-top: 5px;">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                {{ $errors->first('content') }}
                                            </div>
                                            @endif
                                            <div class="comment-form-actions">
                                                <button type="submit" class="btn-submit">Send</button>
                                                <button type="button" class="btn-cancel"
                                                    onclick="toggleReplyForm({{ $comment->id }})">Cancel</button>
                                            </div>
                                        </form>
                                        @endauth

                                        <!-- Replies list -->
                                        <div class="comment-replies" id="replies-{{ $comment->id }}" style="display: none;">
                                            @foreach ($comment->replies->sortByDesc('created_at') as $reply)
                                            <div class="post-comment reply">
                                                <div class="comment-avatar">
                                                    <i class="fas fa-user-circle"></i>
                                                </div>
                                                <div class="comment-content-wrapper">
                                                    <div class="comment-content-header">
                                                        <span class="comment-author">{{ $reply->user->name }}</span>
                                                        <span class="comment-time">{{ $reply->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <div class="comment-text" id="comment-text-{{ $reply->id }}">
                                                        @if($reply->parent->user_id !== $reply->user_id)
                                                        <span class="reply-to">Replying to {{ $reply->parent->user->name }}</span><br>
                                                        @endif
                                                        {{ $reply->content }}
                                                    </div>

                                                    @auth
                                                    @if (Auth::id() === $reply->user_id)
                                                    <!-- Edit form for reply -->
                                                    <form action="{{ route('customer.favorite.comments.update', $reply->id) }}" method="POST"
                                                        class="comment-edit-form edit-form" id="edit-form-{{ $reply->id }}"
                                                        style="display: none;">
                                                        @csrf
                                                        @method('PUT')
                                                        <textarea name="content" required>{{ old('content', $reply->content) }}</textarea>
                                                        @if($errors->has('content') && old('comment_id') == $reply->id)
                                                        <div class="error-message" style="margin-top: 5px;">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            {{ $errors->first('content') }}
                                                        </div>
                                                        @endif
                                                        <div class="comment-form-actions">
                                                            <button type="submit" class="btn-submit">Save</button>
                                                            <button type="button" class="btn-cancel"
                                                                onclick="toggleEditForm({{ $reply->id }}, event)">Cancel</button>
                                                        </div>
                                                    </form>
                                                    @endif
                                                    @endauth

                                                    <div class="comment-actions">
                                                        @auth
                                                        <button class="btn-reply" onclick="toggleReplyForm({{ $reply->id }})">
                                                            <i class="fas fa-reply"></i> Reply
                                                        </button>
                                                        @if (Auth::id() === $reply->user_id)
                                                        <button class="btn-edit" onclick="toggleEditForm({{ $reply->id }}, event)">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        @endif
                                                        @endauth

                                                        @if ($reply->replies->count() > 0)
                                                        <button class="btn-view-replies" onclick="toggleReplies({{ $reply->id }})"
                                                            id="toggle-btn-{{ $reply->id }}">
                                                            <i class="fas fa-comments"></i>
                                                            <span class="toggle-text">View {{ $reply->replies->count() }} replies</span>
                                                            <i class="fas fa-chevron-down toggle-icon"></i>
                                                        </button>
                                                        @endif
                                                    </div>

                                                    <!-- Reply form for reply -->
                                                    @auth
                                                    <form action="{{ route('customer.favorite.comments.store', $favorite->id) }}" method="POST"
                                                        class="comment-reply-form reply-form" id="reply-form-{{ $reply->id }}"
                                                        style="display: none;">
                                                        @csrf
                                                        <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                                        <input type="hidden" name="post_type" value="customer">
                                                        <textarea name="content" placeholder="Write your reply..." required>{{ old('content') }}</textarea>
                                                        @if($errors->has('content') && session('failed_post_id') == $favorite->id && old('parent_id') == $reply->id)
                                                        <div class="error-message" style="margin-top: 5px;">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            {{ $errors->first('content') }}
                                                        </div>
                                                        @endif
                                                        <div class="comment-form-actions">
                                                            <button type="submit" class="btn-submit">Send</button>
                                                            <button type="button" class="btn-cancel"
                                                                onclick="toggleReplyForm({{ $reply->id }})">Cancel</button>
                                                        </div>
                                                    </form>
                                                    @endauth

                                                    <!-- Nested replies -->
                                                    <div class="comment-replies" id="replies-{{ $reply->id }}"
                                                        style="display: none;">
                                                        @foreach ($reply->replies->sortByDesc('created_at') as $nestedReply)
                                                        <div class="post-comment nested-reply">
                                                            <div class="comment-avatar">
                                                                <i class="fas fa-user-circle"></i>
                                                            </div>
                                                            <div class="comment-content-wrapper">
                                                                <div class="comment-content-header">
                                                                    <span class="comment-author">{{ $nestedReply->user->name }}</span>
                                                                    <span class="comment-time">{{ $nestedReply->created_at->diffForHumans() }}</span>
                                                                </div>
                                                                <div class="comment-text" id="comment-text-{{ $nestedReply->id }}">
                                                                    @if($nestedReply->parent->user_id !== $nestedReply->user_id)
                                                                    <span class="reply-to">Replying to {{ $nestedReply->parent->user->name }}</span><br>
                                                                    @endif
                                                                    {{ $nestedReply->content }}
                                                                </div>

                                                                @auth
                                                                @if (Auth::id() === $nestedReply->user_id)
                                                                <!-- Edit form for nested reply -->
                                                                <form action="{{ route('customer.favorite.comments.update', $nestedReply->id) }}"
                                                                    method="POST" class="comment-edit-form edit-form" id="edit-form-{{ $nestedReply->id }}"
                                                                    style="display: none;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <textarea name="content" required>{{ old('content', $nestedReply->content) }}</textarea>
                                                                    @if($errors->has('content') && old('comment_id') == $nestedReply->id)
                                                                    <div class="error-message" style="margin-top: 5px;">
                                                                        <i class="fas fa-exclamation-triangle"></i>
                                                                        {{ $errors->first('content') }}
                                                                    </div>
                                                                    @endif
                                                                    <div class="comment-form-actions">
                                                                        <button type="submit" class="btn-submit">Save</button>
                                                                        <button type="button" class="btn-cancel"
                                                                            onclick="toggleEditForm({{ $nestedReply->id }}, event)">Cancel</button>
                                                                    </div>
                                                                </form>
                                                                @endif
                                                                @endauth
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif
</div>

@endsection

@section('styles')
    <style>
        .filter-tabs {
            display: flex;
            gap: 10px;
        }
        .filter-tab {
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #f0f2f5;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: all 0.2s;
        }
        .filter-tab:hover {
            background-color: #e4e6eb;
        }
        .filter-tab.active {
            background-color: #1877f2;
            color: white;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/listpost.css') }}">
@endsection

@section('scripts')
<script>
    function redirectToDetail(postId, postType) {
        if (postType === 'landlord') {
            window.location.href = "{{ route('customer.post.detail', '') }}/" + postId;
        } else {
            // Can redirect to customer post detail page if available
            window.location.href = "{{ route('customer.roommates.index') }}";
        }
    }

    function showLoginAlert() {
        alert("Please log in to perform this action!");
        window.location.href = "{{ route('login') }}";
    }

    function goToChat(userId) {
        window.location.href = "{{ route('customer.chat.user', ':userId') }}".replace(':userId', userId);
    }

    async function toggleLike(event) {
        const button = event.currentTarget;
        const postId = button.getAttribute('data-post-id');
        const postType = button.getAttribute('data-post-type');
        const icon = button.querySelector('i');
        const actionText = button.querySelector('.action-text');
        const actionCount = button.querySelector('.action-count');

        try {
            const response = await fetch("{{ route('customer.favorite.toggleFavorite') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ 
                    post_id: postId,
                    post_type: postType
                })
            });

            const data = await response.json();

            if (data.status === "error") {
                alert(data.message);
                return;
            }

            if (data.status === "added") {
                icon.style.color = postType === 'customer' ? "#1877f2" : "red";
                actionText ? actionText.textContent = "Liked" : null;
                button.classList.add('active');

                if (postType === 'customer') {
                    if (actionCount) {
                        actionCount.textContent = data.likes_count;
                    } else {
                        const countSpan = document.createElement('span');
                        countSpan.className = 'action-count';
                        countSpan.textContent = data.likes_count;
                        button.appendChild(countSpan);
                    }
                }
            } else if (data.status === "removed") {
                icon.style.color = postType === 'customer' ? "#65676b" : "gray";
                actionText ? actionText.textContent = "Like" : null;
                button.classList.remove('active');

                if (postType === 'customer' && actionCount) {
                    if (data.likes_count > 0) {
                        actionCount.textContent = data.likes_count;
                    } else {
                        actionCount.remove();
                    }
                }
            }
        } catch (error) {
            console.error("Error:", error);
        }
    }

    function toggleCommentSection(button) {
        const commentSection = button.closest('.post-find-roommate').querySelector('.comment-section');
        button.classList.toggle('active');
        commentSection.style.display = commentSection.style.display === 'none' ? 'block' : 'none';
    }

    function toggleReplyForm(commentId) {
        const form = document.getElementById("reply-form-" + commentId);
        if (form.style.display === "none" || form.style.display === "") {
            document.querySelectorAll('.comment-reply-form').forEach(f => {
                if (f.id !== 'reply-form-' + commentId) f.style.display = 'none';
            });
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }

    function toggleEditForm(commentId, event = null) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        const form = document.getElementById('edit-form-' + commentId);
        if (form) {
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
            if (form.style.display === 'block') {
                const textarea = form.querySelector('textarea');
                if (textarea) {
                    textarea.focus();
                    textarea.setSelectionRange(textarea.value.length, textarea.value.length);
                }
            }
        }
    }

    function toggleReplies(commentId) {
        const repliesDiv = document.getElementById("replies-" + commentId);
        const toggleBtn = document.getElementById("toggle-btn-" + commentId);

        if (!repliesDiv || !toggleBtn) return;

        const toggleIcon = toggleBtn.querySelector('.toggle-icon');
        const toggleText = toggleBtn.querySelector('.toggle-text');

        if (repliesDiv.style.display === "none" || repliesDiv.style.display === "") {
            repliesDiv.style.display = "block";
            if (toggleText) toggleText.textContent = "Collapse";
            if (toggleIcon) {
                toggleIcon.classList.remove('fa-chevron-down');
                toggleIcon.classList.add('fa-chevron-up');
            }
        } else {
            repliesDiv.style.display = "none";
            if (toggleText) toggleText.textContent = "View " + repliesDiv.querySelectorAll('.post-comment').length + " replies";
            if (toggleIcon) {
                toggleIcon.classList.remove('fa-chevron-up');
                toggleIcon.classList.add('fa-chevron-down');
            }
        }
    }

    // Check banned words
    const bannedWords = @json(\App\Models\BannedWord::pluck('word')->toArray());

    function checkBannedWords(content) {
        const lowerContent = content.toLowerCase().normalize('NFC');
        const foundWords = bannedWords.filter(word => {
            const regex = new RegExp('\\b' + word.toLowerCase().normalize('NFC') + '\\b', 'i');
            return regex.test(lowerContent);
        });
        return foundWords;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const allForms = document.querySelectorAll('.comment-form, .reply-form, .edit-form');

        allForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const textarea = this.querySelector('textarea[name="content"]');
                const content = textarea.value;
                const foundWords = checkBannedWords(content);

                if (foundWords.length > 0) {
                    alert(`Content contains inappropriate words: ${foundWords.join(', ')}`);
                    textarea.focus();
                    return false;
                }

                this.submit();
            });
        });
    });
</script>
@endsection

<style>
    /* Enhanced Filter Tabs */
.filter-tabs {
    display: flex;
    gap: 15px;
    margin-bottom: 25px;
    padding: 5px;
    background-color: #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 20px;
    z-index: 10;
}

.filter-tab {
    flex: 1;
    padding: 12px 20px;
    border-radius: 8px;
    background-color: transparent;
    text-decoration: none;
    color: #4b5563;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

/* Add icons to filter tabs */
.filter-tab[href*="filter=landlord"]::before {
    content: "\f015"; /* Home icon */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
}

.filter-tab[href*="filter=customer"]::before {
    content: "\f007"; /* User icon */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
}

.filter-tab:hover {
    background-color: #cbd5e0;
    color: #1f2937;
    transform: translateY(-2px);
}

.filter-tab.active {
    background-color: #3b82f6;
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* Add animation effect */
.filter-tab::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 3px;
    background-color: #3b82f6;
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.filter-tab:hover::after {
    width: 80%;
}

.filter-tab.active::after {
    width: 90%;
    background-color: white;
}

/* Count badge for each filter */
.filter-tab .count-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    border-radius: 10px;
    background-color: #e5e7eb;
    color: #4b5563;
    font-size: 12px;
    font-weight: 700;
    margin-left: 5px;
}

.filter-tab.active .count-badge {
    background-color: white;
    color: #3b82f6;
}

/* Responsive styles */
@media (max-width: 768px) {
    .filter-tabs {
        position: sticky;
        top: 10px;
        padding: 3px;
        gap: 8px;
    }
    
    .filter-tab {
        padding: 10px 15px;
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .filter-tab::before {
        font-size: 16px;
    }
    
    .filter-tab span {
        display: none; /* Hide text on very small screens, show only icons */
    }
    
    .filter-tab .count-badge {
        margin-left: 0;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .filter-tabs {
        background-color: #2d3748;
    }
    
    .filter-tab {
        color: #e2e8f0;
    }
    
    .filter-tab:hover {
        background-color: #4a5568;
        color: white;
    }
    
    .filter-tab .count-badge {
        background-color: #4a5568;
        color: #e2e8f0;
    }
}
</style>