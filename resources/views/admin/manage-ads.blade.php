{{-- resources\views\admin\manage-ads.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="p-5">
        <h1 class="text-2xl font-bold mb-4">Manage Ads</h1>

        {{-- Flash message --}}
        @if (session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif

        {{-- Create new ad --}}
        <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data"
            class="border border-gray-300 rounded-lg p-4 mb-6 bg-white shadow-sm">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium mb-1">Title</label>
                    <input type="text" name="title" class="input input-bordered w-full" placeholder="Ad title">
                </div>

                <div>
                    <label class="block font-medium mb-1">Image</label>
                    <input type="file" name="image" class="file-input file-input-bordered w-full" required>
                </div>

                <div>
                    <label class="block font-medium mb-1">Link (optional)</label>
                    <input type="url" name="link" class="input input-bordered w-full" placeholder="https://example.com">
                </div>

                <div>
                    <label class="block font-medium mb-1">Position</label>
                    <select name="position" class="select select-bordered w-full">
                        <option value="homepage">Homepage</option>
                        <option value="sidebar">Sidebar</option>
                        <option value="footer">Footer</option>
                    </select>
                </div>

                <div>
                    <label class="block font-medium mb-1">Status</label>
                    <select name="status" class="select select-bordered w-full">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Add Ad</button>
            </div>
        </form>

        {{-- Ads Table --}}
        <div class="overflow-x-auto border border-gray-300 rounded-lg bg-white shadow-sm">
            <table class="table w-full">
                <thead class="bg-gray-100">
                    <tr class="divide-x divide-gray-300">
                        <th>#</th>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Link</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($ads as $index => $ad)
                        <tr class="divide-x divide-gray-200">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $ad->title ?? '—' }}</td>
                            <td>
                                <img src="{{ asset('storage/' . $ad->image_path) }}" alt="Ad"
                                    class="w-24 h-16 object-cover rounded">
                            </td>
                            <td>
                                @if($ad->link)
                                    <a href="{{ $ad->link }}" class="text-blue-500 underline" target="_blank">{{ $ad->link }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ ucfirst($ad->position) }}</td>
                            <td>
                                <span class="badge {{ $ad->status === 'active' ? 'badge-success' : 'badge-error' }}">
                                    {{ ucfirst($ad->status) }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST"
                                    onsubmit="return confirm('Delete this ad?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-error text-white">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No ads found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">
                {{ $ads->links() }}
            </div>
        </div>
    </div>
@endsection