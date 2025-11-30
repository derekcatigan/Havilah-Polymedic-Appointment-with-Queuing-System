<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeQueueController extends Controller
{
    public function index()
    {
        // Get all queues of this patient today
        $patientQueues = Queue::with('doctor')
            ->where('patient_user_id', Auth::id())
            ->whereDate('queue_date', today())
            ->whereIn('queue_status', ['waiting', 'called', 'in_progress'])
            ->orderBy('queue_number')
            ->get();

        // If patient has bookings today, get all doctors they booked with
        $currentQueues = collect();

        if ($patientQueues->count()) {
            $doctorIds = $patientQueues->pluck('doctor_user_id')->unique();

            // Get all active queues of ALL doctors the patient booked
            $currentQueues = Queue::with('doctor')
                ->whereIn('doctor_user_id', $doctorIds)
                ->whereDate('queue_date', today())
                ->whereIn('queue_status', ['called', 'in_progress'])
                ->orderBy('queue_number')
                ->get()
                ->groupBy('doctor_user_id');
        }

        return view('patient.queue-page', compact('patientQueues', 'currentQueues'));
    }
}
