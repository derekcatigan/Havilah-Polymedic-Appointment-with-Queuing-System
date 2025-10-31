<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Symfony\Component\Clock\now;

class AppointmentController extends Controller
{
    public function book(Request $request, $doctorId)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',
            'starts_at' => 'required|date_format:Y-m-d H:i',
            'ends_at' => 'required|date_format:Y-m-d H:i|after:starts_at',
        ]);

        $doctor = User::with('doctor')->findOrFail($doctorId);
        $userId = Auth::id();

        if (!$doctor->doctor || $doctor->doctor->status !== 'available') {
            return response()->json([
                'message' => 'This doctor is currently unavailable for booking.',
            ], 400);
        }

        // Check if time slot already booked
        $conflict = Appointment::where('doctor_user_id', $doctorId)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('starts_at', [$validated['starts_at'], $validated['ends_at']])
                    ->orWhereBetween('ends_at', [$validated['starts_at'], $validated['ends_at']]);
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'This time slot is already booked. Please choose another one.',
            ], 409);
        }

        $appointment = Appointment::create([
            'doctor_user_id' => $doctor->id,
            'patient_user_id' => $userId,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'reason' => $validated['reason'] ?? null,
        ]);

        return response()->json([
            'message' => 'Appointment booked successfully!',
            'appointment_id' => $appointment->id,
        ]);
    }


    public function cancel($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('patient_user_id', Auth::id())
            ->firstOrFail();

        try {
            $appointment->update([
                'status' => 'cancelled',
            ]);

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

    public function myAppointments()
    {
        $appointments = Appointment::with(['doctor.doctor'])
            ->where('patient_user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('patient.my-appointments', compact('appointments'));
    }

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
        } catch (\Exception $e) {
            Log::error('Delete history failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }
}
