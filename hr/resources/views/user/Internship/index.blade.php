@extends('user.layout.link')

@section('content')
<div class="container mt-4">
  <div class="d-block mb-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-dark m-0">Internship List</h3>

        <div class="d-flex gap-2">
            <!-- Select Employee Dropdown -->
            <select class="form-select" id="employeeSelect" style="min-width: 200px;">
                <option value="">Select Employee</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>

            <!-- Tap In/Out Button -->
            <a href="#" id="tapInBtn" class="btn btn-primary">
                Tap In
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('user.internship') }}" method="GET">
        <div class="row g-3">
            {{-- From Date --}}
            <div class="col-md-4">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control"
                    value="{{ request('from_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}"
                    onclick="this.showPicker()" onfocus="this.showPicker()">
            </div>

            {{-- To Date --}}
            <div class="col-md-4">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control"
                    value="{{ request('to_date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                    onclick="this.showPicker()" onfocus="this.showPicker()">
            </div>

            {{-- Submit Button --}}
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    Search
                </button>
            </div>
        </div>
    </form>
</div>


    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>SR</th>
                <th>Employee Name</th>
                <th>Date</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Working Hours</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($internshipAttendance as $key => $row)
                @php
                    $employee = $employees->firstWhere('id', $row->employee_id);
                @endphp
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $employee->name ?? 'N/A' }}</td>
                   <td>{{ \Carbon\Carbon::parse($row->date)->format('d-m-Y') }}</td>

                    <td>{{ $row->check_in_time ?? '-' }}</td>
                    <td>{{ $row->check_out_time ?? '-' }}</td>
                    <td>{{ $row->working_hours ?? '-' }}</td>
                    <td>{{ $row->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    const employeeSelect = document.getElementById('employeeSelect');
    const tapInBtn = document.getElementById('tapInBtn');

    // Update button text and style on employee change
    employeeSelect.addEventListener('change', function () {
        const employeeId = this.value;

        if (!employeeId) {
            tapInBtn.textContent = 'Tap In';
            tapInBtn.classList.remove('btn-danger');
            tapInBtn.classList.add('btn-primary');
            return;
        }

        fetch(`/user/internship/check-status/${employeeId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'in') {
                    tapInBtn.textContent = 'Tap Out';
                    tapInBtn.classList.remove('btn-primary');
                    tapInBtn.classList.add('btn-danger'); 
                } else {
                    tapInBtn.textContent = 'Tap In';
                    tapInBtn.classList.remove('btn-danger');
                    tapInBtn.classList.add('btn-primary'); 
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tapInBtn.textContent = 'Tap In';
                tapInBtn.classList.remove('btn-danger');
                tapInBtn.classList.add('btn-primary');
            });
    });

    // Action on tap button click
    tapInBtn.addEventListener('click', function (e) {
        e.preventDefault();
        const employeeId = employeeSelect.value;

        if (!employeeId) {
            alert('Please select an employee');
            return;
        }

        if (tapInBtn.textContent === 'Tap Out') {
            if (confirm("You are already tapped in. Do you want to Tap Out?")) {
                window.location.href = `/user/internship/tapout/${employeeId}`;
            }
        } else {
            window.location.href = `/user/internship/tapin/${employeeId}`;
        }
    });
</script>

@endsection
