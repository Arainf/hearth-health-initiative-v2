<?php

namespace App\Services;

use App\Models\Family_history;
use App\Models\Patient;
use App\Models\Records;
use Illuminate\Support\Facades\DB;

class RecordService
{
    public function __construct()
    {
    }

    /**
     * @throws \Throwable
     */
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {

            $toBool = fn ($val) =>
            in_array($val, ['1', 1, true, 'y', 'true'], true);

            $familyData = [
                'Hypertension' => $toBool($data['family_hypertension'] ?? 0),
                'Diabetes' => $toBool($data['family_diabetes-mellitus'] ?? 0),
                'Heart_Attack' => $toBool($data['family_heart-attack-under-60y'] ?? 0),
                'Cholesterol' => $toBool($data['family_cholesterol'] ?? 0),
            ];

            if (!empty($data['patient_id'])) {

                $patient = Patient::with('family_history')
                    ->findOrFail($data['patient_id']);

                $patient->family_history?->update($familyData);

                $patient->update([
                    'last_name' => $data['last_name'],
                    'first_name' => $data['first_name'],
                    'middle_name' => $data['middle_name'],
                    'suffix' => $data['suffix'],
                    'birth_date' => $data['birth_date'],
                    'sex' => $data['sex'],
                    'unit_code' => $data['unit_code'],
                    'weight' => $data['weight'],
                    'height' => $data['height'],
                    'phone_number' => $data['contact'],
                ]);

            } else {

                $family = Family_history::create($familyData);

                $patient = Patient::create([
                    'last_name' => $data['last_name'],
                    'first_name' => $data['first_name'],
                    'middle_name' => $data['middle_name'],
                    'suffix' => $data['suffix'],
                    'birth_date' => $data['birth_date'],
                    'sex' => $data['sex'],
                    'unit_code' => $data['unit_code'],
                    'weight' => $data['weight'],
                    'height' => $data['height'],
                    'phone_number' => $data['contact'],
                    'history_id' => $family->id,
                ]);
            }

            Records::create([
                'patient_id' => $patient->id,
                'cholesterol' => $data['total_cholesterol'],
                'hdl_cholesterol' => $data['hdl_cholesterol'],
                'systolic_bp' => $data['systolic_bp'],
                'fbs' => $data['fbs'],
                'hba1c' => $data['hba1c'],
                'hypertension' => $toBool($data['hypertension_tx'] ?? 0),
                'diabetes' => $toBool($data['diabetes_m'] ?? 0),
                'smoking' => $toBool($data['smoker'] ?? 0),
                'status_id' => 3,
                'generated_id' => null,
                'staff_id' => $data['staff_id'],
            ]);

            return $patient;
        });
    }

    /**
     * @throws \Throwable
     */
    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $record = Records::findOrFail($id);

            $record->update([
                'cholesterol'     => $data['cholesterol'] ?? $record->cholesterol,
                'hdl_cholesterol' => $data['hdl_cholesterol'] ?? $record->hdl_cholesterol,
                'systolic_bp'     => $data['systolic_bp'] ?? $record->systolic_bp,
                'fbs'             => $data['fbs'] ?? $record->fbs,
                'hba1c'           => $data['hba1c'] ?? $record->hba1c,
                'hypertension'    => isset($data['hypertension'])
                    ? (bool) $data['hypertension']
                    : $record->hypertension,
                'diabetes'        => isset($data['diabetes'])
                    ? (bool) $data['diabetes']
                    : $record->diabetes,
                'smoking'         => isset($data['smoking'])
                    ? (bool) $data['smoking']
                    : $record->smoking,
            ]);

            return $record->fresh();
        });
    }
}
