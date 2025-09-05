<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorProfileController extends Controller
{
    public function index()
    {
        $doctor = Auth::user()->load('doctor');

        return view('doctor.doctor-profile', compact('doctor'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:available,unavailable,on leave',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
        ]);

        $user->doctor->update([
            'status' => $request->status,
        ]);

        if ($request->status === 'unavailable') {
            Appointment::where('doctor_user_id', $user->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->update(['status' => 'cancelled']);
        }

        return redirect()->route('doctor.profile')
            ->with('success', 'Profile updated successfully.');
    }
}
