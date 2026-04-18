@extends('layouts.admin')
@section('title', 'Profile')
@section('header', 'My Profile')

@section('content')
<div class="space-y-6">
    
    <!-- Profile Information -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 max-w-2xl">
        <h3 class="font-bold text-lg text-slate-800 mb-1">Profile Information</h3>
        <p class="text-sm text-slate-500 mb-6">Update your account's profile information and email address.</p>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded-lg p-2.5" required autofocus>
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded-lg p-2.5" required>
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-900 transition">Save Changes</button>
        </form>
    </div>

    <!-- Update Password -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 max-w-2xl">
        <h3 class="font-bold text-lg text-slate-800 mb-1">Update Password</h3>
        <p class="text-sm text-slate-500 mb-6">Ensure your account is using a long, random password to stay secure.</p>

        <form method="post" action="{{ route('profile.password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">
                    Current Password
                    <!-- Forgot Password Link Helper -->
                     <a href="{{ route('password.request') }}" onclick="event.preventDefault(); document.getElementById('logout-form-reset').submit();" class="text-xs text-blue-500 font-normal ml-2 hover:underline cursor-pointer" title="Log out and go to reset page">(Forgot? Log out & Reset)</a>
                </label>
                <input type="password" name="current_password" class="w-full border rounded-lg p-2.5" required>
                @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">New Password</label>
                <input type="password" name="password" class="w-full border rounded-lg p-2.5" required>
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full border rounded-lg p-2.5" required>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition">Update Password</button>
        </form>
        
        <!-- Hidden Logout Form for Reset Link -->
        <form id="logout-form-reset" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

</div>
@endsection