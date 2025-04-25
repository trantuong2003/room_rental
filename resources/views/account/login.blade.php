<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Boarding House</title>
  <link rel="stylesheet" href="{{ asset('assets/css/account/login.css') }}">
  <!-- Thêm Font Awesome cho biểu tượng con mắt -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

      <div class="input-field password-container">
        <input type="password" name="password" id="password" required>
        <label>Enter your password</label>
        <span class="eye-icon" onclick="togglePassword()">
          <i class="fas fa-eye"></i>
        </span>
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

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.querySelector('.eye-icon i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
      }
    }
  </script>
</body>

</html>

{{-- 
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

</html> --}}
