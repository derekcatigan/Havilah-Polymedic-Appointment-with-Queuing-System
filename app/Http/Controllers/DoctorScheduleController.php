<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function getAvailableSlots($doctorId)
    {
        $schedules = DoctorSchedule::where('doctor_user_id', $doctorId)
            ->where('is_active', true)
            ->get();

        $availableSlots = [];

        foreach ($schedules as $schedule) {
            $today = now();
            $target = Carbon::parse("this {$schedule->day_of_week}");

            // If today is the same as the schedule day, use today’s date
            // Otherwise, use the next occurrence of that day
            $finalDate = $today->isSameDay($target)
                ? $today->format('Y-m-d')
                : $today->next($schedule->day_of_week)->format('Y-m-d');

            // Build the formatted slot
            $availableSlots[] = [
                'day_of_week' => $schedule->day_of_week,
                'date'        => $finalDate,
                'start_time'  => Carbon::parse($schedule->start_time)->format('h:i A'),
                'end_time'    => Carbon::parse($schedule->end_time)->format('h:i A'),
            ];
        }

        // Remove duplicates (in case multiple entries fall on the same date/time)
        $availableSlots = collect($availableSlots)->unique(function ($slot) {
            return $slot['day_of_week'] . $slot['start_time'] . $slot['end_time'];
        })->values();

        return response()->json($availableSlots);
    }
}
