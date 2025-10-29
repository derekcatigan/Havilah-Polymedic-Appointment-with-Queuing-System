<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManagePatientController extends Controller
{
    public function index()
    {
        return view('admin.manage-patient');
    }
}
