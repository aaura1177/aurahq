@extends('user.layouts.master')
@section('content')

<section id="add_data">
    <h2>Change Password</h2>

    <div id="form_container">
        <form action="{{ route('update.password.user') }}" method="post">
            @csrf

            <div class="form-input">
                <label for="old_password">Old Password: <span class="error">*</span></label>
                <input type="password" class="inp" name="old_password" placeholder="Enter old password" required />
            </div>

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
                    <a href="{{route('updated.user.profile')}}" style="color: white; text-decoration: none;">Exit</a>
                </button>
            </div>
        </form>
    </div>
</section>

@endsection
