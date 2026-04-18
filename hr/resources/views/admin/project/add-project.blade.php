@extends('admin.layout.link')

@section('content')
    <div class="container mt-4">
        <h4>Add New Project</h4>
        <a href="{{ route('admin.project') }}" class="btn btn-primary"> Back</a>
        <hr>
        <form action="{{ route('admin.project.create') }}" method="POST">
            @csrf
            <div class="row">


                <div class="col-md-6 mb-3">
                    <label class="form-label">Project Name<code>*</code></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $project->name ?? '') }}" >
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Client Name <code>*</code></label>
                    <input type="text" name="client_name" class="form-control" value="{{ old('client_name', $project->client_name ?? '') }}" >
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Project Code</label>
                    <input type="text" name="code" class="form-control" value="{{ old('code', $project->code ?? '') }}" >
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="pending" {{ old('status', $project->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ old('status', $project->status ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $project->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="on_hold" {{ old('status', $project->status ?? '') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="Low" {{ old('priority', $project->priority ?? '') == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ old('priority', $project->priority ?? '') == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ old('priority', $project->priority ?? '') == 'High' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" onclick="this.showPicker()" onfocus="this.showPicker()" class="form-control" value="{{ old('start_date', $project->start_date ?? '') }}" >
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Received Date</label>
                    <input type="date" name="received_date" onclick="this.showPicker()" onfocus="this.showPicker()" class="form-control" value="{{ old('received_date', $project->received_date ?? '') }}">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Client Delivery Date<code>*</code></label>
                    <input type="date" name="client_delivery_date" onclick="this.showPicker()" onfocus="this.showPicker()" class="form-control" value="{{ old('client_delivery_date', $project->client_delivery_date ?? '') }}" >
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Company Delivery Date</label>
                    <input type="date" name="company_delivery_date" onclick="this.showPicker()" onfocus="this.showPicker()" class="form-control" value="{{ old('company_delivery_date', $project->company_delivery_date ?? '') }}">
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">Budget</label>
                    <input type="number" name="budget" class="form-control" value="{{ old('budget', $project->budget ?? '') }}">
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">Actual Cost</label>
                    <input type="number" name="actual_hours" class="form-control" value="{{ old('actual_hours', $project->actual_hours ?? '') }}">
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">Profit Loss</label>
                    <input type="number" name="profit_loss" class="form-control" value="{{ old('profit_loss', $project->profit_loss ?? '') }}">
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">Team Size</label>
                    <input type="number" name="team_size" class="form-control" value="{{ old('team_size', $project->team_size ?? '') }}">
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">Project Category</label>
                    <input type="text" name="project_category" class="form-control" value="{{ old('project_category', $project->project_category ?? '') }}">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Project Location</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location', $project->location ?? '') }}" >
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Project Remark</label>
                    <input type="text" name="remark" class="form-control" value="{{ old('remark', $project->remark ?? '') }}" >
                </div>
                

                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
@endsection
