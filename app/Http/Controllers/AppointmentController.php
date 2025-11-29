<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Queue;
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
        // Validate incoming request
        $validated = $request->validate([
            'reason'    => 'nullable|string|max:1000',
            'starts_at' => 'required|date_format:Y-m-d H:i',
            'ends_at'   => 'required|date_format:Y-m-d H:i|after:starts_at',
        ]);

        $doctor = User::with('doctor')->findOrFail($doctorId);
        $patientId = Auth::id();

        // Ensure doctor is accepting bookings
        if (!$doctor->doctor || $doctor->doctor->status !== 'available') {
            return response()->json([
                'message' => 'This doctor is currently unavailable for booking.',
            ], 400);
        }

        /**
         * ====================================================
         *  🔒 LIMIT RULE: A patient can only book 2 doctors
         * ====================================================
         */
        $alreadyBookedDoctors = Appointment::where('patient_user_id', $patientId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('doctor_user_id')
            ->unique()
            ->toArray();

        // If patient already booked 2 different doctors AND new doctor is not one of them
        if (count($alreadyBookedDoctors) >= 2 && !in_array($doctorId, $alreadyBookedDoctors)) {
            return response()->json([
                'message' => 'You can only book a maximum of 2 different doctors.',
            ], 400);
        }

        // Convert date to Manila timezone
        $start = Carbon::createFromFormat('Y-m-d H:i', $validated['starts_at'], 'Asia/Manila');
        $end   = Carbon::createFromFormat('Y-m-d H:i', $validated['ends_at'], 'Asia/Manila');

        // Store appointment
        $appointment = Appointment::create([
            'doctor_user_id'  => $doctor->id,
            'patient_user_id' => $patientId,
            'starts_at'       => $start,
            'ends_at'         => $end,
            'reason'          => $validated['reason'] ?? null,
        ]);

        return response()->json([
            'message' => 'Appointment booked successfully!',
            'appointment_id' => $appointment->id,
        ]);
    }



    /**
     * Cancel an appointment.
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
     * List patient's appointments.
     */
    public function myAppointments(Request $request)
    {
        $userId = Auth::id();

        // Fix: use Carbon (not DatePoint)
        $today = Carbon::now()->toDateString();

        // If no date is selected, use today
        $selectedDate = $request->input('date', $today);

        $appointments = Appointment::with(['doctor.doctor'])
            ->where('patient_user_id', $userId)
            ->where('status', '!=', 'cancelled')   // <-- NEW: exclude cancelled
            ->whereDate('starts_at', $selectedDate)
            ->orderBy('starts_at', 'asc')
            ->get();

        return view('patient.my-appointments', [
            'appointments' => $appointments,
            'selectedDate' => $selectedDate,
        ]);
    }

    /**
     * Delete appointment history.
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



    /**
     * Count confirmed appointments (queue count).
     */
    public function queueCount(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date'      => 'required|date',
        ]);

        $count = Appointment::where('doctor_user_id', $request->doctor_id)
            ->whereDate('starts_at', $request->date)
            ->where('status', 'confirmed')
            ->count();

        return response()->json([
            'count' => $count
        ]);
    }
}
