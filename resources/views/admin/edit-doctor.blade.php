{{-- resources/views/admin/edit-doctor.blade.php --}}
@extends('layout.layout')

@section('content')

    {{-- Page Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b bg-white">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.manage.doctor') }}" class="btn btn-sm btn-outline">
                ← Back
            </a>
            <h1 class="text-xl font-semibold text-gray-800">Edit Doctor Profile</h1>
        </div>
    </div>

    {{-- Page Body --}}
    <div class="min-h-screen bg-gray-50 py-10 px-4">
        <div class="max-w-3xl mx-auto">

            {{-- Card --}}
            <div class="bg-white rounded-xl shadow border">

                {{-- Card Header --}}
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-700">Doctor Information</h2>
                    <p class="text-sm text-gray-500">
                        Update doctor details and availability.
                    </p>
                </div>

                {{-- Card Body --}}
                <form id="updateDoctorForm" method="POST" action="{{ route('update.doctor', $doctor->id) }}"
                    enctype="multipart/form-data" autocomplete="off" class="p-6 space-y-6">

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

                    {{-- Profile Picture --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Profile Picture
                        </label>

                        <div class="flex items-center gap-4">
                            <div class="avatar">
                                <div class="w-24 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                                    @if ($doctor->doctor?->profile_picture)
                                        <img src="{{ asset('storage/' . $doctor->doctor->profile_picture) }}" />
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($doctor->name) }}" />
                                    @endif
                                </div>
                            </div>

                            <input type="file" name="profile_picture"
                                class="file-input file-input-bordered file-input-sm w-full" accept="image/*">
                        </div>
                    </div>

                    {{-- Grid Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" class="input input-bordered input-sm w-full"
                                value="{{ old('name', $doctor->name) }}">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" class="input input-bordered input-sm w-full"
                                value="{{ old('email', $doctor->email) }}">
                        </div>

                        {{-- Contact --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                            <input type="tel" name="phone" class="input input-bordered input-sm w-full" minlength="11"
                                maxlength="11" value="{{ old('contact_number', $doctor->contact_number) }}">
                        </div>

                        {{-- Address --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="address" class="input input-bordered input-sm w-full"
                                value="{{ old('address', $doctor->address) }}">
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="select select-bordered select-sm w-full">
                                <option value="available" @selected($doctor->doctor->status === 'available')>Available
                                </option>
                                <option value="unavailable" @selected($doctor->doctor->status === 'unavailable')>Unavailable
                                </option>
                            </select>
                        </div>

                        {{-- Specialty --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Specialty</label>
                            <select name="specialty" class="select select-bordered select-sm w-full">
                                <option value="nephrologist" @selected($doctor->doctor->specialty === 'nephrologist')>
                                    Nephrologist</option>
                                <option value="general surgeon" @selected($doctor->doctor->specialty === 'general surgeon')>
                                    General Surgeon</option>
                                <option value="pediatrician" @selected($doctor->doctor->specialty === 'pediatrician')>
                                    Pediatrician</option>
                                <option value="internal medicine" @selected($doctor->doctor->specialty === 'internal medicine')>Internal Medicine</option>
                                <option value="eent" @selected($doctor->doctor->specialty === 'eent')>EENT</option>
                                <option value="dental medicine" @selected($doctor->doctor->specialty === 'dental medicine')>
                                    Dental Medicine</option>
                            </select>
                        </div>

                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" id="password" class="input input-bordered input-sm w-full"
                            placeholder="Leave blank to keep current password">
                        <p class="text-xs text-gray-500 mt-1">
                            Leave blank if you don’t want to change it.
                        </p>

                        <div class="flex items-center gap-2 mt-2">
                            <input type="checkbox" id="checkPass" class="checkbox checkbox-xs">
                            <label for="checkPass" class="text-sm">Show password</label>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-4 border-t flex justify-end">
                        <button type="submit" id="updateBtn" class="btn btn-primary btn-sm px-6">
                            <span id="buttonText">Save Changes</span>
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

            $('#checkPass').on('change', function () {
                $('#password').attr('type', this.checked ? 'text' : 'password');
            });

            $('#updateDoctorForm').on('submit', function (e) {
                e.preventDefault();

                let formData = new FormData(this);

                $('#updateBtn').prop('disabled', true);
                $('#buttonText').addClass('hidden');
                $('#spinner').removeClass('hidden');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $.toast({
                            heading: 'Success',
                            text: response.message,
                            icon: 'success',
                            position: 'top-right'
                        });
                        window.location.href = "/admin/manage-doctor";
                    },
                    error: function () {
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong.',
                            icon: 'error',
                            position: 'top-right'
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