@extends('admin.layout.link')
@section('content')


<div class="pagetitle">
  <h1>Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard')}}">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row">

    <!-- Left side columns -->
    <div class="col-lg-12">
      <div class="row">

        <!-- Sales Card -->

        <!-- Revenue Card -->
        <div class="col-xxl-4 col-md-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Present <span>| Today</span></h5>

              <table class="table table-striped">

                <tbody>

                  <!-- Loop through attendanceFrontend -->
                  @foreach($Persent as $Present)
                  @if($Present->status == 'Present') 
                  <tr>
                    <td>{{ $Present->name }}</td>

                    @php
                    $statusColors = [
                    'Present' => 'bg-success text-white',
                    'Absent' => 'bg-danger text-white',
                    'Half-Day' => 'bg-warning text-dark',
                    'Leave' => 'bg-info text-white',
                    ];
                    @endphp

                    <td>
                      <span class="badge rounded-pill {{ $statusColors[$Present->status] ?? 'bg-secondary text-white' }}">
                        {{ ucfirst($Present->status) }} <!-- Capitalizing the first letter of the status -->
                      </span>
                    </td>
                  </tr>
                  @endif
                  @endforeach

                </tbody>
              </table>

            </div>
          </div>
        </div>


        <div class="col-xxl-4 col-md-6">
          <div class="card info-card sales-card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>
                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>

            <div class="card-body">
              <h5 class="card-title">Absent <span>| Today</span></h5>

              <!-- Start of Table -->
              <table class="table table-striped">

                <tbody>

                  <!-- Loop through attendanceFrontend -->
                  @foreach($absent as $absent)
                  @if($absent->status == 'Absent') 
                  <tr>
                    <td>{{ $absent->name }}</td>

                    @php
                    $statusColors = [
                    'Present' => 'bg-success text-white',
                    'Absent' => 'bg-danger text-white',
                    'Half-Day' => 'bg-warning text-dark',
                    'Leave' => 'bg-info text-white',
                    ];
                    @endphp

                    <td>
                      <span class="badge rounded-pill {{ $statusColors[$absent->status] ?? 'bg-secondary text-white' }}">
                        {{ ucfirst($absent->status) }} <!-- Capitalizing the first letter of the status -->
                      </span>
                    </td>
                  </tr>
                  @endif
                  @endforeach


                </tbody>
              </table>
              <!-- End of Table -->
            </div>
          </div>
        </div><!-- End Sales Card -->







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




        <!-- Customers Card -->
        <div class="col-xxl-4 col-xl-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Leave Requests</h5>

              <table class="table table-striped">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Start date</th>
                    <th scope="col">End Date</th>
                    <th scope="col">Status</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                  $i = 1;
                  @endphp

                  @foreach($leaves as $leaves)
                  <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $leaves->employee->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($leaves->start_at)->format('d-M') }}</td>
                    <td>{{ \Carbon\Carbon::parse($leaves->end_at)->format('d-M') }}</td>
                    @php

                    // Define the status colors based on the leave status
                    $statusColors = [
                    'approved' => 'bg-success text-white',
                    'rejected' => 'bg-danger text-white',
                    'pending' => 'bg-warning text-dark', // Added bg-warning for pending status
                    ];
                    @endphp

                    <td>
                      <span class="badge rounded-pill {{ $statusColors[$leaves->status] ?? 'bg-secondary text-white' }}">
                        {{ ucfirst($leaves->status) }} <!-- Capitalizing the first letter of the status -->
                      </span>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- End Customers Card -->

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





      </div>
    </div><!-- End Left side columns -->


  </div>
</section>

@endsection