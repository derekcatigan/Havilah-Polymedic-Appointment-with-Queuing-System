<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ManagePatientController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query: patients only
        $query = User::where('role', 'patient');

        if ($user->role->value === 'doctor') {
            // Doctor: patients who have appointments with this doctor
            $query->whereHas('appointmentsAsPatient', function ($q) use ($user) {
                $q->where('doctor_user_id', $user->id);
            });
        } elseif ($user->role->value === 'staff') {
            // Staff: patients who have appointments with staff's assigned doctor
            $query->whereHas('appointmentsAsPatient', function ($q) use ($user) {
                $q->where('doctor_user_id', $user->doctor_user_id);
            });
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(10)->withQueryString();

        return view('admin.manage-patient', compact('patients'));
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|min:11|max:11|unique:users,contact_number',
            'address' => 'required|string|max:255',
            'password' => 'required|min:6|confirmed',
        ]);

        // Check for duplicate name + phone combo
        $exists = User::where('name', Str::title($validated['name']))
            ->where('contact_number', $validated['phone'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A patient with the same name and phone number already exists.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // 🔹 Auto-generate IDs
            $patientId = 'PID-' . strtoupper(Str::random(6)); // Example: PID-AF7BZT
            $patientNumber = 'PN-' . str_pad(User::where('role', UserRole::Patient)->count() + 1, 5, '0', STR_PAD_LEFT);

            User::create([
                'patient_id' => $patientId,
                'patient_number' => $patientNumber,
                'name' => Str::title($validated['name']),
                'email' => $validated['email'],
                'role' => UserRole::Patient,
                'contact_number' => $validated['phone'],
                'address' => Str::title($validated['address']),
                'password' => Hash::make($validated['password']),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Patient account created successfully!',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Admin patient creation failed: " . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function show(User $patient)
    {
        // Fetch patient and their related appointments + services
        $appointments = Appointment::with('serviceTypes')
            ->where('patient_user_id', $patient->id)
            ->latest()
            ->get();

        return view('admin.view-patient', compact('patient', 'appointments'));
    }


    public function edit(User $patient)
    {
        return response()->json($patient);
    }

    public function update(Request $request, User $patient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $patient->id,
            'phone' => 'required|string|min:11|max:11|unique:users,contact_number,' . $patient->id,
            'address' => 'required|string|max:255',
        ]);

        try {
            $patient->update([
                'name' => Str::title($validated['name']),
                'email' => $validated['email'],
                'contact_number' => $validated['phone'],
                'address' => Str::title($validated['address']),
            ]);

            return response()->json(['message' => 'Patient updated successfully!']);
        } catch (Exception $e) {
            Log::error("Patient update failed: " . $e->getMessage());
            return response()->json(['message' => 'Something went wrong.'], 500);
        }
    }

    public function destroy(User $patient)
    {
        try {
            $patient->delete();
            return response()->json(['message' => 'Patient deleted successfully!']);
        } catch (Exception $e) {
            Log::error("Failed to delete patient: " . $e->getMessage());
            return response()->json(['message' => 'Failed to delete patient.'], 500);
        }
    }
}
