@extends('user.layout.link')
@section('content')
    <style>
        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .blink {
            animation: blink .6s infinite;
        }
    </style>
    <div class="container mt-4">
        <div class="d-block justify-content-between align-items-center mb-2">
            <h3 style="color: black;">Attendance List</h3>

            
     <form action="{{ route('user.attendance') }}" method="GET">
    <div class="row">
        {{-- Employee Dropdown --}}
     

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
                    <th scope="col">SR.NO</th>
                    <th scope="col">Date</th>
                    <th scope="col">Check In</th>
                    <th scope="col">Check Out</th>
                    <th scope="col">Working Hours</th>
                    <th scope="col">Earned Salary</th>
                    <th scope="col">Status</th>

                </tr>
            </thead>
            <tbody>
                @php
                                    $i = 1;
                                    @endphp
                                    @foreach ($attendance as $attendance)

                                    @php
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
                                        <td>{{ $i++ }}@if($attendance->work_from)<i class="fa-solid fa-house"></i>@endif</td>
                                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d-m-Y') }}</td>
                                        <td>{{ $attendance->check_in_time }}</td>
                                        <td>{{ $attendance->check_out_time }}</td>
                                        <td>{{ $attendance->working_hours }}</td>
                                        <td>{{ $attendance->earned_salary }}</td>
                                        <td>
                                            <span class="badge rounded-pill {{ $statusColorClass }}">
                                                {{ ucfirst($attendance->status ?? 'Unknown') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm" action="{{ route('user.tasks.edit') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Hidden field for the task ID -->
                        <input type="hidden" name="id" id="edit_task_id">

                        <div class="row">
                            <div class="mb-6 col-md-6">
                                <label class="form-label">Task Name</label>
                                <input type="text" class="form-control" id="edit_task_name" name="task_name" readonly>
                            </div>



                            <div class="mb-6 col-md-6">
                                <label class="form-label">Status</label>
                                <select name="employee_status" id="edit_employee_status" class="form-select">
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="mb-6 col-md-12">
                                <label class="form-label">Remark</label>
                                <input type="text" class="form-control" id="remark" name="remark">
                            </div>
                            <div class="text-end">
                                <button type="submit" id="UpdateTaskBtn" class="btn btn-success text-center">Update
                                    Status</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>



        </div>
    </div>

    </div>


    <script>
        $(document).on("click", ".editTaskBtn", function() {
            var taskId = $(this).data("id");
            var taskname = $(this).data("name");
            var employeeStatus = $(this).data("employee-status");
            var remark = $(this).data("remark");

            $("#edit_task_id").val(taskId);
            $("#edit_task_name").val(taskname);
            $("#edit_employee_status").val(employeeStatus);
            $("#remark").val(remark);
        });

        document.getElementById('edit_employee_id').addEventListener('change', function() {
            let selectedOption = this.options[this.selectedIndex];
            let employeeName = selectedOption.dataset.name || 'Update Task';
            document.getElementById('UpdateTaskBtn').textContent = `Update Task  ${employeeName}`;
        });
    </script>
@endsection
