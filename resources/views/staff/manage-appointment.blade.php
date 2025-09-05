{{-- resources\views\staff\manage-appointment.blade.php --}}
@extends('layout.layout')

@section('content')
    {{-- Header --}}
    <div class="flex items-center flex-wrap p-5 gap-3">
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

        {{-- Walk-in Button --}}
        <div>
            <a href="{{ route('walkin.create') }}" class="btn btn-sm btn-success">
                + Walk-in
            </a>
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
                        <th>Patient Status</th>
                        <th>Doctor</th>
                        <th>Doctor Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $index => $appointment)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 flex-shrink-0">
                                        {{ strtoupper(substr($appointment->patient->name, 0, 1)) }}
                                    </div>
                                    <div class="flex flex-col">
                                        {{ $appointment->patient->name }}
                                        <span class="text-sm text-gray-500">
                                            {{ $appointment->starts_at->format('M d, Y h:i A') }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 flex-shrink-0">
                                        {{ strtoupper(substr($appointment->doctor->name, 0, 1)) }}
                                    </div>
                                    {{ $appointment->doctor->name }}
                                </div>
                            </td>
                            <td>
                                <span
                                    class="badge {{ $appointment->doctor->doctor->status === 'available' ? 'badge-success' : 'badge-error' }}">
                                    {{ ucfirst($appointment->doctor->doctor->status) }}
                                </span>
                            </td>
                            <td class="text-center flex justify-center gap-2">
                                <a href="{{ route('staff.appointments.show', $appointment->id) }}"
                                    class="btn btn-sm btn-primary">Details</a>

                                <!-- Delete Button opens the modal -->
                                <button type="button" class="btn btn-sm btn-error"
                                    onclick="document.getElementById('deleteModal-{{ $appointment->id }}').showModal()">
                                    Delete
                                </button>

                                <!-- Modal -->
                                <dialog id="deleteModal-{{ $appointment->id }}" class="modal">
                                    <div class="modal-box">
                                        <h3 class="font-bold text-lg">Confirm Deletion</h3>
                                        <p class="py-4">Are you sure you want to delete the appointment for
                                            <strong>{{ $appointment->patient->name }}</strong>?
                                        </p>
                                        <div class="modal-action">
                                            <form method="dialog">
                                                <!-- Close modal -->
                                                <button class="btn">Cancel</button>
                                            </form>
                                            <!-- Delete form -->
                                            <form action="{{ route('staff.appointments.destroy', $appointment->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-error">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </dialog>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No appointments found</td>
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