{{-- resources\views\home-doctor.blade.php --}}
@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('assets/css/home-style.css') }}">
@endsection

@section('content')
    @include('partials.header')

    <div class="flex min-h-screen bg-gray-50">
        <!-- Sidebar Filters -->
        <aside class="w-72 bg-white border-r border-r-gray-200 p-5">
            <h3 class="text-lg font-semibold mb-4">Filter by Specialty</h3>
            <div class="flex flex-col space-y-2">
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
            <section class="flex justify-center">
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
                            <h2 class="text-lg font-semibold text-gray-800">Dr. {{ $doctor->user->name }}</h2>
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