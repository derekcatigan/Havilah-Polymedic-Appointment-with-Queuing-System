{{-- resources\views\auth\reset-password.blade.php --}}
@extends('layout.app')

@section('content')
    <section class="flex justify-center items-center h-screen">
        <div class="bg-white border border-gray-200 rounded shadow-xl p-10 w-[500px]">

            <h3 class="text-lg font-semibold mb-2">Reset Password</h3>
            <p class="label text-xs mb-5">Enter your new password.</p>

            <form id="resetPasswordForm">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">New Password</legend>
                    <input type="password" name="password" class="input w-full">
                </fieldset>

                <fieldset class="fieldset mt-3">
                    <legend class="fieldset-legend">Confirm Password</legend>
                    <input type="password" name="password_confirmation" class="input w-full">
                </fieldset>

                <button class="btn btn-primary text-white btn-block mt-5">Update Password</button>
            </form>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $('#resetPasswordForm').submit(function (e) {
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: "/reset-password",
                data: $(this).serialize(),
                success: function (res) {
                    $.toast({
                        heading: "Success",
                        text: res.message,
                        icon: "success",
                        position: "top-right"
                    });

                    setTimeout(() => {
                        window.location.href = "/login";
                    }, 1500);
                },
                error: function (xhr) {
                    $.toast({
                        heading: "Error",
                        text: xhr.responseJSON?.message || "Something went wrong",
                        icon: "error",
                        position: "top-right"
                    });
                }
            });
        });
    </script>
@endsection