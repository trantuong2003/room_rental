@extends('layouts.landord')

@section('content')
<div class="main">
    <div>
        <div class="detail_container">
            <div class="card layout">
                <div class="left-section">
                    <!-- Hiển thị ảnh chính -->
                    <div class="main-image">
                        @if ($post->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $post->images->first()->image_path) }}" alt="Room for rent">
                        @else
                        <img src="https://placehold.co/300x200" alt="No image">
                        @endif
                    </div>
                    <!-- Hiển thị các ảnh phụ -->
                    <div class="thumbnails">
                        @foreach ($post->images->slice(1, 3) as $image)
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thumbnail">
                        @endforeach
                    </div>

                    <!-- Hiển thị thông tin bài đăng -->
                    <h1 class="header">{{ $post->title }}</h1>
                    <p class="sub-header">{{ $post->address }}</p>
                    <hr>
                    <div class="info">
                        <div>
                            <p class="label">Mức giá</p>
                            <p class="value">{{ $post->price }} </p>
                        </div>
                        <div>
                            <p class="label">Diện tích</p>
                            <p class="value">{{ $post->acreage }} </p>
                        </div>
                        <div>
                            <p class="label">Phòng ngủ</p>
                            <p class="value">{{ $post->bedrooms }} </p>
                        </div>
                        <div class="icons">
                            <div>
                                <p class="label">Status</p>
                                <p class="value">{{ $post->status }}</p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h2 class="section-title">Thông tin mô tả</h2>
                    <p class="description">{{ $post->description }}</p>
                    <h2 class="section-title">Đặc điểm bất động sản</h2>
                    <hr>
                    <div class="features">
                        <div class="feature">
                            <i class="fas fa-money-bill-wave"></i>
                            <p>Mức giá: {{ $post->price }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-ruler-combined"></i>
                            <p>Diện tích: {{ $post->acreage }} </p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-bed"></i>
                            <p>Số phòng ngủ: {{ $post->bedrooms }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-bolt"></i>
                            <p>Mức giá điện: {{ $post->electricity_price ?? 'Do chủ nhà quy định' }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-bath"></i>
                            <p>Số phòng tắm, vệ sinh: {{ $post->bathrooms }} </p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-wifi"></i>
                            <p>Mức giá internet: {{ $post->internet_price ?? 'Do chủ nhà quy định' }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-tint"></i>
                            <p>Mức giá nước: {{ $post->water_price ?? 'Do chủ nhà quy định' }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-clock"></i>
                            <p>Giá dịch vụ: {{ $post->service_price ?? 'Do chủ nhà quy định' }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-video"></i>
                            <p>Tiện ích: {{ $post->utilities ?? 'Cơ bản' }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-couch"></i>
                            <p>Nội thất: {{ $post->furniture ?? 'Cơ bản' }}</p>
                        </div>
                    </div>

                    {{-- Google Maps --}}
                    @if ($post->latitude && $post->longitude)
                    <h2 class="section-title">Vị trí</h2>
                    <div id="map" style="height: 400px; width: 100%;"></div>
                    @endif

                    <!-- Comments Section -->
                    <h2 class="section-title">Bình luận</h2>
                    <div class="comments">
                        <!-- Danh sách bình luận -->
                        <div class="comment-list">
                            @foreach ($post->comments->whereNull('parent_id')->sortByDesc('created_at') as $comment)
                            <div class="comment">
                                <div class="comment-header">
                                    <div class="comment-author-info">
                                        <strong>{{ $comment->user->name }}</strong>
                                        <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    @auth
                                    @if (Auth::id() === $comment->user_id)
                                    <button class="btn-action edit-btn"
                                        onclick="toggleEditForm({{ $comment->id }}, event)">
                                        <i class="fas fa-edit"></i> Chỉnh sửa
                                    </button>
                                    @endif
                                    @endauth
                                </div>

                                <div class="comment-content" id="comment-text-{{ $comment->id }}">
                                    {{ $comment->content }}
                                </div>

                                <!-- Form chỉnh sửa -->
                                @auth
                                @if (Auth::id() === $comment->user_id)
                                <form action="{{ route('landlord.comments.update', $comment->id) }}" method="POST"
                                    class="edit-form" id="edit-form-{{ $comment->id }}" style="display: none;">
                                    @csrf
                                    @method('PUT')
                                    <textarea name="content" required>{{ $comment->content }}</textarea>
                                    <div class="form-actions">
                                        <button type="submit" class="btn-submit">Lưu</button>
                                        <button type="button" class="btn-cancel"
                                            onclick="toggleEditForm(event, {{ $comment->id }})">Hủy</button>
                                    </div>
                                </form>
                                @endif
                                @endauth

                                <!-- Nút trả lời -->
                                <div class="reply-actions">
                                    @auth
                                    @if (auth()->user()->role === 'landlord' && $comment->user->role === 'customer')
                                    <button class="btn-action reply-btn" onclick="toggleReplyForm({{ $comment->id }})">
                                        <i class="fas fa-reply"></i> Trả lời
                                    </button>
                                    @endif
                                    @endauth

                                    @if ($comment->replies->count() > 0)
                                    <button class="btn-action toggle-replies"
                                        onclick="toggleReplies({{ $comment->id }})" id="toggle-btn-{{ $comment->id }}">
                                        <i class="fas fa-comments"></i>
                                        <span class="toggle-text">Xem {{ $comment->replies->count() }} phản hồi</span>
                                        <i class="fas fa-chevron-down toggle-icon"></i>
                                    </button>
                                    @endif
                                </div>

                                <!-- Form trả lời -->
                                @auth
                                @if (auth()->user()->role === 'landlord' && $comment->user->role === 'customer')
                                <form action="{{ route('landlord.comments.store', $post->id) }}" method="POST"
                                    class="reply-form" id="reply-form-{{ $comment->id }}" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <input type="hidden" name="post_type" value="landlord">
                                    <input type="hidden" name="reply_to" value="{{ $comment->user->name }}">
                                    <textarea name="content" placeholder="Viết phản hồi của bạn..." required></textarea>
                                    <div class="form-actions">
                                        <button type="submit" class="btn-submit">Gửi</button>
                                        <button type="button" class="btn-cancel"
                                            onclick="toggleReplyForm({{ $comment->id }})">Hủy</button>
                                    </div>
                                </form>
                                @endif
                                @endauth

                                <!-- Danh sách trả lời -->
                                <div class="replies" id="replies-{{ $comment->id }}" style="display: none;">
                                    @foreach ($comment->replies->sortByDesc('created_at') as $reply)
                                    <div class="comment reply level-2">
                                        <div class="comment-header">
                                            <div class="comment-author-info">
                                                <strong>{{ $reply->user->name }}</strong>
                                                <span class="comment-time">{{ $reply->created_at->diffForHumans()
                                                    }}</span>
                                            </div>
                                            @auth
                                            @if (Auth::id() === $reply->user_id)
                                            <button class="btn-action edit-btn"
                                                onclick="toggleEditForm({{ $reply->id }}, event)">
                                                <i class="fas fa-edit"></i> Chỉnh sửa
                                            </button>
                                            @endif
                                            @endauth
                                        </div>

                                        <div class="comment-content" id="comment-text-{{ $reply->id }}">
                                            @if($reply->parent->user_id !== $reply->user_id)
                                            <span class="reply-to">Trả lời {{ $reply->parent->user->name }}</span><br>
                                            @endif
                                            {{ $reply->content }}
                                        </div>

                                        @auth
                                        @if (Auth::id() === $reply->user_id)
                                        <form action="{{ route('landlord.comments.update', $reply->id) }}" method="POST"
                                            class="edit-form" id="edit-form-{{ $reply->id }}" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                            <textarea name="content" required>{{ $reply->content }}</textarea>
                                            <div class="form-actions">
                                                <button type="submit" class="btn-submit">Lưu</button>
                                                <button type="button" class="btn-cancel"
                                                    onclick="toggleEditForm({{ $reply->id }}, event)">Hủy</button>
                                            </div>
                                        </form>
                                        @endif
                                        @endauth

                                        <!-- Nút trả lời cho reply -->
                                        <div class="reply-actions">
                                            @auth
                                            @if (auth()->user()->role === 'landlord' && $reply->user->role ===
                                            'customer')
                                            <button class="btn-action reply-btn"
                                                onclick="toggleReplyForm({{ $reply->id }})">
                                                <i class="fas fa-reply"></i> Trả lời
                                            </button>
                                            @endif
                                            @endauth

                                            @if ($reply->replies->count() > 0)
                                            <button class="btn-action toggle-replies"
                                                onclick="toggleReplies({{ $reply->id }})"
                                                id="toggle-btn-{{ $reply->id }}">
                                                <i class="fas fa-comments"></i>
                                                <span class="toggle-text">Xem {{ $reply->replies->count() }} phản
                                                    hồi</span>
                                                <i class="fas fa-chevron-down toggle-icon"></i>
                                            </button>
                                            @endif
                                        </div>

                                        <!-- Form trả lời cho reply -->
                                        @auth
                                        @if (auth()->user()->role === 'landlord' && $reply->user->role === 'customer')
                                        <form action="{{ route('landlord.comments.store', $post->id) }}" method="POST"
                                            class="reply-form" id="reply-form-{{ $reply->id }}" style="display: none;">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                            <input type="hidden" name="post_type" value="landlord">
                                            <input type="hidden" name="reply_to" value="{{ $reply->user->name }}">
                                            <textarea name="content" placeholder="Viết phản hồi của bạn..."
                                                required></textarea>
                                            <div class="form-actions">
                                                <button type="submit" class="btn-submit">Gửi</button>
                                                <button type="button" class="btn-cancel"
                                                    onclick="toggleReplyForm({{ $reply->id }})">Hủy</button>
                                            </div>
                                        </form>
                                        @endif
                                        @endauth

                                        <!-- Danh sách trả lời của reply (level 3) -->
                                        <div class="replies level-3-replies" id="replies-{{ $reply->id }}"
                                            style="display: none;">
                                            @foreach ($reply->replies->sortByDesc('created_at') as $replyLevel3)
                                            <div class="comment reply level-3">
                                                <div class="comment-header">
                                                    <div class="comment-author-info">
                                                        <strong>{{ $replyLevel3->user->name }}</strong>
                                                        <span class="comment-time">{{
                                                            $replyLevel3->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    @auth
                                                    @if (Auth::id() === $replyLevel3->user_id)
                                                    <button class="btn-action edit-btn"
                                                        onclick="toggleEditForm({{ $replyLevel3->id }}, event)">
                                                        <i class="fas fa-edit"></i> Chỉnh sửa
                                                    </button>
                                                    @endif
                                                    @endauth
                                                </div>

                                                <div class="comment-content" id="comment-text-{{ $replyLevel3->id }}">
                                                    @if($replyLevel3->parent->user_id !== $replyLevel3->user_id)
                                                    <span class="reply-to">Trả lời {{ $replyLevel3->parent->user->name
                                                        }}</span><br>
                                                    @endif
                                                    {{ $replyLevel3->content }}
                                                </div>

                                                <!-- FORM EDIT cho reply level 3 -->
                                                @auth
                                                @if (Auth::id() === $replyLevel3->user_id)
                                                <form action="{{ route('landlord.comments.update', $replyLevel3->id) }}"
                                                    method="POST" class="edit-form"
                                                    id="edit-form-{{ $replyLevel3->id }}" style="display: none;">
                                                    @csrf
                                                    @method('PUT')
                                                    <textarea name="content"
                                                        required>{{ $replyLevel3->content }}</textarea>
                                                    <div class="form-actions">
                                                        <button type="submit" class="btn-submit">Lưu</button>
                                                        <button type="button" class="btn-cancel"
                                                            onclick="toggleEditForm({{ $replyLevel3->id }}, event)">Hủy</button>
                                                    </div>
                                                </form>
                                                @endif
                                                @endauth
                                                {{--
                                                <!-- Nút trả lời cho reply level 3 (không hiển thị thêm nút xem phản hồi) -->
                                                <div class="reply-actions">
                                                    @auth
                                                    @if (auth()->user()->role === 'landlord' && $replyLevel3->user->role
                                                    === 'customer')
                                                    <button class="btn-action reply-btn"
                                                        onclick="toggleReplyForm({{ $replyLevel3->id }})">
                                                        <i class="fas fa-reply"></i> Trả lời
                                                    </button>
                                                    @endif
                                                    @endauth
                                                </div>

                                                <!-- Form trả lời cho reply level 3 -->
                                                @auth
                                                @if (auth()->user()->role === 'landlord' && $replyLevel3->user->role ===
                                                'customer')
                                                <form action="{{ route('landlord.comments.store', $post->id) }}"
                                                    method="POST" class="reply-form"
                                                    id="reply-form-{{ $replyLevel3->id }}" style="display: none;">
                                                    @csrf
                                                    <input type="hidden" name="parent_id"
                                                        value="{{ $replyLevel3->id }}">
                                                    <input type="hidden" name="post_type" value="landlord">
                                                    <input type="hidden" name="reply_to"
                                                        value="{{ $replyLevel3->user->name }}">
                                                    <textarea name="content" placeholder="Viết phản hồi của bạn..."
                                                        required></textarea>
                                                    <div class="form-actions">
                                                        <button type="submit" class="btn-submit">Gửi</button>
                                                        <button type="button" class="btn-cancel"
                                                            onclick="toggleReplyForm({{ $replyLevel3->id }})">Hủy</button>
                                                    </div>
                                                </form>
                                                @endif
                                                @endauth --}}
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Nhúng Google Maps API -->
@if ($post->latitude && $post->longitude)
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap">
</script>
<script>
    function initMap() {
            console.log("initMap called");
            var postLocation = { lat: parseFloat("{{ $post->latitude }}"), lng: parseFloat("{{ $post->longitude }}") };
            console.log("Post Location:", postLocation);
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: postLocation
            });
            var marker = new google.maps.Marker({
                position: postLocation,
                map: map
            });
        }

//comment
// Hàm toggle reply form
    function toggleReplyForm(commentId) {
        var form = document.getElementById("reply-form-" + commentId);
        if (form.style.display === "none" || form.style.display === "") {
            // Ẩn tất cả các form reply khác
            document.querySelectorAll('.reply-form').forEach(function(f) {
                if (f.id !== 'reply-form-' + commentId) f.style.display = 'none';
            });
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }

    // Hàm toggle edit form
    // function toggleEditForm(commentId) {
    //     var form = document.getElementById("edit-form-" + commentId);
    //     if (form.style.display === "none" || form.style.display === "") {
    //         document.querySelectorAll('.edit-form').forEach(f => {
    //             if (f.id !== 'edit-form-' + commentId) f.style.display = 'none';
    //         });
    //         form.style.display = "block";
    //     } else {
    //         form.style.display = "none";
    //     }
    // }

    function toggleEditForm(commentId, event = null) {
    // Ngăn chặn hành vi mặc định nếu có event
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    console.log('Toggle edit form for comment:', commentId);
    
    // Ẩn tất cả các form edit và reply khác
    document.querySelectorAll('.edit-form, .reply-form').forEach(form => {
        if (form.id !== 'edit-form-' + commentId) {
            form.style.display = 'none';
        }
    });
    
    // Toggle form hiện tại
    const form = document.getElementById('edit-form-' + commentId);
    if (form) {
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
        
        // Focus vào textarea nếu form được hiển thị
        if (form.style.display === 'block') {
            const textarea = form.querySelector('textarea');
            if (textarea) {
                textarea.focus();
                // Đặt cursor ở cuối nội dung
                textarea.setSelectionRange(textarea.value.length, textarea.value.length);
            }
        }
    } else {
        console.error('Form not found with ID:', 'edit-form-' + commentId);
    }
}

    // Hàm toggle replies
    function toggleReplies(commentId) {
        var repliesDiv = document.getElementById("replies-" + commentId);
        var toggleBtn = document.getElementById("toggle-btn-" + commentId);
        var toggleIcon = toggleBtn.querySelector('.toggle-icon');
        var toggleText = toggleBtn.querySelector('.toggle-text');
        
        if (repliesDiv.style.display === "none" || repliesDiv.style.display === "") {
            repliesDiv.style.display = "block";
            toggleText.textContent = "Thu nhỏ";
            toggleIcon.classList.remove('fa-chevron-down');
            toggleIcon.classList.add('fa-chevron-up');
        } else {
            repliesDiv.style.display = "none";
            toggleText.textContent = "Xem " + repliesDiv.querySelectorAll('.reply').length + " phản hồi";
            toggleIcon.classList.remove('fa-chevron-up');
            toggleIcon.classList.add('fa-chevron-down');
        }
    }
</script>
@endif

@endsection