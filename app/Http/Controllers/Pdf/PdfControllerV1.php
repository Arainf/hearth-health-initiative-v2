<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Records;
use Mpdf\Mpdf;

class PdfControllerV1 extends Controller
{
    public function export($id)
    {
        $record = Records::with('generated_report')->findOrFail($id);

        if (!$record->generated_report) {
            abort(404, 'No generated report found for this record.');
        }

        $content = $record->generated_report->generated_text;

        $mpdf = new Mpdf([
            'format' => 'A4',
            'margin_top' => 35,
            'margin_bottom' => 35,
            'margin_left' => 12,
            'margin_right' => 12,
            'default_font' => 'Arial',
        ]);

        // ---------- HEADER ----------
        $mpdf->SetHTMLHeader('
        <div style="text-align:center;">
            <h2 style="margin:0; font-size:28px; font-weight:bold;">
                <img src="' . public_path("img/document_logo.png") . '" style="height:40px;">
                Heart Health Initiative
            </h2>
            <p style="font-size:12px; margin:0;">
                A Primary TeleMedicine Health Program<br>
                Ateneo de Zamboanga University
            </p>
            <hr style="border:1px solid #000;">
        </div>
    ');

        // ---------- FOOTER ----------
        $mpdf->SetHTMLFooter('
        <div style="text-align:right; font-size:12px;">
            <img src="' . public_path("img/tex_signature.jpg") . '" style="height:0.8in;">
            <br>
            Fr. Alberto B. Paurom MD SJ<br>
            Lic# 60679
        </div>
    ');

        // ---------- BASE CSS ----------
        $mpdf->WriteHTML('
        <style>
            pre.content {
                font-family: Arial, sans-serif;
                font-size: 12pt;
                line-height: 1.38;

                text-align: justify;
                margin: 0;
            }
        </style>
    ');

        // Column container full height
        $fullHeightWrapper = '
        <div style="min-height:252mm; height:240mm; padding:12px; box-sizing:border-box;">
            <pre class="content">' . htmlspecialchars($content) . '</pre>
        </div>
    ';

        $mpdf->SetColumns(2, 'J');
        $mpdf->WriteHTML($fullHeightWrapper);

        return $mpdf->Output("HHI_Report_{$record->id}.pdf", "D");
    }



}
