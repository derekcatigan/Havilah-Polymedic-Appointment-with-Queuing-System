{{-- resources\views\admin\create-services.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="min-h-screen py-10 px-5 md:px-10">
        <div class="max-w-3xl mx-auto">
            <!-- Card container -->
            <div class="card bg-base-100 shadow-xl border border-base-300">
                <div class="card-body">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-primary flex items-center gap-2">
                            <!-- Heroicon: Beaker -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.75 3v6.379a2.25 2.25 0 01-.659 1.591L5.5 14.561V17a2.25 2.25 0 002.25 2.25h8.5A2.25 2.25 0 0018.5 17v-2.439l-3.591-3.591a2.25 2.25 0 01-.659-1.591V3" />
                            </svg>
                            Create New Service Type
                        </h2>

                        <a href="{{ route('admin.manage.services') }}"
                            class="btn btn-sm btn-outline flex items-center gap-1">
                            <!-- Heroicon: Arrow Left -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Back
                        </a>
                    </div>

                    <!-- Form -->
                    <form id="createServiceForm" autocomplete="off" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @csrf

                        <!-- Item Code -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Item Code</span>
                            </label>
                            <input type="text" name="item_code_id" value="{{ $itemCode }}"
                                class="input input-bordered w-full bg-gray-100" readonly />
                        </div>

                        <!-- Standard Barcode -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Standard Barcode</span>
                            </label>
                            <input type="text" name="standard_barcode_id" value="{{ $barcode }}"
                                class="input input-bordered w-full bg-gray-100" readonly />
                        </div>

                        <!-- Short Description -->
                        <div class="form-control md:col-span-2">
                            <label class="label">
                                <span class="label-text font-medium">Short Description</span>
                            </label>
                            <input type="text" name="short_description" class="input input-bordered w-full"
                                placeholder="Enter short description" />
                        </div>

                        <!-- Standard Description -->
                        <div class="form-control md:col-span-2">
                            <label class="label">
                                <span class="label-text font-medium">Standard Description</span>
                            </label>
                            <textarea name="standard_description" class="textarea textarea-bordered w-full" rows="3"
                                placeholder="Enter standard description"></textarea>
                        </div>

                        <!-- Generic Name -->
                        <div class="form-control md:col-span-2">
                            <label class="label">
                                <span class="label-text font-medium">Generic Name</span>
                            </label>
                            <input type="text" name="generic_name" class="input input-bordered w-full"
                                placeholder="Enter generic name" />
                        </div>

                        <!-- Specifications -->
                        <div class="form-control md:col-span-2">
                            <label class="label">
                                <span class="label-text font-medium">Specifications</span>
                            </label>
                            <textarea name="specifications" class="textarea textarea-bordered w-full" rows="2"
                                placeholder="Enter specifications"></textarea>
                        </div>

                        <!-- Category -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Item Category</span>
                            </label>
                            <select name="item_category" class="select select-bordered w-full">
                                <option disabled selected>Select category</option>
                                <option>Laboratory</option>
                                <option>Radiology</option>
                                <option>Consultation</option>
                                <option>Pharmacy</option>
                            </select>
                        </div>

                        <!-- Examination Type -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Examination Type</span>
                            </label>
                            <input type="text" name="examination_type" class="input input-bordered w-full"
                                placeholder="Enter examination type" />
                        </div>

                        <!-- Buttons -->
                        <div class="md:col-span-2 flex justify-end mt-6">
                            <button type="reset" class="btn btn-outline btn-sm mr-2 flex items-center gap-1">
                                <!-- Heroicon: Arrow Path -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5l15 15m0-15l-15 15" />
                                </svg>
                                Reset
                            </button>

                            <button type="submit" class="btn btn-primary btn-sm text-white flex items-center gap-1">
                                <!-- Heroicon: Plus -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Create Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('button[type="reset"]').on('click', function (e) {
                e.preventDefault(); // stop the default reset behavior

                // Clear all text and textarea fields
                $('#createServiceForm').find('input[type="text"], textarea').not('[readonly]').val('');
                $('#createServiceForm').find('select').prop('selectedIndex', 0);

                // Generate new item code + barcode dynamically via AJAX
                $.ajax({
                    url: "{{ route('admin.services.create') }}",
                    type: "GET",
                    success: function (response) {
                        // Create endpoint should return JSON instead of full view
                        $('input[name="item_code_id"]').val(response.itemCode);
                        $('input[name="standard_barcode_id"]').val(response.barcode);
                    }
                });

                // Toast message
                $.toast({
                    heading: 'Form Reset',
                    icon: 'info',
                    text: 'All fields have been cleared and new codes generated.',
                    showHideTransition: 'slide',
                    position: 'top-right',
                    stack: 3,
                });
            });


            $('#createServiceForm').on('submit', function (e) {
                e.preventDefault();

                let formData = $(this).serialize();
                let $submitBtn = $(this).find('button[type="submit"]');
                let $btnText = $submitBtn.find('span');

                $submitBtn.prop('disabled', true);
                $btnText.text('Creating...');

                $.ajax({
                    url: "{{ route('admin.services.store') }}",
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        $.toast({
                            heading: 'Success',
                            icon: 'success',
                            text: response.message,
                            showHideTransition: 'slide',
                            stack: 3,
                            position: 'top-right',
                        });

                        // Reset form
                        $('#createServiceForm')[0].reset();

                        // ✅ Set the next codes dynamically
                        $('input[name="item_code_id"]').val(response.next_item_code);
                        $('input[name="standard_barcode_id"]').val(response.next_barcode);
                    },
                    error: function (xhr) {
                        let errorMessage = "Something went wrong. Please try again later.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        $.toast({
                            heading: 'Error',
                            icon: 'error',
                            text: errorMessage,
                            showHideTransition: 'slide',
                            stack: 3,
                            position: 'top-right',
                        });
                    },
                    complete: function () {
                        $submitBtn.prop('disabled', false);
                        $btnText.text('Create Service');
                    }
                });
            });
        });
    </script>
@endsection