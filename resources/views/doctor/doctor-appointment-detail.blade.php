{{-- resources\views\doctor\doctor-appointment-detail.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="p-5 max-w-2xl mx-auto">
        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h2 class="text-xl font-bold mb-4">Appointment Details</h2>

                {{-- Patient Info --}}
                <div class="mb-4">
                    <h3 class="font-semibold">Patient</h3>
                    <p>{{ $appointment->patient->name }}</p>
                </div>

                {{-- Appointment Reason --}}
                <div class="mb-4">
                    <h3 class="font-semibold">Reason for Appointment</h3>
                    <p>{{ $appointment->reason ?? 'No reason provided' }}</p>
                </div>

                {{-- Appointment Time --}}
                <div class="mb-4">
                    <h3 class="font-semibold">Schedule</h3>
                    <p>{{ $appointment->starts_at->format('M d, Y h:i A') }}</p>
                </div>

                <a href="{{ route('doctor.appointment') }}" class="btn btn-sm btn-primary">Back</a>
            </div>
        </div>
    </div>
@endsection