<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Symfony\Component\Clock\now;

class AppointmentController extends Controller
{
    /**
     * Book a new appointment for a doctor.
     */
    public function book(Request $request, $doctorId)
    {
        // Validate request data
        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',
            'starts_at' => 'required|date_format:Y-m-d H:i',
            'ends_at' => 'required|date_format:Y-m-d H:i|after:starts_at',
        ]);

        $doctor = User::with('doctor')->findOrFail($doctorId);
        $userId = Auth::id();

        // Ensure doctor is available for booking
        if (!$doctor->doctor || $doctor->doctor->status !== 'available') {
            return response()->json([
                'message' => 'This doctor is currently unavailable for booking.',
            ], 400);
        }

        // Check for conflicting appointments
        // $conflict = Appointment::where('doctor_user_id', $doctorId)
        //     ->where(function ($query) use ($validated) {
        //         $query->whereBetween('starts_at', [$validated['starts_at'], $validated['ends_at']])
        //             ->orWhereBetween('ends_at', [$validated['starts_at'], $validated['ends_at']]);
        //     })
        //     ->exists();

        // if ($conflict) {
        //     return response()->json([
        //         'message' => 'This time slot is already booked. Please choose another one.',
        //     ], 409);
        // }

        // Convert time to proper timezone (Asia/Manila)
        $start = Carbon::createFromFormat('Y-m-d H:i', $validated['starts_at'], 'Asia/Manila');
        $end   = Carbon::createFromFormat('Y-m-d H:i', $validated['ends_at'], 'Asia/Manila');

        // Store appointment
        $appointment = Appointment::create([
            'doctor_user_id' => $doctor->id,
            'patient_user_id' => $userId,
            'starts_at' => $start,
            'ends_at' => $end,
            'reason' => $validated['reason'] ?? null,
        ]);

        return response()->json([
            'message' => 'Appointment booked successfully!',
            'appointment_id' => $appointment->id,
        ]);
    }

    /**
     * Cancel an existing appointment.
     */
    public function cancel($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('patient_user_id', Auth::id())
            ->firstOrFail();

        try {
            $appointment->update(['status' => 'cancelled']);

            return response()->json([
                'message' => 'Booking cancelled successfully.'
            ]);
        } catch (Exception $e) {
            Log::error('Cancel failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    /**
     * Display the logged-in patient's appointment history.
     */
    public function myAppointments()
    {
        $appointments = Appointment::with(['doctor.doctor'])
            ->where('patient_user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('patient.my-appointments', compact('appointments'));
    }

    /**
     * Permanently delete an appointment from history.
     */
    public function deleteHistory($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('patient_user_id', Auth::id())
            ->firstOrFail();

        try {
            $appointment->delete();

            return response()->json([
                'message' => 'Appointment history removed successfully.'
            ]);
        } catch (Exception $e) {
            Log::error('Delete history failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }
}
