<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="{{ asset('assets/css/account/resetPassword.css') }}">
</head>

<body>
    <div class="container">
        <h2>Reset Password</h2>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">New password:</label>
            <input type="password" name="password" id="password" required>

            <label for="password_confirmation">Confirm new password:</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>

            <button type="submit">Reset Password</button>
        </form>
        <a href="{{ route('login') }}" class="back-link">Back to login</a>
    </div>
</body>

</html>