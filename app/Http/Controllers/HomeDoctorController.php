<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeDoctorController extends Controller
{
    public function index(Request $request, $specialty = null)
    {
        $query = DoctorProfile::with('user');

        if ($specialty && $specialty !== 'all') {
            $query->where('specialty', $specialty);
        }

        $doctors = $query->get();

        $specialties = DoctorProfile::select('specialty')
            ->distinct()
            ->pluck('specialty');

        // Get the current patient's queue for today (if any)
        $patientQueue = Queue::where('patient_user_id', Auth::id())
            ->whereDate('queue_date', today())
            ->whereIn('queue_status', ['waiting', 'called', 'in_progress'])
            ->latest()
            ->first();

        // Get the current active queue number per doctor (status called or in_progress)
        $currentQueues = Queue::with('doctor')
            ->whereDate('queue_date', today())
            ->whereIn('queue_status', ['called', 'in_progress'])
            ->get()
            ->groupBy('doctor_user_id');

        return view('home-doctor', compact('doctors', 'specialties', 'specialty', 'patientQueue', 'currentQueues'));
    }


    public function bookDoctor($id)
    {
        $users = User::with('doctor')->findOrFail($id);

        $appointment = Appointment::where('doctor_user_id', $id)
            ->where('patient_user_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed'])
            ->latest()
            ->first();

        // Get all dates where doctor has a schedule
        $schedules = $users->schedules()
            ->pluck('date')
            ->toArray();

        return view('patient.book-appointment', compact('users', 'appointment', 'schedules'));
    }
}
