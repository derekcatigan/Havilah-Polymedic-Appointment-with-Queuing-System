{{-- resources\views\auth\forgot-password.blade.php --}}
@extends('layout.app')

@section('content')
    <section class="flex justify-center items-center h-screen">
        <div class="bg-white border border-gray-200 rounded shadow-xl p-10 w-[500px]">

            <h3 class="text-lg font-semibold mb-2">Forgot Password</h3>
            <p class="label text-xs mb-5">Enter your email and we will send you a reset link.</p>

            <form id="forgotPasswordForm">
                @csrf

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Email</legend>
                    <input type="email" name="email" class="input w-full" placeholder="e.g johndoe@gmail.com">
                </fieldset>

                <button class="btn btn-primary text-white btn-block mt-5">Send Reset Link</button>
            </form>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $('#forgotPasswordForm').submit(function (e) {
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: "/forgot-password",
                data: $(this).serialize(),
                success: function (res) {
                    $.toast({
                        heading: "Success",
                        text: res.message,
                        icon: "success",
                        position: "top-right"
                    });
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