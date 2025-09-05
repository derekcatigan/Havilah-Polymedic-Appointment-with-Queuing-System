<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;

class ManageAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        $query->whereIn('role', ['admin', 'staff', 'patient']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $accounts = $query->latest()->paginate(5);

        return view('admin.manage-account', compact('accounts'));
    }

    public function create()
    {
        return view('admin.create-account');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone'     => 'required|string|min:11|max:11|unique:users,contact_number',
            'address'   => 'required|string|max:255',
            'role' => 'required|string',
            'password' => 'required|min:6',
        ]);

        $exists = User::where('name', Str::title($validated['name']))
            ->where('contact_number', $validated['phone'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A user with the same name and phone already exists.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            User::create([
                'name' => Str::title($validated['name']),
                'email' => $validated['email'],
                'contact_number' => $validated['phone'],
                'address' => Str::title($validated['address']),
                'role' => $validated['role'],
                'password' => Hash::make($validated['password']),
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Account created successfully!',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('something went wrong: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function edit($id)
    {
        $account = User::findOrFail($id);
        return view('admin.edit-account', compact('account'));
    }

    public function update(Request $request, $id)
    {
        $account = User::findOrFail($id);

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $account->id,
            'phone'     => 'required|string|min:11|max:11|unique:users,contact_number,' . $account->id,
            'address'   => 'required|string|max:255',
            'role'      => 'required|string',
            'status'    => 'required|string',
            'password'  => 'nullable|min:6',
        ]);

        $exists = User::where('name', Str::title($validated['name']))
            ->where('contact_number', $validated['phone'])
            ->where('id', '!=', $account->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Another user with the same name and phone already exists.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $account->update([
                'name'            => Str::title($validated['name']),
                'email'           => $validated['email'],
                'contact_number'  => $validated['phone'],
                'address'         => Str::title($validated['address']),
                'role'            => $validated['role'],
                'status'          => $validated['status'],
                'password'        => !empty($validated['password'])
                    ? Hash::make($validated['password'])
                    : $account->password,
            ]);

            DB::commit();
            return response()->json([
                'message' => "Update successfull!",
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Update failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $account = User::findOrFail($id);
            $account->delete();

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
