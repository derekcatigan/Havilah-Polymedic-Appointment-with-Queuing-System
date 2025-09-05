{{-- resources\views\doctor\doctor-appointment.blade.php --}}
@extends('layout.layout')

@section('content')
    {{-- Header --}}
    <div class="flex items-center flex-wrap p-5">
        <div>
            <form method="GET" class="flex items-center gap-2">
                <label class="input">
                    <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none"
                            stroke="currentColor">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </g>
                    </svg>
                    <input type="text" name="search" class="w-full" placeholder="Search" value="{{ request('search') }}"
                        autocomplete="off" />
                </label>

                <button type="submit" class="btn btn-sm btn-primary">Search</button>
            </form>
        </div>
    </div>

    {{-- Body --}}
    <div class="w-full">
        <div class="rounded-box border border-base-content/5 bg-base-100">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th>Patient</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $index => $appointment)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                {{ $appointment->patient->name }}
                                <br>
                                <span class="text-sm text-gray-500">
                                    {{ $appointment->starts_at->format('M d, Y h:i A') }}
                                </span>
                            </td>
                            <td>
                                @if ($appointment->status === 'confirmed')
                                    <span class="badge badge-success">Confirmed</span>
                                @elseif ($appointment->status === 'completed')
                                    <span class="badge badge-info">Completed</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($appointment->status) }}</span>
                                @endif
                            </td>
                            <td class="flex justify-center">
                                <a href="{{ route('doctor.appointments.show', $appointment->id) }}"
                                    class="btn btn-sm btn-primary flex items-center">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                                        </svg>
                                    </span>
                                    <span>Details</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No appointments found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-5">
                {{ $appointments->links() }}
            </div>
        </div>
    </div>
@endsection