<?php

namespace App\Observers;

use App\Models\Records;
use Illuminate\Support\Facades\DB;

class RecordsObserver
{
    public function created(Records $record): void
    {
        $this->log('model.create', $record, 'created');
    }

    public function updated(Records $record): void
    {
        $this->log('model.update', $record, 'updated');
    }

    public function deleted(Records $record): void
    {
        $this->log('model.delete', $record, 'deleted');
    }

    protected function log(string $eventType, Records $record, string $action): void
    {
      $allowed = ['status_id', 'ai_access']; 
      $changes = collect($record->getChanges())->only($allowed);
      $before = collect($record->getOriginal())->only($changes->keys()->all());
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
                'subject_type' => Records::class,
                'subject_id' => $record->id,
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
