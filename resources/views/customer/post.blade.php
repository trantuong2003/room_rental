@extends('layouts/customer')

@section('content')
<div class="container">
    <div class="card layout">
        <div class="left-section">
            <h1 class="header">Đăng bài cho người thuê</h1>
            <p class="sub-header">Chia sẻ thông tin phòng trọ mà bạn muốn cho thuê</p>
            {{-- <form action="{{ route('tenant.store') }}" method="POST" enctype="multipart/form-data"> --}}
                <form action="">
                @csrf
                <!-- Title and Description -->
                <div class="form-group">
                    <label for="title">Tiêu đề</label>
                    <input type="text" name="title" id="title" placeholder="Nhập tiêu đề" required>
                </div>
                <div class="form-group">
                    <label for="description">Mô tả</label>
                    <textarea name="description" id="description" placeholder="Nhập mô tả chi tiết" required></textarea>
                </div>
                
                <!-- Image Upload -->
                <div class="form-group">
                    <label for="images">Ảnh</label>
                    <input type="file" name="images[]" id="images" accept="image/*" multiple>
                </div>

                <!-- Rent Info -->
                <div class="form-group">
                    <label for="price">Mức giá</label>
                    <input type="text" name="price" id="price" placeholder="Nhập mức giá" required>
                </div>
                <div class="form-group">
                    <label for="area">Diện tích</label>
                    <input type="text" name="area" id="area" placeholder="Nhập diện tích" required>
                </div>

                <!-- Contact Information -->
                <div class="form-group">
                    <label for="contact">Liên hệ (Số điện thoại)</label>
                    <input type="text" name="contact" id="contact" placeholder="Nhập số điện thoại" required>
                </div>

                <button type="submit" class="submit-btn">Đăng bài</button>
            </form>
        </div>

        <div class="right-section">
            <div class="price-info">
                <h3>Phí đăng bài</h3>
                <p>Bài đăng không mất phí.</p>
                <button class="contact-btn">Liên hệ để nhắn tin</button>
            </div>
        </div>
    </div>
</div>
@endsection
