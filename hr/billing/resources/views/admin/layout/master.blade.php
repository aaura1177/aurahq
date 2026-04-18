<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Amdin Panel</title>
    <link rel="icon" type="image/png" href="{{asset('logo.png')}}"> <!-- yeh logo favicon ke liye -->
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap");
    </style>

    <link rel="stylesheet" href="{{ asset('admin/css/navbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/default.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/tabledata.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/button.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/input.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/heading.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/extra.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/popup.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/addmeta.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/addform.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/dashboardnew.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
</head>
<style>
    .toster{
        position: fixed;
        width: fit-content;
        height: fit-content;
        padding: 1rem;
        top: 3rem;
        right: 0;
        z-index: 9999;
        border-radius: 10px;
    }
    .toster p {
        color:#fff;
        font-size: 1.1rem;
    }
    .toster span{
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: #fff;
        animation: widthLow 5s forwards;

    }

    @keyframes widthLow{
        0%{
            width: 100%;
        }
        100%{
            width: 0;
        }
    }

</style>
<body>
    <section id="main_container_site">
        @include('admin.layout.header')
        <section id="main_content_section">
            @include('admin.layout.side')

            <section id="data_section" class="data-section-max">


                @yield('content')

            </section>
        </section>
        <script src="{{ asset('admin/js/dataTable.js') }}"></script>
        <script src="{{ asset('admin/js/main.js') }}"></script>
        <script src="{{ asset('admin/js/removePopupsOnClick.js') }}"></script>
        <script>
            setTimeout(function() {
                const toster = document.querySelector('.toster');
                if (toster) {
                    toster.remove();
                }
            }, 5000);
        </script>
</body>

</html>
