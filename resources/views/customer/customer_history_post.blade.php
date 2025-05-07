@extends('layouts.customer')

@section('content')
<div class="container">
    <h1>Post History</h1>

    <!-- Global alert (for errors not related to specific forms) -->
    @if($errors->any() && !session('failed_post_id') && !old('parent_id') && !old('comment_id'))
    <div class="error-message">
        <i class="fas fa-exclamation-triangle"></i>
        {{ $errors->first('content') }}
    </div>
    @endif

    @if(session('success'))
    <div class="success-message">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="error-message">
        <i class="fas fa-exclamation-triangle"></i>
        {{ session('error') }}
    </div>
    @endif

    @foreach($posts as $post)
    <div class="list-post-container">
        <div class="post-find-roommate">
            <div class="post-find-roommate-header">
                <h4>{{ $post->user->name }}</h4>
                <div class="post-status-info">
                    <p>
                        <span class="status-label">Status: </span>
                        <span class="status-badge status-{{ $post->status }}">
                            {{ $post->status === 'pending' ? 'Pending' : ($post->status === 'approved' ? 'Approved' : 'Rejected') }}
                        </span>
                        @if($post->status === 'approved' && $post->approved_at)
                            - {{ \Carbon\Carbon::parse($post->approved_at)->diffForHumans() }}
                        @endif
                    </p>
                    @if($post->status === 'rejected' && $post->rejection_reason)
                    <div class="rejection-reason">
                        <span class="rejection-title">Reason for rejection:</span>
                        <span class="rejection-text">{{ $post->rejection_reason }}</span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="post-find-roommate-content">
                <h3><strong>{{ $post->title }}</strong></h3>
                <p>{{ $post->content }}</p>
                <p>Contact: {{ $post->user->phone }}</p>
            </div>
            <div class="post-find-roommate-actions">
                <div class="like-btn" data-post-id="{{ $post->id }}" data-post-type="customer" onclick="toggleLike(event)">
                    <i class="fas fa-thumbs-up" style="color: {{ $post->is_favorited ? '#1877f2' : '#65676b' }};"></i>
                    <span class="action-text">{{ $post->is_favorited ? 'Liked' : 'Like' }}</span>
                    <span class="action-count">{{ $post->likes_count }}</span>
                </div>
                <button class="comment-toggle" onclick="toggleCommentSection(this)">
                    <i class="far fa-comment"></i>
                    <span class="action-text">Comment</span>
                </button>
                @if(Auth::check() && Auth::id() == $post->user_id)
                <a href="{{ route('customer.roommates.edit', $post->id) }}" class="btn btn-edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('customer.roommates.destroy', $post->id) }}" method="POST" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-delete"
                        onclick="return confirm('Are you sure you want to delete this post?')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
                @endif
            </div>

            <!-- Comment section -->
            <div class="post-comments-section comment-section" style="display: none;">
                @auth
                <div class="post-comment-form">
                    <div class="comment-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <form action="{{ route('customer.roommates.comments.store', $post->id) }}" method="POST" id="comment-form-{{ $post->id }}" class="comment-form">
                        @csrf
                        <input type="hidden" name="post_type" value="customer">
                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                        
                        <!-- Display errors specific to this post -->
                        @if($errors->has('content') && session('failed_post_id') == $post->id)
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $errors->first('content') }}
                        </div>
                        @endif
                        
                        <textarea name="content" placeholder="Write your comment..." required>{{ old('content') }}</textarea>
                        <button type="submit" class="submit-comment">Send</button>
                    </form>
                </div>
                @endauth

                <!-- Comment list -->
                <div class="post-comments-list">
                    @foreach ($post->comments->whereNull('parent_id')->sortByDesc('created_at') as $comment)
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
                            <!-- Edit form (hidden by default) -->
                            <form action="{{ route('customer.roommates.comments.update', $comment->id) }}" method="POST"
                                class="comment-edit-form edit-form" id="edit-form-{{ $comment->id }}" style="display: none;">
                                @csrf
                                @method('PUT')
                                @if($errors->has('content') && old('comment_id') == $comment->id)
                                <div class="error-message">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ $errors->first('content') }}
                                </div>
                                @endif
                                <textarea name="content"
                                    required>{{ old('comment_id') == $comment->id ? old('content') : $comment->content }}</textarea>
                                <input type="hidden" name="comment_id" value="{{ $comment->id }}">
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

                            <!-- Reply form (hidden by default) -->
                            @auth
                            <form action="{{ route('customer.roommates.comments.store', $post->id) }}" method="POST"
                                class="comment-reply-form reply-form" id="reply-form-{{ $comment->id }}" style="display: none;">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <input type="hidden" name="post_type" value="customer">
                                <input type="hidden" name="post_id" value="{{ $post->id }}">

                                @if($errors->has('content') && old('parent_id') == $comment->id)
                                <div class="error-message">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ $errors->first('content') }}
                                </div>
                                @endif

                                <textarea name="content" placeholder="Write your reply..."
                                    required>{{ old('parent_id') == $comment->id ? old('content') : '' }}</textarea>
                                <div class="comment-form-actions">
                                    <button type="submit" class="btn-submit">Send</button>
                                    <button type="button" class="btn-cancel"
                                        onclick="toggleReplyForm({{ $comment->id }})">Cancel</button>
                                </div>
                            </form>
                            @endauth

                            <!-- Replies list (hidden by default) -->
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
                                        <!-- Edit form for reply (hidden by default) -->
                                        <form action="{{ route('customer.roommates.comments.update', $reply->id) }}"
                                            method="POST" class="comment-edit-form edit-form" id="edit-form-{{ $reply->id }}"
                                            style="display: none;">
                                            @csrf
                                            @method('PUT')
                                            @if($errors->has('content') && old('comment_id') == $reply->id)
                                            <div class="error-message">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                {{ $errors->first('content') }}
                                            </div>
                                            @endif
                                            <textarea name="content"
                                                required>{{ old('comment_id') == $reply->id ? old('content') : $reply->content }}</textarea>
                                            <input type="hidden" name="comment_id" value="{{ $reply->id }}">
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

                                        <!-- Reply form for reply (hidden by default) -->
                                        @auth
                                        <form action="{{ route('customer.roommates.comments.store', $post->id) }}"
                                            method="POST" class="comment-reply-form reply-form" id="reply-form-{{ $reply->id }}"
                                            style="display: none;">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                            <input type="hidden" name="post_type" value="customer">
                                            <input type="hidden" name="post_id" value="{{ $post->id }}">

                                            @if($errors->has('content') && old('parent_id') == $reply->id)
                                            <div class="error-message">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                {{ $errors->first('content') }}
                                            </div>
                                            @endif

                                            <textarea name="content" placeholder="Write your reply..."
                                                required>{{ old('parent_id') == $reply->id ? old('content') : '' }}</textarea>
                                            <div class="comment-form-actions">
                                                <button type="submit" class="btn-submit">Send</button>
                                                <button type="button" class="btn-cancel"
                                                    onclick="toggleReplyForm({{ $reply->id }})">Cancel</button>
                                            </div>
                                        </form>
                                        @endauth

                                        <!-- Nested replies (level 3) -->
                                        <div class="comment-replies" id="replies-{{ $reply->id }}" style="display: none;">
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
                                                    <!-- Edit form for nested reply (hidden by default) -->
                                                    <form action="{{ route('customer.roommates.comments.update', $nestedReply->id) }}"
                                                        method="POST" class="comment-edit-form edit-form" id="edit-form-{{ $nestedReply->id }}"
                                                        style="display: none;">
                                                        @csrf
                                                        @method('PUT')
                                                        @if($errors->has('content') && old('comment_id') == $nestedReply->id)
                                                        <div class="error-message">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            {{ $errors->first('content') }}
                                                        </div>
                                                        @endif
                                                        <textarea name="content"
                                                            required>{{ old('comment_id') == $nestedReply->id ? old('content') : $nestedReply->content }}</textarea>
                                                        <input type="hidden" name="comment_id" value="{{ $nestedReply->id }}">
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
    @endforeach
