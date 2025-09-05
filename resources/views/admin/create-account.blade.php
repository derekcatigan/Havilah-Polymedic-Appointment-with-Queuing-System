{{-- resources\views\admin\create-account.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="w-full p-5 flex items-center gap-3">
        <a href="{{ route('admin.manage.account') }}" class="btn btn-sm btn-primary">Back</a>
        <h3 class="text-xl font-semibold">Create Account</h3>
    </div>
    <div class="w-full flex justify-center">
        <div class="w-[500px] bg-white border border-gray-300 p-5 rounded-md shadow-lg">
            <form id="accountForm" autocomplete="off">
                @csrf
                <div class="w-full bg-cyan-200 p-3 border border-cyan-400 rounded-lg mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <p class="text-sm">Please double check the information before submitting.</p>
                </div>
                <div class="w-full">
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Full name</legend>
                        <input type="text" id="name" name="name" class="w-full input input-sm" placeholder="" />
                    </fieldset>
                </div>
                <div class="w-full">
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Email address</legend>
                        <input type="email" id="email" name="email" class="w-full input input-sm" placeholder="" />
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
                    </fieldset>
                </div>
                <div class="w-full">
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">User role</legend>
                        <select name="role" id="role" class="w-full select select-sm">
                            <option disabled selected>select a role</option>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                            <option value="patient">Patient</option>
                        </select>
                    </fieldset>
                </div>
                <div class="w-full">
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Password</legend>
                        <input type="password" id="password" name="password" class="w-full input input-sm"
                            value="password" />
                        <p class="label text-xs">Default password is "password" or you can customize password.</p>
                    </fieldset>
                    <div class="w-full flex items-center">
                        <div class="flex items-center gap-1">
                            <input type="checkbox" id="checkPass" class="checkbox checkbox-xs" />
                            <label for="checkPass" class="label text-sm">Show password</label>
                        </div>
                    </div>
                </div>

                <button type="submit" id="createBtn" class="btn btn-sm btn-block btn-primary mt-5">
                    <span id="buttonText">Create Account</span>
                    <span id="spinner" class="loading loading-dots loading-sm hidden"></span>
                </button>
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

            $('#accountForm').on('submit', function (e) {
                e.preventDefault();

                let formData = $(this).serialize();
                let $createBtn = $('#createBtn')
                let $buttonText = $('#buttonText');
                let $spinner = $('#spinner');

                $createBtn.prop('disabled', true);
                $buttonText.addClass('hidden');
                $spinner.removeClass('hidden');

                $.ajax({
                    method: 'POST',
                    url: '/admin/account/store',
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
                        window.location.href = "/admin/manage-account";
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
                        $createBtn.prop('disabled', false);
                        $buttonText.removeClass('hidden');
                        $spinner.addClass('hidden');
                    }
                });
            });
        });
    </script>
@endsection