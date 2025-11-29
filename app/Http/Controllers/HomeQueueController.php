<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeQueueController extends Controller
{
    public function index()
    {
        $patientQueue = Queue::with('doctor')
            ->where('patient_user_id', Auth::id())
            ->whereDate('queue_date', today())
            ->whereIn('queue_status', ['waiting', 'called', 'in_progress'])
            ->latest()
            ->first();

        $currentQueues = collect();

        if ($patientQueue) {
            $currentQueues = Queue::with('doctor')
                ->where('doctor_user_id', $patientQueue->doctor_user_id)
                ->whereDate('queue_date', today())
                ->whereIn('queue_status', ['called', 'in_progress'])
                ->get()
                ->groupBy('doctor_user_id');
        }

        return view('patient.queue-page', compact('patientQueue', 'currentQueues'));
    }
}
