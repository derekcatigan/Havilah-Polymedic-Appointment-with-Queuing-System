<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 🔢 Role counts (existing)
        $roleCounts = User::select('role')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        // 📊 Appointments per month (analytics)
        $monthlyAppointments = Appointment::select(
            DB::raw('MONTH(starts_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('starts_at', now()->year) // current year only
            ->groupBy(DB::raw('MONTH(starts_at)'))
            ->orderBy('month')
            ->get();

        // 🧠 Prepare labels & values for chart
        $months = [];
        $totals = [];

        foreach ($monthlyAppointments as $data) {
            $months[] = Carbon::create()->month($data->month)->format('F');
            $totals[] = $data->total;
        }

        return view('admin.dashboard', compact(
            'roleCounts',
            'months',
            'totals'
        ));
    }
}
