<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class WalkInController extends Controller
{
    public function create()
    {
        $doctors = User::where('role', 'doctor')
            ->with('doctor')
            ->get();

        return view('staff.walkin-create', compact('doctors'));
    }

    private function getNextQueueNumber($doctorId)
    {
        $lastQueue = Queue::where('doctor_user_id', $doctorId)
            ->whereDate('queue_date', today())
            ->max('queue_number');

        return $lastQueue ? $lastQueue + 1 : 1;
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name'   => 'required|string|max:255',
            'doctor_user_id' => 'required|exists:users,id',
        ]);

        $patient = User::where('role', 'patient')
            ->when($validated['email'] ?? null, fn($q) => $q->where('email', $validated['email']))
            ->when($validated['phone'] ?? null, fn($q) => $q->orWhere('contact_number', $validated['phone']))
            ->first();

        if (!$patient) {
            $patient = User::create([
                'name'           => Str::title($validated['patient_name']),
                'email'          => $validated['email'] ?? uniqid() . '@walkin.local',
                'contact_number' => $validated['phone'] ?? null,
                'role'           => 'patient',
                'status'         => 'active',
                'password'       => bcrypt('password'),
            ]);
        }

        $patient = User::create([
            'name'     => Str::title($validated['patient_name']),
            'email'    => uniqid() . '@walkin.local',
            'role'     => 'patient',
            'status'   => 'active',
            'password' => bcrypt('password'),
        ]);

        $now = Carbon::now('Asia/Manila');

        $appointment = Appointment::create([
            'doctor_user_id'  => $validated['doctor_user_id'],
            'patient_user_id' => $patient->id,
            'starts_at'       => $now,
            'ends_at'         => $now,
            'status'          => 'confirmed',
        ]);

        $nextQueueNumber = $this->getNextQueueNumber($validated['doctor_user_id']);

        Queue::create([
            'appointment_id'  => $appointment->id,
            'doctor_user_id'  => $validated['doctor_user_id'],
            'patient_user_id' => $patient->id,
            'queue_date'      => today(),
            'queue_number'    => $nextQueueNumber,
            'queue_status'    => 'waiting',
        ]);

        return redirect()->route('manage.appointment')
            ->with('success', 'Walk-in appointment created and added to queue.');
    }
}
