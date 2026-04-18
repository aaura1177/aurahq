@extends('admin.layout.link')

@section('content')
<div class="container mt-4">
    <h4>Add Department</h4>
    <a href="{{ route('admin.department') }}" class="btn btn-primary"> Back</a>
    <hr>
    <form action="{{ route('admin.department.create') }}" method="POST">
        @csrf
        <div class="row">
    
            <div class="col-md-6 mb-3">
                <label class="form-label">Department Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label"> Code</label>
                <input type="text" name="code" value="{{$departmetcode}}" class="form-control" required>
            </div>
            {{-- <div class="col-md-6 mb-3">
                <label class="form-label"> Total Employee</label>
                <input type="number" name="total_employee" class="form-control" required>
            </div> --}}
            <div class="col-md-6 mb-3">
                <label class="form-label"> Description</label>
                <textarea name="description" class="form-control" required>
                </textarea>
            </div>


            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </form>
</div>
@endsection