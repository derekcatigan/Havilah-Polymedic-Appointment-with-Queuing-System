{{-- resources\views\admin\dashboard.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="p-5 bg-white rounded-lg shadow">
            <h2 class="text-lg font-semibold">Admins</h2>
            <p class="text-3xl font-bold text-blue-600">
                {{ $roleCounts['admin'] ?? 0 }}
            </p>
        </div>

        <div class="p-5 bg-white rounded-lg shadow">
            <h2 class="text-lg font-semibold">Doctors</h2>
            <p class="text-3xl font-bold text-green-600">
                {{ $roleCounts['doctor'] ?? 0 }}
            </p>
        </div>

        <div class="p-5 bg-white rounded-lg shadow">
            <h2 class="text-lg font-semibold">Staff</h2>
            <p class="text-3xl font-bold text-yellow-600">
                {{ $roleCounts['staff'] ?? 0 }}
            </p>
        </div>

        <div class="p-5 bg-white rounded-lg shadow">
            <h2 class="text-lg font-semibold">Patients</h2>
            <p class="text-3xl font-bold text-red-600">
                {{ $roleCounts['patient'] ?? 0 }}
            </p>
        </div>
    </div>
@endsection