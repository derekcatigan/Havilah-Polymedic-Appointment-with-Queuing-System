{{-- resources\views\patient\my-apppointments.blade.php --}}
@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('assets/css/home-style.css') }}">
@endsection

@section('content')
    @include('partials.header')

    <div class="max-w-5xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6">My Appointments</h2>

        @forelse ($appointments as $appointment)
            <div class="bg-white border border-gray-200 shadow-md rounded-xl p-5 mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">
                            {{ $appointment->doctor->name }}
                        </h3>
                        <p class="text-gray-600 text-sm">
                            {{ Str::title($appointment->doctor->doctor->specialty ?? 'N/A') }}
                        </p>
                        <span @class([
                            'badge',
                            'badge-success' => $appointment->doctor->doctor?->status === 'available',
                            'badge-error' => $appointment->doctor->doctor?->status === 'unavailable',
                            'badge-neutral' => !$appointment->doctor->doctor?->status,
                        ])>
                            {{ ucfirst($appointment->doctor->doctor->status ?? 'Not Set') }}
                        </span>
                        <p class="text-sm text-gray-500 mt-2">
                            📅 {{ $appointment->starts_at->format('F d, Y h:i A') }}
                        </p>
                    </div>

                    <div class="text-right">
                        <span @class([
                            'badge',
                            'badge-warning' => $appointment->status === 'pending',
                            'badge-success' => $appointment->status === 'confirmed',
                            'badge-info' => $appointment->status === 'completed',
                            'badge-error' => $appointment->status === 'cancelled',
                        ])>
                            {{ ucfirst($appointment->status) }}
                        </span>

                        @if ($appointment->status === 'pending')
                            <form class="cancelForm mt-3" data-id="{{ $appointment->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-warning w-full">
                                    <span class="cancelText">Cancel Booking</span>
                                    <span class="cancelSpinner loading loading-dots loading-sm hidden"></span>
                                </button>
                            </form>
                        @endif

                        {{-- Delete history button --}}
                        <form class="deleteHistoryForm mt-2" data-id="{{ $appointment->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline btn-error w-full">
                                <span class="deleteText">Delete History</span>
                                <span class="deleteSpinner loading loading-dots loading-sm hidden"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-600">
                No appointments found.
            </div>
        @endforelse
    </div>
    <!-- Delete Confirmation Modal -->
    <input type="checkbox" id="deleteConfirmModal" class="modal-toggle" />
    <div class="modal" role="dialog">
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
        let appointmentToDelete = null;
        $(document).ready(function () {
            $(".menu-toggle").click(function () {
                $(".links-container").toggleClass("active");
            });
        });

        $(document).on('submit', '.cancelForm', function (e) {
            e.preventDefault();

            let form = $(this);
            let appointmentId = form.data('id');
            let button = form.find('button');
            let text = form.find('.cancelText');
            let spinner = form.find('.cancelSpinner');

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

                    form.replaceWith(`
                                                                                            <span class="badge badge-error">
                                                                                                Cancelled
                                                                                            </span>
                                                                                        `);
                },
                error: function (xhr) {
                    $.toast({
                        heading: "Error",
                        icon: "error",
                        text: xhr.responseJSON.message || "Failed to cancel booking",
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

        $(document).on('submit', '.deleteHistoryForm', function (e) {
            e.preventDefault();
            appointmentToDelete = $(this);
            $("#deleteConfirmModal").prop("checked", true);
        });

        $("#confirmDeleteBtn").click(function () {
            if (!appointmentToDelete) return;

            let form = appointmentToDelete;
            let appointmentId = form.data('id');
            let button = form.find('button');
            let text = form.find('.deleteText');
            let spinner = form.find('.deleteSpinner');

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

                    form.closest('.bg-white').fadeOut(500, function () {
                        $(this).remove();
                    });

                    $("#deleteConfirmModal").prop("checked", false);
                },
                error: function (xhr) {
                    $.toast({
                        heading: "Error",
                        icon: "error",
                        text: xhr.responseJSON.message || "Failed to delete history",
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
                    appointmentToDelete = null;
                }
            });
        });
    </script>
@endsection