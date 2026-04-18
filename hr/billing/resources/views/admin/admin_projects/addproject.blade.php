@extends('admin.layout.master')
@section('content')


<section id="add_data">
    <h2>
        Add New Project
    </h2>

    <div id="form_container">

    <form action="{{ route('project.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-input">
            <label for="title">Project Name: <span class="error">*</span></label>
            <input type="text" class="inp" name="title" placeholder="Project Name"/>
        </div>

        <div class="form-input">
            <label for="description">Project Description: <span class="error">*</span></label>
            <textarea class="inp" name="description" placeholder="Project Description" ></textarea>
        </div>

        <div class="form-input">
            <label for="start_date">Start Date: <span class="error">*</span></label>
            <input type="date" class="inp" name="start_date" />
        </div>

        <div class="form-input">
            <label for="end_date">End Date: <span class="error">*</span></label>
            <input type="date" class="inp" name="end_date" />
        </div>

        <div class="form-input">
            <label for="client_name">Client Name: <span class="error">*</span></label>
            <input type="text" class="inp" name="client_name" placeholder="Client Name"/>
        </div>

        <div class="form-input">
            <label for="attachment">Project Attachment: <span class="error">*</span></label>
            <input type="file" class="inp" name="attachment"  />
        </div>

        <div class="form-input">
    <label for="status">Project Status: <span class="error">*</span></label>
    <select class="inp" name="status" required>
        <option value="in_progress">In Progress</option>
        <option value="completed">Completed</option>
        <option value="pending">Pending</option>
        <option value="on_hold">On Hold</option>
    </select>
</div>

        <div class="form-submit-container">
            <button type="submit" class="btn success">Submit</button>
            <button type="button" class="btn danger"><a href="{{ route('admin.project') }}" style="color: white; text-decoration: none;">Exit</a></button>
        </div>
    </form>
    </div>
</section>






















@endsection