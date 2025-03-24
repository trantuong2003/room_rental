@extends('layouts/customer');

@section('content')
<div class="container">
    <div class="card layout">
        <div class="left-section">
            <div class="main-image">
                @if ($post->images->isNotEmpty())
                <img src="{{ asset('storage/' . $post->images->first()->image_path) }}" alt="Room for rent">
                @else
                <img src="https://placehold.co/300x200" alt="No image">
                @endif
            </div>
            <div class="thumbnails">
                @foreach ($post->images->slice(1, 8) as $image)
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thumbnail">
                @endforeach
            </div>
            <h1 class="header">{{ $post->title }}</h1>
            <p class="sub-header">{{ $post->address }}</p>
            <hr>
            <div class="info">
                <div>
                    <p class="label">Mức giá</p>
                    <p class="value">{{ $post->price }}</p>
                </div>
                <div>
                    <p class="label">Diện tích</p>
                    <p class="value">{{ $post->acreage }}</p>
                </div>
                <div>
                    <p class="label">Phòng ngủ</p>
                    <p class="value">{{ $post->bedrooms }}</p>
                </div>
                <div class="icons">
                    <i class="fas fa-share-alt"></i>
                    <div class="favorite-btn" data-post-id="{{ $post->id }}" style="cursor: pointer;">
                        <i class="fas fa-heart" style="color: {{ $post->isFavorited ? 'red' : 'gray' }};"></i>
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
                 <!-- Form viết bình luận -->
                <form action="{{ route('customer.comments.store', $post->id) }}" method="POST">
                    @csrf
                    <textarea name="content" placeholder="Viết bình luận của bạn..." required></textarea>
                    <button type="submit">Gửi bình luận</button>
                </form>

                <!-- DANH SÁCH BÌNH LUẬN -->
                <div class="comment-list">
                    @foreach ($post->comments->where('parent_id', null)->sortByDesc('created_at') as $comment)
                        <div class="comment">
                            <p><strong>{{ $comment->user->name }}</strong> - {{ $comment->created_at->diffForHumans() }}</p>
                            <p id="comment-text-{{ $comment->id }}">{{ $comment->content }}</p>

                            <!-- Nút "Chỉnh sửa" -->
                            @auth
                                @if (Auth::id() === $comment->user_id)
                                    <button class="edit-btn" onclick="toggleEditForm({{ $comment->id }})">Chỉnh sửa</button>
                                    <form action="{{ route('customer.comments.update', $comment->id) }}" method="POST" class="edit-form" id="edit-form-{{ $comment->id }}" style="display: none;">
                                        @csrf
                                        @method('PUT')
                                        <textarea name="content" required>{{ $comment->content }}</textarea>
                                        <button type="submit">Lưu</button>
                                    </form>
                                @endif
                            @endauth

                            <!-- Nút "Trả lời" -->
                            @auth
                                @if (Auth::user()->role === 'customer' || (Auth::user()->role === 'landlord' && $comment->user->role === 'customer'))
                                    <button class="reply-btn" onclick="toggleReplyForm({{ $comment->id }})">Trả lời</button>
                                @endif
                            @endauth

                            <!-- Form trả lời bình luận -->
                            <form action="{{ route('customer.comments.store', $post->id) }}" method="POST" class="reply-form" id="reply-form-{{ $comment->id }}" style="display: none;">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <textarea name="content" placeholder="Trả lời..." required></textarea>
                                <button type="submit">Gửi</button>
                            </form>

                            <!-- Danh sách trả lời -->
                            @if ($comment->replies->count() > 0)
                                <button class="toggle-replies" onclick="toggleReplies({{ $comment->id }})">
                                    Xem {{ $comment->replies->count() }} phản hồi
                                </button>
                                <div class="replies" id="replies-{{ $comment->id }}" style="display: none;">
                                    @foreach ($comment->replies as $reply)
                                        <div class="comment reply">
                                            <p><strong>{{ $reply->user->name }}</strong> - {{ $reply->created_at->diffForHumans() }}</p>
                                            <p id="comment-text-{{ $reply->id }}">{{ $reply->content }}</p>

                                            <!-- Nút "Chỉnh sửa" -->
                                            @auth
                                                @if (Auth::id() === $reply->user_id)
                                                    <button class="edit-btn" onclick="toggleEditForm({{ $reply->id }})">Chỉnh sửa</button>
                                                    <form action="{{ route('customer.comments.update', $reply->id) }}" method="POST" class="edit-form" id="edit-form-{{ $reply->id }}" style="display: none;">
                                                        @csrf
                                                        @method('PUT')
                                                        <textarea name="content" required>{{ $reply->content }}</textarea>
                                                        <button type="submit">Lưu</button>
                                                    </form>
                                                @endif
                                            @endauth
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="right-section sticky">
            <div class="profile-landord">
                <div><img class="avatar" src="{{ asset('assets/image/customer02.jpg') }}" alt=""></div>
                <div class="infor">
                    <h2>Landord name</h2>
                    <div class="address">
                        <div>
                            <button class="zalo">Nhắn tin ngay</button>
                        </div>
                        <button class="phone">Phone number: 0938 045 JQK</button>
                    </div>

                </div>
            </div>
            <div class="warning">
                <p><i class="fas fa-exclamation-circle"></i> Không nên đặt cọc, giao dịch trước khi xem nhà.</p>
            </div>
        </div>
    </div>

</div>


{{--
<meta name="csrf-token" content="{{ csrf_token() }}"> --}}
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
        
    //yeu thich
        document.addEventListener("DOMContentLoaded", function () {
    let buttons = document.querySelectorAll('.favorite-btn');
    console.log("Number of buttons found:", buttons.length); // Kiểm tra số lượng nút được tìm thấy

    buttons.forEach(button => {
        button.addEventListener('click', function (event) {
            console.log("Button clicked!"); // Kiểm tra xem sự kiện click có hoạt động không
            event.stopPropagation(); // Ngăn sự kiện click trên phần tử cha

            let postId = this.getAttribute('data-post-id');
            let icon = this.querySelector('i');

            if (!icon) {
                console.error("Icon element not found!");
                return;
            }

            console.log("Post ID:", postId);
            console.log("Icon element:", icon);

            fetch("{{ route('customer.detail.toggleFavorite') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ post_id: postId })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Response:", data); // Kiểm tra phản hồi từ server
                if (data.status === "added") {
                    icon.style.color = "red"; // Chuyển màu đỏ khi yêu thích
                } else if (data.status === "removed") {
                    icon.style.color = "gray"; // Chuyển màu xám khi bỏ yêu thích
                }
            })
            .catch(error => console.error("Error:", error));
        });
    });
});

//comment
function toggleReplyForm(commentId) {
        var form = document.getElementById("reply-form-" + commentId);
        form.style.display = (form.style.display === "none") ? "block" : "none";
    }

    function toggleReplies(commentId) {
        var repliesDiv = document.getElementById("replies-" + commentId);
        repliesDiv.style.display = (repliesDiv.style.display === "none") ? "block" : "none";
    }

    function toggleEditForm(commentId) {
        var form = document.getElementById("edit-form-" + commentId);
        form.style.display = (form.style.display === "none") ? "block" : "none";
    }
</script>
@endif

@endsection