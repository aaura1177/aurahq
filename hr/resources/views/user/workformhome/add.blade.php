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
            <h3 class="text-dark">Add Work Form Home</h3>
            <a href="{{ route('user.work.form.home') }}" class="btn btn-primary">+ Back</a>
        </div>
    </div>

    <div class="container mt-3">
<form action="{{ route('user.work.formhome.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="work_date" class="form-label">Date</label>
        <input type="date" class="form-control" id="work_date" onclick="this.showPicker()" onfocus="this.showPicker()" name="work_date"  required>
    </div>

    <div class="mb-3">
        <label for="start_time" class="form-label">Start Time</label>
        <input type="time" class="form-control" id="start_time" onclick="this.showPicker()" onfocus="this.showPicker()" name="start_time"  required>
    </div>

    <div class="mb-3">
        <label for="end_time" class="form-label">End Time</label>
        <input type="time" class="form-control" id="end_time" onclick="this.showPicker()" onfocus="this.showPicker()" name="end_time" required>
    </div>

    <div class="mb-3">
        <label for="reason" class="form-label">Reason</label>
        <textarea class="form-control" id="reason" name="reason" rows="3" ></textarea>
    </div>

    <div class="mb-3">
        <label for="location" class="form-label">Location</label>
        <textarea class="form-control" id="location" name="location" rows="3" ></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Submit Work From Home</button>
</form>

        

    </div>

 {{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        const timeInput = document.getElementById("start_time");

        // Get current time in HH:MM format
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const currentTime = `${hours}:${minutes}`;

        // Set current time as value
        timeInput.value = currentTime;

        // Make input readonly (user can't change it)
        timeInput.readOnly = true;
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById("work_date").value = today;
    });
</script> --}}
@endsection
