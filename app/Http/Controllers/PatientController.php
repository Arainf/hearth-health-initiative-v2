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

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        // --------------------
        // VALIDATION
        // --------------------
        $validated = $request->validate([
            'last_name'     => 'required|string|max:255',
            'first_name'    => 'required|string|max:255',
            'middle_name'   => 'nullable|string|max:255',
            'suffix'        => 'nullable|string|max:50',

            // 👇 Birth date is now the source of truth
            'birth_date'    => 'required|date|before:today',

            'sex'           => 'required|in:Male,Female',
            'weight'        => 'required|numeric|min:0',
            'height'        => 'required|numeric|min:0',
            'unit'          => 'nullable|string|max:255',
            'phone_number'  => 'nullable|string|max:50',
        ]);

        // Family history validation
        $request->validate([
            'family.Hypertension' => 'required|in:0,1',
            'family.Heart_Attack' => 'required|in:0,1',
            'family.Diabetes'     => 'required|in:0,1',
            'family.Cholesterol'  => 'required|in:0,1',
        ]);

        // --------------------
        // DERIVED FIELDS
        // --------------------

        // 🧮 Compute age from birth_date
        $birthDate = Carbon::parse($validated['birth_date']);
        $validated['age'] = $birthDate->age;

        // 🧮 Recalculate BMI (always server-side)
        $validated['bmi'] = round(
            $validated['weight'] / pow($validated['height'] / 100, 2),
            2
        );

        // --------------------
        // UPDATE PATIENT
        // --------------------
        $patient->update($validated);

        // --------------------
        // UPDATE FAMILY HISTORY
        // --------------------
        Family_history::updateOrCreate(
            ['id' => $patient->history_id],
            [
                'Hypertension' => $request->family['Hypertension'],
                'Heart_Attack' => $request->family['Heart_Attack'],
                'Diabetes'     => $request->family['Diabetes'],
                'Cholesterol'  => $request->family['Cholesterol'],
            ]
        );

        return redirect("/patients/{$patient->id}/edit")
            ->with('success', 'Patient updated successfully.');
    }



}
