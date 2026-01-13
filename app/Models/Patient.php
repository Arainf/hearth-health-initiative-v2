<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'phone_number',
        'birth_date',
        'age',
        'sex',
        'unit',
        'weight',
        'height',
        'bmi',
        'history_id',
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
