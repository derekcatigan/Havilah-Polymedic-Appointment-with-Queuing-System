{{-- resources/views/admin/create-doctor.blade.php --}}
@extends('layout.layout')

@section('content')

    {{-- Page Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b bg-white">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.manage.doctor') }}" class="btn btn-sm btn-outline">
                ← Back
            </a>
            <h1 class="text-xl font-semibold text-gray-800">Add Doctor</h1>
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
                        Fill in the doctor’s details below.
                    </p>
                </div>

                {{-- Card Body --}}
                <form id="createDoctorForm" method="POST" enctype="multipart/form-data" autocomplete="off"
                    class="p-6 space-y-6">

                    @csrf

                    {{-- Info Notice --}}
                    <div class="flex items-start gap-3 bg-cyan-50 border border-cyan-200 rounded-lg p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-cyan-600" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                        <p class="text-sm text-cyan-800">
                            Please double-check the information before submitting.
                        </p>
                    </div>

                    {{-- Profile Picture --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Profile Picture
                        </label>

                        <div class="flex items-center gap-4">
                            <div class="avatar placeholder">
                                <div class="w-24 rounded-full bg-gray-100 text-gray-400">
                                    <span class="text-xl">DR</span>
                                </div>
                            </div>

                            <input type="file" id="profile_picture" name="profile_picture"
                                class="file-input file-input-bordered file-input-sm w-full" accept="image/*" />
                        </div>
                    </div>

                    {{-- Grid Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" id="name" name="name" class="input input-bordered input-sm w-full">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" class="input input-bordered input-sm w-full">
                        </div>

                        {{-- Contact --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                            <input type="tel" id="phone" name="phone"
                                class="input input-bordered input-sm w-full tabular-nums" pattern="[0-9]*" minlength="11"
                                maxlength="11" title="Must be 11 digits">
                        </div>

                        {{-- Address --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" id="address" name="address" class="input input-bordered input-sm w-full">
                        </div>

                        {{-- Specialty --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Specialty</label>
                            <select name="specialty" id="specialty" class="select select-bordered select-sm w-full">
                                <option disabled selected>Select a specialty</option>
                                <option value="nephrologist">Nephrologist</option>
                                <option value="general surgeon">General Surgeon</option>
                                <option value="pediatrician">Pediatrician</option>
                                <option value="internal medicine - diabetes">Internal Medicine - Diabetes</option>
                                <option value="obstetrics - gynecology and ultra sound">
                                    Obstetrics - Gynecology and Ultra Sound
                                </option>
                                <option value="internal medicine">Internal Medicine</option>
                                <option value="eent">EENT</option>
                                <option value="dental medicine">Dental Medicine</option>
                            </select>
                        </div>

                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" class="input input-bordered input-sm w-full"
                            value="password">

                        <p class="text-xs text-gray-500 mt-1">
                            Default password is <strong>password</strong>. You may customize it.
                        </p>

                        <div class="flex items-center gap-2 mt-2">
                            <input type="checkbox" id="checkPass" class="checkbox checkbox-xs">
                            <label for="checkPass" class="text-sm">Show password</label>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="pt-4 border-t flex justify-end">
                        <button type="submit" id="addBtn" class="btn btn-primary btn-sm px-6">
                            <span id="buttonText">Add Doctor</span>
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

            $('#createDoctorForm').on('submit', function (e) {
                e.preventDefault();

                let formData = new FormData(this);
                let $addBtn = $('#addBtn');
                let $buttonText = $('#buttonText');
                let $spinner = $('#spinner');

                $addBtn.prop('disabled', true);
                $buttonText.addClass('hidden');
                $spinner.removeClass('hidden');

                $.ajax({
                    type: "POST",
                    url: "/admin/doctor/store",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $.toast({
                            heading: 'Success',
                            icon: 'success',
                            text: response.message,
                            position: 'top-right',
                        });
                        window.location.href = "/admin/manage-doctor";
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorText = "";
                            $.each(errors, function (key, value) {
                                errorText += value[0] + "<br>";
                            });

                            $.toast({
                                heading: "Validation Error",
                                icon: "error",
                                text: errorText,
                                position: 'top-right',
                            });
                        }
                    },
                    complete: function () {
                        $addBtn.prop('disabled', false);
                        $buttonText.removeClass('hidden');
                        $spinner.addClass('hidden');
                    }
                });
            });
        });
    </script>
@endsection