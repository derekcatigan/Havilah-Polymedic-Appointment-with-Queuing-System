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
            for ($i = 0; $i < 7; $i++) { // show slots for the next 7 days
                $date = Carbon::now()->startOfDay()->addDays($i);

                if ($date->format('l') !== $schedule->day_of_week) {
                    continue;
                }

                $start = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->start_time);
                $end = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->end_time);

                $appointments = Appointment::where('doctor_user_id', $doctorId)
                    ->whereBetween('starts_at', [$start, $end])
                    ->pluck('starts_at');

                $slot = $start->copy();
                while ($slot->lt($end)) {
                    $slotEnd = $slot->copy()->addMinutes(30);
                    $isBooked = $appointments->contains(fn($a) => $a->format('Y-m-d H:i') === $slot->format('Y-m-d H:i'));

                    if (!$isBooked) {
                        $availableSlots[] = [
                            'date' => $date->format('Y-m-d'),
                            'start_time' => $slot->format('H:i'),
                            'end_time' => $slotEnd->format('H:i'),
                        ];
                    }

                    $slot->addMinutes(30);
                }
            }
        }

        return response()->json($availableSlots);
    }
}
