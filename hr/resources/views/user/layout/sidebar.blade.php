<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">


        <li class="nav-item">
            <a class="nav-link " href="{{ route('user.dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="{{ route('user.attendance') }}">
                <i class="bi bi-grid"></i>
                <span>Attendance</span>
            </a>
        </li>


        <!-- End Dashboard Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('user.task') }}">
                <i class="bi bi-menu-button-wide"></i><span>Tasks</span>
            </a>

        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('user.leave') }}">
                <i class="bi bi-journal-text"></i><span>Leaves</span>
            </a>

        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('user.holiday') }}">
                <i class="bi bi-journal-text"></i><span>Holiday</span>
            </a>

        </li>
       @if(Auth::guard('employee')->user()->department_id == 1 &&
    Auth::guard('employee')->user()->salary !== null &&
    Auth::guard('employee')->user()->salary != 0)
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('user.salary') }}">
            <i class="bi bi-journal-text"></i><span>Salary</span>
        </a>
    </li>
@endif

        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('user.work.form.home') }}">
                <i class="bi bi-journal-text"></i><span>Work form Home</span>
            </a>

        </li>
         @if(Auth::guard('employee')->user()->department_id == 1 &&
    Auth::guard('employee')->user()->salary !== null &&
    Auth::guard('employee')->user()->salary != 0)
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('user.internship') }}">
            <i class="bi bi-journal-text"></i><span>Internship Tap-in</span>
        </a>
    </li>
@endif


        <li class="nav-item">
            @php
                $employeeId = Auth::guard('employee')->user()->id ?? null;
            @endphp
          

                <div class="mobile">


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


                                <button type="submit" id="checkInBtn"
                                    class="btn btn-success fade-effect">Check-In</button>
                            </form>

                        </div>
                    @endif

                </div>
          

        <li>

            <style>
                .mobile {
                    display: none;
                }

                @media (max-width: 500px) {
                    .mobile {
                        display: block;
                    }
                }
            </style>




            <!--
         <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-layout-text-window-reverse"></i><span>Tables</span><i
                    class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="tables-general.html">
                        <i class="bi bi-record-circle"></i></i><span>General Tables</span>
                    </a>
                </li>
                <li>
                    <a href="tables-data.html">
                        <i class="bi bi-record-circle"></i></i><span>Data Tables</span>
                    </a>
                </li>
            </ul>
        </li> -->
            <!-- End Tables Nav -->

            {{-- <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-bar-chart"></i><span>Charts</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="charts-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="charts-chartjs.html">
                        <i class="bi bi-record-circle"></i></i><span>Chart.js</span>
                    </a>
                </li>
                <li>
                    <a href="charts-apexcharts.html">
                        <i class="bi bi-record-circle"></i></i><span>ApexCharts</span>
                    </a>
                </li>
                <li>
                    <a href="charts-echarts.html">
                        <i class="bi bi-record-circle"></i></i><span>ECharts</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Charts Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#icons-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-gem"></i><span>Icons</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="icons-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="icons-bootstrap.html">
                        <i class="bi bi-record-circle"></i></i><span>Bootstrap Icons</span>
                    </a>
                </li>
                <li>
                    <a href="icons-remix.html">
                        <i class="bi bi-record-circle"></i></i><span>Remix Icons</span>
                    </a>
                </li>
                <li>
                    <a href="icons-boxicons.html">
                        <i class="bi bi-record-circle"></i></i><span>Boxicons</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Icons Nav -->

        <li class="nav-heading">Pages</li> --}}

            {{-- <li class="nav-item">
            <a class="nav-link collapsed" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>Profile</span>
            </a>
        </li><!-- End Profile Page Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span>F.A.Q</span>
            </a>
        </li><!-- End F.A.Q Page Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="pages-contact.html">
                <i class="bi bi-envelope"></i>
                <span>Contact</span>
            </a>
        </li><!-- End Contact Page Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="pages-register.html">
                <i class="bi bi-card-list"></i>
                <span>Register</span>
            </a>
        </li><!-- End Register Page Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="pages-login.html">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Login</span>
            </a>
        </li><!-- End Login Page Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="pages-error-404.html">
                <i class="bi bi-dash-circle"></i>
                <span>Error 404</span>
            </a>
        </li><!-- End Error 404 Page Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="pages-blank.html">
                <i class="bi bi-file-earmark"></i>
                <span>Blank</span>
            </a>
        </li><!-- End Blank Page Nav --> --}}

    </ul>

</aside><!-- End Sidebar-->
