{{-- resources\views\home-doctor.blade.php --}}
@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('assets/css/home-style.css') }}">
@endsection

@section('content')
    @include('partials.header')

    <div class="flex min-h-screen bg-gray-50">
        <!-- Sidebar Filters & Queue -->
        <aside class="w-72 bg-white border-r border-gray-200 p-5 flex flex-col h-screen">
            <!-- Filter by Specialty -->
            <div class="flex flex-col space-y-2">
                <h3 class="text-lg font-semibold mb-4">Filter by Specialty</h3>
                <a href="{{ route('home.doctor', ['specialty' => 'all']) }}"
                    class="btn btn-block text-xs p-5 {{ ($specialty ?? 'all') === 'all' ? 'btn-primary' : '' }}">
                    All
                </a>

                @foreach ($specialties as $item)
                    <a href="{{ route('home.doctor', ['specialty' => $item]) }}"
                        class="btn btn-block text-xs p-5 {{ ($specialty ?? '') === $item ? 'btn-primary' : '' }}">
                        {{ Str::title($item) }}
                    </a>
                @endforeach
            </div>

            <!-- Sidebar Queue Section -->
            <div class="mt-auto space-y-4">

                {{-- Patient's Own Queue --}}
                @if($patientQueue)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg shadow p-4">
                        <h3 class="text-sm font-semibold text-blue-700 mb-2">Your Queue Today</h3>
                        <div class="text-sm space-y-1">
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
                                                                {{ $patientQueue->queue_status === 'waiting' ? 'bg-gray-200 text-gray-800' : '' }}
                                                                {{ $patientQueue->queue_status === 'called' ? 'bg-blue-500 text-white' : '' }}
                                                                {{ $patientQueue->queue_status === 'in_progress' ? 'bg-yellow-400 text-white' : '' }}
                                                                {{ $patientQueue->queue_status === 'completed' ? 'bg-green-500 text-white' : '' }}
                                                                {{ $patientQueue->queue_status === 'skipped' ? 'bg-red-500 text-white' : '' }}
                                                            ">
                                    {{ ucfirst(str_replace('_', ' ', $patientQueue->queue_status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Current Active Queue Numbers --}}
                @if($currentQueues->count())
                    <div class="bg-green-50 border border-green-200 rounded-lg shadow p-4">
                        <h3 class="text-sm font-semibold text-green-700 mb-2">Currently Serving</h3>
                        <ul class="space-y-2 text-sm">
                            @foreach($currentQueues as $doctorId => $queues)
                                @php
                                    $queue = $queues->first();
                                @endphp
                                <li class="flex justify-between items-center p-2 bg-white rounded shadow-sm">
                                    <span class="font-medium">Dr. {{ $queue->doctor->name }}</span>
                                    <span class="flex items-center space-x-2">
                                        <span class="font-bold text-green-700">{{ $queue->queue_number }}</span>
                                        <span class="px-2 py-1 text-xs font-semibold rounded
                                                                                                {{ $queue->queue_status === 'called' ? 'bg-blue-500 text-white' : '' }}
                                                                                                {{ $queue->queue_status === 'in_progress' ? 'bg-yellow-400 text-white' : '' }}
                                                                                            ">
                                            {{ ucfirst(str_replace('_', ' ', $queue->queue_status)) }}
                                        </span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </aside>

        <!-- Doctor Cards -->
        <main class="flex-1 p-6">
            <section class="flex justify-center mb-6">
                @php
                    $ads = \App\Models\Ad::where('status', 'active')
                        ->where('position', 'homepage')
                        ->latest()
                        ->get();
                @endphp

                @forelse ($ads as $ad)
                    <a href="{{ $ad->link ?? '#' }}" target="_blank" class="block">
                        <img src="{{ asset('storage/' . $ad->image_path) }}" alt="{{ $ad->title ?? 'Advertisement' }}"
                            class="w-[1000px] h-[280px] object-fill rounded-lg shadow-md border border-gray-300">
                    </a>
                @empty
                    <p class="text-gray-500">No ads available at the moment.</p>
                @endforelse
            </section>

            <h1 class="text-lg font-semibold mb-6">Available Doctors</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($doctors as $doctor)
                    <div class="bg-white shadow rounded-lg border border-gray-200 p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 rounded-full flex-shrink-0 overflow-hidden bg-gray-200 flex items-center justify-center text-white text-sm font-semibold">
                                    @if ($doctor->profile_picture)
                                        <img src="{{ asset('storage/' . $doctor->profile_picture) }}" alt="Profile Picture"
                                            class="w-full h-full object-cover">
                                    @else
                                        @php
                                            $name = $doctor->user->name;
                                            $initials = collect(explode(' ', $name))
                                                ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                                ->join('');
                                        @endphp
                                        <span class="text-gray-700">{{ $initials }}</span>
                                    @endif
                                </div>

                                <h2 class="text-lg font-semibold text-gray-800">Dr. {{ $doctor->user->name }}</h2>
                            </div>

                            <p class="text-sm text-gray-500">
                                <span class="font-medium">{{ Str::title($doctor->specialty) }}</span>
                            </p>
                            <p class="mt-2">
                                <span
                                    class="badge  {{ $doctor->status === 'available' ? 'badge badge-soft badge-success border border-green-400' : 'badge badge-soft badge-error border border-red-400' }}">
                                    {{ ucfirst($doctor->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('book.doctor', $doctor->user->id) }}">
                                <button type="button" class="btn btn-sm btn-block btn-primary text-white rounded-lg">
                                    Book
                                </button>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </main>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $(".menu-toggle").click(function () {
                $(".links-container").toggleClass("active");
            });
        });
    </script>
@endsection