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
        $user = Auth::user();
        $role = $user->role->value ?? $user->role;

        if ($role === 'doctor') {
            // doctor sees only their schedules
            $schedules = DoctorSchedule::where('doctor_user_id', $user->id)->get();
            $doctors = collect();
        } else {
            // admin / staff see all
            $schedules = DoctorSchedule::get();
            $doctors = User::where('role', 'doctor')->get(); // adjust role enum if needed
        }

        return view('doctor.doctor-schedule', compact('schedules', 'doctors', 'user'));
    }

    /**
     * Store AM / PM schedule as fixed-slot records
     * AM -> 08:00 - 12:00
     * PM -> 13:00 - 17:00
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->value ?? $user->role;

        $request->validate([
            'date' => ['required', 'date'],
            'doctor_user_id' => Rule::requiredIf($role !== 'doctor'),
        ]);

        if ($role === 'doctor') {
            $doctorId = $user->id;
        } elseif ($role === 'staff') {
            $doctorId = $user->doctor_user_id;
        } else {
            // admin
            $doctorId = $request->input('doctor_user_id');
        }


        // fixed times
        $AM_start = '08:00:00';
        $AM_end   = '12:00:00';
        $PM_start = '13:00:00';
        $PM_end   = '17:00:00';

        $created = [];

        if ($request->has('am') && $request->boolean('am')) {
            // avoid duplicate AM on same date for same doctor
            $exists = DoctorSchedule::where('doctor_user_id', $doctorId)
                ->where('date', $request->date)
                ->where('start_time', $AM_start)
                ->exists();

            if (! $exists) {
                DoctorSchedule::create([
                    'doctor_user_id' => $doctorId,
                    'date' => $request->date,
                    'day_of_week' => date('l', strtotime($request->date)),
                    'start_time' => $AM_start,
                    'end_time' => $AM_end,
                    'is_active' => true,
                ]);
                $created[] = 'AM';
            }
        }

        if ($request->has('pm') && $request->boolean('pm')) {
            $exists = DoctorSchedule::where('doctor_user_id', $doctorId)
                ->where('date', $request->date)
                ->where('start_time', $PM_start)
                ->exists();

            if (! $exists) {
                DoctorSchedule::create([
                    'doctor_user_id' => $doctorId,
                    'date' => $request->date,
                    'day_of_week' => date('l', strtotime($request->date)),
                    'start_time' => $PM_start,
                    'end_time' => $PM_end,
                    'is_active' => true,
                ]);
                $created[] = 'PM';
            }
        }

        if (empty($created)) {
            return response()->json(['message' => 'No new schedule added. Possibly already exists.'], 422);
        }

        return response()->json(['message' => 'Schedule saved successfully.']);
    }

    /**
     * Delete a schedule record (specific doctor + date + slot)
     */
    public function destroy(DoctorSchedule $schedule)
    {
        $user = Auth::user();
        $role = $user->role->value ?? $user->role;

        // doctors can only delete their own schedules
        if ($role === 'doctor' && $schedule->doctor_user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully.']);
    }

    /**
     * Return schedule history for a doctor + date
     * Params: doctor_user_id, date
     */
    public function history(Request $request)
    {
        $request->validate([
            'doctor_user_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        $schedules = DoctorSchedule::where('doctor_user_id', $request->doctor_user_id)
            ->where('date', $request->date)
            ->orderBy('start_time')
            ->get(['id', 'start_time', 'end_time']);

        // attach friendly labels
        $payload = $schedules->map(function ($s) {
            return [
                'id' => $s->id,
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'label' => date('g:i A', strtotime($s->start_time)) . ' - ' . date('g:i A', strtotime($s->end_time)),
            ];
        });

        return response()->json($payload);
    }

    public function month(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->value ?? $user->role;

        $year = $request->query('year', date('Y'));
        $month = $request->query('month', date('m'));

        $startOfMonth = "$year-$month-01";
        $endOfMonth = date("Y-m-t", strtotime($startOfMonth));

        // Generate all dates in the month
        $period = new \DatePeriod(
            new \DateTime($startOfMonth),
            new \DateInterval('P1D'),
            (new \DateTime($endOfMonth))->modify('+1 day')
        );

        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        // Get schedules
        if ($role === 'doctor') {
            $schedules = DoctorSchedule::where('doctor_user_id', $user->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->get();
        } else {
            $schedules = DoctorSchedule::whereBetween('date', [$startOfMonth, $endOfMonth])
                ->get();
        }

        return response()->json([
            'dates' => $dates,
            'schedules' => $schedules,
        ]);
    }
}
