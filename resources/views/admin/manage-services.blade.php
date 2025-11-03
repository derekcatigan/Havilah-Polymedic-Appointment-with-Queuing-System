{{-- resources\views\admin\manage-services.blade.php --}}
@extends('layout.layout')

@section('content')
    {{-- Header --}}
    <div class="flex justify-between items-center flex-wrap p-5">
        <div>
            <form method="GET" class="flex items-center gap-2">
                <label class="input">
                    <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none"
                            stroke="currentColor">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </g>
                    </svg>
                    <input type="text" name="search" class="w-full" placeholder="Search" value="{{ request('search') }}"
                        autocomplete="off" />
                </label>

                <button type="submit" class="btn btn-sm btn-primary">Search</button>
            </form>
        </div>

        <div>
            <a href="{{ route('admin.services.create') }}" class="btn btn-sm btn-primary">Add Services</a>
        </div>
    </div>

    {{-- Body --}}
    <div class="w-full">
        <div class="rounded-box border border-gray-300 bg-base-100 h-[600px]">
            <table class="table table-zebra w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-center w-16">#</th>
                        <th>Code</th>
                        <th>Item Category</th>
                        <th>Item Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($services as $service)
                        <tr>
                            <td class="text-center">
                                {{ $loop->iteration + ($services->currentPage() - 1) * $services->perPage() }}
                            </td>
                            <td>{{ $service->item_code_id }}</td>
                            <td>{{ $service->item_category }}</td>
                            <td>{{ $service->short_description ?? '—' }}</td>
                            <td>
                                <div class="dropdown dropdown-end">
                                    <button tabindex="0" class="btn btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                        </svg>
                                    </button>
                                    <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-32">
                                        <li>
                                            <a href="{{ route('admin.services.edit', $service->id) }}" class="text-blue-600">
                                                Edit
                                            </a>
                                        </li>
                                        <li>
                                            <button type="button" class="text-red-600"
                                                onclick="showDeleteModal('{{ $service->id }}', '{{ $service->short_description }}')">
                                                Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No services found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-5">
                {{ $services->links() }}
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <input type="checkbox" id="delete-modal" class="modal-toggle" />
    <div class="modal" role="dialog">
        <div class="modal-box">
            <h3 class="font-bold text-lg text-red-600">Confirm Deletion</h3>
            <p class="py-4">Are you sure you want to delete <span id="service-name" class="font-semibold"></span>?</p>

            <form id="delete-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-action">
                    <button type="submit" id="confirm-delete" class="btn btn-error">Yes, Delete</button>
                    <label for="delete-modal" class="btn">Cancel</label>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let serviceIdToDelete = null;

        function showDeleteModal(serviceId, serviceName) {
            serviceIdToDelete = serviceId;
            document.getElementById('service-name').textContent = serviceName || 'this service';
            document.getElementById('delete-modal').checked = true;
        }

        // Handle form submit via AJAX
        $('#delete-form').on('submit', function (e) {
            e.preventDefault();

            if (!serviceIdToDelete) return;

            const url = `/admin/services/${serviceIdToDelete}`;
            const token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: token
                },
                success: function (response) {
                    // Hide modal
                    $('#delete-modal').prop('checked', false);

                    $(`button[onclick*="${serviceIdToDelete}"]`).closest('tr').fadeOut(400, function () {
                        $(this).remove();
                    });

                    $.toast({
                        heading: 'Success',
                        icon: 'success',
                        text: response.message || 'Service deleted successfully!',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        stack: 3
                    });
                },
                error: function (xhr) {
                    $('#delete-modal').prop('checked', false);

                    $.toast({
                        heading: 'Error',
                        icon: 'error',
                        text: 'Failed to delete service. Please try again.',
                        showHideTransition: 'fade',
                        position: 'top-right',
                        stack: 3
                    });

                    console.error(xhr.responseText);
                }
            });
        });
    </script>
@endsection