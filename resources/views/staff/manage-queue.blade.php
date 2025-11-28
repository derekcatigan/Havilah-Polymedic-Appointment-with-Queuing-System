{{-- resources\views\staff\manage-queue.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Queue Management</h2>

        {{-- FILTER BAR --}}
        <form method="GET" class="mb-6 bg-white p-4 rounded-lg shadow flex flex-wrap gap-4 items-end">

            {{-- Search Patient --}}
            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-600 mb-1">Search Patient</label>
                <input type="text" name="search" value="{{ $search }}" class="input w-full"
                    placeholder="Enter patient name..." onchange="this.form.submit()">
            </div>

            {{-- Date Filter --}}
            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-600 mb-1">Filter by Date</label>
                <input type="date" name="date" value="{{ $date }}" class="input w-full" onchange="this.form.submit()">
            </div>

            {{-- Reset Button --}}
            <div>
                <button type="submit" class="btn btn-primary">
                    Apply Filters
                </button>
            </div>

            {{-- Clear --}}
            <div>
                <a href="{{ route('staff.queue.index') }}" class="btn btn-soft btn-warning">
                    Reset
                </a>
            </div>
        </form>

        {{-- Current Queue Card --}}
        <div class="mb-6">
            @if($currentQueue)
                <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded-lg shadow flex items-center gap-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-3-3v6m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-semibold text-lg">Now Serving: Queue #{{ $currentQueue->queue_number }}</p>
                        <p class="text-gray-600">{{ $currentQueue->patient->name }}</p>
                    </div>
                </div>
            @else
                <div
                    class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 rounded-lg shadow flex items-center gap-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M12 12h0" />
                    </svg>
                    <div>
                        <p class="font-semibold text-lg">No patient is currently being served</p>
                        <p class="text-gray-600">Please wait for patients to arrive.</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Queue Table Card --}}
        <div class="bg-white p-4 rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-600 font-medium">Queue #</th>
                        <th class="px-4 py-2 text-left text-gray-600 font-medium">Patient</th>
                        <th class="px-4 py-2 text-left text-gray-600 font-medium">Doctor</th>
                        <th class="px-4 py-2 text-left text-gray-600 font-medium">Status</th>
                        <th class="px-4 py-2 text-center text-gray-600 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $statusClasses = [
                            'waiting' => 'bg-gray-200 text-gray-800',
                            'called' => 'bg-blue-200 text-blue-800',
                            'in_progress' => 'bg-yellow-200 text-yellow-800',
                            'completed' => 'bg-green-200 text-green-800',
                            'skipped' => 'bg-red-200 text-red-800',
                            'cancelled' => 'bg-gray-300 text-gray-700',
                        ];
                    @endphp

                    @forelse($queues as $queue)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-semibold">{{ $queue->queue_number }}</td>
                            <td class="px-4 py-3">{{ $queue->patient->name }}</td>
                            <td class="px-4 py-3">Dr. {{ $queue->doctor->name }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$queue->queue_status] ?? 'bg-gray-200 text-gray-800' }}">
                                    {{ ucfirst($queue->queue_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 flex flex-wrap justify-center gap-2">
                                <form method="POST" action="{{ route('staff.queue.call', $queue) }}">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm bg-blue-500 hover:bg-blue-600 text-white transition">Call</button>
                                </form>

                                <form method="POST" action="{{ route('staff.queue.progress', $queue) }}">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm bg-yellow-400 hover:bg-yellow-500 text-white transition">In
                                        Progress</button>
                                </form>

                                <form method="POST" action="{{ route('staff.queue.complete', $queue) }}">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm bg-green-500 hover:bg-green-600 text-white transition">Complete</button>
                                </form>

                                <form method="POST" action="{{ route('staff.queue.skip', $queue) }}">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm bg-red-500 hover:bg-red-600 text-white transition">Skip</button>
                                </form>

                                {{-- Cancel --}}
                                <form method="POST" action="{{ route('staff.queue.cancel', $queue) }}">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm bg-gray-600 hover:bg-gray-700 text-white transition">
                                        Cancel
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500 font-medium">
                                No patients in queue today.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection