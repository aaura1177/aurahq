<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Dashboard - Aurateria </title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('user/img/hrmsaura.jpg') }}" rel="icon">
    <link href="{{ asset('user/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <!-- Font Awesome 6 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('user/vendor/bootstrap/css/bootstrap.min.css') }}?v={{ time() }}i" rel="stylesheet">
    <link href="{{ asset('user/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('user/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('user/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('user/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('user/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('user/vendor/simple-datatables/style.css') }}" rel="stylesheet">
    <link href="{{ asset('user/css/toastr.min.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('user/css/style.css') }}?v={{ time() }}" rel="stylesheet">



</head>

<body>

    <script src="{{ asset('user/js/jquery.min.js') }}"></script>
    <script src="{{ asset('user/js/toastr.min.js') }}"></script>
    {{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                console.log("Location retrieved:");
                console.log("Latitude:", position.coords.latitude);
                console.log("Longitude:", position.coords.longitude);

                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
            }, function (error) {
                alert('❌ Location access denied or unavailable!');
                console.error("Geolocation error:", error.message);
            });
        } else {
            alert("❌ Geolocation is not supported by this browser.");
        }
    });
</script> --}}

<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                console.log("Location retrieved:");
                console.log("Latitude:", position.coords.latitude);
                console.log("Longitude:", position.coords.longitude);

                // Fill all latitude inputs
                document.querySelectorAll('.latitude').forEach(function (el) {
                    el.value = position.coords.latitude;
                });

                // Fill all longitude inputs
                document.querySelectorAll('.longitude').forEach(function (el) {
                    el.value = position.coords.longitude;
                });

            }, function (error) {
                alert('❌ Location access denied or unavailable!');
                console.error("Geolocation error:", error.message);
            });
        } else {
            alert("❌ Geolocation is not supported by this browser.");
        }
    });
</script>


    <script>
        $(document).ready(function() {
            @if (session()->has('success'))


                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "timeOut": 10000,
                    "extendedTimeOut": 10000
                };
                toastr.success("{{ session('success') }}");




                // toastr.success("{{ session('success') }}");
            @endif

            @if (session()->has('error'))
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "timeOut": 10000,
                    "extendedTimeOut": 10000
                };
                toastr.error("{{ session('error') }}");
            @endif


        });
    </script>
    @if ($errors->any())
        <script>
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "timeOut": 12000,
                "extendedTimeOut": 12000
            };

            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        </script>
    @endif

    @include('user.layout.header')

    @include('user.layout.sidebar')
    <main id="main" class="main">


        @yield('content')

    </main>

    <style>
        .toast-success {
            background-color: red !important;
            color: white !important;
        }
    </style>


    @include('user.layout.footer')
