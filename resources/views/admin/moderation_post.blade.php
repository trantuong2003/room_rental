@extends('layouts.admin')

@section('content')
@foreach($posts as $post)
<div class="detail_container">
    <div class="card layout">
        <div class="left-section">
            <!-- Hiển thị ảnh chính và ảnh phụ -->
            <div class="image-container">
                <!-- Ảnh chính -->
                <div class="main-image">
                    @if ($post->images->isNotEmpty())
                    <img src="{{ asset('storage/' . $post->images->first()->image_path) }}" alt="Room for rent">
                    @else
                    <img src="https://placehold.co/300x200" alt="No image">
                    @endif
                </div>

                <!-- Ảnh phụ -->
                <div class="thumbnails">
                    @foreach ($post->images->slice(1, 3) as $image)
                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thumbnail">
                    @endforeach
                    @if ($post->images->count() > 4)
                    <div class="see-more-overlay" onclick="showAllImages({{ $post->id }})">
                        <span>+{{ $post->images->count() - 4 }}</span>
                        <p>Xem thêm</p>
                    </div>
                    @endif
                </div>
            </div>
            <!-- Modal hiển thị tất cả ảnh phụ -->
            <div id="image-modal-{{ $post->id }}" class="image-modal hidden">
                <div class="modal-content">
                    <span class="close" onclick="closeModal({{ $post->id }})">&times;</span>
                    <div class="carousel">
                        @foreach ($post->images as $index => $image)
                        <div class="carousel-item" style="{{ $index === 0 ? 'display: block;' : 'display: none;' }}">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Image {{ $index + 1 }}">
                        </div>
                        @endforeach
                        <button class="carousel-control prev" onclick="prevImage({{ $post->id }})">&#10094;</button>
                        <button class="carousel-control next" onclick="nextImage({{ $post->id }})">&#10095;</button>
                    </div>
                </div>
            </div>
            <!-- Hiển thị title và address -->
            <h1 class="header">{{ $post->title }}</h1>
            <p class="sub-header">{{ $post->address }}</p>
            <p class="username">Người đăng: {{ $post->user->name ?? 'Không xác định' }}</p>

            <!-- Nút Xem thêm -->
            <button id="btn-show-{{ $post->id }}" class="btn btn-blue toggle-details"
                onclick="toggleDetails({{ $post->id }})">
                Xem thêm
            </button>

            <!-- Nút Duyệt / Không duyệt -->
            <div class="moderation-buttons">
                <form action="{{ route('posts.approve', $post->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-green">Duyệt</button>
                </form>
                <form action="{{ route('posts.reject', $post->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-red">Không duyệt</button>
                </form>
            </div>

            <!-- Phần thông tin chi tiết (ẩn mặc định) -->
            <div id="details-{{ $post->id }}" class="details" style="display: none;">
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
                    <div>
                        <p class="label">Status</p>
                        <p class="value">{{ $post->status }}</p>
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
                        <p>Diện tích: {{ $post->acreage }}</p>
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
                        <p>Số phòng tắm, vệ sinh: {{ $post->bathrooms }}</p>
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

                <!-- Nút Thu gọn -->
                <button class="btn btn-blue toggle-details" onclick="toggleDetails({{ $post->id }})">
                    Thu gọn
                </button>

                <!-- Nút Duyệt / Không duyệt -->
                <div class="moderation-buttons">
                    <form action="{{ route('posts.approve', $post->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-green">Duyệt</button>
                    </form>
                    <form action="{{ route('posts.reject', $post->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-red">Không duyệt</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- JavaScript để xử lý toggle và hiển thị ảnh -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
    let currentIndex = {};

    function toggleDetails(postId) {
    console.log("Toggle details for post:", postId); // Kiểm tra xem hàm có được gọi không
    let details = document.getElementById('details-' + postId);
    let btnShow = document.getElementById('btn-show-' + postId);

    if (details.style.display === 'none') {
        details.style.display = 'block';
        btnShow.textContent = 'Thu gọn';
    } else {
        details.style.display = 'none';
        btnShow.textContent = 'Xem thêm';
    }
}

    function showAllImages(postId) {
        let modal = document.getElementById('image-modal-' + postId);
        if (!modal) return;

        modal.classList.add("active"); // Hiển thị modal
        currentIndex[postId] = 0; // Bắt đầu từ ảnh đầu tiên
        showImage(postId, currentIndex[postId]);

        // Đóng modal khi bấm ra ngoài
        modal.addEventListener("click", function (event) {
            if (event.target === modal) {
                closeModal(postId);
            }
        });
    }

    function closeModal(postId) {
        let modal = document.getElementById('image-modal-' + postId);
        if (modal) {
            modal.classList.remove("active"); // Ẩn modal
        }
    }

    function showImage(postId, index) {
        let carouselItems = document.querySelectorAll(`#image-modal-${postId} .carousel-item`);
        if (!carouselItems.length) return;

        carouselItems.forEach((item, i) => {
            item.style.display = i === index ? 'block' : 'none';
        });
    }

    function prevImage(postId) {
        let carouselItems = document.querySelectorAll(`#image-modal-${postId} .carousel-item`);
        if (!carouselItems.length) return;

        currentIndex[postId] = (currentIndex[postId] - 1 + carouselItems.length) % carouselItems.length;
        showImage(postId, currentIndex[postId]);
    }

    function nextImage(postId) {
        let carouselItems = document.querySelectorAll(`#image-modal-${postId} .carousel-item`);
        if (!carouselItems.length) return;

        currentIndex[postId] = (currentIndex[postId] + 1) % carouselItems.length;
        showImage(postId, currentIndex[postId]);
    }

    // Gán các hàm vào `window` để có thể gọi từ HTML
    window.showAllImages = showAllImages;
    window.closeModal = closeModal;
    window.prevImage = prevImage;
    window.nextImage = nextImage;
    window.toggleDetails = toggleDetails;
});

</script>
@endsection