    @extends('admin.layout.link')

    @section('content')
    <div class="container mt-4">
        <div class="d-block justify-content-between align-items-center mb-2">
            <h3>Eltercity Reading List</h3>
                      <form action="{{ route('admin.eltercity_readings') }}" method="GET">
    <div class="row">
        {{-- Employee Dropdown --}}
      

        {{-- From Date --}}
        <div class="form-group mb-3 col-md-4">
            <label for="from_date" class="form-label">From Date</label>
            <input type="date" name="from_date" id="from_date" class="form-control"
                   value="{{ request('to_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" onclick="this.showPicker()"
                        onfocus="this.showPicker()">
        </div>

        {{-- To Date --}}
       <div class="form-group mb-3 col-md-4">
    <label for="to_date" class="form-label">To Date</label>
    <input type="date" name="to_date" id="to_date" class="form-control"
           value="{{ request('to_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" onclick="this.showPicker()"
                        onfocus="this.showPicker()">
</div>


        {{-- Submit Button --}}
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </div>
</form>
            <a href="{{ route('admin.eltercity_readings.create') }}" class="btn btn-primary mt-2">+ Add Eltercity Reading</a>
        </div>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>SR</th>
                    <th>Time Slot</th>
                    <th>Date</th>
                    <th>Reading</th>
                    <th>Screenshot</th>
                 
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($readings as $key => $reading)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td style="color: {{ $reading->color }}; ">
                        {{ $reading->time_slot }}
                    </td>


                    <!-- <td>{{ $reading->date ?? 'N/A' }}</td> -->
                    <td>{{ \Carbon\Carbon::parse($reading->date)->format('d-m-Y') ?? 'N/A' }}</td>

                    <td>{{ $reading->reading ?? 'N/A' }}</td>
                    <td>
                        @if ($reading->screenshot)
                        <a href="{{ asset('storage/' . $reading->screenshot) }}" target="_blank" class="btn btn-sm btn-info">
                            View
                        </a>
                        @else
                        N/A
                        @endif
                    </td>
                    <!-- <td>{{ $reading->is_active ?? 'N/A' }}</td> -->
                    @php
                    $statusColors = [
                    '1' => 'bg-success text-white',
                    '0' => 'bg-danger text-white',
                    ];
                    @endphp




                    <td>
                        <button class="btn btn-sm btn-warning editreadingBtn" data-bs-toggle="modal"
                            data-bs-target="#editreadingModal" data-id="{{ $reading->id }}"
                            data-time_slot="{{ $reading->time_slot }}"
                            data-reading="{{ $reading->reading }}"
                            data-date="{{ $reading->date }}" data-screenshot="{{ $reading->screenshot }}">
                            Edit
                        </button>


                    @if (Auth::user()->id == 1)
                        <form action="{{ route('admin.eltercity_readings.destroy', $reading->id) }}" method="POST"
      style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this reading?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
</form>

                          @endif
                    </td>
                  
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <!-- Edit Reading Modal -->
    <div class="modal fade" id="editreadingModal" tabindex="-1" aria-labelledby="editreadingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editreadingModalLabel">Edit Reading</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm" action="{{route('admin.eltercity_readings.edit')}}" enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="id" id="edit_reading_id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Time Slot</label>
                                <select name="time_slot" id="edit_time_slot" class="form-select" required>
                                    <option value="">Select Time Slot</option>
                                    <option value="morning">Morning</option>
                                    <option value="evening">Evening</option>
                                </select>
                            </div>


                            <div class="mb-3 col-md-6">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" id="edit_date" class="form-control" required>
                            </div>

                            <div class="mb-3 col-md-12">
                                <label class="form-label">Existing Screenshot</label>
                                <div id="existing_screenshot">No Screenshot</div>
                            </div>
                            <div class="mb-3 col-md-12">
                                <label class="form-label">Reading</label>
                                <input type="text" name="reading" id="edit_reading" class="form-control" required>
                            </div>
                            <div class="mb-3 col-md-12">
                                <label class="form-label"> Screenshot</label>
                                <input type="file" name="screenshot" id="edit_date" class="form-control" >

                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Update Reading</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script>
      $(document).on("click", ".editreadingBtn", function () {
    var readingId = $(this).data("id");
    var timeSlot = $(this).data("time_slot");
    var reading = $(this).data("reading");
    var date = $(this).data("date");
    var screenshot = $(this).data("screenshot");

    $("#edit_reading_id").val(readingId);
    $("#edit_time_slot").val(timeSlot);  // ✅ Corrected
    $("#edit_reading").val(reading);  
    $("#edit_date").val(date);

    if (screenshot) {
        $("#existing_screenshot").html(
            `<a href="/storage/${screenshot}" target="_blank" class="btn btn-sm btn-info">View Screenshot</a>`
        );
    } else {
        $("#existing_screenshot").html("No Screenshot");
    }
});

    </script>


    @endsection