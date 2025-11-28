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
        </aside>

        <!-- Doctor Cards -->
        <main class="flex-1 p-6">
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