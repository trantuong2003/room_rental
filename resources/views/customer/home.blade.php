@extends('layouts.customer')

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

    <!-- Listing -->
    {{-- <div class="listing">
        <div class="images">
            <img src="https://placehold.co/300x200"
                alt="Interior view of a studio apartment with bed, table, and bathroom">
            <div class="grid">
                <img src="https://placehold.co/100x100" alt="Additional view of the studio apartment">
                <img src="https://placehold.co/100x100" alt="Additional view of the studio apartment">
                <img src="https://placehold.co/100x100" alt="Additional view of the studio apartment">
            </div>
        </div>
        <div class="details">
            <div class="header">
                <h2>Cho thuê phòng studio, 1K1N full đồ, ngõ 44 Trần Thái Tông, Cầu Giấy</h2>
            </div>
            <div class="price">
                5,5 triệu/tháng
                <span>· 28 m²</span>
            </div>
            <div class="location">
                <i class="fas fa-map-marker-alt"></i>
                <span>Cầu Giấy, Hà Nội</span>
            </div>
            <p>Diện tích từ 23m², 28m², 35m². Giá thuê từ 4,9 triệu đến 8 triệu. Tiện ích: Tạp hóa tầng 1, chợ, siêu thị
                100m. Gần học viện Báo Chí, đại học Quốc Gia 200m. Gym, bể bơi, ngân hàng, công viên thể dục 500m. An
                ninh cu...</p>
            <div class="footer">
                <div class="profile">
                    <img src="https://placehold.co/40x40" alt="Profile picture of Nhật Phong">
                    <div>
                        <p>Landord Name</p>
                    </div>
                </div>
                <div class="actions">
                    <button>
                        <i class="fas fa-phone-alt"></i> 0973 808 JQK
                    </button>
                    <button>
                        <i class="far fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- @foreach($posts as $post)
    <div class="listing">
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
                    <img src="https://placehold.co/40x40" alt="Profile picture">
                    <div>
                        <p>{{ $post->landlord->name ?? 'Chủ nhà' }}</p>
                    </div>
                </div>
                <div class="actions">
                    <button>
                        <i class="fas fa-phone-alt"></i> {{ $post->phone ?? 'Không có số' }}
                    </button>
                    <button>
                        <i class="far fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach --}}
    {{--  --}}
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



@endsection