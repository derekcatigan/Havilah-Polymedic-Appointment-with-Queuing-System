{{-- resources/views/doctor/doctor-profile.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-3xl mx-auto">

            {{-- Header --}}
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">My Profile</h2>
                <p class="text-sm text-gray-500">Manage your personal and availability details</p>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded shadow border border-gray-300">

                {{-- Success Alert --}}
                @if (session('success'))
                    <div class="alert alert-success rounded-none  custom-alert">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="p-8">

                    {{-- VIEW MODE --}}
                    <div id="viewMode" class="text-center space-y-4">

                        {{-- Avatar --}}
                        <div class="flex justify-center">
                            <div
                                class="w-24 h-24 rounded-full bg-primary text-white flex items-center justify-center text-3xl font-bold shadow">
                                {{ strtoupper(substr($doctor->name, 0, 1)) }}
                            </div>
                        </div>

                        {{-- Info --}}
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">
                                {{ $doctor->name }}
                            </h3>
                            <p class="text-gray-500">
                                {{ Str::title($doctor->doctor->specialty ?? 'N/A') }}
                            </p>
                        </div>

                        {{-- Status --}}
                        <div>
                            <span @class([
                                'badge badge-lg badge-soft px-4',
                                'badge-success border border-green-500' => $doctor->doctor?->status === 'available',
                                'badge-error border border-red-500' => $doctor->doctor?->status === 'unavailable',
                                'badge-neutral' => !$doctor->doctor?->status,
                            ])>
                                {{ ucfirst($doctor->doctor->status ?? 'Not Set') }}
                            </span>
                        </div>

                        {{-- Action --}}
                        <button onclick="toggleEdit()" class="btn btn-primary btn-sm px-6">
                            Edit Profile
                        </button>
                    </div>

                    {{-- EDIT MODE --}}
                    <div id="editMode" class="hidden">

                        <form action="{{ route('doctor.profile.update') }}" method="POST"
                            class="max-w-md mx-auto space-y-5">
                            @csrf
                            @method('PUT')

                            {{-- Name --}}
                            <div class="form-control">
                                <label class="label font-medium">Name</label>
                                <input type="text" name="name" class="input input-bordered w-full"
                                    value="{{ old('name', $doctor->name) }}">
                            </div>

                            {{-- Status --}}
                            <div class="form-control">
                                <label class="label font-medium">Availability Status</label>
                                <select name="status" class="select select-bordered w-full">
                                    <option value="available" @selected($doctor->doctor?->status === 'available')>
                                        Available
                                    </option>
                                    <option value="unavailable" @selected($doctor->doctor?->status === 'unavailable')>
                                        Unavailable
                                    </option>
                                </select>
                            </div>

                            {{-- Actions --}}
                            <div class="flex justify-end gap-3 pt-4 border-t">
                                <button type="button" onclick="toggleEdit()" class="btn btn-ghost">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-success px-6">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function toggleEdit() {
            document.getElementById('viewMode').classList.toggle('hidden');
            document.getElementById('editMode').classList.toggle('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const alert = document.querySelector('.custom-alert');
            if (alert) {
                setTimeout(() => alert.classList.add('opacity-0'), 2500);
                setTimeout(() => alert.remove(), 3000);
            }
        });
    </script>
@endsection