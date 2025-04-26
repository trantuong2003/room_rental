@extends('layouts.customer')
@section('content')
<div class="container">
    <h1>Lịch sử bài đăng</h1>
    @foreach($posts as $post)
    <div class="list-post-container">
        <div class="post-find-roommate">
            <div class="post-find-roommate-header">
                <h4>{{ $post->user->name }}</h4>
                <p>{{ $post->approved_at ? \Carbon\Carbon::parse($post->approved_at)->diffForHumans() : 'Chưa được duyệt' }}</p>
            </div>
            <div class="post-find-roommate-content">
                <h3><strong>{{ $post->title }}</strong></h3>
                <p>{{ $post->content }}</p>
                <p>Contact:{{ $post->user->phone }}</p>
            </div>
            <div class="post-find-roommate-actions">
                <div class="like-btn" data-post-id="{{ $post->id }}" data-post-type="customer" onclick="toggleLike(event)">
                    <i class="fas fa-thumbs-up" style="color: {{ $post->isLiked ? '#1877f2' : '#65676b' }};"></i>
                    <span class="action-text">{{ $post->isLiked ? 'Đã thích' : 'Thích' }}</span>
                    @if($post->likes_count > 0)
                    <span class="action-count">{{ $post->likes_count }}</span>
                    @endif
                </div>
                <button class="comment-toggle" onclick="toggleCommentSection(this)">
                    <i class="far fa-comment"></i>
                    <span class="action-text">Bình luận</span>
                </button>
                @if(Auth::check() && Auth::id() == $post->user_id)
                <a href="{{ route('customer.roommates.edit', $post->id) }}" class="btn btn-edit">
                    <i class="fas fa-edit"></i> Sửa
                </a>
                <form action="{{ route('customer.roommates.destroy', $post->id) }}" method="POST" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa bài đăng này?')">
                        <i class="fas fa-trash"></i> Xóa
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
                    <form action="{{ route('customer.roommates.comments.store', $post->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="post_type" value="customer">
                        <textarea name="content" placeholder="Viết bình luận của bạn..." required></textarea>
                        <button type="submit" class="submit-comment">Gửi</button>
                    </form>
                </div>
                @else
                <div class="login-to-comment">
                    <p>Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để bình luận</p>
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
                                class="comment-edit-form" id="edit-form-{{ $comment->id }}" style="display: none;">
                                @csrf
                                @method('PUT')
                                <textarea name="content" required>{{ $comment->content }}</textarea>
                                <div class="comment-form-actions">
                                    <button type="submit" class="btn-submit">Lưu</button>
                                    <button type="button" class="btn-cancel"
                                        onclick="toggleEditForm({{ $comment->id }}, event)">Hủy</button>
                                </div>
                            </form>
                            @endif
                            @endauth

                            <div class="comment-actions">
                                @auth
                                <button class="btn-reply" onclick="toggleReplyForm({{ $comment->id }})">
                                    <i class="fas fa-reply"></i> Trả lời
                                </button>
                                @if (Auth::id() === $comment->user_id)
                                <button class="btn-edit" onclick="toggleEditForm({{ $comment->id }}, event)">
                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                </button>
                                @endif
                                @endauth

                                @if ($comment->replies->count() > 0)
                                <button class="btn-view-replies" onclick="toggleReplies({{ $comment->id }})"
                                    id="toggle-btn-{{ $comment->id }}">
                                    <i class="fas fa-comments"></i>
                                    <span class="toggle-text">Xem {{ $comment->replies->count() }} phản hồi</span>
                                    <i class="fas fa-chevron-down toggle-icon"></i>
                                </button>
                                @endif
                            </div>

                            <!-- Reply form (hidden by default) -->
                            @auth
                            <form action="{{ route('customer.roommates.comments.store', $post->id) }}" method="POST"
                                class="comment-reply-form" id="reply-form-{{ $comment->id }}" style="display: none;">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <input type="hidden" name="post_type" value="customer">
                                <textarea name="content" placeholder="Viết phản hồi của bạn..." required></textarea>
                                <div class="comment-form-actions">
                                    <button type="submit" class="btn-submit">Gửi</button>
                                    <button type="button" class="btn-cancel"
                                        onclick="toggleReplyForm({{ $comment->id }})">Hủy</button>
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
                                            <span class="reply-to">Trả lời {{ $reply->parent->user->name }}</span><br>
                                            @endif
                                            {{ $reply->content }}
                                        </div>

                                        @auth
                                        @if (Auth::id() === $reply->user_id)
                                        <!-- Edit form for reply (hidden by default) -->
                                        <form action="{{ route('customer.roommates.comments.update', $reply->id) }}" method="POST"
                                            class="comment-edit-form" id="edit-form-{{ $reply->id }}"
                                            style="display: none;">
                                            @csrf
                                            @method('PUT')
                                            <textarea name="content" required>{{ $reply->content }}</textarea>
                                            <div class="comment-form-actions">
                                                <button type="submit" class="btn-submit">Lưu</button>
                                                <button type="button" class="btn-cancel"
                                                    onclick="toggleEditForm({{ $reply->id }}, event)">Hủy</button>
                                            </div>
                                        </form>
                                        @endif
                                        @endauth

                                        <div class="comment-actions">
                                            @auth
                                            <button class="btn-reply" onclick="toggleReplyForm({{ $reply->id }})">
                                                <i class="fas fa-reply"></i> Trả lời
                                            </button>
                                            @if (Auth::id() === $reply->user_id)
                                            <button class="btn-edit" onclick="toggleEditForm({{ $reply->id }}, event)">
                                                <i class="fas fa-edit"></i> Chỉnh sửa
                                            </button>
                                            @endif
                                            @endauth

                                            @if ($reply->replies->count() > 0)
                                            <button class="btn-view-replies" onclick="toggleReplies({{ $reply->id }})"
                                                id="toggle-btn-{{ $reply->id }}">
                                                <i class="fas fa-comments"></i>
                                                <span class="toggle-text">Xem {{ $reply->replies->count() }} phản
                                                    hồi</span>
                                                <i class="fas fa-chevron-down toggle-icon"></i>
                                            </button>
                                            @endif
                                        </div>

                                        <!-- Reply form for reply (hidden by default) -->
                                        @auth
                                        <form action="{{ route('customer.roommates.comments.store', $post->id) }}" method="POST"
                                            class="comment-reply-form" id="reply-form-{{ $reply->id }}"
                                            style="display: none;">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                            <input type="hidden" name="post_type" value="customer">
                                            <textarea name="content" placeholder="Viết phản hồi của bạn..."
                                                required></textarea>
                                            <div class="comment-form-actions">
                                                <button type="submit" class="btn-submit">Gửi</button>
                                                <button type="button" class="btn-cancel"
                                                    onclick="toggleReplyForm({{ $reply->id }})">Hủy</button>
                                            </div>
                                        </form>
                                        @endauth

                                        <!-- Nested replies (level 3) -->
                                        <div class="comment-replies" id="replies-{{ $reply->id }}"
                                            style="display: none;">
                                            @foreach ($reply->replies->sortByDesc('created_at') as $nestedReply)
                                            <div class="post-comment nested-reply">
                                                <div class="comment-avatar">
                                                    <i class="fas fa-user-circle"></i>
                                                </div>
                                                <div class="comment-content-wrapper">
                                                    <div class="comment-content-header">
                                                        <span class="comment-author">{{ $nestedReply->user->name
                                                            }}</span>
                                                        <span class="comment-time">{{
                                                            $nestedReply->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <div class="comment-text" id="comment-text-{{ $nestedReply->id }}">
                                                        @if($nestedReply->parent->user_id !== $nestedReply->user_id)
                                                        <span class="reply-to">Trả lời {{
                                                            $nestedReply->parent->user->name }}</span><br>
                                                        @endif
                                                        {{ $nestedReply->content }}
                                                    </div>

                                                    @auth
                                                    @if (Auth::id() === $nestedReply->user_id)
                                                    <!-- Edit form for nested reply (hidden by default) -->
                                                    <form
                                                        action="{{ route('customer.roommates.comments.update', $nestedReply->id) }}"
                                                        method="POST" class="comment-edit-form"
                                                        id="edit-form-{{ $nestedReply->id }}" style="display: none;">
                                                        @csrf
                                                        @method('PUT')
                                                        <textarea name="content"
                                                            required>{{ $nestedReply->content }}</textarea>
                                                        <div class="comment-form-actions">
                                                            <button type="submit" class="btn-submit">Lưu</button>
                                                            <button type="button" class="btn-cancel"
                                                                onclick="toggleEditForm({{ $nestedReply->id }}, event)">Hủy</button>
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

@endsection



<script>
        // Toggle like function
        async function toggleLike(event) {
        const button = event.currentTarget;
        const postId = button.getAttribute('data-post-id');
        const postType = button.getAttribute('data-post-type');
        const icon = button.querySelector('i');
        const actionText = button.querySelector('.action-text');
        const actionCount = button.querySelector('.action-count');
        
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
                // Update like UI
                icon.style.color = "#1877f2";
                actionText.textContent = "Đã thích";
                button.classList.add('active');
                
                // Update like count
                if (actionCount) {
                    actionCount.textContent = data.likes_count;
                } else {
                    const countSpan = document.createElement('span');
                    countSpan.className = 'action-count';
                    countSpan.textContent = data.likes_count;
                    button.appendChild(countSpan);
                }
            } else if (data.status === "removed") {
                // Update like UI
                icon.style.color = "#65676b";
                actionText.textContent = "Thích";
                button.classList.remove('active');
                
                // Update like count
                if (actionCount) {
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

    // Thêm hàm toggleCommentSection
function toggleCommentSection(button) {
    const commentSection = button.closest('.post-find-roommate').querySelector('.comment-section');
    
    // Toggle class active cho button
    button.classList.toggle('active');
    
    // Toggle hiển thị phần bình luận
    if (commentSection.style.display === 'none') {
        commentSection.style.display = 'block';
    } else {
        commentSection.style.display = 'none';
    }
}


    // Comment functions
    function toggleReplyForm(commentId) {
        var form = document.getElementById("reply-form-" + commentId);
        if (form.style.display === "none" || form.style.display === "") {
            // Hide all other reply forms
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
    var repliesDiv = document.getElementById("replies-" + commentId);
    var toggleBtn = document.getElementById("toggle-btn-" + commentId);
    
    if (!repliesDiv || !toggleBtn) return;
    
    var toggleIcon = toggleBtn.querySelector('.toggle-icon');
    var toggleText = toggleBtn.querySelector('.toggle-text');
    
    if (repliesDiv.style.display === "none" || repliesDiv.style.display === "") {
        repliesDiv.style.display = "block";
        if (toggleText) toggleText.textContent = "Thu nhỏ";
        if (toggleIcon) {
            toggleIcon.classList.remove('fa-chevron-down');
            toggleIcon.classList.add('fa-chevron-up');
        }
    } else {
        repliesDiv.style.display = "none";
        if (toggleText) toggleText.textContent = "Xem " + repliesDiv.querySelectorAll('.post-comment').length + " phản hồi";
        if (toggleIcon) {
            toggleIcon.classList.remove('fa-chevron-up');
            toggleIcon.classList.add('fa-chevron-down');
        }
    }
}
</script>



