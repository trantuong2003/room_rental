@extends('layouts.admin')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Subscription Packages</h1>
        <ul class="breadcrumb">
            <li>
                <a href="#">Dashboard</a>
            </li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li>
                <a class="active" href="#">Moderation landlord post</a>
            </li>
        </ul>
    </div>
</div>

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
                        <p>See more</p>
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
            <p class="username">Posted by: {{ $post->user->name ?? 'Không xác định' }}</p>
            
            <!-- Hiển thị trạng thái -->
            <p class="status">Status: <span class="status-badge status-{{ $post->status }}">{{ $post->status }}</span></p>
            
            @if($post->status === 'rejected' && $post->rejection_reason)
            <div class="rejection-reason">
                <p class="rejection-title">Reason for rejection:</p>
                <p class="rejection-text">{{ $post->rejection_reason }}</p>
            </div>
            @endif

            <!-- Nút Xem thêm -->
            <button id="btn-show-{{ $post->id }}" class="btn btn-blue toggle-details"
                onclick="toggleDetails({{ $post->id }})">
                More
            </button>

            <!-- Nút Duyệt / Không duyệt - hiển thị cho tất cả bài đăng -->
            <div class="moderation-buttons">
                <button type="button" class="btn btn-green" onclick="confirmApprove({{ $post->id }})">Approval</button>
                <button type="button" class="btn btn-red" onclick="showRejectModal({{ $post->id }})">Reject</button>
            </div>

            <!-- Form ẩn cho Duyệt -->
            <form id="approve-form-{{ $post->id }}" action="{{ route('posts.approve', $post->id) }}" method="POST" style="display: none;">
                @csrf
                @method('PATCH')
            </form>

            <!-- Phần thông tin chi tiết (ẩn mặc định) -->
            <div id="details-{{ $post->id }}" class="details" style="display: none;">
                <hr>
                <div class="info">
                    <div>
                        <p class="label">Price</p>
                        <p class="value">{{ $post->price }}</p>
                    </div>
                    <div>
                        <p class="label">Acreage</p>
                        <p class="value">{{ $post->acreage }}</p>
                    </div>
                    <div>
                        <p class="label">Bedroom</p>
                        <p class="value">{{ $post->bedrooms }}</p>
                    </div>
                    <div>
                        <p class="label">Status</p>
                        <p class="value">{{ $post->status }}</p>
                    </div>
                </div>
                <hr>
                <h2 class="section-title">Description information</h2>
                <p class="description">{{ $post->description }}</p>
                <h2 class="section-title">Real estate features</h2>
                <hr>
                <div class="features">
                    <div class="feature">
                        <i class="fas fa-money-bill-wave"></i>
                        <p>Price: {{ $post->price }}</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-ruler-combined"></i>
                        <p>Acreage: {{ $post->acreage }}</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-bed"></i>
                        <p>Bedrooms: {{ $post->bedrooms }}</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-bolt"></i>
                        <p>Electricity price: {{ $post->electricity_price ?? 'Do chủ nhà quy định' }}</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-bath"></i>
                        <p>Bathrooms {{ $post->bathrooms }}</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-wifi"></i>
                        <p>Internet price: {{ $post->internet_price ?? 'Do chủ nhà quy định' }}</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-tint"></i>
                        <p>Water price: {{ $post->water_price ?? 'Do chủ nhà quy định' }}</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-clock"></i>
                        <p>Service price: {{ $post->service_price ?? 'Do chủ nhà quy định' }}</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-video"></i>
                        <p>Utilities: {{ $post->utilities ?? 'Cơ bản' }}</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-couch"></i>
                        <p>Interior: {{ $post->furniture ?? 'Cơ bản' }}</p>
                    </div>
                </div>

                <!-- Nút Thu gọn -->
                <button class="btn btn-blue toggle-details" onclick="toggleDetails({{ $post->id }})">
                    Collapse
                </button>

                <!-- Nút Duyệt / Không duyệt (trong phần detail) - hiển thị cho tất cả bài đăng -->
                <div class="moderation-buttons">
                    <button type="button" class="btn btn-green" onclick="confirmApprove({{ $post->id }})">Approval</button>
                    <button type="button" class="btn btn-red" onclick="showRejectModal({{ $post->id }})">Reject</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal từ chối -->
<div id="reject-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeRejectModal()">&times;</span>
        <h2>Reasons for post rejection</h2>
        <form id="reject-form" action="" method="POST">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label for="rejection_reason">Please enter reason for rejection:</label>
                <textarea name="rejection_reason" id="rejection_reason" rows="5" class="form-control" required minlength="10" maxlength="500"></textarea>
                <small class="text-muted">Minimum 10 characters, maximum 500 characters</small>
            </div>
            <div class="button-group">
                <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="btn btn-red">Confirm rejection</button>
            </div>
        </form>
    </div>
