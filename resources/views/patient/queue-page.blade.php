{{-- resources\views\patient\queue-page.blade.php --}}
@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('assets/css/home-style.css') }}">
@endsection

@section('content')
    @include('partials.header')

    <div class="p-6 max-w-4xl mx-auto">

        <h1 class="text-2xl font-bold mb-6">Queue Information</h1>

        {{-- Patient Queue Card --}}
        @if($patientQueue)
            <div class="bg-blue-50 border-2 border-blue-300 rounded-xl shadow-lg p-7 mb-8">
                <h3 class="text-2xl font-semibold text-blue-700 mb-4">Your Queue Today</h3>

                <div class="text-lg space-y-3">
                    <div class="flex justify-between">
                        <span class="font-medium">Doctor:</span>
                        <span class="font-semibold">Dr. {{ $patientQueue->doctor->name }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="font-medium">Queue #:</span>
                        <span class="text-2xl font-bold text-blue-800">{{ $patientQueue->queue_number }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="font-medium">Status:</span>
                        <span @class([
                            'px-3 py-1.5 text-sm font-bold rounded-lg',
                            'bg-gray-300 text-gray-800' => $patientQueue->queue_status === 'waiting',
                            'bg-blue-500 text-white' => $patientQueue->queue_status === 'called',
                            'bg-yellow-400 text-white' => $patientQueue->queue_status === 'in_progress',
                            'bg-green-500 text-white' => $patientQueue->queue_status === 'completed',
                            'bg-red-500 text-white' => $patientQueue->queue_status === 'skipped',
                        ])>
                            {{ ucfirst(str_replace('_', ' ', $patientQueue->queue_status)) }}
                        </span>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-gray-100 border border-gray-300 rounded-xl p-6 mb-8 text-center">
                <p class="text-lg text-gray-600">You are not in the queue today.</p>
            </div>
        @endif

        {{-- Current Active Queue Numbers --}}
        @if($currentQueues->count())
            <div class="bg-green-50 border-2 border-green-300 rounded-xl shadow-lg p-7">
                <h3 class="text-2xl font-semibold text-green-700 mb-4">Currently Serving</h3>

                <ul class="space-y-4 text-lg">
                    @foreach($currentQueues as $doctorId => $queues)
                        @php $queue = $queues->first(); @endphp

                        <li class="flex justify-between items-center bg-white p-4 rounded-xl shadow-md border">
                            <span class="font-semibold text-lg">Dr. {{ $queue->doctor->name }}</span>

                            <span class="flex items-center gap-3">
                                <span class="text-2xl font-bold text-green-700">{{ $queue->queue_number }}</span>
                                <span @class([
                                    'px-3 py-1.5 text-sm font-bold rounded-lg',
                                    'bg-blue-500 text-white' => $queue->queue_status === 'called',
                                    'bg-yellow-400 text-white' => $queue->queue_status === 'in_progress',
                                ])>
                                    {{ ucfirst(str_replace('_', ' ', $queue->queue_status)) }}
                                </span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="bg-gray-100 border border-gray-300 rounded-xl p-6 text-center mt-8">
                <p class="text-lg text-gray-600">No active queues at the moment.</p>
            </div>
        @endif

    </div>
@endsection