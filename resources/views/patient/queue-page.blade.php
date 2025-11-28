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
            <div class="bg-blue-50 border border-blue-200 rounded-lg shadow-md p-5 mb-6">
                <h3 class="text-lg font-semibold text-blue-700 mb-3">Your Queue Today</h3>

                <div class="text-sm space-y-2">
                    <div class="flex justify-between">
                        <span>Doctor:</span>
                        <span class="font-medium">Dr. {{ $patientQueue->doctor->name }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Queue #:</span>
                        <span class="font-bold text-blue-800">{{ $patientQueue->queue_number }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span>Status:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded
                                                @if($patientQueue->queue_status == 'waiting') bg-gray-300 text-gray-800
                                                @elseif($patientQueue->queue_status == 'called') bg-blue-500 text-white
                                                @elseif($patientQueue->queue_status == 'in_progress') bg-yellow-400 text-white
                                                @elseif($patientQueue->queue_status == 'completed') bg-green-500 text-white
                                                @elseif($patientQueue->queue_status == 'skipped') bg-red-500 text-white
                                                @endif">
                            {{ ucfirst(str_replace('_', ' ', $patientQueue->queue_status)) }}
                        </span>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 mb-6 text-center">
                <p class="text-gray-600">You are not in the queue today.</p>
            </div>
        @endif

        {{-- Current Active Queue Numbers --}}
        @if($currentQueues->count())
            <div class="bg-green-50 border border-green-200 rounded-lg shadow-md p-5">
                <h3 class="text-lg font-semibold text-green-700 mb-3">Currently Serving</h3>

                <ul class="space-y-3 text-sm">
                    @foreach($currentQueues as $doctorId => $queues)
                        @php $queue = $queues->first(); @endphp

                        <li class="flex justify-between items-center bg-white p-3 rounded shadow-sm border">
                            <span class="font-medium">Dr. {{ $queue->doctor->name }}</span>

                            <span class="flex items-center gap-2">
                                <span class="font-bold text-green-700">{{ $queue->queue_number }}</span>

                                <span class="px-2 py-1 text-xs font-semibold rounded
                                                                    @if($queue->queue_status == 'called') bg-blue-500 text-white
                                                                    @elseif($queue->queue_status == 'in_progress') bg-yellow-400 text-white
                                                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $queue->queue_status)) }}
                                </span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 text-center mt-6">
                <p class="text-gray-600">No active queues at the moment.</p>
            </div>
        @endif

    </div>
@endsection