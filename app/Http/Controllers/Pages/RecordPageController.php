<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dump\trashController;
use App\Models\VPatient;
use App\Services\DropdownService;
use App\Services\RecordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class RecordPageController extends Controller
{

    public object $trash;
    public string $token;
    public string $module;

    public function __construct(){
        $this->module = 'record';
        $this->trash = new trashController;
        $this->token = $this->trash->encrypt($this->module);
    }

    public function index(Request $request,$token)
    {
        $MODULE_NAME = '<i class="fa-solid fa-user-group  mr-2"></i> Patients';

        $user = auth()->user();
        if(!$user) return redirect('/unauthorized');

        $units = DropdownService::units();

        if($request->query('mode'))
            return $this->menu($request);

        return view('pages.record', [
            'MODULE_NAME' => $MODULE_NAME,
            'TABLE' => $token,
            'UNITS' => $units
        ]);

    }

    public function menu(Request $request)
    {

        if($request->query('mode')){
            $decrypt_MODE = Crypt::decryptString($request->query('mode'));
            switch($decrypt_MODE){
                case 'store':
                    $validated = Validator::make($request->all(), [
                        'staff_id' => 'required|string',
                        'last_name' => 'required|string',
                        'first_name' => 'required|string',
                        'middle_name' => 'nullable|string',
                        'suffix' => 'nullable|string',
                        'birth_date' => 'required|nullable|date',
                        'sex' => 'required|nullable|string',
                        'weight' => 'required|numeric|min:0|max:700',
                        'height' => 'required|numeric|min:0|max:300',
                        'unit_code' => 'required|nullable|string',
                        'contact' => 'nullable|string',

                        'total_cholesterol' => 'nullable|numeric',
                        'hdl_cholesterol' => 'nullable|numeric',
                        'systolic_bp' => 'nullable|numeric',
                        'fbs' => 'nullable|numeric',
                        'hba1c' => 'nullable|numeric',

                        'hypertension_tx' => 'nullable',
                        'diabetes_m' => 'nullable',
                        'smoker' => 'nullable',

                        // Hyphenated family fields allowed; keep validation as string/nullable
                        'family_hypertension' => 'nullable',
                        'family_diabetes-mellitus' => 'nullable',
                        'family_heart-attack-under-60y' => 'nullable',
                        'family_cholesterol' => 'nullable',

                        'patient_id' => 'nullable|numeric'
                    ])->validate();

                    $record = app(RecordService::class)->store($validated);

                    return response()->json(['success' => true]);
                    break;

            }

        }
    }


    public function table(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        if($request->query('id')){
            return VPatient::findForView($this->trash->decrypt($request->query('id')));
        }

        return response()->json(
            VPatient::datatableSmall($request)
        );
    }

}
