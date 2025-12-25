{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="space-y-8">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
                <p class="text-sm text-gray-500">
                    Overview & Analytics
                </p>
            </div>
        </div>

        {{-- STATS CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-sm font-medium text-gray-500">Admins</h2>
                <p class="mt-2 text-3xl font-bold text-blue-600">
                    {{ $roleCounts['admin'] ?? 0 }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-sm font-medium text-gray-500">Doctors</h2>
                <p class="mt-2 text-3xl font-bold text-green-600">
                    {{ $roleCounts['doctor'] ?? 0 }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-sm font-medium text-gray-500">Staff</h2>
                <p class="mt-2 text-3xl font-bold text-yellow-600">
                    {{ $roleCounts['staff'] ?? 0 }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-sm font-medium text-gray-500">Patients</h2>
                <p class="mt-2 text-3xl font-bold text-red-600">
                    {{ $roleCounts['patient'] ?? 0 }}
                </p>
            </div>
        </div>

        {{-- ANALYTICS CHART --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    Monthly Appointment Bookings
                </h2>
                <p class="text-sm text-gray-500">
                    Total appointments per month ({{ now()->year }})
                </p>
            </div>

            <div class="relative h-[350px]">
                <canvas id="appointmentsChart"></canvas>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        const ctx = document.getElementById('appointmentsChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Appointments',
                    data: @json($totals),
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
@endsection