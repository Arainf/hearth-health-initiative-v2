<?php

namespace App\Services;

use App\Models\Records;
use App\Models\Status;
use App\Models\VOffice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;


class DropdownService
{
    public static function years()
    {
        // Test Case Friendly
        $yearFunction = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y', created_at)"
            : 'YEAR(created_at)';

        return Records::where('is_archived', false)
            ->selectRaw("$yearFunction as year")
            ->distinct()
            ->orderBy('year', 'asc')
            ->pluck('year')->toArray();
    }


    public static function status($year, bool $archived = false)
    {
        return Status::query()
            ->withCount(['records as count' => function ($query) use ($year, $archived) {
                $query->where('is_archived', $archived);

                if ($year && $year !== 'all') {
                    $query->whereYear('create', $year);
                }
            }])
            ->get(['id', 'status_name']);
    }

    public static function offices()
    {
        return VOffice::query()
            ->select('unit_group_code', 'unit_group_name')
            ->whereNotNull('unit_group_code')
            ->whereNotNull('unit_group_name')
            ->distinct()
            ->orderBy('unit_group_name', 'asc')
            ->get();
    }

    public static function units(): Collection
    {
        return VOffice::query()
            ->get(['unit_code', 'unit_name']);
    }

}
