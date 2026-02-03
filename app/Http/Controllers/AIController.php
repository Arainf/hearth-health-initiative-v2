<?php

namespace App\Http\Controllers;

use App\Models\Records;
use App\Models\Generated_reports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
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

    public function status()
    {
        $user = auth()->user();

        return response()->json([
            'ai_access' => (bool) $user?->ai_access,
            'ai_ready' => (bool) $user?->ai_ready,
        ]);
    }

    public function check($id)
    {
        try {
            $record = Records::with(['patient', 'status', 'generated_report'])->find($id);

            if (!$record) {
                return ['found' => false, 'message' => 'Record not found'];
            }

            // Check if patient exists (might be deleted)
            if (!$record->patient) {
                return ['found' => false, 'message' => 'Patient associated with this record no longer exists'];
            }

            if ($record->hasGeneratedReport()) {
                return [
                    'found' => true,
                    'generated' => true,
                    'message' => 'Already has AI report',
                    'record' => $record,
                ];
            }

            // Validate required data for AI evaluation
            $inputData = $record->getAIInputData();
            if (empty($inputData['record']) || empty($inputData['patient'])) {
                return [
                    'found' => true,
                    'generated' => false,
                    'error' => 'Insufficient data for AI evaluation. Please ensure the record has all required fields.',
                    'input_data' => $inputData,
                    'record' => $record,
                ];
            }

            return [
                'found' => true,
                'generated' => false,
                'input_data' => $inputData,
                'record' => $record,
            ];
        } catch (\Exception $e) {
            Log::error('Error checking record', ['record_id' => $id, 'error' => $e->getMessage()]);
            return ['found' => false, 'message' => 'Error checking record: ' . $e->getMessage()];
        }
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
                'updated' => true
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
            'updated' => false
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
                'generated' => true
            ]);
        }

        $record = $check['record'];
        $inputData = $check['input_data'];

        ksort($inputData);

        $inputData = array_map(function ($v) {
            return $v === null ? '' : $v;
        }, $inputData);

        $ai = $this->getUserAIConfig();

        $systemPrompt = $ai['prompt'] . '
                 Output rules:
                    - HTML only
                    - Allowed tags: p, br, strong
                    - No Markdown or explanations
                    - Preserve order and section labels
                    - Each section header in its own <p>, <strong>
                    - Use <br> between sections
                    - Group related items into paragraphs
                    - Do not repeat labels unnecessarily
                    - One paragraph per grouped topic
                    - Each field label ends with ": "
                    - Leave blank if data is missing
                    - Group pertinent risk factors and management items by theme rather than listing each individually.

                ';

        try {
            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 300,
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $ai['api_key'],
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4.1',
//                gpt-5-nano-2025-08-07

                // 🔒 DETERMINISM CONTROLS
                'temperature' => 0.0,
                'top_p' => 1.0,
                'frequency_penalty' => 0.0,
                'presence_penalty' => 0.0,
                // 'max_tokens' => 1200,

                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Patient info: ' . json_encode($inputData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    ],
                ],
            ]);
//            $response = Http::withOptions([
//                'verify' => false,
//                'timeout' => 300,
//            ])->withHeaders([
//                'Authorization' => 'Bearer ' . $ai['api_key'],
//                'Content-Type' => 'application/json',
//            ])->post('https://router.huggingface.co/v1/chat/completions', [
//                'model' => 'openai/gpt-oss-20b',
//
//                // 🔒 DETERMINISM CONTROLS
//                'temperature' => 0.0,
//                'top_p' => 1.0,
//                'frequency_penalty' => 0.0,
//                'presence_penalty' => 0.0,
//                'seed' => 42,
//
//                'messages' => [
//                    [
//                        'role' => 'system',
//                        'content' => $systemPrompt,
//                    ],
//                    [
//                        'role' => 'user',
//                        'content' => 'Patient info: ' . json_encode($inputData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
//                    ],
//                ],
//            ]);

            // Check for HTTP errors
            if ($response->failed()) {
                $errorBody = $response->json();
                $errorMsg = $errorBody['error']['message'] ?? $errorBody['error'] ?? 'AI service error';
                return response()->json([
                    'error' => 'AI evaluation failed: ' . $errorMsg
                ], $response->status());
            }

            $summary = $response->json('choices.0.message.content')
                ?? $response->json('choices.0.text')
                ?? null;

            if (!$summary) {
                // Log the full response for debugging
                Log::error('AI Response structure unexpected', ['response' => $response->json()]);
                return response()->json([
                    'error' => 'AI service returned unexpected response format'
                ], 500);
            }

            $summary = trim($summary);

            // normalize line breaks
            $summary = preg_replace("/\r\n|\r/", "\n", $summary);

            // remove markdown asterisks (single or double)
            $summary = preg_replace('/\*+/', '', $summary);

            // Validate summary is not empty
            if (empty($summary)) {
                return response()->json([
                    'error' => 'AI service returned empty response'
                ], 500);
            }

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
        } catch (\Exception $e) {
            Log::error('AI Evaluation Error', [
                'record_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to generate evaluation: ' . $e->getMessage()
            ], 500);
        }
    }
}
