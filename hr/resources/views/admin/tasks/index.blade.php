@extends('admin.layout.link')
@section('content')
<style>
     @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0; }
        100% { opacity: 1; }
    }
    .blink {
        animation: blink .6s infinite;
    }
</style>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Task List</h3>
        <a href="{{ route('admin.tasks.add') }}" class="btn btn-primary">+ Add Task</a>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>SR</th>
                <th>Task Name</th>
                <th>Project</th>
                <th>Employee Name</th>
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
                                {{ $task->priority }} — {{ $task->remark ?? 'No Remark' }}              
                                  </span>
                </td>
                
                <td>
                    <span class="badge rounded-pill {{ $statusColors[$task->employee_status] ?? 'bg-secondary text-white' }}">
                        {{ ucfirst(str_replace('_', ' ', $task->employee_status)) }}
                    </span>
                </td>
                <td> <button class="btn btn-sm btn-warning editTaskBtn" data-bs-toggle="modal"
                        data-bs-target="#editTaskModal" data-id="{{ $task->id }}"
                        data-name="{{ $task->name }}" data-project="{{ $task->project_id }}"
                        data-employee="{{ $task->employee_id }}" data-status="{{ $task->status }}"
                        data-priority="{{ $task->priority }}" data-actual-hours="{{ $task->actual_hours }}"
                        data-start-date="{{ $task->start_date }}" data-complete-date="{{ $task->complete_date }}"
                        data-estimated-date="{{ $task->estimated_date }}">
                        Edit
                    </button> |
<form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger"
        onclick="return confirm('Are you sure you want to delete this task?')">
        Delete
    </button>
</form>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTaskForm" action="{{ route('admin.tasks.edit') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_task_id">
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Task Name</label>
                            <input type="text" name="name" id="edit_task_name" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Project</label>
                            <select name="project_id" id="edit_project_id" class="form-select">
                                @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-5">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" id="edit_employee_id" class="form-select">
                                @foreach ($employees as $employee)
                                <option data-name="{{ $employee->name }}" value="{{ $employee->id }}">
                                    {{ $employee->name }} / {{ $employee->emp_id }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="urgent">Urgent</option>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                        </div>

                        <div class="mb-3 col-md-2">
                            <label class="form-label">Priority</label>
                            <select name="priority" id="edit_priority" class="form-select">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>

                        <div class="mb-3 col-md-3">
                            <label class="form-label">Actual H.</label>
                            <input type="time" onclick="this.showPicker()"
                                onfocus="this.showPicker()" name="actual_hours" id="edit_actual_hours" class="form-control">
                        </div>

                        <div class="mb-3 col-md-3 ">
                            <label class="form-label">Start Date</label>
                            <input onclick="this.showPicker()" onfocus="this.showPicker()" type="date"
                                name="start_date" id="edit_start_date" class="form-control">
                        </div>

                        <div class="mb-3 col-md-3">
                            <label class="form-label">Completion Date</label>
                            <input onclick="this.showPicker()" onfocus="this.showPicker()" type="date"
                                name="complete_date" id="edit_complete_date" class="form-control">
                        </div>

                        <div class="mb-3 col-md-3">
                            <label class="form-label">Estimated Date</label>
                            <input onclick="this.showPicker()" onfocus="this.showPicker()" type="date"
                                name="estimated_date" id="edit_estimated_date" class="form-control">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" id="UpdateTaskBtn" class="btn btn-success">Update Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).on("click", ".editTaskBtn", function() {
        var taskId = $(this).data("id");
        var taskName = $(this).data("name");
        var projectId = $(this).data("project");
        var employeeId = $(this).data("employee");
        var status = $(this).data("status");
        var priority = $(this).data("priority");
        var actualHours = $(this).data("actual-hours");
        var startDate = $(this).data("start-date");
        var completeDate = $(this).data("complete-date");
        var estimatedDate = $(this).data("estimated-date");

        $("#edit_task_id").val(taskId);
        $("#edit_task_name").val(taskName);
        $("#edit_project_id").val(projectId);
        $("#edit_employee_id").val(employeeId);
        $("#edit_status").val(status);
        $("#edit_priority").val(priority);
        $("#edit_actual_hours").val(actualHours);
        $("#edit_start_date").val(startDate);
        $("#edit_complete_date").val(completeDate);
        $("#edit_estimated_date").val(estimatedDate);
    });

    document.getElementById('edit_employee_id').addEventListener('change', function() {
        let selectedOption = this.options[this.selectedIndex];
        let employeeName = selectedOption.dataset.name || 'Update Task';
        document.getElementById('UpdateTaskBtn').textContent = `Update Task  ${employeeName}`;
    });
</script>
@endsection