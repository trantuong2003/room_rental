@extends('layouts/customer');
@section('content')
<!-- Listing -->
<div class="container">
    <h2> My favourite room</h2>

    @foreach($favorites as $favorite)
    <div class="listing" onclick="redirectToDetail({{ $favorite->id }})" style="cursor: pointer;">
        <div class="images">
            <img src="{{ asset('storage/' . $favorite->images->first()->image_path) }}" alt="Hình ảnh chính">
            <div class="grid">
                @foreach ($favorite->images->slice(1, 3) as $image)
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Hình ảnh bổ sung">
                @endforeach
            </div>
        </div>
        <div class="details">
            <div class="header">
                <h2>{{ $favorite->title }}</h2>
            </div>
            <div class="price">
                {{ $favorite->price }}
                <span>. {{ $favorite->acreage }}</span>
            </div>
            <div class="location">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ $favorite->address }}</span>
            </div>
            <p>{{ Str::limit($favorite->description, 150) }}</p>
            <div class="footer">
                <div class="profile">
                    <img {{ asset('assets/image/userimage.jpg') }} alt="Profile picture">
                    <div>
                        <p>{{ $favorite->landlord->name ?? 'Chủ nhà' }}</p>
                    </div>
                </div>
                <div class="actions">
                    <button onclick="event.stopPropagation();">
                        <i class="fas fa-phone-alt"></i> {{ $favorite->phone ?? 'Không có số' }}
                    </button>
                    <button class="favorite-btn" data-post-id="{{ $favorite->id }}" style="cursor: pointer;">
                        <i class="fas fa-heart" style="color: {{ $favorite->isFavorited ? 'red' : 'gray' }};"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection


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