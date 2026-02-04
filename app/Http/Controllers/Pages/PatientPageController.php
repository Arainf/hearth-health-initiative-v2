<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dump\trashController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientPageController extends Controller
{
    public function index()
    {

        $user = auth()->user();

        if(!$user) return redirect('/unauthorized');

        return view('pages.patient',[
            'table' =>  trashController::encrypt('patient')
        ]);
    }

    public function table(Request $request)
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
}
