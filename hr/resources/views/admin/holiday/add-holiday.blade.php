@extends('admin.layout.link')

@section('content')
    <div class="container mt-4">
        <h4>Add New Holiday</h4>
        <a href="{{ route('admin.holiday') }}" class="btn btn-primary"> Back</a>
        <hr>
        <form action="{{ route('admin.holiday.create') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Holiday Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" onclick="this.showPicker()"
                    onfocus="this.showPicker()" class="form-control" required>
                </div>
                
                <div class="col-md-3 mb-3">
                  <label class="form-label">Is Active</label>
                  <select name="is_active" class="form-select">
                      <option value="1">Active</option>
                      <option value="0">In Active</option>
                  </select>
              </div>
              <div class="col-md-3 mb-3">
                  <label class="form-label">Color</label>
                  <input type="color" name="color" class="form-control" required>
                  </div>

             
            <div class="col-md-6 mb-3">
                <label class="form-label">Holiday Remark</label>
                <input type="text" name="remark" class="form-control" required>
            </div>

                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Send Holiday</button>
                </div>
            </div>
        </form>
    </div>
@endsection