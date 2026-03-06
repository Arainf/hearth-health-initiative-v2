<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dump\trashController;
use App\Models\Family_history;
use App\Models\Patient;
use App\Models\Records;
use App\Models\VPatient;
use App\Models\VRecords;
use App\Services\DropdownService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PatientPageController extends Controller
{

    public object $trash;
    public string $token;
    public string $module;

    public function __construct(){
        $this->module = 'patient';
        $this->trash = new trashController;
        $this->token = $this->trash->encrypt($this->module);
    }
    public function index(Request $request,$token)
    {

        $MODULE_NAME = [
            'icon' => 'users',
            'label' => 'Patients'
        ];

        $user = auth()->user();
        $units = DropdownService::units();

        if(!$user) return redirect('/unauthorized');

        if($request->query('id') || $request->query('mode') || $request->input('id') || $request->input('mode'))
            return $this->menu($request);


        return view('pages.patient',[
            'MODULE_NAME' => $MODULE_NAME,
            'UNITS' => $units,
            'TOKEN' =>  $token,
        ]);
    }

    public function menu(Request $request)
    {
        $rawId = $request->query('id') ?? $request->input('id');
        if (!$rawId) return redirect('/unauthorized');

        $decrypt_ID = $this->trash->decrypt($rawId);
        $rawMode = $request->query('mode') ?? $request->input('mode');
        $mode = $this->trash->decrypt($rawMode) ?: 'edit';

        return match ($mode) {
            'show' => (function() use ($rawId, $decrypt_ID) {
                return view('patients.show', [
                    'TOKEN' => $this->token,
                    'PATIENT' => VPatient::findOrFail($decrypt_ID),
                    'SECRET' => $rawId,
                ]);
            })(),

            'edit' => view('patients.edit', [
                'patient' => VPatient::findOrFail($decrypt_ID),
                'units'   => DropdownService::units(),
            ]),

            'save' => (function () use ($request, $decrypt_ID) {
                $patient = $this->update($request, $decrypt_ID);
                return view('patients.edit', [
                    'patient' => $patient,
                    'units'   => DropdownService::units(),
                ]);
            })(),

            'delete'  => $this->removePatientArchiveRecords($decrypt_ID),

            default => abort(404),
        };
    }

    public function table(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        if ($request->filled('id')) {
            return response()->json(VRecords::patientsOwnRecord($request));
        }

        return response()->json(
            VPatient::datatable($request)
        );
    }


    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $validated = $request->validate([
            'last_name'     => 'required|string|max:255',
            'first_name'    => 'required|string|max:255',
            'middle_name'   => 'nullable|string|max:255',
            'suffix'        => 'nullable|string|max:50',

            'birth_date'    => 'required|date|before:today',
            'sex'           => 'required|in:Male,Female',
            'weight'        => 'required|numeric|min:0|max:700',
            'height'        => 'required|numeric|min:0|max:300',
            'unit_code'          => 'nullable|string|max:255',
            'phone_number'  => 'nullable|string|max:50',
        ]);

        // Family history validation
        $request->validate([
            'family.Hypertension' => 'required|in:0,1',
            'family.Heart_Attack' => 'required|in:0,1',
            'family.Diabetes'     => 'required|in:0,1',
            'family.Cholesterol'  => 'required|in:0,1',
        ]);

        $patient->update($validated);

        Family_history::updateOrCreate(
            ['id' => $patient->history_id],
            [
                'Hypertension' => $request->family['Hypertension'],
                'Heart_Attack' => $request->family['Heart_Attack'],
                'Diabetes'     => $request->family['Diabetes'],
                'Cholesterol'  => $request->family['Cholesterol'],
            ]
        );

        return VPatient::findOrFail($id);
    }


    // ✅ FIX: Accept the integer ID instead of the Request object
    public function removePatientArchiveRecords($id)
    {
        DB::transaction(function () use ($id) {
            $patient = Patient::findOrFail($id);
            $patient->update([
                'deleted_by' => auth()->user()->name
            ]);

            $patient->delete();

            Records::where('patient_id', $id)
                ->update(['is_archived' => 1]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Patient and records archived successfully'
        ]);
    }

}
