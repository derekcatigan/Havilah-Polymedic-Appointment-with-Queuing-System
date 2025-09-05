<?php

namespace App\Http\Controllers;

use App\Mail\QueueStatusNotification;
use App\Models\Appointment;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ManageAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $appointments = Appointment::with(['patient', 'doctor'])
            ->when($search, function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('doctor', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(5)
            ->withQueryString(); // keeps search text in pagination links

        return view('staff.manage-appointment', compact('appointments'));
    }


    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor']);
        return view('staff.manage-appointment-detail', compact('appointment'));
    }

    private function getNextQueueNumber($doctorId)
    {
        $lastQueue = Queue::where('doctor_user_id', $doctorId)
            ->whereDate('queue_date', today())
            ->max('queue_number');

        return $lastQueue ? $lastQueue + 1 : 1;
    }

    public function confirm(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'confirmed',
            'starts_at' => now('Asia/Manila'),
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

        return back()->with('success', 'Appointment confirmed and queue number assigned.');
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
