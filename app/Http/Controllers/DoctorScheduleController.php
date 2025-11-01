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

        $availableSlots = $schedules->map(function ($schedule) {
            return [
                'day_of_week' => $schedule->day_of_week,
                'start_time'  => Carbon::parse($schedule->start_time)->format('h:i A'),
                'end_time'    => Carbon::parse($schedule->end_time)->format('h:i A'),
                'date'        => Carbon::now()->next($schedule->day_of_week)->format('Y-m-d'),
            ];
        })->unique(fn($slot) => $slot['day_of_week'] . $slot['start_time'] . $slot['end_time'])->values();

        return response()->json($availableSlots);
    }
}
