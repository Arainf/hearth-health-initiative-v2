<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Records;
use Mpdf\Mpdf;

class PdfController extends Controller
{
    public function style()
    {
        return'
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 14px;
                line-height: 1.4;
            }

            p {
                margin: 4px 0;
            }

            ul, ol {
                margin-left: 18px;
                margin-bottom: 8px;
            }

            li {
                margin-bottom: 4px;
            }

            img {
                max-width: 100%;
            }

            /* Optional: page-break helpers */
            .page-break {
                page-break-after: always;
            }
             .report-header {
                width: 100%;
                font-size: 14px;
                margin-bottom: 8px;
            }

            .report-header td {
                padding: 0;
                vertical-align: top;
            }

            .section-title {
                font-weight: bold;
                margin: 8px 0 4px;
                font-size: 14px;
            }

            table.medical-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }

            table.medical-table td {
                border: 1px solid #000;
                padding: 6px 8px;
                vertical-align: top;
            }

            .label {
                display: block;
                font-weight: normal;
                margin-bottom: 4px;
            }

            .value {
                font-weight: bold;
            }
        </style>
        ';
    }

    public function sectionA($patient)
    {
        if(!$patient){ abort(404,'No patient found'); }


        $full_name      = $patient->first_name.' '.$patient->middle_name.' '.$patient->last_name.' '.$patient->suffix;
        $unit           = $patient->unit;
        $dob            = $patient->birth_date ? $patient->birth_date->format('d/m/Y') : " ";
        $age            = $patient->age;
        $weight         = $patient->weight;
        $height         = $patient->height;
        $bmi            = $patient->bmi;
        $contact        = $patient->phone_number;

        return '
        <p class="section-title">A. Personal Information</p>
        <table class="medical-table">
            <tr>
                <td colspan="2">
                    <span class="label">Name:</span>
                    <span class="value">'. $full_name .'</span>
                </td>
                <td colspan="2">
                    <span class="label">Unit:</span>
                    <span class="value">'. $unit .'</span>
                </td>
                <td colspan="2">
                    <span class="label">Date of Birth:</span>
                    <span class="value">'. $dob .'</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Weight (kg):</span>
                    <span class="value">'. $weight .'</span>
                </td>
                <td>
                    <span class="label">Height (cm):</span>
                    <span class="value">'. $height .'</span>
                </td>
                <td>
                    <span class="label">BMI:</span>
                    <span class="value">'. $bmi .'</span>
                </td>
                 <td>
                    <span class="label">Age:</span>
                    <span class="value">'. $age .'</span>
                </td>
                <td colspan="2">
                    <span class="label">Contact:</span>
                    <span class="value">'. $contact .'</span>
                </td>
            </tr>
        </table>';
    }

    public function sectionB($record)
    {
        if(!$record){ abort(404,'No record found'); }


        $chol          = $record->cholesterol. ' mg/dl';
        $hdl           = $record->hdl_cholesterol. ' mg/dl';
        $bp            = $record->systolic_bp. ' mmHg';
        $fbs           = $record->fbs;
        $hbac          = $record->hba1c;


        return '
        <br/>
        <p class="section-title">B. Clinical Data</p>

        <table class="medical-table">
            <tr>
                <td>
                    <span class="label">Cholesterol:</span>
                    <span class="value">'. $chol .'</span>
                </td>
                <td>
                    <span class="label">HDL:</span>
                    <span class="value">'. $hdl .'</span>
                </td>
                <td>
                    <span class="label">BP:</span>
                    <span class="value">'. $bp .'</span>
                </td>
                <td>
                    <span class="label">FBS:</span>
                    <span class="value">'. $fbs .'</span>
                </td>
                <td>
                    <span class="label">HbA1c:</span>
                    <span class="value">'. $hbac .'</span>
                </td>
            </tr>
        </table>
        <br/>';
    }

    public function sectionF()
    {
        return '
        <br>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <!-- LEFT: Disclaimer -->
                <td width="65%" style="text-align:left; vertical-align:bottom;">
                <p  style="font-size:10px; margin:0; color:#808080;"><strong>Confidentiality Note</strong></p>
                    <p style="font-size:10px; margin:0; color:#808080;">
                        <em>
                            This report contains confidential medical information intended solely for the designated individual.
                            Unauthorized access, use, disclosure, or distribution of its contents is strictly prohibited and may be
                            subject to legal action. The findings and recommendations are based solely on the information available
                            at the time of evaluation.
                        </em>
                    </p>
                </td>

                <!-- RIGHT: Signature -->
                <td width="35%" style="text-align:right; vertical-align:bottom; font-size:12px;">
                    <img src="'. public_path('img/tex_signature.jpg') .'" style="height:0.8in; "><br>
                    Fr. Alberto B. Paurom MD SJ<br>
                    Lic# 60679
                </td>
            </tr>
        </table>'
        ;
    }



    public function export($id)
    {
        $record = Records::with('generated_report', 'patient')->findOrFail($id);

        if (!$record->generated_report) {
            abort(404, 'No generated report found for this record.');
        }

        $content = $record->generated_report->generated_text;

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_top'    => 35,
            'margin_bottom' => 35,
            'margin_left'   => 12,
            'margin_right'  => 12,
            'default_font'  => 'Arial',
        ]);

        $date = $record->created_at->format('F d, Y');


        $mpdf->SetHTMLHeader('
            <div style="text-align:center;">
                <h2 style="margin:0; font-size:28px; font-weight:bold;">
                    <img src="' . public_path("img/document_logo.png") . '" style="height:40px; vertical-align:middle;">
                    Heart Health Initiative
                </h2>
                <p style="font-size:12px; margin:0; text-align: center">
                    A Primary TeleMedicine Health Program<br>
                    Ateneo de Zamboanga University
                </p>
                <hr style="border:1px solid #000;">
            </div>

        ');

        $mpdf->WriteHTML($this->style());
        $mpdf->WriteHTML('
        <table class="report-header">
            <tr>
                <td><strong>ANNUAL MEDICAL REPORT</strong></td>
                <td style="text-align:right;">
                    Date: <span id="template-date">'. $date .'</span>
                </td>
            </tr>
        </table>
        ');
        $mpdf->WriteHTML($this->sectionA($record->patient));
        $mpdf->WriteHTML($this->sectionB($record));
        $mpdf->WriteHTML($content);
//        $mpdf->WriteHTML($this->sectionF());
        $mpdf->SetHTMLFooter(
            $this->sectionF()
        );


        return $mpdf->Output(
            "HHI_Report_{$record->patient->last_name}.pdf",
            "D"
        );
    }
}
