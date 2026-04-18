@extends('admin.layout.master')
@section('content')


<section id="add_data">
    <h2>
        Profile 
    </h2>

    <div id="form_container">

    <form action="{{ isset($user) ? route('user.update', $user->id) : route('user.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="form-input">
        <label>Name: *</label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required>
    </div>
    <div class="form-input">
        <label>Phone No: *</label>
        <input type="text" name="phone_no" value="{{ old('phone_no', $user->phone_no ?? '') }}" required>
    </div>
    <div class="form-input">
        <label>Profile Picture: *</label>
        <input type="file" name="profile_picture">
        @if(isset($user))
            <img src="{{ asset('storage/' . $user->profile_picture) }}" width="100" alt="Current Profile">
        @endif
    </div>
    <div class="form-input">
        <label>Address: *</label>
        <input type="text" name="address" value="{{ old('address', $user->address ?? '') }}" required>
    </div>
    <div class="form-input">
        <label>Email: *</label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
    </div>
    <div class="form-submit-container">
        <button class="btn success" type="submit">{{ isset($user) ? 'Update' : 'Save' }}</button>
        <a href="{{ url('/admin/dashboard') }}" class="btn danger" style="color: white; text-decoration: none;">Exit</a>
    </div>
</form>
    </div>
</section>






















@endsection