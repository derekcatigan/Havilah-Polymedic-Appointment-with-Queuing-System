{{-- resources\views\admin\view-patient.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="p-6 space-y-6">

        {{-- 🔹 Breadcrumb / Header --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 2a8 8 0 00-8 8 8 8 0 1016 0 8 8 0 00-8-8zM7 9a1 1 0 112 0 1 1 0 01-2 0zm4 0a1 1 0 112 0 1 1 0 01-2 0zm-2 3.5a5.978 5.978 0 01-3.29-.97.75.75 0 11.82-1.26A4.48 4.48 0 0010 11.5c1.07 0 2.09-.37 2.77-1.23a.75.75 0 111.15.96A5.978 5.978 0 0110 12.5z"
                            clip-rule="evenodd" />
                    </svg>
                    Patient Details
                </h2>
                <p class="text-sm text-gray-500">View patient information and appointment history</p>
            </div>

            <a href="{{ route('admin.manage.patient') }}" class="btn btn-ghost btn-sm">
                ← Back
            </a>
        </div>

        {{-- 🔹 Patient Info Card --}}
        <div class="card bg-base-100 shadow-md border border-gray-200">
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Patient Number</p>
                    <h3 class="font-semibold">{{ $patient->patient_number }}</h3>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Full Name</p>
                    <h3 class="font-semibold">{{ $patient->name }}</h3>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <h3 class="font-semibold">{{ $patient->email }}</h3>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Phone</p>
                    <h3 class="font-semibold">{{ $patient->contact_number }}</h3>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Address</p>
                    <h3 class="font-semibold">{{ $patient->address }}</h3>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    @if ($patient->status === 'active')
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-warning">Inactive</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- 🔹 Appointments Table --}}
        <div class="card bg-base-100 shadow-md border border-gray-200">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-3">Appointment History</h3>

                @if ($appointments->isEmpty())
                    <p class="text-gray-500 text-center py-6">No appointment records found.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Visit Date</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                    <th>Services</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($appointments as $index => $appointment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $appointment->visit_datetime?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'badge-warning',
                                                    'completed' => 'badge-success',
                                                    'cancelled' => 'badge-error',
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusColors[$appointment->status] ?? 'badge-ghost' }}">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $appointment->reason ?? '—' }}</td>
                                        <td>
                                            @if ($appointment->serviceTypes->isEmpty())
                                                <span class="text-gray-400">No Services</span>
                                            @else
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach ($appointment->serviceTypes as $service)
                                                        <span class="badge badge-outline badge-sm">{{ $service->short_description }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection