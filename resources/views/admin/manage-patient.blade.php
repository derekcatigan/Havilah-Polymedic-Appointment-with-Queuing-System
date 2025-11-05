{{-- resources\views\admin\manage-patient.blade.php --}}
@extends('layout.layout')

@section('content')
    {{-- Header --}}
    <div class="flex justify-between items-center flex-wrap p-5">
        <div>
            <form id="searchForm" class="flex items-center gap-2">
                <label class="input">
                    <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none"
                            stroke="currentColor">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </g>
                    </svg>
                    <input type="text" name="search" class="w-full" placeholder="Search" autocomplete="off" />
                </label>

                <button type="submit" class="btn btn-sm btn-primary">Search</button>
            </form>
        </div>

        <div>
            <button class="btn btn-sm btn-primary" onclick="addPatientModal.showModal()">
                <!-- Heroicon: User Plus -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M8 9a3 3 0 100-6 3 3 0 000 6zm5 2a5 5 0 10-10 0v3h10v-3zm3 1h2v2h-2v2h-2v-2h-2v-2h2V9h2v3z"
                        clip-rule="evenodd" />
                </svg>
                Add Patient
            </button>
        </div>
    </div>

    {{-- Body --}}
    <div class="w-full">
        <div class="rounded-box border border-gray-300 bg-base-100 h-[600px]">
            <table class="table table-zebra w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-center"></th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($patients as $index => $patient)
                        <tr>
                            <td class="text-center">{{ $patients->firstItem() + $index }}</td>
                            <td>{{ $patient->patient_number }}</td>
                            <td>{{ $patient->name }}</td>
                            <td>{{ $patient->email }}</td>
                            <td>
                                @if ($patient->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-warning">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown dropdown-end">
                                    <button tabindex="0" class="btn btn-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                                        </svg>
                                    </button>

                                    <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-36">
                                        <li>
                                            <a href="{{ route('admin.patients.show', $patient->id) }}"
                                                class="btn btn-sm btn-ghost justify-start text-info">View History</a>
                                        </li>
                                        <li>
                                            <button class="btn btn-sm btn-ghost justify-start text-warning btn-edit"
                                                data-id="{{ $patient->id }}">Edit</button>
                                        </li>
                                        <li>
                                            <button class="btn btn-sm btn-ghost justify-start text-error btn-delete"
                                                data-id="{{ $patient->id }}">Delete</button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500">No patients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-5">
                {{ $patients->links() }}
            </div>
        </div>
    </div>

    <dialog id="addPatientModal" class="modal">
        <div class="modal-box max-w-md">
            <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                <!-- Heroicon: User -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5.121 17.804A9 9 0 1118.878 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Register New Patient
            </h3>

            <form id="addPatientForm" class="space-y-3">
                @csrf

                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Full Name</span></label>
                    <input type="text" name="name" class="input input-bordered w-full" placeholder="Enter full name"
                        required>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Email</span></label>
                    <input type="email" name="email" class="input input-bordered w-full" placeholder="Enter email address"
                        required>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Phone</span></label>
                    <input type="text" name="phone" class="input input-bordered w-full" placeholder="09XXXXXXXXX"
                        maxlength="11" required>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Address</span></label>
                    <input type="text" name="address" class="input input-bordered w-full" placeholder="Enter address"
                        required>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Password</span></label>
                    <input type="password" name="password" class="input input-bordered w-full" placeholder="Enter password"
                        required>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Confirm Password</span></label>
                    <input type="password" name="password_confirmation" class="input input-bordered w-full"
                        placeholder="Confirm password" required>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" onclick="addPatientModal.close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- Edit Modal --}}
    <dialog id="editPatientModal" class="modal">
        <div class="modal-box max-w-md">
            <h3 class="font-bold text-lg mb-4">Edit Patient</h3>

            <form id="editPatientForm" class="space-y-3">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" id="edit_id">

                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Full Name</span></label>
                    <input type="text" name="name" id="edit_name" class="input input-bordered w-full" required>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Email</span></label>
                    <input type="email" name="email" id="edit_email" class="input input-bordered w-full" required>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Phone</span></label>
                    <input type="text" name="phone" id="edit_phone" class="input input-bordered w-full" maxlength="11"
                        required>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Address</span></label>
                    <input type="text" name="address" id="edit_address" class="input input-bordered w-full" required>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" onclick="editPatientModal.close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- Delete Confirmation Modal --}}
    <dialog id="deletePatientModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4 text-error">Confirm Deletion</h3>
            <p>Are you sure you want to delete this patient?</p>

            <div class="modal-action">
                <button type="button" class="btn btn-ghost" onclick="deletePatientModal.close()">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-error">Delete</button>
            </div>
        </div>
    </dialog>
@endsection

@section('script')
    <script>
        $('#addPatientForm').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const formData = form.serialize();

            $.ajax({
                url: "{{ route('admin.patients.store') }}",
                method: "POST",
                data: formData,
                beforeSend: function () {
                    form.find('button[type="submit"]').prop('disabled', true).text('Saving...');
                },
                success: function (response) {
                    $.toast({
                        heading: 'Success',
                        icon: 'success',
                        text: response.message || 'Patient added successfully!',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        stack: 3
                    });

                    form[0].reset();
                    addPatientModal.close();

                    setTimeout(() => location.reload(), 1000);
                },
                error: function (xhr) {
                    let message = 'Something went wrong, please try again.';
                    if (xhr.status === 422 && xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }

                    $.toast({
                        heading: 'Error',
                        icon: 'error',
                        text: message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        stack: 3
                    });
                },
                complete: function () {
                    form.find('button[type="submit"]').prop('disabled', false).text('Save');
                }
            });
        });

        // 🟢 Handle Edit Button Click
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');

            $.get(`{{ url('admin/patients') }}/${id}/edit`, function (patient) {
                $('#edit_id').val(patient.id);
                $('#edit_name').val(patient.name);
                $('#edit_email').val(patient.email);
                $('#edit_phone').val(patient.contact_number);
                $('#edit_address').val(patient.address);

                editPatientModal.showModal();
            });
        });

        // 🟢 Handle Edit Form Submit
        $('#editPatientForm').on('submit', function (e) {
            e.preventDefault();
            const id = $('#edit_id').val();

            $.ajax({
                url: `{{ url('admin/patients') }}/${id}`,
                method: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    $.toast({
                        heading: 'Updated',
                        icon: 'success',
                        text: response.message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                    });

                    editPatientModal.close();
                    setTimeout(() => location.reload(), 800);
                },
                error: function () {
                    $.toast({
                        heading: 'Error',
                        icon: 'error',
                        text: 'Failed to update patient.',
                        position: 'top-right',
                    });
                }
            });
        });

        // 🟠 Handle Delete Button Click
        let deleteId = null;
        $(document).on('click', '.btn-delete', function () {
            deleteId = $(this).data('id');
            deletePatientModal.showModal();
        });

        // 🟠 Confirm Delete
        $('#confirmDeleteBtn').on('click', function () {
            if (!deleteId) return;

            $.ajax({
                url: `{{ url('admin/patients') }}/${deleteId}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    $.toast({
                        heading: 'Deleted',
                        icon: 'success',
                        text: response.message,
                        position: 'top-right',
                    });

                    deletePatientModal.close();
                    setTimeout(() => location.reload(), 800);
                },
                error: function () {
                    $.toast({
                        heading: 'Error',
                        icon: 'error',
                        text: 'Failed to delete patient.',
                        position: 'top-right',
                    });
                }
            });
        });
    </script>
@endsection