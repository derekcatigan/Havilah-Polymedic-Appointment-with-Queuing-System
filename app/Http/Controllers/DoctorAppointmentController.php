<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $doctor = Auth::user();
        $search = $request->input('search');

        $appointments = $doctor->appointmentsAsDoctor()
            ->with('patient')
            ->where('status', 'confirmed')
            ->when($search, function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('doctor.doctor-appointment', compact('appointments'));
    }


    public function show(Appointment $appointment)
    {
        $appointment->load('patient');

        return view('doctor.doctor-appointment-detail', compact('appointment'));
    }
}
