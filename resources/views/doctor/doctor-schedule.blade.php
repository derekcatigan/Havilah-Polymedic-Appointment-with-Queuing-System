{{-- resources/views/doctor/doctor-schedule.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="p-6 bg-white rounded shadow-lg">
        <h1 class="text-2xl font-bold mb-6 text-blue-700">My Schedule</h1>

        @php
            $role = $user->role->value ?? $user->role;
            $tabGroup = 'my_tabs_schedule_' . uniqid();
            $currentYear = date('Y');
            $currentMonth = date('m');
        @endphp

        <!-- Month navigation -->
        <div class="flex justify-between items-center mb-2 px-2">
            <button class="btn btn-sm" id="prevMonthBtn">← Previous</button>
            <h3 class="text-lg font-semibold text-gray-700" id="currentMonthLabel">{{ date('F Y') }}</h3>
            <button class="btn btn-sm" id="nextMonthBtn">Next →</button>
        </div>
        <small class="text-gray-500 mb-3 block">Click a date to add schedule (AM / PM)</small>

        <!-- Calendar grid -->
        <div id="calendarGrid" class="grid grid-cols-7 gap-3">
            {{-- Dynamic calendar will load here --}}
        </div>
    </div>

    {{-- Add / History Schedule Modal --}}
    <div id="addScheduleModal" class="modal">
        <div class="modal-box relative max-h-[85vh] overflow-y-auto">
            <h3 class="font-bold text-lg mb-3">Manage Schedule</h3>

            <div class="tabs tabs-box">
                <input type="radio" name="{{ $tabGroup }}" class="tab" id="{{ $tabGroup }}_add" checked aria-label="Add" />
                <div class="tab-content bg-base-100 border-base-300 p-4" id="{{ $tabGroup }}_add_content">
                    <form id="modalScheduleForm">
                        @csrf
                        <input type="hidden" name="date" id="scheduleDate">

                        @if(in_array($role, ['admin', 'staff']))
                            <label class="block text-sm font-medium mb-2">Select Doctor</label>
                            <select name="doctor_user_id" id="selectDoctorForModal" class="select w-full mb-3">
                                <option value="">-- Select doctor --</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                @endforeach
                            </select>
                        @endif

                        <label class="flex items-center gap-2 mb-2">
                            <input type="checkbox" class="checkbox" name="am" value="1">
                            <span>Morning</span>
                        </label>

                        <label class="flex items-center gap-2 mb-4">
                            <input type="checkbox" class="checkbox" name="pm" value="1">
                            <span>Afternoon</span>
                        </label>

                        <div class="flex justify-end gap-2">
                            <button type="button" class="btn"
                                onclick="$('#addScheduleModal').removeClass('modal-open')">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>

                <input type="radio" name="{{ $tabGroup }}" class="tab" id="{{ $tabGroup }}_history" aria-label="History" />
                <div class="tab-content bg-base-100 border-base-300 p-4" id="{{ $tabGroup }}_history_content">
                    <div class="mb-3">
                        @if(in_array($role, ['admin', 'staff']))
                            <label class="block text-sm font-medium mb-2">Choose doctor to view history</label>
                            <select id="historyDoctorSelect" class="select w-full mb-3">
                                <option value="">-- Select doctor --</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="text-sm text-gray-600 mb-2">Showing your schedule history for the selected date.</div>
                        @endif
                        <div id="historyList" class="flex flex-col gap-2"></div>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="btn"
                            onclick="document.getElementById('{{ $tabGroup }}_add').checked = true;">Back to Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete confirmation modal --}}
    <div id="deleteScheduleModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg text-red-600">Delete Schedule?</h3>
            <p class="mb-4">This will permanently remove the selected schedule.</p>
            <div class="flex justify-end gap-2">
                <button type="button" class="btn" id="cancelDeleteBtn">Cancel</button>
                <button type="button" class="btn btn-error" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            const role = "{{ $role }}";
            const tabGroup = "{{ $tabGroup }}";
            let deleteForm = null;
            let deleteHistoryId = null;

            // Parse integers explicitly to avoid any leading-zero / octal pitfalls
            let currentYear = parseInt("{{ $currentYear }}", 10);
            let currentMonth = parseInt("{{ $currentMonth }}", 10); // 1..12
            let selectedDate = null;

            function pad(n) { return String(n).padStart(2, '0'); }

            function loadCalendar(year, month) {
                // month is 1..12
                $('#calendarGrid').html('<div class="col-span-7 text-center text-gray-500">Loading...</div>');
                $('#currentMonthLabel').text(new Date(year, month - 1).toLocaleString('default', { month: 'long', year: 'numeric' }));

                // send month padded (e.g. "08") to be safe for server parsing
                $.get("{{ route('doctor.schedule.month') }}", { year, month: pad(month) })
                    .done(function (res) {
                        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                        let html = '';

                        // Day headers
                        dayNames.forEach(d => {
                            html += `<div class="text-center font-semibold text-gray-600 py-1">${d}</div>`;
                        });

                        // If no dates returned, show empty grid message
                        if (!res.dates || res.dates.length === 0) {
                            // produce blank month with same number of cells as days in month
                            // but simpler: show message spanning all columns
                            html += `<div class="col-span-7 text-center text-gray-500 py-6">No dates</div>`;
                            $('#calendarGrid').html(html);
                            return;
                        }

                        // compute weekday offset for the first date so the days align under correct weekdays
                        // use the server-provided first date (res.dates[0]) - ensure we parse as midnight ISO to avoid tz shift
                        const firstDateIso = res.dates[0] + 'T00:00:00';
                        const firstWeekday = new Date(firstDateIso).getDay(); // 0..6

                        // Add empty cells for offset
                        for (let i = 0; i < firstWeekday; i++) {
                            html += `<div class="min-h-[170px] p-2 rounded-lg border bg-gray-100"></div>`;
                        }

                        // Now render actual date cells
                        res.dates.forEach(date => {
                            const iso = date + 'T00:00:00';
                            // compare at midnight to avoid timezone issues
                            const isPast = new Date(iso) < new Date(new Date().toISOString().split('T')[0] + 'T00:00:00');
                            const daySchedules = res.schedules.filter(s => s.date === date);
                            const hasMorning = daySchedules.some(s => s.start_time === '08:00:00' && s.end_time === '12:00:00');
                            const hasAfternoon = daySchedules.some(s => s.start_time === '13:00:00' && s.end_time === '17:00:00');

                            html += `<div class="day-cell relative border rounded-lg p-2 flex flex-col cursor-pointer hover:shadow-md transition duration-200 ${isPast ? 'bg-gray-100 cursor-not-allowed opacity-60' : 'bg-white'}"
                            data-date="${date}" data-disabled="${isPast ? 'true' : 'false'}" style="min-height:170px;">
                            <div class="text-sm font-semibold text-center mb-1 ${isPast ? 'text-gray-400' : 'text-gray-800'}">${new Date(iso).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' })}</div>
                            <div class="text-center text-xs mb-1">
                                ${isPast ? '<span class="text-red-500 font-medium">Unavailable</span>' : (hasMorning || hasAfternoon ? '<span class="text-green-600 font-medium">Available</span>' : '<span class="text-yellow-500 font-medium">No Schedule</span>')}
                            </div>
                            <div class="flex flex-col gap-1 mt-1">
                                ${hasMorning ? `<div class="bg-blue-100 border border-blue-300 text-blue-700 rounded px-2 py-1 text-xs flex justify-between items-center">
                                    <span>Morning</span>
                                    ${(!isPast && role === 'doctor' && daySchedules.find(s => s.start_time === '08:00:00') ? `
                                        <form method="POST" action="/doctor/schedule/${daySchedules.find(s => s.start_time === '08:00:00').id}" class="delete-schedule-form">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="button" class="text-red-500 delete-btn">✕</button>
                                        </form>` : '')}
                                </div>` : ''}
                                ${hasAfternoon ? `<div class="bg-blue-100 border border-blue-300 text-blue-700 rounded px-2 py-1 text-xs flex justify-between items-center">
                                    <span>Afternoon</span>
                                    ${(!isPast && role === 'doctor' && daySchedules.find(s => s.start_time === '13:00:00') ? `
                                        <form method="POST" action="/doctor/schedule/${daySchedules.find(s => s.start_time === '13:00:00').id}" class="delete-schedule-form">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="button" class="text-red-500 delete-btn">✕</button>
                                        </form>` : '')}
                                </div>` : ''}
                            </div>
                        </div>`;
                        });

                        $('#calendarGrid').html(html);
                    })
                    .fail(function () {
                        $.toast({
                            heading: "Something went wrong.",
                            icon: "error",
                            text: "Failed to load calendar.",
                            showHideTransition: 'slide',
                            stack: 3,
                            position: 'top-right',
                        });
                    });
            }

            // Initial load
            loadCalendar(currentYear, currentMonth);

            // Month navigation
            $('#prevMonthBtn').on('click', function () {
                currentMonth--;
                if (currentMonth < 1) { currentMonth = 12; currentYear--; }
                loadCalendar(currentYear, currentMonth);
            });
            $('#nextMonthBtn').on('click', function () {
                currentMonth++;
                if (currentMonth > 12) { currentMonth = 1; currentYear++; }
                loadCalendar(currentYear, currentMonth);
            });

            // Calendar click - open Add modal and set selected date
            $(document).on('click', '.day-cell', function (e) {

                // Do not allow delete button to trigger modal
                if ($(e.target).closest('.delete-btn').length) return;

                // Prevent clicking past dates
                if ($(this).attr('data-disabled') === "true") {
                    return; // stops ALL interaction
                }

                selectedDate = $(this).data('date');
                $('#scheduleDate').val(selectedDate);

                // Switch to Add tab automatically
                document.getElementById(tabGroup + '_add').checked = true;
                $('#addScheduleModal').addClass('modal-open');

                // Auto-load history if doctor already selected
                const doctorId = $('#historyDoctorSelect').val();

                if (role !== 'doctor' && doctorId) {
                    document.getElementById(tabGroup + '_history').checked = true;
                    loadHistory(doctorId, selectedDate);
                } else if (role === 'doctor') {
                    loadHistory("{{ $user->id }}", selectedDate);
                }
            });

            // Submit schedule
            $('#modalScheduleForm').on('submit', function (e) {
                e.preventDefault();
                const am = $("input[name='am']").is(':checked');
                const pm = $("input[name='pm']").is(':checked');
                if (!am && !pm) {
                    $.toast({
                        heading: "Something went wrong.",
                        icon: "error",
                        text: "Please select AM or PM.",
                        showHideTransition: 'slide',
                        stack: 3,
                        position: 'top-right',
                    });
                    return;
                }
                if (role !== 'doctor' && !$('#selectDoctorForModal').val()) {
                    $.toast({
                        heading: "Something went wrong.",
                        icon: "error",
                        text: "Please select a doctor.",
                        showHideTransition: 'slide',
                        stack: 3,
                        position: 'top-right',
                    });
                    return;
                }

                $.post("{{ route('doctor.schedule.store') }}", $(this).serialize())
                    .done(res => {
                        $.toast({
                            heading: 'Success',
                            icon: 'success',
                            text: res.message ?? 'Saved',
                            showHideTransition: 'slide',
                            stack: 3,
                            position: 'top-right',
                        });
                        loadCalendar(currentYear, currentMonth);
                        $('#addScheduleModal').removeClass('modal-open');
                    })
                    .fail(() => {
                        $.toast({
                            heading: "Something went wrong.",
                            icon: "error",
                            text: "Failed to save schedule.",
                            showHideTransition: 'slide',
                            stack: 3,
                            position: 'top-right',
                        });
                    });
            });

            function loadHistory(doctorId, date) {
                if (!doctorId || !date) return;
                $.get('/doctor/schedule/history', { doctor_user_id: doctorId, date: date })
                    .done(function (res) {
                        let html = '';
                        if (res.length === 0) {
                            html = '<div class="text-gray-500">No schedules found.</div>';
                        } else {
                            res.forEach(s => {
                                html += `<div class="p-2 bg-gray-100 rounded flex justify-between items-center">
                                                                                                <span>${s.label}</span>
                                                                                                <button type="button" class="text-red-500 delete-history-btn" data-id="${s.id}">✕</button>
                                                                                            </div>`;
                            });
                        }
                        $('#historyList').html(html);
                    })
                    .fail(function () {
                        $.toast({
                            heading: "Something went wrong.",
                            icon: "error",
                            text: "Failed to load history.",
                            showHideTransition: 'slide',
                            stack: 3,
                            position: 'top-right',
                        });
                    });
            }


            // Admin/staff selects doctor in history tab
            $('#historyDoctorSelect').on('change', function () {
                const doctorId = $(this).val();
                if (!selectedDate) return;
                loadHistory(doctorId, selectedDate);
            });

            // Delete schedule (calendar slot delete)
            $(document).on('click', '.delete-btn', function (e) {
                e.stopPropagation();
                deleteForm = $(this).closest('form');
                $('#deleteScheduleModal').addClass('modal-open');
            });

            $('#cancelDeleteBtn').on('click', function () {
                deleteForm = null;
                deleteHistoryId = null;
                $('#deleteScheduleModal').removeClass('modal-open');
            });

            $('#confirmDeleteBtn').on('click', function () {
                if (deleteForm) {
                    // Existing calendar slot delete
                    $.ajax({
                        url: deleteForm.attr('action'),
                        method: 'POST',
                        data: deleteForm.serialize(),
                        success() {
                            $.toast({
                                heading: 'Success',
                                icon: 'success',
                                text: 'Schedule deleted',
                                showHideTransition: 'slide',
                                stack: 3,
                                position: 'top-right',
                            });
                            loadCalendar(currentYear, currentMonth);
                            $('#deleteScheduleModal').removeClass('modal-open');
                            deleteForm = null;
                        },
                        error() {
                            $.toast({
                                heading: "Something went wrong.",
                                icon: "error",
                                text: "Failed to delete schedule.",
                                showHideTransition: 'slide',
                                stack: 3,
                                position: 'top-right',
                            });
                        }
                    });
                } else if (deleteHistoryId) {
                    // History delete
                    $.ajax({
                        url: '/doctor/schedule/' + deleteHistoryId,
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: 'DELETE'
                        },
                        success() {
                            $.toast({
                                heading: 'Success',
                                icon: 'success',
                                text: 'Schedule deleted',
                                showHideTransition: 'slide',
                                stack: 3,
                                position: 'top-right',
                            });

                            // reload calendar and history
                            loadCalendar(currentYear, currentMonth);
                            loadHistory(
                                role === 'doctor' ? "{{ $user->id }}" : $('#historyDoctorSelect').val(),
                                selectedDate
                            );

                            $('#deleteScheduleModal').removeClass('modal-open');
                            deleteHistoryId = null;
                        },
                        error() {
                            $.toast({
                                heading: "Something went wrong.",
                                icon: "error",
                                text: "Failed to delete schedule.",
                                showHideTransition: 'slide',
                                stack: 3,
                                position: 'top-right',
                            });
                        }
                    });
                }
            });

            // Delete from history list (opens modal and sets id)
            $(document).on('click', '.delete-history-btn', function (e) {
                e.stopPropagation(); // prevent opening add modal
                deleteHistoryId = $(this).data('id');

                if (!deleteHistoryId) return;

                $('#deleteScheduleModal').addClass('modal-open');
            });

        });
    </script>
@endsection