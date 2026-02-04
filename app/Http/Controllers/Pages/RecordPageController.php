<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dump\trashController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecordPageController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user) return redirect('unauthorized');

        return view('pages.record', [
            'table' => trashController::encrypt('record'),
        ]);

    }



    public function table(Request $request)
    {
        if ($request->ajax()) {
            $draw = (int) $request->get('draw', 1);
            $start = (int) $request->get('start', 0);
            $length = (int) $request->get('length', 1000);


            $base = DB::table('patients')
                ->leftJoin('family_histories', 'patients.history_id', '=', 'family_histories.id')
                ->select(
                    'patients.id as patient_id',
                    'patients.*',
                    'family_histories.*',
                );

            // Global search
            $search = $request->input('search.value');
            if ($search) {
                $base->where(function ($q) use ($search) {
                    $q->where('patients.last_name', 'like', "%{$search}%")
                        ->orWhere('patients.first_name', 'like', "%{$search}%")
                        ->orWhere('patients.unit', 'like', "%{$search}%")
                        ->orWhere('patients.phone_number', 'like', "%{$search}%");
                });
            }



            // Ordering and paging
            $rows = $base->orderBy('patients.last_name', 'asc')
                ->skip($start)
                ->take($length)
                ->get();

            $data = [];
            $counter = $start + 1;
            foreach ($rows as $row) {
                $patient_name = '
                <div class="leading-tight">
                    <div class="font-medium text-gray-900 md:text-sm">
                        '.$row->last_name.', '.$row->first_name.' '.$row->middle_name.' '.$row->suffix.'
                    </div>
                    <div class="text-md text-gray-500 md:text-sm">
                        '.$row->age.' y.o. ( '. $row->unit . ' )
                    </div>
                </div>';

                $hiddenData = [
                    'id' => $row->patient_id,
                    'first_name' => $row->first_name,
                    'last_name' => $row->last_name,
                    'middle_name' => $row->middle_name,
                    'suffix' => $row->suffix,
                    'age' => $row->age,
                    'phone_number' => $row->phone_number,
                    'sex' => $row->sex,
                    'birth_date' => $row->birth_date,
                    'unit' => $row->unit,
                    'weight' => $row->weight,
                    'height' => $row->height,
                    'bmi' => $row->bmi,
                    'family_history' => [
                        'Hypertension' => $row->Hypertension,
                        'Diabetes' => $row->Diabetes,
                        'Heart_Attack' => $row->Heart_Attack,
                        'Cholesterol' => $row->Cholesterol,
                    ],
                ];

                $data[] = [
                    $patient_name,
                    $hiddenData,
                ];
            }

            return response()->json([
                'draw' => $draw,
                'data' => $data,
            ]);
        }
    }
}
