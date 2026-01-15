<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Records;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function index()
    {

    }

    public function getSingleRecord($id)
    {
        $record = Records::with(['patient', 'status', 'generated_report'])->find($id);

        if (!$record) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        return response()->json($record);
    }

    public function getStatuses()
    {
        return response()->json(Status::all());
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:records,id',
            'status' => 'required|integer|exists:status,id',
            'approved' => 'nullable|string|max:255',
        ]);

        $record = Records::findOrFail($validated['id']);

        // 🚫 Block double approval
        if ($record->status_id === 1) {
            return response()->json([
                'error' => 'This record has already been approved and cannot be modified.'
            ], 422);
        }

        $record->status_id = $validated['status'];
        $record->approved_by = $validated['approved'];
        $record->save();

        return response()->json(
            $record->fresh()->load('status')
        );
    }

    public function countStatus(Request $request)
    {
        $year = $request->get('year');
        $archived = filter_var($request->get('archived', 'false'), FILTER_VALIDATE_BOOLEAN);

        $statuses = Status::query();

        if ($year && $year !== 'all') {
            $statuses->withCount([
                'records as count' => function ($query) use ($year, $archived) {
                    $query->whereYear('created_at', $year)
                          ->where('is_archived', $archived);
                }
            ]);
        } else {
            $statuses->withCount([
                'records as count' => function ($query) use ($archived) {
                    $query->where('is_archived', $archived);
                }
            ]);
        }

        return response()->json($statuses->get(['id', 'status_name']));
    }

    public function getRecordYears()
    {
        $years = Records::where('is_archived', false)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'asc')
            ->pluck('year');

        return response()->json($years);
    }

    public function getArchiveYears()
    {
        $years = Records::where('is_archived', true)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'asc')
            ->pluck('year');

        return response()->json($years);
    }

    public function getPatientYears()
    {
        $years = Patient::selectRaw('YEAR(birth_date) as year')
            ->distinct()
            ->orderBy('year', 'asc')
            ->pluck('year');

        return response()->json($years);
    }

    public function searchPatient(Request $request)
    {
        $query = $request->input('q');
        $patients = Patient::where('first_name', 'like', "%$query%")
            ->orWhere('last_name', 'like', "%$query%")
            ->limit(5)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'full_name' => "{$p->first_name} {$p->last_name}",
                    'sex' => ucfirst($p->sex),
                    'age' => $p->age,
                    'unit' => $p->unit,
                ];
            });

        return response()->json($patients);
    }

    public function recordsWithPatient($id, $action)
    {
        switch ($action) {
            case "search":

                $record = Patient::with('family_history')->find($id);

                if (!$record) {
                    return response()->json(['error' => 'Record not found'], 404);
                }

                return response()->json([
                    'patient' => $record
                ]);
                break;
            case "create":

                $record = Records::with(['patient', 'patient.family_history'])->find($id);

                if (!$record) {
                    return response()->json(['error' => 'Record not found'], 404);
                }

                return response()->json([
                    'patient' => $record
                ]);
                break;
            case "compare":
                $patient = Patient::with(['records' => function($q){
                    $q->orderBy('created_at', 'desc'); // newest first
                }])->find($id);

                if (!$patient) {
                    return response()->json(['error' => 'Patient not found'], 404);
                }

                // Format response: patient + array of records
                return response()->json([
                    'patient' => $patient,
                    'records' => $patient->records->map(function($r){
                        return [
                            'id' => $r->id,
                            'created_at' => $r->created_at,
                            'cholesterol' => $r->cholesterol,
                            'hdl_cholesterol' => $r->hdl_cholesterol,
                            'systolic_bp' => $r->systolic_bp,
                            'fbs' => $r->fbs,
                            'hba1c' => $r->hba1c,
                            'hypertension' => (bool)$r->hypertension,
                            'diabetes' => (bool)$r->diabetes,
                            'smoking' => (bool)$r->smoking,
                            'generated_report' => $r->generated_report ? [
                                'id' => $r->generated_report->id,
                                'generated_text' => $r->generated_report->generated_text
                            ] : null,
                        ];
                    })
                ]);
                break;
        }
        // eager load records (make sure Records model relationship name is 'records' if you use plural)

    }

    public function getGeneratedContent($id)
    {
        $content = DB::table('generated_reports')
            ->leftJoin('records', 'generated_reports.id', '=', 'records.generated_id')
            ->select(
                'generated_reports.*',
                'records.status_id'
            )
            ->where('generated_reports.id', $id)
            ->first();


        if (!$content) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json($content);
    }

    public function disable(Request $request, $id)
    {
        $authUser = auth()->user();

        if (!$authUser->is_admin) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 403);
        }

        if ($authUser->id == $id) {
            return response()->json([
                'error' => 'You cannot disable your own account'
            ], 422);
        }

        $user = User::findOrFail($id);

        $user->update([
            'is_active' => 0
        ]);

        return response()->json([
            'success' => true,
            'id' => $user->id
        ]);
    }

    public function deleteUser($id){

        $patient = User::find($id);
        $patient->delete();

        return response()->json($patient);
    }

    public function aiAccess(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->ai_access = (bool) $request->input('ai_access');
        $user->save();

        return response()->json([
            'success' => true,
            'ai_access' => $user->ai_access,
        ]);
    }

    public function adminAccess(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->is_admin = (bool) $request->input('is_admin');
        $user->save();

        return response()->json([
            'success' => true,
            'is_admin' => $user->is_admin,
        ]);
    }

    public function doctorAccess(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->is_doctor = (bool) $request->input('is_doctor');
        $user->save();

        return response()->json([
            'success' => true,
            'is_doctor' => $user->is_doctor,
        ]);
    }

}
