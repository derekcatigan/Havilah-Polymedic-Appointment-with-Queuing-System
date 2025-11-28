{{-- resources\views\patient\book-appointment.blade.php --}}
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
                {{-- Doctor Info --}}
                <div class="flex items-center gap-4">
                    <div class="w-15 h-15 rounded-full overflow-hidden flex-shrink-0">
                        @if($users->doctor->profile_picture)
                            <img src="{{ asset('storage/' . $users->doctor->profile_picture) }}" alt="{{ $users->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <div
                                class="w-full h-full flex items-center justify-center bg-gray-300 text-gray-600 text-2xl font-bold">
                                {{ strtoupper(substr($users->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <div>
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

                {{-- Appointment Section --}}
                <div class="w-full mt-8">

                    @if ($appointment && $appointment->status === 'pending')
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
                        <button class="btn btn-block btn-sm btn-success mt-5" disabled>Booked</button>

                    @else
                        @if ($users->doctor->status === 'available')
                            <form id="bookForm" autocomplete="off">
                                @csrf

                                {{-- Month Navigation --}}
                                <div class="flex justify-between items-center mb-2 px-2">
                                    <button type="button" id="prevMonthBtn" class="btn btn-sm">← Previous</button>
                                    <h3 class="text-lg font-semibold text-gray-700" id="currentMonthLabel"></h3>
                                    <button type="button" id="nextMonthBtn" class="btn btn-sm">Next →</button>
                                </div>
                                <small class="text-gray-500 mb-3 block">Click a date to select it</small>

                                {{-- Calendar Grid --}}
                                <div id="patientCalendar" class="grid grid-cols-7 gap-2 border rounded bg-white p-2"></div>

                                {{-- Selected Date --}}
                                <div class="mt-3 flex items-center gap-3">
                                    <div class="text-sm">Selected:</div>
                                    <div id="selectedSlotLabel" class="text-sm font-medium text-blue-600">None</div>
                                </div>

                                <div class="mt-2">
                                    <span class="text-sm text-gray-600">Active Queues:</span>
                                    <span class="text-sm font-semibold text-red-600" id="queueCountLabel">0</span>
                                </div>


                                <input type="hidden" id="starts_at" name="starts_at" required>

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
            const schedules = @json($schedules); // array of available dates (Y-m-d)
            let selectedDate = null;

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });

            let currentYear = new Date().getFullYear();
            let currentMonth = new Date().getMonth() + 1; // JS months 0-11

            const calendarEl = $('#patientCalendar');
            const monthLabel = $('#currentMonthLabel');

            function renderCalendar(year, month) {
                calendarEl.empty();
                const dateObj = new Date(year, month - 1);
                monthLabel.text(dateObj.toLocaleString('default', { month: 'long', year: 'numeric' }));

                const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                days.forEach(d => calendarEl.append(`<div class="text-center py-2 font-medium text-sm border-b">${d}</div>`));

                const firstOfMonth = new Date(year, month - 1, 1);
                const lastOfMonth = new Date(year, month, 0);
                const firstWeekday = firstOfMonth.getDay();

                // Empty cells
                for (let i = 0; i < firstWeekday; i++) {
                    calendarEl.append('<div class="min-h-[120px] p-2 rounded-lg border bg-gray-100"></div>');
                }

                // Dates
                for (let d = 1; d <= lastOfMonth.getDate(); d++) {
                    const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                    const isPast = new Date(dateStr) < new Date(new Date().toISOString().split('T')[0]);
                    const hasSchedule = schedules.includes(dateStr);
                    const disabled = isPast || !hasSchedule;

                    const dayHtml = `
                                                <div class="min-h-[120px] relative p-3 rounded-lg border flex flex-col justify-between cursor-pointer transition
                                                    ${disabled ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : 'bg-white hover:shadow-md'}"
                                                    data-date="${dateStr}" data-disabled="${disabled ? '1' : '0'}">
                                                    <div class="flex items-start justify-between">
                                                        <div class="text-sm font-semibold">${d}</div>
                                                        ${isPast ? '<div class="text-xs text-gray-400">Past</div>' : (!hasSchedule ? '<div class="text-xs text-red-500">No Schedule</div>' : '')}
                                                    </div>
                                                    <div class="mt-2">
                                                        <span class="text-xs font-medium status-label ${disabled ? 'text-red-500' : 'text-green-600'}">
                                                            ${disabled ? (isPast ? 'Unavailable' : 'No Schedule') : 'Available'}
                                                        </span>
                                                    </div>
                                                    <div class="mt-3 text-center">
                                                        ${!disabled ? `<button type="button" class="select-date-btn btn btn-sm btn-outline w-full text-xs" data-date="${dateStr}">Select date</button>` : ''}
                                                    </div>
                                                </div>
                                            `;
                    calendarEl.append(dayHtml);
                }
            }

            // Initial render
            renderCalendar(currentYear, currentMonth);

            // Month navigation
            $('#prevMonthBtn').click(() => {
                currentMonth--;
                if (currentMonth < 1) { currentMonth = 12; currentYear--; }
                renderCalendar(currentYear, currentMonth);
            });
            $('#nextMonthBtn').click(() => {
                currentMonth++;
                if (currentMonth > 12) { currentMonth = 1; currentYear++; }
                renderCalendar(currentYear, currentMonth);
            });

            // Select date
            $(document).on('click', '.select-date-btn', function () {
                const date = $(this).data('date');

                // Remove previous highlights
                $('#patientCalendar [data-date]').removeClass('ring-2 ring-blue-300 bg-blue-50');
                $(this).closest('[data-date]').addClass('ring-2 ring-blue-300 bg-blue-50');

                selectedDate = date;

                const formattedDate = new Date(date).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                $('#selectedSlotLabel').text(formattedDate);

                // 🔵 NEW FEATURE: Load queue count via AJAX
                $.get('/queue/count', {
                    doctor_id: doctorId,
                    date: date
                })
                    .done(res => {
                        $('#queueCountLabel').text(res.count);
                    })
                    .fail(() => {
                        $('#queueCountLabel').text('0');
                    });
            });


            // Booking submission
            $('#bookForm').on('submit', function (e) {
                e.preventDefault();
                if (!selectedDate) {
                    $.toast({ heading: 'Error', icon: 'error', text: 'Please select a date.' });
                    return;
                }

                const btn = $('#bookBtn');
                btn.prop('disabled', true);
                $('#buttonText').addClass('hidden');
                $('#spinner').removeClass('hidden');

                const payload = {
                    reason: $('#reason').val(),
                    starts_at: selectedDate + ' 09:00',
                    ends_at: selectedDate + ' 09:30',
                    _token: csrfToken
                };

                $.post(`/book/appointment/${doctorId}`, payload)
                    .done(res => {
                        $.toast({ heading: 'Success', icon: 'success', text: res.message, position: 'top-right' });
                        setTimeout(() => window.location.reload(), 600);
                    })
                    .fail(xhr => {
                        $.toast({ heading: 'Error', icon: 'error', text: xhr.responseJSON?.message || 'Booking failed.' });
                    })
                    .always(() => {
                        btn.prop('disabled', false);
                        $('#buttonText').removeClass('hidden');
                        $('#spinner').addClass('hidden');
                    });
            });

            // Cancel booking
            $('#cancelForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#cancelBtn').data('id');
                $.ajax({
                    url: `/cancel/appointment/${id}`,
                    type: 'DELETE',
                    success: function (res) {
                        $.toast({ heading: 'Success', icon: 'success', text: res.message, position: 'top-right' });
                        setTimeout(() => window.location.reload(), 600);
                    },
                    error: function () {
                        $.toast({ heading: 'Error', icon: 'error', text: 'Cancel failed.' });
                    }
                });
            });
        })();
    </script>
@endsection