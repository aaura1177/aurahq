@extends('user.layout.link')
@section('content')
    <style>
        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .blink {
            animation: blink .6s infinite;
        }
    </style>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-dark">Add Leaves</h3>
            <a href="{{ route('user.leave') }}" class="btn btn-primary">+ Back</a>
        </div>
    </div>

    <div class="container mt-3">

        <form action="{{ route('create.leave') }}" method="POST">
            @csrf
            <!-- flex-wrap -->
            <!-- <div class="mb-3">
                <label for="leave_type_id" class="form-label">Select Leave Type</label>
                <div class="d-flex flex-wrap">
                    @foreach ($leavetypes as $leavetype)
                        <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="leave_type_id"
                                id="leave_{{ $leavetype->id }}" value="{{ $leavetype->id }}" required>
                            <label class="form-check-label" for="leave_{{ $leavetype->id }}">
                                {{ $leavetype->leave_name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div> -->



            <div class="mb-3">
        <label for="leave_type_id" class="form-label">Select Leave Type</label>
        <div class="d-flex flex-wrap">
            @foreach ($leavetypes as $leavetype)
                <div class="form-check me-3">
                    <input class="form-check-input" type="radio" name="leave_type_id"
                        id="leave_{{ $leavetype->id }}" value="{{ $leavetype->id }}" required>
                    <label class="form-check-label" for="leave_{{ $leavetype->id }}">
                        {{ $leavetype->leave_name }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
        
            <div class="mb-3">
                <label for="start_at" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_at" onclick="this.showPicker()" onfocus="this.showPicker()" name="start_at" required>
            </div>
        
            <div class="mb-3">
                <label for="end_at" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_at" onclick="this.showPicker()" onfocus="this.showPicker()" name="end_at" required>
            </div>
        
            <div class="mb-3">
                <label for="reason" class="form-label">Reason</label>
                <textarea class="form-control" id="reason" name="reason" rows="3" ></textarea>
            </div>
        
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Apply Leave</button>
        </form>
        

    </div>
@endsection
