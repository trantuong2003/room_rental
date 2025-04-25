@extends('layouts.customer')

@section('content')
<div class="container">
    <!-- Phần tìm kiếm và filter giữ nguyên -->
    <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Enter the location">
        <button class="search-button">Tìm kiếm</button>
        <button class="map-button"><i class="fas fa-map-marked-alt"></i> Xem bản đồ</button>
    </div>
    <div class="filter">
        <div class="filter-item">
            <div>
                <span class="filter-name">Loại nhà đất</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="option">
                <span>Tất cả</span>
            </div>

        </div>
        <div class="filter-item">
            <div>
                <span class="filter-name">Khu vực</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="option">
                <span>Toàn quốc</span>
            </div>
        </div>
        <div class="filter-item">
            <div>
                <span class="filter-name">Khoảng giá</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="option">
                <span>Tất cả</span>
            </div>
        </div>
        <div class="filter-item">
            <div>
                <span class="filter-name">Diện tích</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="option">
                <span>Tất cả</span>
            </div>
        </div>
        <button class="reset-button">
            <i class="fas fa-sync-alt"></i>
            <span>Đặt lại</span>
        </button>
    </div>
    <!-- Listing -->
    @foreach($posts as $post)
    <div class="listing" onclick="redirectToDetail('{{ $post->type }}', {{ $post->id }})" style="cursor: pointer;">
        <div class="images">
            <img src="{{ asset('storage/' . $post->images->first()->image_path) }}" alt="Hình ảnh chính">
            <div class="grid">
                @foreach ($post->images->slice(1, 3) as $image)
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Hình ảnh bổ sung">
                @endforeach
            </div>
        </div>
        <div class="details">
            <div class="header">
                <h2>{{ $post->title }}</h2>
            </div>
            <div class="price">
                {{ $post->price }}
                <span>. {{ $post->acreage }}</span>
            </div>
            <div class="location">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ $post->address }}</span>
            </div>
            <p>{{ Str::limit($post->description, 150) }}</p>
            <div class="footer">
                <div class="profile">
                    <img src="{{ asset('assets/image/userimage.jpg') }}" alt="Profile picture">
                    <div>
                        <p>{{ $post->user->name ?? 'Chủ nhà' }}</p>
                    </div>
                </div>
                <div class="actions">
                    <button onclick="event.stopPropagation();">
                        <i class="fas fa-phone-alt"></i> {{ $post->user->phone ?? 'Không có số' }}
                    </button>
                    <button class="favorite-btn" 
                            data-post-id="{{ $post->id }}" 
                            data-post-type="{{ $post->type }}" 
                            style="cursor: pointer;"
                            onclick="event.stopPropagation(); toggleFavorite(event)">
                        <i class="fas fa-heart" style="color: {{ $post->isFavorited ? 'red' : 'gray' }};"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    function redirectToDetail(postType, postId) {
        const route = postType === 'landlord' 
            ? "{{ route('customer.post.detail', '') }}/" + postId
            : "{{ route('customer.post.detail', '') }}/" + postId;
        window.location.href = route;
    }

    async function toggleFavorite(event) {
        const button = event.currentTarget;
        const postId = button.getAttribute('data-post-id');
        const postType = button.getAttribute('data-post-type');
        const icon = button.querySelector('i');

        try {
            const response = await fetch("{{ route('customer.post.toggleFavorite') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ 
                    post_id: postId,
                    post_type: postType
                })
            });

            const data = await response.json();
            
            if (data.status === "added") {
                icon.style.color = "red";
            } else if (data.status === "removed") {
                icon.style.color = "gray";
            }
        } catch (error) {
            console.error("Error:", error);
        }
    }
</script>

@endsection


{{-- @extends('layouts.customer')

@section('content')
<div class="container">
    <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Enter the location">
        <button class="search-button">Tìm kiếm</button>
        <button class="map-button"><i class="fas fa-map-marked-alt"></i> Xem bản đồ</button>
    </div>
    <div class="filter">
        <div class="filter-item">
            <div>
                <span class="filter-name">Loại nhà đất</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="option">
                <span>Tất cả</span>
            </div>

        </div>
        <div class="filter-item">
            <div>
                <span class="filter-name">Khu vực</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="option">
                <span>Toàn quốc</span>
            </div>
        </div>
        <div class="filter-item">
            <div>
                <span class="filter-name">Khoảng giá</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="option">
                <span>Tất cả</span>
            </div>
        </div>
        <div class="filter-item">
            <div>
                <span class="filter-name">Diện tích</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="option">
                <span>Tất cả</span>
            </div>
        </div>
        <button class="reset-button">
            <i class="fas fa-sync-alt"></i>
            <span>Đặt lại</span>
        </button>
    </div>

    @foreach($posts as $post)
    <div class="listing" onclick="redirectToDetail({{ $post->id }})" style="cursor: pointer;">
        <div class="images">
            <img src="{{ asset('storage/' . $post->images->first()->image_path) }}" alt="Hình ảnh chính">
            <div class="grid">
                @foreach ($post->images->slice(1, 3) as $image)
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Hình ảnh bổ sung">
                @endforeach
            </div>
        </div>
        <div class="details">
            <div class="header">
                <h2>{{ $post->title }}</h2>
            </div>
            <div class="price">
                {{ $post->price }}
                <span>. {{ $post->acreage }}</span>
            </div>
            <div class="location">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ $post->address }}</span>
            </div>
            <p>{{ Str::limit($post->description, 150) }}</p>
            <div class="footer">
                <div class="profile">
                    <img {{ asset('assets/image/userimage.jpg') }} alt="Profile picture">
                    <div>
                        <p>{{ $post->user->name ?? 'Chủ nhà' }}</p>
                    </div>
                </div>
                <div class="actions">
                    <button onclick="event.stopPropagation();">
                        <i class="fas fa-phone-alt"></i> {{ $post->user->phone ?? 'Không có số' }}
                    </button>
                    <button class="favorite-btn" data-post-id="{{ $post->id }}" style="cursor: pointer;">
                        <i class="fas fa-heart" style="color: {{ $post->isFavorited ? 'red' : 'gray' }};"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    function redirectToDetail(postId) {
        window.location.href = "{{ route('customer.post.detail', '') }}/" + postId;
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Lấy tất cả các nút yêu thích
        document.querySelectorAll('.favorite-btn').forEach(button => {
            button.addEventListener('click', function (event) {
                event.stopPropagation(); // Ngăn sự kiện click trên phần tử cha

                // Lấy postId và biểu tượng trái tim
                let postId = this.getAttribute('data-post-id');
                let icon = this.querySelector('i');

                // Gửi yêu cầu AJAX đến server
                fetch("{{ route('customer.post.toggleFavorite') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ post_id: postId })
                })
                .then(response => response.json())
                .then(data => {
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
</script>



@endsection --}}