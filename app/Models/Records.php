<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Patient;
use App\Models\Status;

class Records extends Model
{
    protected $fillable = [
        'patient_id',
        'cholesterol',
        'hdl_cholesterol',
        'systolic_bp',
        'fbs',
        'hba1c',
        'hypertension',
        'diabetes',
        'smoking',
        'status_id',
        'generated_id',
        'is_archived',
        'staff_id',
        'approved_by',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function generated_report(): BelongsTo
    {
        return $this->belongsTo(Generated_reports::class, 'generated_id');
    }

    protected function casts(): array
    {
        return [
            'hypertension' => 'boolean',
            'diabetes' => 'boolean',
            'smoking' => 'boolean',
        ];
    }

    public function hasGeneratedReport(): bool
    {
        return $this->generated_report()->exists();
    }

    public function getAIInputData(): array
    {
        // Combine record + patient data
        $patient = $this->patient;
        $recordData = $this->only([
            'id',
            'cholesterol',
            'hdl_cholesterol',
            'systolic_bp',
            'fbs',
            'hba1c',
            'hypertension',
            'diabetes',
            'smoking'
        ]);

        $patientData = $patient ? $patient->only([
            'id',
            'first_name',
            'last_name',
            'middle_name',
            'suffix',
            'phone_number',
            'birth_date',
            'age',
            'sex',
            'unit',
            'weight',
            'height',
            'bmi'
        ]) : [];

        return [
            'record' => $recordData,
            'patient' => $patientData,
            'status' => $this->status?->status_name,
        ];
    }
}
