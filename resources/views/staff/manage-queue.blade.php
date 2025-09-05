{{-- resources\views\staff\manage-queue.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="p-5">
        <h2 class="text-2xl font-bold mb-4">Queue Management</h2>

        {{-- Doctor Filter --}}
        <form method="GET" class="mb-4 flex gap-2">
            <select name="doctor" class="select select-bordered">
                <option value="">All Doctors</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ request('doctor') == $doctor->id ? 'selected' : '' }}>
                        Dr. {{ $doctor->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        {{-- Call Next Patient --}}
        {{-- <form method="POST" action="{{ route('staff.queue.callNext') }}">
            @csrf
            <button type="submit" class="btn btn-accent mb-4">
                Call Next Patient
            </button>
        </form> --}}

        {{-- Current Queue --}}
        @if($currentQueue)
            <div class="alert alert-info shadow-lg mb-4">
                <div>
                    <span class="text-lg font-bold">
                        Now Serving: Queue #{{ $currentQueue->queue_number }} - {{ $currentQueue->patient->name }}
                    </span>
                </div>
            </div>
        @else
            <div class="alert alert-warning shadow-lg mb-4">
                <div>
                    <span class="text-lg font-bold">
                        No patient is currently being served.
                    </span>
                </div>
            </div>
        @endif

        {{-- Queue Table --}}
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>Queue #</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusClasses = [
                            'waiting' => 'badge-neutral',
                            'called' => 'badge-primary',
                            'in_progress' => 'badge-warning',
                            'completed' => 'badge-success',
                            'skipped' => 'badge-error',
                        ];
                    @endphp
                    @forelse($queues as $queue)

                        <tr>
                            <td class="font-bold">{{ $queue->queue_number }}</td>
                            <td>{{ $queue->patient->name }}</td>
                            <td>Dr. {{ $queue->doctor->name }}</td>
                            <td>
                                <span class="badge {{ $statusClasses[$queue->queue_status] ?? 'badge-neutral' }}">
                                    {{ ucfirst($queue->queue_status) }}
                                </span>
                            </td>
                            <td class="flex gap-2">
                                {{-- Call --}}
                                <form method="POST" action="{{ route('staff.queue.call', $queue) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-info">Call</button>
                                </form>

                                {{-- In Progress --}}
                                <form method="POST" action="{{ route('staff.queue.progress', $queue) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">In Progress</button>
                                </form>

                                {{-- Complete --}}
                                <form method="POST" action="{{ route('staff.queue.complete', $queue) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Complete</button>
                                </form>

                                {{-- Skip --}}
                                <form method="POST" action="{{ route('staff.queue.skip', $queue) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-error">Skip</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500">
                                No patients in queue today.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection