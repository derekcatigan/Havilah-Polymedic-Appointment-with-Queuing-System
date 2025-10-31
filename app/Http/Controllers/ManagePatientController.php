<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ManagePatientController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        $query->whereIn('role', ['patient']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(10);

        return view('admin.manage-patient', compact('patients'));
    }
}
