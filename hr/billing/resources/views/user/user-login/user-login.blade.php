<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User-login</title>
  <link rel="stylesheet" href="{{ asset('css/adminlogin.css') }}">

</head>
<body>
<form method="POST" action="{{ route('user.login') }} ">
    @csrf
    <h3> USER LOGIN</h3>

    <fieldset>
        <label>Email ID:</label>
        <i class="fa-solid fa-envelope"></i>
        <input type="email" name="email" class="inp" placeholder="Email ID" value="{{ old('email') }}">
        @error('email') <span style="color:red">{{ $message }}</span> @enderror
    </fieldset>

    <fieldset>
        <label>Password:</label>
        <i class="fa-solid fa-key"></i>
        <input type="password" name="password" class="inp" id="input_password" placeholder="Password">
        <i id="fa_eye" class="fa fa-eye-slash" onclick="handleEyePassword(this)"></i>
        @error('password') <span style="color:red">{{ $message }}</span> @enderror
    </fieldset>

    <button type="submit" id="button" class="btn">Login</button>

    <section class="forgot-section">
        <a href="{{ route('user.forgot.form') }}">Forgot Password?</a>
        <!-- <a href="">Don't have an account? Sign Up</a> -->
    </section>
</form>
<script src="{{ asset('css/login.js') }}"></script>
</body>
</html>