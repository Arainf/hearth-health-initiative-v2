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

class GeneratedPageController extends Controller
{

    public object $trash;
    public string $token;
    public string $module;

    public function __construct(RecordService $recordService){
        $this->module               = 'generated';
        $this->trash                = new trashController;
        $this->token                = $this->trash->encrypt($this->module);
    }


    public function index(Request $request, $token)
    {
        $MODULE_NAME = [
            'icon' => 'stethoscope',
            'label' => 'generated'
        ];

        $user = auth()->user();

       if(!$user->is_Doctor()) return redirect('unauthorized');

        if($request->query('id') || $request->query('mode') || $request->input('id') || $request->input('mode'))
            return $this->menu($request);

       return view('pages.doctor',
           [
               'MODULE_NAME' => $MODULE_NAME,
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
            'show' => view('generated.show', [
                'record' => VRecords::getGeneratedContent($id)
            ]),
            'edit' => (function() use ($id, $rawId) {
                $record = VRecords::getGeneratedContent($id);

                return view('generated.edit', [
                    'record' => $record,
                    'TOKEN'  => $this->token,
                    'SECRET' => $rawId,
                    'MODE_SAVE' => $this->trash->encrypt('store'),
                    'MODE_APPROVE' => $this->trash->encrypt('approve'),
                ]);
            })(),

            'store', 'approve' => response()->json([
                'success' => true,
                'data' => Generated_reports::updateGeneratedRecord(
                    Records::findOrFail($id),
                    $request->input('content'),
                    $mode === 'approve'
                )
            ]),
            default => abort(404),
        };
    }

}
