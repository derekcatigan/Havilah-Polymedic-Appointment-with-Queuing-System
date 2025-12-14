{{-- resources/views/staff/walkin-create.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
        <div class="w-full max-w-xl bg-white border border-gray-300 rounded shadow-lg">

            {{-- Header --}}
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">
                    Walk-in Appointment
                </h2>
                <p class="text-sm text-gray-500">
                    Register a patient and assign them to a doctor
                </p>
            </div>

            {{-- Form --}}
            <form action="{{ route('walkin.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                {{-- Patient Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Patient Name
                    </label>
                    <input type="text" name="patient_name" placeholder="Enter full name" class="input input-bordered w-full"
                        required>
                </div>

                {{-- Doctor --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Assigned Doctor
                    </label>

                    <select name="doctor_user_id" class="select select-bordered w-full" required onchange="
                                document.getElementById('specialtyBadge').innerText =
                                this.options[this.selectedIndex].dataset.specialty || '';
                            ">
                        <option value="">Select doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" data-specialty="{{ ucfirst($doctor->doctor?->specialty ?? '') }}">
                                {{ $doctor->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Specialty Badge --}}
                    <div class="mt-2">
                        <span id="specialtyBadge" class="badge badge-accent badge-soft border border-green-500"></span>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t pt-4 flex justify-between items-center">

                    {{-- Cancel --}}
                    <a href="{{ route('manage.appointment') }}" class="btn btn-ghost">
                        Cancel
                    </a>

                    {{-- Submit --}}
                    <button type="submit" class="btn btn-success px-6">
                        Book Walk-in
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection