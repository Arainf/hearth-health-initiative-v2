<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Family_history;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        return view('patient');
    }

    public function show($id)
    {
        $patient = Patient::with('family_history')->findOrFail($id);
        return view('patients.show', compact('patient'));
    }

    public function edit($id)
    {
        $patient = Patient::with('family_history')->findOrFail($id);
        return view('patients.edit', compact('patient'));
    }





}
