@extends('layouts.customer')
@section('content')
<div class="container">
    <h1>Cộng đồng tìm người ở cùng</h1>
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
                <p>Contact: {{ $post->user->phone }}</p>
            </div>
            <div class="post-find-roommate-actions">
                <button class="like-btn" 
                        data-post-id="{{ $post->id }}" 
                        data-post-type="customer"
                        data-url="{{ route('customer.post.toggleFavorite') }}">
                    @if(Auth::check() && $post->isFavorited)
                        ❤️ Đã thích
                    @else
                        🤍 Thích
                    @endif
                    (<span class="like-count">{{ $post->favoritedby->count() }}</span>)
                </button>
                <button class="comment-toggle">💬 Bình luận (<span class="comment-count">{{ $post->comments->count() }}</span>)</button>
                <button>🔗 Nhắn tin</button>
            </div>

            <!-- Phần bình luận -->
            <div class="post-find-roommate-comments hidden">
                <!-- Form bình luận -->
                <div class="post-find-roommate-add-comment">
                    <form action="{{ route('customer.comments.store', $post->id) }}" method="POST" class="comment-form">
                        @csrf
                        <input type="hidden" name="post_type" value="customer">
                        <textarea name="content" placeholder="Viết bình luận..." rows="2" required></textarea>
                        <button type="submit" class="submit-comment">Gửi</button>
                    </form>
                </div>

                <!-- Danh sách bình luận -->
                <div class="comments-list">
                    @foreach($post->comments->where('parent_id', null) as $comment)
                    <!-- Bình luận chính -->
                    <div class="comment" data-comment-id="{{ $comment->id }}">
                        <div class="comment-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-author">{{ $comment->user->name }}</span>
                                <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                @if(Auth::id() == $comment->user_id)
                                <button class="edit-comment-btn">Sửa</button>
                                @endif
                            </div>
                            <p class="comment-text">{{ $comment->content }}</p>
                            <div class="comment-actions">
                                <button class="reply-btn">Trả lời</button>
                                @if($comment->replies->count() > 0)
                                <button class="view-replies">Xem {{ $comment->replies->count() }} trả lời</button>
                                @endif
                            </div>

                            <!-- Form sửa bình luận (ẩn ban đầu) -->
                            <div class="edit-comment-form hidden">
                                <form action="{{ route('customer.comments.update', $comment->id) }}" method="POST" class="update-comment-form">
                                    @csrf
                                    @method('PUT')
                                    <textarea name="content" rows="2">{{ $comment->content }}</textarea>
                                    <div class="edit-actions">
                                        <button type="submit" class="save-edit">Lưu</button>
                                        <button type="button" class="cancel-edit">Hủy</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Các trả lời -->
                            <div class="replies hidden">
                                @foreach($comment->replies as $reply)
                                <!-- Trả lời -->
                                <div class="reply" data-comment-id="{{ $reply->id }}">
                                    <div class="comment-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="comment-content">
                                        <div class="comment-header">
                                            <span class="comment-author">{{ $reply->user->name }}</span>
                                            <span class="comment-time">{{ $reply->created_at->diffForHumans() }}</span>
                                            @if(Auth::id() == $reply->user_id)
                                            <button class="edit-comment-btn">Sửa</button>
                                            @endif
                                        </div>
                                        <p class="comment-text">{{ $reply->content }}</p>
                                    </div>

                                    <!-- Form sửa trả lời (ẩn ban đầu) -->
                                    <div class="edit-comment-form hidden">
                                        <form action="{{ route('customer.comments.update', $reply->id) }}" method="POST" class="update-comment-form">
                                            @csrf
                                            @method('PUT')
                                            <textarea name="content" rows="2">{{ $reply->content }}</textarea>
                                            <div class="edit-actions">
                                                <button type="submit" class="save-edit">Lưu</button>
                                                <button type="button" class="cancel-edit">Hủy</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endforeach

                                <!-- Form trả lời (ẩn ban đầu) -->
                                <div class="reply-form hidden">
                                    <form action="{{ route('customer.comments.store', $post->id) }}" method="POST" class="reply-comment-form">
                                        @csrf
                                        <input type="hidden" name="post_type" value="customer">
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        <textarea name="content" placeholder="Viết trả lời..." rows="1" required></textarea>
                                        <div class="reply-actions">
                                            <button type="submit" class="submit-reply">Gửi</button>
                                            <button type="button" class="cancel-reply">Hủy</button>
                                        </div>
                                    </form>
                                </div>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // CSRF token cho AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Toggle hiển thị phần bình luận
        document.querySelectorAll('.comment-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const commentsSection = this.closest('.post-find-roommate').querySelector('.post-find-roommate-comments');
                commentsSection.classList.toggle('hidden');
            });
        });
        
        // Toggle hiển thị các trả lời
        document.querySelectorAll('.view-replies').forEach(btn => {
            btn.addEventListener('click', function() {
                const repliesContainer = this.closest('.comment-content').querySelector('.replies');
                repliesContainer.classList.toggle('hidden');
                
                // Đổi text nút
                if (repliesContainer.classList.contains('hidden')) {
                    this.textContent = 'Xem ' + this.dataset.count + ' trả lời';
                } else {
                    this.textContent = 'Ẩn trả lời';
                }
            });
        });
        
        // Toggle form trả lời
        document.querySelectorAll('.reply-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const replyForm = this.closest('.comment-content').querySelector('.reply-form');
                if (replyForm) {
                    replyForm.classList.toggle('hidden');
                }
            });
        });
        
        // Hủy trả lời
        document.querySelectorAll('.cancel-reply, .cancel-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.reply-form, .edit-comment-form').classList.add('hidden');
            });
        });
        
        // Toggle form sửa bình luận
        document.querySelectorAll('.edit-comment-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const editForm = this.closest('.comment-content').querySelector('.edit-comment-form');
                if (editForm) {
                    editForm.classList.toggle('hidden');
                }
            });
        });

        // Xử lý like bài đăng
        document.querySelectorAll('.like-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const postId = this.dataset.postId;
                const postType = this.dataset.postType;
                const likeCount = this.querySelector('.like-count');
                const likeBtn = this;
                
                $.ajax({
                    url: this.dataset.url,
                    method: 'POST',
                    data: {
                        post_id: postId,
                        post_type: postType
                    },
                    success: function(response) {
                        if (response.status === 'added') {
                            likeBtn.innerHTML = '❤️ Đã thích (<span class="like-count">' + (parseInt(likeCount.textContent) + 1) + '</span>)';
                        } else {
                            likeBtn.innerHTML = '🤍 Thích (<span class="like-count">' + (parseInt(likeCount.textContent) - 1) + '</span>)';
                        }
                        likeCount.textContent = response.status === 'added' ? 
                            parseInt(likeCount.textContent) + 1 : 
                            parseInt(likeCount.textContent) - 1;
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            window.location.href = "{{ route('login') }}";
                        } else {
                            alert('Có lỗi xảy ra, vui lòng thử lại');
                        }
                    }
                });
            });
        });

        // Xử lý form bình luận bằng AJAX
        function handleCommentFormSubmit(form, successCallback) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const postId = this.closest('.post-find-roommate-comments')?.querySelector('.comment-form')?.action.split('/').pop();
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof successCallback === 'function') {
                            successCallback(data);
                        }
                        this.reset();
                        location.reload(); // Tải lại trang để cập nhật bình luận
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi gửi bình luận');
                });
            });
        }

        // Áp dụng xử lý cho tất cả các form bình luận
        document.querySelectorAll('.comment-form, .reply-comment-form, .update-comment-form').forEach(form => {
            handleCommentFormSubmit(form, function(data) {
                // Có thể thêm xử lý tùy chỉnh ở đây nếu cần
            });
        });
    });
</script>
@endsection