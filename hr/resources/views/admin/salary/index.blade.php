@extends('admin.layout.link')

@section('content')
<div class="container mt-4">
    <div class="d-block justify-content-between align-items-center mb-2">
        <h3>Employee List</h3>
    <form action="{{ route('admin.salary') }}" method="GET">
    <div class="row">
        {{-- Employee Dropdown --}}
        <div class="form-group mb-3 col-md-4">
            <label for="employee_id" class="form-label">Employee</label>
            <select name="employee_id" id="employee_id" class="form-control">
                <option value="">-- All Employees --</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Month Dropdown --}}
        <div class="form-group mb-3 col-md-3">
            <label for="month" class="form-label">Month</label>
            <select name="month" id="month" class="form-control">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endfor
            </select>
        </div>

        {{-- Year Dropdown --}}
        <div class="form-group mb-3 col-md-3">
            <label for="year" class="form-label">Year</label>
            <select name="year" id="year" class="form-control">
                @for ($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>

        {{-- Submit --}}
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </div>
</form>

        <a href="{{ route('admin.add.salary') }}" class="btn btn-primary mt-2">+ Add Salary</a>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>SR</th>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Employee Email</th>
                <th>Month</th>
                <th>Offsite Work Hours</th>
                <th>Holiday Salary</th>
                <th>Weekoff Salary</th>
                <th>Salary</th>

                <th>Acti ons</th>
            </tr>
        </thead>
        <tbody>
            @php
            $i=1;
            @endphp
            @foreach ($employeesalary as $employeesalary)


            <tr>
                <td>{{$i++}}</td>



                <td>{{$employeesalary->employee->emp_id}}</td>
                <td>{{$employeesalary->employee->name}}</td>
                <td>{{$employeesalary->employee->email}}</td>
                <td>{{$employeesalary->salary_month}}</td>
                <td>{{$employeesalary->home_working_hours}}</td>
                <td>{{$employeesalary->holiday_salary}}</td>
                <td>{{$employeesalary->weekoffsalary}}</td>
                <td>{{$employeesalary->net_salary}}</td>



                <td>
                    <button class="btn btn-sm btn-warning editemployeesalaryBtn"
                        data-bs-toggle="modal"
                        data-bs-target="#editemployeesalaryModal"
                        data-id="{{ $employeesalary->id }}"
                        data-employee_id="{{ $employeesalary->employee->emp_id }}"
                        data-name="{{ $employeesalary->employee->name }}"
                        data-salary_month="{{ $employeesalary->salary_month }}"
                        data-working_hours="{{ $employeesalary->home_working_hours }}"
                        data-weekoffsalary="{{ $employeesalary->weekoffsalary }}"
                        data-holiday_salary="{{ $employeesalary->holiday_salary }}"
                        data-attendance_salay="{{ $employeesalary->attendance_salay }}"
                        data-attendance_salay="{{ $employeesalary->attendance_salay }}"
                        data-workform_salary="{{ $employeesalary->workform_salary }}"
                        data-leave="{{ $employeesalary->leave }}"
                        data-leave_bal="{{ $employeesalary->leave_bal }}"
                        data-net_salary="{{ $employeesalary->net_salary }}">
                        View
                    </button>





                    <form action="{{ route('admin.salary.destroy', $employeesalary->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure you want to delete this salary record and add monthly leave to the employee table?')">
                            Delete
                        </button>
                    </form>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


<div class="modal fade" id="editemployeesalaryModal" tabindex="-1" aria-labelledby="editemployeesalaryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="editemployeesalaryModalLabel">Edit Employee Salary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="mb-3">
                        <label class="form-label">Employee ID</label>
                        <input type="text" id="employee_id" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Employee Name</label>
                        <input type="text" id="edit_name" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Salary Month</label>
                        <input type="text" name="salary_month" id="edit_salary_month" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Home Working Hours</label>
                        <input type="text" step="0.01" name="home_working_hours" id="edit_home_working_hours" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Holiday Salary</label>
                        <input type="text" step="0.01" name="holiday_salary" id="edit_holiday_salary" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Leave</label>
                        <input type="text" step="0.01" name="holiday_salary" id="edit_leave" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Leave Salary</label>
                        <input type="text" step="0.01" name="holiday_salary" id="edit_leave_bal" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Week Off Salary</label>
                        <input type="text" step="0.01" name="weekoffsalary" id="edit_weekoffsalary" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Work Form Salary</label>
                        <input type="text" step="0.01" name="weekoffsalary" id="edit_workform_salary" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attendance Salary</label>
                        <input type="text" step="0.01" name="weekoffsalary" id="edit_attendance_salay" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Net Salary</label>
                        <input type="number" step="0.01" name="net_salary" id="edit_net_salary" class="form-control">
                    </div>
                </div>
                <!-- 
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                </div> -->

            </form>
        </div>
    </div>
</div>



<script>
    $(document).on("click", ".editemployeesalaryBtn", function() {
        $("#edit_id").val($(this).data("id"));
        $("#edit_emp_id").val($(this).data("empid"));
        $("#edit_name").val($(this).data("name"));
        $("#employee_id").val($(this).data("employee_id"));
        $("#edit_salary_month").val($(this).data("salary_month"));
        $("#edit_home_working_hours").val($(this).data("working_hours"));
        $("#edit_holiday_salary").val($(this).data("holiday_salary"));
        $("#edit_attendance_salay").val($(this).data("attendance_salay"));
        $("#edit_weekoffsalary").val($(this).data("weekoffsalary"));
        $("#edit_workform_salary").val($(this).data("workform_salary"));
        $("#edit_leave").val($(this).data("leave"));
        $("#edit_leave_bal").val($(this).data("leave_bal"));
        $("#edit_net_salary").val($(this).data("net_salary"));
    });
</script>


@endsection