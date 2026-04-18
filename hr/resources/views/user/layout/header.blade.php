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
        animation: blink 1s infinite;
    }

    .fade-effect {
        transition: opacity 0.5s ease-in-out, background-color 0.5s ease-in-out;
    }

    .disabled {
        opacity: 0.5;
        pointer-events: none;
    }
</style>



<header id="header" class="header fixed-top d-flex align-items-center  {{ $urgenttask ? 'bg-danger' : '' }}">
    <div class="d-flex align-items-center justify-content-between">
        <a href="{{ route('user.dashboard') }}" class="logo d-flex align-items-center">
            <img src="{{ asset('admin/img/hrmsaura.jpg') }}" alt="">
            <span class="d-none d-lg-block">Aurateria</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <div class="search-bar text-start">
        <div class="row">


            @if (!empty($lastIncomplete))
                <div class="col-md-6">
                    <form id="checkoutForm" action="{{ route('user.check-out') }}" method="POST">
                        @csrf
                        <input type="hidden" name="attendance_id" value="{{ $lastIncomplete->id }}">
                        <button type="submit" class="btn btn-danger fade-effect"
                            onclick="return confirm('Are you sure you want to check out?')"
                            id="checkoutBtn">Check-Out</button>
                    </form>
                </div>
            @elseif ($checkinCount >= 3)
                <div class="col-md-12">
                    <p class="text-danger">You have already checked in 3 times today.</p>
                </div>
            @else
                <div class="col-md-6">
                    <form id="checkInForm" action="{{ route('user.check-in') }}" method="POST">
                        @csrf

                        <input type="hidden" class="latitude" name="latitude" placeholder="Latitude">
                        <input type="hidden" class="longitude" name="longitude" placeholder="Longitude">



                        <button type="submit" id="checkInBtn" class="btn btn-success fade-effect">Check-In</button>
                    </form>
                </div>
            @endif




        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">

            <li class="nav-item d-block d-lg-none">
                <a class="nav-link nav-icon search-bar-toggle " href="#">
                    <i class="bi bi-search"></i>
                </a>
            </li><!-- End Search Icon-->

            <li class="nav-item dropdown">


                <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-bell {{ $count > 5 ? 'blink text-white' : '' }}"></i>
                    <span class="badge bg-primary badge-number {{ $count > 1 ? 'blink bg-danger' : '' }}"
                        id="notification-badge">
                        {{ $count }}
                    </span>
                </a>


                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                    <li class="dropdown-header">
                        You have {{ $count }} new notifications
                        <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    @foreach ($employee_notifications as $notification)
                        @php

                            $messageTemplate = $notiMessages[$notification->title] ?? $notification->message;
                            $message = str_replace('{RESPONSE}', 'approved', $messageTemplate);
                            $link = $notiLinks[$notification->title] ?? '#';
                        @endphp

                        <li class="notification-item">
                            <i class="bi bi-exclamation-circle text-warning"></i>
                            <div>
                                <h4>{{ ucfirst($notification->title) }}</h4>
                                <p>{{ $message }}</p>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                                <a href="{{ $link }}" class="btn btn-sm btn-primary">View</a>
                            </div>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                    @endforeach

                    @if ($count == 0)
                        <li class="dropdown-header text-center">No new notifications</li>
                    @endif

                    <li class="dropdown-footer">
                        <a href="#">Show all notifications</a>
                    </li>
                </ul>
                <!-- End Notification Dropdown Items -->

            </li><!-- End Notification Nav -->

            <li class="nav-item dropdown">

                <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-chat-left-text"></i>
                    <span class="badge bg-success badge-number">3</span>
                </a><!-- End Messages Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
                    <li class="dropdown-header">
                        You have 3 new messages
                        <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li class="message-item">
                        <a href="#">
                            <img src="{{ asset('admin/img/messages-1.jpg') }}" alt="" class="rounded-circle">
                            <div>
                                <h4>Maria Hudson</h4>
                                <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                <p>4 hrs. ago</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li class="message-item">
                        <a href="#">
                            <img src="{{ asset('admin/img/messages-2.jpg') }}" alt="" class="rounded-circle">
                            <div>
                                <h4>Anna Nelson</h4>
                                <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                <p>6 hrs. ago</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li class="message-item">
                        <a href="#">
                            <img src="{{ asset('admin/img/messages-3.jpg') }}" alt="" class="rounded-circle">
                            <div>
                                <h4>David Muldon</h4>
                                <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                <p>8 hrs. ago</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li class="dropdown-footer">
                        <a href="#">Show all messages</a>
                    </li>

                </ul><!-- End Messages Dropdown Items -->

            </li><!-- End Messages Nav -->

            <li class="nav-item dropdown pe-3">

                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#"
                    data-bs-toggle="dropdown">
                    <img src="{{ auth('employee')->user() && auth('employee')->user()->image ? asset('storage/' . auth('employee')->user()->image) : asset('admin/img/profile-img.jpg') }}"
                        alt="Profile" class="rounded-circle">
                    <span
                        class="d-none d-md-block dropdown-toggle ps-2">{{ auth('employee')->user()->name ?? 'null' }}</span>
                </a><!-- End Profile Iamge Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6>{{ auth('employee')->user()->name ?? 'null' }}</h6>

                        <span>{{ auth('employee')->user()->position ?? 'null' }}</span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('user.profile') }}">
                            <i class="bi bi-person"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>


                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('user.logout') }}">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Sign Out</span>
                        </a>
                    </li>

                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->

        </ul>
    </nav><!-- End Icons Navigation -->

    <script>
        // Display Current Date (22 - Monday - Year)
        function displayDate() {
            const date = new Date();
            const day = date.getDate(); // Get day of the month
            const dayName = date.toLocaleString('en-us', {
                weekday: 'long'
            }); // Get full weekday name
            const year = date.getFullYear(); // Get year

            const formattedDate = `${day} - ${dayName} - ${year}`;
            document.getElementById('currentDate').textContent = formattedDate;
        }

        if ()

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
                document.getElementById('checkInBtn').disabled =
                    true; // Disable the Check-In button after checking in
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
            console.log(`${formatTime(minutes)}:${formatTime(remainingSeconds)}`);

            document.getElementById('timeDisplay').textContent = `${formatTime(minutes)}:${formatTime(remainingSeconds)}`;
        }

        // Format Time for Display (e.g., 5 minutes should display "05")
        function formatTime(time) {
            return time < 10 ? `0${time}` : time;
        }
    </script>







</header>
