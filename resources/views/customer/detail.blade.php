@extends('layouts/customer');

@section('content')
<div class="container">
    <div class="card layout">
        <div class="left-section">
            <div class="main-image">
                <img src="https://storage.googleapis.com/a1aa/image/hFKybOLqEDx33aaXxZbp7xUGnCS5YY1Nf9BEi2HhJOo.jpg" alt="Room for rent">
            </div>
            <div class="thumbnails">
                <img src="https://storage.googleapis.com/a1aa/image/R_3qY45BZ7T9Xu0vQSd9hsZjUJ2zNi0JmfG4dKijdAE.jpg" alt="Thumbnail 1">
                <img src="https://storage.googleapis.com/a1aa/image/Dgex1GmpdM-QKny8LVFNn2v3md5zR2S4mZQQSQVMl5U.jpg" alt="Thumbnail 2">
                <img src="https://storage.googleapis.com/a1aa/image/WqwYlZfAMpaqnNiqsGhGTc_k_CHbpva50hW7MSlWGHo.jpg" alt="Thumbnail 3">
                <img src="https://storage.googleapis.com/a1aa/image/e4Sw065MnW0IUd-lJG2aVQ4dgebEzeQv_hBPcv9jhF0.jpg" alt="Thumbnail 4">
            </div>
            <h1 class="header">Cho thuê phòng tầng 1 DT 30m2 tại ngõ 63 đường Đại Mỗ Hà Nội</h1>
            <p class="sub-header">Đại Mỗ, ngõ 63, Đường Đại Mỗ, Phường Đại Mỗ, Nam Từ Liêm, Hà Nội</p>
            <hr>
            <div class="info">
                <div>
                    <p class="label">Mức giá</p>
                    <p class="value">3,9 triệu/tháng</p>
                </div>
                <div>
                    <p class="label">Diện tích</p>
                    <p class="value">30 m²</p>
                </div>
                <div>
                    <p class="label">Phòng ngủ</p>
                    <p class="value">1 PN</p>
                </div>
                <div class="icons">
                    <i class="fas fa-share-alt"></i>
                    <i class="fas fa-exclamation-triangle"></i>
                    <i class="fas fa-heart"></i>
                </div>
            </div>
            <hr>
            <h2 class="section-title">Thông tin mô tả</h2>
            <p class="description">Cho thuê phòng tại ngõ 63 đường Đại Mỗ. Tầng 1 DT 30m² phòng khép kín, giường tủ bàn ghế điều hòa nóng lạnh đầy đủ.</p>
            <h2 class="section-title">Đặc điểm bất động sản</h2>
            <hr>
            <div class="features">
                <div class="feature">
                    <i class="fas fa-money-bill-wave"></i>
                    <p>Mức giá: 3,9 triệu/tháng</p>
                </div>
                <div class="feature">
                    <i class="fas fa-road"></i>
                    <p>Đường vào: 3 m</p>
                </div>
                <div class="feature">
                    <i class="fas fa-ruler-combined"></i>
                    <p>Diện tích: 30 m²</p>
                </div>
                <div class="feature">
                    <i class="fas fa-clock"></i>
                    <p>Thời gian dự kiến vào ở: Ở ngay</p>
                </div>
                <div class="feature">
                    <i class="fas fa-bed"></i>
                    <p>Số phòng ngủ: 1 phòng</p>
                </div>
                <div class="feature">
                    <i class="fas fa-bolt"></i>
                    <p>Mức giá điện: Do chủ nhà quy định</p>
                </div>
                <div class="feature">
                    <i class="fas fa-bath"></i>
                    <p>Số phòng tắm, vệ sinh: 1 phòng</p>
                </div>
                <div class="feature">
                    <i class="fas fa-tint"></i>
                    <p>Mức giá nước: Do chủ nhà quy định</p>
                </div>
                <div class="feature">
                    <i class="fas fa-layer-group"></i>
                    <p>Số tầng: 1 tầng</p>
                </div>
                <div class="feature">
                    <i class="fas fa-wifi"></i>
                    <p>Mức giá internet: Do chủ nhà quy định</p>
                </div>
                <div class="feature">
                    <i class="fas fa-compass"></i>
                    <p>Hướng nhà: Tây - Bắc</p>
                </div>
                <div class="feature">
                    <i class="fas fa-video"></i>
                    <p>Tiện ích: Camera</p>
                </div>
                <div class="feature">
                    <i class="fas fa-compass"></i>
                    <p>Hướng ban công: Tây - Bắc</p>
                </div>
                <div class="feature">
                    <i class="fas fa-couch"></i>
                    <p>Nội thất: Cơ bản</p>
                </div>
            </div>
            {{-- map --}}
            <h2 class="section-title">Vị trí</h2>
            <div id="map" style="height: 400px; width: 100%;"></div>

            <!-- Comments Section -->
            <h2 class="section-title">Bình luận</h2>
            <div class="comments">
                <form action="" method="POST">
                    <textarea name="comment" placeholder="Viết bình luận của bạn..." required></textarea>
                    <button type="submit">Gửi bình luận</button>
                </form>
                <div class="comment-list">
                    <div class="comment">
                        <p><strong>Nguyễn Văn A</strong>: Phòng rất đẹp và thoải mái!</p>
                    </div>
                    <div class="comment">
                        <p><strong>Trần Thị B</strong>: Địa điểm dễ tìm và giá cả hợp lý.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="right-section sticky">
            <div class="profile-landord">
                <div><img class="avatar" src="assets/image/customer02.jpg" alt=""></div>
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
@endsection
@section('scripts')
<!-- Add the Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap" async defer></script>

<script>
    // Initialize the map
    function initMap() {
        var location = { lat: 21.034, lng: 105.797 }; // Coordinates for Đại Mỗ, Hà Nội
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 14,
            center: location
        });
        var marker = new google.maps.Marker({
            position: location,
            map: map,
            title: "Cho thuê phòng"
        });
    }
</script>
@endsection