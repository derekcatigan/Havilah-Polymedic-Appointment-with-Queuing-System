{{-- resources\views\doctor\doctor-profile.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="p-6 max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">My Profile</h2>

        {{-- Profile Card --}}
        <div class="card bg-base-100 shadow-lg border border-base-200">

            @if (session('success'))
                <div class="custom-alert alert alert-success mb-4">{{ session('success') }}</div>
            @endif

            <div class="card-body">
                {{-- View Mode --}}
                <div id="viewMode" class="text-center">
                    <h3 class="text-xl font-semibold">{{ $doctor->name }}</h3>
                    <p class="text-gray-600">{{ Str::title($doctor->doctor->specialty ?? 'N/A') }}</p>

                    <div class="mt-2">
                        <span @class([
                            'badge',
                            'badge-success' => $doctor->doctor?->status === 'available',
                            'badge-error' => $doctor->doctor?->status === 'unavailable',
                            'badge-neutral' => !$doctor->doctor?->status,
                        ])>
                            {{ ucfirst($doctor->doctor->status ?? 'Not Set') }}
                        </span>
                    </div>

                    <button onclick="toggleEdit()" class="btn btn-sm btn-primary mt-4">Edit</button>
                </div>

                {{-- Edit Mode --}}
                <div id="editMode" class="hidden text-center">
                    <form action="{{ route('doctor.profile.update') }}" method="POST" class="max-w-md mx-auto">
                        @csrf
                        @method('PUT')

                        {{-- Name --}}
                        <div class="form-control mb-4">
                            <label class="label font-semibold justify-center">Name</label>
                            <input type="text" name="name" class="input input-bordered w-full text-center"
                                value="{{ old('name', $doctor->name) }}">
                        </div>

                        {{-- Status --}}
                        <div class="form-control mb-4">
                            <label class="label font-semibold justify-center">Status</label>
                            <select name="status" class="select select-bordered w-full text-center">
                                <option value="available" @selected($doctor->doctor?->status === 'available')>Available
                                </option>
                                <option value="unavailable" @selected($doctor->doctor?->status === 'unavailable')>Unavailable
                                </option>
                            </select>
                        </div>

                        <div class="flex justify-center gap-2">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" onclick="toggleEdit()" class="btn btn-ghost">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')
    <script>
        function toggleEdit() {
            $("#viewMode").toggleClass("hidden");
            $("#editMode").toggleClass("hidden");
        }

        $(document).ready(function () {
            $(".custom-alert").delay(3000).fadeOut(600);
        });
    </script>
@endsection