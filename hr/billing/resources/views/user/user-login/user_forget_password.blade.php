<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body { font-family: Arial; background: #f7f7f7; }
        
        .container {
            width: 400px;
            margin: 100px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 { text-align: center; margin-bottom: 20px; }
        input { width: 100%; padding: 10px; margin: 10px 0; }
        button { width: 100%; padding: 10px; background: blue; color: white; border: none; }
        .hidden { display: none; }
    </style>
</head>
<body>

<div class="container">
    <h2>Forgot Password</h2>

    <form id="emailForm">
        @csrf
        <input type="email" name="email" id="email" placeholder="Enter your email" required>
        <button type="submit">Send OTP</button>
    </form>

    <form id="otpForm" class="hidden">
        @csrf
        <input type="text" name="otp" id="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify OTP</button>
    </form>

    <form id="resetForm" method="POST" action="{{ route('user.forgot.reset.password') }}" class="hidden">
        @csrf
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="new_password_confirmation" placeholder="Confirm Password" required>
        <button type="submit">Reset Password</button>
    </form>
</div>

<script>
    const emailForm = document.getElementById('emailForm');
    const otpForm = document.getElementById('otpForm');
    const resetForm = document.getElementById('resetForm');

    emailForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(emailForm);

        fetch("{{ route('user.forgot.send.otp') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        }).then(res => res.json()).then(data => {
            if (data.success) {
                alert('OTP sent to your email.');
                emailForm.classList.add('hidden');
                otpForm.classList.remove('hidden');
            } else {
                alert(data.message);
            }
        });
    });

    otpForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(otpForm);

        fetch("{{ route('user.forgot.verify.otp') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        }).then(res => res.json()).then(data => {
            if (data.success) {
                alert('OTP verified. Set your new password.');
                otpForm.classList.add('hidden');
                resetForm.classList.remove('hidden');
            } else {
                alert(data.message);
            }
        });
    });
</script>

</body>
</html>
