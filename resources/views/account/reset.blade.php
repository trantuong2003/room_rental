<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <link rel="stylesheet" href="{{ asset('assets/css/account/resetPassword.css') }}">
</head>

<body>
    <div class="container">
        <h2>Đặt lại mật khẩu</h2>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Mật khẩu mới:</label>
            <input type="password" name="password" id="password" required>

            <label for="password_confirmation">Xác nhận mật khẩu:</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>

            <button type="submit">Đặt lại mật khẩu</button>
        </form>
        <a href="{{ route('login') }}" class="back-link">Quay lại đăng nhập</a>
    </div>
</body>

</html>