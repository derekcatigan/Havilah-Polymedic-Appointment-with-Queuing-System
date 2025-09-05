<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Havilah Polymedic Appointment Queuing System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('assets/images/logo/logoH.png') }}">

    {{-- Poppins Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    @yield('head')
</head>

<body>
    <div id="loader" class="fixed inset-0 flex items-center justify-center bg-white z-[9999]">
        <div class="w-12 h-12 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
    </div>
    @yield('content')


    <script src="{{ asset('assets/js/jquery-3.7.1.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(window).on("load", function () {
                $("#loader").fadeOut("slow");
            });
        });
    </script>
    @yield('script')
</body>

</html>