<?php

namespace App\Exports;

use App\Models\VPatient;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithStyles,
    WithEvents,
    WithMapping
};
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use Maatwebsite\Excel\Events\AfterSheet;
class PatientsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithEvents,
    WithCustomStartCell
{
    protected $unitCode;

    protected $rowCount = 0;

    public function __construct($unitCode)
    {
        $this->unitCode = $unitCode;
    }

    public function collection()
    {
        $data = VPatient::where('unit_code', $this->unitCode)
            ->select([
                'id', // 👈 add this
                'last_name',
                'first_name',
                'middle_initial',
                'suffix',
                'birthday',
                'sex',
                'weight',
                'height',
                'phone_number',
                'hypertension',
                'diabetes_mellitus',
                'heart_attack_under_60y',
                'cholesterol'
            ])
            ->get();
        $this->rowCount = $data->count();

        return $data;
    }

    // ✅ Now this will work
    public function map($patient): array
    {
        $fullName = trim(
            $patient->last_name . ', ' .
            $patient->first_name . ' ' .
            ($patient->middle_initial ? $patient->middle_initial . '. ' : '') .
            ($patient->suffix ?? '')
        );

        return [
            $patient->id,
            'UPDATE',
            $fullName,
            $patient->birthday,
            $patient->sex,
            $patient->weight,
            $patient->height,
            $patient->phone_number,

            // 👇 Convert 1/0 to Yes/No
            $patient->hypertension ? 'Yes' : 'No',
            $patient->diabetes_mellitus ? 'Yes' : 'No',
            $patient->heart_attack_under_60y ? 'Yes' : 'No',
            $patient->cholesterol ? 'Yes' : 'No',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'MODE',
            'Full Name',
            'Birthday',
            'Sex',
            'Weight (KG)',
            'Height (CM)',
            'Phone Number',

            // Family History
            'Hypertension',
            'Diabetes Mellitus',
            'Heart Attack under 60y',
            'Cholesterol',

            //  RISK FACTORS
            'Total Cholesterol (mg/dl)',
            'HDL Cholesterol (mg/dl)',
            'Systolic BP (mmHg)',
            'FBS (mg/dl)',
            'HbA1c (%)',
            'Hypertension Tx (Yes/No)',
            'Diabetes M (Yes/No)',
            'Current Smoker (Yes/No)',
            'DATE RECORD (YYYY-MM-DD)'
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $this->rowCount + 2;
                $noticeRow = $lastRow + 1;

                $sheet = $event->sheet->getDelegate();
                $sheet->setCellValue('A1', 'UNIT: ' . $this->unitCode);
                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $sheet->setCellValue('I1', 'FAMILY HISTORY' );
                $sheet->mergeCells('I1:L1');
                $sheet->getStyle('I1')->getFont()->setBold(true)->setSize(14);

                $sheet->setCellValue('M1', 'RISK FACTORS' );
                $sheet->mergeCells('M1:T1');
                $sheet->getStyle('M1')->getFont()->setBold(true)->setSize(14);

                // Style it
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                ]);

                $sheet->getStyle('I1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                ]);

                $sheet->getStyle('M1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                ]);

                $sheet->getStyle('C2:H2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFD9E1F2', // Light blue
                        ],
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => [
                                'argb' => 'FF000000',
                            ],
                        ],
                    ],
                ]);

                $sheet->getStyle('I2:L2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'F2DCDB', // Light blue
                        ],
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => [
                                'argb' => 'FF000000',
                            ],
                        ],
                    ],
                ]);

                $sheet->getStyle('M2:T2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'FDE9D9', // Light blue
                        ],
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => [
                                'argb' => 'FF000000',
                            ],
                        ],
                    ],
                ]);

                // Hide ID column
                $sheet->getColumnDimension('A')->setVisible(false);

                // Hide MODE column
                $sheet->getColumnDimension('B')->setVisible(false);

                // Set custom widths
                $sheet->getColumnDimension('C')->setWidth(28); // Full Name
                $sheet->getColumnDimension('D')->setWidth(18); // Birthday
                $sheet->getColumnDimension('E')->setWidth(12); // Sex
                $sheet->getColumnDimension('F')->setWidth(15); // Weight
                $sheet->getColumnDimension('G')->setWidth(15); // Height
                $sheet->getColumnDimension('H')->setWidth(20); // Phone Number

                $sheet->getColumnDimension('I')->setWidth(20); // Hypertension
                $sheet->getColumnDimension('J')->setWidth(20); // Diabetes Mellitus
                $sheet->getColumnDimension('K')->setWidth(30); // Heart Attack under 60y
                $sheet->getColumnDimension('L')->setWidth(20); // Cholesterol

                $sheet->getColumnDimension('M')->setWidth(30); // Total Cholesterol
                $sheet->getColumnDimension('N')->setWidth(30); // HDL Cholesterol
                $sheet->getColumnDimension('O')->setWidth(30); // Systolic BP
                $sheet->getColumnDimension('P')->setWidth(18); // FBS
                $sheet->getColumnDimension('Q')->setWidth(18); // HbA1c
                $sheet->getColumnDimension('R')->setWidth(25); // Hypertension Tx
                $sheet->getColumnDimension('S')->setWidth(25); // Diabetes M
                $sheet->getColumnDimension('T')->setWidth(25); // Current Smoker

                $sheet->getColumnDimension('U')->setWidth(30); // Date Recorded

                $sheet->getStyle("U3:U{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD2);

                $sheet->getStyle("U2:U{$lastRow}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '8DB4E2',
                        ],
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => [
                                'argb' => 'FF000000',
                            ],
                        ],
                    ],
                ]);


                for ($row = 3; $row <= $lastRow; $row++) {

                    $validation = $sheet->getCell("U{$row}")->getDataValidation();

                    $validation->setType(DataValidation::TYPE_DATE);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setOperator(DataValidation::OPERATOR_BETWEEN);

                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);

                    $validation->setErrorTitle('Invalid Date');
                    $validation->setError('Please enter a valid date (YYYY-MM-DD).');

                    // Restrict allowed range
                    $validation->setFormula1('DATE(1900,1,1)');
                    $validation->setFormula2('DATE(2100,12,31)');
                }

                for ($row = 3; $row <= $lastRow; $row++) {

                    foreach (['I','J','K','L','R','S','T'] as $col) {

                        $validation = $sheet->getCell("{$col}{$row}")->getDataValidation();

                        $validation->setType(DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(DataValidation::STYLE_STOP);
                        $validation->setAllowBlank(true);

                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);

                        $validation->setErrorTitle('Invalid Selection');
                        $validation->setError('Only Yes or No is allowed.');

                        $validation->setFormula1('"Yes,No"');
                    }
                }

                for ($row = 3; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell("E{$row}")->getDataValidation();
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1('"Male,Female"');
                }


                for ($row = 3; $row <= $lastRow; $row++) {
                    foreach (['I','J','K','L'] as $col) {
                        $validation = $sheet->getCell("{$col}{$row}")->getDataValidation();
                        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                        $validation->setAllowBlank(true);
                        $validation->setShowDropDown(true);
                        $validation->setFormula1('"Yes,No"');
                    }
                }

                for ($row = 3; $row <= $lastRow; $row++) {
                    foreach (['R', 'S', 'T'] as $col) {
                        $validation = $sheet->getCell("{$col}{$row}")->getDataValidation();
                        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                        $validation->setAllowBlank(true);
                        $validation->setShowDropDown(true);
                        $validation->setFormula1('"Yes,No"');
                    }
                }


                // Lock headings
                $sheet->getStyle('A1:T2')
                    ->getProtection()
                    ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);

                // Unlock data rows
                $sheet->getStyle("B3:U{$lastRow}")
                    ->getProtection()
                    ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

                $sheet->setCellValue("C{$noticeRow}",
                    "⚠️ No additional rows are allowed. Please create or update records directly in the system."
                );

                $sheet->setCellValue('Z1', 'HEART_HEALTH_INITIATIVE_2026');
                $sheet->setCellValue('Z2', $this->unitCode);
                $sheet->getColumnDimension('Z')->setVisible(false);

                $sheet->getStyle("C{$noticeRow}:U{$noticeRow}")->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'color' => ['argb' => 'FF808080'], // gray text
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF2F2F2'], // light gray background
                    ],
                ]);

                $sheet->getStyle("V1:Z1000")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFEDEDED'],
                    ],
                ]);

                $sheet->getStyle("V1:Z1000")
                    ->getProtection()
                    ->setLocked(Protection::PROTECTION_PROTECTED);

// Merge across full visible range
                $sheet->mergeCells("C{$noticeRow}:U{$noticeRow}");
                // Freeze header
                $sheet->freezePane('B3');

                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setInsertRows(false);
                $sheet->getProtection()->setDeleteRows(false);
                $sheet->getProtection()->setPassword('1234');
            },
        ];
    }
}
