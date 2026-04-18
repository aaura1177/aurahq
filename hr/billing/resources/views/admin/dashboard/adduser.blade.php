@extends('admin.layout.master')
@section('content')


<section id="add_data">
    <h2>
        Add User Data
    </h2>

    <div id="form_container">

        <form action="{{ route('user.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-input">
                <label for="">Name : <span class="error">*</span></label>
                <input type="text" class="inp" name="name" placeholder="Name" />
            </div>
            <div class="form-input">
                <label for="">Phone no: <span class="error">*</span></label>
                <input type="text" class="inp" name="phone_no" placeholder="phone_no"  />
            </div>
            <div class="form-input">
                <label for="">Profile  : <span class="error">*</span></label>
                <input type="file" class="inp" name="profile_picture" placeholder="Name"  />
            </div>
            <div class="form-input">
                <label for="">Address <span class="error">*</span></label>
                <input type="text" class="inp" name="address" placeholder="Username"  />
            </div>
            <div class="form-input">
                <label for="">Email : <span class="error">*</span></label>
                <input type="email" class="inp" name="email" placeholder="Email" required />
            </div>

              <!-- <div class="form-input">
                <label for="">Salary: <span class="error">*</span></label>
                <input type="salary" class="inp" name="monthly_salary" placeholder="salary"  />
            </div> -->
            <div class="form-input">
                <label for="">Password  : <span class="error">*</span></label>
                <input type="password" class="inp" name="password" placeholder="Password" maxlength="6" required />
            </div>

            <div class="form-submit-container">
                <button class="btn success">Save</button>
                <button class="btn danger"><a href="{{ url('/admin/dashboard')}}"style="color: white; text-decoration: none;">Exit</a></button>
            </div>
        </form>
    </div>
</section>






















@endsection