<?php

namespace App\Models;

use App\Http\Controllers\Dump\trashController;
use App\Services\EncryptionService;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class VRecords extends Model
{
    protected $table = 'v_records';
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];
    protected $primaryKey = 'record_id';

    protected static function booted()
    {
        static::creating(fn () => false);
        static::updating(fn () => false);
        static::deleting(fn () => false);
    }

    /* =====================================
       DATATABLE
    ===================================== */
    public static function datatable($request, $user)
    {
        $enc = app(EncryptionService::class);

        $draw     = (int) $request->get('draw', 1);
        $start    = (int) $request->get('start', 0);
        $length   = (int) $request->get('length', 20);
        $aiAccess = (bool) $user->ai_access;
        $aiReady  = (bool) $user->ai_ready;

        $query = static::query()->where('is_archived', 0);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('last_name', 'like', "{$search}%")
                  ->orWhere('first_name', 'like', "{$search}%")
                  ->orWhere('unit_name', 'like', "{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status_name', $request->status);
        }

        if ($request->filled('year') && $request->year !== 'all') {
            $query->whereYear('create', $request->year);
        }

        if ($request->filled('unit') && $request->unit !== 'all') {
            $query->where('unit_name', $request->unit);
        }

        $recordsTotal    = static::where('is_archived', 0)->count();
        $recordsFiltered = $query->count();

        $rows = $query
            ->orderByDesc('create')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function ($row) use ($aiAccess, $aiReady, $enc) {

            $detailsUrl = route('page', [
                'token' => $enc->encrypt('doctor'),
                'id'    => $enc->encrypt($row->record_id),
                'mode'  => $enc->encrypt('instance')
            ]);

            $printUrl = route('page', [
                'token' => $enc->encrypt('doctor'),
                'id'    => $enc->encrypt($row->record_id),
                'mode'  => $enc->encrypt('print')
            ]);



            $color = match (strtolower($row->status_name ?? '')) {
                'approved' => 'badge-approved',
                'pending'  => 'badge-pending',
                default    => 'badge-not-evaluated',
            };

            $doctorNote = $row->approved_by
                ? '<div class="text-[11px] text-gray-400 mt-1">by '.e($row->approved_by).'</div>'
                : '';

            $statusHtml = '
                <div class="flex flex-col items-center">
                    <span class="badge '.$color.'">'.e($row->status_name).'</span>
                    '.$doctorNote.'
                </div>
            ';

            $patientHtml = '
                <div class="leading-tight relative">
                    <div class="font-semibold font-inter text-md">
                        '.e($row->last_name).', '.e($row->first_name).' '.e($row->middle_name).'
                    </div>
                    <div class="absolute text-xs text-gray-500 right-0 top-3">
                        <strong>'.e($row->age).'</strong> y.o.
                    </div>
                </div>
            ';

            $hasGenerated      = !empty($row->generated_id);
            $hasDoctorApproval = !empty($row->approved_by);
            $viewBtn = '';

            if($hasGenerated) {
                $viewUrl = route('page', [
                    'token' => $enc->encrypt('generated'),
                    'id'    => $enc->encrypt($row->generated_id),
                    'mode'  => auth()->user()->is_Doctor() ? $enc->encrypt('edit') : $enc->encrypt('show')
                ]);


                $viewBtn = '<a class="hhi-btn hhi-btn-view icon-only view-generated-btn"
                    title="View Evaluation"
                    href="'.$viewUrl.'">
                    <i data-lucide="search" class="w-4 h-4"></i>
                   </a>';
            }


            $evaluateBtn = (!$hasGenerated && $aiAccess && $aiReady)
                ? '<button class="hhi-btn hhi-btn-evaluate icon-only evaluate-btn relative"
                    title="Evaluate with AI"
                    data-record-id="'.$enc->encrypt($row->record_id).'"
                    data-record-mode="'.$enc->encrypt('evaluate').'">
                    <i data-lucide="brain" class="w-4 h-4"></i>
                   </button>'
                : '';

            $printBtn = ($hasGenerated && $hasDoctorApproval)
                ? '<a href="'. $printUrl .'"
                     target="_blank"
                     class="hhi-btn hhi-btn-print icon-only"
                     title="Print">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                   </a>'
                : '';

            $actionsHtml = '
                <div class="flex flex-col items-center gap-1">
                    <div class="actions-btn">
                        '.$viewBtn.'
                        '.$evaluateBtn.'
                        '.$printBtn.'
                        <button
                            class="hhi-btn hhi-btn-secondary icon-only row-toggle"
                            data-url="'.$detailsUrl.'">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            ';

            return [
                'id'         => $enc->encrypt($row->record_id),
                'patient'    => $patientHtml,
                'unit'       => $row->unit_name,
                'staff'      => $row->staff_id,
                'created_at' => Carbon::parse($row->create)->format('F d, Y'),
                'status'     => $statusHtml,
                'actions'    => $actionsHtml,
            ];
        });

        return [
            'draw'            => $draw,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data->values(),
        ];
    }


    public static function findValueSingleInstance($id)
    {
        $enc = app(EncryptionService::class);

        $row = static::findOrFail($id);

        return [
            'id'              => $enc->encrypt($row->record_id),
            'mode'            => $enc->encrypt('update'),
            'generated_id'    => $row->generated_id,
            'cholesterol'     => $row->total_cholesterol,
            'systolic_bp'     => $row->systolic_bp,
            'fbs'             => $row->fbs,
            'hba1c'           => $row->hba1c,
            'hdl_cholesterol' => $row->hdl_cholesterol,
            'hypertension'    => $row->hypertension_tx,
            'diabetes'        => $row->diabetes_m,
            'smoking'         => $row->current_smoker,
        ];
    }

    public static function findContentForPdf($id)
    {
        $enc = app(EncryptionService::class);

        $row = static::findOrFail($id);

        return [
            'generated_report' => [
                  'generated_text' => $row->generated_text,
                  'created_at'     => $row->create,
            ],

            'patient' => [
                'first_name'    => $row->first_name,
                'middle_name'   => $row->middle_name,
                'last_name'     => $row->last_name,
                'unit'          => $row->unit_name,
                'dob'            => $row->birth_date,
                'age'            => $row->age,
                'weight'         => $row->weight,
                'height'         => $row->height,
                'bmi'            => $row->bmi,
                'contact'        => $row->phone_number,
            ],

            'medical' => [
                'cholesterol'       => $row->total_cholesterol,
                'hdl_cholesterol'   => $row->hdl_cholesterol,
                'systolic_bp'       => $row->systolic_bp,
                'fbs'               => $row->fbs,
                'hba1c'             => $row->hba1c,
            ]
        ];
    }

    public static function patientsOwnRecord($request)
    {
        $trash = new trashController;
        // We need the raw encryption instance to match your route generation requirement
        $enc = app(\App\Services\EncryptionService::class);

        $draw   = (int) $request->get('draw', 1);
        $start  = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);
        $id = $trash->decrypt($request->id);

        $query = static::where('patient_id', $id);
        $recordsTotal = $query->count();
        $rows = $query->orderBy('create', 'desc')->skip($start)->take($length)->get();

        $data = $rows->map(function ($row, $index) use ($start, $enc) {
            // Status & Badge
            $statusName = strtolower($row->status_name ?? 'pending');
            $color = match ($statusName) { 'approved' => 'badge-approved', 'pending' => 'badge-pending', default => 'badge-not-evaluated' };
            $doctor = $row->approved_by ? '<div class="text-[11px] text-gray-400 mt-1">by ' . e($row->approved_by) . '</div>' : '';

            // Risk Group Layout
            $risks = [ ['L' => 'Hypertension', 'V' => $row->hypertension], ['L' => 'Diabetes Mellitus', 'V' => $row->diabetes], ['L' => 'Smoking', 'V' => $row->smoking] ];
            $riskHtml = '<div class="risk-group flex justify-center gap-4">';
            foreach ($risks as $r) {
                $riskHtml .= '<div class="flex items-center gap-2" title="'.$r['L'].'"><span class="risk-box '.($r['V'] ? 'yes' : 'no').'">'.($r['V'] ? '✓' : '✕').'</span><span class="text-xs font-medium text-gray-700 whitespace-nowrap">'.$r['L'].'</span></div>';
            }
            $riskHtml .= '</div>';

            // ✅ NEW Action Button Logic
            if ($row->generated_id) {
                $viewUrl = route('page', [
                    'token' => $enc->encrypt('generated'),
                    'id'    => $enc->encrypt($row->generated_id),
                    'mode'  => $enc->encrypt('show')
                ]);

                $actionBtn = '
                <a class="hhi-btn hhi-btn-view  flex items-center justify-center view-generated-btn"
                   title="View Evaluation"
                   href="'.$viewUrl.'">
                    <i data-lucide="search" class="w-4 h-4"></i> View Record
                </a>';
            } else {
                $actionBtn = '<button class="hhi-btn hhi-btn-secondary text-sm" disabled>Not Available</button>';
            }

            return [
                $start + $index + 1,
                $row->total_cholesterol,
                $row->hdl_cholesterol,
                $row->systolic_bp,
                $row->fbs,
                $row->hba1c,
                $riskHtml,
                '<div class="flex flex-col items-center"><span class="badge '.$color.'">'.ucfirst($statusName).'</span>'.$doctor.'</div>',
                \Carbon\Carbon::parse($row->create)->format('F d, Y'),
                $actionBtn
            ];
        });

        return [ 'draw' => $draw, 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $recordsTotal, 'data' => $data->values() ];
    }
    public static function findFullRecord($id)
    {
        return static::query()
            ->where('patient_id', $id)
            ->firstOrFail();
    }


    // Change return type to object or array
    public static function getGeneratedContent($generatedId): ?object
    {
        $row = static::where('generated_id', $generatedId)->first();
        if (!$row) {
            return null;
        }
        $enc = app(EncryptionService::class);

        // Return as an object for easy $record->property access in Blade
        return (object)[
            'mode_save' => $enc->encrypt('store'),
            'mode_save_and_approve' => $enc->encrypt('approve'),
            'generated_id'   => $row->generated_id,
            'generated_text' => $row->generated_text,

            'record_id'   => $enc->encrypt($row->record_id),
            'id'          => $row->record_id,
            'status_id'   => $row->status_id,
            'status_name' => $row->status_name,
            'created'     => $row->create ? Carbon::parse($row->create)->format('F j, Y') : null,

            'cholesterol'     => $row->total_cholesterol,
            'hdl_cholesterol' => $row->hdl_cholesterol,
            'systolic_bp'     => $row->systolic_bp,
            'fbs'             => $row->fbs,
            'hba1c'           => $row->hba1c,
            'hypertension'    => $row->hypertension_tx,
            'diabetes'        => $row->diabetes_m,
            'smoking'         => $row->current_smoker,

            'patient' => (object)[
                'last_name'  => $row->last_name,
                'first_name' => $row->first_name,
                'middle_name'=> $row->middle_name,
                'suffix'     => $row->suffix,
                'age'        => $row->age,
                'sex'        => $row->sex,
                'unit'       => $row->unit_name,
                'bmi'        => $row->bmi,
                'birthday'   => $row->birth_date ? Carbon::parse($row->birth_date)->format('F j, Y') : null,
                'weight'     => $row->weight,
                'height'     => $row->height,
                'contact'    => $row->phone_number,
            ],
        ];
    }
}
