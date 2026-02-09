<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dump\trashController;
use App\Services\DropdownService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardPageController extends Controller
{
    public function index()
    {

        $currentYear = now()->year;
        $years = DropdownService::years();
        $status = DropdownService::status($currentYear, false);




        return view('pages.dashboard',
        [
            'years' => $years,
            'currentYear' => $currentYear,
            'status' => $status,
            'table' => trashController::encrypt('dashboard')
        ]);
    }

    public function table(Request $request)
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
                'records.created_at as create',
                'patients.*',
                'records.id as record_id',

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

            $doctor = $row->approved_by;


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
            $created = \Carbon\Carbon::parse($row->create,)
                ->format('F d, Y');

            $data[] = [
                'id' => $row->record_id,
                'counter' => $counter,
                // patient
                'patient' => [
                    'unit'       => $row->unit,
                    'last_name'  => $row->last_name,
                    'first_name' => $row->first_name,
                    'middle_name'=> $row->middle_name,
                    'suffix'    =>$row->suffix,
                    'birthday'     => $row->birth_date,
                    'age'        => $row->age,
                    'height'    => $row->height,
                    'weight'    => $row->weight,
                    'bmi'       => $row->bmi,
                    'contact'   => $row->phone_number
                ],

                'staff' => $row->staff_id,
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

                'created_at'   => $created,
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
}