</div>

<!-- CSS cho modal, status badges và rejection reasons -->
<style>
    /* Status badges */
    .status-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: bold;
    }
    .status-pending {
        background-color: #ffb74d;
        color: #fff;
    }
    .status-approved {
        background-color: #4caf50;
        color: #fff;
    }
    .status-rejected {
        background-color: #f44336;
        color: #fff;
    }
    
    /* Rejection reason */
    .rejection-reason {
        margin: 10px 0;
        padding: 10px;
        background-color: #ffebee;
        border-left: 4px solid #f44336;
        border-radius: 4px;
    }
    .rejection-title {
        font-weight: bold;
        margin-bottom: 5px;
        color: #d32f2f;
    }
    .rejection-text {
        margin: 0;
    }
    
    /* Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
        background-color: #fefefe;
        margin: 10% auto;
        padding: 20px;
        border-radius: 5px;
        width: 60%;
        max-width: 600px;
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
    }
    .form-group {
        margin-bottom: 15px;
    }
    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    textarea.form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .button-group {
        text-align: right;
        margin-top: 15px;
    }
    .btn-secondary {
        background-color: #757575;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }
    
    /* Image modal fix */
    .image-modal.hidden {
        display: none;
    }
    .image-modal.active {
        display: block;
    }
</style>

<!-- JavaScript để xử lý toggle và hiển thị ảnh -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let currentIndex = {};
        
        // Hàm toggle details
        window.toggleDetails = function(postId) {
            console.log("Toggle details for post:", postId);
            let details = document.getElementById('details-' + postId);
            let btnShow = document.getElementById('btn-show-' + postId);

            if (details.style.display === 'none') {
                details.style.display = 'block';
                btnShow.textContent = 'Collapse';
            } else {
                details.style.display = 'none';
                btnShow.textContent = 'More';
            }
        }

        // Hàm xác nhận duyệt
        window.confirmApprove = function(postId) {
            if (confirm("Are you sure you want to browse this post?")) {
                document.getElementById('approve-form-' + postId).submit();
            }
        }

        // Hàm hiển thị modal từ chối
        window.showRejectModal = function(postId) {
            const modal = document.getElementById('reject-modal');
            const form = document.getElementById('reject-form');
            // Sửa lại đường dẫn route để khớp với route bạn đã định nghĩa
            form.action = "/admin/moderation_post/" + postId + "/reject";
            modal.style.display = "block";
        }

        // Hàm đóng modal từ chối
        window.closeRejectModal = function() {
            const modal = document.getElementById('reject-modal');
            modal.style.display = "none";
            document.getElementById('rejection_reason').value = '';
        }

        // Hàm xử lý hiển thị ảnh
        window.showAllImages = function(postId) {
            let modal = document.getElementById('image-modal-' + postId);
            if (!modal) return;

            modal.classList.add("active");
            currentIndex[postId] = 0;
            showImage(postId, currentIndex[postId]);

            modal.addEventListener("click", function (event) {
                if (event.target === modal) {
                    closeModal(postId);
                }
            });
        }

        // Hàm đóng modal ảnh
        window.closeModal = function(postId) {
            let modal = document.getElementById('image-modal-' + postId);
            if (modal) {
                modal.classList.remove("active");
            }
        }

        // Hàm hiển thị ảnh
        function showImage(postId, index) {
            let carouselItems = document.querySelectorAll(`#image-modal-${postId} .carousel-item`);
            if (!carouselItems.length) return;

            carouselItems.forEach((item, i) => {
                item.style.display = i === index ? 'block' : 'none';
            });
        }

        // Hàm chuyển đến ảnh trước
        window.prevImage = function(postId) {
            let carouselItems = document.querySelectorAll(`#image-modal-${postId} .carousel-item`);
            if (!carouselItems.length) return;

            currentIndex[postId] = (currentIndex[postId] - 1 + carouselItems.length) % carouselItems.length;
            showImage(postId, currentIndex[postId]);
        }

        // Hàm chuyển đến ảnh tiếp theo
        window.nextImage = function(postId) {
            let carouselItems = document.querySelectorAll(`#image-modal-${postId} .carousel-item`);
            if (!carouselItems.length) return;

            currentIndex[postId] = (currentIndex[postId] + 1) % carouselItems.length;
            showImage(postId, currentIndex[postId]);
        }
    });
</script>
@endsection