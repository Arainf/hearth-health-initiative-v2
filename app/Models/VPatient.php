<?php

namespace App\Models;

use App\Http\Controllers\Dump\trashController;
use App\Services\EncryptionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class VPatient extends Model
{
    protected $table = 'v_patients';
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    /**
     * Make the model read-only
     */
    protected static function booted()
    {
        static::creating(fn () => false);
        static::updating(fn () => false);
        static::deleting(fn () => false);
    }


    // FORMATTERS
    public function getFullNameAttribute()
    {
        return trim("{$this->last_name}, {$this->first_name} {$this->middle_name} {$this->suffix}");
    }


    public function showViewData()
    {
        return [
            'id'                        => $this->id,
            'full_name'                 => $this->getFullNameAttribute(),
            'first_name'                => $this->first_name,
            'middle_name'               => $this->middle_name,
            'last_name'                 => $this->last_name,
            'suffix'                    => $this->suffix,
            'birthday'                  => $this->birthday,
            'sex'                       => $this->sex,
            'age'                       => $this->age,
            'weight'                    => $this->weight,
            'height'                    => $this->height,
            'bmi'                       => $this->bmi,
            'phone_number'              => $this->phone_number,
            'unit'                      => $this->unit_code,
            'hypertension'              => $this->hypertension,
            'diabetes_mellitus'         => $this->diabetes_mellitus,
            'heart_attack_under_60y'    => $this->heart_attack_under_60y,
            'cholesterol'               => $this->cholesterol,
        ];
    }

    public function minimalData()
    {
        return [
            'id'                        => $this->id,
            'full_name'                 => $this->full_name,
            'first_name'                => $this->first_name,
            'middle_name'               => $this->middle_name,
            'last_name'                 => $this->last_name,
            'suffix'                    => $this->suffix,
            'unit'                      => $this->unit,
        ];
    }


    public function scopeUnit($query, $unitCode)
    {
        if (!$unitCode) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('unit', $unitCode);
    }

    public static function datatable($request)
    {
        $draw   = (int) $request->get('draw', 1);
        $unit = $request->input('unit');
        $search = $request->input('search_name') ?? $request->input('search.value');

        // 1. "Either/Or" Logic: Only exit if BOTH are empty.
        if (empty($unit) && empty($search)) {
            return [
                'draw'            => $draw,
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => []
            ];
        }
        $start  = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 15);

        $query = static::query()->whereNull('deleted_at');

        if ($unit) {
            $query->where('unit_code', $unit);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('last_name', 'like', "{$search}%")
                    ->orWhere('first_name', 'like', "{$search}%")
                    ->orWhere('unit', 'like', "{$search}%")
                    ->orWhere('phone_number', 'like', "{$search}%");
            });
        }
        $total = $query->count();

        $rows = $query
            ->orderBy('last_name', 'asc')
            ->skip($start)
            ->take($length)
            ->get();

        $trash = new trashController;
        $counter = $start + 1;

        $data = $rows->map(function ($row) use ($trash, &$counter) {

            $patientName = '
                <div class="leading-tight">
                    <div class="font-medium text-gray-900 md:text-sm">
                        '.$row->full_name.'
                    </div>
                    <div class="text-md text-gray-500 md:text-sm">
                        '.$row->age.' y.o.
                    </div>
                </div>';

                    $recordCounts = '
                <div class="text-center">
                    <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                       <i data-lucide="paperclip" class="w-3 h-3 mr-1"></i>

                        '.(int)$row->records_count.'
                    </span>
                </div>';

                    $actions = '
                <a href="'.route('page', [
                            'token' => request()->route('token'),
                            'id'    => $trash->encrypt($row->id),
                            'mode'  => $trash->encrypt('show')
                        ]).'"
                    class="hhi-btn hhi-btn-view icon-only">
                          <i data-lucide="search" class="w-4 h-4"></i>

                </a>

                <a href="'.route('page', [
                            'token' => request()->route('token'),
                            'id'    => $trash->encrypt($row->id),
                            'mode'  => $trash->encrypt('edit')
                        ]).'"
                    class="hhi-btn hhi-btn-edit icon-only">
                     <i data-lucide="pen" class="w-4 h-4"></i>

                </a>

                <button type="button"
                    class="hhi-btn hhi-btn-delete icon-only"
                    data-id='. $trash->encrypt($row->id) .'
                    data-more='. $trash->encrypt('delete') .'
                   >
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            ';

            return [
                $counter++,
                $patientName,
                $row->unit,
                $recordCounts,
                $row->phone_number,
                $row->sex,
                $row->birthday,
                $actions,
            ];
        });

        return [
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $data->values(),
        ];
    }

    public static function datatableSmall($request)
    {
        $draw   = (int) $request->get('draw', 1);

        if(!$request->filled('unit_code')) {
            return [
                'draw'  => $draw,
                'data'  => []
            ];
        }
        $start  = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 15);

        $query = static::query()
            ->whereNull('deleted_at')
            ->where('unit', $request->unit_code);

        /*
        |--------------------------------------------------------------------------
        | GLOBAL SEARCH
        |--------------------------------------------------------------------------
        */
        $search = $request->input('search.value');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('last_name', 'like', "{$search}%")
                    ->orWhere('first_name', 'like', "{$search}%")
                    ->orWhere('unit', 'like', "{$search}%")
                    ->orWhere('phone_number', 'like', "{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATED RESULTS
        |--------------------------------------------------------------------------
        */
        $rows = $query
            ->orderBy('last_name', 'asc')
            ->skip($start)
            ->take($length)
            ->get();

        $trash = new EncryptionService();

        $data = $rows->map(function ($row) use ($trash ) {

            $patientName = '
                <div class="leading-tight">
                    <div class="font-medium text-gray-900 md:text-sm">
                        '.$row->full_name.'
                    </div>
                    <div class="text-md text-gray-500 md:text-sm">
                        '.$row->age.' y.o.
                    </div>
                </div>';


            return [
                $patientName,
                [
                    'id' => $trash->encrypt($row->id),
                ],
                $row->unit,
            ];
        });

        return [
            'draw'            => $draw,
            'data'            => $data->values(),
        ];
    }

    public static function datatableArchive($request, $user)
    {
        $trash = new trashController;

        $draw     = (int) $request->get('draw', 1);
        $start    = (int) $request->get('start', 0);
        $length   = (int) $request->get('length', 20);

        // 1. Correct the Input Keys to match your JS payload
        $search = $request->input('search_name'); // Changed from 'search'
        $unitFilter = $request->input('unit');
        $yearFilter = $request->input('year');

        // 2. Base Query with Join
        $query = static::query()
            ->whereNotNull('deleted_at');

        /* ===============================
        SEARCH & FILTERS
     =============================== */
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('last_name', 'like', "{$search}%")
                    ->orWhere('first_name', 'like', "{$search}%")
                    ->orWhere('unit', 'like', "{$search}%");
            });
        }

        if (!empty($yearFilter) && $yearFilter !== 'all') {
            // Now whereYear will work because $query is still a Query Builder
            $query->whereYear('deleted_at', $yearFilter);
        }

        if (!empty($unitFilter) && $unitFilter !== 'all') {
            $query->where('unit_code', $unitFilter);
        }

        $recordsTotal = static::whereNotNull('deleted_at')->count();
        $recordsFiltered = $query->count();

        /* ===============================
           PAGINATION & DATA FETCHING
        =============================== */
        $rows = $query
            ->orderByDesc('deleted_at') // Ensure prefix here
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function ($row) use ($trash) {
            $encPatientId = $trash->encrypt($row->id);
            $modeRestore = $trash->encrypt('restore');
            $modeCount = $trash->encrypt('show');

            $patientHtml = '
            <div class="leading-tight">
                <div class="font-semibold font-inter text-md text-gray-900">
                    '.e($row->last_name).', '.e($row->first_name).'
                </div>
                <div class="text-xs text-gray-500">
                    <strong>'.e($row->age).'</strong> y.o.
                </div>
            </div>';

            $viewUrl = route('page', [
                'token' => $trash->encrypt('archive'), // The current page token
                'id'    => $encPatientId,   // Encrypted Patient ID
                'mode'  => $modeCount       // Encrypted 'show' mode
            ]);

            $actions = '
            <div class="flex items-center justify-end gap-2 shrink-0">
                <a href="'.$viewUrl.'"
                   class="action-btn hhi-btn hhi-btn-secondary icon-only flex items-center justify-center"
                   title="View Records">
                    <i data-lucide="layers" class="w-4 h-4 text-purple-600"></i>
                </a>

                <button type="button"
                    data-patient="'.$encPatientId.'"
                    data-mode="'.$modeRestore.'"
                    class="action-btn hhi-btn hhi-btn-secondary icon-only restore-record"
                    title="Restore">
                    <i data-lucide="rotate-ccw" class="w-4 h-4 text-blue-600"></i>
                </button>
            </div>';

            return [
                'id'         => $encPatientId,
                'patient'    => $patientHtml,
                'unit'       => $row->unit,
                'staff'      => $row->id,
                'deleted_at' => $row->deleted_at ? \Carbon\Carbon::parse($row->deleted_at)->format('F d, Y') : 'N/A',
                'actions'    => $actions,
            ];
        });

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data->values(),
        ];
    }
    public static function findForView($id)
    {
        $patient = static::findOrFail($id);
        return $patient->showViewData();
    }

    public static function exportPatientsWithUnit($unit_code): Collection
    {
        return static::where('unit', $unit_code)
            ->orderBy('last_name')
            ->select([
                'last_name',
                'first_name',
                'middle_name',
                'middle_initial',
                'suffix',
                'birthday',
                'sex',
                'weight',
                'height',
                'phone_number',

                // Added medical columns
                'cholesterol',
                'hdl_cholesterol',
                'systolic_bp',
                'fbs',
                'hba1c',
                'hypertension',
                'diabetes',
                'smoking',
            ])
            ->get();
    }

    public function toDatatableRow()
    {
        $patientName = '
        <div class="leading-tight">
            <div class="font-medium text-gray-900 md:text-sm">
                '.$this->full_name.'
            </div>
            <div class="text-md text-gray-500 md:text-sm">
                ( '.$this->unit.' )
            </div>
        </div>';

        return [
            $patientName,
            $this->minimalData()
        ];
    }







}
