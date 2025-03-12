<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Boarding House</title>
  <link rel="stylesheet" href="{{ asset('assets/css/account/login.css') }}">
</head>

<body>
  <div class="wrapper">
    <form action="" method="POST">
      <h2>Login</h2>

      @if(session('msg'))
      <p style="color: red; text-align: center; margin-bottom: 10px;">{{ session('msg') }}</p>
      @endif

      <div class="input-field">
        <input type="email" name="email" value="{{ old('email') }}" required>
        <label>Enter your email</label>
      </div>

      <div class="input-field">
        <input type="password" name="password" required>
        <label>Enter your password</label>
      </div>

      <div class="forget">
        <label for="remember">
          <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
          <p>Remember me</p>
        </label>
        <a href="/password/reset">Forgot password?</a>
      </div>

      <button type="submit">LogIn</button>

      <div class="register">
        <p>Don't have an account? <a href="/register">Register</a></p>
      </div>
      @csrf
    </form>
  </div>
</body>

</html>