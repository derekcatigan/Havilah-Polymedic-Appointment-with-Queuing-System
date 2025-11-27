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
                {{-- Info --}}
                <div class="w-full bg-cyan-200 p-3 border border-cyan-400 rounded-lg mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <p class="text-sm">Please double check the information before submitting.</p>
                </div>

                {{-- Name --}}
                <fieldset class="fieldset mb-3">
                    <legend class="fieldset-legend">Full name</legend>
                    <input type="text" id="name" name="name" class="w-full input input-sm" />
                </fieldset>

                {{-- Email --}}
                <fieldset class="fieldset mb-3">
                    <legend class="fieldset-legend">Email address</legend>
                    <input type="email" id="email" name="email" class="w-full input input-sm" />
                </fieldset>

                {{-- Phone --}}
                <fieldset class="fieldset mb-3">
                    <legend class="fieldset-legend">Contact number</legend>
                    <input type="tel" id="phone" name="phone" class="w-full tabular-nums input input-sm" pattern="[0-9]*"
                        minlength="11" maxlength="11" title="Must be 11 digits" />
                </fieldset>

                {{-- Address --}}
                <fieldset class="fieldset mb-3">
                    <legend class="fieldset-legend">Address</legend>
                    <input type="text" id="address" name="address" class="w-full input input-sm" />
                </fieldset>

                {{-- Role --}}
                <fieldset class="fieldset mb-3">
                    <legend class="fieldset-legend">User role</legend>
                    <select name="role" id="role" class="w-full select select-sm">
                        <option disabled selected>select a role</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                    </select>
                </fieldset>

                {{-- Doctor Select (for staff) --}}
                <fieldset class="fieldset mb-3 hidden" id="doctorSelectWrapper">
                    <legend class="fieldset-legend">Assign Doctor</legend>
                    <select name="doctor_user_id" id="doctor_user_id" class="w-full select select-sm">
                        <option disabled selected>Select a doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }} - {{ $doctor->doctor->specialty ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </fieldset>

                {{-- Password --}}
                <fieldset class="fieldset mb-2">
                    <legend class="fieldset-legend">Password</legend>
                    <input type="password" id="password" name="password" class="w-full input input-sm" value="password" />
                    <p class="label text-xs">Default password is "password" or you can customize password.</p>
                </fieldset>
                <div class="w-full flex items-center mb-3">
                    <div class="flex items-center gap-1">
                        <input type="checkbox" id="checkPass" class="checkbox checkbox-xs" />
                        <label for="checkPass" class="label text-sm">Show password</label>
                    </div>
                </div>

                <button type="submit" id="createBtn" class="btn btn-sm btn-block btn-primary mt-3">
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

            // Show/hide doctor select if role = staff
            $('#role').change(function () {
                if ($(this).val() === 'staff') {
                    $('#doctorSelectWrapper').removeClass('hidden');
                } else {
                    $('#doctorSelectWrapper').addClass('hidden');
                }
            });

            // Toggle password visibility
            $('#checkPass').change(function () {
                $('#password').attr('type', $(this).is(':checked') ? 'text' : 'password');
            });

            // Form submit
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
                            position: 'top-right',
                        });
                        window.location.href = "/admin/manage-account";
                    },
                    error: function (xhr) {
                        let error = xhr.responseJSON?.message || "Something went wrong.";
                        $.toast({
                            heading: "Error",
                            icon: "error",
                            text: error,
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