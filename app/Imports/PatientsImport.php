<?php

namespace App\Imports;

use App\Models\Family_history;
use App\Models\Patient;
use App\Models\Records;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\{
    ToModel,
    WithHeadingRow,
    WithStartRow
};

class PatientsImport implements ToModel, WithHeadingRow
{
    /**
     * Headings are in row 2
     */
    public function headingRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        if (empty($row['id'])) {
            return null; // ID is required
        }

        $yesNo = fn($v) => strtolower(trim($v ?? '')) === 'yes' ? 1 : 0;

        return DB::transaction(function () use ($row, $yesNo) {

            $patient = Patient::find($row['id']);

            if (!$patient) {
                return null; // skip if patient not found
            }

            /*
            |--------------------------------------------------------------------------
            | 1️⃣ Update or Create Family History
            |--------------------------------------------------------------------------
            */

            if ($patient->family_history) {

                $patient->family_history->update([
                    'Hypertension' => $yesNo($row['hypertension']),
                    'Diabetes' => $yesNo($row['diabetes_mellitus']),
                    'Heart_Attack' => $yesNo($row['heart_attack_under_60y']),
                    'Cholesterol' => $yesNo($row['cholesterol']),
                ]);

            }

            /*
            |--------------------------------------------------------------------------
            | 2️⃣ Update Patient Basic Info
            |--------------------------------------------------------------------------
            */

            $patient->update([
                'birth_date' => $row['birthday'],
                'sex' => $row['sex'],
                'weight' => $row['weight_kg'],
                'height' => $row['height_cm'],
                'phone_number' => $row['phone_number'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | 3️⃣ Insert New Clinical Record
            |--------------------------------------------------------------------------
            */

            $dateValue = $row['date_record_yyyy_mm_dd'] ?? null;

            if ($dateValue) {

                if (is_numeric($dateValue)) {
                    $dateValue = Date::excelToDateTimeObject($dateValue)
                        ->format('Y-m-d');
                }
            }


            Records::create([
                'patient_id' => $patient->id,

                'cholesterol' => $row['total_cholesterol_mgdl'],
                'hdl_cholesterol' => $row['hdl_cholesterol_mgdl'],
                'systolic_bp' => $row['systolic_bp_mmhg'],
                'fbs' => $row['fbs_mgdl'],
                'hba1c' => $row['hba1c'],

                'hypertension' => $yesNo($row['hypertension_tx_yesno']),
                'diabetes' => $yesNo($row['diabetes_m_yesno']),
                'smoking' => $yesNo($row['current_smoker_yesno']),

                'status_id' => 3,
                'generated_id' => null,
                'is_archived' => 0,
                'staff_id' => auth()->user()->name,
                'approved_by' => null,

                'created_at' => !empty($dateValue)
                    ? Carbon::parse($dateValue)
                    : now(),
            ]);

            return $patient;
        });
    }
}
