{{-- resources/views/patient/my-apppointments.blade.php --}}
@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('assets/css/home-style.css') }}">
    {{-- If you use any extra utility CSS, include here. DaisyUI should already be configured with Tailwind. --}}
@endsection

@section('content')
    @include('partials.header')

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Page header --}}
        <header class="flex items-center justify-between rounded border border-gray-300 mb-6">
            <h1 class="text-2xl font-bold">My Appointments</h1>
            <p class="text-sm text-gray-500">Manage your bookings and history</p>
        </header>

        {{-- Date Filter --}}
        <form method="GET" action="{{ route('patient.appointments') }}"
            class="mb-6 bg-white border border-gray-300 rounded-xl shadow-sm p-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Date:</label>

            <div class="flex items-center gap-3">
                <input type="date" name="date" value="{{ $selectedDate }}" class="input input-bordered w-48" required>

                <button class="btn btn-primary">Filter</button>
            </div>
        </form>

        {{-- Layout: main + sidebar --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left / main column (appointments) --}}
            {{-- Appointment list --}}
            <section class="lg:col-span-2 space-y-4">

                @forelse ($appointments as $appointment)
                    <article
                        class="bg-white border border-gray-300 rounded-lg shadow-sm p-5 flex flex-col md:flex-row items-start md:items-center gap-4">

                        {{-- Doctor Avatar --}}
                        <div class="w-full md:w-28 flex-shrink-0">
                            <div
                                class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center text-xl font-semibold text-gray-700">
                                @if($appointment->doctor->doctor?->profile_picture)
                                    <img src="{{ asset('storage/' . $appointment->doctor->doctor->profile_picture) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    @php
                                        $name = $appointment->doctor->name;
                                        $initials = collect(explode(' ', $name))->map(fn($p) => strtoupper(substr($p, 0, 1)))->join('');
                                    @endphp
                                    <span>{{ $initials }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 w-full">
                            <div class="flex justify-between items-start gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold">Dr. {{ $appointment->doctor->name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ Str::title($appointment->doctor->doctor->specialty ?? 'General') }}
                                    </p>

                                    <div class="mt-3 text-sm text-gray-600 flex flex-col sm:flex-row sm:items-center sm:gap-4">
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 20h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
                                            </svg>

                                            <span>{{ $appointment->starts_at->timezone('Asia/Manila')->format('F d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right flex-shrink-0">

                                    {{-- Status Badge --}}
                                    @php
                                        $badge = [
                                            'pending' => 'badge badge-warning',
                                            'confirmed' => 'badge badge-success',
                                            'completed' => 'badge badge-info',
                                            'cancelled' => 'badge badge-error',
                                        ][$appointment->status] ?? 'badge';
                                    @endphp

                                    <span class="{{ $badge }}">{{ ucfirst($appointment->status) }}</span>

                                    {{-- Buttons --}}
                                    <div class="mt-2">

                                        @if ($appointment->status === 'pending')
                                            <form class="cancelForm w-full" data-id="{{ $appointment->id }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline btn-warning w-full mt-1">
                                                    <span class="cancelText">Cancel Booking</span>
                                                    <span class="cancelSpinner loading loading-dots loading-sm hidden"></span>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- <form class="deleteHistoryForm mt-2 w-full" data-id="{{ $appointment->id }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline btn-error w-full">
                                                <span class="deleteText">Delete History</span>
                                                <span class="deleteSpinner loading loading-dots loading-sm hidden"></span>
                                            </button>
                                        </form> --}}

                                    </div>
                                </div>
                            </div>

                            {{-- Reason --}}
                            @if($appointment->reason)
                                <div class="mt-3 text-sm">
                                    <strong>Purpose:</strong>
                                    <p class="mt-1">{{ $appointment->reason }}</p>
                                </div>
                            @endif

                        </div>
                    </article>

                @empty
                    <div class="p-8 bg-white border rounded-xl text-center text-gray-600">
                        No appointments found for <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</strong>.
                    </div>
                @endforelse

            </section>

            {{-- Right column: helpful panel (summary + tips) --}}
            <aside class="space-y-4">
                <div class="bg-white border border-gray-300 rounded-xl p-4 shadow-sm">
                    <h4 class="font-semibold">Summary</h4>
                    <div class="mt-3 text-sm space-y-2">
                        <div class="flex justify-between">
                            <span>Upcoming</span>
                            <span class="font-medium">{{ $appointments->where('status', 'pending')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Confirmed</span>
                            <span class="font-medium">{{ $appointments->where('status', 'confirmed')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Completed</span>
                            <span class="font-medium">{{ $appointments->where('status', 'completed')->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-300 rounded-xl p-4 shadow-sm">
                    <h4 class="font-semibold">Helpful Tips</h4>
                    <ul class="mt-3 text-sm space-y-2 text-gray-600">
                        <li>Arrive 10 minutes before your appointment.</li>
                        <li>Bring a valid ID and any relevant medical documents.</li>
                        <li>If you need to cancel, use the "Cancel Booking" button.</li>
                        <li>Use the date filter to view past or upcoming appointments.</li>
                    </ul>
                </div>
            </aside>
        </div>
    </main>

    {{-- DaisyUI delete confirmation modal (reused for history deletes) --}}
    <input type="checkbox" id="deleteConfirmModal" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Confirm Delete</h3>
            <p class="py-4">Are you sure you want to remove this appointment from history? This action cannot be undone.</p>
            <div class="modal-action">
                <label for="deleteConfirmModal" class="btn">Cancel</label>
                <button id="confirmDeleteBtn" class="btn btn-error">Yes, Delete</button>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let appointmentToDeleteForm = null;

        $(document).ready(function () {
            // Toggle mobile menu if present in header (keeps existing)
            $(".menu-toggle").click(function () {
                $(".links-container").toggleClass("active");
            });
        });

        // Cancel booking (AJAX delete) - keeps your toast API
        $(document).on('submit', '.cancelForm', function (e) {
            e.preventDefault();

            const form = $(this);
            const appointmentId = form.data('id');
            const button = form.find('button');
            const text = form.find('.cancelText');
            const spinner = form.find('.cancelSpinner');

            button.prop('disabled', true);
            text.addClass('hidden');
            spinner.removeClass('hidden');

            $.ajax({
                type: "DELETE",
                url: "/cancel/appointment/" + appointmentId,
                data: form.serialize(),
                success: function (response) {
                    $.toast({
                        heading: 'Success',
                        icon: 'success',
                        text: response.message,
                        showHideTransition: 'slide',
                        stack: 3,
                        position: 'top-right',
                    });

                    // replace cancel button with cancelled badge
                    form.replaceWith(`
                                                                                                                <div class="text-center">
                                                                                                                    <span class="badge badge-error">Cancelled</span>
                                                                                                                </div>
                                                                                                            `);
                },
                error: function (xhr) {
                    $.toast({
                        heading: "Error",
                        icon: "error",
                        text: xhr.responseJSON?.message || "Failed to cancel booking",
                        showHideTransition: 'slide',
                        stack: 3,
                        position: 'top-right',
                    });
                },
                complete: function () {
                    button.prop('disabled', false);
                    text.removeClass('hidden');
                    spinner.addClass('hidden');
                }
            });
        });

        // Delete history: open confirmation modal first
        $(document).on('submit', '.deleteHistoryForm', function (e) {
            e.preventDefault();
            appointmentToDeleteForm = $(this);
            $("#deleteConfirmModal").prop("checked", true); // opens DaisyUI modal
        });

        // Confirm delete from modal
        $("#confirmDeleteBtn").on('click', function () {
            if (!appointmentToDeleteForm) return;

            const form = appointmentToDeleteForm;
            const appointmentId = form.data('id');
            const button = form.find('button');
            const text = form.find('.deleteText');
            const spinner = form.find('.deleteSpinner');

            button.prop('disabled', true);
            text.addClass('hidden');
            spinner.removeClass('hidden');

            $.ajax({
                type: "DELETE",
                url: "/appointment/history/" + appointmentId,
                data: form.serialize(),
                success: function (response) {
                    $.toast({
                        heading: 'Deleted',
                        icon: 'success',
                        text: response.message,
                        showHideTransition: 'slide',
                        stack: 3,
                        position: 'top-right',
                    });

                    // remove the card
                    form.closest('article, .bg-white').fadeOut(300, function () { $(this).remove(); });

                    $("#deleteConfirmModal").prop("checked", false);
                },
                error: function (xhr) {
                    $.toast({
                        heading: "Error",
                        icon: "error",
                        text: xhr.responseJSON?.message || "Failed to delete history",
                        showHideTransition: 'slide',
                        stack: 3,
                        position: 'top-right',
                    });

                    $("#deleteConfirmModal").prop("checked", false);
                },
                complete: function () {
                    button.prop('disabled', false);
                    text.removeClass('hidden');
                    spinner.addClass('hidden');
                    appointmentToDeleteForm = null;
                }
            });
        });
    </script>
@endsection