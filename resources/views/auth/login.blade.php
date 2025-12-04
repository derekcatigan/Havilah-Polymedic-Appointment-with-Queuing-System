{{-- resources\views\auth\login.blade.php --}}
@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('assets/css/home-style.css') }}">
@endsection

@section('content')
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
                class="{{ Request::routeIs('home.doctor', 'book.doctor') ? 'active' : '' }}">Book</a>
            <a href="{{ route('home.queue') }}" class="{{ Request::routeIs('home.queue') ? 'active' : '' }}">Queue</a>
            <a href="{{ route('patient.appointments') }}"
                class="{{ Request::routeIs('patient.appointments') ? 'active' : '' }}">My Appointments</a>
            <a href="#">AboutUs</a>
            <a href="#">Contact</a>
        </div>

        <div class="button-container">
            <a href="{{ route('register') }}" class="btn btn-sm px-10 btn-primary text-white font-bold">Create Account</a>
        </div>
    </header>

    <main>
        <section class="flex justify-center items-center h-screen">
            <div class="bg-white border border-gray-200 rounded shadow-xl p-10 w-[500px]">
                <div class="flex items-center gap-3 mb-5">
                    <img src="{{ asset('assets/images/logo/logoH.png') }}" alt="Havilah Polymedic Logo"
                        class="w-[35px] h-auto object-contain">
                    <div>
                        <h3 class="text-lg font-semibold">Login here</h3>
                        <p class="label text-xs">Please log in to book appointments.</p>
                    </div>
                </div>
                <form id="authenticateForm" autocomplete="off">
                    @csrf
                    <div class="w-full">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Email</legend>
                            <input type="email" id="email" name="email" class="w-full input"
                                placeholder="e.g johndoe@gmail.com" />
                        </fieldset>
                    </div>
                    <div class="w-full">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Password</legend>
                            <input type="password" id="password" name="password" class="w-full input"
                                placeholder="password" />
                        </fieldset>
                        <div class="w-full flex justify-between items-center">
                            <div class="flex items-center gap-1">
                                <input type="checkbox" id="checkPass" class="checkbox checkbox-sm" />
                                <label for="checkPass" class="label text-sm">Show password</label>
                            </div>

                            <div>
                                <a href="{{ route('password.request') }}" class="text-sm text-blue-400">
                                    Forget password?
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="w-full mt-5">
                        <button type="submit" id="loginBtn" class="btn btn-block btn-primary text-white">
                            <span id="loginText">Login</span>
                            <span id="loginSpinner" class="loading loading-dots loading-sm hidden"></span>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $(".menu-toggle").click(function () {
                $(".links-container").toggleClass("active");
            });

            $('#checkPass').change(function (e) {
                e.preventDefault();
                $('#password').attr('type', $(this).is(':checked') ? 'text' : 'password');
            });

            $('#authenticateForm').on('submit', function (e) {
                e.preventDefault();

                let formData = $(this).serialize();
                let $loginBtn = $('#loginBtn');
                let $loginText = $('#loginText');
                let $loginSpinner = $('#loginSpinner');

                $loginBtn.prop('disabled', true);
                $loginText.addClass('hidden');
                $loginSpinner.removeClass('hidden');

                $.ajax({
                    type: "POST",
                    url: "/authenticate",
                    data: formData,
                    success: function (response) {
                        $.toast({
                            heading: 'Success',
                            icon: 'success',
                            text: response.message,
                            showHideTransition: 'slide',
                            stack: 3,
                            position: 'top-right',
                        })

                        if (response.redirect) {
                            window.location.href = response.redirect
                        }
                    },
                    error: function (xhr) {
                        let errorMsg = "Something went wrong!";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        $.toast({
                            heading: "Login Failed",
                            icon: "error",
                            text: errorMsg,
                            showHideTransition: 'slide',
                            stack: 3,
                            position: 'top-right',
                        });
                    },
                    complete: function () {
                        $loginBtn.prop('disabled', false);
                        $loginText.removeClass('hidden');
                        $loginSpinner.addClass('hidden');
                    }
                });
            });
        });
    </script>
@endsection