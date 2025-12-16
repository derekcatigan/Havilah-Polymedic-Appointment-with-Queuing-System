{{-- resources/views/admin/edit-account.blade.php --}}
@extends('layout.layout')

@section('content')

    {{-- Page Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b bg-white">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.manage.account') }}" class="btn btn-sm btn-outline">
                ← Back
            </a>
            <h1 class="text-xl font-semibold text-gray-800">Edit Account</h1>
        </div>
    </div>

    {{-- Page Body --}}
    <div class="min-h-screen bg-gray-50 py-10 px-4">
        <div class="max-w-3xl mx-auto">

            {{-- Card --}}
            <div class="bg-white rounded-xl shadow border">

                {{-- Card Header --}}
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-700">Account Details</h2>
                    <p class="text-sm text-gray-500">
                        Update the user information below.
                    </p>
                </div>

                {{-- Form --}}
                <form id="accountForm" method="POST" action="{{ route('update.account', $account->id) }}"
                    class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Info Notice --}}
                    <div class="flex items-start gap-3 bg-cyan-50 border border-cyan-200 rounded-lg p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-cyan-600" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                        <p class="text-sm text-cyan-800">
                            Please double-check the information before saving changes.
                        </p>
                    </div>

                    {{-- Grid Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" id="name" name="name" class="input input-bordered input-sm w-full"
                                value="{{ old('name', $account->name) }}">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" class="input input-bordered input-sm w-full"
                                value="{{ old('email', $account->email) }}">
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                            <input type="tel" id="phone" name="phone"
                                class="input input-bordered input-sm w-full tabular-nums" pattern="[0-9]*" minlength="11"
                                maxlength="11" title="Must be 11 digits"
                                value="{{ old('phone', $account->contact_number) }}">
                        </div>

                        {{-- Address --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" id="address" name="address" class="input input-bordered input-sm w-full"
                                value="{{ old('address', $account->address) }}">
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Account Status</label>
                            <select name="status" id="accountStatus" class="select select-bordered select-sm w-full">
                                <option value="active" {{ $account->status === 'active' ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="deactivate" {{ $account->status === 'deactivate' ? 'selected' : '' }}>
                                    Deactivated
                                </option>
                            </select>
                        </div>

                        {{-- Role --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">User Role</label>
                            <select name="role" id="role" class="select select-bordered select-sm w-full">
                                <option value="admin" {{ $account->role->value === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="staff" {{ $account->role->value === 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="patient" {{ $account->role->value === 'patient' ? 'selected' : '' }}>Patient
                                </option>
                            </select>
                        </div>

                        {{-- Doctor Select --}}
                        <div class="md:col-span-2 {{ $account->role->value === 'staff' ? '' : 'hidden' }}"
                            id="doctorSelectWrapper">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assign Doctor</label>
                            <select name="doctor_user_id" id="doctor_user_id"
                                class="select select-bordered select-sm w-full">
                                <option disabled>Select a doctor</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ $account->doctor_user_id === $doctor->id ? 'selected' : '' }}>
                                        {{ $doctor->name }} — {{ $doctor->doctor->specialty ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" class="input input-bordered input-sm w-full"
                            placeholder="Leave blank to keep current password">

                        <p class="text-xs text-gray-500 mt-1">
                            Leave empty if you don’t want to change the password.
                        </p>

                        <div class="flex items-center gap-2 mt-2">
                            <input type="checkbox" id="checkPass" class="checkbox checkbox-xs">
                            <label for="checkPass" class="text-sm">Show password</label>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-4 border-t flex justify-end">
                        <button type="submit" id="updateBtn" class="btn btn-primary btn-sm px-6">
                            <span id="buttonText">Update</span>
                            <span id="spinner" class="loading loading-dots loading-sm hidden"></span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {

            $('#checkPass').change(function () {
                $('#password').attr('type', this.checked ? 'text' : 'password');
            });

            $('#role').change(function () {
                if ($(this).val() === 'staff') {
                    $('#doctorSelectWrapper').removeClass('hidden');
                } else {
                    $('#doctorSelectWrapper').addClass('hidden');
                    $('#doctor_user_id').val('');
                }
            });


            $('#accountForm').on('submit', function (e) {
                e.preventDefault();

                let form = $(this);
                let url = form.attr('action');
                let formData = form.serialize();

                $('#updateBtn').prop('disabled', true);
                $('#buttonText').addClass('hidden');
                $('#spinner').removeClass('hidden');

                $.ajax({
                    url: url,
                    method: 'POST',
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
                        $.toast({
                            heading: 'Error',
                            icon: 'error',
                            text: xhr.responseJSON?.message || 'Something went wrong.',
                            position: 'top-right',
                        });
                    },
                    complete: function () {
                        $('#updateBtn').prop('disabled', false);
                        $('#buttonText').removeClass('hidden');
                        $('#spinner').addClass('hidden');
                    }
                });
            });

        });
    </script>
@endsection