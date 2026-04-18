
@extends('admin.layout.master')
@section('content')

<section id="add_data">
    <h2>Change Password (with OTP)</h2>

    <div id="form_container">
        {{-- Step 1: Send OTP --}}
        <form id="sendOtpForm" method="POST" action="{{ route('admin.send-otp') }}">
            @csrf
            <div class="form-input">
                <label>Email: <span class="error">*</span></label>
              <input type="email" class="inp" name="email" placeholder="Enter your email" required />
            </div>

            <div class="form-submit-container">
                <button type="submit" class="btn success">Send OTP</button>
            </div>
        </form>

        {{-- Step 2: OTP Verify --}}
        <form id="verifyOtpForm" method="POST" action="{{ route('admin.verify-otp') }}" style="display:none;">
            @csrf
            <div class="form-input">
                <label>Enter OTP: <span class="error">*</span></label>
                <input type="text" class="inp" name="otp" placeholder="Enter OTP" required />
            </div>

            <div class="form-submit-container">
                <button type="submit" class="btn success">Verify OTP</button>
            </div>
        </form>

        {{-- Step 3: Reset Password --}}
        <form id="resetPasswordForm" method="POST" action="{{ route('admin.change-password') }}" style="display:none;">
            @csrf

            <div class="form-input">
                <label for="new_password">New Password: <span class="error">*</span></label>
                <input type="password" class="inp" name="new_password" placeholder="Enter new password" required />
            </div>

            <div class="form-input">
                <label for="new_password_confirmation">Confirm New Password: <span class="error">*</span></label>
                <input type="password" class="inp" name="new_password_confirmation" placeholder="Confirm new password" required />
            </div>

            <div class="form-submit-container">
                <button type="submit" class="btn success">Update Password</button>
                <button type="button" class="btn danger">
                    <a href="{{ url('/user') }}" style="color: white; text-decoration: none;">Exit</a>
                </button>
            </div>
        </form>
    </div>
</section>

<script>
    const sendOtpForm = document.getElementById('sendOtpForm');
    const verifyOtpForm = document.getElementById('verifyOtpForm');
    const resetPasswordForm = document.getElementById('resetPasswordForm');

    sendOtpForm.addEventListener('submit', function(e) {
        e.preventDefault();

        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('OTP sent to your email.');
                sendOtpForm.style.display = 'none';
                verifyOtpForm.style.display = 'block';
            } else {
                alert(data.message || 'Failed to send OTP.');
            }
        });
    });

    verifyOtpForm.addEventListener('submit', function(e) {
        e.preventDefault();

        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('OTP verified successfully!');
                verifyOtpForm.style.display = 'none';
                resetPasswordForm.style.display = 'block';
            } else {
                alert(data.message || 'Invalid OTP');
            }
        });
    });
</script>

@endsection
