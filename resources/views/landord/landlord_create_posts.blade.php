@extends('layouts.landord')

@section('content')
<div class="main">
    <div class="create_post">
        <div class="container">
            <h1>Create a new post</h1>
            <form action="{{ route('landlord.posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h2>Location</h2>
                <div class="mb-4">
                    <label>Address</label>
                    <input type="text" name="address" placeholder="Enter address" required>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Latitude</label>
                        <input type="text" name="latitude" placeholder="Nhập vĩ độ" required>
                    </div>
                    <div>
                        <label>Longitude</label>
                        <input type="text" name="longitude" placeholder="Nhập kinh độ" required>
                    </div>
                </div>
                <h2>Rental category</h2>
                <div class="grid grid-2">
                    <div>
                        <label>Price</label>
                        <input type="text" name="price" placeholder="Nhập giá thuê" required>
                    </div>
                    <div>
                        <label>Acreage</label>
                        <input type="text" name="acreage" placeholder="Nhập diện tích" required>
                    </div>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Số phòng ngủ</label>
                        <input type="number" name="bedrooms" placeholder="Nhập số phòng ngủ" required>
                    </div>
                    <div>
                        <label>Số phòng vệ sinh</label>
                        <input type="number" name="bathrooms" placeholder="Nhập số phòng vệ sinh" required>
                    </div>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Giá điện</label>
                        <input type="text" name="electricity_price" placeholder="Nhập giá điện">
                    </div>
                    <div>
                        <label>Giá nước</label>
                        <input type="text" name="water_price" placeholder="Nhập giá nước">
                    </div>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Giá internet</label>
                        <input type="text" name="internet_price" placeholder="Nhập giá internet">
                    </div>
                    <div>
                        <label>Giá dịch vụ</label>
                        <input type="text" name="service_price" placeholder="Nhập giá dịch vụ">
                    </div>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Nội thất</label>
                        <input type="text" name="furniture" placeholder="Nhập nội thất">
                    </div>
                    <div>
                        <label>Tiện ích</label>
                        <input type="text" name="utilities" placeholder="Nhập tiện ích">
                    </div>
                </div>
                <h2>Thông tin mô tả</h2>
                <div class="mb-4">
                    <label>Tiêu đề <span class="text-red-500">*</span></label>
                    <input type="text" name="title" placeholder="Tiêu đề" required>
                    <div class="text-right text-gray-500 text-sm">0/150 ký tự</div>
                </div>
                <div class="mb-4">
                    <label>Mô tả <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="6" placeholder="Mô tả" required></textarea>
                    <div class="text-right text-gray-500 text-sm">0/5000 ký tự</div>
                </div>
                <h2>Hình ảnh/video</h2>
                <div class="mb-4">
                    <label>Hình ảnh</label>
                    <div class="border border-dashed border-gray-300 p-4 rounded-lg text-center">
                        <input type="file" name="images[]" id="upload-image" multiple accept="image/*">
                        <p class="text-gray-500 text-sm mt-2">Tối đa 25 ảnh, dung lượng không quá 8MB</p>
                    </div>
                </div>
                <div class="text-right mt-6">
                    <button type="submit" class="btn-create">Đăng Bài</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
