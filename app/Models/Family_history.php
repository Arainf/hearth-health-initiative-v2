<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Family_history extends Model
{
    protected $fillable = [
        'Hypertension',
        'Diabetes',
        'Heart_Attack',
        'Cholesterol',
    ];

    protected function casts(): array
    {
        return [
            'Hypertension' => 'boolean',
            'Diabetes' => 'boolean',
            'Heart_Attack' => 'boolean',
            'Cholesterol' => 'boolean',
        ];
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class, 'history_id');
    }
}
