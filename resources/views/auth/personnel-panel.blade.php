{{-- resources\views\auth\admin-panel.blade.php --}}
@extends('layout.app')

@section('title', 'Havliah Polymedic Panel')

@section('content')
    <div class="h-screen flex justify-center items-center">
        <div class="border border-gray-200 p-10 w-[500px] rounded shadow-lg">
            <div class="flex items-center gap-3 mb-5">
                <img src="{{ asset('assets/images/logo/logoH.png') }}" alt="Havilah Polymedic Logo"
                    class="w-[35px] h-auto object-contain">
                <div>
                    <h3 class="text-lg font-semibold">Login personnel here</h3>
                    <p class="label text-xs">Authorized personnel only.</p>
                </div>
            </div>
            <form id="personnelForm" autocomplete="off">
                @csrf
                <div class="w-full">
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Email</legend>
                        <input type="email" id="email" name="email" class="w-full input"
                            placeholder="e.g. Johndoe@gmail.com" />
                    </fieldset>
                </div>
                <div class="w-full">
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Password</legend>
                        <input type="password" id="password" name="password" class="w-full input" placeholder="password" />
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
                    </fieldset>
                </div>

                <div class="w-full mt-5">
                    <button type="submit" id="loginBtn" class="btn btn-block btn-primary text-white">
                        <span id="loginText">Login</span>
                        <span id="loginSpinner" class="loading loading-dots loading-sm hidden"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('#checkPass').change(function (e) {
                e.preventDefault();

                $('#password').attr('type', $(this).is(':checked') ? 'text' : 'password');
            });

            $('#personnelForm').on('submit', function (e) {
                e.preventDefault();

                let formData = $(this).serialize();
                let $loginBtn = $('#loginBtn');
                let $loginText = $('#loginText');
                let $loginSpinner = $('#loginSpinner');

                $loginBtn.prop('disabled', true);
                $loginText.addClass('hidden');
                $loginSpinner.removeClass('hidden');

                $.ajax({
                    url: '/personnel/auth',
                    method: 'POST',
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