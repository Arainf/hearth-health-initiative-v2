<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Status extends Model
{
    protected $table = 'status';
    public $timestamps = false;

    protected $fillable = [
        'status_name',
    ];

    public function records(): HasMany
    {
        return $this->hasmany(VRecords::class, 'status_id');
    }

    public function scopeWithRecordCount($query,$unit = null, $status = null, $year = null, $archived = false)
    {
        return $query->withCount([
            'records as count' => function ($q) use ($year, $archived, $status, $unit) {

                $q->where('is_archived', $archived);

                if ($year && $year !== 'all') {
                    $q->whereYear('create', $year);
                }

                if ($status && $status !== 'all') {
                    $q->where('status_name', $status);
                }

                if ($unit && $unit !== 'all') {
                    $q->where('unit_name', $unit);
                }
            }
        ]);
    }


}
