{{-- resources\views\partials\header.blade.php --}}
<header>
    <div class="logo-container">
        <div class="menu-toggle">
            <span>&#9776;</span>
        </div>
        <img src="{{ asset('assets/images/logo/logoHV2.png') }}" alt="Havilah Polymedic Logo">
    </div>

    <div class="links-container text-sm">
        <a href="{{ route('home') }}" class="{{ Request::routeIs('home') ? 'active' : ''}}">Home</a>
        <a href="{{ route('home.doctor') }}"
            class="{{ Request::routeIs('home.doctor', 'book.doctor') ? 'active' : '' }}">Doctors</a>
        <a href="#">AboutUs</a>
        <a href="#">Contact</a>
    </div>


    @auth
        @if (Auth::user()->role->value)
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
                            <a class="justify-between">
                                Profile
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('patient.appointments') }}"
                                class="{{ Request::routeIs('patient.appointments') ? 'active' : '' }}">My Appointments</a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-xs w-[175px] bg-red-500 text-white">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        @else
            <div class="button-container">
                <a href="{{ route('login') }}" class="btn btn-sm px-10 btn-primary text-white font-bold">Login</a>
            </div>
        @endif
    @else
        <div class="button-container">
            <a href="{{ route('login') }}" class="btn btn-sm px-10 btn-primary text-white font-bold">Login</a>
        </div>
    @endauth
</header>