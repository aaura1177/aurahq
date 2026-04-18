@extends('admin.layout.link')

@section('content')
    <div class="container mt-4">
        <div class="d-block justify-content-between align-items-center mb-2">
            <h3>Work Form Home List</h3>
               <form action="{{ route('admin.work-form-home') }}" method="GET">
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
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Working Hours</th>
                    <th>Reson</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($workformhome as $key => $workformhome)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $workformhome->user->name }}</td>
                        <!-- <td>{{ $workformhome->work_date ?? 'N/A' }}</td> -->
                      <td>{{ \Carbon\Carbon::parse($workformhome->work_date)->format('d-m-Y') ?? 'N/A' }}</td>
                        <td>{{ $workformhome->start_time ?? 'N/A' }}</td>
                        <td>{{ $workformhome->end_time ?? 'N/A' }}</td>
                        <td>{{ $workformhome->working_hours ?? 'N/A' }}</td>
                        <td>{{ $workformhome->reason ?? 'N/A' }}</td>
                        <td>{{ $workformhome->location ?? 'N/A' }}</td>
                        <td>{{ $workformhome->status }}</td>
                        <td>

                           <button class="btn btn-sm btn-warning editworkformhomeBtn" data-bs-toggle="modal"
    data-bs-target="#editworkformhomeModal"
    data-id="{{ $workformhome->id }}"
    data-user="{{ $workformhome->user_id }}"
    data-username="{{ $workformhome->user->name ?? '' }}"
    data-work_date="{{ $workformhome->work_date }}"
    data-start_time="{{ $workformhome->start_time }}"
    data-end_time="{{ $workformhome->end_time }}"
    data-reason="{{ $workformhome->reason }}"
    data-location="{{ $workformhome->location }}"
    data-status="{{ $workformhome->status }}">
    Edit
</button>



                           <form action="{{ route('admin.workformhome.destroy', $workformhome->id) }}" method="POST"
      style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this item?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
</form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


   <div class="modal fade" id="editworkformhomeModal" tabindex="-1" aria-labelledby="editworkformhomeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.workformhome.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="editworkformhomeModalLabel">Edit Work From Home</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="mb-3">
                        <label class="form-label">User ID</label>
                        <input type="text" name="user_id" id="edit_user_name" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Work Date</label>
                        <input type="date" name="work_date" id="edit_work_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Start Time</label>
                        <input type="time" name="start_time" id="edit_start_time" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">End Time</label>
                        <input type="time" name="end_time" id="edit_end_time" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" id="edit_reason" class="form-control" rows="3"readonly></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <textarea name="location" id="edit_location" class="form-control" rows="2"readonly></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>

            </form>
        </div>
    </div>
</div>




    <script>
     $(document).on("click", ".editworkformhomeBtn", function() {
    var id = $(this).data("id");
    var userId = $(this).data("user");
    var username = $(this).data("username");
    var workDate = $(this).data("work_date");
    var startTime = $(this).data("start_time");
    var endTime = $(this).data("end_time");
    var reason = $(this).data("reason");
    var location = $(this).data("location");
    var status = $(this).data("status");

    // Populate the modal form fields
    $("#edit_id").val(id);
    $("#edit_user_id").val(userId);
    $("#edit_user_name").val(username);
    $("#edit_work_date").val(workDate);
    $("#edit_start_time").val(startTime);
    $("#edit_end_time").val(endTime);
    $("#edit_reason").val(reason);
    $("#edit_location").val(location);
    $("#edit_status").val(status);
});

    </script>
    
@endsection
