<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="{{ asset('assets/css/account/resetPassword.css') }}">

</head>

<body>
    <div class="container">
        <h2>Quên Mật Khẩu</h2>
        @if (session('status'))
        <p class="success-message">{{ session('status') }}</p>
        @endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <label for="email">Nhập Email của bạn:</label>
            <input type="email" name="email" id="email" required>
            <button type="submit">Gửi liên kết đặt lại mật khẩu</button>
        </form>
        <a href="{{ route('login') }}" class="back-link">Quay lại đăng nhập</a>
    </div>
</body>

</html>