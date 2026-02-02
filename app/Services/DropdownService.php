<?php

namespace App\Services;

use App\Models\Records;
use App\Models\Status;
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
                    $query->whereYear('created_at', $year);
                }
            }])
            ->get(['id', 'status_name']);
    }

}
