@extends('user.layout.link')
@section('content')
<div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row">

        <div class="col-lg-12">
            <div class="row">
                <!-- Sales Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card sales-card">

                        <div class="card-body">
                            <h5 class="card-title">Date <span>| Today</span></h5>

                            <!-- Display the Date -->
                            <p id="currentDate" class="mb-3"></p> <!-- The date will be displayed here -->



                            <!-- Stopwatch Section -->

                        </div>

                    </div>
                </div>




                <!-- Revenue Card -->
                <div class="col-xxl-4 col-md-6  col-12 ">
                    <div class="card info-card revenue-card">



                        <div class="card-body">
                            <h5 class="card-title">Attendance Total <span>| This Month</span></h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{$attendanceCount}}</h6>


                                </div>
                            </div>
                        </div>

                    </div>
                </div><!-- End Revenue Card -->
                <div class="col-xxl-4 col-md-6  col-12 ">
                    <div class="card info-card revenue-card">



                        <div class="card-body">
                            <h5 class="card-title">Paid Leave Total <span>| This Month</span></h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{auth('employee')->user()->monthly_leave ?? 'null' }}</h6>


                                </div>
                            </div>
                        </div>

                    </div>
                </div><!-- End Revenue Card -->

   <div class="col-xxl-4 col-xl-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Holiday</h5>

              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Holiday Name</th>
                    <th>Date</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>

                  @php
                  $i=1;
                  @endphp
                  @foreach($holidays as $holiday)
                  <tr>
                    <td>{{$i++}}</td>
                    <td>{{$holiday->name}}</td>
                    <td>{{ \Carbon\Carbon::parse($holiday->date)->format('d-M') }}</td>

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
                  </tr>

                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>

                <div class="col-xxl-4 col-xl-12">
                    <div class="card info-card customers-card">



                        <div class="card-body">
                            <h5 class="card-title">Tasks <span>| Today</span></h5>

                            <!-- Table -->
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">SR.NO</th>
                                        <th scope="col">Task Name</th>
                                        <th scope="col">Project</th>
                                        <th scope="col">Employee Name</th>
                                        <th scope="col">Priority</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 1;
                                    @endphp
                                    @foreach ($tasks as $key => $task)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><a href="{{ route('user.task') }}" style="text-decoration: none; color: black;">{{ $task->name ?? 'N/A' }}</a></td>
                                        <td><a href="{{ route('user.task') }}" style="text-decoration: none; color: black;">{{ $task->project->name ?? 'N/A' }}</a></td>
                                        <td><a href="{{ route('user.task') }}" style="text-decoration: none; color: black;">{{ ($task->employee->name ?? 'N/A') . ' / ' . ($task->employee->emp_id ?? 'N/A') }}</a></td>
                                        <td>
                                            <a href="{{ route('user.task') }}" style="text-decoration: none; color: black;">
                                                <span class="badge rounded-pill 
                @if ($task->priority == 'Low') bg-info text-dark
                @elseif($task->priority == 'Medium') bg-warning text-dark
                @elseif($task->priority == 'High') bg-danger text-white @endif">
                                                    {{ $task->priority }}
                                                </span>
                                            </a>
                                        </td>
                                    </tr>

                                    @endforeach


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                 <div class="col-xxl-4 col-xl-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Brithday</h5>

              <table class="table table-striped">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Employee Name</th>
                    <th scope="col">date of Brith</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                  $i=1;
                  @endphp
                  @foreach($birthdays as $birthday)
                  <tr>
                    <td>{{$i++ }}</td>
                    <td>{{$birthday->name}}</td>
                    <td>{{ \Carbon\Carbon::parse($birthday->date_of_birth)->format('d-F') }}</td>
                  </tr>

                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>

                <div class="col-xxl-4 col-xl-12">
                    <div class="card info-card customers-card">



                        <div class="card-body">
                            <h5 class="card-title">Attendance <span>| This Month</span></h5>

                            <!-- Table -->
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">SR.NO</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Check In</th>
                                        <th scope="col">Check Out</th>
                                        <th scope="col">Working Hours</th>
                                        <th scope="col">Earned Salary</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 1;
                                    @endphp
                                    @foreach ($attendance as $attendance)

                                    @php
                                    $statusColors = [
                                    'Present' => 'bg-success text-white',
                                    'Absent' => 'bg-danger text-white',
                                    'Half-Day' => 'bg-warning text-dark',
                                    'Leave' => 'bg-info text-white',
                                    ];

                                    // Get the appropriate status color, defaulting to 'bg-secondary' if status is unknown
                                    $statusColorClass = $statusColors[$attendance->status] ?? 'bg-secondary text-white';
                                    @endphp

                                    <tr>
                                        <td>{{ $i++ }}@if($attendance->work_from)<i class="fa-solid fa-house"></i>@endif</td>
                                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d-m-Y') }}</td>
                                        <td>{{ $attendance->check_in_time }}</td>
                                        <td>{{ $attendance->check_out_time }}</td>
                                        <td>{{ $attendance->working_hours }}</td>
                                        <td>{{ $attendance->earned_salary }}</td>
                                        <td>
                                            <span class="badge rounded-pill {{ $statusColorClass }}">
                                                {{ ucfirst($attendance->status ?? 'Unknown') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>







            </div>
        </div><!-- End Left side columns -->


    </div>
</section>

<script>
    // Display Current Date (22 - Monday - Year)
    function displayDate() {
        const date = new Date();
        const day = date.getDate(); // Day of the month
        const monthName = date.toLocaleString('en-us', {
            month: 'long'
        }); // Full month name
        const year = date.getFullYear(); // Year

        const formattedDate = `${day} - ${monthName} - ${year}`;
        document.getElementById('currentDate').textContent = formattedDate;
    }


    displayDate();

    // Stopwatch Functionality
    let timer;
    let isRunning = false;
    let seconds = 0;


    // Start the Stopwatch when Check-In is clicked
    document.getElementById('checkInBtn').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent form submission for check-in

        // Start the stopwatch
        if (!isRunning) {
            timer = setInterval(updateTime, 1000);
            document.getElementById('checkInBtn').textContent = 'Checked-In';
            document.getElementById('checkInBtn').disabled = true; // Disable the Check-In button after checking in
        }


        // Submit the form after starting the stopwatch
        document.getElementById('checkInForm').submit();
    });

    // Stop the Stopwatch when Check-Out is clicked
    document.getElementById('checkoutBtn').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent form submission for check-out

        // Stop the stopwatch
        clearInterval(timer);
        document.getElementById('checkInBtn').disabled = false; // Enable the Check-In button again
        document.getElementById('checkInBtn').textContent = 'Check-In'; // Reset text on Check-In button

        // Submit the form after stopping the stopwatch



        document.getElementById('checkoutForm').submit();
    });

    // Update Time Function for Stopwatch
    function updateTime() {
        seconds++;
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        document.getElementById('timeDisplay').textContent = `${formatTime(minutes)}:${formatTime(remainingSeconds)}`;
    }

    // Format Time for Display (e.g., 5 minutes should display "05")
    function formatTime(time) {
        return time < 10 ? `0${time}` : time;
    }
</script>
@endsection