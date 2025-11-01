{{-- resources\views\admin\manage-ads.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="min-h-screen bg-base-200 p-6 md:p-10">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl font-bold text-primary flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 20h9M12 4h9m-9 8h9M3 4h.01M3 12h.01M3 20h.01" />
                    </svg>
                    Manage Ads
                </h1>
            </div>

            <!-- Flash Message -->
            @if (session('success'))
                <div class="alert alert-success mb-6 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Create Ad Form -->
            <div class="card bg-base-100 shadow-xl border border-base-300 mb-10">
                <div class="card-body">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2 text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create New Ad
                    </h2>

                    <form id="createAdForm" action="{{ route('admin.ads.store') }}" method="POST"
                        enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @csrf

                        <!-- Title -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Title</span>
                            </label>
                            <input type="text" name="title" class="input input-bordered w-full"
                                placeholder="Enter ad title" />
                        </div>

                        <!-- Image -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Image</span>
                            </label>
                            <input type="file" name="image" class="file-input file-input-bordered w-full" required />
                        </div>

                        <!-- Link -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Link (optional)</span>
                            </label>
                            <input type="url" name="link" class="input input-bordered w-full"
                                placeholder="https://example.com" />
                        </div>

                        <!-- Position -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Position</span>
                            </label>
                            <select name="position" class="select select-bordered w-full">
                                <option disabled selected>Select position</option>
                                <option value="homepage">Homepage</option>
                                <option value="sidebar">Sidebar</option>
                                <option value="footer">Footer</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Status</span>
                            </label>
                            <select name="status" class="select select-bordered w-full">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="md:col-span-2 flex justify-end mt-4">
                            <button type="reset" class="btn btn-outline btn-sm mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.5 19.5L19.5 4.5M19.5 19.5L4.5 4.5" />
                                </svg>
                                Reset
                            </button>
                            <button type="submit" id="createBtn" class="btn btn-primary btn-sm text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                <span id="buttonText">Add Ad</span>
                                <span id="spinner" class="loading loading-dots loading-sm hidden"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ads Table -->
            <div class="card bg-base-100 shadow-xl border border-base-300">
                <div class="card-body">
                    <h2 class="text-lg font-semibold mb-4 text-primary flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v16l-8-4-8 4V4z" />
                        </svg>
                        Current Ads
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead class="bg-base-200 text-sm uppercase text-gray-600">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Image</th>
                                    <th>Link</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ads as $index => $ad)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="font-medium">{{ $ad->title ?? '—' }}</td>
                                        <td>
                                            <img src="{{ asset('storage/' . $ad->image_path) }}" alt="Ad"
                                                class="w-20 h-14 object-cover rounded-lg border border-base-300 shadow-sm">
                                        </td>
                                        <td>
                                            @if ($ad->link)
                                                <a href="{{ $ad->link }}" class="text-blue-600 underline"
                                                    target="_blank">{{ Str::limit($ad->link, 25) }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ ucfirst($ad->position) }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $ad->status === 'active' ? 'badge-success' : 'badge-error' }}">
                                                {{ ucfirst($ad->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this ad?')"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-error text-white flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-gray-500 py-4">No ads found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $ads->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function () {

            $('#createAdForm').on('submit', function (e) {
                e.preventDefault();

                let formData = new FormData(this);
                let $createBtn = $('#createBtn')
                let $buttonText = $('#buttonText');
                let $spinner = $('#spinner');

                $createBtn.prop('disabled', true);
                $buttonText.addClass('hidden');
                $spinner.removeClass('hidden');

                $.ajax({
                    method: 'POST',
                    url: '/admin/ads',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $.toast({
                            heading: 'Success',
                            icon: 'success',
                            text: response.message,
                            showHideTransition: 'slide',
                            stack: 3,
                            position: 'top-right',
                        });
                        window.location.href = "/admin/manage-ads";
                    },
                    error: function (xhr) {
                        let error = "Something went wrong. Please try again later.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            error = xhr.responseJSON.message;
                        }
                        $.toast({
                            heading: "Error",
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