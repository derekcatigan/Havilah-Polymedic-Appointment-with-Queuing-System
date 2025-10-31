<?php

namespace App\Http\Controllers;

use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyScheduleController extends Controller
{
    public function index()
    {
        $schedules = DoctorSchedule::where('doctor_user_id', Auth::id())->get();

        return view('doctor.doctor-schedule', compact('schedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $schedule = DoctorSchedule::create([
            'doctor_user_id' => Auth::id(),
            'day_of_week' => $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Schedule added successfully.',
        ], 201);
    }

    public function destroy(DoctorSchedule $schedule)
    {
        // Ensure the logged-in doctor owns the schedule
        if ($schedule->doctor_user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $schedule->delete();

        return response()->json(['success' => true, 'message' => 'Schedule deleted successfully.']);
    }
}
