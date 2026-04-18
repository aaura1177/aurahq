@extends('admin.layout.link')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Department List</h3>
        <a href="{{ route('admin.department.add') }}" class="btn btn-primary">+ Add Department</a>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>SR</th>
                <th>Department Name</th>
                <th>Department Code</th>
                <th>Total Employee</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($department as $key => $department)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $department->name }}</td>
                <td>{{ $department->code ?? 'N/A' }}</td>
                <td>{{ $department->employees_count ?? 'N/A' }}</td>
                <td>

                    <button class="btn btn-sm btn-warning editDepartmentBtn" data-bs-toggle="modal"
                        data-bs-target="#editDepartmentModal" data-id="{{ $department->id }}"
                        data-user="{{ $department->user_id }}" data-name="{{ $department->name }}"
                        data-code="{{ $department->code }}"
                        data-description="{{ $department->description }}">
                        Edit
                    </button>


                  <form action="{{ route('admin.department.destroy', $department->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger"
        onclick="return confirm('Are you sure you want to delete this department?')">
        Delete
    </button>
</form>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


<div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTaskForm" action="{{ route('admin.department.edit') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_department_id">

                    <div class="row">
                        <!-- Department Name -->
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Department Name</label>
                            <input type="text" name="name" id="edit_department_name" class="form-control"  required>
                        </div>

                        <!-- Department Code (Readonly) - Don't want to update this -->
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Department Code</label>
                            <input type="text" name="code" id="edit_department_code" class="form-control"  readonly>
                        </div>

                        <!-- Department Description -->
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_department_description"></textarea>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" id="UpdateProjectBtn" class="btn btn-success">Update Department</button>
                    </div>
                </form>


            </div>
        </div>
    </div>
</div>



<script>
    $(document).on("click", ".editDepartmentBtn", function() {
        var departmentId = $(this).data("id");
        var departmentName = $(this).data("name");
        var departmentCode = $(this).data("code");
        var departmentDescription = $(this).data("description");
        var userId = $(this).data("user");


        $("#edit_department_id").val(departmentId);
        $("#edit_department_name").val(departmentName);
        $("#edit_department_code").val(departmentCode);
        $("#edit_department_description").val(departmentDescription);
        $("#edit_user_id").val(userId);
    });
</script>

@endsection