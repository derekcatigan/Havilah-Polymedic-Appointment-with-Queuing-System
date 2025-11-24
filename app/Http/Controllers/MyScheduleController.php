<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MyScheduleController extends Controller
{
    public function index()
    {
        $schedules = DoctorSchedule::get();
        $user = Auth::user();

        $doctors = [];
        if (in_array($user->role->value ?? $user->role, ['admin', 'staff'])) {
            $doctors = User::where('role', UserRole::Doctor)->get();
        }

        return view('doctor.doctor-schedule', compact('schedules', 'doctors', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $doctorId = ($user->role->value ?? $user->role) === 'doctor' ? $user->id : $request->doctor_user_id;

        $request->validate([
            'date' => 'required|date',
            'start_time.*' => 'required|date_format:H:i',
            'end_time.*' => 'required|date_format:H:i|after:start_time.*',
            'doctor_user_id' => [Rule::requiredIf(in_array($user->role->value ?? $user->role, ['admin', 'staff'])), 'exists:users,id']
        ]);

        foreach ($request->start_time as $i => $start) {
            $end = $request->end_time[$i];

            $overlap = DoctorSchedule::where('doctor_user_id', $doctorId)
                ->where('date', $request->date)
                ->where(function ($q) use ($start, $end) {
                    $q->where('start_time', '<', $end)
                        ->where('end_time', '>', $start);
                })->exists();

            if ($overlap) return response()->json(['message' => "Slot $start-$end overlaps with existing schedule."], 422);

            DoctorSchedule::create([
                'doctor_user_id' => $doctorId,
                'date' => $request->date,
                'day_of_week' => date('l', strtotime($request->date)),
                'start_time' => $start,
                'end_time' => $end,
                'is_active' => true
            ]);
        }

        return response()->json(['message' => 'Schedule added successfully.'], 201);
    }

    public function destroy(DoctorSchedule $schedule)
    {
        $user = Auth::user();
        $role = $user->role->value ?? $user->role;

        if ($role === 'doctor' && $schedule->doctor_user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $schedule->delete();
        return response()->json(['success' => true, 'message' => 'Schedule deleted successfully.']);
    }

    public function history(Request $request)
    {
        $schedules = DoctorSchedule::where('doctor_user_id', $request->doctor_user_id)
            ->where('date', $request->date)
            ->orderBy('start_time')
            ->get(['start_time', 'end_time']);
        return response()->json($schedules);
    }
}
