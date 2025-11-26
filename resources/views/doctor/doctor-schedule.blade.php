{{-- resources/views/doctor/doctor-schedule.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="p-6 bg-white rounded shadow-lg">
        <h1 class="text-2xl font-bold mb-6 text-blue-700">My Schedule</h1>

        @php
            $startOfMonth = new DateTime('first day of this month');
            $endOfMonth = new DateTime('last day of this month');
            $dates = [];
            for ($d = clone $startOfMonth; $d <= $endOfMonth; $d->modify('+1 day')) {
                $dates[] = $d->format('Y-m-d');
            }
            $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            $role = $user->role->value ?? $user->role;
        @endphp

        <!-- Calendar Header -->
        <div class="flex justify-between items-center mb-4 px-2">
            <h3 class="text-lg font-semibold text-gray-700">{{ date('F Y') }}</h3>
            <small class="text-gray-500">Click a date to add schedule</small>
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-3">
            @foreach($dayNames as $dow)
                <div class="text-center font-semibold text-gray-600 py-1">{{ $dow }}</div>
            @endforeach

            @foreach($dates as $date)
                @php
                    $isPast = strtotime($date) < strtotime(date('Y-m-d'));
                    $daySchedules = $schedules->where('date', $date)
                        ->when($role === 'doctor', fn($q) => $q)
                        ->sortBy('start_time')->values();
                @endphp

                <div class="day-cell relative border rounded-lg p-2 flex flex-col cursor-pointer hover:shadow-md transition duration-200
                                                                                                                                    {{ $isPast ? 'bg-gray-100 cursor-not-allowed opacity-60' : 'bg-white'}}"
                    data-date="{{ $date }}" data-disabled="{{ $isPast ? 'true' : 'false'}}" style="min-height:180px;">
                    <div class="text-sm font-semibold text-center mb-1 {{ $isPast ? 'text-gray-400' : 'text-gray-800'}}">
                        {{ date('d M', strtotime($date)) }}
                    </div>
                    <div class="text-center text-xs mb-1">
                        @if($isPast)
                            <span class="text-red-500 font-medium">Unavailable</span>
                        @elseif(count($daySchedules))
                            <span class="text-green-600 font-medium">Available</span>
                        @else
                            <span class="text-yellow-500 font-medium">No Slots</span>
                        @endif
                    </div>

                    @if($role === 'doctor')
                        <div class="flex-1 flex flex-col gap-1 overflow-y-auto max-h-28 pr-1">
                            @foreach($daySchedules as $schedule)
                                <div
                                    class="flex items-center justify-between bg-blue-100 border border-blue-300 text-blue-700 rounded px-2 py-1 text-xs shadow-sm">
                                    <span>{{ date('g:i A', strtotime($schedule->start_time)) }} -
                                        {{ date('g:i A', strtotime($schedule->end_time)) }}</span>
                                    @if(!$isPast)
                                        <form method="POST" action="{{ route('doctor.schedule.destroy', $schedule->id)}}"
                                            class="delete-schedule-form flex items-center">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-500 hover:text-red-700 delete-btn ml-1">✕</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Add Schedule Modal -->
    <div id="addScheduleModal" class="modal">
        <div class="modal-box relative max-h-[90vh] overflow-y-auto">
            <h3 class="font-bold text-lg mb-2">Add Schedule</h3>
            <form id="modalScheduleForm">
                @csrf
                <input type="hidden" name="date" id="scheduleDate">

                @if(in_array($role, ['admin', 'staff']))
                    <label class="block mt-2 text-sm font-medium">Doctor</label>
                    <select name="doctor_user_id" class="select w-full">
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                @endif

                <!-- DaisyUI Radio Tabs -->
                <div class="tabs tabs-lift mt-4">
                    <!-- Add Slots Tab -->
                    <input type="radio" name="schedule_tabs" id="tabSlots" class="tab" checked aria-label="Add Slots">
                    <div class="tab-content bg-base-100 border-base-300 p-4" id="slotsTabContent">
                        <div class="flex justify-center gap-2 mb-3" id="amPmButtons">
                            <button type="button" id="btnAM" class="btn btn-outline btn-primary">AM Slots</button>
                            <button type="button" id="btnPM" class="btn btn-outline btn-primary">PM Slots</button>
                        </div>
                        <div id="timeSlots" class="grid grid-cols-3 gap-2 max-h-48 overflow-y-auto"></div>
                    </div>

                    <!-- Schedule History Tab -->
                    <input type="radio" name="schedule_tabs" id="tabHistory" class="tab" aria-label="Schedule History">
                    <div class="tab-content bg-base-100 border-base-300 p-4 max-h-64 overflow-y-auto"
                        id="historyTabContent">
                        <h4 class="font-semibold mb-3 text-gray-700">Existing Schedule</h4>
                        <div id="historyList" class="flex flex-col gap-2">
                            <!-- JS will append schedule cards here -->
                        </div>
                    </div>
                </div>

                <div class="modal-action mt-3">
                    <button type="button" class="btn"
                        onclick="$('#addScheduleModal').removeClass('modal-open')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Selected Slots</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteScheduleModal" class="modal">
        <div class="modal-box relative">
            <h3 class="font-bold text-lg text-red-600">Delete Schedule</h3>
            <p class="py-4">Are you sure you want to delete this schedule?</p>
            <div class="modal-action">
                <button type="button" class="btn" id="cancelDeleteBtn">Cancel</button>
                <button type="button" class="btn btn-error" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            let formToDelete = null;
            const role = "{{ $role }}";

            /** ----------------------------------------
             *  TIME SLOT GENERATION
             ---------------------------------------- */
            function generateTimeSlots(startHour, endHour) {
                const slots = [];
                const today = new Date();
                let start = new Date(today.getFullYear(), today.getMonth(), today.getDate(), startHour, 0, 0);
                let end = new Date(today.getFullYear(), today.getMonth(), today.getDate(), endHour, 0, 0);

                while (start < end) {
                    const slotStart = start.toTimeString().slice(0, 5);
                    const slotEndDate = new Date(start.getTime() + 30 * 60000);
                    const slotEnd = slotEndDate.toTimeString().slice(0, 5);

                    const labelStart = start.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true });
                    const labelEnd = slotEndDate.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true });

                    slots.push({ value: `${slotStart}-${slotEnd}`, label: `${labelStart} - ${labelEnd}` });

                    start = slotEndDate;
                }
                return slots;
            }

            function renderTimeSlots(slots) {
                const container = $('#timeSlots');
                container.empty();
                slots.forEach(s => {
                    const id = `slot-${s.value.replace(':', '')}`;
                    container.append(`
                                <label class="flex items-center gap-1 border p-1 rounded cursor-pointer">
                                    <input type="checkbox" name="slots[]" value="${s.value}" id="${id}" class="checkbox">
                                    <span class="text-xs">${s.label}</span>
                                </label>
                            `);
                });
            }

            /** ----------------------------------------
             *  LOAD HISTORY
             ---------------------------------------- */
            function loadScheduleHistory(date, doctorId) {
                if (!doctorId) return;

                $.get("{{ route('doctor.schedule.history') }}", { date, doctor_user_id: doctorId }, function (res) {
                    const list = $('#historyList');
                    list.empty();

                    if (res.length === 0) {
                        list.append('<div class="text-gray-500 text-sm">No schedules found</div>');
                        return;
                    }

                    res.forEach(s => {
                        const start = new Date('1970-01-01T' + s.start_time);
                        const end = new Date('1970-01-01T' + s.end_time);

                        const label =
                            start.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true }) +
                            ' - ' +
                            end.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true });

                        list.append(`
                                    <div class="flex justify-between items-center p-2 bg-white border rounded shadow-sm hover:shadow-md transition">
                                        <div class="text-sm font-medium text-gray-700">${label}</div>
                                        ${role !== 'doctor'
                                ? `<div class="text-xs text-gray-500">${s.doctor_name}</div>`
                                : ''}
                                    </div>
                                `);
                    });
                });
            }


            /** ----------------------------------------
             *  FIX: PREVENT BUBBLING WHEN CLICKING DELETE
             ---------------------------------------- */

            // Clicking the X button
            $(document).on("click", ".delete-btn", function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();  // ⛔ FULL STOP — NOTHING BUBBLES
                e.stopPropagation();

                formToDelete = $(this).closest("form");
                $("#deleteScheduleModal").addClass("modal-open");
            });

            // Clicking inside the delete form itself
            $(document).on("click", ".delete-schedule-form", function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                e.stopPropagation();
            });


            /** ----------------------------------------
             *  DAY CELL CLICK — OPEN ADD SCHEDULE MODAL
             ---------------------------------------- */
            $(".day-cell").on("click", function (e) {
                // Make sure delete click NEVER triggers this
                if ($(e.target).closest(".delete-btn").length) return;
                if ($(e.target).closest(".delete-schedule-form").length) return;

                if ($(this).data("disabled")) return;

                const date = $(this).data("date");
                $("#scheduleDate").val(date);

                renderTimeSlots(generateTimeSlots(0, 12));

                const doctorId = role === "doctor"
                    ? "{{ $user->id }}"
                    : $('select[name="doctor_user_id"]').val();

                if (doctorId) {
                    loadScheduleHistory(date, doctorId);
                }


                $("#addScheduleModal").addClass("modal-open");
            });


            /** ----------------------------------------
             *  TABS CHANGE (HISTORY/ADD)
             ---------------------------------------- */
            $('input[name="schedule_tabs"]').on("change", function () {
                if ($("#tabHistory").is(":checked")) {
                    const date = $("#scheduleDate").val();
                    const doctorId = role === "doctor" ? "{{ $user->id }}" : $('select[name="doctor_user_id"]').val();
                    if (doctorId) loadScheduleHistory(date, doctorId);
                }
            });

            /** AM/PM buttons **/
            $(document).on("click", "#btnAM", () =>
                renderTimeSlots(generateTimeSlots(0, 12))
            );
            $(document).on("click", "#btnPM", () =>
                renderTimeSlots(generateTimeSlots(12, 24))
            );

            /** Doctor dropdown change **/
            $(document).on("change", 'select[name="doctor_user_id"]', function () {
                const doctorId = $(this).val();
                const date = $("#scheduleDate").val();
                if (doctorId) loadScheduleHistory(date, doctorId);
            });


            /** ----------------------------------------
             *  SUBMIT ADD SLOT FORM
             ---------------------------------------- */
            $("#modalScheduleForm").on("submit", function (e) {
                e.preventDefault();

                const slots = $('input[name="slots[]"]:checked')
                    .map(function () {
                        return $(this).val();
                    })
                    .get();

                if (slots.length === 0) {
                    alert("Please select at least one time slot");
                    return;
                }

                const formData = $(this).serializeArray();

                slots.forEach(s => {
                    const [start, end] = s.split("-");
                    formData.push({ name: "start_time[]", value: start });
                    formData.push({ name: "end_time[]", value: end });
                });

                $.ajax({
                    url: "{{ route('doctor.schedule.store') }}",
                    method: "POST",
                    data: formData,
                    success: function (res) {
                        $.toast({ heading: "Success", icon: "success", text: res.message });
                        location.reload();
                    },
                    error: function (xhr) {
                        $.toast({
                            heading: "Error",
                            icon: "error",
                            text: xhr.responseJSON?.message || "Something went wrong",
                        });
                    },
                });
            });


            /** ----------------------------------------
             *  DELETE CONFIRMATION
             ---------------------------------------- */
            $("#cancelDeleteBtn").on("click", function () {
                formToDelete = null;
                $("#deleteScheduleModal").removeClass("modal-open");
            });

            $("#confirmDeleteBtn").on("click", function () {
                if (!formToDelete) return;

                $.ajax({
                    url: formToDelete.attr("action"),
                    method: "POST",
                    data: { _token: "{{ csrf_token() }}", _method: "DELETE" },
                    success: function (res) {
                        if (res.success) location.reload();
                    },
                    error: function () {
                        alert("Error deleting schedule.");
                    },
                });

                $("#deleteScheduleModal").removeClass("modal-open");
                formToDelete = null;
            });

        });
    </script>
@endsection