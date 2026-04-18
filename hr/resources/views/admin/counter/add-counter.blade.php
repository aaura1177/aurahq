@extends('admin.layout.link')

@section('content')
    <div class="container mt-5">
        <h2>Add Counter</h2>

        <a href="{{route('admin.counter')}}" class="btn btn-success">Back</a>
        <hr>
     
        <form action="{{ route('admin.counter.create') }}" method="POST">
            @csrf
            <div class="row">    

              
            <div class="mb-3 col-md-4">
                <label class="form-label">Counter Name</label>
                <input type="text" name="counter_name" class="form-control" >
            </div>   
            <div class="mb-3 col-md-4">
                <label class="form-label">Prefix</label>
                <input type="text" name="prefix" class="form-control"  style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
            </div>
            {{-- <div class="mb-3 col-md-4">
                <label class="form-label">Count</label>
                <input type="number" name="count" class="form-control" required>
            </div> --}}
            <div class="mb-3 col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
          </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
@endsection
