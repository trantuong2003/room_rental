@extends('layouts.customer')

@section('content')
<div class="container">
    <div class="customer-roommate-posts">
        <!-- Header -->
        <div class="customer-roommate-posts__header">
            <h1 class="customer-roommate-posts__title">Quản lý bài đăng tìm người thuê</h1>
            <button class="customer-roommate-posts__new-post-btn">
                <a href="{{ route('customer.roommates.create') }}">
                    <i class="fas fa-plus"></i> Đăng bài mới
                </a>
            </button>
        </div>

        <div class="list-post-container">
            @foreach($posts as $post)
                <!-- Bài đăng -->
                <div class="customer-roommate-post">
                    <!-- Header bài đăng -->
                    <div class="customer-roommate-post__header">
                        <h3 class="customer-roommate-post__title">{{ $post->title }}</h3>
                        <div class="customer-roommate-post__actions">
                            {{-- <button class="customer-roommate-post__edit-btn"> --}}
                                <a href="{{ route('customer.roommates.edit', $post->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                            {{-- </button> --}}
                            {{-- <button class="customer-roommate-post__delete-btn">
                                <i class="fas fa-trash"></i>
                            </button> --}}
                            <div>

                            <form action="{{ route('customer.roommates.destroy', $post->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>

                        </div>
                    </div>

                    <!-- Nội dung -->
                    <div class="customer-roommate-post__content">
                        <p>{{ $post->content }}</p>
                        <p>Contact: </p>
                    </div>

                    <!-- Meta -->
                    <div class="customer-roommate-post__meta">
                        <span class="customer-roommate-post__status customer-roommate-post__status--active">
                            Status: {{ $post->status }}
                        </span>
                    </div>

                    <!-- Thống kê -->
                    <div class="customer-roommate-post__stats">
                        <div class="customer-roommate-post__stat">
                            <i class="far fa-eye"></i>
                            <span>102 lượt xem</span>
                        </div>
                        <div class="customer-roommate-post__stat">
                            <i class="far fa-heart"></i>
                            <span>15 lượt thích</span>
                        </div>
                        <div class="customer-roommate-post__stat customer-roommate-post__comment-toggle">
                            <i class="far fa-comment"></i>
                            <span>5 bình luận</span>
                        </div>
                    </div>

                    <!-- Phần bình luận (ẩn ban đầu) -->
                    <div class="customer-roommate-post__comments">
                        <!-- Form bình luận -->
                        <div class="customer-roommate-post__comment-form">
                            <textarea placeholder="Viết bình luận..." rows="2"></textarea>
                            <button class="customer-roommate-post__submit-comment">Gửi</button>
                        </div>

                        <!-- Danh sách bình luận -->
                        <div class="customer-roommate-post__comments-list">
                            <!-- Bình luận chính -->
                            <div class="customer-roommate-post__comment">
                                <div class="customer-roommate-post__comment-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="customer-roommate-post__comment-content">
                                    <div class="customer-roommate-post__comment-header">
                                        <span class="customer-roommate-post__comment-author">Nguyễn Văn A</span>
                                        <span class="customer-roommate-post__comment-time">2 giờ trước</span>
                                    </div>
                                    <p class="customer-roommate-post__comment-text">Phòng này có chỗ để xe không bạn?</p>
                                    <div class="customer-roommate-post__comment-actions">
                                        <button class="customer-roommate-post__reply-btn">Trả lời</button>
                                        <button class="customer-roommate-post__view-replies">Xem 3 trả lời</button>
                                    </div>

                                    <!-- Các trả lời (ẩn ban đầu) -->
                                    <div class="customer-roommate-post__replies">
                                        <!-- Trả lời 1 -->
                                        <div class="customer-roommate-post__reply">
                                            <div class="customer-roommate-post__comment-avatar">
                                                <i class="fas fa-user-circle"></i>
                                            </div>
                                            <div class="customer-roommate-post__comment-content">
                                                <div class="customer-roommate-post__comment-header">
                                                    <span class="customer-roommate-post__comment-author">Trần Thị B</span>
                                                    <span class="customer-roommate-post__comment-time">1 giờ trước</span>
                                                </div>
                                                <p class="customer-roommate-post__comment-text">Có chỗ để xe rộng rãi nhé
                                                    bạn</p>
                                                <div class="customer-roommate-post__comment-actions">
                                                    <button class="customer-roommate-post__reply-btn">Trả lời</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Form trả lời (ẩn ban đầu) -->
                                        <div class="customer-roommate-post__reply-form">
                                            <textarea placeholder="Viết trả lời..." rows="1"></textarea>
                                            <div class="customer-roommate-post__reply-actions">
                                                <button class="customer-roommate-post__submit-reply">Gửi</button>
                                                <button class="customer-roommate-post__cancel-reply">Hủy</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle hiển thị phần bình luận
        document.querySelectorAll('.customer-roommate-post__comment-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const commentsSection = this.closest('.customer-roommate-post').querySelector('.customer-roommate-post__comments');
                commentsSection.classList.toggle('customer-roommate-post__comments--visible');
            });
        });
        
        // Toggle hiển thị các trả lời
        document.querySelectorAll('.customer-roommate-post__view-replies').forEach(btn => {
            btn.addEventListener('click', function() {
                const repliesContainer = this.closest('.customer-roommate-post__comment-content').querySelector('.customer-roommate-post__replies');
                repliesContainer.classList.toggle('customer-roommate-post__replies--visible');
                
                // Đổi text nút
                if (repliesContainer.classList.contains('customer-roommate-post__replies--visible')) {
                    this.textContent = 'Ẩn trả lời';
                } else {
                    this.textContent = 'Xem ' + (this.dataset.count || 'tất cả') + ' trả lời';
                }
            });
        });
        
        // Toggle form trả lời
        document.querySelectorAll('.customer-roommate-post__reply-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Tìm form trả lời gần nhất
                let replyForm;
                if (this.classList.contains('customer-roommate-post__reply-btn')) {
                    replyForm = this.closest('.customer-roommate-post__comment-content').querySelector('.customer-roommate-post__reply-form');
                }
                
                if (replyForm) {
                    replyForm.classList.toggle('customer-roommate-post__reply-form--visible');
                }
            });
        });
        
        // Hủy trả lời
        document.querySelectorAll('.customer-roommate-post__cancel-reply').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.customer-roommate-post__reply-form').classList.remove('customer-roommate-post__reply-form--visible');
            });
        });
    });
