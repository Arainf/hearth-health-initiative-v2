<?php

namespace App\Observers;

use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class PatientObserver
{
    public function created(Patient $patient): void
    {
        $this->log('model.create', $patient, 'created');
    }

    public function updated(Patient $patient): void
    {
        $this->log('model.update', $patient, 'updated');
    }

    public function deleted(Patient $patient): void
    {
        $this->log('model.delete', $patient, 'deleted');
    }

    protected function log(string $eventType, Patient $patient, string $action): void
    {
      $allowed = ['status_id', 'ai_access']; 
      $changes = collect($patient->getChanges())->only($allowed);
      $before = collect($patient->getOriginal())->only($changes->keys()->all());
      $metadata = [
         'changed_fields' => $changes->keys()->values()->all(),
         'before' => $before,
         'after' => $changes,
      ];

        try {
            DB::table('audit_logs')->insert([
                'occurred_at' => now(),
                'event_type' => $eventType,
                'user_id' => optional(request()->user())->id,
                'session_id' => request()->session()?->getId(),
                'request_id' => request()->attributes->get('request_id'),
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 1024),
                'http_method' => request()->method(),
                'route_name' => optional(request()->route())->getName(),
                'route_uri' => optional(request()->route())->uri(),
                'url' => request()->fullUrl(),
                'status_code' => null,
                'latency_ms' => null,
                'subject_type' => Patient::class,
                'subject_id' => $patient->id,
                'action' => $action,
                'outcome' => 'success',
                'failure_reason' => null,
                'metadata_json' => json_encode($metadata),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {}
    }
}
