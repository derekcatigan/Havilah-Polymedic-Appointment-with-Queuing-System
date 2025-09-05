<?php

namespace App\Http\Controllers;

use App\Mail\QueueStatusNotification;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StaffQueueController extends Controller
{
    public function index(Request $request)
    {
        $doctorId = $request->get('doctor');

        $doctors = User::where('role', 'doctor')->get();

        $queues = Queue::with(['patient', 'doctor'])
            ->when($doctorId, fn($q) => $q->where('doctor_user_id', $doctorId))
            ->whereDate('queue_date', today())
            ->orderBy('queue_number')
            ->get();

        $currentQueue = Queue::with('patient')
            ->whereIn('queue_status', ['called', 'in_progress'])
            ->when($doctorId, fn($q) => $q->where('doctor_user_id', $doctorId))
            ->whereDate('queue_date', today())
            ->orderBy('queue_number')
            ->first();

        return view('staff.manage-queue', compact('queues', 'doctors', 'currentQueue'));
    }

    // public function callNext(Request $request)
    // {
    //     $doctorId = $request->get('doctor');

    //     $currentActive = Queue::where('doctor_user_id', $doctorId)
    //         ->whereIn('queue_status', ['called', 'in_progress'])
    //         ->whereDate('queue_date', today())
    //         ->first();

    //     if ($currentActive) {
    //         return back()->with('error', "Patient {$currentActive->patient->name} is currently being served.");
    //     }

    //     $queue = Queue::where('doctor_user_id', $doctorId)
    //         ->where('queue_status', 'waiting')
    //         ->whereDate('queue_date', today())
    //         ->orderBy('queue_number')
    //         ->first();

    //     if ($queue) {
    //         $queue->update(['queue_status' => 'called']);

    //         if (!str_ends_with($queue->patient->email, '@walkin.local')) {
    //             Mail::to($queue->patient->email)
    //                 ->send(new QueueStatusNotification($queue, 'It is your turn. Please proceed to the doctor.'));
    //         }

    //         return back()->with('success', "Patient {$queue->patient->name} called.");
    //     }

    //     return back()->with('error', 'No patients waiting in the queue.');
    // }

    public function call(Queue $queue)
    {
        $currentActive = Queue::where('doctor_user_id', $queue->doctor_user_id)
            ->whereIn('queue_status', ['called', 'in_progress'])
            ->whereDate('queue_date', today())
            ->first();

        if ($currentActive) {
            return back()->with('error', "Patient {$currentActive->patient->name} is currently being served.");
        }

        $queue->update(['queue_status' => 'called']);

        if (!str_ends_with($queue->patient->email, '@walkin.local')) {
            Mail::to($queue->patient->email)
                ->send(new QueueStatusNotification($queue, 'It is your turn. Please proceed to the doctor.'));
        }

        return back()->with('success', "Patient {$queue->patient->name} called.");
    }


    public function progress(Queue $queue)
    {
        $queue->update(['queue_status' => 'in_progress']);
        return back()->with('success', "Patient {$queue->patient->name} marked as in progress.");
    }

    public function complete(Queue $queue)
    {
        $queue->update(['queue_status' => 'completed']);

        if ($queue->appointment) {
            $queue->appointment->update([
                'status' => 'completed',
                'ends_at' => now('Asia/Manila'),
            ]);
        }

        return back()->with('success', "Patient {$queue->patient->name} marked as completed.");
    }

    public function skip(Queue $queue)
    {
        $queue->update(['queue_status' => 'skipped']);

        if (!str_ends_with($queue->patient->email, '@walkin.local')) {
            Mail::to($queue->patient->email)
                ->send(new QueueStatusNotification($queue, 'Your turn has been skipped. Please contact the staff for rescheduling.'));
        }

        return back()->with('success', "Patient {$queue->patient->name} skipped.");
    }
}
