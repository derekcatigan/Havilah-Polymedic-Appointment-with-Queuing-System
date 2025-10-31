{{-- resources\views\patient\book-appointment.blade.php --}}
@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('assets/css/home-style.css') }}">
@endsection

@section('content')
    @include('partials.header')

    <div class="flex justify-center">
        <div class="min-w-[1000px] min-h-screen p-5">
            <h2 class="text-xl font-semibold mb-3">Book Appoinment</h2>
            <div class="p-5 bg-white border border-gray-300 shadow rounded-md">
                <div class="flex items-center gap-2">
                    <div
                        class="w-15 h-15 rounded-full text-2xl bg-gray-300 flex items-center justify-center text-gray-600 flex-shrink-0">
                        {{ strtoupper(substr($users->name, 0, 1)) }}
                    </div>

                    <div class="leading-0">
                        <div class="flex items-center gap-2">
                            <p class="text-lg font-semibold">{{ $users->name }}</p>
                            <span
                                class="badge {{ $users->doctor->status === 'available' ? 'badge badge-sm badge-soft badge-success border border-green-400' : 'badge badge-sm badge-soft badge-error border border-red-400' }}">{{ $users->doctor->status }}</span>
                        </div>
                        <span class="label text-sm">{{ Str::title($users->doctor->specialty) }}</span>
                    </div>
                </div>
                <div class="w-full mt-10">
                    @if ($appointment)
                        @if ($appointment->status === 'pending')
                            {{-- Cancel Booking Button --}}
                            <form id="cancelForm" autocomplete="off">
                                @csrf
                                @method('DELETE')
                                <button type="submit" id="cancelBtn" class="btn btn-block btn-sm btn-warning mt-5"
                                    data-id="{{ $appointment->id }}">
                                    <span id="cancelButtonText">Cancel Booking</span>
                                    <span id="cancelSpinner" class="loading loading-dots loading-sm hidden"></span>
                                </button>
                            </form>
                        @elseif ($appointment->status === 'confirmed')
                            {{-- Disabled Booked Button --}}
                            <button class="btn btn-block btn-sm btn-success mt-5" disabled>
                                Booked
                            </button>
                        @elseif (in_array($appointment->status, ['completed', 'cancelled']))
                            {{-- Revert back to booking form --}}
                            <form id="bookForm" autocomplete="off">
                                @csrf
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">Select Schedule</legend>
                                    <select id="slot" name="slot" class="select w-full" required>
                                        <option value="">Select a time slot</option>
                                    </select>
                                </fieldset>

                                <fieldset class="fieldset mt-3">
                                    <legend class="fieldset-legend">Reason <span class="label text-xs">Optional</span></legend>
                                    <textarea class="w-full textarea" id="reason" name="reason" rows="5"
                                        placeholder="Enter reason here"></textarea>
                                </fieldset>

                                <button type="submit" id="bookBtn" class="btn btn-block btn-sm btn-primary mt-5"
                                    data-id="{{ $users->id }}">
                                    <span id="buttonText">Book</span>
                                    <span id="spinner" class="loading loading-dots loading-sm hidden"></span>
                                </button>
                            </form>
                        @endif
                    @else
                        @if ($users->doctor->status === 'available')
                            {{-- Show booking form --}}
                            <form id="bookForm" autocomplete="off">
                                @csrf
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">Select Schedule</legend>
                                    <select id="slot" name="slot" class="select w-full" required>
                                        <option value="">Select a time slot</option>
                                    </select>
                                </fieldset>

                                <fieldset class="fieldset mt-3">
                                    <legend class="fieldset-legend">Reason <span class="label text-xs">Optional</span></legend>
                                    <textarea class="w-full textarea" id="reason" name="reason" rows="5"
                                        placeholder="Enter reason here"></textarea>
                                </fieldset>

                                <button type="submit" id="bookBtn" class="btn btn-block btn-sm btn-primary mt-5"
                                    data-id="{{ $users->id }}">
                                    <span id="buttonText">Book</span>
                                    <span id="spinner" class="loading loading-dots loading-sm hidden"></span>
                                </button>
                            </form>
                        @else
                            {{-- Disabled button --}}
                            <button class="btn btn-block btn-sm btn-secondary mt-5" disabled>
                                Doctor Unavailable
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        const csrfToken = "{{ csrf_token() }}";
        const doctorId = "{{ $users->id }}";
        let currentAppointmentId = "{{ $appointment->id ?? '' }}";
        let currentStatus = "{{ $appointment->status ?? '' }}";

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });

        /** ------------ UI Renderers ------------ **/
        function renderBooked(appointmentId, status = 'pending') {
            currentAppointmentId = appointmentId;
            currentStatus = status;

            if (status === 'confirmed') {
                $("#bookForm, #cancelForm").replaceWith(`
                                                            <button class="btn btn-block btn-sm btn-success mt-5" disabled>Booked</button>
                                                        `);
            } else {
                $("#bookForm, #cancelForm").replaceWith(`
                                                            <form id="cancelForm" autocomplete="off">
                                                                <input type="hidden" name="_method" value="DELETE">
                                                                <button type="submit" id="cancelBtn" class="btn btn-block btn-sm btn-warning mt-5" data-id="${appointmentId}">
                                                                    <span id="cancelButtonText">Cancel Booking</span>
                                                                    <span id="cancelSpinner" class="loading loading-dots loading-sm hidden"></span>
                                                                </button>
                                                            </form>
                                                        `);
            }
        }

        function renderAvailable() {
            currentAppointmentId = '';
            currentStatus = '';

            $("#cancelForm, #bookForm").replaceWith(`
                                                        <form id="bookForm" autocomplete="off">
                                                            <fieldset class="fieldset">
                                                                <legend class="fieldset-legend">Reason <span class="label text-xs">Optional</span></legend>
                                                                <textarea class="w-full textarea" id="reason" name="reason" rows="5" placeholder="Enter reason here"></textarea>
                                                            </fieldset>
                                                            <button type="submit" id="bookBtn" class="btn btn-block btn-sm btn-primary mt-5" data-id="${doctorId}">
                                                                <span id="buttonText">Book</span>
                                                                <span id="spinner" class="loading loading-dots loading-sm hidden"></span>
                                                            </button>
                                                        </form>
                                                    `);
        }

        function renderUnavailable() {
            currentAppointmentId = '';
            currentStatus = 'cancelled';

            $("#bookForm, #cancelForm").replaceWith(`
                                                        <button class="btn btn-block btn-sm btn-secondary mt-5" disabled>Doctor Unavailable</button>
                                                    `);
        }

        /** ------------ Actions ------------ **/
        // function bookAppointment(formData, btn) {
        //     $.post(`/book/appointment/${doctorId}`, formData)
        //         .done(res => {
        //             $.toast({ heading: 'Success', icon: 'success', text: res.message, position: 'top-right' });
        //             renderBooked(res.appointment_id, res.status || 'pending');
        //         })
        //         .fail(xhr => {
        //             const error = xhr.responseJSON?.message || "Booking failed.";
        //             $.toast({ heading: 'Error', icon: 'error', text: error, position: 'top-right' });
        //         })
        //         .always(() => {
        //             btn.prop("disabled", false);
        //             $("#buttonText").removeClass("hidden");
        //             $("#spinner").addClass("hidden");
        //         });
        // }

        function cancelAppointment(appointmentId, btn, autoCancel = false) {
            $.ajax({ url: `/cancel/appointment/${appointmentId}`, type: "DELETE" })
                .done(res => {
                    const msg = autoCancel ? "Doctor unavailable. Your booking was auto-cancelled." : res.message;
                    $.toast({ heading: 'Success', icon: 'success', text: msg, position: 'top-right' });

                    // If doctor available → show booking form, else show unavailable
                    if ("{{ $users->doctor->status }}" === "available" && !autoCancel) {
                        renderAvailable();
                    } else {
                        renderUnavailable();
                    }
                })
                .fail(xhr => {
                    $.toast({ heading: 'Error', icon: 'error', text: xhr.responseJSON?.message || "Cancel failed.", position: 'top-right' });
                })
                .always(() => {
                    if (!autoCancel) {
                        btn.prop("disabled", false);
                        $("#cancelButtonText").removeClass("hidden");
                        $("#cancelSpinner").addClass("hidden");
                    }
                });
        }

        $.get(`/doctor/${doctorId}/available-slots`, function (slots) {
            let options = slots.map(slot => {
                return `<option value="${slot.date} ${slot.start_time}|${slot.end_time}">
                            ${slot.date} — ${slot.start_time} to ${slot.end_time}
                        </option>`;
            }).join('');
            $("#slot").append(options);
        });

        /** ------------ Event Bindings ------------ **/
        $(document).on("submit", "#bookForm", function (e) {
            e.preventDefault();
            const btn = $("#bookBtn");
            btn.prop("disabled", true);
            $("#buttonText").addClass("hidden");
            $("#spinner").removeClass("hidden");

            const [starts_at, ends_at] = $("#slot").val().split("|");
            const data = {
                reason: $("#reason").val(),
                starts_at,
                ends_at,
                _token: "{{ csrf_token() }}"
            };

            $.post(`/book/appointment/${doctorId}`, data)
                .done(res => {
                    $.toast({ heading: 'Success', icon: 'success', text: res.message, position: 'top-right' });
                    location.reload();
                })
                .fail(xhr => {
                    $.toast({ heading: 'Error', icon: 'error', text: xhr.responseJSON?.message || "Booking failed.", position: 'top-right' });
                })
                .always(() => {
                    btn.prop("disabled", false);
                    $("#buttonText").removeClass("hidden");
                    $("#spinner").addClass("hidden");
                });
        });

        $(document).on("submit", "#cancelForm", function (e) {
            e.preventDefault();
            const btn = $("#cancelBtn");
            const appointmentId = btn.data("id");

            btn.prop("disabled", true);
            $("#cancelButtonText").addClass("hidden");
            $("#cancelSpinner").removeClass("hidden");

            cancelAppointment(appointmentId, btn);
        });

        /** ------------ Auto-Cancel If Doctor Unavailable ------------ **/
        setInterval(() => {
            if (!currentAppointmentId || currentStatus !== 'pending') return;

            $.get(`/doctor/${doctorId}/status`, res => {
                if (res.status === "unavailable") {
                    cancelAppointment(currentAppointmentId, null, true); // auto-cancel
                }
            });
        }, 5000); // every 5s
    </script>
@endsection