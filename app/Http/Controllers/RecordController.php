<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Family_history;
use App\Models\Records;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RecordController extends Controller
{
    public function store(Request $request)
    {
        // VALIDATION
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required',
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'suffix' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'age' => 'nullable|numeric',
            'sex' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'bmi' => 'nullable|string',
            'unit' => 'nullable|string',
            'contact' => 'nullable|string',

            'total_cholesterol' => 'nullable|numeric',
            'hdl_cholesterol' => 'nullable|numeric',
            'systolic_bp' => 'nullable|numeric',
            'fbs' => 'nullable|numeric',
            'hba1c' => 'nullable|numeric',

            'hypertension_tx' => 'nullable',
            'diabetes_m' => 'nullable',
            'smoker' => 'nullable',

            // Hyphenated family fields allowed; keep validation as string/nullable
            'family_hypertension' => 'nullable',
            'family_diabetes-mellitus' => 'nullable',
            'family_heart-attack-under-60y' => 'nullable',
            'family_cholesterol' => 'nullable',

            'patient_id' => 'nullable|numeric'
        ]);

        DB::beginTransaction();

        try {
            // Helper to convert incoming values to boolean
            $toBool = function ($val) {
                return ($val === '1' || $val === 1 || $val === true || $val === 'y' || $val === 'true');
            };

            // normalize family history inputs from request (use null coalescing)
            $familyHypertension = $toBool($request->input('family_hypertension', '0'));
            $familyDiabetes = $toBool($request->input('family_diabetes-mellitus', '0'));
            $familyHeartAttack = $toBool($request->input('family_heart-attack-under-60y', '0'));
            $familyCholesterol = $toBool($request->input('family_cholesterol', '0'));

            // normalize risk radios
            $hypertensionTx = $toBool($request->input('hypertension_tx', '0'));
            $diabetesM = $toBool($request->input('diabetes_m', '0'));
            $smoker = $toBool($request->input('smoker', '0'));

            // ------------------------------------------------
            // 1️⃣ IF patient_id exists → update patient
            // ------------------------------------------------
            if ($request->patient_id) {

                $patient = Patient::with('family_history')->findOrFail($request->patient_id);

                // Update family history (ensure relation exists)
                if ($patient->family_history) {
                    $patient->family_history->update([
                        'Hypertension' => $familyHypertension,
                        'Diabetes' => $familyDiabetes,
                        'Heart_Attack' => $familyHeartAttack,
                        'Cholesterol' => $familyCholesterol,
                    ]);
                } else {
                    // if family_history missing, create it and attach
                    $family = Family_history::create([
                        'Hypertension' => $familyHypertension,
                        'Diabetes' => $familyDiabetes,
                        'Heart_Attack' => $familyHeartAttack,
                        'Cholesterol' => $familyCholesterol,
                    ]);
                    $patient->history_id = $family->id;
                }

                // Update patient info
                $patient->update([
                    'last_name' => $request->last_name,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'suffix' => $request->suffix,
                    'birth_date' => $request->birth_date,
                    'age' => $request->age,
                    'sex' => $request->sex,
                    'unit' => $request->unit,
                    'weight' => $request->weight,
                    'height' => $request->height,
                    'bmi' => $request->bmi,
                    'phone_number' => $request->contact,
                ]);

            } else {

                // ------------------------------------------------
                // 2️⃣ NEW PATIENT → create family history + patient
                // ------------------------------------------------
                $family = Family_history::create([
                    'Hypertension' => $familyHypertension,
                    'Diabetes' => $familyDiabetes,
                    'Heart_Attack' => $familyHeartAttack,
                    'Cholesterol' => $familyCholesterol,
                ]);

                $patient = Patient::create([
                    'last_name' => $request->last_name,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'suffix' => $request->suffix,
                    'birth_date' => $request->birth_date,
                    'age' => $request->age,
                    'sex' => $request->sex,
                    'unit' => $request->unit,
                    'weight' => $request->weight,
                    'height' => $request->height,
                    'bmi' => $request->bmi,
                    'phone_number' => $request->contact,
                    'history_id' => $family->id,
                ]);
            }

            // ------------------------------------------------
            // 3️⃣ ALWAYS CREATE A NEW RECORD
            // ------------------------------------------------
            Records::create([
                'patient_id' => $patient->id,

                'cholesterol' => $request->total_cholesterol,
                'hdl_cholesterol' => $request->hdl_cholesterol,
                'systolic_bp' => $request->systolic_bp,
                'fbs' => $request->fbs,
                'hba1c' => $request->hba1c,

                'hypertension' => $hypertensionTx,
                'diabetes' => $diabetesM,
                'smoking' => $smoker,

                'status_id' => 3, // pending
                'generated_id' => null,
                'staff_id' => $request->staff_id,

            ]);

            DB::commit();

            // return JSON or redirect depending on your UI needs. original used back()
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }else{
                return response()->json([
                    'success' => true,
                    'patient_id' => $patient->id,
                    'message' => 'Record saved successfully!',
                ]);


            }

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'cholesterol' => 'nullable|numeric',
            'hdl_cholesterol' => 'nullable|numeric',
            'systolic_bp' => 'nullable|numeric',
            'fbs' => 'nullable|numeric',
            'hba1c' => 'nullable|numeric',
            'hypertension' => 'nullable|boolean',
            'diabetes' => 'nullable|boolean',
            'smoking' => 'nullable|boolean',
        ]);

        try {
            $record = Records::findOrFail($id);

            // Only update fields that are provided
            $updateData = [];
            if ($request->has('cholesterol')) $updateData['cholesterol'] = $request->cholesterol;
            if ($request->has('hdl_cholesterol')) $updateData['hdl_cholesterol'] = $request->hdl_cholesterol;
            if ($request->has('systolic_bp')) $updateData['systolic_bp'] = $request->systolic_bp;
            if ($request->has('fbs')) $updateData['fbs'] = $request->fbs;
            if ($request->has('hba1c')) $updateData['hba1c'] = $request->hba1c;
            if ($request->has('hypertension')) $updateData['hypertension'] = (bool)$request->hypertension;
            if ($request->has('diabetes')) $updateData['diabetes'] = (bool)$request->diabetes;
            if ($request->has('smoking')) $updateData['smoking'] = (bool)$request->smoking;

            $record->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully',
                'record' => $record
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update record: ' . $e->getMessage()
            ], 500);
        }
    }

}
