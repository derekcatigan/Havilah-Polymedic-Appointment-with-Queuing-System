<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $roleCounts = User::select('role')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        return view('admin.dashboard', compact('roleCounts'));
    }
}
