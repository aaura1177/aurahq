@extends('admin.layout.link')

@section('content')
<div class="container mt-4">
    <h4> Edit Employee</h4>
    <a href="{{ route('admin.employee') }}" class="btn btn-primary"> Back</a>
    <hr>
    <form action="{{route('admin.employee.edit.post')}}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
          <input type="hidden" value="{{$employee->id}}" name="id">

          <div class="col-md-4 mb-3">
        <label class="form-label">Image:</label>
        <input type="file" class="form-control" id="image" name="image">
    </div>

    <!-- Display Existing Image if Available -->
    @if($employee->image)
        <div class="col-md-4 mb-3">
            <label class="form-label">Current Image:</label>
            <img src="{{ asset('storage/' . $employee->image) }}" alt="Employee Image" class="img-fluid rounded-circle" width="100">
        </div>
    @endif

            <div class="col-md-4 mb-3">
                <label class="form-label">Name:</label>
                <input type="text" class="form-control" value="{{$employee->name}}" name="name" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Mobile:</label>
                <input type="text" class="form-control" value="{{$employee->mobile}}" name="mobile" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Date of Birth:</label>
                <input type="date" class="form-control" value="{{$employee->date_of_birth}}" name="date_of_birth" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Date of Joining:</label>
                <input type="date" class="form-control" value="{{$employee->date_of_joining}}" name="date_of_joining" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Department:</label>
                <select class="form-select" name="department_id" required>
                    <option value="">Select Department</option>
                    @foreach($depts as $dep)
                        <option value="{{ $dep->id }}" 
                            {{ old('department_id', $employee->department_id) == $dep->id ? 'selected' : '' }}>
                            {{ $dep->name }}
                        </option>
                    @endforeach
                </select>
                
            </div>
            

            <div class="col-md-4 mb-3">
                <label class="form-label">Position:</label>
                <select class="form-select" name="position">
                    <option value="frontend" {{ $employee->position == 'frontend' ? 'selected' : '' }}>Frontend Developer</option>
                    <option value="backend" {{ $employee->position == 'backend' ? 'selected' : '' }}>Backend Developer</option>
                </select>
            </div>
            

            <div class="col-md-4 mb-3">
                <label class="form-label">Salary:</label>
                <input type="number" class="form-control" value="{{$employee->salary}}" name="salary">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Gender:</label>
                <select class="form-select" name="gender">
                    <option value="">Select Gender</option>
                    <option value="male" @selected($employee->gender === 'male')>Male</option>
                    <option value="female" @selected($employee->gender === 'female')>Female</option>
                    <option value="other" @selected($employee->gender === 'other')>Other</option>
                </select>
            </div>
            
            <div class="col-md-4 mb-3">
                <label class="form-label">Status:</label>
                <select class="form-select" name="status">
                    <option value="">Select Status</option>
                    <option value="1" @selected($employee->status == 1)>Active</option>
                    <option value="0" @selected($employee->status == 0)>Inactive</option>
                </select>
            </div>
            
            <div class="col-md-4 mb-3">
                <label class="form-label">Employee Type:</label>
                <select class="form-select" name="employee_type">
                    <option value="">Select Employee Type</option>
                    <option value="permanent" @selected($employee->employee_type == 'permanent')>Permanent</option>
                    <option value="contract" @selected($employee->employee_type == 'contract')>Contract</option>
                </select>
            </div>
            
            <div class="col-md-4 mb-3">
                <label class="form-label">Notice Period:</label>
                <input type="number" class="form-control" value="{{$employee->notice_period}}" name="notice_period">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Father Name:</label>
                <input type="text" class="form-control" value="{{$employee->father_name}}" name="father_name">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Mother Name:</label>
                <input type="text" class="form-control" value="{{$employee->mother_name}}" name="mother_name">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Emergency Contact Number:</label>
                <input type="text" class="form-control" value="{{$employee->emergency_contact_number}}" name="emergency_contact_number">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Resume (PDF/DOC/DOCX):</label>
                <input type="file" class="form-control" name="resume">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Bank Name:</label>
                <input type="text" class="form-control"  value="{{$employee->bank_name}}" name="bank_name">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Bank Account No:</label>
                <input type="text" class="form-control" value="{{$employee->bank_account_number}}" name="bank_account_number">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">IFSC Code:</label>
                <input type="text" class="form-control" value="{{$employee->ifsc_code}}" name="ifsc_code">
            </div>

            
            <div class="col-md-4 mb-3">
                <label class="form-label">Increment_date:</label>
                <input type="date" class="form-control" value="{{$employee->increment_date}}" name="increment_date">
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Address:</label>
                <textarea class="form-control" value="" name="address" rows="2">{{$employee->address}}</textarea>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Zipcode:</label>
                <input type="text" name="zipcode" value="{{$employee->zipcode}}" class="form-control" id="">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Email:</label>
                <input type="email" class="form-control" value="{{$employee->email}}" name="email" required>
            </div>

       

            <div class="col-md-4 mb-3">
                <label class="form-label">Employee ID:</label>
                <input type="text" class="form-control" value="{{$employee->emp_id}}" name="emp_id" readonly>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection