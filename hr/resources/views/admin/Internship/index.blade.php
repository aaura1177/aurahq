@extends('admin.layout.link')

@section('content')
    <div class="container mt-4">
        <div class="d-block justify-content-between align-items-center mb-2">
            <h3>Internship Attendance List</h3>

     <form action="{{ route('admin.internship') }}" method="GET">
    <div class="row">
        {{-- Employee Dropdown --}}
        <div class="form-group mb-3 col-md-4">
            <label for="employee_id" class="form-label">Employee</label>
            <select name="employee_id" id="employee_id" class="form-control">
                <option value="">-- All Employees --</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}"
                        {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- From Date --}}
        <div class="form-group mb-3 col-md-4">
            <label for="from_date" class="form-label">From Date</label>
            <input type="date" name="from_date" id="from_date" class="form-control"
                   value="{{ request('to_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" onclick="this.showPicker()"
                        onfocus="this.showPicker()">
        </div>

        {{-- To Date --}}
       <div class="form-group mb-3 col-md-4">
    <label for="to_date" class="form-label">To Date</label>
    <input type="date" name="to_date" id="to_date" class="form-control"
           value="{{ request('to_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" onclick="this.showPicker()"
                        onfocus="this.showPicker()">
</div>


        {{-- Submit Button --}}
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </div>
</form>




        </div>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>SR</th>
                    <th>Employee Name</th>
                    <th>User Name</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>date</th>
                    <th>Working Hours</th>
                    <th>Status</th>
                    @if (Auth::user()->id == 1)
                    <th>Action</th>
                    @endif
                    <!-- <th>Actions</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach ($attendance as $key => $attendance)
                    @php
                        // Check if the attendance date is today
                        $isToday = \Carbon\Carbon::parse($attendance->date)->isToday();

                        // Define status colors mapping
                        $statusColors = [
                            'Present' => 'bg-success text-white',
                            'Absent' => 'bg-danger text-white',
                            'Half-Day' => 'bg-warning text-dark',
                            'Leave' => 'bg-info text-white',
                        ];

                        // Get the appropriate status color, defaulting to 'bg-secondary' if status is unknown
                        $statusColorClass = $statusColors[$attendance->status] ?? 'bg-secondary text-white';
                    @endphp

                    <tr>
                        <td style="{{ $isToday ? 'background-color: #ceffcef5;' : '' }}">{{ $key + 1 }}@if ($attendance->work_from)
                                <i class="fa-solid fa-house"></i>
                            @endif
                        </td>
                        <td style="{{ $isToday ? 'background-color: #ceffcef5;' : '' }}">{{ $attendance->employee->name }}
                        </td>
                        <td style="{{ $isToday ? 'background-color: #ceffcef5;' : '' }}">{{ $attendance->user->name }}
                        </td>
                        <td style="{{ $isToday ? 'background-color: #ceffcef5;' : '' }}">
                            {{ $attendance->check_in_time ?? 'N/A' }}</td>
                        <td style="{{ $isToday ? 'background-color: #ceffcef5;' : '' }}">
                            {{ $attendance->check_out_time ?? 'N/A' }}</td>
                        <td style="{{ $isToday ? 'background-color: #ceffcef5;' : '' }}">
                            {{ \Carbon\Carbon::parse($attendance->date)->format('d-m-Y') }}</td>
                        <td style="{{ $isToday ? 'background-color: #ceffcef5;' : '' }}">
                            {{ $attendance->working_hours ?? 'N/A' }}</td>
                       

                        <td style="{{ $isToday ? 'background-color: #ceffcef5;' : '' }}">
                            <span class="badge rounded-pill {{ $statusColorClass }}">
                                {{ ucfirst($attendance->status ?? 'Unknown') }}
                            </span>
                        </td>
                        <td style="{{ $isToday ? 'background-color: #ceffcef5;' : '' }}">
                            @if (Auth::user()->id == 1)
                           <form action="{{ route('admin.internship.destroy', $attendance->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger"
        onclick="return confirm('Are you sure you want to delete this record?')">
        Delete
    </button>
</form>

                    @endif
                        </td>
                       

                    </tr>
                @endforeach


            </tbody>
        </table>
    </div>


    <div class="modal fade" id="editworkformhomeModal" tabindex="-1" aria-labelledby="editworkformhomeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editworkformhomeModalLabel">Edit Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm" action="{{ route('admin.attendace.edit') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="id" id="edit_attendance_id">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Employee Name</label>
                                <input type="text" id="edit_username" class="form-control" required>
                            </div>




                            <div class="mb-3 col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="edit_status" class="form-select">
                                    <option value="Present">Present</option>
                                    <option value="Absent">Absent</option>
                                    <option value="Half-Day">Half-Day</option>

                                </select>
                            </div>
                            <div class="mb-3 col-md-3 ">
                                <label class="form-label">Check In</label>
                                <input type="time" name="check_in_time" id="edit_check_in_time" class="form-control">
                            </div>

                            <div class="mb-3 col-md-3">
                                <label class="form-label">check Out</label>
                                <input type="time" name="check_out_time" id="edit_check_out_time"
                                    class="form-control">
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label"> Date</label>
                                <input type="date" name="date" id="edit_date" class="form-control" readonly>
                            </div>





                        </div>

                        <div class="text-end">
                            <button type="submit" id="UpdateProjectBtn" class="btn btn-success">Update
                                Attendance</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).on("click", ".editworkformhomeBtn", function() {
            var id = $(this).data("id");
            var username = $(this).data("username");
            var checkInTime = $(this).data("check_in_time");
            var checkOutTime = $(this).data("check_out_time");
            var date = $(this).data("date");
            var status = $(this).data("status");

            $("#edit_attendance_id").val(id);
            $("#edit_username").val(username);
            $("#edit_check_in_time").val(checkInTime);
            $("#edit_check_out_time").val(checkOutTime);
            $("#edit_date").val(date);
            $("#edit_status").val(status);
        });
    </script>
@endsection
