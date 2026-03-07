<?php

namespace App\Http\Controllers\Pages;

use App\Exports\PatientsExport;
use App\Http\Controllers\AIController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Dump\trashController;
use App\Http\Controllers\Pdf\PdfController;
use App\Imports\PatientsImport;
use App\Imports\PatientsPreviewImport;
use App\Models\Generated_reports;
use App\Models\Records;
use App\Models\Status;
use App\Models\VPatient;
use App\Services\RecordService;
use App\Models\VRecords;
use App\Services\DropdownService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DoctorPageController extends Controller
{

    public object $trash;
    public string $token;
    public string $module;
    private RecordService $recordService;

    public function __construct(RecordService $recordService){
        $this->module               = 'doctor';
        $this->trash                = new trashController;
        $this->token                = $this->trash->encrypt($this->module);
        $this->recordService        = $recordService;
    }


    public function index(Request $request, $token)
    {
        $MODULE_NAME = [
            'icon' => 'stethoscope',
            'label' => 'Doctor'
        ];


        $currentYear = now()->year;
        $years = DropdownService::years();
        $status = DropdownService::status($currentYear, false);
        $units = DropdownService::units();
        $user = auth()->user();

       if(!$user->is_Doctor()) return redirect('unauthorized');

        if($request->query('id') || $request->query('mode') || $request->input('id') || $request->input('mode'))
            return $this->menu($request);

       return view('pages.doctor',
           [
               'MODULE_NAME' => $MODULE_NAME,
               'YEARS' => $years,
               'CURRENT' => $currentYear,
               'STATUS' => $status,
               'UNITS' => $units,
               'TOKEN' => $token
       ]);


    }


    public function menu(Request $request)
    {
        $rawId  = $request->query('id') ?? $request->input('id');
        $rawMode = $request->query('mode') ?? $request->input('mode');

        $mode = $this->trash->decrypt($rawMode);
        $id = $rawId ? $this->trash->decrypt($rawId) : null;

        return match ($mode) {
            'update'            => $this->handleUpdate($request, $id),
            'instance'          => VRecords::findValueSingleInstance($id),
            'generatedContent'  => VRecords::getGeneratedContent($id),
            'print'         => app(PdfController::class)->export($id),
            'evaluate'      => app(AIController::class)->evaluateRecord($request, $id),
            'export'        => $this->exportPatients($request),
            'validate'      => $this->previewImport($request),
            'confirm'       => $this->confirmImport($request),
            default => abort(404),
        };
    }


    // ACTIONS & LOGIC

    public function table(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $status = $request->get('status');
        $unit = $request->get('unit');
        $year = $request->get('year');
        $archived = filter_var($request->get('archived', 'false'), FILTER_VALIDATE_BOOLEAN);

        $tableData = VRecords::datatable($request, auth()->user());

        return response()->json([
            'draw'            => $tableData['draw'],
            'recordsFiltered' => $tableData['recordsFiltered'],
            'data'            => $tableData['data'],
        ]);
    }

    private function handleUpdate(Request $request, int $id)
    {
        $validated = $request->validate([
            'cholesterol'     => 'nullable|numeric',
            'hdl_cholesterol' => 'nullable|numeric',
            'systolic_bp'     => 'nullable|numeric',
            'fbs'             => 'nullable|numeric',
            'hba1c'           => 'nullable|numeric',
            'hypertension'    => 'nullable|boolean',
            'diabetes'       => 'nullable|boolean',
            'smoking'         => 'nullable|boolean',

            // also accept legacy key from other controllers / forms
            'total_cholesterol' => 'nullable|numeric',
        ]);

        // Some callers (legacy forms / controllers) use `total_cholesterol`.
        // Normalize it into the `cholesterol` key that RecordService expects.
        if ($request->has('total_cholesterol') && ! $request->has('cholesterol')) {
            $validated['cholesterol'] = $request->input('total_cholesterol');
        }

        $record = $this->recordService->update($id, $validated);

        return response()->json([
            'success' => true,
            'record'  => $record
        ]);
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportPatients(Request $request)
    {
        return Excel::download(
            new PatientsExport($request->unit_code),
            $request->unit_code . '.xlsx'
        );
    }


    // IMPORT FILE
    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx|max:5120',
        ]);

        $file = $request->file('file');

        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();

        $signature = $sheet->getCell('Z1')->getValue();
        $unit_code = $sheet->getCell('Z2')->getValue();

        if ($signature !== 'HEART_HEALTH_INITIATIVE_2026' || !$unit_code) {
            return response()->json([
                'message' => 'Invalid template file. Please use the official system template.'
            ], 422);
        }

        $preview = new PatientsPreviewImport($unit_code);
        Excel::import($preview, $file);

        return response()->json([
            'valid_rows' => $preview->validCount,
            'invalid_rows' => $preview->invalidCount,
            'errors' => $preview->errors,
            'data' => $preview->previewData,
        ]);
    }

    public function confirmImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        Excel::import(new PatientsImport, $request->file('file'));

        return back()->with('success', 'Import completed successfully.');
    }
}
