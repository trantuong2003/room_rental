@extends('layouts.customer')

@section('content')
<div class="container">
    <h2>My favorite posts</h2>

    <!-- Bộ lọc -->
    <div class="filter-tabs" style="margin-bottom: 20px;">
        <a href="{{ route('customer.favorites') }}?filter=landlord"
            class="filter-tab {{ $filter === 'landlord' || $filter === '' ? 'active' : '' }}">Landlord</a>
        <a href="{{ route('customer.favorites') }}?filter=customer"
            class="filter-tab {{ $filter === 'customer' ? 'active' : '' }}">Customer</a>
    </div>

    @if($favorites->isEmpty())
    <p>You don't have any one in your list posts.</p>
    @else
    @foreach($favorites as $favorite)
    @if($favorite->post_type === 'landlord' && $filter === 'landlord')
    <!-- Định dạng cho bài đăng của chủ nhà -->
    <div class="listing" onclick="redirectToDetail({{ $favorite->id }}, 'landlord')" style="cursor: pointer;">
        <div class="images">
            @if($favorite->images->isNotEmpty())
            <img src="{{ asset('storage/' . $favorite->images->first()->image_path) }}" alt="Hình ảnh chính">
            <div class="grid">
                @foreach ($favorite->images->slice(1, 3) as $image)
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Hình ảnh bổ sung">
                @endforeach
            </div>
            @else
            <img src="{{ asset('assets/image/placeholder.jpg') }}" alt="Không có hình ảnh">
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
                    <img src="{{ asset('assets/image/userimage.jpg') }}" alt="Ảnh đại diện">
                    <div>
                        <p>{{ $favorite->user->name ?? 'Chủ nhà' }}</p>
                    </div>
                </div>
                <div class="actions">
                    <button onclick="event.stopPropagation();">
                        <i class="fas fa-phone-alt"></i> {{ $favorite->user->phone ?? 'No have phone' }}
                    </button>
                    <button class="favorite-btn" data-post-id="{{ $favorite->id }}" data-post-type="landlord"
                        style="cursor: pointer;" data-favorited="{{ $favorite->isFavorited ? 'true' : 'false' }}">
                        <i class="fas fa-heart" style="color: {{ $favorite->isFavorited ? 'red' : 'gray' }};"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @elseif($favorite->post_type === 'customer' && $filter === 'customer')
    <!-- Định dạng cho bài đăng của khách hàng -->
    <div class="list-post-container">
        <div class="post-find-roommate">
            <div class="post-find-roommate-header">
                <h4>{{ $favorite->user->name }}</h4>
                <p>{{ $favorite->approved_at ? \Carbon\Carbon::parse($favorite->approved_at)->diffForHumans() : 'Chưa được phê duyệt' }}</p>
            </div>
            <div class="post-find-roommate-content">
                <h3><strong>{{ $favorite->title }}</strong></h3>
                <p>{{ $favorite->content }}</p>
                <p>Liên hệ: {{ $favorite->user->phone }}</p>
            </div>
            <div class="post-find-roommate-actions">
                <div class="like-btn" data-post-id="{{ $favorite->id }}" data-post-type="customer"
                    data-liked="{{ $favorite->favoritedby->where('user_id', auth()->id())->isNotEmpty() ? 'true' : 'false' }}">
                    <i class="fas fa-thumbs-up" style="color: {{ $favorite->favoritedby->where('user_id', auth()->id())->isNotEmpty() ? '#1877f2' : '#65676b' }};"></i>
                    <span class="action-text">{{ $favorite->favoritedby->where('user_id', auth()->id())->isNotEmpty() ? 'Liked' : 'Like' }}</span>
                    <span class="action-count">{{ $favorite->favoritedby->count() }}</span>
                </div>
                <button class="comment-toggle">
                    <i class="far fa-comment"></i>
                    <span class="action-text">Comment</span>
                </button>
                @if($favorite->user->id != auth()->id())
                <button class="message-btn" onclick="goToChat({{ $favorite->user->id }})">
                    <i class="far fa-envelope"></i>
                    <span class="action-text">Message</span>
                </button>
                @endif
            </div>

            <!-- Phần bình luận -->
            <div class="post-comments-section comment-section" style="display: none;">
                @auth
                <div class="post-comment-form">
                    <div class="comment-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <form action="{{ route('customer.favorites.comments.store', $favorite->id) }}" method="POST"
                        class="comment-form">
                        @csrf
                        <input type="hidden" name="post_type" value="customer">
                        <textarea name="content" placeholder="Enter your comment..."
                            required>{{ old('content') }}</textarea>
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
                    <p>Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để bình luận</p>
                </div>
                @endauth

                <!-- Danh sách bình luận -->
                <div class="post-comments-list">
                    @if($favorite->comments->isEmpty())
                    <p>No comment.</p>
                    @else
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
                            @if (auth()->id() === $comment->user_id)
                            <!-- Form chỉnh sửa -->
                            <form action="{{ route('customer.favorites.comments.update', $comment->id) }}" method="POST"
                                class="comment-edit-form edit-form" id="edit-form-{{ $comment->id }}"
                                style="display: none;">
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
                                    <button type="submit" class="btn-submit">Lưu</button>
                                    <button type="button" class="btn-cancel"
                                        onclick="toggleEditForm({{ $comment->id }}, event)">Hủy</button>
                                </div>
                            </form>
                            @endif
                            @endauth

                            <div class="comment-actions">
                                @auth
                                <button class="btn-reply" data-comment-id="{{ $comment->id }}">
                                    <i class="fas fa-reply"></i> Reply
                                </button>
                                @if (auth()->id() === $comment->user_id)
                                <button class="btn-edit" data-comment-id="{{ $comment->id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                @endif
                                @endauth

                                @if ($comment->replies->count() > 0)
                                <button class="btn-view-replies" data-comment-id="{{ $comment->id }}"
                                    id="toggle-btn-{{ $comment->id }}">
                                    <i class="fas fa-comments"></i>
                                    <span class="toggle-text">See {{ $comment->replies->count() }} trả lời</span>
                                    <i class="fas fa-chevron-down toggle-icon"></i>
                                </button>
                                @endif
                            </div>

                            <!-- Form trả lời -->
                            @auth
                            <form action="{{ route('customer.favorites.comments.store', $favorite->id) }}" method="POST"
                                class="comment-reply-form reply-form" id="reply-form-{{ $comment->id }}"
                                style="display: none;">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <input type="hidden" name="post_type" value="customer">
                                <textarea name="content" placeholder="Viết trả lời của bạn..."
                                    required>{{ old('content') }}</textarea>
                                @if($errors->has('content') && session('failed_post_id') == $favorite->id &&
                                old('parent_id') == $comment->id)
                                <div class="error-message" style="margin-top: 5px;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ $errors->first('content') }}
                                </div>
                                @endif
                                <div class="comment-form-actions">
                                    <button type="submit" class="btn-submit">Gửi</button>
                                    <button type="button" class="btn-cancel"
                                        onclick="toggleReplyForm({{ $comment->id }})">Hủy</button>
                                </div>
                            </form>
                            @endauth

                            <!-- Danh sách trả lời -->
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
                                            <span class="reply-to">Reply {{ $reply->parent->user->name }}</span><br>
                                            @endif
                                            {{ $reply->content }}
                                        </div>

                                        @auth
                                        @if (auth()->id() === $reply->user_id)
                                        <!-- Form chỉnh sửa trả lời -->
                                        <form action="{{ route('customer.favorites.comments.update', $reply->id) }}"
                                            method="POST" class="comment-edit-form edit-form"
                                            id="edit-form-{{ $reply->id }}" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                            <textarea name="content"
                                                required>{{ old('content', $reply->content) }}</textarea>
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
                                            <button class="btn-reply" data-comment-id="{{ $reply->id }}">
                                                <i class="fas fa-reply"></i> Reply
                                            </button>
                                            @if (auth()->id() === $reply->user_id)
                                            <button class="btn-edit" data-comment-id="{{ $reply->id }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            @endif
                                            @endauth

                                            @if ($reply->replies->count() > 0)
                                            <button class="btn-view-replies" data-comment-id="{{ $reply->id }}"
                                                id="toggle-btn-{{ $reply->id }}">
                                                <i class="fas fa-comments"></i>
                                                <span class="toggle-text">See {{ $reply->replies->count() }} Reply</span>
                                                <i class="fas fa-chevron-down toggle-icon"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach
    @endif

    <!-- JavaScript được đặt trực tiếp trong section content -->
    <script>
        console.log('JavaScript loaded');

        function redirectToDetail(postId, postType) {
            console.log('redirectToDetail called', { postId, postType });
            if (postType === 'landlord') {
                window.location.href = "{{ route('customer.post.detail', '') }}/" + postId;
            } else {
                window.location.href = "{{ route('customer.roommates.index') }}";
            }
        }

        function showLoginAlert() {
            console.log('showLoginAlert called');
            alert("Vui lòng đăng nhập để thực hiện hành động này!");
            window.location.href = "{{ route('login') }}";
        }

        function goToChat(userId) {
            console.log('goToChat called', { userId });
            const currentUserId = {{ auth()->id() ?? 'null' }};
            if (userId == currentUserId) {
                console.log('Prevented self-chat');
                alert("Bạn không thể nhắn tin cho chính mình!");
                return;
            }
            window.location.href = "{{ route('customer.chat.user', ':userId') }}".replace(':userId', userId);
        }

        async function toggleFavorite(button) {
            console.log('toggleFavorite called', {
                postId: button.getAttribute('data-post-id'),
                postType: button.getAttribute('data-post-type'),
                favorited: button.getAttribute('data-favorited')
            });
            const postId = button.getAttribute('data-post-id');
            const postType = button.getAttribute('data-post-type');
            const icon = button.querySelector('i');

            try {
                const response = await fetch("{{ route('customer.favorites.toggleFavorite') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ 
                        post_id: postId,
                        post_type: postType
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`HTTP error! status: ${response.status}, message: ${errorData.error || response.statusText}`);
                }

                const data = await response.json();
                console.log('toggleFavorite response:', data);
                
                if (data.status === "added") {
                    icon.style.color = "red";
                    button.setAttribute('data-favorited', 'true');
                } else if (data.status === "removed") {
                    icon.style.color = "gray";
                    button.setAttribute('data-favorited', 'false');
                }
            } catch (error) {
                console.error("Lỗi trong toggleFavorite:", error);
                alert(`Có lỗi xảy ra khi thực hiện hành động yêu thích: ${error.message}`);
            }
        }

        async function toggleLike(button) {
            console.log('toggleLike called', {
                postId: button.getAttribute('data-post-id'),
                postType: button.getAttribute('data-post-type'),
                liked: button.getAttribute('data-liked')
            });
            const postId = button.getAttribute('data-post-id');
            const postType = button.getAttribute('data-post-type');
            const icon = button.querySelector('i');
            const actionText = button.querySelector('.action-text');
            const actionCount = button.querySelector('.action-count');

            try {
                const response = await fetch("{{ route('customer.favorites.toggleLike') }}", {
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

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`HTTP error! status: ${response.status}, message: ${errorData.error || response.statusText}`);
                }

                const data = await response.json();
                console.log('toggleLike response:', data);
                
                if (data.status === "added") {
                    icon.style.color = "#1877f2";
                    actionText.textContent = "Đã thích";
                    button.classList.add('active');
                    button.setAttribute('data-liked', 'true');
                    if (actionCount) {
                        actionCount.textContent = data.likes_count;
                    } else {
                        const countSpan = document.createElement('span');
                        countSpan.className = 'action-count';
                        countSpan.textContent = data.likes_count;
                        button.appendChild(countSpan);
                    }
                } else if (data.status === "removed") {
                    icon.style.color = "#65676b";
                    actionText.textContent = "Thích";
                    button.classList.remove('active');
                    button.setAttribute('data-liked', 'false');
                    if (actionCount) {
                        actionCount.textContent = data.likes_count;
                        if (data.likes_count === 0) {
                            actionCount.remove();
                        }
                    }
                }
            } catch (error) {
                console.error("Lỗi trong toggleLike:", error);
                alert(`Có lỗi xảy ra khi thực hiện hành động thích: ${error.message}`);
            }
        }

        function toggleCommentSection(button) {
            console.log('toggleCommentSection called');
            const postContainer = button.closest('.post-find-roommate');
            const commentSection = postContainer.querySelector('.comment-section');
            if (commentSection) {
                const isVisible = commentSection.style.display === 'block';
                commentSection.style.display = isVisible ? 'none' : 'block';
                button.classList.toggle('active', !isVisible);
                console.log('Comment section toggled', { display: commentSection.style.display });
            } else {
                console.error('Không tìm thấy comment-section');
            }
        }

        function toggleReplyForm(commentId) {
            console.log('toggleReplyForm called', { commentId });
            const form = document.getElementById("reply-form-" + commentId);
            if (form) {
                if (form.style.display === "none" || form.style.display === "") {
                    document.querySelectorAll('.comment-reply-form').forEach(f => {
                        if (f.id !== 'reply-form-' + commentId) f.style.display = 'none';
                    });
                    form.style.display = "block";
                } else {
                    form.style.display = "none";
                }
                console.log('Reply form toggled', { commentId, display: form.style.display });
            } else {
                console.error('Không tìm thấy reply-form-' + commentId);
            }
        }

        function toggleEditForm(commentId, event = null) {
            console.log('toggleEditForm called', { commentId });
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
                console.log('Edit form toggled', { commentId, display: form.style.display });
            } else {
                console.error('Không tìm thấy edit-form-' + commentId);
            }
        }

        function toggleReplies(commentId) {
            console.log('toggleReplies called', { commentId });
            const repliesDiv = document.getElementById("replies-" + commentId);
            const toggleBtn = document.getElementById("toggle-btn-" + commentId);
            
            if (!repliesDiv || !toggleBtn) {
                console.error('Không tìm thấy replies-' + commentId + ' hoặc toggle-btn-' + commentId);
                return;
            }
            
            const toggleIcon = toggleBtn.querySelector('.toggle-icon');
            const toggleText = toggleBtn.querySelector('.toggle-text');
            
            if (repliesDiv.style.display === "none" || repliesDiv.style.display === "") {
                repliesDiv.style.display = "block";
                if (toggleText) toggleText.textContent = "";
                if (toggleIcon) {
                    toggleIcon.classList.remove('fa-chevron-down');
                    toggleIcon.classList.add('fa-chevron-up');
                }
            } else {
                repliesDiv.style.display = "none";
                if (toggleText) toggleText.textContent = "Xem " + repliesDiv.querySelectorAll('.post-comment').length + " trả lời";
                if (toggleIcon) {
                    toggleIcon.classList.remove('fa-chevron-up');
                    toggleIcon.classList.add('fa-chevron-down');
                }
            }
            console.log('Replies toggled', { commentId, display: repliesDiv.style.display });
        }

        const bannedWords = @json(\App\Models\BannedWord::pluck('word')->toArray());

        function checkBannedWords(content) {
            console.log('checkBannedWords called', { content });
            const lowerContent = content.toLowerCase().normalize('NFC');
            const foundWords = bannedWords.filter(word => {
                const regex = new RegExp('\\b' + word.toLowerCase().normalize('NFC') + '\\b', 'i');
                return regex.test(lowerContent);
            });
            return foundWords;
        }

        // Gắn sự kiện động sau khi DOM tải xong
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded');

            // Debug trạng thái nút và dữ liệu
            document.querySelectorAll('.favorite-btn').forEach(button => {
                console.log('Favorite button:', {
                    post_id: button.getAttribute('data-post-id'),
                    post_type: button.getAttribute('data-post-type'),
                    favorited: button.getAttribute('data-favorited')
                });
            });

            document.querySelectorAll('.like-btn').forEach(button => {
                console.log('Like button:', {
                    post_id: button.getAttribute('data-post-id'),
                    post_type: button.getAttribute('data-post-type'),
                    liked: button.getAttribute('data-liked'),
                    likes_count: button.querySelector('.action-count')?.textContent || '0'
                });
            });

            document.querySelectorAll('.message-btn').forEach(button => {
                console.log('Message button:', {
                    user_id: button.getAttribute('onclick')?.match(/\d+/)?.[0],
                    is_own_post: {{ auth()->id() ?? 'null' }} == button.getAttribute('onclick')?.match(/\d+/)?.[0]
                });
            });

            document.querySelectorAll('.post-comments-list').forEach(list => {
                const comments = list.querySelectorAll('.post-comment').length;
                console.log('Comments loaded:', { count: comments });
            });

            // Đảm bảo comment-section ẩn ban đầu
            document.querySelectorAll('.comment-section').forEach(section => {
                if (section.style.display !== 'none') {
                    section.style.display = 'none';
                    console.log('Forced comment-section to hidden');
                }
            });

            // Gắn sự kiện cho nút yêu thích
            document.querySelectorAll('.favorite-btn').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.stopPropagation();
                    toggleFavorite(this);
                });
            });

            // Gắn sự kiện cho nút thích
            document.querySelectorAll('.like-btn').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.stopPropagation();
                    toggleLike(this);
                });
            });

            // Gắn sự kiện cho nút bình luận
            document.querySelectorAll('.comment-toggle').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.stopPropagation();
                    toggleCommentSection(this);
                });
            });

            // Gắn sự kiện cho nút trả lời và chỉnh sửa
            document.querySelectorAll('.btn-reply').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.stopPropagation();
                    const commentId = this.getAttribute('data-comment-id');
                    toggleReplyForm(commentId);
                });
            });

            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.stopPropagation();
                    const commentId = this.getAttribute('data-comment-id');
                    toggleEditForm(commentId, event);
                });
            });

            document.querySelectorAll('.btn-view-replies').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.stopPropagation();
                    const commentId = this.getAttribute('data-comment-id');
                    toggleReplies(commentId);
                });
            });

            // Kiểm tra từ cấm cho form bình luận
            const allForms = document.querySelectorAll('.comment-form, .reply-form, .edit-form');
            allForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const textarea = this.querySelector('textarea[name="content"]');
                    const content = textarea.value;
                    const foundWords = checkBannedWords(content);
                    
                    if (foundWords.length > 0) {
                        alert(`Nội dung chứa từ ngữ không phù hợp: ${foundWords.join(', ')}`);
                        textarea.focus();
                        console.log('Đã chặn nội dung với từ cấm:', foundWords);
                        return false;
                    }
                    
                    this.submit();
                });
            });
        });
    </script>
</div>
@endsection

@section('styles')
<style>
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

    .filter-tab[href*="filter=landlord"]::before {
        content: "\f015";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
    }

    .filter-tab[href*="filter=customer"]::before {
        content: "\f007";
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
            display: none;
        }

        .filter-tab .count-badge {
            margin-left: 0;
        }
    }

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

    /* Đảm bảo comment-section ẩn ban đầu */
    .comment-section {
        display: none !important;
    }
</style>
{{-- <link rel="stylesheet" href="{{ asset('css/listpost.css') }}"> --}}
@endsection