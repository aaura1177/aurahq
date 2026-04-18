    <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin-Register</title>

<link rel="stylesheet" href="{{ asset('css/signup.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>
<section id="main_container">

<section id="form_container">
    <figure>
        <img src="../images/download.jpg" alt="">
    </figure>
  
    <form method="POST" action="{{ route('register.submit') }}">
    @csrf
    <h3>SIGN UP</h3>
    <fieldset>
        <label>Name:</label>
        <i class="fa-solid fa-user"></i>
        <input type="text" name="name" class="inp" placeholder="Full Name" value="{{ old('name') }}">
        @error('name') <span style="color:red">{{ $message }}</span> @enderror
    </fieldset>

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

    <fieldset>
        <label>Confirm Password:</label>
        <i class="fa-solid fa-key"></i>
        <input type="password" name="password_confirmation" class="inp" placeholder="Confirm Password">
    </fieldset>

    <button type="submit" id="button" class="btn">Sign Up</button>

    <section class="forgot-section">
        <a href="">Already have an account? Login</a>
    </section>
</form>

</section>

</section>
<script src="{{ asset('js/login.js') }}"></script>
</body>
</html>