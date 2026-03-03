<?php

namespace App\Imports;

use App\Models\Patient;
use App\Models\VPatient;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\{
    ToCollection,
    WithHeadingRow
};
use Illuminate\Support\Collection;

class PatientsPreviewImport implements ToCollection, WithHeadingRow
{
    public $validCount = 0;
    public $invalidCount = 0;
    public $unitCode;
    public $previewData = [];
    public $errors = [];

    public function __construct($unitCode)
    {
        $this->unitCode = $unitCode;
    }
    public function headingRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {

        $max = Patient::where('unit_code', $this->unitCode)->count();

        foreach ($rows as $index => $row) {

            // ✅ Stop processing when max reached
            if ($index >= $max) {
                break;
            }

            // Skip empty
            if (empty($row['id'])) {
                $this->invalidCount++;
                $this->errors[] = "Row ".($index+3)." missing ID.";
                continue;
            }

            // Check ID exists within the unit
            $patient = Patient::where('unit_code', $this->unitCode)
                ->where('id', $row['id'])
                ->exists();

            if (!$patient) {
                $this->invalidCount++;
                $this->errors[] = "Row ".($index+3)." invalid ID.";
                continue;
            }

            // Check required fields
//            if (empty($row['date_record_yyyy_mm_dd'])) {
//                $this->invalidCount++;
//                $this->errors[] = "Row ".($index+3)." missing date.";
//                continue;
//            }

            $dateValue = $row['date_record_yyyy_mm_dd'] ?? null;

            if ($dateValue) {

                if (is_numeric($dateValue)) {
                    $dateValue = Date::excelToDateTimeObject($dateValue)
                        ->format('Y-m-d');
                }
            }

            $this->previewData[] = [
                'row' => $index + 1,

                'full_name' => $row['full_name'],
                'birthday' => $row['birthday'],
                'sex' => $row['sex'],
                'weight' => $row['weight_kg'],
                'height' => $row['height_cm'],
                'phone_number' => $row['phone_number'],

                'hypertension' => $row['hypertension'],
                'diabetes_mellitus' => $row['diabetes_mellitus'],
                'heart_attack_under_60y' => $row['heart_attack_under_60y'],
                'cholesterol' => $row['cholesterol'],

                'total_cholesterol' => $row['total_cholesterol_mgdl'],
                'hdl_cholesterol' => $row['hdl_cholesterol_mgdl'],
                'systolic_bp' => $row['systolic_bp_mmhg'],
                'fbs' => $row['fbs_mgdl'],
                'hba1c' => $row['hba1c'],

                'hypertension_tx' => $row['hypertension_tx_yesno'],
                'diabetes_m' => $row['diabetes_m_yesno'],
                'smoking' => $row['current_smoker_yesno'],

                'date_record' =>  $dateValue,
            ];

            $this->validCount++;
        }
    }
}
