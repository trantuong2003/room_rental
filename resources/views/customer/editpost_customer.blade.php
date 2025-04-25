@extends('layouts.customer')

@section('content')
<div class="container">
    <div class="edit-post-customer">
        <div class="card layout">
            <div class="left-section">
                <h1 class="header">Chỉnh sửa bài đăng tìm người ở cùng</h1>
                <p class="sub-header">Cập nhật nội dung của bạn</p>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('customer.roommates.update', $post->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Title and Description -->
                    <div class="form-group">
                        <label for="title">Tiêu đề</label>
                        <input type="text" name="title" id="title" class="form-control"
                            value="{{ old('title', $post->title) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Nội dung</label>
                        <textarea name="content" id="content" class="form-control" rows="8"
                            required>{{ old('content', $post->content) }}</textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="submit-btn">Cập nhật bài đăng</button>
                        <a href="{{ route('customer.roommates.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                    </div>
                </form>
            </div>

            <div class="right-section">
                <div class="price-info">
                    <h3>Lưu ý khi chỉnh sửa</h3>
                    <ul>
                        <li>Bài đăng sẽ được cập nhật ngay lập tức</li>
                        <li>Đảm bảo thông tin chính xác và đầy đủ</li>
                        <li>Tránh vi phạm các quy định của cộng đồng</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection