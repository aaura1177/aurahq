@extends('admin.layout.link')

@section('content')
    <div class="container mt-4">
        <h4>Add Eltercity Reading</h4>
        <a href="{{ route('admin.eltercity_readings') }}" class="btn btn-primary"> Back</a>
        <hr>
      <form action="{{ route('admin.eltercity_readings.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        {{-- Time Slot --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Time Slot</label>
            <select name="time_slot" class="form-select">
                <option value="">Select Time Slot</option>
                <option value="morning">Morning</option>
                <option value="evening">Evening</option>
            </select>
        </div>

        {{-- Reading --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Reading</label>
            <input type="text" name="reading" class="form-control" placeholder="Enter reading value">
        </div>

        {{-- Date --}}
       <div class="col-md-6 mb-3">
    <label class="form-label">Date</label>
    <input type="date" name="date"
           value="{{ date('Y-m-d') }}"
           onclick="this.showPicker()"
           onfocus="this.showPicker()"
           class="form-control" required>
</div>


        {{-- Screenshot Upload --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Screenshot</label>
            <input type="file" name="screenshot" class="form-control" accept="image/*">
        </div>

        {{-- Submit Button --}}
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">Submit Reading</button>
        </div>
    </div>
</form>

    </div>
@endsection