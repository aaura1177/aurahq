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
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="text-dark">Leaves List</h3>
    <a href="{{route('add.leave')}}" class="btn btn-primary">+ Add Leaves</a>
  </div>
</div>






<table class="table table-bordered">
  <thead class="table-dark">
    <tr>
      <th>SR</th>
      <th>Employee Name</th>
      <th>Leave Type</th>
      <th>Start Date</th>
      <th>End Date </th>
      <th> Reason</th>
      <th>Rejection Reason</th>
      <th>Status</th>

      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    @if(isset($leavetypes) && $leavetypes->count() > 0)

    @foreach ($leavetypes as $key => $leavetypes)
    <tr>
      <td>{{ $key + 1 }}</td>
      <td>{{ $leavetypes->employee->name  ?? 'N/A' }}</td>
      <td>{{ $leavetypes->leaveType->leave_name ?? 'N/A' }}</td>
      <td>{{ \Carbon\Carbon::parse($leavetypes->start_at)->format('d-m-Y') ?? 'N/A' }}</td>
      <td>{{ \Carbon\Carbon::parse($leavetypes->end_at)->format('d-m-Y') ?? 'N/A' }}</td>
      <td>{{ $leavetypes->reason?? 'N/A' }}</td>
      <td>
  {{ \Str::limit($leavetypes->rejection_reason_text ?? 'N/A', 10) }} 
  @if(strlen($leavetypes->rejection_reason_text ?? '') > 10)
    <a href="#" data-bs-toggle="modal" data-bs-target="#editLeaveModal" 
       data-rejection-reason-text="{{ $leavetypes->rejection_reason_text ?? 'N/A' }}">More</a>
  @endif
</td>





      @php
      $statusColors = [
      'approved' => 'bg-success text-white',
      'rejected' => 'bg-danger text-white',
      'pending' => 'bg-warning text-dark', // Added bg-warning for pending status
      ];
      @endphp
      <td>
        <span class="badge rounded-pill {{ $statusColors[$leavetypes->status] ?? 'bg-secondary text-white' }}">
          {{ ucfirst(str_replace('_', ' ', $leavetypes->status)) }}
        </span>
      </td>




      <td>
        @if($leavetypes->status == 'pending')

    

<form action="{{ route('user.leave.destroy', $leavetypes->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit"  class="btn btn-sm btn-danger">Delete</button>
</form>
@endif
      </td>
    </tr>
    @endforeach
    @else
    <tr>
      <td colspan="8" class="text-center text-danger">No Data Available</td>
    </tr>
    @endif
  </tbody>
</table>
</div>



</div>
<div class="modal fade" id="editLeaveModal" tabindex="-1" aria-labelledby="editLeaveModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editLeaveModalLabel"> Admin Rejection Reason</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editTaskForm" action="#" method="POST">
          @csrf
          @method('PUT')

          <input type="hidden" name="id" id="edit_leave_id">
          <div class="row">
            <div class="mb-3 col-md-6">
              <!-- This is the textarea where the rejection reason will be populated -->
              <textarea id="edit_rejection_reason" class="form-control" readonly></textarea>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
var editLeaveModal = document.getElementById('editLeaveModal');
editLeaveModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget;
  
  var rejectionReasonText = button.getAttribute('data-rejection-reason-text');
  
  var rejectionTextarea = document.getElementById('edit_rejection_reason');
  rejectionTextarea.value = rejectionReasonText;
});



</script>



@endsection