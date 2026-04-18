@extends('admin.layout.link')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <h4>Add New Employee</h4>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.employee') }}" class="btn btn-success"> Back</a>
        </div>
    </div>
    <hr>
    <form action="{{ route('admin.employee.create') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">


            <div class="col-md-4 mb-3">
                <label class="form-label">Image: <code>*</code></label>
                <input type="file" class="form-control" id="image" name="image" value="{{ old('image') }}">
            </div>
            
            <div class="col-md-4 mb-3">
                <label class="form-label">Name: <code>*</code></label>
                <input type="text" class="form-control" id="nameInput" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Mobile: <code>*</code></label>
                <input type="text" class="form-control" name="mobile" value="{{ old('mobile') }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Date of Birth: <code>*</code></label>
                <input type="date" class="form-control" value="{{ old('date_of_birth') }}" name="date_of_birth" required onclick="this.showPicker()"
                    onfocus="this.showPicker()">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Date of Joining: <code>*</code></label>
                <input type="date" class="form-control" value="{{ old('date_of_joining') }}" name="date_of_joining" required onclick="this.showPicker()"
                    onfocus="this.showPicker()">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Department: <code>*</code></label>           
                    <select class="form-select" name="department_id" required>
                        <option value="" disabled  selected>Select Department</option>
                        @foreach($depts as $dep)
                        <option value="{{ $dep->id }}" {{ old('department_id') == $dep->id ? 'selected' : '' }}>
                            {{ $dep->name }}
                        </option>
                        @endforeach
                    </select>

            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Position: <code>*</code></label>
                <select class="form-select" name="position">
                    <option value="frontend" {{ old('position') == 'frontend' ? 'selected' : '' }}>Frontend Developer</option>
                    <option value="backend" {{ old('position') == 'backend' ? 'selected' : '' }}>Backend Developer</option>
                </select>
            </div>
            

            <div class="col-md-4 mb-3">
                <label class="form-label">Salary:</label>
                <input type="number" class="form-control" name="salary" value="{{ old('salary') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Gender: <code>*</code></label>
                <select class="form-select" name="gender">
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>

            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Status: <code>*</code></label>
                <select class="form-select" name="status">
                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>In Active</option>
                </select>

            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Employee Type:<code>*</code></label>
                <select class="form-select" name="employee_type">
                    <option value="permanent" {{ old('employee_type', 'permanent') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                    <option value="contract" {{ old('employee_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                </select>

            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Notice Period:</label>
                <input type="number" class="form-control" name="notice_period" value="{{ old('notice_period') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Father Name:</label>
                <input type="text" class="form-control" name="father_name" value="{{ old('father_name') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Mother Name:</label>
                <input type="text" class="form-control" name="mother_name" value="{{ old('mother_name') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Emergency Contact Number:</label>
                <input type="text" class="form-control" name="emergency_contact_number" value="{{ old('emergency_contact_number') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Resume (PDF/DOC/DOCX):</label>
                <input value="{{ old('resume') }}" type="file" class="form-control" name="resume">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Bank Name:</label>
                <input value="{{ old('bank_name') }}" type="text" class="form-control" name="bank_name">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Bank Account No:</label>
                <input value="{{ old('bank_account_number') }}" type="text" class="form-control" name="bank_account_number">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">IFSC Code:</label>
                <input value="{{ old('ifsc_code') }}" type="text" class="form-control" name="ifsc_code">
            </div>


            
            <div class="col-md-4 mb-3">
                <label class="form-label">Incremant date</label>
                <input value="{{ old('increment_date') }}" type="date" class="form-control" name="increment_date">
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Address: <code>*</code></label>
                <textarea class="form-control" name="address" rows="2"></textarea>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Zipcode:</label>
                <input value="{{ old('zipcode') }}" type="text" name="zipcode" class="form-control" id="">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Email: <code>*</code></label>
                <input value="{{ old('email') }}" type="email" class="form-control" name="email" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Password: <code>*</code></label>
                <input value="{{ old('password') }}" type="password" class="form-control" name="password" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Employee ID: <code>*</code></label>
                <input required type="text" class="form-control" value="{{$empid}}" name="emp_id" readonly>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" id="setButton">Submit</button>
    </form>
</div>


<script>
    $(document).ready(function() {
        $("#nameInput").on("input", function() {
            var inputValue = $(this).val();
            $("#setButton").text('Add Employee ' + inputValue);
        });
    });
</script>
@endsection