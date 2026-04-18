@extends('admin.layout.link')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Employee Add Salary</h3>
    </div>

    <form action="{{ route('admin.create.salary') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="employee_id" class="form-label">Employee</label>
                    <select name="employee_id" id="employee_id" class="form-control" required>
                        <option value="all">All Employee</option>
                        @foreach ($employees as $employee)                            
                        <option value="{{$employee->id}}">{{$employee->name}} // {{$employee->emp_id}}</option>
                        @endforeach

                    </select>
                    @error('employee_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                  <label for="salary_month" class="form-label">Salary Month</label>
                  <input type="month" name="salary_month" id="salary_month" class="form-control" required  
                         min="{{ \Carbon\Carbon::now()->subMonth()->format('Y-m') }}" 
                         max="{{ \Carbon\Carbon::now()->format('Y-m') }}"
                         onfocus="this.showPicker()">
                  @error('salary_month')
                      <div class="text-danger">{{ $message }}</div>
                  @enderror
              </div>
          </div>
          
           
        </div>


        <button type="submit" class="btn btn-primary">Save Salary</button>
    </form>
</div>
@endsection
