<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    /**
     * Get all available schedule slots for a given doctor.
     */
    public function getAvailableSlots($doctorId)
    {
        // Fetch all active schedules for the doctor
        $schedules = DoctorSchedule::where('doctor_user_id', $doctorId)
            ->where('is_active', true)
            ->get();

        $availableSlots = [];

        foreach ($schedules as $schedule) {
            $today = now();
            $targetDay = Carbon::parse("this {$schedule->day_of_week}");

            // Determine whether to use today’s date or the next occurrence of that day
            $finalDate = $today->isSameDay($targetDay)
                ? $today->format('Y-m-d')
                : $today->next($schedule->day_of_week)->format('Y-m-d');

            // Format and store the available slot
            $availableSlots[] = [
                'day_of_week' => $schedule->day_of_week,
                'date'        => $finalDate,
                'start_time'  => Carbon::parse($schedule->start_time)->format('h:i A'),
                'end_time'    => Carbon::parse($schedule->end_time)->format('h:i A'),
            ];
        }

        // Remove duplicate day/time entries
        $availableSlots = collect($availableSlots)
            ->unique(fn($slot) => $slot['day_of_week'] . $slot['start_time'] . $slot['end_time'])
            ->values();

        return response()->json($availableSlots);
    }
}
