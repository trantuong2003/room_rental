<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Registration Form in HTML CSS</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('assets/css/account/register.css') }}">
  <!-- Thêm Font Awesome cho biểu tượng con mắt -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="{{ asset('assets/js/register.js') }}" defer></script>
</head>

<body>
  <section class="container">
    <header>Registration Form</header>
    @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('register') }}" class="form" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="input-box">
        <label>Full Name</label>
        <input type="text" name="name" placeholder="Enter full name" required />
      </div>

      <div class="input-box">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter email address" required />
      </div>

      <div class="input-box password-container">
        <label>Password</label>
        <input type="password" name="password" id="password" placeholder="Enter password" required />
        <span class="eye-icon" onclick="togglePassword('password')">
          <i class="fas fa-eye"></i>
        </span>
      </div>

      <div class="input-box password-container">
        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm password" required />
        <span class="eye-icon" onclick="togglePassword('password_confirmation')">
          <i class="fas fa-eye"></i>
        </span>
      </div>

      <div class="column">
        <div class="input-box">
          <label>Phone Number</label>
          <input type="number" name="phone" placeholder="Enter phone number" required />
        </div>
      </div>

      <div class="gender-box">
        <h3>Do you want to be a?</h3>
        <div class="gender-option">
          <div class="gender">
            <input type="radio" id="check-renter" name="role" value="renter" checked />
            <label for="check-renter">Renter</label>
          </div>
          <div class="gender">
            <input type="radio" id="check-landlord" name="role" value="landlord" />
            <label for="check-landlord">Landlord</label>
          </div>
        </div>
      </div>

      <div id="renter-fields">
        <div class="input-box address">
          <label>Address</label>
          <div class="column">
            <div class="select-box">
              <select name="city" id="city">
                <option hidden>City</option>
              </select>
            </div>
            <div class="select-box">
              <select name="region" id="region">
                <option hidden>Region</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div id="landlord-fields" class="hidden">
        <div class="input-box">
          <label>Government ID</label>
          <input type="text" name="government_id" placeholder="Enter government ID number" />
        </div>
        <div class="input-box">
          <label for="document-upload">Upload Proof of Ownership</label>
          <input type="file" name="proof" id="document-upload" accept=".jpg, .jpeg, .png, .pdf" />
        </div>
      </div>

      <div class="input-box terms-box">
        <div class="terms">
          <input type="checkbox" id="terms" name="terms" value="1" />
          <label for="terms">I agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a></label>
        </div>
      </div>

      <button type="submit" id="submitButton">Submit</button>
      <p class="login-text">
        Already have an account? <a href="/login">Login here</a>
      </p>
    </form>
  </section>

  <script>
    // Function to toggle password visibility
    function togglePassword(fieldId) {
      const passwordInput = document.getElementById(fieldId);
      const eyeIcon = document.querySelector(`#${fieldId} + .eye-icon i`);
      
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

{{-- <!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Registration Form in HTML CSS</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('assets/css/account/register.css') }}">
  <script src="{{ asset('assets/js/register.js') }}" defer></script>
</head>

<body>
  <section class="container">
    <header>Registration Form</header>
    @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('register') }}" class="form" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="input-box">
        <label>Full Name</label>
        <input type="text" name="name" placeholder="Enter full name" required />
      </div>

      <div class="input-box">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter email address" required />
      </div>

      <div class="input-box">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required />
      </div>

      <div class="input-box">
        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" placeholder="Confirm password" required />
      </div>

      <div class="column">
        <div class="input-box">
          <label>Phone Number</label>
          <input type="number" name="phone" placeholder="Enter phone number" required />
        </div>
      </div>

      <div class="gender-box">
        <h3>Do you want to be a?</h3>
        <div class="gender-option">
          <div class="gender">
            <input type="radio" id="check-renter" name="role" value="renter" checked />
            <label for="check-renter">Renter</label>
          </div>
          <div class="gender">
            <input type="radio" id="check-landlord" name="role" value="landlord" />
            <label for="check-landlord">Landlord</label>
          </div>
        </div>
      </div>

      <div id="renter-fields">
        <div class="input-box address">
          <label>Address</label>
          <div class="column">
            <div class="select-box">
              <select name="city" id="city">
                <option hidden>City</option>
              </select>
            </div>
            <div class="select-box">
              <select name="region" id="region">
                <option hidden>Region</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div id="landlord-fields" class="hidden">
        <div class="input-box">
          <label>Government ID</label>
          <input type="text" name="government_id" placeholder="Enter government ID number" />
        </div>
        <div class="input-box">
          <label for="document-upload">Upload Proof of Ownership</label>
          <input type="file" name="proof" id="document-upload" accept=".jpg, .jpeg, .png, .pdf" />
        </div>
      </div>

      <div class="input-box terms-box">
        <div class="terms">
          <input type="checkbox" id="terms" name="terms" value="1" />
          <label for="terms">I agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a></label>
        </div>
      </div>

      <button type="submit" id="submitButton">Submit</button>
      <p class="login-text">
        Already have an account? <a href="/login">Login here</a>
      </p>
    </form>
  </section>
</body>

</html> --}}