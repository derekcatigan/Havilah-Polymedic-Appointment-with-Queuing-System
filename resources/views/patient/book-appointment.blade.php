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

                <div class="w-full mt-8">
                    {{-- 3 states: pending, confirmed, or booking form --}}
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

                                @php
                                    $firstOfMonth = new DateTime('first day of this month');
                                    $lastOfMonth = new DateTime('last day of this month');

                                    $calendarDates = [];
                                    $firstWeekday = (int) $firstOfMonth->format('w');

                                    for ($i = 0; $i < $firstWeekday; $i++) {
                                        $calendarDates[] = null;
                                    }
                                    for ($d = clone $firstOfMonth; $d <= $lastOfMonth; $d->modify('+1 day')) {
                                        $calendarDates[] = $d->format('Y-m-d');
                                    }
                                @endphp

                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="text-lg font-semibold">{{ date('F Y') }}</h3>
                                        <small class="text-gray-500">Click a date to select it</small>
                                    </div>

                                    <div id="patientCalendar" class="grid grid-cols-7 gap-2 border rounded bg-white p-2">
                                        {{-- day names --}}
                                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dow)
                                            <div class="text-center py-2 font-medium text-sm border-b">{{ $dow }}</div>
                                        @endforeach

                                        {{-- date cells --}}
                                        @foreach ($calendarDates as $cdate)
                                            @if (!$cdate)
                                                <div class="min-h-[120px] p-2 rounded-lg border bg-gray-100"></div>
                                            @else
                                                @php
                                                    $isPast = strtotime($cdate) < strtotime(date('Y-m-d'));
                                                    $hasSchedule = in_array($cdate, $schedules);
                                                    $disabled = $isPast || !$hasSchedule;
                                                    $dayNum = date('j', strtotime($cdate));
                                                @endphp
                                                <div class="min-h-[120px] relative p-3 rounded-lg border flex flex-col justify-between cursor-pointer transition
                                                                                                                                        {{ $disabled ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : 'bg-white hover:shadow-md' }}"
                                                    data-date="{{ $cdate }}" data-disabled="{{ $disabled ? '1' : '0' }}">
                                                    <div class="flex items-start justify-between">
                                                        <div class="text-sm font-semibold">{{ $dayNum }}</div>
                                                        @if($isPast)
                                                            <div class="text-xs text-gray-400">Past</div>
                                                        @elseif(!$hasSchedule)
                                                            <div class="text-xs text-red-500">No Schedule</div>
                                                        @endif
                                                    </div>

                                                    <div class="mt-2">
                                                        <span
                                                            class="text-xs font-medium status-label {{ $disabled ? 'text-red-500' : 'text-green-600' }}">
                                                            {{ $disabled ? ($isPast ? 'Unavailable' : 'No Schedule') : 'Available' }}
                                                        </span>
                                                    </div>

                                                    <div class="mt-3 text-center">
                                                        @if(!$disabled)
                                                            <button type="button" class="select-date-btn btn btn-sm btn-outline w-full text-xs"
                                                                data-date="{{ $cdate }}">
                                                                Select date
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <div class="mt-3 flex items-center gap-3">
                                        <div class="text-sm">Selected:</div>
                                        <div id="selectedSlotLabel" class="text-sm font-medium text-blue-600">None</div>
                                    </div>
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
            let selectedDate = $('#starts_at').val() || null;

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });

            $(document).on('click', '.select-date-btn', function () {
                const date = $(this).data('date');
                $('#patientCalendar [data-date]').removeClass('ring-2 ring-blue-300 bg-blue-50');
                $(this).closest('[data-date]').addClass('ring-2 ring-blue-300 bg-blue-50');

                $('#starts_at').val(date);

                // Format selected date to "Weekday, Month Day, Year"
                const formattedDate = new Date(date).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                $('#selectedSlotLabel').text(formattedDate);
                selectedDate = date;
            });


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
                    starts_at: selectedDate,
                    _token: csrfToken
                };

                $.post(`/book/appointment/${doctorId}`, payload)
                    .done(function (res) {
                        $.toast({ heading: 'Success', icon: 'success', text: res.message, position: 'top-right' });
                        setTimeout(() => window.location.reload(), 600);
                    })
                    .fail(function (xhr) {
                        $.toast({ heading: 'Error', icon: 'error', text: xhr.responseJSON?.message || 'Booking failed.' });
                    })
                    .always(function () {
                        btn.prop('disabled', false);
                        $('#buttonText').removeClass('hidden');
                        $('#spinner').addClass('hidden');
                    });
            });

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