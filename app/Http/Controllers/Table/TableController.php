<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\records;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    public function index()
    {

    }

    public function records(Request $request)
{
    if (!$request->ajax()) return;

    $draw   = (int) $request->get('draw', 1);
    $start  = (int) $request->get('start', 0);
    $length = (int) $request->get('length', 20);

    $base = DB::table('records')
        ->join('patients', 'patients.id', '=', 'records.patient_id')
        ->join('status', 'status.id', '=', 'records.status_id')
        ->leftJoin('generated_reports', 'generated_reports.id', '=', 'records.generated_id')
        ->where('records.is_archived', false)
        ->select(
            'records.*',
            'records.status_id',
            'patients.last_name',
            'patients.first_name',
            'patients.middle_name',
            'patients.unit',
            'patients.age',

            'status.status_name',
            'generated_reports.id as generated_id'
        );

    /* ================= TOTAL ================= */
    $recordsTotal = (clone $base)->count();

    /* ================= SEARCH ================= */
    if ($request->filled('search')) {
        $search = $request->search;
        $base->where(function ($q) use ($search) {
            $q->where('patients.last_name', 'like', "%{$search}%")
              ->orWhere('patients.first_name', 'like', "%{$search}%")
              ->orWhere('patients.unit', 'like', "%{$search}%");
        });
    }

    /* ================= STATUS FILTER ================= */
    if ($request->filled('status') && $request->status !== 'all') {
        $base->where('status.status_name', $request->status);
    }

    /* ================= YEAR FILTER ================= */
    if ($request->filled('year') && $request->year !== 'all') {
        $base->whereYear('records.created_at', $request->year);
    }

    $recordsFiltered = (clone $base)->count();

    /* ================= PAGINATION ================= */
    $rows = $base
        ->orderBy('records.created_at', 'desc')
        ->skip($start)
        ->take($length)
        ->get();

    /* ================= FORMAT ================= */
    $data = [];
    $counter = 0;
    foreach ($rows as $row) {

        $staff = DB::table('users')
            ->where('id', $row->staff_id)
            ->value('name');

        $doctor = DB::table('users')
            ->where('id', $row->approved_by)
            ->value('name');



        /* Status badge */
        $color = match (strtolower($row->status_name)) {
            'approved' => 'badge-approved',
            'pending'  => 'badge-pending',
            default    => 'badge-not-evaluated',
        };

        $doctorName = '';
        if ($doctor) {
            $doctorName = '
            <div class="text-[11px] text-gray-400 mt-1">
                by ' . e($doctor) . '
            </div>
        ';
            }

            $status = '
        <div class="flex flex-col items-center">
            <span class="badge ' . $color . '">
                ' . e($row->status_name) . '
            </span>
            ' . $doctorName . '
        </div>
    ';


        /* Created */
        $created = \Carbon\Carbon::parse($row->created_at)
            ->format('M d, Y');

        $data[] = [
            'id' => $row->id,
            'counter' => $counter,
            // patient
            'patient' => [
                'unit'       => $row->unit,
                'last_name'  => $row->last_name,
                'first_name' => $row->first_name,
                'middle_name'=> $row->middle_name,
                'age'        => $row->age,
            ],

            'staff' => $staff,
            'doctor' => $doctor,

            // metrics (used by dropdown)
            'cholesterol'     => $row->cholesterol,
            'hdl_cholesterol' => $row->hdl_cholesterol,
            'systolic_bp'     => $row->systolic_bp,
            'fbs'             => $row->fbs,
            'hba1c'           => $row->hba1c,

            // risks
            'hypertension' => (bool) $row->hypertension,
            'diabetes'     => (bool) $row->diabetes,
            'smoking'      => (bool) $row->smoking,

            // status
            'status' => [
                'status_name' => $status,
            ],
            'status_id' => $row->status_id,

            'created_at'   =>  $created,
            'generated_id' => $row->generated_id,
        ];

        $counter++;

    }

    return response()->json([
        'draw'            => $draw,
        'recordsTotal'    => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data'            => $data,
    ]);
}

    public function patients(Request $request)
    {
          if ($request->ajax()) {
            $draw = (int) $request->get('draw', 1);
            $start = (int) $request->get('start', 0);
            $length = (int) $request->get('length', 15);

            // Subquery for record counts
            $recordCounts = DB::table('records')
                ->select('patient_id', DB::raw('COUNT(*) as record_count'))
                ->where('is_archived', false)
                ->groupBy('patient_id');

            $base = DB::table('patients')
                ->leftJoinSub($recordCounts, 'record_counts', function($join) {
                    $join->on('patients.id', '=', 'record_counts.patient_id');
                })
                ->select(
                    'patients.*',
                    DB::raw('COALESCE(record_counts.record_count, 0) as record_count')
                );

            // Total before filters
            $recordsTotal = DB::table('patients')->count();

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

            // Custom filters from request
            if ($request->filled('birth_from')) {
                $base->whereDate('patients.birth_date', '>=', $request->get('birth_from'));
            }
            if ($request->filled('birth_to')) {
                $base->whereDate('patients.birth_date', '<=', $request->get('birth_to'));
            }
            if ($request->filled('age_min')) {
                $base->where('patients.age', '>=', (int) $request->get('age_min'));
            }
            if ($request->filled('age_max')) {
                $base->where('patients.age', '<=', (int) $request->get('age_max'));
            }
            $genders = $request->input('genders');
            if (is_array($genders) && count($genders) > 0) {
                $base->whereIn('patients.sex', $genders);
            }

            $recordsFiltered = (clone $base)->count();

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
                        '.$row->age.' y.o.
                    </div>
                </div>';

                $record_count = '
                    <div class="text-center">
                        <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fa-solid fa-file-medical mr-1"></i>
                            ' . (int)$row->record_count . '
                        </span>
                    </div>';

                $actions = '';

                $actions .= '<a href="javascript:void(0)"
                    title="View Details"
                    class="hhi-btn hhi-btn-view icon-only"
                    onclick="event.stopPropagation(); window.viewPatient && window.viewPatient(' . (int)$row->id . ');">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </a>';

                $actions .= '
                <a href="javascript:void(0)"
                    title="Edit"
                    class="hhi-btn hhi-btn-edit icon-only"
                    onclick="event.stopPropagation(); window.editPatient && window.editPatient(' . (int)$row->id . ');">
                    <i class="fa-solid fa-pen"></i>
                </a>';

                $actions .= '<button type="button"
                    title="Delete"
                    class="hhi-btn hhi-btn-delete icon-only"
                    onclick="event.stopPropagation(); window.deletePatient && window.deletePatient(' . (int)$row->id . ');">
                    <i class="fa-solid fa-trash"></i>
                </button>';



                $data[] = [
                    $counter++,
                    $patient_name,
                    $row->unit,
                    $record_count,
                    $row->phone_number,
                    $row->sex,
                    $row->birth_date,
                    $actions,
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        }
    }

    public function patientsNav(Request $request)
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

    public function patientsOwnRecord(Request $request, $id)
{
    if (!$request->ajax()) return;

    $draw   = (int) $request->get('draw', 1);
    $start  = (int) $request->get('start', 0);
    $length = (int) $request->get('length', 10);

    $base = DB::table('records')
        ->where('patient_id', $id);

    // DATE FILTER
    if ($request->date_from) {
        $base->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->date_to) {
        $base->whereDate('created_at', '<=', $request->date_to);
    }

    $recordsTotal = $base->count();

    $rows = $base
        ->orderBy('created_at', 'desc')
        ->skip($start)
        ->take($length)
        ->get();

    $data = [];
    $counter = $start + 1;

    foreach ($rows as $row) {

        $status = DB::table('status')
            ->where('id', $row->status_id)
            ->value('status_name');

        $data[] = [
            $counter++,                // #
            $row->cholesterol,
            $row->hdl_cholesterol,
            $row->systolic_bp,
            $row->fbs,
            $row->hba1c,
            $row->hypertension,        // HTN
            $row->diabetes,            // DM
            $row->smoking,             // Smoking
            $status,
            \Carbon\Carbon::parse($row->created_at)
                ->format('M d, Y '),
            $row->generated_id         // 👈 used by drawer
        ];
    }

    return response()->json([
        'draw' => $draw,
        'recordsTotal' => $recordsTotal,
        'recordsFiltered' => $recordsTotal,
        'data' => $data
    ]);
}

    public function accounts(Request $request)
{
    if (!$request->ajax()) return;

    $draw   = (int) $request->get('draw', 1);
    $start  = (int) $request->get('start', 0);
    $length = (int) $request->get('length', 20);

    $base = User::query();

    /* ===============================
       TOTAL COUNT (NO FILTER)
    =============================== */
    $recordsTotal = $base->count();

    /* ===============================
       SEARCH
    =============================== */
    if ($request->filled('search')) {
        $search = $request->search;

        $base->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%");

        });
    }

    $recordsFiltered = $base->count();

    /* ===============================
       PAGINATION + ORDER
    =============================== */
    $rows = $base
        ->orderBy('created_at', 'desc')
        ->skip($start)
        ->take($length)
        ->get();

    /* ===============================
       FORMAT FOR DATATABLE
    =============================== */
    $data = $rows->map(fn ($u) => [
        'id'         => $u->id,
        'name'       => $u->name,
        'username'   => $u->username ?? $u->email,
        'is_admin'   => (int) $u->is_admin,
        'ai_access'  => (int) $u->ai_access,
        'is_doctor' => (int) $u->is_doctor,
        'created_at' => $u->created_at,
    ]);

    return response()->json([
        'draw'            => $draw,
        'recordsTotal'    => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data'            => $data,
    ]);
}

    public function archiveRecords(Request $request)
{
    if (!$request->ajax()) return;

    $draw   = (int) $request->get('draw', 1);
    $start  = (int) $request->get('start', 0);
    $length = (int) $request->get('length', 20);

    $base = DB::table('records')
        ->leftJoin('patients', 'patients.id', '=', 'records.patient_id')
        ->join('status', 'status.id', '=', 'records.status_id')
        ->leftJoin('generated_reports', 'generated_reports.id', '=', 'records.generated_id')
        ->where('records.is_archived', true)
        ->select(
            'records.*',
            'patients.last_name',
            'patients.first_name',
            'patients.middle_name',
            'patients.unit',
            'patients.age',
            'status.status_name',
            'generated_reports.id as generated_id'
        );

    /* ================= TOTAL ================= */
    $recordsTotal = (clone $base)->count();

    /* ================= SEARCH ================= */
    if ($request->filled('search')) {
        $search = $request->search;
        $base->where(function ($q) use ($search) {
            $q->where('patients.last_name', 'like', "%{$search}%")
              ->orWhere('patients.first_name', 'like', "%{$search}%")
              ->orWhere('patients.unit', 'like', "%{$search}%")
              ->orWhereNull('patients.id'); // Include records with deleted patients in search
        });
    }

    /* ================= STATUS FILTER ================= */
    if ($request->filled('status') && $request->status !== 'all') {
        $base->where('status.status_name', $request->status);
    }

    /* ================= YEAR FILTER ================= */
    if ($request->filled('year') && $request->year !== 'all') {
        $base->whereYear('records.created_at', $request->year);
    }

    $recordsFiltered = (clone $base)->count();

    /* ================= PAGINATION ================= */
    $rows = $base
        ->orderBy('records.created_at', 'desc')
        ->skip($start)
        ->take($length)
        ->get();

    /* ================= FORMAT ================= */
    $data = [];

    foreach ($rows as $row) {
        /* Status badge */
        $color = match (strtolower($row->status_name)) {
            'approved' => 'green',
            'pending'  => 'orange',
            default    => 'gray',
        };

        $status = '
            <span class="px-2 py-1 text-xs rounded-full
                bg-' . $color . '-100 text-' . $color . '-800 capitalize">
                ' . e($row->status_name) . '
            </span>';

        /* Created */
        $created = \Carbon\Carbon::parse($row->created_at)
            ->format('M d, Y');

        $data[] = [
            'id' => $row->id,

            // patient (handle null if patient was deleted)
            'patient' => [
                'unit'       => $row->unit ?? 'N/A',
                'last_name'  => $row->last_name ?? 'Deleted',
                'first_name' => $row->first_name ?? 'Patient',
                'middle_name'=> $row->middle_name ?? '',
                'age'        => $row->age ?? 'N/A',
            ],

            // metrics (used by dropdown)
            'cholesterol'     => $row->cholesterol,
            'hdl_cholesterol' => $row->hdl_cholesterol,
            'systolic_bp'     => $row->systolic_bp,
            'fbs'             => $row->fbs,
            'hba1c'           => $row->hba1c,

            // risks
            'hypertension' => (bool) $row->hypertension,
            'diabetes'     => (bool) $row->diabetes,
            'smoking'      => (bool) $row->smoking,

            // status
            'status' => [
                'status_name' => $status,
            ],

            'created_at'   =>  $created,
            'generated_id' => $row->generated_id,
        ];
    }

    return response()->json([
        'draw'            => $draw,
        'recordsTotal'    => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data'            => $data,
    ]);
}

}


