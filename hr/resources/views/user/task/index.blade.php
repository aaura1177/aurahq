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
    <h3 style="color: black;">Task List</h3>    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>SR</th>
                <th>Task Name</th>
                <th>Project</th>
                <th>Employee Name</th>
                <th>Date</th>
                <th>Times & Days </th>
                <th>Status</th>
                <th>Priority</th>
                <th>Employee Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $key => $task)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $task->name ?? 'N/A' }}</td>
                <td>{{ $task->project->name ?? 'N/A' }}</td>
                <td>{{ ($task->employee->name ?? 'N/A') . ' / ' . ($task->employee->emp_id ?? 'N/A') }}</td>
<td>{{ $task->created_at ? $task->created_at->format('d-m-Y') : 'N/A' }}</td>
                <td>
                    @if ($task->actual_hours === null)
                    {{ $task->start_date ?? 'N/A' }} - {{ $task->complete_date ?? 'N/A' }}
                    @if ($task->start_date && $task->complete_date)
                    <span class="text-success">
                        ({{ \Carbon\Carbon::parse($task->start_date)->diffInDays(\Carbon\Carbon::parse($task->complete_date)) }} days)
                    </span>
                    @endif
                    @else
                    {{ $task->actual_hours }} hours
                    @endif
                </td>
                @php
                $statusColors = [
                    'urgent' => $task->employee_status == 'pending' ? 'bg-danger text-dark blink' : 'bg-danger text-dark',
                    'pending' => 'bg-warning text-dark',
                'in_progress' => 'bg-primary text-white',
                'completed' => 'bg-success text-white',
                'on_hold' => 'bg-danger text-white',
                ];
                @endphp
                <td>
                    <span class="badge rounded-pill {{ $statusColors[$task->status] ?? 'bg-secondary text-white' }}">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                </td>
                <td>
                    <span
                        class="badge rounded-pill 
                                @if ($task->priority == 'Low') bg-info text-dark
                                @elseif($task->priority == 'Medium') bg-warning text-dark
                                @elseif($task->priority == 'High') bg-danger text-white @endif">
                        {{ $task->priority }}
                    </span>
                </td>
                

                <td>
                    <span class="badge rounded-pill {{ $statusColors[$task->employee_status] ?? 'bg-secondary text-white' }}">
                        {{ ucfirst(str_replace('_', ' ', $task->employee_status)) }}
                    </span>
                </td>

                <td> <button class="btn btn-sm btn-warning editTaskBtn" data-bs-toggle="modal"
                        data-bs-target="#editTaskModal" data-id="{{ $task->id }}"
                        data-name="{{ $task->name}}"
                        data-remark="{{ $task->remark}}"
                        data-employee-status="{{ $task->employee_status }}">
                        Edit
                    </button>



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