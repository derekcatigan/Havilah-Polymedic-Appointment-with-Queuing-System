{{-- resources\views\doctor\doctor-schedule.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4 text-blue-700">My Schedule</h1>

        <!-- Add Schedule Form -->
        <div class="bg-white rounded-lg shadow p-4 mb-6 border border-gray-200">
            <h2 class="text-lg font-semibold mb-3">Add New Schedule</h2>
            <form id="mySchedule" method="POST" class="flex flex-col md:flex-row gap-3 items-center">
                @csrf

                <!-- Day of Week -->
                <div class="flex flex-col w-full md:w-1/3">
                    <label for="day_of_week" class="text-sm font-medium text-gray-700 mb-1">Day of Week</label>
                    <select id="day_of_week" name="day_of_week" class="select w-full border-gray-300 rounded-md" required>
                        <option value="">Select Day</option>
                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                            <option value="{{ $day }}">{{ $day }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Start Time -->
                <div class="flex flex-col w-full md:w-1/4">
                    <label for="start_time" class="text-sm font-medium text-gray-700 mb-1">Start Time</label>
                    <input type="time" id="start_time" name="start_time" class="input w-full border-gray-300 rounded-md"
                        required>
                </div>

                <!-- End Time -->
                <div class="flex flex-col w-full md:w-1/4">
                    <label for="end_time" class="text-sm font-medium text-gray-700 mb-1">End Time</label>
                    <input type="time" id="end_time" name="end_time" class="input w-full border-gray-300 rounded-md"
                        required>
                </div>

                <!-- Submit -->
                <div class="flex items-end w-full md:w-auto">
                    <button type="submit" class="btn btn-primary">
                        Add
                    </button>
                </div>
            </form>
        </div>

        <!-- Calendar View -->
        <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                <div class="bg-white shadow rounded-lg border border-gray-200 p-3">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ $day }}</h3>

                    @php
                        $daySchedules = $schedules->where('day_of_week', $day);
                    @endphp

                    @forelse ($daySchedules as $schedule)
                        <div class="flex justify-between items-center bg-blue-50 border border-blue-200 p-2 rounded mb-2">
                            <span class="text-sm text-gray-700">
                                {{ date('g:i A', strtotime($schedule->start_time)) }} -
                                {{ date('g:i A', strtotime($schedule->end_time)) }}
                            </span>
                            <form method="POST" action="{{ route('doctor.schedule.destroy', $schedule->id) }}"
                                class="delete-schedule-form ml-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm">✕</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm italic">No schedules</p>
                    @endforelse
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {

            // Add Schedule
            $('#mySchedule').on('submit', function (e) {
                e.preventDefault();

                const form = $(this);
                const formData = form.serialize();

                $.ajax({
                    url: "{{ route('doctor.schedule.store') }}",
                    method: "POST",
                    data: formData,
                    success: function (response) {
                        $.toast({
                            heading: 'Success',
                            icon: 'success',
                            text: response.message,
                            showHideTransition: 'slide',
                            stack: 3,
                            position: 'top-right',
                        })
                        location.reload();
                    },
                    error: function (xhr) {
                        let error = "Something went wrong. Please try again later.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            error = xhr.responseJSON.message
                        }
                        $.toast({
                            heading: "Something went wrong.",
                            icon: "error",
                            text: error,
                            showHideTransition: 'slide',
                            stack: 3,
                            position: 'top-right',
                        });
                    }
                });
            });

            // Delete Schedule
            $('.delete-schedule-form').on('submit', function (e) {
                e.preventDefault();

                if (!confirm('Are you sure you want to delete this schedule?')) return;

                const form = $(this);
                const url = form.attr('action');

                $.ajax({
                    url: url,
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "DELETE",
                    },
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        }
                    },
                    error: function () {
                        alert("Error deleting schedule.");
                    }
                });
            });

        });
    </script>
@endsection