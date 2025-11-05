<?php

namespace App\Http\Controllers;

use App\Mail\QueueStatusNotification;
use App\Models\Appointment;
use App\Models\Queue;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ManageAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $appointments = Appointment::with(['patient', 'doctor'])
            ->when($search, function ($query, $search) {
                $query->whereHas('patient', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('doctor', fn($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('staff.manage-appointment', compact('appointments'));
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'serviceTypes']);
        $serviceTypes = ServiceType::orderBy('short_description')->get();

        return view('staff.manage-appointment-detail', compact('appointment', 'serviceTypes'));
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
        $request->validate([
            'service_type_id' => 'required|exists:service_types,id',
        ]);

        // Attach service type (avoid duplicates)
        $appointment->serviceTypes()->syncWithoutDetaching([$request->service_type_id]);

        return back()->with('success', 'Service Type added to this appointment.');
    }

    public function confirm(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'confirmed',
        ]);

        // Handle queue assignment
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

        // Send notification email if not walk-in
        if (!str_ends_with($appointment->patient->email, '@walkin.local')) {
            Mail::to($appointment->patient->email)
                ->send(new QueueStatusNotification($queue, 'Your appointment has been confirmed.'));
        }

        return back()->with('success', 'Appointment confirmed, service type assigned, and queue number generated.');
    }

    public function cancel(Appointment $appointment)
    {
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
        $appointment->update([
            'status' => 'completed',
            'starts_at' => now('Asia/Manila'),
            'ends_at' => now('Asia/Manila'),
        ]);

        return back()->with('success', 'Appointment marked as completed.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return back()->with('success', 'Appointment deleted successfully.');
    }
}
