<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="{{ asset('assets/css/account/resetPassword.css') }}">

</head>

<body>
    <div class="container">
        <h2>Forgot Password</h2>
        @if (session('status'))
        <p class="success-message">{{ session('status') }}</p>
        @endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <label for="email">Enter your Email:</label>
            <input type="email" name="email" id="email" required>
            <button type="submit">Send password reset link</button>
        </form>
        <a href="{{ route('login') }}" class="back-link">Back to login</a>
    </div>
</body>

</html>