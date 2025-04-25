
@extends('layouts/customer')

@section('content')
<div class="container">
    <div class="card layout">
        <div class="left-section">
            <h1 class="header">Đăng bài tìm người ở cùng</h1>
            <p class="sub-header">Chia sẻ nội dung của bạn</p>
            <form action="{{ isset($post) ? route('customer.roommates.update', $post->id) : route('customer.roommates.store') }}" method="POST">
                @csrf
                @if(isset($post))
                    @method('PUT')
                @endif
                <!-- Title and Description -->
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ $post->title ?? '' }}" required>
                </div>
                <div class="form-group">
                    <label for="content">Description</label>
                    <textarea name="content" id="content" class="form-control" required>{{ $post->content ?? '' }}</textarea>
                </div>
                
                {{-- <!-- Image Upload -->
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
                </div> --}}

                <button type="submit"  class="submit-btn">{{ isset($post) ? 'Cập nhật' : 'Đăng bài' }}</button>
                <a href="{{ route('customer.roommates.history') }}" class="btn btn-secondary">Hủy</a>
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