</script>

//list post blade
@extends('layouts.customer')
@section('content')
<div class="container">
    <h1>Cộng đồng tìm người ở cùng</h1>
    @foreach($posts as $post)
        
    <div class="list-post-container">
        <div class="post-find-roommate">
            <div class="post-find-roommate-header">
                <h4>{{ $post->user->name }}</h4>
                {{-- <p>{{ $post->approved_at }}</p> --}}
                <p>{{ $post->approved_at ? \Carbon\Carbon::parse($post->approved_at)->diffForHumans() : 'Chưa được duyệt' }}</p>

            </div>
            <div class="post-find-roommate-content">
                <h3><strong>{{ $post->title }}</strong></h3>
                <p>{{ $post->content }}</p>
                <p>Contact:{{ $post->user->phone }}</p>

            </div>
            <div class="post-find-roommate-actions">
                <button id="like-btn">👍 Thích</button>
                <button class="comment-toggle">💬 Bình luận</button>
                <button>🔗 Nhắn tin</button>
            </div>

            <!-- Phần bình luận (ẩn ban đầu) -->
            <div class="post-find-roommate-comments hidden">
                <!-- Form bình luận -->
                <div class="post-find-roommate-add-comment">
                    <textarea placeholder="Viết bình luận..." rows="2"></textarea>
                    <button class="submit-comment">Gửi</button>
                </div>

                <!-- Danh sách bình luận -->
                <div class="comments-list">
                    <!-- Bình luận chính -->
                    <div class="comment">
                        <div class="comment-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-author">Hà Tú Vy</span>
                                <span class="comment-time">2 giờ trước</span>
                            </div>
                            <p class="comment-text">Phòng đẹp quá! Giá bao nhiêu vậy bạn?</p>
                            <div class="comment-actions">
                                <button class="reply-btn">Trả lời</button>
                                <button class="view-replies">Xem 1 trả lời</button>
                            </div>

                            <!-- Các trả lời (ẩn ban đầu) -->
                            <div class="replies hidden">
                                <!-- Trả lời 1 -->
                                <div class="reply">
                                    <div class="comment-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="comment-content">
                                        <div class="comment-header">
                                            <span class="comment-author">Bạn</span>
                                            <span class="comment-time">1 giờ trước</span>
                                        </div>
                                        <p class="comment-text">Giá 3 triệu/tháng bạn nhé!</p>
                                    </div>
                                </div>

                                <!-- Form trả lời (ẩn ban đầu) -->
                                <div class="reply-form hidden">
                                    <textarea placeholder="Viết trả lời..." rows="1"></textarea>
                                    <div class="reply-actions">
                                        <button class="submit-reply">Gửi</button>
                                        <button class="cancel-reply">Hủy</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bình luận 2 -->
                    <div class="comment">
                        <div class="comment-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-author">Nguyễn Đức Huy</span>
                                <span class="comment-time">1 giờ trước</span>
                            </div>
                            <p class="comment-text">Địa chỉ cụ thể ở đâu vậy?</p>
                            <div class="comment-actions">
                                <button class="reply-btn">Trả lời</button>
                                <button class="view-replies">Xem 1 trả lời</button>
                            </div>

                            <!-- Các trả lời (ẩn ban đầu) -->
                            <div class="replies hidden">
                                <!-- Trả lời 1 -->
                                <div class="reply">
                                    <div class="comment-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="comment-content">
                                        <div class="comment-header">
                                            <span class="comment-author">Bạn</span>
                                            <span class="comment-time">30 phút trước</span>
                                        </div>
                                        <p class="comment-text">123 Nguyễn Đình Chiểu, Quận 1 nha!</p>
                                    </div>
                                </div>

                                <!-- Form trả lời (ẩn ban đầu) -->
                                <div class="reply-form hidden">
                                    <textarea placeholder="Viết trả lời..." rows="1"></textarea>
                                    <div class="reply-actions">
                                        <button class="submit-reply">Gửi</button>
                                        <button class="cancel-reply">Hủy</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection


