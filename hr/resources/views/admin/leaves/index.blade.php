@extends('admin.layout.link')

@section('content')
<div class="container mt-4">
    <div class="d-block justify-content-between align-items-center mb-2">
        <h3>Leaves List</h3>
          <form action="{{ route('admin.leaves') }}" method="GET">
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
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>SR</th>
                <th>Employee Name</th>
                <th>Leave Type</th>
                <th>Start At</th>
                <th>End At</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Applied On</th>
                

                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($leaves as $key => $leaves)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $leaves->employee->name ?? 'N/A'}} </td>
                <td>{{ $leaves->leaveType->leave_name ?? 'N/A'}} </td>

                <td>{{ \Carbon\Carbon::parse($leaves->start_at)->format('d-m-Y') ?? 'N/A' }}</td>

                <td>{{ \Carbon\Carbon::parse($leaves->end_at)->format('d-m-Y') ?? 'N/A' }}</td>

                @php

                // Define the status colors based on the leave status
                $statusColors = [
                'approved' => 'bg-success text-white',
                'rejected' => 'bg-danger text-white',
                'pending' => 'bg-warning text-dark', // Added bg-warning for pending status
                ];
                @endphp
                <td>{{ $leaves->reason ?? 'N/A'}} </td>

                <td>
                    <span class="badge rounded-pill {{ $statusColors[$leaves->status] ?? 'bg-secondary text-white' }}">
                        {{ ucfirst($leaves->status) }} <!-- Capitalizing the first letter of the status -->
                    </span>
                </td>


<td>
    {{ $leaves->applied_on ? \Carbon\Carbon::parse($leaves->applied_on)->format('d-m-Y') : 'N/A' }}
</td>

              
            
                <td>
                    <button class="btn btn-sm btn-warning editLeavesBtn" data-bs-toggle="modal"
                        data-bs-target="#editLeaveModal" data-id="{{ $leaves->id }}"
                        data-name="{{ $leaves->employee->name}}" data-rejection-reason-text="{{ $leaves->rejection_reason_text}}"
                        data-status="{{ $leaves->status}}" data-approved-by="{{ $leaves->approved_by }}">
                        Edit
                    </button>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>


<div class="modal fade" id="editLeaveModal" tabindex="-1" aria-labelledby="editLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLeaveModalLabel">Edit Leaves</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTaskForm" action="{{route('admin.leaves.edit')}}" method="POST">
                    @csrf
                    @method('PUT')
                
                    <input type="hidden" name="id" id="edit_leave_id">
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Employee Name</label>
                            <input type="text" id="edit_leave_name" class="form-control" readonly>
                        </div>
                
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_leave_status" class="form-select">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                
                        <div class="mb-3 col-md-6" id="rejection_reason_container" style="display: none;">
                            <label class="form-label">Rejection Reason</label>
                            <textarea class="form-control" name="rejection_reason_text" id="edit_rejection_reason"></textarea>
                        </div>
                
                    </div>
                
                    <div class="text-end">
                        <button type="submit" id="UpdateTaskBtn" class="btn btn-success">Update Leaves</button>
                    </div>
                </form>
                
                <script>
                    // Handle the display of rejection reason field based on status change
                    document.getElementById('edit_leave_status').addEventListener('change', function() {
                        const rejectionReasonContainer = document.getElementById('rejection_reason_container');
                        const rejectionReasonText = document.getElementById('edit_rejection_reason');
                        if (this.value === 'rejected') {
                            rejectionReasonContainer.style.display = 'block'; // Show rejection reason
                            rejectionReasonText.setAttribute('required', 'required'); // Make it required
                        } else {
                            rejectionReasonContainer.style.display = 'none'; // Hide rejection reason
                            rejectionReasonText.removeAttribute('required'); // Remove required attribute
                        }
                    });
                
                    // Trigger the event on page load to set the initial state
                    document.getElementById('edit_leave_status').dispatchEvent(new Event('change'));
                </script>
                
            </div>
        </div>
    </div>
</div>


<script>
    $(document).on("click", ".editLeavesBtn", function() {
        var leavesId = $(this).data("id");
        var leavesName = $(this).data("name");
        var rejection = $(this).data("rejection-reason-text");
        var status = $(this).data("status");
        var approvedBy = $(this).data("approved-by");

        // Set the values in the modal fields
        $("#edit_leave_id").val(leavesId);
        $("#edit_leave_name").val(leavesName);
        $("#edit_rejection_reason").val(rejection);
        $("#edit_leave_status").val(status);
        $("#edit_approved_by").val(approvedBy);
    });
</script>


@endsection