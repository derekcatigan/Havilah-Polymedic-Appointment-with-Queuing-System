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
        $validate = $request->validate([
            'reason' => 'nullable|string|max:1000'
        ]);

        $doctor = User::with('doctor')->findOrFail($doctorId);
        $userId = Auth::id();

        if (!$doctor->doctor || $doctor->doctor->status !== 'available') {
            return response()->json([
                'message' => 'This doctor is currently unavailable for booking.',
            ], 400);
        }

        try {
            DB::beginTransaction();

            $appointment = Appointment::create([
                'doctor_user_id' => $doctor->id,
                'patient_user_id' => $userId,
                'starts_at' => now('Asia/Manila'),
                'ends_at' => now('Asia/Manila'),
                'reason' => $validate['reason'] ?? null,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Booking doctor successfully!',
                'appointment_id' => $appointment->id,
                'status' => $appointment->status,
                'doctor_status' => $appointment->doctor->status,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Something went wrong: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
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
