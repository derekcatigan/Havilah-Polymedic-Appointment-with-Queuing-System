{{-- resources/views/patient/book-appointment.blade.php --}}
@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('assets/css/home-style.css') }}">
@endsection

@section('content')
    @include('partials.header')

    <div class="flex justify-center">
        <div class="min-w-[1000px] min-h-screen p-5">
            <h2 class="text-xl font-semibold mb-3">Book Appointment</h2>

            <div class="p-5 bg-white border border-gray-300 shadow rounded-md">
                <div class="flex items-center gap-2">
                    <div class="w-15 h-15 rounded-full overflow-hidden flex-shrink-0">
                        @if($users->doctor->profile_picture)
                            <img src="{{ asset('storage/' . $users->doctor->profile_picture) }}" alt="{{ $users->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-300 text-gray-600 text-2xl font-bold">
                                {{ strtoupper(substr($users->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <div class="leading-0">
                        <div class="flex items-center gap-2">
                            <p class="text-lg font-semibold">{{ $users->name }}</p>
                            <span
                                class="badge {{ $users->doctor->status === 'available' ? 'badge badge-sm badge-soft badge-success border border-green-400' : 'badge badge-sm badge-soft badge-error border border-red-400' }}">
                                {{ $users->doctor->status }}
                            </span>
                        </div>
                        <span class="label text-sm">{{ Str::title($users->doctor->specialty) }}</span>
                    </div>
                </div>

                <div class="w-full mt-10">
                    {{-- 3 states: booked pending, booked confirmed, or booking form --}}
                    @if ($appointment && $appointment->status === 'pending')
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

                    @elseif ($appointment && $appointment->status === 'confirmed')
                        {{-- Disabled Booked Button --}}
                        <button class="btn btn-block btn-sm btn-success mt-5" disabled>Booked</button>

                    @else
                        {{-- Booking form (shown when no appointment OR completed/cancelled) --}}
                        @if ($users->doctor->status === 'available')
                            <form id="bookForm" autocomplete="off">
                                @csrf

                                @php
                                    $firstOfMonth = new DateTime('first day of this month');
                                    $lastOfMonth = new DateTime('last day of this month');

                                    $calendarDates = [];

                                    // Get the weekday index of the first day (0=Sun, 1=Mon, ..., 6=Sat)
                                    $firstWeekday = (int) $firstOfMonth->format('w');

                                    // Add empty padding for days before the 1st
                                    for ($i = 0; $i < $firstWeekday; $i++) {
                                        $calendarDates[] = null;
                                    }

                                    // Add actual dates
                                    for ($d = clone $firstOfMonth; $d <= $lastOfMonth; $d->modify('+1 day')) {
                                        $calendarDates[] = $d->format('Y-m-d');
                                    }
                                @endphp

                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-semibold">{{ date('F Y') }}</h3>
                                        </div>

                                        <div>
                                            <small class="text-gray-500">Click a time slot to select it</small>
                                        </div>
                                    </div>

                                    <!-- Calendar grid -->
                                    <div id="patientCalendar" class="grid grid-cols-7 gap-2 border rounded bg-white p-2">
                                        {{-- Header row: day names --}}
                                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dow)
                                            <div class="text-center py-2 font-medium text-sm border-b">{{ $dow }}</div>
                                        @endforeach

                                        {{-- Date cells --}}
                                        @foreach ($calendarDates as $cdate)
                                            @if (!$cdate)
                                                {{-- Empty cell for padding --}}
                                                <div class="min-h-[120px] p-2 rounded-lg border bg-gray-100"></div>
                                            @else
                                                @php
                                                    $isPast = strtotime($cdate) < strtotime(date('Y-m-d'));
                                                    $dayNum = date('j', strtotime($cdate));
                                                @endphp
                                                <div class="min-h-[120px] relative p-2 rounded-lg border {{ $isPast ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : 'bg-white' }}"
                                                    data-date="{{ $cdate }}" data-disabled="{{ $isPast ? '1' : '0' }}">
                                                    <div class="flex items-start justify-between">
                                                        <div class="text-sm font-semibold">{{ $dayNum }}</div>
                                                        @if($isPast)
                                                            <div class="text-xs text-gray-400">Past</div>
                                                        @endif
                                                    </div>

                                                    {{-- Doctor status placeholder --}}
                                                    <div class="mt-1">
                                                        <span
                                                            class="text-xs font-medium status-label {{ $isPast ? 'text-red-500' : 'text-green-600' }}">
                                                            {{ $isPast ? 'Unavailable' : 'Checking...' }}
                                                        </span>
                                                    </div>

                                                    {{-- Slot container (filled by JS) --}}
                                                    <div class="mt-2 space-y-2 slot-list overflow-auto max-h-40">
                                                        <div class="text-xs text-gray-300">Loading...</div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <!-- legend / selected -->
                                    <div class="mt-3 flex items-center gap-3">
                                        <div class="text-sm">Selected:</div>
                                        <div id="selectedSlotLabel" class="text-sm font-medium text-blue-600">None</div>
                                    </div>
                                </div>

                                <input type="hidden" id="slot" name="slot" required>

                                <fieldset class="fieldset mt-3">
                                    <legend class="fieldset-legend">Purpose <span class="label text-xs">Optional</span></legend>
                                    <textarea class="w-full textarea" id="reason" name="reason" rows="5"
                                        placeholder="Enter purpose here"></textarea>
                                </fieldset>

                                <button type="submit" id="bookBtn" class="btn btn-block btn-sm btn-primary mt-5"
                                    data-id="{{ $users->id }}">
                                    <span id="buttonText">Book</span>
                                    <span id="spinner" class="loading loading-dots loading-sm hidden"></span>
                                </button>
                            </form>
                        @else
                            <button class="btn btn-block btn-sm btn-secondary mt-5" disabled>Doctor Unavailable</button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        (function () {
            const csrfToken = "{{ csrf_token() }}";
            const doctorId = "{{ $users->id }}";
            let currentAppointmentId = "{{ $appointment->id ?? '' }}";
            let currentStatus = "{{ $appointment->status ?? '' }}";

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });

            const calendarSelector = '#patientCalendar';
            const selectedLabel = $('#selectedSlotLabel');
            let selectedSlotRaw = $('#slot').val() || '';

            function formatDisplay(start, end) {
                return `${start} - ${end}`;
            }

            function renderSlotsToCalendar(slots) {
                const grouped = slots.reduce((acc, s) => {
                    if (!s.date) return acc;
                    (acc[s.date] = acc[s.date] || []).push(s);
                    return acc;
                }, {});

                $(calendarSelector).find('[data-date]').each(function () {
                    const $cell = $(this);
                    const date = $cell.data('date');
                    const disabled = $cell.data('disabled') === '1';
                    const $slotList = $cell.find('.slot-list').empty();
                    const $statusLabel = $cell.find('.status-label');

                    const daySlots = (grouped[date] || []).sort((a, b) => a.start_time.localeCompare(b.start_time));

                    // Update doctor status
                    if (disabled || !daySlots.length) {
                        $statusLabel.text('Unavailable').removeClass('text-green-600').addClass('text-red-500');
                    } else {
                        $statusLabel.text('Available').removeClass('text-red-500').addClass('text-green-600');
                    }

                    if (!daySlots.length) {
                        $slotList.append('<div class="text-xs text-gray-300">No slots</div>');
                        return;
                    }

                    daySlots.forEach(slot => {
                        const raw = `${slot.date} ${slot.start_time}|${slot.date} ${slot.end_time}`;
                        const display = formatDisplay(slot.start_time, slot.end_time);
                        const disabledAttr = disabled ? 'disabled' : '';
                        const disabledClass = disabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-100';
                        const selectedClass = (selectedSlotRaw === raw) ? 'bg-blue-500 text-white' : 'bg-white';

                        const $btn = $(`<button type="button" class="w-full text-left text-xs px-2 py-1 border rounded ${disabledClass} ${selectedClass}" data-slot="${raw}" ${disabledAttr}>${display}</button>`);

                        $btn.on('click', function () {
                            if (disabled) return;
                            $(calendarSelector).find('.slot-list button').removeClass('bg-blue-500 text-white');
                            $(this).addClass('bg-blue-500 text-white');
                            const v = $(this).data('slot');
                            $('#slot').val(v);
                            selectedSlotRaw = v;
                            selectedLabel.text(display);
                        });

                        $slotList.append($btn);
                    });
                });
            }

            function loadScheduleSlots() {
                return $.get(`/doctor/${doctorId}/available-slots`)
                    .done(function (slots) {
                        const data = Array.isArray(slots) ? slots : (slots.data || slots.slots || []);
                        renderSlotsToCalendar(data);
                    })
                    .fail(function () {
                        $(calendarSelector).find('.slot-list').each(function () {
                            $(this).html('<div class="text-xs text-red-400">Failed to load</div>');
                        });
                    });
            }

            if (!window.scheduleLoaded) {
                window.scheduleLoaded = true;
                $(function () { loadScheduleSlots(); });
            }

            // --- Booking submit ---
            $(document).on('submit', '#bookForm', function (e) {
                e.preventDefault();
                const btn = $('#bookBtn');
                const slotVal = $('#slot').val() || '';
                if (!slotVal) { $.toast({ heading: 'Error', icon: 'error', text: 'Please select a time slot.' }); return; }

                btn.prop('disabled', true);
                $('#buttonText').addClass('hidden');
                $('#spinner').removeClass('hidden');

                const [rawStart = '', rawEnd = ''] = slotVal.split('|');
                function normalize(raw) {
                    if (!raw) return raw;
                    const parts = raw.trim().split(' ');
                    const date = parts[0];
                    if (parts.length >= 2) {
                        const [h, m] = parts[1].split(':');
                        return `${date} ${String(parseInt(h)).padStart(2, '0')}:${String(parseInt(m || 0)).padStart(2, '0')}`;
                    }
                    return raw;
                }

                const payload = { reason: $('#reason').val(), starts_at: normalize(rawStart), ends_at: normalize(rawEnd), _token: csrfToken };

                $.post(`/book/appointment/${doctorId}`, payload)
                    .done(res => { $.toast({ heading: 'Success', icon: 'success', text: res.message, position: 'top-right' }); setTimeout(() => window.location.reload(), 600); })
                    .fail(xhr => { $.toast({ heading: 'Error', icon: 'error', text: xhr.responseJSON?.message || 'Booking failed.', position: 'top-right' }); })
                    .always(() => { btn.prop('disabled', false); $('#buttonText').removeClass('hidden'); $('#spinner').addClass('hidden'); });
            });

            // --- Cancel appointment ---
            function cancelAppointment(appointmentId, btn, autoCancel = false) {
                $.ajax({ url: `/cancel/appointment/${appointmentId}`, type: 'DELETE' })
                    .done(res => {
                        const msg = autoCancel ? 'Doctor unavailable. Your booking was auto-cancelled.' : res.message;
                        $.toast({ heading: 'Success', icon: 'success', text: msg, position: 'top-right' });
                        setTimeout(() => window.location.reload(), 600);
                    })
                    .fail(() => $.toast({ heading: 'Error', icon: 'error', text: 'Cancel failed.', position: 'top-right' }))
                    .always(() => { if (!autoCancel && btn) { btn.prop('disabled', false); $('#cancelButtonText').removeClass('hidden'); $('#cancelSpinner').addClass('hidden'); } });
            }

            $(document).on('submit', '#cancelForm', function (e) { e.preventDefault(); cancelAppointment($('#cancelBtn').data('id'), $('#cancelBtn')); });

            // --- Auto cancel if doctor unavailable ---
            setInterval(() => {
                if (!currentAppointmentId || currentStatus !== 'pending') return;
                $.get(`/doctor/${doctorId}/status`, function (res) {
                    if (res.status === 'unavailable') cancelAppointment(currentAppointmentId, null, true);
                });
            }, 5000);

        })();
    </script>
@endsection