<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Toggle hiển thị phần bình luận
    document.querySelectorAll('.comment-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const commentsSection = this.closest('.post-find-roommate').querySelector('.post-find-roommate-comments');
            commentsSection.classList.toggle('visible');
        });
    });
    
    // Toggle hiển thị các trả lời
    document.querySelectorAll('.view-replies').forEach(btn => {
        btn.addEventListener('click', function() {
            const repliesContainer = this.closest('.comment-content').querySelector('.replies');
            repliesContainer.classList.toggle('visible');
            
            // Đổi text nút
            if (repliesContainer.classList.contains('visible')) {
                this.textContent = 'Ẩn trả lời';
            } else {
                this.textContent = 'Xem trả lời';
            }
        });
    });
    
    // Toggle form trả lời
    document.querySelectorAll('.reply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const replyForm = this.closest('.comment-content').querySelector('.reply-form');
            if (replyForm) {
                replyForm.classList.toggle('visible');
            }
        });
    });
    
    // Hủy trả lời
    document.querySelectorAll('.cancel-reply').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.reply-form').classList.remove('visible');
        });
    });
    
    // Xử lý thêm bình luận chính
    document.querySelector('.submit-comment')?.addEventListener('click', function() {
        const commentText = this.previousElementSibling.value.trim();
        if (commentText !== "") {
            const commentSection = document.querySelector('.comments-list');
            const newComment = document.createElement('div');
            newComment.classList.add('comment');
            newComment.innerHTML = `
                <div class="comment-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="comment-content">
                    <div class="comment-header">
                        <span class="comment-author">Bạn</span>
                        <span class="comment-time">Vừa xong</span>
                    </div>
                    <p class="comment-text">${commentText}</p>
                    <div class="comment-actions">
                        <button class="reply-btn">Trả lời</button>
                    </div>
                    <div class="replies">
                        <div class="reply-form">
                            <textarea placeholder="Viết trả lời..." rows="1"></textarea>
                            <div class="reply-actions">
                                <button class="submit-reply">Gửi</button>
                                <button class="cancel-reply">Hủy</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            commentSection.appendChild(newComment);
            this.previousElementSibling.value = "";
            
            // Thêm sự kiện cho các nút mới
            addCommentEvents(newComment);
        }
    });
    
    // Thêm sự kiện cho các bình luận hiện có
    document.querySelectorAll('.comment').forEach(comment => {
        addCommentEvents(comment);
    });
    
    function addCommentEvents(commentElement) {
        // Thêm sự kiện cho nút trả lời
        const replyBtn = commentElement.querySelector('.reply-btn');
        const replyForm = commentElement.querySelector('.reply-form');
        const submitReply = commentElement.querySelector('.submit-reply');
        const cancelReply = commentElement.querySelector('.cancel-reply');
        
        if (replyBtn && replyForm) {
            replyBtn.addEventListener('click', () => {
                replyForm.classList.toggle('visible');
            });
        }
        
        if (submitReply) {
            submitReply.addEventListener('click', function() {
                const replyText = this.parentElement.previousElementSibling.value.trim();
                if (replyText !== "") {
                    const repliesContainer = this.closest('.replies');
                    const newReply = document.createElement('div');
                    newReply.classList.add('reply');
                    newReply.innerHTML = `
                        <div class="comment-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-author">Bạn</span>
                                <span class="comment-time">Vừa xong</span>
                            </div>
                            <p class="comment-text">${replyText}</p>
                        </div>
                    `;
                    repliesContainer.insertBefore(newReply, replyForm);
                    this.parentElement.previousElementSibling.value = "";
                    replyForm.classList.remove('visible');
                }
            });
        }
        
        if (cancelReply) {
            cancelReply.addEventListener('click', function() {
                this.closest('.reply-form').classList.remove('visible');
            });
        }
    }
});
</script>