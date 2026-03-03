<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use softDeletes;
    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'phone_number',
        'birth_date',
        'sex',
        'unit_code',
        'weight',
        'height',
        'history_id',
        'deleted_at',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function records(): HasMany
    {
        return $this->hasMany(Records::class, 'patient_id');
    }

    public function family_history(): BelongsTo
    {
        return $this->belongsTo(Family_history::class, 'history_id');
    }

}