</div>

<!-- Add JavaScript for handling banned words -->
<script>
    // Get the list of banned words from PHP
    const bannedWords = @json(\App\Models\BannedWord::pluck('word')->toArray());

    // Debug: Check the list of banned words
    console.log('Banned Words:', bannedWords);

    // Function to check for banned words
    function checkBannedWords(content) {
        const lowerContent = content.toLowerCase().normalize('NFC'); // Normalize UTF-8
        const foundWords = bannedWords.filter(word => {
            const regex = new RegExp('\\b' + word.toLowerCase().normalize('NFC') + '\\b', 'i');
            return regex.test(lowerContent);
        });
        return foundWords;
    }

    // Modify form handling to prevent submission with banned words
    document.addEventListener('DOMContentLoaded', function() {
        // Handle all types of comment forms (main comment, reply, edit)
        const allForms = document.querySelectorAll('.comment-form, .reply-form, .edit-form');
        
        allForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                // Prevent form submission immediately
                e.preventDefault();
                
                const textarea = this.querySelector('textarea[name="content"]');
                const content = textarea.value;
                const foundWords = checkBannedWords(content);
                
                if (foundWords.length > 0) {
                    // Show alert when banned words are detected
                    window.alert(`Content contains inappropriate words: ${foundWords.join(', ')}`);
                    textarea.focus();
                    console.log('Blocked content with banned words:', foundWords);
                    return false;
                }
                
                // If no banned words, proceed with form submission
                this.submit();
            });
        });
    });

    // Toggle like function (vanilla JavaScript)
    async function toggleLike(event) {
        const button = event.currentTarget;
        const postId = button.getAttribute('data-post-id');
        const postType = button.getAttribute('data-post-type');
        const icon = button.querySelector('i');
        const actionText = button.querySelector('.action-text');
        let actionCount = button.querySelector('.action-count');
        
        try {
            const response = await fetch("{{ route('customer.roommates.toggleFavorite') }}", {
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
            
            if (data.status === "added") {
                icon.style.color = "#1877f2";
                actionText.textContent = "Liked";
                button.classList.add('active');
                
                if (actionCount) {
                    actionCount.textContent = data.likes_count;
                } else {
                    actionCount = document.createElement('span');
                    actionCount.className = 'action-count';
                    actionCount.textContent = data.likes_count;
                    button.appendChild(actionCount);
                }
            } else if (data.status === "removed") {
                icon.style.color = "#65676b";
                actionText.textContent = "Like";
                button.classList.remove('active');
                
                if (actionCount) {
                    if (data.likes_count > 0) {
                        actionCount.textContent = data.likes_count;
                    } else {
                        actionCount.remove();
                    }
                }
            }
        } catch (error) {
            console.error("Error in toggleLike:", error);
        }
    }

    // Toggle comment section
    function toggleCommentSection(button) {
        const commentSection = button.closest('.post-find-roommate').querySelector('.comment-section');
        button.classList.toggle('active');
        
        if (commentSection.style.display === 'none') {
            commentSection.style.display = 'block';
        } else {
            commentSection.style.display = 'none';
        }
    }

    // Comment functions
    function toggleReplyForm(commentId) {
        const form = document.getElementById("reply-form-" + commentId);
        if (form.style.display === "none" || form.style.display === "") {
            document.querySelectorAll('.comment-reply-form').forEach(function(f) {
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
</script>

<style>
    .error-message {
        background-color: #ffe6e6;
        color: #cc0000;
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 5px;
        border-left: 4px solid #cc0000;
    }
    .success-message {
        background-color: #e6ffe6;
        color: #006600;
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 5px;
        border-left: 4px solid #006600;
    }

    /* Styles for status and rejection reason */
    .status-label {
        font-weight: bold;
        color: #333;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        color: #fff;
    }
    .status-pending {
        background-color: #ffb74d;
    }
    .status-approved {
        background-color: #4caf50;
    }
    .status-rejected {
        background-color: #f44336;
    }
    .rejection-reason {
        margin-top: 8px;
        padding: 10px;
        background-color: #ffebee;
        border-left: 4px solid #f44336;
        border-radius: 4px;
    }
    .rejection-title {
        font-weight: bold;
        color: #d32f2f;
        margin-right: 5px;
    }
    .rejection-text {
        color: #333;
    }
</style>
@endsection