{{-- resources\views\admin\manage-doctor.blade.php --}}
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
            <a href="{{ route('create.doctor') }}" class="btn btn-sm btn-primary">Add doctor</a>
        </div>
    </div>

    {{-- Body --}}
    <div class="w-full">
        <div class="rounded-box border border-gray-300 bg-base-100 h-[600px]">
            <table class="table table-zebra w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-center"></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($doctors as $index => $doctor)
                        <tr class="font-medium border-b border-b-gray-300">
                            <td class="font-semibold text-center">{{ $index + 1 }}</td>
                            <td class="flex items-center gap-2">
                                <div
                                    class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 flex-shrink-0">
                                    {{ strtoupper(substr($doctor->name, 0, 1)) }}
                                </div>
                                {{ $doctor->name }}
                            </td>
                            <td>{{ $doctor->email }}</td>
                            <td>{{ Str::title($doctor->role->value) }}</td>
                            <td>
                                @if ($doctor->doctor?->status === 'available')
                                    <span class="badge badge-sm badge-success">
                                        {{ Str::title($doctor->doctor->status) }}
                                    </span>
                                @else
                                    <span class="badge badge-sm badge-error">null</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown dropdown-end">
                                    <div tabindex="0" role="button" class="btn btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                        </svg>
                                    </div>
                                    <ul tabindex="0"
                                        class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm space-y-1">
                                        <li>
                                            <a href="{{ route('edit.doctor', $doctor->id) }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                                Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="text-red-500 delete-btn" data-id="{{ $doctor->id }}">
                                                <svg xmlns=" http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No accounts found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-5">
                {{ $doctors->links() }}
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <input type="checkbox" id="deleteConfirmModal" class="modal-toggle" />
    <div class="modal" role="dialog">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Are you sure?</h3>
            <p class="py-4">This action cannot be undone.</p>

            <div class="modal-action">
                <label for="deleteConfirmModal" class="btn">Cancel</label>
                <button id="confirmDeleteBtn" class="btn btn-error">Delete</button>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            let doctorIdToDelete = null;

            $('.delete-btn').click(function (e) {
                e.preventDefault();
                doctorIdToDelete = $(this).data('id');
                $('#deleteConfirmModal').prop('checked', true);
            });

            $('#confirmDeleteBtn').click(function () {
                if (!doctorIdToDelete) return;

                $.ajax({
                    url: "{{ url('/admin/doctor/delete') }}/" + doctorIdToDelete,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        $.toast({
                            heading: 'Success',
                            icon: 'success',
                            text: response.message,
                            showHideTransition: 'slide',
                            stack: 3,
                            position: 'top-right',
                        });

                        $('#deleteConfirmModal').prop('checked', false);

                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    },
                    error: function (xhr) {
                        let errorMessage = xhr.responseJSON?.message || "Something went wrong";
                        $.toast({
                            heading: "Error",
                            icon: "error",
                            text: errorMessage,
                            showHideTransition: 'slide',
                            stack: 3,
                            position: 'top-right',
                        });
                    }
                });
            });
        });
    </script>
@endsection