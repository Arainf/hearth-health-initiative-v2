<span class="xte mb-2">
    {{-- ANNUAL MEDICAL REPORT / DATE --}}
    <div class="report-header">
        <p><strong>ANNUAL MEDICAL REPORT</strong></p>
        <p>Date: <span class="value">{{ $record->created }}</span></p>
    </div>

    <p class="section-title">A. Personal Information</p>

    <div class="grid grid-cols-6 gap-0 medical-table">
        <div class="col-span-2">
            <span class="label">Name:</span>
            <span class="value">
                {{ $record->patient->last_name }}, {{ $record->patient->first_name }}
                {{ $record->patient->middle_name }} {{ $record->patient->suffix }}
            </span>
        </div>
        <div class="col-span-2 col-start-3">
            <span class="label">Unit:</span>
            <span class="value">{{ $record->patient->unit }}</span>
        </div>
        <div class="col-span-2 col-start-5">
            <span class="label">Date of Birth:</span>
            <span class="value">{{ $record->patient->birthday }}</span>
        </div>

        <div class="row-start-2">
            <span class="label">Weight (kg):</span>
            <span class="value">{{ $record->patient->weight }}</span>
        </div>
        <div class="row-start-2">
            <span class="label">Height (cm):</span>
            <span class="value">{{ $record->patient->height }}</span>
        </div>
        <div class="row-start-2">
            <span class="label">BMI:</span>
            <span class="value">{{ $record->patient->bmi }}</span>
        </div>
        <div class="row-start-2">
            <span class="label">Age:</span>
            <span class="value">{{ $record->patient->age }}</span>
        </div>
        <div class="col-span-2 col-start-5 row-start-2">
            <span class="label">Contact:</span>
            <span class="value">{{ $record->patient->contact }}</span>
        </div>
    </div>

    <br/>

    <p class="section-title">B. Clinical Data </p>

    <div class="grid grid-cols-5 gap-0 medical-table">
        <div>
            <span class="label">Cholesterol:</span>
            <span class="value">{{ $record->cholesterol }} mg/dL</span>
        </div>
        <div>
            <span class="label">HDL:</span>
            <span class="value">{{ $record->hdl_cholesterol }} mg/dL</span>
        </div>
        <div>
            <span class="label">BP:</span>
            <span class="value">{{ $record->systolic_bp }} mmHg</span>
        </div>
        <div>
            <span class="label">FBS:</span>
            <span class="value">{{ $record->fbs }}</span>
        </div>
        <div>
            <span class="label">HbA1c:</span>
            <span class="value">{{ $record->hba1c }}%</span>
        </div>
    </div>
</span>
