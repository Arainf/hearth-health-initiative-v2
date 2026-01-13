<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;

class LogRegistered
{
    public function handle(Registered $event): void
    {
        $metadata = ['channel' => 'username'];

        try {
            DB::table('audit_logs')->insert([
                'occurred_at' => now(),
                'event_type' => 'auth.register',
                'user_id' => $event->user?->id,
                'session_id' => session()->getId(),
                'request_id' => request()->attributes->get('request_id'),
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 1024),
                'http_method' => request()->method(),
                'route_name' => optional(request()->route())->getName(),
                'route_uri' => optional(request()->route())->uri(),
                'url' => request()->fullUrl(),
                'status_code' => null,
                'latency_ms' => null,
                'action' => 'register',
                'outcome' => 'success',
                'failure_reason' => null,
                'metadata_json' => json_encode($metadata),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {}
    }
}
