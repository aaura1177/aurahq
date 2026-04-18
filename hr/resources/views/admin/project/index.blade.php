@extends('admin.layout.link')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Project List</h3>
            <a href="{{ route('admin.project.add') }}" class="btn btn-primary">+ Add Project</a>
        </div>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>SR</th>
                    <th>Project Name</th>
                    <th>Project Code</th>
                    <th>Assigned User</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projects as $key => $project)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $project->name }}</td>
                        <td>{{ $project->code ?? 'N/A' }}</td>
                        <td>{{ $project->user->name ?? 'N/A' }}</td>
                        <td>{{ $project->status }}</td>
                        <td>{{ $project->priority }}</td>
                        <td>

                            <button class="btn btn-sm btn-warning editProjectBtn" data-bs-toggle="modal"
                                data-bs-target="#editProjectModal" data-id="{{ $project->id }}"
                                data-user="{{ $project->user_id }}" data-name="{{ $project->name }}"
                                data-code="{{ $project->code }}" data-client-name="{{ $project->client_name }}"
                                data-start-date="{{ $project->start_date }}"
                                data-received-date="{{ $project->received_date }}"
                                data-client-delivery-date="{{ $project->client_delivery_date }}"
                                data-company-delivery-date="{{ $project->company_delivery_date }}"
                                data-status="{{ $project->status }}" data-priority="{{ $project->priority }}"
                                data-budget="{{ $project->budget }}" data-actual-cost="{{ $project->actual_cost }}"
                                data-profit-loss="{{ $project->profit_loss }}" data-team-size="{{ $project->team_size }}"
                                data-project-category="{{ $project->project_category }}"
                                data-location="{{ $project->location }}" data-remark="{{ $project->remark }}">
                                Edit
                            </button>


                          <form action="{{ route('admin.project.destroy', $project->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger"
        onclick="return confirm('Are you sure you want to delete this project?')">
        Delete
    </button>
</form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProjectModalLabel">Edit project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm" action="{{route('admin.project.edit')}}" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="id" id="edit_project_id">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Project Name</label>
                                <input type="text" name="name" id="edit_project_name" class="form-control" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Project Code</label>
                                <input type="text" name="code" id="edit_project_code" class="form-control" required>
                            </div>

                          

                            <div class="mb-3 col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="edit_status" class="form-select">
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

                         

                            <div class="mb-3 col-md-3 ">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="edit_start_date" class="form-control">
                            </div>

                            <div class="mb-3 col-md-3">
                                <label class="form-label">Received Date</label>
                                <input type="date" name="received_date" id="edit_received_date" class="form-control">
                            </div>
                            
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Client Delivery Date</label>
                                <input type="date" name="client_delivery_date" id="edit_client_delivery_date" class="form-control">
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Company Delivery Date</label>
                                <input type="date" name="company_delivery_date" id="edit_company_delivery_date" class="form-control">
                            </div>

                            <div class="mb-3 col-md-3 ">
                                <label class="form-label">Budget</label>
                                <input type="number" name="budget" id="edit_budget" class="form-control">
                            </div>

                            <div class="mb-3 col-md-3">
                                <label class="form-label">Actual Cost</label>
                              
                                <input type="number" name="actual_cost" id="edit_actual_cost" class="form-control">

                            </div>

                            <div class="mb-3 col-md-3 ">
                                <label class="form-label">Profit Loss</label>
                                <input type="number" name="profit_loss" id="edit_profit_loss" class="form-control">
                            </div>

                            <div class="mb-3 col-md-3">
                                <label class="form-label">Team Size</label>
                                <input type="number" name="team_size" id="edit_team_size" class="form-control">
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Project Category</label>
                                <input type="text" name="project_category" id="edit_project_category" class="form-control">
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Project Location</label>
                                <input type="text" name="location" id="edit_location" class="form-control">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Project Remark</label>
                                <input type="text" name="remark" id="edit_remark" class="form-control">
                            </div>



                         
                        </div>

                        <div class="text-end">
                            <button type="submit" id="UpdateProjectBtn" class="btn btn-success">Update Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).on("click", ".editProjectBtn", function() {
            var projectId = $(this).data("id");
            var projectName = $(this).data("name");
            var projectCode = $(this).data("code");
            var clientName = $(this).data("client-name");
            var startDate = $(this).data("start-date");
            var receivedDate = $(this).data("received-date");
            var clientDeliveryDate = $(this).data("client-delivery-date");
            var companyDeliveryDate = $(this).data("company-delivery-date");
            var status = $(this).data("status");
            var priority = $(this).data("priority");
            var budget = $(this).data("budget");
            var actualCost = $(this).data("actual-cost");
            var profitLoss = $(this).data("profit-loss");
            var teamSize = $(this).data("team-size");
            var projectCategory = $(this).data("project-category");
            var location = $(this).data("location");
            var remark = $(this).data("remark");
    
            // Set the values to the modal form fields
            $("#edit_project_id").val(projectId);
            $("#edit_project_name").val(projectName);
            $("#edit_project_code").val(projectCode);
            $("#edit_client_name").val(clientName);
            $("#edit_start_date").val(startDate);
            $("#edit_received_date").val(receivedDate);
            $("#edit_client_delivery_date").val(clientDeliveryDate);
            $("#edit_company_delivery_date").val(companyDeliveryDate);
            $("#edit_status").val(status);
            $("#edit_priority").val(priority);
            $("#edit_budget").val(budget);
            $("#edit_actual_cost").val(actualCost);
            $("#edit_profit_loss").val(profitLoss);
            $("#edit_team_size").val(teamSize);
            $("#edit_project_category").val(projectCategory);
            $("#edit_location").val(location);
            $("#edit_remark").val(remark);
        });
    </script>
    
@endsection
