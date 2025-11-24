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

                {{-- Queue Actions --}}
                @if($appointment->queue)
                    <div class="mt-4 flex gap-2">
                        @php
                            $queue = $appointment->queue;
                        @endphp

                        {{-- Call --}}
                        <form method="POST" action="{{ route('staff.queue.call', $queue) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-info" {{ in_array($queue->queue_status, ['called', 'in_progress', 'completed']) ? 'disabled' : '' }}>
                                Call
                            </button>
                        </form>

                        {{-- In Progress --}}
                        <form method="POST" action="{{ route('staff.queue.progress', $queue) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning" {{ $queue->queue_status !== 'called' ? 'disabled' : '' }}>
                                In Progress
                            </button>
                        </form>

                        {{-- Complete --}}
                        <form method="POST" action="{{ route('staff.queue.complete', $queue) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" {{ $queue->queue_status !== 'in_progress' ? 'disabled' : '' }}>
                                Complete
                            </button>
                        </form>

                        {{-- Skip --}}
                        <form method="POST" action="{{ route('staff.queue.skip', $queue) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-error" {{ in_array($queue->queue_status, ['completed', 'skipped']) ? 'disabled' : '' }}>
                                Skip
                            </button>
                        </form>
                    </div>
                @endif

                <a href="{{ route('doctor.appointment') }}" class="btn btn-sm btn-primary">Back</a>
            </div>
        </div>
    </div>
@endsection