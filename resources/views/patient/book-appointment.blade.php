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
                                <fieldset class="fieldset mb-4">
                                    <legend class="fieldset-legend">Select Schedule</legend>
                                    <div id="calendarView"
                                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                        <!-- Slots will be loaded here dynamically -->
                                    </div>

                                    <!-- Hidden input to store the selected value -->
                                    <input type="hidden" id="slot" name="slot" required>
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
                                <fieldset class="fieldset mb-4">
                                    <legend class="fieldset-legend">Select Schedule</legend>
                                    <div id="calendarView"
                                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                        <!-- Slots will be loaded here dynamically -->
                                    </div>

                                    <!-- Hidden input to store the selected value -->
                                    <input type="hidden" id="slot" name="slot" required>
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
        /** ------------ Global Variables ------------ **/
        const csrfToken = "{{ csrf_token() }}";
        const doctorId = "{{ $users->id }}";
        let currentAppointmentId = "{{ $appointment->id ?? '' }}";
        let currentStatus = "{{ $appointment->status ?? '' }}";

        /** ------------ AJAX Setup ------------ **/
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });

        /** =====================================================
         *  FETCH & RENDER DOCTOR'S AVAILABLE SCHEDULE SLOTS
         *  ===================================================== **/
        function loadScheduleSlots() {
            $.get(`/doctor/${doctorId}/available-slots`, function (slots) {
                const grouped = {};

                // Group schedules by day
                slots.forEach(slot => {
                    if (!grouped[slot.day_of_week]) grouped[slot.day_of_week] = [];
                    grouped[slot.day_of_week].push(slot);
                });

                // Build schedule cards
                let html = '';
                for (const [day, times] of Object.entries(grouped)) {
                    html += `
                                        <div class="border border-gray-300 rounded-lg p-3 bg-white shadow-sm">
                                            <h3 class="font-semibold text-blue-600 mb-2">${day}</h3>
                                            <div class="flex flex-wrap gap-2">
                                                ${times.map(t => `
                                                    <button type="button"
                                                        class="time-slot border border-blue-300 text-sm px-3 py-1 rounded hover:bg-blue-100 transition"
                                                        data-slot="${t.date} ${t.start_time}|${t.date} ${t.end_time}">
                                                        ${t.start_time} - ${t.end_time}
                                                    </button>
                                                `).join('')}
                                            </div>
                                        </div>
                                    `;
                }

                $('#calendarView').html(html);
            });
        }

        /** =====================================================
         *  UI STATE HANDLERS
         *  ===================================================== **/

        // When appointment exists
        function renderBooked(appointmentId, status = 'pending') {
            currentAppointmentId = appointmentId;
            currentStatus = status;

            const content = (status === 'confirmed')
                ? `<button class="btn btn-block btn-sm btn-success mt-5" disabled>Booked</button>`
                : `
                                    <form id="cancelForm" autocomplete="off">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" id="cancelBtn" class="btn btn-block btn-sm btn-warning mt-5" data-id="${appointmentId}">
                                            <span id="cancelButtonText">Cancel Booking</span>
                                            <span id="cancelSpinner" class="loading loading-dots loading-sm hidden"></span>
                                        </button>
                                    </form>
                                  `;
            $("#bookForm, #cancelForm").replaceWith(content);
        }

        // When appointment is cancelled or no appointment exists
        function renderAvailable() {
            currentAppointmentId = '';
            currentStatus = '';

            $("#cancelForm, #bookForm").replaceWith(`
                                <form id="bookForm" autocomplete="off">
                                    <fieldset class="fieldset mb-4">
                                        <legend class="fieldset-legend">Select Schedule</legend>
                                        <div id="calendarView"
                                            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                        </div>
                                        <input type="hidden" id="slot" name="slot" required>
                                    </fieldset>

                                    <fieldset class="fieldset mt-3">
                                        <legend class="fieldset-legend">Reason <span class="label text-xs">Optional</span></legend>
                                        <textarea class="w-full textarea" id="reason" name="reason" rows="5"
                                            placeholder="Enter reason here"></textarea>
                                    </fieldset>

                                    <button type="submit" id="bookBtn" class="btn btn-block btn-sm btn-primary mt-5" data-id="${doctorId}">
                                        <span id="buttonText">Book</span>
                                        <span id="spinner" class="loading loading-dots loading-sm hidden"></span>
                                    </button>
                                </form>
                            `);

            // Reload schedule after form re-render
            loadScheduleSlots();
        }

        // When doctor is unavailable
        function renderUnavailable() {
            currentAppointmentId = '';
            currentStatus = 'cancelled';

            $("#bookForm, #cancelForm").replaceWith(`
                                <button class="btn btn-block btn-sm btn-secondary mt-5" disabled>
                                    Doctor Unavailable
                                </button>
                            `);
        }

        /** =====================================================
         *  CANCEL APPOINTMENT FUNCTION
         *  ===================================================== **/
        function cancelAppointment(appointmentId, btn, autoCancel = false) {
            $.ajax({ url: `/cancel/appointment/${appointmentId}`, type: "DELETE" })
                .done(res => {
                    const msg = autoCancel
                        ? "Doctor unavailable. Your booking was auto-cancelled."
                        : res.message;

                    $.toast({ heading: 'Success', icon: 'success', text: msg, position: 'top-right' });

                    // Restore booking form if doctor is still available
                    if ("{{ $users->doctor->status }}" === "available" && !autoCancel) {
                        renderAvailable();
                    } else {
                        renderUnavailable();
                    }
                })
                .fail(xhr => {
                    $.toast({
                        heading: 'Error',
                        icon: 'error',
                        text: xhr.responseJSON?.message || "Cancel failed.",
                        position: 'top-right'
                    });
                })
                .always(() => {
                    if (!autoCancel && btn) {
                        btn.prop("disabled", false);
                        $("#cancelButtonText").removeClass("hidden");
                        $("#cancelSpinner").addClass("hidden");
                    }
                });
        }

        /** =====================================================
         *  INITIAL LOAD
         *  ===================================================== **/
        if (!window.scheduleLoaded) {
            window.scheduleLoaded = true;
            loadScheduleSlots();
        }

        /** =====================================================
         *  INTERACTIONS & FORM HANDLING
         *  ===================================================== **/

        // Highlight selected time slot
        $(document).on('click', '.time-slot', function () {
            $('.time-slot').removeClass('bg-blue-500 text-white');
            $(this).addClass('bg-blue-500 text-white');
            $('#slot').val($(this).data('slot'));
        });

        // Convert "YYYY-MM-DD hh:mm AM/PM" or "YYYY-MM-DD HH:mm" -> "YYYY-MM-DD HH:mm" (local)
        function formatLocalDateTime(raw) {
            // raw examples:
            //  "2025-11-03 07:00 AM"
            //  "2025-11-03 07:00" (already 24h)
            if (!raw || typeof raw !== 'string') return raw;

            const parts = raw.trim().split(' ');
            // Expect [date, time] or [date, time, ampm]
            const date = parts[0];
            if (!date) return raw;

            if (parts.length === 3) {
                // has AM/PM
                let time = parts[1];       // e.g. "07:00"
                const ampm = parts[2].toUpperCase(); // "AM" or "PM"
                const [hStr, mStr] = time.split(':');
                let hh = parseInt(hStr, 10);
                const mm = parseInt(mStr, 10);

                if (ampm === 'PM' && hh !== 12) hh += 12;
                if (ampm === 'AM' && hh === 12) hh = 0;

                const hhStr = String(hh).padStart(2, '0');
                const mmStr = String(mm).padStart(2, '0');
                return `${date} ${hhStr}:${mmStr}`;
            }

            // If already in "YYYY-MM-DD HH:mm" format, just return normalized
            if (parts.length >= 2) {
                const time = parts[1];
                // ensure HH:mm is padded
                const [h, m] = time.split(':');
                if (typeof m === 'undefined') return `${date} ${time}`;
                const hh = String(parseInt(h, 10)).padStart(2, '0');
                const mm = String(parseInt(m, 10)).padStart(2, '0');
                return `${date} ${hh}:${mm}`;
            }

            return raw;
        }

        // Book appointment
        $(document).on("submit", "#bookForm", function (e) {
            e.preventDefault();
            const btn = $("#bookBtn");

            btn.prop("disabled", true);
            $("#buttonText").addClass("hidden");
            $("#spinner").removeClass("hidden");

            // #slot contains "YYYY-MM-DD HH:MM AM|YYYY-MM-DD HH:MM AM" (or 24h)
            const slotVal = $("#slot").val() || '';
            const [rawStart = '', rawEnd = ''] = slotVal.split("|");

            const starts_at = formatLocalDateTime(rawStart); // returns "Y-m-d H:i"
            const ends_at = formatLocalDateTime(rawEnd);

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
                    $.toast({
                        heading: 'Error',
                        icon: 'error',
                        text: xhr.responseJSON?.message || "Booking failed.",
                        position: 'top-right'
                    });
                })
                .always(() => {
                    btn.prop("disabled", false);
                    $("#buttonText").removeClass("hidden");
                    $("#spinner").addClass("hidden");
                });
        });

        // Cancel booking manually
        $(document).on("submit", "#cancelForm", function (e) {
            e.preventDefault();
            const btn = $("#cancelBtn");
            cancelAppointment(btn.data("id"), btn);
        });

        /** =====================================================
         *  AUTO-CANCEL IF DOCTOR UNAVAILABLE
         *  ===================================================== **/
        setInterval(() => {
            if (!currentAppointmentId || currentStatus !== 'pending') return;

            $.get(`/doctor/${doctorId}/status`, res => {
                if (res.status === "unavailable") {
                    cancelAppointment(currentAppointmentId, null, true);
                }
            });
        }, 5000); // Every 5 seconds
    </script>
@endsection