<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $homepageAds = Ad::where('status', 'active')
            ->where('position', 'homepage')
            ->latest()
            ->get();

        return view('welcome', compact('homepageAds'));
    }
}
