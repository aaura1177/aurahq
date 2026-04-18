@extends('admin.layout.link')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Holiday List</h3>
        <a href="{{ route('admin.holiday.add') }}" class="btn btn-primary">+ Add Holiday</a>
    </div>

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">Filter Holidays</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.holiday') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small">Year</label>
                    <select name="year" class="form-select form-select-sm">
                        <option value="">All Years</option>
                        @php $selectedYear = request('year') ?? date('Y'); @endphp
                        @for ($y = date('Y') + 1; $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Month</label>
                    <select name="month" class="form-select form-select-sm">
                        <option value="">All Months</option>
                        @foreach (['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'] as $num => $label)
                            <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Search by Name</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Holiday name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-primary">Apply Filter</button>
                    <a href="{{ route('admin.holiday') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>SR</th>
                <th>Holiday Name</th>
                <th>Date</th>
                <th>Approved By</th>
                <th>Is Active</th>
     @if (Auth::user()->id == 1)
                <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($holiday as $key => $holiday)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td style="color: {{ $holiday->color }}; ">
                    {{ $holiday->name }}
                </td>


                <!-- <td>{{ $holiday->date ?? 'N/A' }}</td> -->
                 <td>{{ \Carbon\Carbon::parse($holiday->date)->format('d-m-Y') ?? 'N/A' }}</td>

                <td>{{ $holiday->approved_by ?? 'N/A' }}</td>
                <!-- <td>{{ $holiday->is_active ?? 'N/A' }}</td> -->
                @php
                $statusColors = [
                '1' => 'bg-success text-white',
                '0' => 'bg-danger text-white',
                ];
                @endphp

                <td>
                    <span
                        class="badge rounded-pill {{ $statusColors[ $holiday->is_active] ?? 'bg-secondary text-white' }}">
                        {{ $holiday->is_active == '1' ? 'Active' : ( $holiday->is_active == '0' ? 'Inactive' : 'Unknown') }}
                    </span>
                </td>

               
                         @if (Auth::user()->id == 1)
                          <td>
                    <button class="btn btn-sm btn-warning editHolidayBtn" data-bs-toggle="modal"
                        data-bs-target="#editHolidayModal" data-id="{{ $holiday->id }}"
                        data-name="{{ $holiday->name }}" data-approved-by="{{ $holiday->approved_by }}"
                        data-date="{{ $holiday->date }}" data-is-active="{{ $holiday->is_active }}"
                        data-color="{{ $holiday->color }}" data-remark="{{ $holiday->remark }}">
                        Edit
                    </button>


                  <form action="{{ route('admin.holiday.destroy', $holiday->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger"
        onclick="return confirm('Are you sure you want to delete this holiday?')">
        Delete
    </button>
</form>

                </td>
                    @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


<div class="modal fade" id="editHolidayModal" tabindex="-1" aria-labelledby="editHolidayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editHolidayModalLabel">Edit Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTaskForm" action="{{route('admin.holiday.edit')}}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_holiday_id">
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Holiday Name</label>
                            <input type="text" name="name" id="edit_holiday_name" class="form-control" required>
                        </div>
                        
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Approved By</label>
                            <input type="text" name="approved_by" id="edit_approved_by" class="form-control" required>
                        </div>







                        <div class="mb-3 col-md-6">
                            <label class="form-label">Date.</label>
                            <input type="date" name="date" id="edit_date" class="form-control">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Is Active</label>
                            <select name="is_active" id="edit_is_active" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 col-md-2">
                        <label class="form-label">Color</label>
                        <input type="color" name="color" id="edit_color" class="form-control">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Holiday Remark</label>
                        <input type="text" name="remark" id="edit_remark" class="form-control">
                    </div>

                    <div class="text-end">
                        <button type="submit" id="UpdateTaskBtn" class="btn btn-success">Update Holiday</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).on("click", ".editHolidayBtn", function() {
        var holidayId = $(this).data("id");
        var holidayName = $(this).data("name");
        var approvedBy = $(this).data("approved-by");
        var holidayDate = $(this).data("date");
        var isActive = $(this).data("is-active");
        var color = $(this).data("color");
        var remark = $(this).data("remark");

        $("#edit_holiday_id").val(holidayId);
        $("#edit_holiday_name").val(holidayName);
        $("#edit_approved_by").val(approvedBy);
        $("#edit_date").val(holidayDate);
        $("#edit_is_active").val(isActive);
        $("#edit_color").val(color);
        $("#edit_remark").val(remark);
    });
</script>


@endsection