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
        <div class="listing">
            <div class="images">
                <img src="https://placehold.co/300x200" alt="Interior view of a studio apartment with bed, table, and bathroom">
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
                <p>Diện tích từ 23m², 28m², 35m². Giá thuê từ 4,9 triệu đến 8 triệu. Tiện ích: Tạp hóa tầng 1, chợ, siêu thị 100m. Gần học viện Báo Chí, đại học Quốc Gia 200m. Gym, bể bơi, ngân hàng, công viên thể dục 500m. An ninh cu...</p>
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
        </div>
</div>


@endsection
