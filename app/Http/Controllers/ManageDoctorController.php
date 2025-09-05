<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\DoctorProfile;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ManageDoctorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $doctors = User::where('role', 'doctor')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->with('doctor')
            ->latest()
            ->paginate(5);

        return view('admin.manage-doctor', compact('doctors', 'search'));
    }

    public function create()
    {
        return view('admin.create-doctor');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // User Model
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|min:11|max:11|unique:users,contact_number',
            'address' => 'required|string|max:255',
            'password' => 'required|min:6',

            // Doctor Model
            'specialty' => 'required|string'
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => Str::title($validated['name']),
                'email' => $validated['email'],
                'role' => UserRole::Doctor,
                'contact_number' => $validated['phone'],
                'address' => Str::title($validated['address']),
                'password' => Hash::make($validated['password'])
            ]);

            $user->doctor()->create([
                'specialty' => $validated['specialty'],
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Doctor added!'
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Something went wrong: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function edit($id)
    {
        $doctor = User::where('role', UserRole::Doctor)->with('doctor')->findOrFail($id);
        return view('admin.edit-doctor', compact('doctor'));
    }

    public function update(Request $request, $id)
    {
        $doctor = User::where('role', UserRole::Doctor)->with('doctor')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->id,
            'phone' => 'required|min:11|max:11|unique:users,contact_number,' . $doctor->id,
            'address'   => 'required|string|max:255',
            'status'   => 'required|string',
            'specialty'   => 'required|string',
            'password'  => 'nullable|min:6',
        ]);
        DB::beginTransaction();
        try {
            $doctor->update([
                'name' => Str::title($validated['name']),
                'email' => $validated['email'],
                'contact_number' => $validated['phone'],
                'address' => Str::title($validated['address']),
                'password' => Hash::make($validated['password'])
            ]);

            $doctor->doctor()->update([
                'status' => $validated['status'],
                'specialty' => $validated['specialty']
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Update successfull!'
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Something went wrong: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $doctor = User::where('role', UserRole::Doctor)->with('doctor')->findOrFail($id);
            $doctor->delete();

            DB::commit();
            return response()->json([
                'message' => 'Account deleted successfully!'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Delete failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }
}
