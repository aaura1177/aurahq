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
    <h3 style="color: black;">Work Form Home List</h3>   
    <a href="{{route('user.work.formhome.create')}}" class="btn btn-primary">+ Add Work Form Home</a>
 </div>

    <table class="table table-bordered">
        <thead class="table-dark">
                       <tr>
                <th>SR</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Working Time</th>
                <th>Reason</th>
                <th>Location</th>
                <th>Stats</th>
                <th>Action</th>

            </tr>
        </thead>
        <tbody>
            @php
            $i=1;
            @endphp
            @foreach ($workformhome as $workformhome)
            
            
            <tr>
                <td>{{$i++}}</td>
              
                

                      <td>{{ \Carbon\Carbon::parse($workformhome->work_date)->format('d-m-Y') ?? 'N/A' }}</td>
                <td>{{$workformhome->start_time}}</td>
                <td>{{$workformhome->end_time}}</td>
                 <td>{{ $workformhome->working_hours ?? 'N/A' }}</td>
                <td>{{$workformhome->reason}}</td>
                <td>{{$workformhome->location}}</td>
                 @php
      $statusColors = [
      'approved' => 'bg-success text-white',
      'rejected' => 'bg-danger text-white',
      'pending' => 'bg-warning text-dark', 
      ];
      @endphp
      <td>
        <span class="badge rounded-pill {{ $statusColors[$workformhome->status] ?? 'bg-secondary text-white' }}">
          {{ ucfirst(str_replace('_', ' ', $workformhome->status)) }}
        </span>
      </td>

        <td>
        @if($workformhome->status == 'pending')

    

<form action="{{ route('user.work.formhome.destroy', $workformhome->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit"  class="btn btn-sm btn-danger">Delete</button>
</form>
@endif
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
                            <button type="submit" id="UpdateTaskBtn" class="btn btn-success text-center">Update Status</button>
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