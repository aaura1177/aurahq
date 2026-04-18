@extends('admin.layout.link')

@section('content')
    <div class="container mt-4">
        <h4>Add New Task By <span class="text-success"> -- {{ $admin->username }}</span>
        </h4>
        <a href="{{ route('admin.tasks') }}" class="btn btn-success">Back</a>
        <hr>
        <form action="{{ route('admin.tasks.create') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select" required>
                        <option value="">Select Project</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }} / {{ $project->client_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Employee</label>
                    <select name="employee_id" id="employeeSelect" class="form-select" required>
                        <option value="">Select Employee</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" data-name="{{ $employee->name }}"
                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} / {{ $employee->emp_id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Task Name</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="urgent" {{ old('status') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress
                        </option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Actual Hours</label>
                    <input type="time" onclick="this.showPicker()"
                    onfocus="this.showPicker()" name="actual_hours" class="form-control" value="{{ old('actual_hours') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label" for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required
                        value="{{ old('start_date', date('Y-m-d')) }}" onclick="this.showPicker()"
                        onfocus="this.showPicker()">
                </div>



                <div class="col-md-3 mb-3">
                    <label class="form-label">Completion Date</label>
                    <input type="date" name="complete_date" class="form-control"
                        value="{{ old('complete_date', date('Y-m-d')) }}" onclick="this.showPicker()"
                        onfocus="this.showPicker()">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Estimated Date</label>
                    <input type="date" name="estimated_date" class="form-control"
                        value="{{ old('estimated_date', date('Y-m-d')) }}" onclick="this.showPicker()"
                        onfocus="this.showPicker()">
                </div>

                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary" id="sendTaskBtn">Send Task</button>
                </div>
            </div>
        </form>
    </div>


    <script>
        document.getElementById('employeeSelect').addEventListener('change', function() {
            let selectedOption = this.options[this.selectedIndex];
            let employeeName = selectedOption.dataset.name || 'Send Task';
            document.getElementById('sendTaskBtn').textContent = `Send Task  ${employeeName}`;
        });
    </script>
@endsection
