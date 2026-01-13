<?php

namespace App\Http\Controllers;

use App\Models\Records;
use App\Models\Generated_reports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OPENAIController extends Controller
{
    private function getUserAIConfig()
    {
        $user = auth()->user();

        if (!$user || !$user->ai_access) {
            abort(403, 'AI access not allowed.');
        }

        if (!$user->openai_api_key) {
            abort(422, 'No OpenAI API key configured.');
        }

        return [
            'api_key' => decrypt($user->openai_api_key),
            'prompt' => $user->ai_prompt,
        ];
    }

    public function check($id)
    {
        $record = Records::with(['patient', 'status', 'generated_report'])->find($id);

        if (!$record) {
            return ['found' => false, 'message' => 'Record not found'];
        }

        if ($record->hasGeneratedReport()) {
            return [
                'found' => true,
                'generated' => true,
                'message' => 'Already has AI report',
                'record' => $record,
            ];
        }

        return [
            'found' => true,
            'generated' => false,
            'input_data' => $record->getAIInputData(),
            'record' => $record,
        ];
    }

    public function editChangesSave(Request $request, $id)
    {
        $check = $this->check($id);

        if (!$check['found']) {
            return response()->json(['error' => $check['message']], 404);
        }

        if ($check['generated']) {
            $record = $check['record'];
            $report = $record->generated_report;

            if (!$report) {
                return response()->json(['error' => 'Generated report not found'], 404);
            }

            $report->update([
                'generated_text' => $request->input('content'),
                'staff_updates' => auth()->id() ?? 0,
            ]);

            return response()->json([
                'success' => true,
                'report_id' => $report->id,
                'updated' => true,
            ]);
        }

        $record = $check['record'];

        $report = Generated_reports::create([
            'generated_text' => $request->input('content'),
            'staff_generated' => auth()->id() ?? 0,
            'staff_updates' => '',
        ]);

        $record->update(['generated_id' => $report->id]);

        return response()->json([
            'success' => true,
            'report_id' => $report->id,
            'updated' => false,
        ]);
    }

    public function evaluateRecord(Request $request, $id)
    {
        set_time_limit(300);
        ini_set('max_execution_time', 300);

        $check = $this->check($id);

        if (!$check['found']) {
            return response()->json(['error' => $check['message']], 404);
        }

        if ($check['generated']) {
            return response()->json([
                'message' => $check['message'],
                'generated' => true,
            ]);
        }

        $record = $check['record'];
        $inputData = $check['input_data'];

        $ai = $this->getUserAIConfig();

        $systemPrompt = $ai['prompt'] ?: '
Then, generate the Annual Medical Report in clean, text-only format (no PDF), following this structure:

ANNUAL MEDICAL REPORT
Date: [Date Today]

A. Personal Information
Name:
Unit:
Date of Birth:
Age/Sex:
Weight:
Height:
BMI:
Contact:

B. Clinical Data
Cholesterol:
HDL:
BP:
FBS:
HbA1c:

C. CVD Risk Assessment
10-year risk (WHO SEAR):
Pertinent risk factors:

D. Management Plan
Non-drug measures:
Drug measures:

E. Follow-up Plan
Timing and monitoring:

F. Confidentiality Note
This report contains confidential medical information intended solely for the designated individual. Unauthorized access, use, disclosure, or distribution of its contents is strictly prohibited and may be subject to legal action. The findings and recommendations are based solely on the information available at the time of evaluation.

Formatting Rules (STRICT):
- Plain text only
- No markdown
- No asterisks
- No underscores
- Preserve spacing and line breaks exactly
- Do not add commentary
- Output must behave exactly like text inside a <pre> tag
';

        $response = Http::withOptions([
            'verify' => false,
            'timeout' => 300,
        ])->withHeaders([
            'Authorization' => 'Bearer ' . $ai['api_key'],
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4.1-mini',
            'temperature' => 0,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ],
                [
                    'role' => 'user',
                    'content' => 'Patient info: ' . json_encode($inputData, JSON_PRETTY_PRINT),
                ],
            ],
        ]);

        $summary = $response->json('choices.0.message.content');

        $report = Generated_reports::create([
            'generated_text' => $summary,
            'staff_generated' => auth()->id() ?? 0,
            'staff_updates' => '',
        ]);

        $record->update([
            'generated_id' => $report->id,
            'status_id' => 2,
        ]);

        return response()->json([
            'generated' => true,
            'record_id' => $record->id,
            'generated_id' => $report->id,
            'summary' => $summary,
        ]);
    }
}
