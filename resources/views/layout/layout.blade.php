<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Havilah Polymedic Online Appointment Transaction System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('assets/images/logo/logoH.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">

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

    <nav class="nav">
        <div class="flex justify-center items-center">
            <button id="sidebarToggle" class="btn btn-sm btn-square btn-ghost">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    class="inline-block h-5 w-5 stroke-current">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
            <img src="{{ asset('assets/images/logo/logoHV2.png') }}" alt="Havilah Polymedic Logo"
                class="w-[90px] h-auto object-contain">

        </div>

        <div>
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-block btn-sm ">
                    {{ Auth::user()->name }}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
                    <li>
                        <form action="{{ route('personnel.logout') }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-xs w-[175px] bg-red-500 text-white">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <aside class=" sidebar">
        <ul>
            {{-- Admin Links --}}
            @if (Auth::user()->role->value === 'admin')
                {{-- Admin Dashboard Link --}}
                {{-- <li class="list-items">
                    <a href="{{ route('admin.dashboard') }}"
                        class="link-items text-sm {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                        </span>
                        Dashboard
                    </a>
                </li> --}}

                {{-- Patients --}}
                <li class="list-items">
                    <a href="{{ route('admin.manage.account') }}"
                        class="link-items text-sm {{ Request::routeIs('admin.manage.account', 'create.account') ? 'active' : '' }}">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </span>
                        Patients
                    </a>
                </li>

                {{-- Admin Manage Account Link --}}
                <li class="list-items">
                    <a href="{{ route('admin.manage.account') }}"
                        class="link-items text-sm {{ Request::routeIs('admin.manage.account', 'create.account') ? 'active' : '' }}">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                            </svg>
                        </span>
                        Manage Account
                    </a>
                </li>

                {{-- Admin Manage Doctors Link --}}
                <li class="list-items">
                    <a href="{{ route('admin.manage.doctor') }}"
                        class="link-items text-sm {{ Request::routeIs('admin.manage.doctor', 'create.doctor') ? 'active' : '' }}">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                        </span>
                        Manage Doctor
                    </a>
                </li>

                {{-- Manage ADS --}}
                <li class="list-items">
                    <a href="{{ route('admin.manage.ads') }}"
                        class="link-items text-sm {{ Request::routeIs('admin.manage.ads') ? 'active' : '' }}">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                            </svg>
                        </span>
                        Manage ADS
                    </a>
                </li>
            @endif

            {{-- Doctor Links --}}
            @if (Auth::user()->role->value === 'doctor')
                {{-- Doctor Profile Link --}}
                <li class="list-items">
                    <a href="{{ route('doctor.profile') }}"
                        class="link-items text-sm {{ Request::routeIs('doctor.profile') ? 'active' : '' }}">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </span>
                        Profile
                    </a>
                </li>

                {{-- Manage Patient Appointments --}}
                <li class="list-items">
                    <a href="{{ route('doctor.appointment') }}"
                        class="link-items text-sm {{ Request::routeIs('doctor.appointment') ? 'active' : '' }}">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                            </svg>
                        </span>
                        Manage Appointments
                    </a>
                </li>
            @endif

            {{-- Staff Links --}}
            @if (Auth::user()->role->value === 'staff')
                {{-- Staff Dashboard Link --}}
                {{-- <li class="list-items">
                    <a href="{{ route('staff.dashboard') }}"
                        class="link-items text-sm {{ Request::routeIs('staff.dashboard') ? 'active' : '' }}">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                        </span>
                        Dashboard
                    </a>
                </li> --}}

                {{-- Staff Manage Patient Appointments --}}
                <li class="list-items">
                    <a href="{{ route('manage.appointment') }}"
                        class="link-items text-sm {{ Request::routeIs('manage.appointment') ? 'active' : '' }}">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                            </svg>
                        </span>
                        Manage Appointments
                    </a>
                </li>
                {{-- Staff Manage Queue Appointments --}}
                <li class="list-items">
                    <a href="{{ route('staff.queue.index') }}"
                        class="link-items text-sm {{ Request::routeIs('staff.queue.index') ? 'active' : '' }}">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9" />
                            </svg>
                        </span>
                        Manage Queue
                    </a>
                </li>
            @endif
        </ul>
    </aside>

    <main class="main-content bg-gray-100">
        @yield('content')
    </main>


    <script src="{{ asset('assets/js/jquery-3.7.1.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#sidebarToggle').click(function () {
                $('.sidebar').toggleClass('active');
            });

            if (!sessionStorage.getItem("visited")) {
                $("#loader").fadeOut("slow");
                sessionStorage.setItem("visited", "true");
            } else {
                $("#loader").hide();
            }
        });
    </script>
    @yield('script')
</body>

</html>