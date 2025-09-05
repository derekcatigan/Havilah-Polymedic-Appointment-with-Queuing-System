{{-- resources\views\staff\manage-appointment-detail.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="p-6 max-w-4xl mx-auto space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">Appointment Details</h2>

        {{-- Patient Info --}}
        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-2">Patient Information</h3>
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 text-xl rounded-full bg-gray-300 flex items-center justify-center text-gray-600 flex-shrink-0">
                        {{ strtoupper(substr($appointment->patient->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium">{{ $appointment->patient->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Doctor Info --}}
        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-2">Doctor Information</h3>
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 text-xl rounded-full bg-gray-300 flex items-center justify-center text-gray-600 flex-shrink-0">
                        {{ strtoupper(substr($appointment->doctor->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium">{{ $appointment->doctor->name }}</p>
                        <p class="text-sm text-gray-500">{{ Str::title($appointment->doctor->doctor->specialty ?? 'N/A') }}
                        </p>
                        <p class="text-sm">
                            Status:
                            <span @class([
                                'badge badge-sm badge-soft',
                                'border border-green-300 badge-success' => $appointment->doctor->doctor->status === 'available',
                                'border border-red-300 badge-error' => $appointment->doctor->doctor->status === 'unavailable',
                                'border border-yellow-300 badge-warning' => !in_array($appointment->doctor->doctor->status, ['available', 'unavailable']),
                            ])>
                                {{ ucfirst($appointment->doctor->doctor->status ?? 'Unknown') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Appointment Info --}}
        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-2">Appointment Information</h3>
                <p><span class="font-medium">Date:</span> {{ $appointment->starts_at->format('M d, Y h:i A') }}</p>
                <p>
                    <span class="font-medium">Status:</span>
                    <span @class([
                        'badge badge-sm badge-soft',
                        'border border-yellow-300 badge-warning' => $appointment->status === 'pending',
                        'border border-green-300 badge-success' => $appointment->status === 'confirmed',
                        'border border-blue-300 badge-info' => $appointment->status === 'completed',
                        'border border-red-300 badge-error' => !in_array($appointment->status, ['pending', 'confirmed', 'completed']),
                    ])>
                        {{ ucfirst($appointment->status) }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-3">Actions</h3>
                <div class="flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('staff.appointments.confirm', $appointment->id) }}">
                        @csrf
                        <button class="btn btn-success" @disabled($appointment->status !== 'pending')>Confirm</button>
                    </form>

                    <form method="POST" action="{{ route('staff.appointments.cancel', $appointment->id) }}">
                        @csrf
                        <button class="btn btn-error" @disabled($appointment->status === 'cancelled' || $appointment->status === 'completed')>
                            Cancel
                        </button>
                    </form>

                    <form method="POST" action="{{ route('staff.appointments.complete', $appointment->id) }}">
                        @csrf
                        <button class="btn btn-info" @disabled($appointment->status !== 'confirmed')>Complete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection