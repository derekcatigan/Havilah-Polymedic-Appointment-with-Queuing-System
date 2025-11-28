<?php

namespace App\Http\Controllers;

use App\Mail\QueueStatusNotification;
use App\Models\Appointment;
use App\Models\Queue;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ManageAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $appointments = Appointment::with(['patient', 'doctor'])
            ->when($user->role->value === 'staff', function ($query) use ($user) {
                // Always apply this for staff
                $query->where('doctor_user_id', $user->doctor_user_id);
            })
            ->when($search, function ($query, $search) use ($user) {

                // Apply search but still respect doctor filtering
                $query->where(function ($q) use ($search, $user) {

                    $q->whereHas('patient', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%");
                    })
                        ->orWhereHas('doctor', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%");
                        });

                    // If staff, don't allow pulling appointments from other doctors
                    if ($user->role->value === 'staff') {
                        $q->where('doctor_user_id', $user->doctor_user_id);
                    }
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('staff.manage-appointment', compact('appointments'));
    }

    public function show(Appointment $appointment)
    {
        $this->authorizeStaffWithAppointment($appointment);

        $appointment->load(['patient', 'doctor', 'serviceTypes']);
        $serviceTypes = ServiceType::orderBy('short_description')->get();

        return view('staff.manage-appointment-detail', compact('appointment', 'serviceTypes'));
    }

    private function authorizeStaffWithAppointment(Appointment $appointment)
    {
        $user = Auth::user();

        if ($user->role === 'staff' && $user->doctor_user_id !== $appointment->doctor_user_id) {
            abort(403, 'Unauthorized access.');
        }
    }

    private function getNextQueueNumber($doctorId)
    {
        $lastQueue = Queue::where('doctor_user_id', $doctorId)
            ->whereDate('queue_date', today())
            ->max('queue_number');

        return $lastQueue ? $lastQueue + 1 : 1;
    }

    public function addServiceType(Request $request, Appointment $appointment)
    {
        $this->authorizeStaffWithAppointment($appointment);

        $request->validate([
            'service_type_id' => 'required|exists:service_types,id',
        ]);

        $appointment->serviceTypes()->syncWithoutDetaching([$request->service_type_id]);

        return back()->with('success', 'Service Type added to this appointment.');
    }

    public function confirm(Appointment $appointment)
    {
        $this->authorizeStaffWithAppointment($appointment);

        $appointment->update([
            'status' => 'confirmed',
        ]);

        if (!$appointment->queue) {
            $nextQueueNumber = $this->getNextQueueNumber($appointment->doctor_user_id);

            $queue = Queue::create([
                'appointment_id'  => $appointment->id,
                'doctor_user_id'  => $appointment->doctor_user_id,
                'patient_user_id' => $appointment->patient_user_id,
                'queue_date'      => today(),
                'queue_number'    => $nextQueueNumber,
                'queue_status'    => 'waiting',
            ]);
        } else {
            $queue = $appointment->queue;
        }

        if (!str_ends_with($appointment->patient->email, '@walkin.local')) {
            Mail::to($appointment->patient->email)
                ->send(new QueueStatusNotification($queue, 'Your appointment has been confirmed.'));
        }

        return back()->with('success', 'Appointment confirmed and queue generated.');
    }

    public function cancel(Appointment $appointment)
    {
        $this->authorizeStaffWithAppointment($appointment);

        $appointment->update([
            'status' => 'cancelled',
            'starts_at' => now('Asia/Manila'),
        ]);

        $queue = $appointment->queue;

        if ($queue && !str_ends_with($queue->patient->email, '@walkin.local')) {
            Mail::to($queue->patient->email)
                ->send(new QueueStatusNotification($queue, 'Your appointment has been cancelled.'));
        }

        return back()->with('success', 'Appointment cancelled.');
    }

    public function complete(Appointment $appointment)
    {
        $this->authorizeStaffWithAppointment($appointment);

        $appointment->update([
            'status' => 'completed',
            'starts_at' => now('Asia/Manila'),
            'ends_at' => now('Asia/Manila'),
        ]);

        return back()->with('success', 'Appointment marked as completed.');
    }

    public function destroy(Appointment $appointment)
    {
        $this->authorizeStaffWithAppointment($appointment);

        $appointment->delete();

        return back()->with('success', 'Appointment deleted successfully.');
    }
}
