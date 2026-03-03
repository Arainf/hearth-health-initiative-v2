<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dump\trashController;
use App\Models\Patient;
use App\Models\Records;
use App\Models\VPatient;
use App\Models\VRecords;
use App\Services\DropdownService;
use Illuminate\Http\Request;

class ArchivePageController extends Controller
{
    public object $trash;
    public string $token;
    public string $module;

    public function __construct(){
        $this->module = 'archive';
        $this->trash = new trashController;
        $this->token = $this->trash->encrypt($this->module);
    }

    public function index(Request $request, $token)
    {
        $user = auth()->user();
        if(!$user->is_Admin()) return redirect('unauthorized');

        /**
         * CORE FILE
         */
        $MODULE_NAME = [
            'icon' => 'package',
            'label' => 'Archive'
        ];

        $currentYear = now()->year;
        $years = DropdownService::years();
        $units = DropdownService::units();

        if($request->query('id') || $request->query('mode') || $request->input('id') || $request->input('mode'))
            return $this->menu($request);

        return view('pages.archive',
        [
            'MODULE_NAME' => $MODULE_NAME,
            'YEARS' => $years,
            'CURRENT' => $currentYear,
            'TOKEN' => $token,
            'UNITS' => $units,
        ]);

    }

    public function menu(Request $request)
    {
        // Support both GET query params and DELETE/POST body inputs
        $rawId = $request->query('id') ?? $request->input('id');

        if (!$rawId) {
            return redirect('/unauthorized');
        }

        $decrypt_ID = $this->trash->decrypt($rawId);

        $rawMode = $request->query('mode') ?? $request->input('mode');
        $mode = $this->trash->decrypt($rawMode);

        return match ($mode) {
            'show' => (function() use ($rawId, $decrypt_ID) {
                return view('archive.show', [
                    'TOKEN' => $this->token,
                    'PATIENT' => VPatient::findOrFail($decrypt_ID),
                    'SECRET' => $rawId,
                ]);
            })(),
            'restore' => $this->handleRestore($decrypt_ID),
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

        $status = $request->get('status');
        $unit = $request->get('unit');
        $year = $request->get('year');
        $archived = filter_var($request->get('archived', 'false'), FILTER_VALIDATE_BOOLEAN);

        $tableData = VPatient::datatableArchive($request, auth()->user());

        return response()->json([
            'draw'            => $tableData['draw'],
            'recordsTotal'    => $tableData['recordsTotal'],
            'recordsFiltered' => $tableData['recordsFiltered'],
            'data'            => $tableData['data'],
        ]);
    }

    private function handleRestore($id)
    {
        try {
            Patient::withTrashed()->find($id)->restore();

            Records::where('patient_id', $id) ->update([
                'is_archived' => 0,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


}
