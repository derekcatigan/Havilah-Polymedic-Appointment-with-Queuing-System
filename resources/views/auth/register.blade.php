{{-- resources\views\auth\register.blade.php --}}
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
            <a href="{{ route('home.doctor') }}" class="{{ Request::routeIs('home.doctor') ? 'active' : '' }}">Doctors</a>
            <a href="#">AboutUs</a>
            <a href="#">Contact</a>
        </div>

        <div class="button-container">
            <a href="{{ route('login') }}" class="btn btn-sm px-10 btn-primary text-white font-bold">Login</a>
        </div>
    </header>

    <main>
        <section class="flex justify-center items-center">
            <div class="bg-white border border-gray-200 rounded shadow-xl p-10 w-[500px]">
                <div class="flex items-center gap-3 mb-5">
                    <img src="{{ asset('assets/images/logo/logoH.png') }}" alt="Havilah Polymedic Logo"
                        class="w-[35px] h-auto object-contain">
                    <div>
                        <h3 class="text-lg font-semibold">Sign up here</h3>
                        <p class="label text-xs">Please sign up here to book appointments.</p>
                    </div>
                </div>
                <form id="registerForm" autocomplete="off">
                    @csrf
                    <div class="w-full">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Full name</legend>
                            <input type="text" id="name" name="name" class="w-full input input-sm" />
                        </fieldset>
                    </div>
                    <div class="w-full">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Email</legend>
                            <input type="email" id="email" name="email" class="w-full input input-sm"
                                placeholder="e.g johndoe@gmail.com" />
                        </fieldset>
                    </div>
                    <div class="w-full">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Contact number</legend>
                            <input type="tel" id="phone" name="phone" class="w-full tabular-nums input input-sm"
                                pattern="[0-9]*" minlength="11" maxlength="11" title="Must be 11 digits" />
                        </fieldset>
                    </div>
                    <div class="w-full">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Address</legend>
                            <input type="text" id="address" name="address" class="w-full input input-sm" />
                            <p class="label text-xs">(street name (optional), barangay, muncipality, province)</p>
                        </fieldset>
                    </div>
                    <div class="w-full">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Password</legend>
                            <input type="password" id="password" name="password" class="w-full input input-sm"
                                placeholder="password" />
                        </fieldset>
                    </div>
                    <div class="w-full">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Confirm Password</legend>
                            <input type="password" name="password_confirmation" class="w-full input input-sm"
                                placeholder="confirm password" />
                        </fieldset>
                        <div class="w-full flex items-center">
                            <div class="flex items-center gap-1">
                                <input type="checkbox" id="checkPass" class="checkbox checkbox-sm" />
                                <label for="checkPass" class="label text-sm">Show password</label>
                            </div>
                        </div>
                    </div>

                    <div class="w-full mt-5">
                        <button type="submit" id="registerBtn" class="btn btn-block btn-primary text-white">
                            <span id="registerText">Register</span>
                            <span id="registerSpinner" class="loading loading-dots loading-sm hidden"></span>
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

                let checkPass = $(this).is(':checked');

                if (checkPass) {
                    $('#password, [name="password_confirmation"]').attr('type', 'text');
                } else {
                    $('#password, [name="password_confirmation"]').attr('type', 'password');
                }
            });

            $('#registerForm').on('submit', function (e) {
                e.preventDefault();

                let formData = $(this).serialize();
                let $registerBtn = $('#registerBtn');
                let $registerText = $('#registerText');
                let $registerSpinner = $('#registerSpinner');

                $registerBtn.prop('disabled', true);
                $registerText.addClass('hidden');
                $registerSpinner.removeClass('hidden');

                $.ajax({
                    method: 'POST',
                    url: '/register/patient',
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
                        window.location.href = "/register";
                    },
                    error: function (xhr) {
                        let error = "Something went wrong. Please try again later.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            error = xhr.responseJSON.message
                        }
                        $.toast({
                            heading: "Something went wrong.",
                            icon: "error",
                            text: error,
                            showHideTransition: 'slide',
                            stack: 3,
                            position: 'top-right',
                        });
                    },
                    complete: function () {
                        $registerBtn.prop('disabled', false);
                        $registerText.removeClass('hidden');
                        $registerSpinner.addClass('hidden');
                    }
                });
            });
        });
    </script>
@endsection