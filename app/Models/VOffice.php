<?php

namespace App\Models;

use App\Services\EncryptionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VOffice extends Model
{
    protected $table = 'v_offices';
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];
    protected $primaryKey = 'unit_code';

    /**
     * Make the model read-only
     */
    protected static function booted()
    {
        static::creating(fn () => false);
        static::updating(fn () => false);
        static::deleting(fn () => false);
    }

    /**
     * DataTables Logic for Offices
     */
    public static function datatable(Request $request)
    {
        $enc = new EncryptionService();
        $draw   = (int) $request->get('draw', 1);
        $start  = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 20);

        $query = self::query();
        $recordsTotal = $query->count();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('unit_name', 'like', "%{$search}%")
                    ->orWhere('unit_abbr', 'like', "%{$search}%")
                    ->orWhere('unit_group_code', 'like', "%{$search}%")
                    ->orWhere('unit_group_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('unit_group') && $request->unit_group !== 'all') {
            $query->where('unit_group_name', $request->unit_group);
        }

        $recordsFiltered = $query->count();

        $rows = $query->orderBy('unit_name', 'asc')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function($row) use ($enc) {
            $id = $enc->encrypt($row->unit_code);
            $mode = $enc->encrypt('edit');
            $modeDelete = $enc->encrypt('delete');
            return [
                'unit_name'        => $row->unit_name,
                'unit_abbr'        => $row->unit_abbr,
                'unit_group_name'  => $row->unit_group_name,
                'actions'          => '
        <div class="flex gap-2 justify-center">
            <button class="hhi-btn hhi-btn-edit text-xs flex items-center gap-1 edit-office"
                    data-id="'. $id .'" data-mode="'. $mode .'" data-group-code="'.$row->unit_group_code.'">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                <span>Edit</span>
            </button>
            <button class="hhi-btn hhi-btn-delete text-xs flex items-center gap-1 delete-office"
                   data-id="'. $id .'" data-mode="'. $modeDelete .'">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                <span>Delete</span>
            </button>
        </div>'
            ];
        });

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];
    }
}
